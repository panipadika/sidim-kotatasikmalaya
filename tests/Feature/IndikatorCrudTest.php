<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Indikator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndikatorCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Admin::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('admin12345'),
        ]);
        $this->actingAs(Admin::first(), 'admin');
    }

    public function test_admin_can_view_search_and_filter_indicators(): void
    {
        Indikator::create(['nama_indikator' => 'Inflasi', 'jenis' => 'utama']);
        Indikator::create(['nama_indikator' => 'Indeks Pendidikan', 'jenis' => 'komponen']);

        $this->get(route('indikator.index', ['search' => 'Inflasi', 'jenis' => 'utama']))
            ->assertOk()
            ->assertSee('Inflasi')
            ->assertDontSee('Indeks Pendidikan')
            ->assertSee('id="delete-confirm-modal"', false)
            ->assertSee('data-confirm-title="Hapus data indikator?"', false);
    }

    public function test_admin_can_create_update_and_delete_indicator(): void
    {
        $parent = Indikator::create(['nama_indikator' => 'Indikator Utama', 'jenis' => 'utama']);

        $this->post(route('indikator.store'), [
            'nama_indikator' => 'Indikator Anak',
            'jenis' => 'komponen',
            'parent_id' => $parent->id,
            'satuan' => 'Persen',
        ])->assertRedirect(route('indikator.index'));

        $child = Indikator::where('nama_indikator', 'Indikator Anak')->firstOrFail();
        $this->assertSame($parent->id, $child->parent_id);

        $this->put(route('indikator.update', $child), [
            'nama_indikator' => 'Indikator Anak Diperbarui',
            'jenis' => 'subindikator',
            'parent_id' => $parent->id,
        ])->assertRedirect(route('indikator.index'));

        $this->delete(route('indikator.destroy', $child))
            ->assertRedirect(route('indikator.index'));
        $this->assertDatabaseMissing('indikator', ['id' => $child->id]);
    }

    public function test_indicator_form_validates_required_fields(): void
    {
        $this->from(route('indikator.create'))
            ->post(route('indikator.store'), [])
            ->assertRedirect(route('indikator.create'))
            ->assertSessionHasErrors(['nama_indikator', 'jenis']);
    }
}
