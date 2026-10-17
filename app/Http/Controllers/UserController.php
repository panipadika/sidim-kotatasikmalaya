<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $perPage = in_array($request->integer('per_page'), [10, 15, 25, 50, 100], true)
            ? $request->integer('per_page')
            : 10;

        $users = $query->latest()
            ->paginate($perPage)
            ->appends($request->query());

        return view('users.index', compact('users', 'totalUsers', 'newUsersThisMonth'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun pengguna '{$userName}' berhasil dihapus.");
    }
}
