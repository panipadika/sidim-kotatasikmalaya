@extends('layouts.admin')

@section('title', 'Data Indikator')

@section('content')
    <style>
        .indicator-page { width: 100%; min-width: 0; }
        .page-heading, .toolbar, .table-wrap { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
        .page-heading { padding: 16px 20px; margin-bottom: 10px; }
        .page-heading h1 { margin: 0 0 4px; font-size: 1.35rem; letter-spacing: -.02em; }
        .page-heading p { margin: 0; color: var(--text-soft); font-size: .84rem; }
        .toolbar { display: flex; gap: 12px; align-items: center; justify-content: space-between; padding: 10px; margin-bottom: 10px; }
        .filters { display: flex; gap: 8px; flex: 0 1 auto; min-width: 0; align-items: center; }
        .filters input, .filters select { flex: 0 0 auto; height: 38px; border: 1px solid #d8e2ee; border-radius: 8px; padding: 0 10px; color: var(--text-main); background: #fbfdff; font: inherit; font-size: .84rem; white-space: nowrap; }
        .filters input { width: 260px; }
        .filters select { width: 180px; }
        .filters input:focus, .filters select:focus { outline: 0; border-color: var(--sidebar-active); box-shadow: 0 0 0 3px rgba(244, 201, 93, .22); }
        .search-actions { display: flex; align-items: center; gap: 8px; }
        .search-btn, .reset-btn { display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border-radius: 8px; font-size: .84rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; border: 1px solid transparent; }
        .search-btn { background: #7dd3fc; color: #075985; border-color: #7dd3fc; }
        .search-btn:hover { background: #38bdf8; border-color: #38bdf8; }
        .reset-btn { background: #edf2f7; color: #475569; border-color: #dfe7ee; }
        .reset-btn:hover { background: #e2e8f0; color: #334155; }
        .action-btn { display: inline-flex; align-items: center; justify-content: center; height: 38px; border: 0; border-radius: 8px; padding: 0 14px; font-size: .82rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; background: #7dd3fc; color: #075985; }
        .action-btn:hover { background: #38bdf8; }
        .submit-btn { border: 0; border-radius: 8px; padding: 9px 13px; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; }
        .indicator-modal { display: none; position: fixed; inset: 0; z-index: 100; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, .42); }
        .indicator-modal.is-open { display: flex; }
        .indicator-modal-card { width: min(100%, 760px); max-height: min(760px, calc(100vh - 48px)); overflow: auto; background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 24px 70px rgba(15, 39, 71, .25); }
        .indicator-modal-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 22px 26px 18px; border-bottom: 1px solid #e6edf4; background: linear-gradient(110deg, #fffaf0 0%, #f4faff 100%); }
        .indicator-modal-header h2 { margin: 0 0 5px; font-size: 1.35rem; letter-spacing: -.03em; }
        .indicator-modal-header p { margin: 0; color: var(--text-soft); font-size: .82rem; }
        .modal-close { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 0; border-radius: 8px; background: #eaf3ff; color: #17539b; font-size: 1.1rem; cursor: pointer; }
        .modal-close:hover { background: #dceeff; }
        .indicator-modal-form { padding: 24px 26px 22px; }
        .indicator-modal-form .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 14px; }
        .indicator-modal-form .field { display: flex; flex-direction: column; gap: 7px; }
        .indicator-modal-form .field.full { grid-column: 1 / -1; }
        .indicator-modal-form label { color: var(--text-main); font-size: .82rem; font-weight: 800; }
        .indicator-modal-form label span { color: var(--text-soft); font-weight: 400; }
        .indicator-modal-form input, .indicator-modal-form select, .indicator-modal-form textarea { width: 100%; border: 1px solid #d8e2ee; border-radius: 9px; padding: 11px 12px; font: inherit; font-size: .86rem; color: var(--text-main); background: #fbfdff; }
        .indicator-modal-form input:focus, .indicator-modal-form select:focus, .indicator-modal-form textarea:focus { outline: 0; border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(14, 165, 233, .14); background: #fff; }
        .indicator-modal-form textarea { resize: vertical; min-height: 100px; line-height: 1.5; }
        .indicator-modal-form small { color: #b4232d; font-size: .76rem; }
        .indicator-modal-form .form-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e8eef4; }
        .indicator-modal-form .form-actions a, .indicator-modal-form .form-actions .cancel-btn { padding: 10px 13px; color: var(--text-soft); text-decoration: none; font-size: .84rem; font-weight: 700; background: transparent; border: 0; cursor: pointer; font-family: inherit; }
        .indicator-modal-form .form-actions a:hover, .indicator-modal-form .form-actions .cancel-btn:hover { color: var(--text-main); }
        .indicator-modal-form .submit-btn { border: 0; border-radius: 9px; padding: 11px 16px; background: #38bdf8; color: #064e70; font-size: .84rem; font-weight: 800; cursor: pointer; }
        .table-wrap { overflow: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .84rem; }
        th { background: #fffaf0; color: var(--text-soft); text-align: left; font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; }
        th, td { padding: 10px 13px; border-bottom: 1px solid #edf1f5; vertical-align: middle; }
        tbody tr:hover { background: #fffdf6; }
        tbody tr:last-child td { border-bottom: 0; }
        th:first-child, td:first-child { width: 36%; }
        th:last-child, td:last-child { width: 150px; }
        .child-name { padding-left: 30px; color: var(--text-soft); }
        .badge { display: inline-block; border-radius: 999px; padding: 4px 8px; font-size: .7rem; font-weight: 700; background: #eef5ff; white-space: nowrap; }
        .badge.utama { background: #fff0b8; }
        .badge.komponen { background: #dff3ff; }
        .badge.subindikator { background: #e8e8ff; }
        .row-actions { display: flex; gap: 6px; align-items: center; }
        .link-btn { border: 0; border-radius: 7px; padding: 6px 9px; font-size: .78rem; font-weight: 700; text-decoration: none; cursor: pointer; }
        .edit-btn { background: #eaf3ff; color: #17539b; }
        .delete-btn { background: #ffe7e7; color: #b4232d; }
        .alert { padding: 10px 14px; border-radius: 10px; margin-bottom: 12px; background: #e6f7ed; color: #176b3a; font-weight: 600; }
        @media (max-width: 700px) { .toolbar, .filters { flex-direction: column; align-items: stretch; } .filters input, .filters select { width: 100%; } .table-wrap { overflow-x: auto; } table { min-width: 700px; } }
    </style>

    <div class="indicator-page">
        <div class="page-heading">
            <h1>Data Indikator</h1>
            <p>Kelola struktur indikator makro dan hubungan induknya.</p>
        </div>

        <div class="toolbar">
            <form class="filters" method="GET" action="{{ route('indikator.index') }}">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama indikator...">
                <select name="jenis" onchange="this.form.submit()">
                    <option value="">Semua jenis</option>
                    @foreach (['utama' => 'Utama', 'komponen' => 'Komponen', 'subindikator' => 'Subindikator'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('jenis') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="search-actions">
                    <button class="search-btn" type="submit">Cari</button>
                    <a href="{{ route('indikator.index') }}" class="reset-btn">Reset</a>
                </div>
            </form>
            <button class="action-btn" type="button" data-open-modal="indicator-add">+ Tambah indikator</button>
        </div>

        <div class="table-wrap">
            <table>
                <thead><tr><th>Nama indikator</th><th>Jenis</th><th>Induk</th><th>Satuan</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse ($indikators as $indikator)
                        <tr class="delete-row">
                            <td class="{{ $indikator->parent_id ? 'child-name' : '' }}">{{ $indikator->parent_id ? '- ' : '' }}{{ $indikator->nama_indikator }}</td>
                            <td><span class="badge {{ $indikator->jenis }}">{{ ucfirst($indikator->jenis) }}</span></td>
                            <td>{{ $indikator->parent?->nama_indikator ?? '-' }}</td>
                            <td>{{ $indikator->satuan ?? '-' }}</td>
                            <td><div class="row-actions"><button class="link-btn edit-btn" type="button" data-open-modal="indicator-edit-{{ $indikator->id }}">Edit</button><form method="POST" action="{{ route('indikator.destroy', $indikator) }}" class="delete-form" data-confirm-title="Hapus data indikator?" data-confirm-message="Data indikator yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin melanjutkan?">@csrf @method('DELETE')<button class="link-btn delete-btn" type="submit">Hapus</button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada indikator yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $indikators->links() }}
        </div>
    </div>

    @foreach ($indikators as $indikator)
        <div class="indicator-modal" id="indicator-edit-{{ $indikator->id }}" data-modal aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="indicator-edit-title-{{ $indikator->id }}">
            <div class="indicator-modal-card">
                <div class="indicator-modal-header">
                    <div><h2 id="indicator-edit-title-{{ $indikator->id }}">Edit Indikator</h2><p>Perbarui informasi indikator dan hubungan induknya.</p></div>
                    <button class="modal-close" type="button" data-close-modal aria-label="Tutup">&times;</button>
                </div>
                <form class="indicator-modal-form" method="POST" action="{{ route('indikator.update', $indikator) }}">
                    @method('PUT')
                    @include('indikator._form', ['indikator' => $indikator])
                </form>
            </div>
        </div>
    @endforeach

    <div class="indicator-modal{{ $errors->any() ? ' is-open' : '' }}" id="indicator-add" data-modal aria-hidden="{{ $errors->any() ? 'false' : 'true' }}" role="dialog" aria-modal="true" aria-labelledby="indicator-modal-title">
        <div class="indicator-modal-card">
            <div class="indicator-modal-header">
                <div><h2 id="indicator-modal-title">Tambah Indikator</h2><p>Masukkan informasi indikator dan hubungan induknya.</p></div>
                <button class="modal-close" type="button" data-close-modal aria-label="Tutup">&times;</button>
            </div>
            <form class="indicator-modal-form" method="POST" action="{{ route('indikator.store') }}">
                @include('indikator._form')
            </form>
        </div>
    </div>

    @include('components.delete-confirm-modal')

    <script>
        (() => {
            const setModalState = (modal, isOpen) => {
                modal.classList.toggle('is-open', isOpen);
                modal.setAttribute('aria-hidden', String(!isOpen));
                document.body.style.overflow = isOpen ? 'hidden' : '';
                if (isOpen) modal.querySelector('input, select, textarea')?.focus();
            };

            document.querySelectorAll('[data-open-modal]').forEach((button) => {
                button.addEventListener('click', () => setModalState(document.getElementById(button.dataset.openModal), true));
            });
            document.querySelectorAll('[data-modal]').forEach((modal) => {
                modal.querySelectorAll('[data-close-modal]').forEach((closeBtn) => {
                    closeBtn.addEventListener('click', (event) => {
                        event.preventDefault();
                        setModalState(modal, false);
                    });
                });
                modal.addEventListener('click', (event) => { if (event.target === modal) setModalState(modal, false); });
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') document.querySelectorAll('[data-modal].is-open').forEach((modal) => setModalState(modal, false));
            });
        })();
    </script>
@endsection
