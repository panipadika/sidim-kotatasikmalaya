<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\AdminLoginPathService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        $currentLoginPath = AdminLoginPathService::getPath();
        $fullLoginUrl = AdminLoginPathService::getFullUrl();

        return view('settings.index', compact('admin', 'currentLoginPath', 'fullLoginUrl'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                Rule::unique('admin', 'username')->ignore($admin->id),
            ],
        ], [
            'name.required' => 'Nama admin wajib diisi.',
            'name.max' => 'Nama admin maksimal 100 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung (-), atau garis bawah (_).',
            'username.unique' => 'Username tersebut sudah digunakan oleh akun lain.',
        ]);

        $admin->update([
            'name' => trim($validated['name']),
            'username' => strtolower(trim($validated['username'])),
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Profil dan username admin berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password:admin'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $admin->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Password admin berhasil diubah.');
    }

    public function updateLoginPath(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login_path' => [
                'required',
                'string',
                'min:3',
                'max:60',
                'regex:/^[a-zA-Z0-9_\-]+$/',
            ],
        ], [
            'login_path.required' => 'URL khusus login admin wajib diisi.',
            'login_path.min' => 'URL khusus minimal 3 karakter.',
            'login_path.max' => 'URL khusus maksimal 60 karakter.',
            'login_path.regex' => 'URL khusus hanya boleh terdiri dari huruf, angka, strip (-), atau garis bawah (_). Jangan sertakan spasi atau garis miring.',
        ]);

        $path = strtolower(trim($validated['login_path'], " \t\n\r\0\x0B/"));

        if (AdminLoginPathService::isReserved($path)) {
            return back()->withErrors([
                'login_path' => "URL '{$path}' merupakan URL sistem yang dilindungi dan tidak dapat digunakan sebagai URL login khusus.",
            ])->withInput();
        }

        AdminLoginPathService::setPath($path);

        return redirect()->route('settings.index')
            ->with('success', "URL khusus login admin berhasil diubah menjadi '/{$path}'. Harap simpan atau bookmark URL ini.");
    }

    public function resetLoginPath(): RedirectResponse
    {
        AdminLoginPathService::resetToDefault();

        return redirect()->route('settings.index')
            ->with('success', "URL khusus login admin berhasil dikembalikan ke default: '/" . AdminLoginPathService::DEFAULT_PATH . "'.");
    }
}
