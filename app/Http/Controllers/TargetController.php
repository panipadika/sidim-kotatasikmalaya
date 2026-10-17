<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TargetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Target::with('indikator');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->whereHas('indikator', fn ($indicatorQuery) => $indicatorQuery->where('nama_indikator', 'like', "%{$search}%"));
        }

        if ($request->filled('jenis_target')) {
            $query->where('jenis_target', $request->string('jenis_target')->toString());
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->integer('tahun'));
        }

        $perPage = in_array($request->integer('per_page'), [10, 15, 25, 50, 100]) ? $request->integer('per_page') : 15;
        $targets = $query->orderByDesc('tahun')->orderBy('indikator_id')->paginate($perPage)->appends($request->query());
        $indikators = Indikator::orderBy('nama_indikator')->get();
        $years = \App\Models\Capaian::query()->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('target.index', compact('targets', 'indikators', 'years'));
    }

    public function create(): View
    {
        $indikators = Indikator::orderBy('nama_indikator')->get();
        $target = new Target();

        return view('target.create', compact('indikators', 'target'));
    }

    public function store(Request $request): RedirectResponse
    {
        Target::create($this->validatedData($request));
        return redirect()->route('target.index')->with('success', 'Data target berhasil ditambahkan.');
    }

    public function edit(Target $target): View
    {
        $indikators = Indikator::orderBy('nama_indikator')->get();
        return view('target.edit', compact('target', 'indikators'));
    }

    public function update(Request $request, Target $target): RedirectResponse
    {
        $target->update($this->validatedData($request, $target));
        return redirect()->route('target.index')->with('success', 'Data target berhasil diperbarui.');
    }

    public function destroy(Target $target): RedirectResponse
    {
        $target->delete();
        return redirect()->route('target.index')->with('success', 'Data target berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Target $target = null): array
    {
        $nilaiTarget = $request->input('nilai_target');
        if (is_string($nilaiTarget)) {
            $request->merge([
                'nilai_target' => $this->normalizeTargetValue($nilaiTarget),
                'nilai_target_range' => $this->normalizeTargetRangeDisplay($nilaiTarget),
            ]);
        }

        $data = $request->validate([
            'indikator_id' => ['required', 'integer', 'exists:indikator,id'],
            'jenis_target' => ['required', Rule::in(['RPJMD', 'RKPD', 'RPJPD'])],
            'periode' => ['required', 'string', 'max:100'],
            'tahun' => ['nullable', 'integer', 'min:2005', 'max:2100'],
            'nilai_target' => ['nullable', 'numeric'],
            'nilai_target_range' => ['nullable', 'string', 'max:255'],
            'sumber' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'indikator_id.required' => 'Indikator wajib dipilih.',
            'indikator_id.exists' => 'Indikator tidak ditemukan.',
            'jenis_target.required' => 'Jenis target wajib dipilih.',
            'jenis_target.in' => 'Jenis target harus RPJMD, RKPD, atau RPJPD.',
            'periode.required' => 'Periode wajib diisi.',
            'tahun.min' => 'Tahun minimal 2005.',
            'tahun.max' => 'Tahun maksimal 2100.',
            'nilai_target.numeric' => 'Nilai target harus berupa angka.',
        ]);

        validator($data, [
            'periode' => [function (string $attribute, mixed $value, \Closure $fail) use ($data, $target): void {
                $duplicate = Target::where('indikator_id', $data['indikator_id'])
                    ->where('jenis_target', $data['jenis_target'])
                    ->where('periode', $data['periode'])
                    ->when(
                        array_key_exists('tahun', $data) && $data['tahun'] !== null,
                        fn ($query) => $query->where('tahun', $data['tahun']),
                        fn ($query) => $query->whereNull('tahun')
                    )
                    ->when($target, fn ($query) => $query->where('id', '!=', $target->id))
                    ->exists();

                if ($duplicate) {
                    $fail('Data target dengan indikator, jenis target, periode, dan tahun tersebut sudah tersedia.');
                }
            }],
        ])->validate();

        return $data;
    }

    private function normalizeTargetValue(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        if (preg_match('/^([0-9]+(?:[.,][0-9]+)?)\s*[-–—]\s*([0-9]+(?:[.,][0-9]+)?)$/u', $trimmed, $matches) === 1) {
            $start = (float) str_replace(',', '.', $matches[1]);
            $end = (float) str_replace(',', '.', $matches[2]);

            return (string) (($start + $end) / 2);
        }

        return str_replace(',', '.', $trimmed);
    }

    private function normalizeTargetRangeDisplay(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        return preg_replace('/\s*[-–—]\s*/u', '-', $trimmed);
    }
}
