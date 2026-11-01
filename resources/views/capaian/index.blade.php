@extends('layouts.admin')

@section('title', 'Data Capaian')

@section('content')
<style>
    .achievement-page { width: 100%; min-width: 0; }
    .page-heading, .toolbar, .table-wrap { background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); }
    .page-heading { padding: 16px 20px; margin-bottom: 10px; } .page-heading h1 { margin: 0 0 4px; font-size: 1.35rem; } .page-heading p { margin: 0; color: var(--text-soft); font-size: .84rem; }
    .toolbar { display: flex; gap: 12px; align-items: center; justify-content: space-between; padding: 10px; margin-bottom: 10px; } .filters { display: flex; gap: 7px; flex: 0 1 auto; min-width: 0; align-items: center; }
    .filters input[type="search"] { flex: 0 0 auto; height: 38px; border: 1px solid #d8e2ee; border-radius: 8px; padding: 0 10px; color: var(--text-main); background: #fbfdff; font: inherit; font-size: .8rem; white-space: nowrap; width: 190px; }
    .filters input[type="search"]:focus { outline: 0; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .15); background: #fff; }
    .custom-select-container { position: relative; flex: 0 0 auto; }
    .custom-select-container.indikator-dropdown { width: 230px; }
    .custom-select-container.tahun-dropdown { width: 140px; }
    .custom-select-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        width: 100%;
        height: 38px;
        border: 1px solid #d8e2ee;
        border-radius: 8px;
        padding: 0 11px;
        background: #fbfdff;
        color: var(--text-main);
        font: inherit;
        font-size: .8rem;
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
    .custom-select-arrow { font-size: .68rem; color: #64748b; transition: transform .2s ease; flex-shrink: 0; }
    .custom-select-container.is-open .custom-select-arrow { transform: rotate(180deg); color: #10b981; }
    .custom-select-menu {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        min-width: 220px;
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
        font-size: .79rem;
        font-weight: 500;
        color: var(--text-main);
        cursor: pointer;
        text-align: left;
        transition: background-color .12s ease, color .12s ease;
    }
    .custom-select-option:hover { background-color: #ecfdf5; color: #065f46; }
    .custom-select-option.is-selected { background-color: #d1fae5; color: #065f46; font-weight: 700; }
    .custom-select-option .check-icon { color: #10b981; font-size: .85rem; flex-shrink: 0; }
    @keyframes dropFade { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
    .search-actions { display: flex; align-items: center; gap: 8px; }
    .search-btn, .reset-btn { display: inline-flex; align-items: center; justify-content: center; height: 38px; padding: 0 14px; border-radius: 8px; font-size: .84rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; border: 1px solid transparent; }
    .search-btn { background: #10b981; color: #ffffff; border-color: #10b981; }
    .search-btn:hover { background: #059669; border-color: #059669; }
    .reset-btn { background: #edf2f7; color: #475569; border-color: #dfe7ee; }
    .reset-btn:hover { background: #e2e8f0; color: #334155; }
    .toolbar-actions { display: flex; align-items: center; gap: 7px; flex: 0 0 auto; }
    .action-btn { display: inline-flex; align-items: center; justify-content: center; height: 38px; border: 0; border-radius: 8px; padding: 0 14px; font-size: .8rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; background: #10b981; color: #ffffff; }
    .action-btn:hover { background: #059669; }
    .import-btn { background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; }
    .import-btn:hover { background: #10b981; color: #ffffff; border-color: #10b981; }
    .link-btn { border: 0; border-radius: 8px; padding: 8px 11px; font-size: .76rem; font-weight: 700; text-decoration: none; cursor: pointer; white-space: nowrap; }
    .data-modal { display: none; position: fixed; inset: 0; z-index: 100; align-items: center; justify-content: center; padding: 24px; background: rgba(15, 23, 42, .42); }
    .data-modal.is-open { display: flex; }
    .data-modal-card { width: min(100%, 820px); max-height: calc(100vh - 48px); overflow: auto; background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: 0 24px 70px rgba(15, 39, 71, .25); }
    .data-modal-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 22px 26px 18px; border-bottom: 1px solid #e6edf4; background: linear-gradient(110deg, #fffaf0 0%, #f0f9f4 100%); }
    .data-modal-header h2 { margin: 0 0 5px; font-size: 1.35rem; } .data-modal-header p { margin: 0; color: var(--text-soft); font-size: .82rem; }
    .data-modal-close { width: 32px; height: 32px; border: 0; border-radius: 8px; background: #ecfdf5; color: #10b981; font-size: 1.1rem; cursor: pointer; }
    .data-modal-close:hover { background: #d1fae5; } .data-modal-body { padding: 22px 26px; } .data-modal-body .capaian-form { max-width: none; border: 0; box-shadow: none; border-radius: 0; } .data-modal-body .form-header { display: none; } .data-modal-body .form-content { padding: 0; }
    @media (max-width: 650px) { .data-modal { padding: 12px; } .data-modal-body { padding: 18px 16px; } }
    .table-wrap { overflow: auto; } table { width: 100%; border-collapse: collapse; font-size: .84rem; } th { background: #fffaf0; color: var(--text-soft); text-align: left; font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; white-space: nowrap; } th, td { padding: 10px 13px; border-bottom: 1px solid #edf1f5; vertical-align: middle; } th.numeric, td.numeric { text-align: right; } tbody tr:hover { background: #fffdf6; }
    .value { font-weight: 700; } .edit-btn { background: #ecfdf5; color: #10b981; } .edit-btn:hover { background: #10b981; color: #fff; } .delete-btn { background: #ffe7e7; color: #b4232d; } .row-actions { display: flex; gap: 6px; } .alert { padding: 10px 14px; border-radius: 10px; margin-bottom: 12px; background: #e6f7ed; color: #176b3a; font-weight: 600; }
    @media (max-width: 700px) { .toolbar, .filters, .toolbar-actions { flex-direction: column; align-items: stretch; } .filters input[type="search"], .custom-select-container.indikator-dropdown, .custom-select-container.tahun-dropdown { width: 100%; } .toolbar-actions { width: 100%; } .toolbar-actions .action-btn { text-align: center; } table { min-width: 760px; } }
</style>
<div class="achievement-page">
    <div class="page-heading"><h1>Data Capaian Indikator</h1><p>Kelola nilai capaian indikator berdasarkan tahun.</p></div>
    <div class="toolbar">
        <form class="filters" method="GET" action="{{ route('capaian.index') }}" id="capaian-filter-form">
            <input type="hidden" name="indikator_id" id="filter-indikator-input" value="{{ request('indikator_id') }}">
            <div class="custom-select-container indikator-dropdown" id="capaian-indikator-dropdown">
                @php
                    $currentIndikator = $indikators->firstWhere('id', request('indikator_id'));
                @endphp
                <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                    <span class="custom-select-label">{{ $currentIndikator?->nama_indikator ?? 'Semua indikator' }}</span>
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
                        @foreach($indikators as $indikator)
                            <button type="button" class="custom-select-option {{ (string)request('indikator_id') === (string)$indikator->id ? 'is-selected' : '' }}" data-value="{{ $indikator->id }}" data-label="{{ $indikator->nama_indikator }}">
                                <span>{{ $indikator->nama_indikator }}</span>
                                @if((string)request('indikator_id') === (string)$indikator->id) <i class="bi bi-check2 check-icon"></i> @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <input type="hidden" name="tahun" id="filter-tahun-input" value="{{ request('tahun') }}">
            <div class="custom-select-container tahun-dropdown" id="capaian-tahun-dropdown">
                <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                    <span class="custom-select-label">{{ request('tahun') ? request('tahun') : 'Semua tahun' }}</span>
                    <i class="bi bi-chevron-down custom-select-arrow"></i>
                </button>
                <div class="custom-select-menu" role="listbox">
                    <div class="custom-select-options">
                        <button type="button" class="custom-select-option {{ !request('tahun') ? 'is-selected' : '' }}" data-value="">
                            <span>Semua tahun</span>
                            @if(!request('tahun')) <i class="bi bi-check2 check-icon"></i> @endif
                        </button>
                        @foreach($years as $year)
                            <button type="button" class="custom-select-option {{ (string)request('tahun') === (string)$year ? 'is-selected' : '' }}" data-value="{{ $year }}">
                                <span>{{ $year }}</span>
                                @if((string)request('tahun') === (string)$year) <i class="bi bi-check2 check-icon"></i> @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="search-actions">
                <button class="search-btn" type="submit">Cari</button>
                <a href="{{ route('capaian.index') }}" class="reset-btn">Reset</a>
            </div>
        </form>
        <div class="toolbar-actions"><a class="action-btn import-btn" href="{{ route('capaian.import') }}">Import Excel</a><button class="action-btn" type="button" data-open-modal="capaian-add">+ Tambah capaian</button></div>
    </div>
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
            if (value) modal.querySelector('input:not([type="hidden"]), select:not([tabindex="-1"]), textarea, .form-custom-select-trigger')?.focus();
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

        // Handler Custom Dropdown Capaian
        const initCustomSelect = (containerId, inputId) => {
            const container = document.getElementById(containerId);
            if (!container) return;
            const trigger = container.querySelector('.custom-select-trigger');
            const hiddenInput = document.getElementById(inputId);
            const form = document.getElementById('capaian-filter-form');
            const searchInput = container.querySelector('.custom-select-search-input');
            const options = container.querySelectorAll('.custom-select-option');

            const toggle = (open) => {
                const isOpen = open ?? !container.classList.contains('is-open');
                // Tutup dropdown lain jika sedang terbuka
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

        initCustomSelect('capaian-indikator-dropdown', 'filter-indikator-input');
        initCustomSelect('capaian-tahun-dropdown', 'filter-tahun-input');
    })();
</script>
@endsection
