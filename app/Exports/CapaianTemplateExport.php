<?php

namespace App\Exports;

use App\Models\Capaian;
use App\Models\Target;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class CapaianTemplateExport implements FromArray, ShouldAutoSize, WithColumnWidths, WithEvents, WithStyles
{
    public function array(): array
    {
        $header = ['nama_indikator', 'satuan', 'tahun', 'target', 'capaian', 'sumber', 'keterangan'];

        $rows = [];
        $groupedRows = [];

        foreach (Capaian::with('indikator')->orderBy('indikator_id')->orderBy('tahun')->get() as $capaian) {
            $key = ($capaian->indikator_id ?? 'unknown') . ':' . ($capaian->tahun ?? 0);
            $groupedRows[$key] = [
                'nama_indikator' => $capaian->indikator?->nama_indikator ?? '',
                'satuan' => $capaian->indikator?->satuan ?? '',
                'tahun' => (int) $capaian->tahun,
                'target' => null,
                'capaian' => $capaian->nilai !== null ? (float) $capaian->nilai : null,
                'sumber' => $capaian->sumber,
                'keterangan' => $capaian->keterangan,
            ];
        }

        foreach (Target::with('indikator')->orderBy('indikator_id')->orderBy('tahun')->orderByRaw("CASE WHEN jenis_target = 'RKPD' THEN 2 ELSE 1 END")->get() as $target) {
            $key = ($target->indikator_id ?? 'unknown') . ':' . ($target->tahun ?? 0);
            if (!isset($groupedRows[$key])) {
                $groupedRows[$key] = [
                    'nama_indikator' => $target->indikator?->nama_indikator ?? '',
                    'satuan' => $target->indikator?->satuan ?? '',
                    'tahun' => (int) $target->tahun,
                    'target' => null,
                    'capaian' => null,
                    'sumber' => $target->sumber,
                    'keterangan' => $target->keterangan,
                ];
            }

            $groupedRows[$key]['target'] = $target->nilai_target !== null ? (float) $target->nilai_target : null;
            if (empty($groupedRows[$key]['sumber']) && !empty($target->sumber)) {
                $groupedRows[$key]['sumber'] = $target->sumber;
            }
            if (empty($groupedRows[$key]['keterangan']) && !empty($target->keterangan)) {
                $groupedRows[$key]['keterangan'] = $target->keterangan;
            }
        }

        foreach ($groupedRows as $row) {
            if (empty($row['nama_indikator'])) {
                continue;
            }

            $rows[] = [
                $row['nama_indikator'],
                $row['satuan'],
                $row['tahun'],
                $row['target'],
                $row['capaian'],
                $row['sumber'],
                $row['keterangan'],
            ];
        }

        if (empty($rows)) {
            return [
                $header,
                ['Indeks Pembangunan Manusia', 'Persen', 2025, 78.50, 76.20, 'BPS Kota Tasikmalaya', 'Data contoh. Hapus baris ini sebelum import.'],
            ];
        }

        return array_merge([$header], $rows);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 34,
            'B' => 18,
            'C' => 12,
            'D' => 16,
            'E' => 16,
            'F' => 28,
            'G' => 48,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '1F6B45'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
            2 => [
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => 'F2F8F4'],
                ],
                'alignment' => [
                    'vertical' => 'top',
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getStyle('A1:G2')->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('A1:G2')->getBorders()->getAllBorders()->getColor()->setRGB('D7E5DC');

                $table = new Table('A1:G2', 'CapaianTemplate');
                    $table->getStyle()->setTheme(TableStyle::TABLE_STYLE_MEDIUM4);
                    $table->getStyle()->setShowRowStripes(true);
                $sheet->addTable($table);
            },
        ];
    }
}
