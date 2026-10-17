<?php

namespace App\Http\Controllers;

use App\Exports\CapaianTemplateExport;
use App\Imports\CapaianImport;
use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CapaianController extends Controller
{
    public function index(Request $request): View
    {
        $query = Capaian::with(['indikator.targets']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->whereHas('indikator', fn ($indicatorQuery) => $indicatorQuery->where('nama_indikator', 'like', "%{$search}%"));
        }

        if ($request->filled('indikator_id')) {
            $query->where('indikator_id', $request->integer('indikator_id'));
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->integer('tahun'));
        }

        $perPage = in_array($request->integer('per_page'), [10, 15, 25, 50, 100]) ? $request->integer('per_page') : 10;
        $capaians = $query->orderByDesc('tahun')->orderBy('indikator_id')->paginate($perPage)->appends($request->query());
        $indikators = Indikator::orderBy('nama_indikator')->get();
        $years = Capaian::query()->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('capaian.index', compact('capaians', 'indikators', 'years'));
    }

    public function create(): View
    {
        $indikators = Indikator::orderBy('nama_indikator')->get();
        return view('capaian.create', compact('indikators'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        DB::transaction(function () use ($data): void {
            $capaian = Capaian::create($data);
            $this->syncTarget($capaian, $data);
        });
        return redirect()->route('capaian.index')->with('success', 'Data capaian berhasil ditambahkan.');
    }

    public function edit(Capaian $capaian): View
    {
        $indikators = Indikator::orderBy('nama_indikator')->get();
        return view('capaian.edit', compact('capaian', 'indikators'));
    }

    public function update(Request $request, Capaian $capaian): RedirectResponse
    {
        $data = $this->validatedData($request, $capaian);
        DB::transaction(function () use ($capaian, $data): void {
            $capaian->update($data);
            $this->syncTarget($capaian, $data);
        });
        $filters = array_filter([
            'search' => $request->input('_return_search'),
            'indikator_id' => $request->input('_return_indikator_id'),
            'tahun' => $request->input('_return_tahun'),
            'page' => $request->input('_return_page'),
        ], static fn ($value) => $value !== null && $value !== '');

        return redirect()->route('capaian.index', $filters)->with('success', 'Data capaian berhasil diperbarui.');
    }

    public function destroy(Capaian $capaian): RedirectResponse
    {
        $capaian->delete();
        return redirect()->route('capaian.index')->with('success', 'Data capaian berhasil dihapus.');
    }

    public function showImport(): View
    {
        return view('capaian.import');
    }

    public function import(Request $request): View|RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes' => 'File harus berformat XLS atau XLSX.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $headings = $spreadsheet->getActiveSheet()->rangeToArray('A1:Z1', null, true, false)[0] ?? [];
        $spreadsheet->disconnectWorksheets();
        $headings = array_map(fn ($heading) => strtolower(trim((string) $heading)), $headings);

        if (!in_array('nama_indikator', $headings, true) || !in_array('tahun', $headings, true)) {
            return back()->withErrors(['file' => 'Format header Excel tidak sesuai. Kolom nama_indikator dan tahun wajib ada.']);
        }

        $indikators = Indikator::get()->keyBy(fn (Indikator $indikator) => mb_strtolower(trim($indikator->nama_indikator)));
        $import = new CapaianImport($indikators);
        Excel::import($import, $file);

        if ($import->totalRows === 0) {
            return back()->withErrors(['file' => 'File Excel tidak memiliki data.']);
        }

        if ($import->errors) {
            return view('capaian.import', [
                'result' => [
                    'total' => $import->totalRows,
                    'added' => 0,
                    'updated' => 0,
                    'failed' => count($import->errors),
                ],
                'importErrors' => $import->errors,
            ])->with('errorMessage', 'Import gagal. Periksa data Excel Anda.');
        }

        $result = ['total' => $import->totalRows, 'added' => 0, 'updated' => 0, 'failed' => 0];
        DB::transaction(function () use ($import, &$result): void {
            foreach ($import->validRows as $row) {
                $indikator = Indikator::find($row['indikator_id']);
                if ($indikator && !empty($row['satuan'])) {
                    $indikator->satuan = $row['satuan'];
                    $indikator->save();
                }

                $existing = Capaian::where('indikator_id', $row['indikator_id'])
                    ->where('tahun', $row['tahun'])->exists();

                if ($row['capaian'] !== null) {
                    Capaian::updateOrCreate(
                        ['indikator_id' => $row['indikator_id'], 'tahun' => $row['tahun']],
                        ['nilai' => $row['capaian'], 'sumber' => $row['sumber'], 'keterangan' => $row['keterangan']]
                    );
                }

                if ($row['target'] !== null) {
                    Target::updateOrCreate(
                        [
                            'indikator_id' => $row['indikator_id'],
                            'tahun' => $row['tahun'],
                            'jenis_target' => 'RKPD',
                        ],
                        [
                            'nilai_target' => $row['target'],
                            'nilai_target_range' => $row['target_range'] ?? null,
                            'periode' => 'Tahunan',
                            'sumber' => $row['sumber'],
                            'keterangan' => $row['keterangan'],
                        ]
                    );
                }

                $result[$existing ? 'updated' : 'added']++;
            }
        });

        return view('capaian.import', [
            'result' => $result,
            'successMessage' => 'Data capaian berhasil diimport.',
        ]);
    }

    public function template()
    {
        return Excel::download(new CapaianTemplateExport(), 'template_capaian.xlsx');
    }

    private function validatedData(Request $request, ?Capaian $capaian = null): array
    {
        foreach (['nilai', 'target'] as $field) {
            $value = $request->input($field);
            if (is_string($value)) {
                $normalized = $field === 'target'
                    ? $this->normalizeRangeValue($value)
                    : str_replace(',', '.', trim($value));

                $request->merge([
                    $field => $normalized,
                    $field === 'target' ? 'target_range_display' : 'ignored' => $field === 'target' ? $this->normalizeRangeDisplay($value) : null,
                ]);
            }
        }

        return $request->validate([
            'indikator_id' => ['required', 'integer', 'exists:indikator,id'],
                'tahun' => [
                'required',
                'integer',
                'between:2005,2100',
                Rule::unique('capaian', 'tahun')
                    ->where(fn ($query) => $query->where('indikator_id', $request->integer('indikator_id')))
                    ->ignore($capaian?->id),
            ],
            'nilai' => ['nullable', 'numeric', 'between:-999999999999.999999,999999999999.999999'],
            'target' => ['nullable', 'numeric', 'between:-999999999999.999999,999999999999.999999'],
            'target_range_display' => ['nullable', 'string', 'max:255'],
            'sumber' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'indikator_id.required' => 'Indikator wajib dipilih.',
            'indikator_id.exists' => 'Indikator tidak ditemukan.',
            'tahun.between' => 'Tahun harus berada di antara 2005 dan 2100.',
            'tahun.unique' => 'Capaian untuk indikator dan tahun tersebut sudah ada.',
            'nilai.numeric' => 'Nilai harus berupa angka.',
            'target.numeric' => 'Target harus berupa angka.',
        ]);
    }

    private function normalizeRangeValue(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        if (preg_match('/^(-?\d+(?:[.,]\d+)?)\s*[-–—]\s*(-?\d+(?:[.,]\d+)?)$/u', $trimmed, $matches) === 1) {
            $start = (float) str_replace(',', '.', $matches[1]);
            $end = (float) str_replace(',', '.', $matches[2]);

            return (string) (($start + $end) / 2);
        }

        return str_replace(',', '.', $trimmed);
    }

    private function normalizeRangeDisplay(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }

        return preg_replace('/\s*[-–—]\s*/u', '-', $trimmed);
    }

    private function syncTarget(Capaian $capaian, array $data): void
    {
        if (!array_key_exists('target', $data)) {
            return;
        }

        $targetQuery = Target::where('indikator_id', $capaian->indikator_id)
            ->where('tahun', $capaian->tahun)
            ->where('jenis_target', 'RKPD');

        if ($data['target'] === null || $data['target'] === '') {
            $targetQuery->delete();
            return;
        }

        Target::updateOrCreate(
            [
                'indikator_id' => $capaian->indikator_id,
                'tahun' => $capaian->tahun,
                'jenis_target' => 'RKPD',
            ],
            [
                'nilai_target' => $data['target'],
                'nilai_target_range' => $data['target_range_display'] ?? null,
                'periode' => 'Tahunan',
                'sumber' => $data['sumber'] ?? null,
                'keterangan' => $data['keterangan'] ?? null,
            ]
        );
    }
}
