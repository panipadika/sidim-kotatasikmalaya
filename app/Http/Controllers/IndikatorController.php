<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IndikatorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Indikator::with('parent')->withCount('children');

        if ($request->filled('indikator_id')) {
            $query->where('id', $request->integer('indikator_id'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('nama_indikator', 'like', "%{$search}%");
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->string('jenis')->toString());
        }

        $perPage = in_array($request->integer('per_page'), [10, 15, 25, 50, 100]) ? $request->integer('per_page') : 10;
        $indikators = $query->orderByRaw('COALESCE(parent_id, id), parent_id, nama_indikator')
            ->paginate($perPage)
            ->appends($request->query());
        $allIndikators = Indikator::orderBy('nama_indikator')->get();
        $parentsQuery = Indikator::whereIn('jenis', ['utama', 'komponen']);
        if ($request->filled('search')) {
            $parentsQuery->where('nama_indikator', 'like', '%' . $request->string('search')->toString() . '%');
        }
        if ($request->filled('jenis')) {
            $parentsQuery->where('jenis', $request->string('jenis')->toString());
        }
        $parents = $parentsQuery->orderBy('nama_indikator')->get();

        return view('indikator.index', compact('indikators', 'parents', 'allIndikators'));
    }

    public function create(): View
    {
        $parents = Indikator::whereIn('jenis', ['utama', 'komponen'])
            ->orderBy('nama_indikator')
            ->get();

        return view('indikator.create', compact('parents'));
    }

    public function store(Request $request): RedirectResponse
    {
        Indikator::create($this->validatedData($request));

        return redirect()->route('indikator.index')
            ->with('success', 'Indikator berhasil ditambahkan.');
    }

    public function edit(Indikator $indikator): View
    {
        $excludedIds = $this->descendantIds($indikator);
        $excludedIds[] = $indikator->id;

        $parents = Indikator::whereIn('jenis', ['utama', 'komponen'])
            ->whereNotIn('id', $excludedIds)
            ->orderBy('nama_indikator')
            ->get();

        return view('indikator.edit', compact('indikator', 'parents'));
    }

    public function publicDetail(?Indikator $indikator = null): View
    {
        if ($indikator === null) {
            $indikator = Indikator::query()
                ->where(function ($query) {
                    $query->whereRaw('LOWER(nama_indikator) = ?', ['ipm'])
                        ->orWhereRaw('LOWER(nama_indikator) LIKE ?', ['%ipm%'])
                        ->orWhereRaw('LOWER(nama_indikator) LIKE ?', ['%indeks pembangunan manusia%']);
                })
                ->first();
        }

        $indikator ??= Indikator::query()->orderBy('nama_indikator')->firstOrFail();

        return $this->show($indikator);
    }

    public function show(Indikator $indikator): View
    {
        $indikator->load([
            'capaians' => fn ($query) => $query->orderBy('tahun'),
            'targets' => fn ($query) => $query->orderByRaw('tahun IS NULL')->orderBy('tahun')->orderByRaw("CASE WHEN jenis_target = 'RKPD' THEN 2 ELSE 1 END"),
        ]);

        $capaianByYear = $indikator->capaians->keyBy('tahun');
        $targetByYear = $indikator->targets->filter(fn ($target) => $target->tahun !== null)->keyBy('tahun');
        $riwayat = $indikator->capaians->map(function ($capaian) use ($targetByYear): array {
            return [
                'capaian' => $capaian,
                'target' => $targetByYear->get($capaian->tahun),
            ];
        });
        $capaianYears = $indikator->capaians->pluck('tahun')->filter();
        $targetYears = $targetByYear->keys();
        $allYears = $capaianYears->merge($targetYears)->unique()->sort()->values();

        if ($allYears->isNotEmpty()) {
            $minYear = (int) $allYears->first();
            $maxYear = (int) $allYears->last();
            $chartYears = range($minYear, $maxYear);
            $chartSubtitle = $minYear === $maxYear
                ? "Riwayat capaian dan target tahunan {$minYear}."
                : "Riwayat capaian dan target tahunan {$minYear} - {$maxYear}.";
        } else {
            $chartYears = [];
            $chartSubtitle = 'Riwayat capaian dan target tahunan.';
        }

        $allIndicators = Indikator::query()
            ->orderBy('nama_indikator')
            ->get(['id', 'nama_indikator']);

        return view('indikator.show', [
            'indikator' => $indikator,
            'riwayat' => $riwayat,
            'chartLabels' => $chartYears,
            'chartSubtitle' => $chartSubtitle,
            'allIndicators' => $allIndicators,
            'capaianData' => collect($chartYears)->map(fn (int $year) =>
                $capaianByYear->get($year)?->nilai !== null ? (float) $capaianByYear->get($year)->nilai : null
            )->all(),
            'targetData' => collect($chartYears)->map(fn (int $year) =>
                $targetByYear->get($year)?->nilai_target !== null ? (float) $targetByYear->get($year)->nilai_target : null
            )->all(),
        ]);
    }

    public function update(Request $request, Indikator $indikator): RedirectResponse
    {
        $indikator->update($this->validatedData($request, $indikator));

        return redirect()->route('indikator.index')
            ->with('success', 'Indikator berhasil diperbarui.');
    }

    public function destroy(Indikator $indikator): RedirectResponse
    {
        $indikator->delete();

        return redirect()->route('indikator.index')
            ->with('success', 'Indikator berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Indikator $indikator = null): array
    {
        $data = $request->validate([
            'nama_indikator' => ['required', 'string', 'max:255'],
            'satuan' => ['nullable', 'string', 'max:100'],
            'jenis' => ['required', Rule::in(['utama', 'komponen', 'subindikator'])],
            'parent_id' => ['nullable', 'integer', 'exists:indikator,id'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'nama_indikator.required' => 'Nama indikator wajib diisi.',
            'jenis.required' => 'Jenis indikator wajib dipilih.',
            'parent_id.exists' => 'Induk indikator tidak ditemukan.',
        ]);

        if (!empty($data['parent_id']) && $indikator) {
            validator($data, [
                'parent_id' => [function (string $attribute, mixed $value, \Closure $fail) use ($indikator): void {
                    if ($value == $indikator->id || in_array((int) $value, $this->descendantIds($indikator), true)) {
                        $fail('Indikator tidak dapat menjadi induk dirinya sendiri atau turunannya.');
                    }
                }],
            ])->validate();
        }

        return $data;
    }

    private function descendantIds(Indikator $indikator): array
    {
        $ids = [];
        $children = $indikator->children()->get();

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->descendantIds($child));
        }

        return $ids;
    }
}
