<?php

namespace Database\Seeders;

use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndikatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indicators = [
            [
                'nama_indikator' => 'Indeks Pembangunan Manusia',
                'satuan' => 'Point',
                'jenis' => 'utama',
                'parent_id' => null,
                'children' => [
                    [
                        'nama_indikator' => 'Indeks Pendidikan',
                        'satuan' => 'Point',
                        'jenis' => 'komponen',
                        'children' => [
                            ['nama_indikator' => 'Rata-rata Lama Sekolah', 'satuan' => 'Tahun', 'jenis' => 'subindikator'],
                            ['nama_indikator' => 'Harapan Lama Sekolah', 'satuan' => 'Tahun', 'jenis' => 'subindikator'],
                        ],
                    ],
                    [
                        'nama_indikator' => 'Indeks Kesehatan',
                        'satuan' => 'Point',
                        'jenis' => 'komponen',
                        'children' => [
                            ['nama_indikator' => 'Umur Harapan Hidup', 'satuan' => 'Tahun', 'jenis' => 'subindikator'],
                        ],
                    ],
                    [
                        'nama_indikator' => 'Indeks Daya Beli',
                        'satuan' => 'Point',
                        'jenis' => 'komponen',
                        'children' => [
                            ['nama_indikator' => 'Pengeluaran per Kapita', 'satuan' => 'Juta Rp', 'jenis' => 'subindikator'],
                        ],
                    ],
                ],
            ],
            [
                'nama_indikator' => 'PDRB ADHB Perkapita',
                'satuan' => 'Juta Rp',
                'jenis' => 'utama',
                'parent_id' => null,
            ],
            [
                'nama_indikator' => 'PDRB ADHB Berlaku',
                'satuan' => 'Triliun Rp',
                'jenis' => 'utama',
                'parent_id' => null,
                'children' => [
                    [
                        'nama_indikator' => 'PDRB ADH Konstan',
                        'satuan' => 'Triliun Rp',
                        'jenis' => 'komponen',
                    ],
                ],
            ],
            [
                'nama_indikator' => 'Laju Pertumbuhan Ekonomi',
                'satuan' => '%',
                'jenis' => 'utama',
                'parent_id' => null,
            ],
            [
                'nama_indikator' => 'Inflasi',
                'satuan' => '%',
                'jenis' => 'utama',
                'parent_id' => null,
            ],
            [
                'nama_indikator' => 'Indeks Gini',
                'satuan' => 'Point',
                'jenis' => 'utama',
                'parent_id' => null,
            ],
            [
                'nama_indikator' => 'Laju Pertumbuhan Penduduk',
                'satuan' => '%',
                'jenis' => 'utama',
                'parent_id' => null,
            ],
            [
                'nama_indikator' => 'Tingkat Kemiskinan',
                'satuan' => '%',
                'jenis' => 'utama',
                'parent_id' => null,
                'children' => [
                    [
                        'nama_indikator' => 'Jumlah Penduduk Miskin',
                        'satuan' => 'Ribu Jiwa',
                        'jenis' => 'komponen',
                    ],
                ],
            ],
            [
                'nama_indikator' => 'Tingkat Pengangguran Terbuka',
                'satuan' => '%',
                'jenis' => 'utama',
                'parent_id' => null,
            ],
        ];

        $indicatorMap = [];

        foreach ($indicators as $indicator) {
            $parent = Indikator::create([
                'nama_indikator' => $indicator['nama_indikator'],
                'parent_id' => $indicator['parent_id'],
                'satuan' => $indicator['satuan'],
                'jenis' => $indicator['jenis'],
                'deskripsi' => null,
            ]);

            $indicatorMap[$indicator['nama_indikator']] = $parent->id;

            if (!empty($indicator['children'] ?? [])) {
                foreach ($indicator['children'] as $child) {
                    $childRecord = Indikator::create([
                        'nama_indikator' => $child['nama_indikator'],
                        'parent_id' => $parent->id,
                        'satuan' => $child['satuan'],
                        'jenis' => $child['jenis'],
                        'deskripsi' => null,
                    ]);

                    $indicatorMap[$child['nama_indikator']] = $childRecord->id;

                    if (!empty($child['children'] ?? [])) {
                        foreach ($child['children'] as $grandChild) {
                            $grandChildRecord = Indikator::create([
                                'nama_indikator' => $grandChild['nama_indikator'],
                                'parent_id' => $childRecord->id,
                                'satuan' => $grandChild['satuan'],
                                'jenis' => $grandChild['jenis'],
                                'deskripsi' => null,
                            ]);

                            $indicatorMap[$grandChild['nama_indikator']] = $grandChildRecord->id;
                        }
                    }
                }
            }
        }

        $seriesMap = [
            'Indeks Pembangunan Manusia' => ['start' => 62.50, 'end' => 76.20, 'multiplier' => 1.04],
            'Indeks Pendidikan' => ['start' => 58.10, 'end' => 74.80, 'multiplier' => 1.06],
            'Rata-rata Lama Sekolah' => ['start' => 7.80, 'end' => 12.10, 'multiplier' => 1.08],
            'Harapan Lama Sekolah' => ['start' => 10.20, 'end' => 14.50, 'multiplier' => 1.07],
            'Indeks Kesehatan' => ['start' => 60.30, 'end' => 75.10, 'multiplier' => 1.05],
            'Umur Harapan Hidup' => ['start' => 68.50, 'end' => 73.80, 'multiplier' => 1.04],
            'Indeks Daya Beli' => ['start' => 52.80, 'end' => 72.60, 'multiplier' => 1.05],
            'Pengeluaran per Kapita' => ['start' => 4.10, 'end' => 11.60, 'multiplier' => 1.08],
            'PDRB ADHB Perkapita' => ['start' => 8.80, 'end' => 26.40, 'multiplier' => 1.09],
            'PDRB ADHB Berlaku' => ['start' => 16.40, 'end' => 54.30, 'multiplier' => 1.12],
            'PDRB ADH Konstan' => ['start' => 14.60, 'end' => 47.90, 'multiplier' => 1.10],
            'Laju Pertumbuhan Ekonomi' => ['start' => 5.10, 'end' => 6.90, 'multiplier' => 1.10],
            'Inflasi' => ['start' => 6.80, 'end' => 2.80, 'multiplier' => 1.08],
            'Indeks Gini' => ['start' => 0.36, 'end' => 0.33, 'multiplier' => 1.02],
            'Laju Pertumbuhan Penduduk' => ['start' => 1.92, 'end' => 1.28, 'multiplier' => 1.04],
            'Tingkat Kemiskinan' => ['start' => 18.50, 'end' => 8.70, 'multiplier' => 1.06],
            'Jumlah Penduduk Miskin' => ['start' => 142.00, 'end' => 64.00, 'multiplier' => 1.07],
            'Tingkat Pengangguran Terbuka' => ['start' => 8.80, 'end' => 4.70, 'multiplier' => 1.06],
        ];

        $years = range(2005, 2023);

        foreach ($indicatorMap as $indicatorName => $indicatorId) {
            $base = $seriesMap[$indicatorName] ?? ['start' => 0, 'end' => 0, 'multiplier' => 1.05];
            $start = (float) $base['start'];
            $end = (float) $base['end'];
            $steps = count($years) - 1;

            foreach ($years as $index => $year) {
                $value = $steps === 0
                    ? $start
                    : $start + (($end - $start) * ($index / $steps));

                $capaianValue = round($value, 2);
                $targetValue = round($capaianValue * (float) $base['multiplier'], 2);

                Capaian::updateOrCreate(
                    ['indikator_id' => $indicatorId, 'tahun' => $year],
                    [
                        'nilai' => $capaianValue,
                        'sumber' => 'BPS Kota Tasikmalaya',
                        'keterangan' => 'Data historis',
                    ]
                );

                Target::updateOrCreate(
                    ['indikator_id' => $indicatorId, 'tahun' => $year, 'jenis_target' => 'RPJMD'],
                    [
                        'periode' => '2005-2023',
                        'nilai_target' => $targetValue,
                        'sumber' => 'Bapperida',
                        'keterangan' => 'Target tahunan',
                    ]
                );
            }
        }
    }
}
