<?php

namespace App\Http\Controllers;

use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalisisController extends Controller
{
    public function index(Request $request): View
    {
        $allIndikators = Indikator::orderBy('id')->get();
        $years = Capaian::query()->distinct()->orderByDesc('tahun')->pluck('tahun');
        $indikatorId = $request->filled('indikator') ? $request->integer('indikator') : null;
        $selectedIndikator = $indikatorId ? $allIndikators->firstWhere('id', $indikatorId) : null;

        $tahunMulai = $this->normalizeYear($request->input('tahun_mulai'), $years->last() ?: 2005, $years);
        $tahunAkhir = $this->normalizeYear($request->input('tahun_akhir'), $years->first() ?: 2025, $years);

        if ($tahunMulai > $tahunAkhir) {
            $tahunMulai = $years->last() ?: 2005;
            $tahunAkhir = $years->first() ?: 2025;
        }

        $capaians = collect();
        $targetByYear = collect();
        $periodTargets = collect();

        if ($selectedIndikator) {
            $capaians = Capaian::where('indikator_id', $selectedIndikator->id)
                ->whereBetween('tahun', [$tahunMulai, $tahunAkhir])
                ->orderBy('tahun')
                ->get();

            $targetByYear = Target::where('indikator_id', $selectedIndikator->id)
                ->whereNotNull('tahun')
                ->whereBetween('tahun', [$tahunMulai, $tahunAkhir])
                ->orderBy('tahun')
                ->orderByRaw("CASE WHEN jenis_target = 'RKPD' THEN 2 ELSE 1 END")
                ->get()
                ->keyBy('tahun');

            $periodTargets = Target::where('indikator_id', $selectedIndikator->id)
                ->whereNull('tahun')
                ->orderBy('id')
                ->get();
        }

        $chartYears = range($tahunMulai, $tahunAkhir);
        $chartCapaian = [];
        $chartTarget = [];

        foreach ($chartYears as $year) {
            $capaian = $capaians->firstWhere('tahun', $year);
            $chartCapaian[$year] = $capaian && $capaian->nilai !== null ? (float) $capaian->nilai : null;

            $target = $targetByYear->get($year);
            $chartTarget[$year] = $target && $target->nilai_target !== null ? (float) $target->nilai_target : null;
        }

        $analysisData = [];
        $previousValue = null;
        foreach ($capaians as $capaian) {
            $changeValue = null;
            $targetValue = $targetByYear->get($capaian->tahun)?->nilai_target;
            if ($previousValue !== null && $capaian->nilai !== null) {
                $changeValue = (float) $capaian->nilai - (float) $previousValue;
            }

            $targetPercentage = $targetValue !== null && (float) $targetValue !== 0.0 && $capaian->nilai !== null
                ? ((float) $capaian->nilai / (float) $targetValue) * 100
                : null;

            $analysisData[] = [
                'tahun' => $capaian->tahun,
                'capaian' => $capaian->nilai,
                'target' => $targetValue,
                'persentaseTarget' => $targetPercentage,
                'perubahan' => $changeValue,
            ];

            if ($capaian->nilai !== null) {
                $previousValue = (float) $capaian->nilai;
            }
        }

        $growthData = $this->buildGrowthData($capaians, $selectedIndikator);
        $projectionData = $this->buildProjectionData($capaians, $targetByYear, $tahunAkhir, $selectedIndikator);

        $stats = [
            'nilaiAwal' => null,
            'tahunAwal' => null,
            'nilaiAkhir' => null,
            'tahunAkhir' => null,
            'perubahan' => null,
            'persenPerubahan' => null,
            'status' => 'Stabil',
            'nilaiTertinggi' => null,
            'tahunTertinggi' => null,
            'nilaiTerendah' => null,
            'tahunTerendah' => null,
            'rataRata' => null,
            'jumlahData' => $capaians->count(),
        ];

        if ($capaians->isNotEmpty()) {
            $nilaiAwalRecord = $capaians->first();
            $nilaiAkhirRecord = $capaians->last();
            $nilaiAwal = $nilaiAwalRecord->nilai !== null ? (float) $nilaiAwalRecord->nilai : null;
            $nilaiAkhir = $nilaiAkhirRecord->nilai !== null ? (float) $nilaiAkhirRecord->nilai : null;

            $stats['nilaiAwal'] = $nilaiAwal;
            $stats['tahunAwal'] = $nilaiAwalRecord->tahun;
            $stats['nilaiAkhir'] = $nilaiAkhir;
            $stats['tahunAkhir'] = $nilaiAkhirRecord->tahun;
            $stats['nilaiTertinggi'] = $capaians->max(fn ($item) => $item->nilai !== null ? (float) $item->nilai : null);
            $stats['tahunTertinggi'] = $capaians->filter(fn ($item) => $item->nilai !== null && (float) $item->nilai === (float) $stats['nilaiTertinggi'])->first()?->tahun;
            $stats['nilaiTerendah'] = $capaians->min(fn ($item) => $item->nilai !== null ? (float) $item->nilai : null);
            $stats['tahunTerendah'] = $capaians->filter(fn ($item) => $item->nilai !== null && (float) $item->nilai === (float) $stats['nilaiTerendah'])->first()?->tahun;
            $stats['rataRata'] = $capaians->whereNotNull('nilai')->avg('nilai');

            if ($nilaiAwal !== null && $nilaiAkhir !== null) {
                $stats['perubahan'] = $nilaiAkhir - $nilaiAwal;
                $isPercentageUnit = $selectedIndikator && trim((string) $selectedIndikator->satuan) === '%';
                $stats['persenPerubahan'] = $isPercentageUnit
                    ? $stats['perubahan']
                    : ($nilaiAwal != 0 ? (($nilaiAkhir - $nilaiAwal) / abs($nilaiAwal)) * 100 : null);
                $stats['status'] = $stats['perubahan'] > 0 ? 'Meningkat' : ($stats['perubahan'] < 0 ? 'Menurun' : 'Stabil');
            }
        }

        $comparisonTarget = null;
        if ($targetByYear->isNotEmpty()) {
            $comparisonYear = $targetByYear->keys()->sort()->last();
            $targetRecord = $targetByYear->get($comparisonYear);
            $comparisonCapaian = $capaians->firstWhere('tahun', $comparisonYear);

            if ($targetRecord && ($targetRecord->nilai_target !== null || $comparisonCapaian)) {
                $comparisonTarget = [
                    'tahun' => $comparisonYear,
                    'target' => $targetRecord->nilai_target,
                    'capaian' => $comparisonCapaian?->nilai,
                    'selisih' => $comparisonCapaian && $targetRecord->nilai_target !== null
                        ? (float) $comparisonCapaian->nilai - (float) $targetRecord->nilai_target
                        : null,
                ];
            }
        }

        return view('analisis.index', [
            'allIndikators' => $allIndikators,
            'years' => $years,
            'selectedIndikator' => $selectedIndikator,
            'tahunMulai' => $tahunMulai,
            'tahunAkhir' => $tahunAkhir,
            'capaians' => $capaians,
            'chartYears' => $chartYears,
            'chartCapaian' => $chartCapaian,
            'chartTarget' => $chartTarget,
            'stats' => $stats,
            'analysisData' => $analysisData,
            'comparisonTarget' => $comparisonTarget,
            'periodTargets' => $periodTargets,
            'growthData' => $growthData,
            'projectionData' => $projectionData,
        ]);
    }

    private function buildGrowthData($capaians, ?Indikator $selectedIndikator = null): array
    {
        $isPercentageUnit = $selectedIndikator && trim((string) $selectedIndikator->satuan) === '%';
        $unit = $isPercentageUnit ? '% poin' : '%';
        $rows = [];
        $previousValue = null;

        foreach ($capaians as $capaian) {
            $nilai = $capaian->nilai !== null ? (float) $capaian->nilai : null;
            $perubahan = $previousValue !== null && $nilai !== null ? $nilai - $previousValue : null;
            $pertumbuhan = null;

            if ($nilai !== null && $previousValue !== null) {
                if ($isPercentageUnit) {
                    $pertumbuhan = $perubahan;
                } elseif ($previousValue != 0) {
                    $pertumbuhan = (($nilai - $previousValue) / abs($previousValue)) * 100;
                }
            }

            $rows[] = [
                'tahun' => (int) $capaian->tahun,
                'capaian' => $nilai,
                'perubahan' => $perubahan,
                'pertumbuhan' => $pertumbuhan,
            ];

            if ($nilai !== null) {
                $previousValue = $nilai;
            }
        }

        $growthValues = array_values(array_filter(array_map(fn ($row) => $row['pertumbuhan'], $rows), fn ($value) => $value !== null && is_numeric($value)));

        if (empty($growthValues)) {
            return [
                'rows' => $rows,
                'unit' => $unit,
                'isPercentageUnit' => $isPercentageUnit,
                'average' => null,
                'highest' => null,
                'highestYear' => null,
                'lowest' => null,
                'lowestYear' => null,
                'trend' => 'Stabil',
                'chartLabels' => array_map(fn ($row) => $row['tahun'], $rows),
                'chartValues' => array_map(fn ($row) => $row['pertumbuhan'], $rows),
                'chartCapaian' => array_map(fn ($row) => $row['capaian'], $rows),
                'chartChanges' => array_map(fn ($row) => $row['perubahan'], $rows),
            ];
        }

        $highestValue = max($growthValues);
        $lowestValue = min($growthValues);
        $highestEntry = collect($rows)->firstWhere('pertumbuhan', $highestValue);
        $lowestEntry = collect($rows)->firstWhere('pertumbuhan', $lowestValue);

        return [
            'rows' => $rows,
            'unit' => $unit,
            'isPercentageUnit' => $isPercentageUnit,
            'average' => array_sum($growthValues) / count($growthValues),
            'highest' => $highestValue,
            'highestYear' => $highestEntry['tahun'] ?? null,
            'lowest' => $lowestValue,
            'lowestYear' => $lowestEntry['tahun'] ?? null,
            'trend' => $this->resolveGrowthTrend($growthValues),
            'chartLabels' => array_map(fn ($row) => $row['tahun'], $rows),
            'chartValues' => array_map(fn ($row) => $row['pertumbuhan'], $rows),
            'chartCapaian' => array_map(fn ($row) => $row['capaian'], $rows),
            'chartChanges' => array_map(fn ($row) => $row['perubahan'], $rows),
        ];
    }

    private function resolveGrowthTrend(array $growthValues): string
    {
        if (empty($growthValues)) {
            return 'Stabil';
        }

        $positive = count(array_filter($growthValues, fn ($value) => $value > 0));
        $negative = count(array_filter($growthValues, fn ($value) => $value < 0));
        $averageAbs = array_sum(array_map('abs', $growthValues)) / count($growthValues);

        if ($averageAbs < 0.25) {
            return 'Stabil';
        }

        if ($positive > $negative) {
            return 'Meningkat';
        }

        if ($negative > $positive) {
            return 'Menurun';
        }

        return 'Fluktuatif';
    }

    private function buildProjectionData($capaians, $targetByYear, int $tahunAkhir, ?Indikator $selectedIndikator = null): array
    {
        $isPercentageUnit = $selectedIndikator && trim((string) $selectedIndikator->satuan) === '%';
        $unit = $isPercentageUnit ? '% poin' : '%';
        $actualSeries = $capaians->filter(fn ($item) => $item->nilai !== null)->values();

        if ($actualSeries->count() < 2) {
            return [
                'rows' => [],
                'unit' => $unit,
                'summary' => [
                    'firstYear' => null,
                    'firstValue' => null,
                    'lastYear' => null,
                    'lastValue' => null,
                    'change' => null,
                    'growth' => null,
                    'unit' => $unit,
                    'status' => 'Belum cukup data',
                ],
                'combinedLabels' => [],
                'combinedActual' => [],
                'combinedTarget' => [],
                'combinedProjection' => [],
            ];
        }

        $xValues = $actualSeries->map(fn ($item) => (float) $item->tahun)->all();
        $yValues = $actualSeries->map(fn ($item) => (float) $item->nilai)->all();
        $xMean = array_sum($xValues) / count($xValues);
        $yMean = array_sum($yValues) / count($yValues);

        $numerator = 0.0;
        $denominator = 0.0;
        foreach ($xValues as $index => $xValue) {
            $numerator += ($xValue - $xMean) * ($yValues[$index] - $yMean);
            $denominator += ($xValue - $xMean) ** 2;
        }

        $slope = $denominator != 0 ? $numerator / $denominator : 0;
        $intercept = $yMean - $slope * $xMean;

        $actualYears = $capaians->pluck('tahun')->all();
        $lastActualYear = (int) $capaians->last()->tahun;
        $projectionYears = [];

        for ($year = $lastActualYear + 1; $year <= $lastActualYear + 5; $year++) {
            if (! in_array($year, $actualYears, true)) {
                $projectionYears[] = $year;
            }
        }

        $latestActualValue = (float) $actualSeries->last()->nilai;
        $rows = [];
        $previousProjectionValue = null;

        foreach ($projectionYears as $year) {
            $projectedValue = $intercept + $slope * $year;
            $change = $previousProjectionValue !== null
                ? $projectedValue - $previousProjectionValue
                : $projectedValue - $latestActualValue;
            $growth = null;

            if ($isPercentageUnit) {
                $growth = $change;
            } elseif ($previousProjectionValue !== null && $previousProjectionValue != 0) {
                $growth = (($projectedValue - $previousProjectionValue) / abs($previousProjectionValue)) * 100;
            } elseif ($latestActualValue != 0) {
                $growth = (($projectedValue - $latestActualValue) / abs($latestActualValue)) * 100;
            }

            $targetValue = $targetByYear->get($year)?->nilai_target;
            $targetDifference = $targetValue !== null ? $projectedValue - (float) $targetValue : null;
            $status = $targetValue !== null ? ($projectedValue >= $targetValue ? 'Target tercapai' : 'Di bawah target') : 'Proyeksi';

            $rows[] = [
                'tahun' => $year,
                'nilai' => $projectedValue,
                'perubahan' => $change,
                'pertumbuhan' => $growth,
                'target' => $targetValue,
                'status' => $status,
                'selisih_target' => $targetDifference,
            ];

            $previousProjectionValue = $projectedValue;
        }

        $actualValues = $capaians
            ->filter(fn ($item) => $item->nilai !== null)
            ->map(fn ($item) => ['tahun' => (int) $item->tahun, 'nilai' => (float) $item->nilai])
            ->values();

        $combinedLabels = array_merge(
            $actualValues->pluck('tahun')->all(),
            array_column($rows, 'tahun')
        );
        $combinedActual = array_merge(
            $actualValues->pluck('nilai')->all(),
            array_fill(0, count($rows), null)
        );
        $combinedTarget = [];
        $combinedProjection = [];

        foreach ($combinedLabels as $year) {
            $targetValue = $targetByYear->get($year)?->nilai_target;
            $combinedTarget[] = $targetValue !== null ? (float) $targetValue : null;

            $projectionMatch = collect($rows)->firstWhere('tahun', $year);
            $combinedProjection[] = $projectionMatch ? (float) $projectionMatch['nilai'] : null;
        }

        $firstProjection = $rows[0] ?? null;
        $lastProjection = $rows[count($rows) - 1] ?? null;

        $summary = [
            'firstYear' => $firstProjection['tahun'] ?? null,
            'firstValue' => $firstProjection['nilai'] ?? null,
            'lastYear' => $lastProjection['tahun'] ?? null,
            'lastValue' => $lastProjection['nilai'] ?? null,
            'change' => $firstProjection && $lastProjection ? $lastProjection['nilai'] - $firstProjection['nilai'] : null,
            'growth' => $firstProjection && $lastProjection
                ? ($isPercentageUnit
                    ? $lastProjection['nilai'] - $firstProjection['nilai']
                    : ($firstProjection['nilai'] != 0 ? (($lastProjection['nilai'] - $firstProjection['nilai']) / abs($firstProjection['nilai'])) * 100 : null))
                : null,
            'unit' => $unit,
            'status' => $lastProjection['status'] ?? 'Belum cukup data',
        ];

        return [
            'rows' => $rows,
            'unit' => $unit,
            'summary' => $summary,
            'combinedLabels' => $combinedLabels,
            'combinedActual' => $combinedActual,
            'combinedTarget' => $combinedTarget,
            'combinedProjection' => $combinedProjection,
        ];
    }

    private function normalizeYear(mixed $value, int $default, \Illuminate\Support\Collection $years): int
    {
        if (! is_numeric($value)) {
            return $default;
        }

        $year = (int) $value;

        if ($year < 2005 || $year > 2100 || ($years->isNotEmpty() && ! $years->contains($year))) {
            return $default;
        }

        return $year;
    }
}
