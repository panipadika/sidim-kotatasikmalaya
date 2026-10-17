<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CapaianImport implements ToCollection, WithHeadingRow
{
    public array $validRows = [];
    public array $errors = [];
    public int $totalRows = 0;

    public function __construct(private readonly Collection $indikators)
    {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $data = $row->toArray();
            if (count(array_filter($data, fn ($value) => $value !== null && trim((string) $value) !== '')) === 0) {
                continue;
            }

            $excelRow = $index + 2;
            $this->totalRows++;
            $normalizedData = [];
            foreach ($data as $key => $value) {
                $normalizedData[$this->normalizeHeaderName((string) $key)] = $value;
            }

            $namaIndikator = trim((string) ($normalizedData['nama_indikator'] ?? ''));
            $normalizedName = $this->normalizeIndicatorName($namaIndikator);
            $indikator = $this->indikators->get($normalizedName);
            $messages = [];

            $capaianValue = $normalizedData['capaian'] ?? $normalizedData['nilai'] ?? null;
            $targetValue = $normalizedData['target'] ?? $normalizedData['nilai_target'] ?? null;
            $satuanValue = $this->nullableValue($normalizedData['satuan'] ?? null);

            $targetRangeDisplay = null;
            if (is_string($targetValue) && $this->normalizeRangeValue($targetValue) !== null) {
                $targetRangeDisplay = $this->normalizeRangeDisplay($targetValue);
                $normalizedData['target'] = $this->normalizeRangeValue($targetValue);
            } else {
                $normalizedData['target'] = $this->normalizeNumericValue($targetValue);
            }

            $normalizedData['tahun'] = $normalizedData['tahun'] ?? null;
            $normalizedData['capaian'] = $this->normalizeNumericValue($capaianValue);
            $normalizedData['nilai'] = $this->normalizeNumericValue($capaianValue);
            $normalizedData['sumber'] = $normalizedData['sumber'] ?? null;
            $normalizedData['keterangan'] = $normalizedData['keterangan'] ?? null;

            if (!$indikator) {
                $messages[] = sprintf('Indikator "%s" tidak ditemukan.', $namaIndikator);
            }

            $validator = Validator::make([
                'nama_indikator' => $namaIndikator,
                'satuan' => $satuanValue,
                'tahun' => $normalizedData['tahun'],
                'target' => $normalizedData['target'],
                'capaian' => $normalizedData['capaian'],
                'nilai' => $normalizedData['capaian'],
                'sumber' => $normalizedData['sumber'],
                'keterangan' => $normalizedData['keterangan'],
            ], [
                'nama_indikator' => ['required', 'string'],
                'satuan' => ['nullable', 'string'],
                'tahun' => ['required', 'integer', 'between:2005,2100'],
                'target' => ['nullable', 'numeric'],
                'capaian' => ['nullable', 'numeric'],
                'nilai' => ['nullable', 'numeric'],
                'sumber' => ['nullable', 'string'],
                'keterangan' => ['nullable', 'string'],
            ], [
                'nama_indikator.required' => 'Nama indikator wajib diisi.',
                'satuan.string' => 'Satuan harus berupa teks.',
                'tahun.required' => 'Tahun wajib diisi.',
                'tahun.integer' => 'Tahun harus berupa angka bulat.',
                'tahun.between' => 'Tahun harus berada pada rentang 2005-2100.',
                'target.numeric' => 'Target harus berupa angka.',
                'capaian.numeric' => 'Nilai harus berupa angka.',
                'nilai.numeric' => 'Nilai harus berupa angka.',
                'sumber.string' => 'Sumber harus berupa teks.',
                'keterangan.string' => 'Keterangan harus berupa teks.',
            ]);

            if ($validator->fails()) {
                $messages = array_merge($messages, $validator->errors()->all());
            }

            if ($messages) {
                foreach ($messages as $message) {
                    $this->errors[] = [
                        'row' => $excelRow,
                        'message' => $message,
                    ];
                }
                continue;
            }

            $this->validRows[] = [
                'indikator_id' => $indikator->id,
                'satuan' => $satuanValue,
                'tahun' => (int) $normalizedData['tahun'],
                'target' => $normalizedData['target'] === null || $normalizedData['target'] === '' ? null : $normalizedData['target'],
                'target_range' => $targetRangeDisplay,
                'capaian' => $normalizedData['capaian'] === null || $normalizedData['capaian'] === '' ? null : $normalizedData['capaian'],
                'sumber' => $this->nullableValue($normalizedData['sumber'] ?? null),
                'keterangan' => $this->nullableValue($normalizedData['keterangan'] ?? null),
            ];
        }
    }

    private function normalizeHeaderName(string $header): string
    {
        $normalized = mb_strtolower(trim((string) $header));
        $normalized = preg_replace('/\s+/', '_', $normalized);
        $normalized = str_replace(['-', '/'], '_', (string) $normalized);

        $aliases = [
            'nilai_target' => 'target',
            'nilai' => 'capaian',
            'capaian_nilai' => 'capaian',
            'target_nilai' => 'target',
        ];

        return $aliases[$normalized] ?? $normalized;
    }

    private function nullableValue(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' || $value === '-' ? null : $value;
    }

    private function normalizeIndicatorName(string $name): string
    {
        $normalized = mb_strtolower(trim(preg_replace('/\s+/', ' ', $name)));

        $aliases = [
            'pdrb adhk perkapita' => 'pdrb adhb perkapita',
            'pdrb adhk konstan' => 'pdrb adh konstan',
        ];

        return $aliases[$normalized] ?? $normalized;
    }

    private function normalizeNumericValue(mixed $value): mixed
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return $value;
        }

        $value = trim((string) $value);
        $value = str_replace([' ', '%'], '', $value);

        if ($value === '' || $value === '-') {
            return null;
        }

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? $value : (string) $value;
    }

    private function normalizeRangeValue(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '' || $trimmed === '-') {
            return null;
        }

        if (preg_match('/^(-?\d+(?:[.,]\d+)?)\s*[-–—]\s*(-?\d+(?:[.,]\d+)?)$/u', $trimmed, $matches) === 1) {
            $start = (float) str_replace(',', '.', $matches[1]);
            $end = (float) str_replace(',', '.', $matches[2]);

            return (string) (($start + $end) / 2);
        }

        return null;
    }

    private function normalizeRangeDisplay(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '' || $trimmed === '-') {
            return null;
        }

        if (preg_match('/^(-?\d+(?:[.,]\d+)?)\s*[-–—]\s*(-?\d+(?:[.,]\d+)?)$/u', $trimmed) === 1) {
            return preg_replace('/\s*[-–—]\s*/u', '-', $trimmed);
        }

        return null;
    }
}
