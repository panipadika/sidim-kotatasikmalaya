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
        .filters input[type="search"] { flex: 0 0 auto; height: 38px; border: 1px solid #d8e2ee; border-radius: 8px; padding: 0 10px; color: var(--text-main); background: #fbfdff; font: inherit; font-size: .84rem; white-space: nowrap; width: 220px; }
        .filters input[type="search"]:focus { outline: 0; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .15); background: #fff; }
        .custom-select-container { position: relative; flex: 0 0 auto; }
        .custom-select-container.indikator-dropdown { width: 250px; }
        .custom-select-container.jenis-dropdown { width: 170px; }
        .custom-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            height: 38px;
            border: 1px solid #d8e2ee;
            border-radius: 8px;
            padding: 0 12px;
            background: #fbfdff;
            color: var(--text-main);
            font: inherit;
            font-size: .84rem;
            cursor: pointer;
            text-align: left;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .custom-select-trigger:focus, .custom-select-container.is-open .custom-select-trigger {
            outline: 0;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .15);
            background: #fff;
        }
        .custom-select-label { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .custom-select-arrow { font-size: .72rem; color: #64748b; transition: transform .2s ease; flex-shrink: 0; }
        .custom-select-container.is-open .custom-select-arrow { transform: rotate(180deg); color: #10b981; }
        .custom-select-menu {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            min-width: 180px;
            max-height: 280px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            box-shadow: 0 12px 30px rgba(15, 39, 71, 0.12), 0 2px 6px rgba(0, 0, 0, 0.04);
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        .custom-select-container.indikator-dropdown .custom-select-menu { min-width: 280px; }
        .custom-select-container.is-open .custom-select-menu { display: flex; animation: dropFade .15s cubic-bezier(0.16, 1, 0.3, 1); }
        .custom-select-search {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
            transition: background .15s ease, border-color .15s ease;
        }
        .custom-select-search:focus-within {
            background: #ffffff;
            border-bottom-color: #10b981;
        }
        .custom-select-search i {
            color: #94a3b8;
            font-size: .78rem;
            flex-shrink: 0;
            transition: color .15s ease;
        }
        .custom-select-search:focus-within i {
            color: #10b981;
        }
        .custom-select-search input,
        .filters .custom-select-search input,
        .filters input.custom-select-search-input {
            flex: 1 1 auto !important;
            width: 100% !important;
            height: 28px !important;
            min-height: unset !important;
            border: 0 !important;
            outline: 0 !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            color: var(--text-main) !important;
            font: inherit !important;
            font-size: .82rem !important;
            white-space: normal !important;
        }
        .custom-select-search input:focus,
        .filters .custom-select-search input:focus,
        .filters input.custom-select-search-input:focus {
            outline: 0 !important;
            border: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .custom-select-options { overflow-y: auto; max-height: 230px; padding: 4px; }
        .custom-select-option {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 7px 9px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            font: inherit;
            font-size: .82rem;
            font-weight: 500;
            color: var(--text-main);
            cursor: pointer;
            text-align: left;
            transition: background-color .12s ease, color .12s ease;
        }
        .custom-select-option:hover { background-color: #ecfdf5; color: #065f46; }
        .custom-select-option.is-selected { background-color: #d1fae5; color: #065f46; font-weight: 700; }
        .custom-select-option .check-icon { color: #10b981; font-size: .9rem; flex-shrink: 0; }
        @keyframes dropFade { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
        .search-actions { display: flex; align-items: center; gap: 8px; }
        .search-btn, .reset-btn { display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border-radius: 8px; font-size: .84rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; border: 1px solid transparent; }
        .search-btn { background: #10b981; color: #ffffff; border-color: #10b981; }
        .search-btn:hover { background: #059669; border-color: #059669; }
        .reset-btn { background: #edf2f7; color: #475569; border-color: #dfe7ee; }
        .reset-btn:hover { background: #e2e8f0; color: #334155; }
        .action-btn { display: inline-flex; align-items: center; justify-content: center; height: 38px; border: 0; border-radius: 8px; padding: 0 14px; font-size: .82rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; background: #10b981; color: #ffffff; }
        .action-btn:hover { background: #059669; }
        .submit-btn { border: 0; border-radius: 8px; padding: 9px 13px; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; }
        .indicator-modal { display: none; position: fixed; inset: 0; z-index: 100; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, .42); }
        .indicator-modal.is-open { display: flex; }
        .indicator-modal-card { width: min(100%, 760px); max-height: min(760px, calc(100vh - 48px)); overflow: auto; background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 24px 70px rgba(15, 39, 71, .25); }
        .indicator-modal-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 22px 26px 18px; border-bottom: 1px solid #e6edf4; background: linear-gradient(110deg, #fffaf0 0%, #f0f9f4 100%); }
        .indicator-modal-header h2 { margin: 0 0 5px; font-size: 1.35rem; letter-spacing: -.03em; }
        .indicator-modal-header p { margin: 0; color: var(--text-soft); font-size: .82rem; }
        .modal-close { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 0; border-radius: 8px; background: #ecfdf5; color: #10b981; font-size: 1.1rem; cursor: pointer; }
        .modal-close:hover { background: #d1fae5; color: #047857; }
        .indicator-modal-form { padding: 24px 26px 22px; }
        .indicator-modal-form .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 14px; }
        .indicator-modal-form .field { display: flex; flex-direction: column; gap: 7px; }
        .indicator-modal-form .field.full { grid-column: 1 / -1; }
        .indicator-modal-form label { color: var(--text-main); font-size: .82rem; font-weight: 800; }
        .indicator-modal-form label span { color: var(--text-soft); font-weight: 400; }
        .indicator-modal-form input, .indicator-modal-form select, .indicator-modal-form textarea { width: 100%; border: 1px solid #d8e2ee; border-radius: 9px; padding: 11px 12px; font: inherit; font-size: .86rem; color: var(--text-main); background: #fbfdff; }
        .indicator-modal-form input:focus, .indicator-modal-form select:focus, .indicator-modal-form textarea:focus { outline: 0; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .15); background: #fff; }
        .indicator-modal-form textarea { resize: vertical; min-height: 100px; line-height: 1.5; }
        .indicator-modal-form small { color: #b4232d; font-size: .76rem; }
        .indicator-modal-form .form-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e8eef4; }
        .indicator-modal-form .form-actions a, .indicator-modal-form .form-actions .cancel-btn { padding: 10px 13px; color: var(--text-soft); text-decoration: none; font-size: .84rem; font-weight: 700; background: transparent; border: 0; cursor: pointer; font-family: inherit; }
        .indicator-modal-form .form-actions a:hover, .indicator-modal-form .form-actions .cancel-btn:hover { color: var(--text-main); }
        .indicator-modal-form .submit-btn { border: 0; border-radius: 9px; padding: 11px 16px; background: #10b981; color: #ffffff; font-size: .84rem; font-weight: 800; cursor: pointer; }
        .indicator-modal-form .submit-btn:hover { background: #059669; }
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
        .badge.komponen { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .badge.subindikator { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .row-actions { display: flex; gap: 6px; align-items: center; }
        .link-btn { border: 0; border-radius: 7px; padding: 6px 9px; font-size: .78rem; font-weight: 700; text-decoration: none; cursor: pointer; }
        .edit-btn { background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; }
        .edit-btn:hover { background: #10b981; color: #ffffff; }
        .delete-btn { background: #ffe7e7; color: #b4232d; }
        .alert { padding: 10px 14px; border-radius: 10px; margin-bottom: 12px; background: #e6f7ed; color: #176b3a; font-weight: 600; }
        @media (max-width: 700px) { .toolbar, .filters { flex-direction: column; align-items: stretch; } .custom-select-container.indikator-dropdown, .custom-select-container.jenis-dropdown { width: 100%; } .table-wrap { overflow-x: auto; } table { min-width: 700px; } }
    </style>

    <div class="indicator-page">
        <div class="page-heading">
            <h1>Data Indikator</h1>
            <p>Kelola struktur indikator makro dan hubungan induknya.</p>
        </div>

        <div class="toolbar">
            <form class="filters" method="GET" action="{{ route('indikator.index') }}" id="indikator-filter-form">
                {{-- Custom Select Indikator with Live Search --}}
                <input type="hidden" name="indikator_id" id="filter-indikator-input" value="{{ request('indikator_id') }}">
                <div class="custom-select-container indikator-dropdown" id="indikator-select-dropdown">
                    @php
                        $selectedIndikator = $allIndikators->firstWhere('id', request('indikator_id'));
                    @endphp
                    <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="custom-select-label">{{ $selectedIndikator?->nama_indikator ?? 'Semua indikator' }}</span>
                        <i class="bi bi-chevron-down custom-select-arrow"></i>
                    </button>
                    <div class="custom-select-menu" role="listbox">
                        <div class="custom-select-search">
                            <i class="bi bi-search"></i>
                            <input type="text" class="custom-select-search-input" placeholder="Cari indikator..." autocomplete="off">
                        </div>
                        <div class="custom-select-options">
                            <button type="button" class="custom-select-option {{ !request('indikator_id') ? 'is-selected' : '' }}" data-value="" data-label="Semua indikator">
                                <span>Semua indikator</span>
                                @if(!request('indikator_id')) <i class="bi bi-check2 check-icon"></i> @endif
                            </button>
                            @foreach($allIndikators as $item)
                                <button type="button" class="custom-select-option {{ (string)request('indikator_id') === (string)$item->id ? 'is-selected' : '' }}" data-value="{{ $item->id }}" data-label="{{ $item->nama_indikator }}">
                                    <span>{{ $item->nama_indikator }}</span>
                                    @if((string)request('indikator_id') === (string)$item->id) <i class="bi bi-check2 check-icon"></i> @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Custom Select Jenis --}}
                <input type="hidden" name="jenis" id="filter-jenis-input" value="{{ request('jenis') }}">
                <div class="custom-select-container jenis-dropdown" id="indikator-jenis-dropdown">
                    <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                        <span class="custom-select-label">
                            @php
                                $jenisLabels = ['utama' => 'Utama', 'komponen' => 'Komponen', 'subindikator' => 'Subindikator'];
                            @endphp
                            {{ $jenisLabels[request('jenis')] ?? 'Semua jenis' }}
                        </span>
                        <i class="bi bi-chevron-down custom-select-arrow"></i>
                    </button>
                    <div class="custom-select-menu" role="listbox">
                        <div class="custom-select-options">
                            <button type="button" class="custom-select-option {{ !request('jenis') ? 'is-selected' : '' }}" data-value="">
                                <span>Semua jenis</span>
                                @if(!request('jenis')) <i class="bi bi-check2 check-icon"></i> @endif
                            </button>
                            @foreach (['utama' => 'Utama', 'komponen' => 'Komponen', 'subindikator' => 'Subindikator'] as $value => $label)
                                <button type="button" class="custom-select-option {{ request('jenis') === $value ? 'is-selected' : '' }}" data-value="{{ $value }}">
                                <span>{{ $label }}</span>
                                @if(request('jenis') === $value) <i class="bi bi-check2 check-icon"></i> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
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
                if (isOpen) modal.querySelector('input:not([type="hidden"]), select:not([tabindex="-1"]), textarea, .form-custom-select-trigger')?.focus();
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

            // Handler Custom Dropdowns Indikator
            const initCustomSelect = (containerId, inputId) => {
                const container = document.getElementById(containerId);
                if (!container) return;
                const trigger = container.querySelector('.custom-select-trigger');
                const hiddenInput = document.getElementById(inputId);
                const form = document.getElementById('indikator-filter-form');
                const searchInput = container.querySelector('.custom-select-search-input');
                const options = container.querySelectorAll('.custom-select-option');

                const toggle = (open) => {
                    const isOpen = open ?? !container.classList.contains('is-open');
                    if (isOpen) {
                        document.querySelectorAll('.custom-select-container.is-open').forEach(el => {
                            if (el !== container) el.classList.remove('is-open');
                        });
                    }
                    container.classList.toggle('is-open', isOpen);
                    trigger.setAttribute('aria-expanded', String(isOpen));
                    if (isOpen && searchInput) {
                        searchInput.value = '';
                        options.forEach(opt => opt.style.display = 'flex');
                        setTimeout(() => searchInput.focus(), 50);
                    }
                };

                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggle();
                });

                if (searchInput) {
                    searchInput.addEventListener('input', (e) => {
                        const q = e.target.value.toLowerCase().trim();
                        options.forEach(opt => {
                            const label = (opt.getAttribute('data-label') || opt.textContent).toLowerCase();
                            opt.style.display = label.includes(q) ? 'flex' : 'none';
                        });
                    });
                    searchInput.addEventListener('click', (e) => e.stopPropagation());
                }

                options.forEach((opt) => {
                    opt.addEventListener('click', (e) => {
                        e.stopPropagation();
                        hiddenInput.value = opt.getAttribute('data-value');
                        toggle(false);
                        form.submit();
                    });
                });

                document.addEventListener('click', (e) => {
                    if (!container.contains(e.target)) toggle(false);
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && container.classList.contains('is-open')) {
                        toggle(false);
                        trigger.focus();
                    }
                });
            };

            initCustomSelect('indikator-select-dropdown', 'filter-indikator-input');
            initCustomSelect('indikator-jenis-dropdown', 'filter-jenis-input');
        })();
    </script>
@endsection
