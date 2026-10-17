<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Capaian;
use App\Models\Indikator;
use App\Models\Target;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class CapaianImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = Admin::create(['name' => 'Admin', 'username' => 'admin', 'password' => Hash::make('admin12345')]);
        $this->actingAs($admin, 'admin');
    }

    public function test_valid_excel_imports_and_updates_existing_record(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);
        Capaian::create(['indikator_id' => $indicator->id, 'tahun' => 2025, 'nilai' => 2.1]);

        $file = $this->excelFile([
            ['nama_indikator', 'tahun', 'nilai', 'sumber', 'keterangan'],
            ['inflasi', 2025, 2.35, 'BPS', 'Diperbarui'],
            ['Inflasi', 2024, 1.85, 'BPS', 'Baru'],
        ]);

        try {
            $response = $this->post(route('capaian.import.process'), ['file' => $file]);
        } finally {
            @unlink($file->getRealPath());
        }

        $response->assertOk()->assertSee('Data capaian berhasil diimport.');
        $this->assertDatabaseCount('capaian', 2);
        $this->assertDatabaseHas('capaian', ['indikator_id' => $indicator->id, 'tahun' => 2025, 'nilai' => '2.350000']);
        $this->assertDatabaseHas('capaian', ['indikator_id' => $indicator->id, 'tahun' => 2024, 'nilai' => '1.850000']);
    }

    public function test_invalid_rows_are_reported_without_saving_any_data(): void
    {
        Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);
        $file = $this->excelFile([
            ['nama_indikator', 'tahun', 'nilai', 'sumber', 'keterangan'],
            ['Indikator Tidak Ada', 2025, 2.1, 'BPS', ''],
            ['Inflasi', 2101, 'ABC', 'BPS', ''],
        ]);

        try {
            $response = $this->post(route('capaian.import.process'), ['file' => $file]);
        } finally {
            @unlink($file->getRealPath());
        }

        $response->assertOk()
            ->assertSee('Import gagal. Periksa data Excel Anda.')
            ->assertSee('Indikator "Indikator Tidak Ada" tidak ditemukan.')
            ->assertSee('Tahun harus berada pada rentang 2005-2100.')
            ->assertSee('Nilai harus berupa angka.');
        $this->assertDatabaseCount('capaian', 0);
    }

    public function test_import_normalizes_indicator_aliases_and_comma_decimal_values(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'PDRB ADHB Perkapita', 'jenis' => 'utama']);
        $file = $this->excelFile([
            ['nama_indikator', 'tahun', 'nilai', 'sumber', 'keterangan'],
            ['PDRB ADHK Perkapita', 2025, '1.234,56', 'BPS', ''],
        ]);

        try {
            $response = $this->post(route('capaian.import.process'), ['file' => $file]);
        } finally {
            @unlink($file->getRealPath());
        }

        $response->assertOk()->assertSee('Data capaian berhasil diimport.');
        $this->assertDatabaseHas('capaian', [
            'indikator_id' => $indicator->id,
            'tahun' => 2025,
            'nilai' => '1234.560000',
        ]);
    }

    public function test_import_accepts_new_template_columns_for_target_and_capaian(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Inflasi', 'satuan' => 'Persen', 'jenis' => 'utama']);
        $file = $this->excelFile([
            ['nama_indikator', 'satuan', 'tahun', 'target', 'capaian', 'sumber', 'keterangan'],
            ['Inflasi', 'Persen', 2025, 2.35, 2.10, 'BPS', 'Data baru'],
        ]);

        try {
            $response = $this->post(route('capaian.import.process'), ['file' => $file]);
        } finally {
            @unlink($file->getRealPath());
        }

        $response->assertOk()->assertSee('Data capaian berhasil diimport.');
        $this->assertDatabaseHas('indikator', ['id' => $indicator->id, 'satuan' => 'Persen']);
        $this->assertDatabaseHas('capaian', ['indikator_id' => $indicator->id, 'tahun' => 2025, 'nilai' => '2.100000']);
        $this->assertDatabaseHas('target', ['indikator_id' => $indicator->id, 'tahun' => 2025, 'nilai_target' => '2.350000']);
    }

    public function test_import_accepts_range_value_for_target(): void
    {
        $indicator = Indikator::create(['nama_indikator' => 'Tingkat Kemiskinan', 'satuan' => '%', 'jenis' => 'utama']);
        $file = $this->excelFile([
            ['nama_indikator', 'satuan', 'tahun', 'target', 'capaian', 'sumber', 'keterangan'],
            ['Tingkat Kemiskinan', '%', 2025, '9,2-10,16', '9.50', 'BPS', 'Target range'],
        ]);

        try {
            $response = $this->post(route('capaian.import.process'), ['file' => $file]);
        } finally {
            @unlink($file->getRealPath());
        }

        $response->assertOk()->assertSee('Data capaian berhasil diimport.');
        $this->assertDatabaseHas('capaian', ['indikator_id' => $indicator->id, 'tahun' => 2025, 'nilai' => '9.500000']);
        // 9.2 + 10.16 = 19.36 / 2 = 9.68
        $this->assertDatabaseHas('target', [
            'indikator_id' => $indicator->id,
            'tahun' => 2025,
            'nilai_target' => '9.680000',
            'nilai_target_range' => '9,2-10,16',
        ]);
    }

    public function test_missing_required_header_is_rejected(): void
    {
        $file = $this->excelFile([
            ['nama_indikator', 'nilai'],
            ['Inflasi', 2.1],
        ]);

        try {
            $response = $this->post(route('capaian.import.process'), ['file' => $file]);
        } finally {
            @unlink($file->getRealPath());
        }

        $response->assertRedirect()->assertSessionHasErrors('file');
        $this->assertDatabaseCount('capaian', 0);
    }

    public function test_import_page_highlights_only_import_excel_menu_in_sidebar(): void
    {
        $response = $this->get(route('capaian.import'));

        $response->assertOk();
        // Check that Import Excel has active class while Data Capaian does not
        $response->assertSee('href="'.route('capaian.import').'" class="nav-item active"', false);
        $response->assertDontSee('href="'.route('capaian.index').'" class="nav-item active"', false);
    }

    private function excelFile(array $rows): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'capaian_').'.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $sheet->setCellValue([$columnIndex + 1, $rowIndex + 1], $value);
            }
        }

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return new UploadedFile($path, 'capaian.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', UPLOAD_ERR_OK, true);
    }
}
