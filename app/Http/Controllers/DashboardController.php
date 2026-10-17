<?php

namespace App\Http\Controllers;

use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $years = Capaian::query()->distinct()->orderByDesc('tahun')->pluck('tahun');
        $latestYear = $years->first();
        $defaultYear = $latestYear ?: 2025;
        $requestedYear = $request->integer('tahun');
        $tahun = $requestedYear > 0 && ($years->isEmpty() || $years->contains($requestedYear))
            ? $requestedYear
            : $defaultYear;

        $allIndikators = Indikator::orderBy('id')->get();
        $selectedIndikatorId = $request->integer('indikator');
        $selectedIndikator = $allIndikators->firstWhere('id', $selectedIndikatorId)
            ?? $allIndikators->first();

        $tahunMulai = $request->integer('tahun_mulai', $years->last() ?: 2005);
        $tahunAkhir = $request->integer('tahun_akhir', $years->first() ?: 2025);
        if ($tahunMulai < 2005 || $tahunAkhir > 2100 || $tahunMulai > $tahunAkhir) {
            $tahunMulai = $years->last() ?: 2005;
            $tahunAkhir = $years->first() ?: 2025;
        }

        $chartYears = range($tahunMulai, $tahunAkhir);
        $capaianByYear = $selectedIndikator
            ? Capaian::where('indikator_id', $selectedIndikator->id)
                ->whereBetween('tahun', [$tahunMulai, $tahunAkhir])
                ->get()->keyBy('tahun')
            : collect();
        $targetByYear = $selectedIndikator
            ? Target::where('indikator_id', $selectedIndikator->id)
                ->whereBetween('tahun', [$tahunMulai, $tahunAkhir])
                ->orderByRaw("CASE WHEN jenis_target = 'RKPD' THEN 2 ELSE 1 END")
                ->get()->keyBy('tahun')
            : collect();

        $chartLabels = $chartYears;
        $chartCapaian = collect($chartYears)->map(fn (int $year) =>
            $capaianByYear->get($year)?->nilai !== null
                ? (float) $capaianByYear->get($year)->nilai
                : null
        )->all();
        $chartTarget = collect($chartYears)->map(fn (int $year) =>
            $targetByYear->get($year)?->nilai_target !== null
                ? (float) $targetByYear->get($year)->nilai_target
                : null
        )->all();

        $indikators = Indikator::where('jenis', 'utama')
            ->with([
                'capaians' => fn ($query) => $query->where('tahun', $tahun),
                'targets' => fn ($query) => $query->where('tahun', $tahun),
            ])
            ->orderBy('id')
            ->get();

        $dataDashboard = $indikators->map(function (Indikator $indikator): array {
            $capaian = $indikator->capaians->first();
            $target = $indikator->targets->firstWhere('jenis_target', 'RKPD') ?? $indikator->targets->first();
            $capaianValue = $capaian?->nilai;
            $targetValue = $target?->nilai_target;

            return [
                'indikator' => $indikator,
                'capaian' => $capaianValue,
                'target' => $targetValue,
                'status' => $capaianValue !== null && $targetValue !== null
                    ? ((float) $capaianValue >= (float) $targetValue ? 'Tercapai' : 'Belum Tercapai')
                    : 'Data Tidak Tersedia',
            ];
        });

        return view('dashboard', [
            'indikators' => $indikators,
            'allIndikators' => $allIndikators,
            'dataDashboard' => $dataDashboard,
            'totalIndikator' => Indikator::count(),
            'totalCapaian' => Capaian::count(),
            'totalTarget' => Target::count(),
            'tahunTerbaru' => $latestYear ?: '-',
            'tahun' => $tahun,
            'years' => $years,
            'selectedIndikator' => $selectedIndikator,
            'tahunMulai' => $tahunMulai,
            'tahunAkhir' => $tahunAkhir,
            'chartLabels' => $chartLabels,
            'chartCapaian' => $chartCapaian,
            'chartTarget' => $chartTarget,
        ]);
    }
}
