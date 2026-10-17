@extends('layouts.admin')

@section('title', 'Data Capaian')

@section('content')
<style>
    .achievement-page { width: 100%; min-width: 0; }
    .page-heading, .toolbar, .table-wrap { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
    .page-heading { padding: 16px 20px; margin-bottom: 10px; } .page-heading h1 { margin: 0 0 4px; font-size: 1.35rem; } .page-heading p { margin: 0; color: var(--text-soft); font-size: .84rem; }
    .toolbar { display: flex; gap: 12px; align-items: center; justify-content: space-between; padding: 10px; margin-bottom: 10px; } .filters { display: flex; gap: 7px; flex: 0 1 auto; min-width: 0; align-items: center; }
    .filters input, .filters select { flex: 0 0 auto; height: 38px; border: 1px solid #d8e2ee; border-radius: 8px; padding: 0 10px; color: var(--text-main); background: #fbfdff; font: inherit; font-size: .8rem; white-space: nowrap; } .filters input { width: 190px; } .filters select[name="indikator_id"] { width: 220px; } .filters select[name="tahun"] { width: 150px; }
    .search-actions { display: flex; align-items: center; gap: 8px; }
    .search-btn, .reset-btn { display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border-radius: 8px; font-size: .84rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; border: 1px solid transparent; }
    .search-btn { background: #7dd3fc; color: #075985; border-color: #7dd3fc; }
    .search-btn:hover { background: #38bdf8; border-color: #38bdf8; }
    .reset-btn { background: #edf2f7; color: #475569; border-color: #dfe7ee; }
    .reset-btn:hover { background: #e2e8f0; color: #334155; }
    .toolbar-actions { display: flex; align-items: center; gap: 7px; flex: 0 0 auto; }
    .action-btn { display: inline-flex; align-items: center; justify-content: center; height: 38px; border: 0; border-radius: 8px; padding: 0 14px; font-size: .8rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; background: #7dd3fc; color: #075985; }
    .import-btn { background: #eaf3ff; color: #17539b; }
    .link-btn { border: 0; border-radius: 8px; padding: 8px 11px; font-size: .76rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; }
    .data-modal { display: none; position: fixed; inset: 0; z-index: 100; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, .42); }
    .data-modal.is-open { display: flex; }
    .data-modal-card { width: min(100%, 820px); max-height: calc(100vh - 48px); overflow: auto; background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 24px 70px rgba(15, 39, 71, .25); }
    .data-modal-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 22px 26px 18px; border-bottom: 1px solid #e6edf4; background: linear-gradient(110deg, #fffaf0 0%, #f4faff 100%); }
    .data-modal-header h2 { margin: 0 0 5px; font-size: 1.35rem; } .data-modal-header p { margin: 0; color: var(--text-soft); font-size: .82rem; }
    .data-modal-close { width: 32px; height: 32px; border: 0; border-radius: 8px; background: #eaf3ff; color: #17539b; font-size: 1.1rem; cursor: pointer; }
    .data-modal-close:hover { background: #dceeff; } .data-modal-body { padding: 22px 26px; } .data-modal-body .capaian-form { max-width: none; border: 0; box-shadow: none; border-radius: 0; } .data-modal-body .form-header { display: none; } .data-modal-body .form-content { padding: 0; }
    @media (max-width: 650px) { .data-modal { padding: 12px; } .data-modal-body { padding: 18px 16px; } }
    .table-wrap { overflow: auto; } table { width: 100%; border-collapse: collapse; font-size: .84rem; } th { background: #fffaf0; color: var(--text-soft); text-align: left; font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; } th, td { padding: 10px 13px; border-bottom: 1px solid #edf1f5; vertical-align: middle; } th.numeric, td.numeric { text-align: right; } tbody tr:hover { background: #fffdf6; }
    .value { font-weight: 700; } .edit-btn { background: #eaf3ff; color: #17539b; } .delete-btn { background: #ffe7e7; color: #b4232d; } .row-actions { display: flex; gap: 6px; } .alert { padding: 10px 14px; border-radius: 10px; margin-bottom: 12px; background: #e6f7ed; color: #176b3a; font-weight: 600; }
    @media (max-width: 700px) { .toolbar, .filters, .toolbar-actions { flex-direction: column; align-items: stretch; } .filters input, .filters select { width: 100%; } .toolbar-actions { width: 100%; } .toolbar-actions .action-btn { text-align: center; } table { min-width: 760px; } }
</style>
<div class="achievement-page">
    <div class="page-heading"><h1>Data Capaian Indikator</h1><p>Kelola nilai capaian indikator berdasarkan tahun.</p></div>
    <div class="toolbar"><form class="filters" method="GET" action="{{ route('capaian.index') }}"><input type="search" name="search" value="{{ request('search') }}" placeholder="Cari indikator..."><select name="indikator_id" onchange="this.form.submit()"><option value="">Semua indikator</option>@foreach($indikators as $indikator)<option value="{{ $indikator->id }}" @selected((string)request('indikator_id') === (string)$indikator->id)>{{ $indikator->nama_indikator }}</option>@endforeach</select><select name="tahun" onchange="this.form.submit()"><option value="">Semua tahun</option>@foreach($years as $year)<option value="{{ $year }}" @selected((string)request('tahun') === (string)$year)>{{ $year }}</option>@endforeach</select><div class="search-actions"><button class="search-btn" type="submit">Cari</button><a href="{{ route('capaian.index') }}" class="reset-btn">Reset</a></div></form><div class="toolbar-actions"><a class="action-btn import-btn" href="{{ route('capaian.import') }}">Import Excel</a><button class="action-btn" type="button" data-open-modal="capaian-add">+ Tambah capaian</button></div></div>
    <div class="table-wrap"><table><thead><tr><th>Indikator</th><th>Satuan</th><th class="numeric">Tahun</th><th class="numeric">Target</th><th class="numeric">Capaian</th><th>Sumber</th><th>Keterangan</th><th>Aksi</th></tr></thead><tbody>@forelse($capaians as $capaian)@php
        $target = $capaian->indikator->targets->where('jenis_target', 'RKPD')->firstWhere('tahun', $capaian->tahun)
            ?? $capaian->indikator->targets->firstWhere('tahun', $capaian->tahun);
        $capaianValue = (float) $capaian->nilai;
        $capaianDecimals = abs($capaianValue - round($capaianValue, 2)) < 0.0000001 ? 2 : 3;
    @endphp<tr><td>{{ $capaian->indikator->nama_indikator }}</td><td>{{ $capaian->indikator->satuan ?? '-' }}</td><td class="numeric">{{ $capaian->tahun }}</td><td class="value numeric">{{ $target?->nilai_target_range ?? ($target?->nilai_target !== null ? number_format((float) round($target->nilai_target, 2), 2, ',', '.') : '-') }}</td><td class="value numeric">{{ $capaian->nilai !== null ? number_format((float) round($capaian->nilai, $capaianDecimals), $capaianDecimals, ',', '.') : '-' }}</td><td>{{ $capaian->sumber ?? '-' }}</td><td>{{ $capaian->keterangan ?? '-' }}</td><td><div class="row-actions"><button class="link-btn edit-btn" type="button" data-open-modal="capaian-edit-{{ $capaian->id }}">Edit</button><form method="POST" action="{{ route('capaian.destroy', $capaian) }}" class="delete-form" data-confirm-title="Hapus data capaian?" data-confirm-message="Data capaian yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin melanjutkan?">@csrf @method('DELETE')<button class="link-btn delete-btn" type="button">Hapus</button></form></div></td></tr>@empty<tr><td colspan="8">Belum ada data capaian.</td></tr>@endforelse</tbody></table>{{ $capaians->links() }}</div>
</div>

@include('components.delete-confirm-modal')

@foreach ($capaians as $capaian)
    <div class="data-modal" id="capaian-edit-{{ $capaian->id }}" data-modal aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="capaian-edit-title-{{ $capaian->id }}">
        <div class="data-modal-card"><div class="data-modal-header"><div><h2 id="capaian-edit-title-{{ $capaian->id }}">Edit Data Capaian</h2><p>Perbarui nilai capaian indikator.</p></div><button class="data-modal-close" type="button" data-close-modal aria-label="Tutup">&times;</button></div><div class="data-modal-body"><form method="POST" action="{{ route('capaian.update', $capaian) }}">
            @foreach (request()->only(['search', 'indikator_id', 'tahun', 'page']) as $key => $value)
                <input type="hidden" name="_return_{{ $key }}" value="{{ $value }}">
            @endforeach
            @method('PUT') @include('capaian._form', ['capaian' => $capaian])
        </form></div></div>
    </div>
@endforeach
<div class="data-modal{{ $errors->any() ? ' is-open' : '' }}" id="capaian-add" data-modal aria-hidden="{{ $errors->any() ? 'false' : 'true' }}" role="dialog" aria-modal="true" aria-labelledby="capaian-modal-title">
    <div class="data-modal-card"><div class="data-modal-header"><div><h2 id="capaian-modal-title">Tambah Data Capaian</h2><p>Masukkan nilai capaian indikator secara manual.</p></div><button class="data-modal-close" type="button" data-close-modal aria-label="Tutup">&times;</button></div><div class="data-modal-body"><form method="POST" action="{{ route('capaian.store') }}">@include('capaian._form')</form></div></div>
</div>
<script>
    (() => {
        const setState = (modal, value) => {
            if (!modal) return;
            modal.classList.toggle('is-open', value);
            modal.setAttribute('aria-hidden', String(!value));
            document.body.style.overflow = value ? 'hidden' : '';
            if (value) modal.querySelector('input, select, textarea')?.focus();
        };

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                document.querySelectorAll('[data-modal].is-open').forEach((modal) => setState(modal, false));
            }
        });

        document.querySelectorAll('[data-open-modal]').forEach((button) => {
            button.addEventListener('click', () => setState(document.getElementById(button.dataset.openModal), true));
        });
        document.querySelectorAll('[data-modal]').forEach((modal) => {
            modal.querySelectorAll('[data-close-modal]').forEach((closeBtn) => {
                closeBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    setState(modal, false);
                });
            });
            modal.addEventListener('click', (event) => { if (event.target === modal) setState(modal, false); });
        });
    })();
</script>
@endsection
