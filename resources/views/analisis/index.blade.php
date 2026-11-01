@extends('layouts.admin')

@section('title', 'Analisis Indikator')

@php
    $analysisRoute = (request()->routeIs('public.*') || (auth()->user() instanceof \App\Models\User))
        ? route('public.analisis')
        : route('analisis.index');
@endphp

@section('content')
<style>
    .analysis-page { width: 100%; min-width: 0; overflow: auto; padding-bottom: 16px; }
    .analysis-head {
        background: linear-gradient(110deg, #ffffff 0%, #f0f9f4 100%);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(15, 39, 71, 0.05);
        padding: 14px 18px;
        margin-bottom: 12px;
    }
    .analysis-head h1 {
        margin: 0 0 3px;
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.2;
        color: var(--text-main);
    }
    .analysis-head p {
        margin: 0;
        color: var(--text-soft);
        font-size: .74rem;
    }
    .analysis-filter {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(15, 39, 71, 0.05);
        padding: 14px 18px;
        margin-bottom: 14px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: minmax(240px, 2fr) repeat(2, minmax(130px, 1fr)) auto;
        gap: 12px;
        align-items: flex-end;
    }
    .field { min-width: 0; }
    .field label {
        display: block;
        margin-bottom: 5px;
        color: var(--text-soft);
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .03em;
        text-transform: uppercase;
    }
    .field select {
        width: 100%;
        min-width: 0;
        height: 38px;
        border: 1px solid #d8e2ee;
        border-radius: 8px;
        padding: 0 30px 0 12px;
        background: #ffffff;
        color: var(--text-main);
        font: inherit;
        font-size: .82rem;
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .field select:focus {
        outline: 0;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, .15);
    }
    .custom-select-container { position: relative; width: 100%; min-width: 0; }
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
        background: #ffffff;
        color: var(--text-main);
        font: inherit;
        font-size: .82rem;
        font-weight: 600;
        cursor: pointer;
        text-align: left;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .custom-select-trigger:focus, .custom-select-container.is-open .custom-select-trigger {
        outline: 0;
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, .15);
    }
    .custom-select-label { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .custom-select-arrow { font-size: .68rem; color: #64748b; transition: transform .2s ease; flex-shrink: 0; }
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
    .custom-select-container.is-indikator .custom-select-menu { min-width: 280px; }
    .custom-select-container.is-open .custom-select-menu { display: flex; animation: dropFade .15s cubic-bezier(0.16, 1, 0.3, 1); }
    .custom-select-search {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-bottom: 1px solid #f1f5f9;
        background: #f8fafc;
    }
    .custom-select-search i { color: #94a3b8; font-size: .75rem; }
    .custom-select-search input {
        border: 0;
        outline: 0;
        background: transparent;
        font: inherit;
        font-size: .78rem;
        width: 100%;
        color: var(--text-main);
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
    .filter-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .submit-btn, .reset-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        border-radius: 8px;
        padding: 0 16px;
        font-size: .82rem;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
        gap: 6px;
        box-sizing: border-box;
        transition: all .15s ease;
    }
    .submit-btn {
        background: #10b981;
        color: #ffffff;
        border: 1px solid #10b981;
        box-shadow: 0 2px 6px rgba(16, 185, 129, .25);
    }
    .submit-btn:hover {
        background: #059669;
        border-color: #059669;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(16, 185, 129, .35);
    }
    .reset-btn {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .reset-btn:hover {
        background: #e2e8f0;
        color: #1e293b;
        transform: translateY(-1px);
    }
    .section-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0 16px; }
    .section-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        border-radius: 8px;
        padding: 8px 16px;
        text-decoration: none;
        font-size: .8rem;
        font-weight: 700;
        transition: all .15s ease;
    }
    .section-tab:hover {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }
    .section-tab.active {
        background: #10b981;
        color: #ffffff;
        border-color: #10b981;
        box-shadow: 0 2px 6px rgba(16, 185, 129, .25);
    }
    .info-panel {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(15, 39, 71, 0.05);
        padding: 14px 18px;
        margin-bottom: 14px;
    }
    .info-panel h2 { margin: 0 0 10px; font-size: .95rem; font-weight: 800; color: var(--text-main); }
    .info-list { display: flex; flex-wrap: wrap; gap: 16px; font-size: .82rem; color: var(--text-soft); }
    .info-list strong { color: var(--text-main); }
    .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; margin-bottom: 14px; }
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(15, 39, 71, 0.04);
        padding: 12px 14px;
        transition: border-color .15s, box-shadow .15s;
    }
    .stat-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 12px rgba(15, 39, 71, 0.06); }
    .stat-card .label { display: block; color: var(--text-soft); font-size: .68rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .stat-card .value { display: block; margin-top: 6px; color: var(--text-main); font-size: 1.25rem; font-weight: 800; letter-spacing: -.02em; }
    .stat-card .meta { display: block; margin-top: 4px; color: var(--text-soft); font-size: .72rem; }
    .stat-card.muted { background: #ffffff; }
    .projection-card { background: linear-gradient(180deg, #f8fbff 0%, #edf4ff 100%); border: 1px solid #cfe1ff; }
    .projection-card-blue { background: linear-gradient(180deg, #eef6ff 0%, #dfeeff 100%); }
    .projection-card-sky { background: linear-gradient(180deg, #edf9ff 0%, #dff3ff 100%); }
    .projection-card-indigo { background: linear-gradient(180deg, #eef2ff 0%, #e0e9ff 100%); }
    .projection-card-cyan { background: linear-gradient(180deg, #effcff 0%, #dff9ff 100%); }
    .chart-card, .table-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(15, 39, 71, 0.05);
        padding: 16px 18px;
        margin-bottom: 14px;
    }
    .chart-card h3, .table-card h3 { margin: 0 0 4px; font-size: .95rem; font-weight: 800; color: var(--text-main); }
    .chart-card p, .table-card p { margin: 0 0 12px; color: var(--text-soft); font-size: .76rem; }
    .chart-container { position: relative; height: 300px; }
    .summary-change { margin-top: 10px; font-size: .84rem; color: var(--text-main); }
    .data-table-responsive { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; box-shadow: 0 2px 8px rgba(15, 39, 71, 0.04); }
    table { width: 100%; border-collapse: collapse; min-width: 760px; font-size: .82rem; }
    table.table-fit { min-width: 100%; }
    .th-compact { display: none; }
    .cell-sumber { white-space: normal; min-width: 120px; max-width: 220px; word-break: normal; line-height: 1.35; }
    thead th {
        background: #f8fafc;
        color: var(--text-soft);
        font-size: .68rem;
        letter-spacing: .05em;
        text-transform: uppercase;
        font-weight: 800;
        text-align: left;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
    }
    tbody td {
        padding: 9px 14px;
        color: var(--text-main);
        border-bottom: 1px solid #edf2f8;
    }
    tbody tr:hover td { background: #f8fafc; }
    th.numeric, td.numeric { text-align: right; }
    .badge-status { display: inline-block; padding: 2px 8px; border-radius: 999px; font-weight: 700; font-size: .68rem; }
    .badge-up { background: #e8f7ee; color: #198754; }
    .badge-down { background: #fce9ea; color: #dc3545; }
    .badge-stable { background: #f4f6f9; color: #465467; }

    .analysis-empty-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(15, 39, 71, 0.04);
        padding: 44px 24px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .empty-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #ecfdf5;
        color: #10b981;
        border: 1px solid #a7f3d0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 14px;
    }
    .analysis-empty-card h3 {
        margin: 0 0 6px;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--text-main);
    }
    .analysis-empty-card p {
        margin: 0;
        max-width: 520px;
        font-size: .82rem;
        color: var(--text-soft);
        line-height: 1.5;
    }
    .analysis-section.hidden { display: none; }
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
    @media (max-width: 900px) {
        .filter-grid { grid-template-columns: 1fr 1fr; }
        .field.filter-actions { grid-column: 1 / -1; }
        .stats-grid { grid-template-columns: repeat(2, minmax(140px, 1fr)); }
    }
    @media (max-width: 600px) {
        .analysis-page { padding-bottom: 24px; }
        .analysis-head { padding: 12px 14px; border-radius: 12px; }
        .analysis-head h1 { font-size: 1.15rem; line-height: 1.25; }
        .analysis-head p { font-size: 0.76rem; }
        .analysis-filter { padding: 12px 14px; border-radius: 12px; }
        .filter-grid { grid-template-columns: 1fr; gap: 10px; }
        .field select {
            height: 42px;
            font-size: 16px; /* Mencegah auto-zoom di iOS Safari */
            border-radius: 9px;
        }
        .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            width: 100%;
        }
        .submit-btn, .reset-btn {
            height: 42px;
            font-size: 0.82rem;
            justify-content: center;
            width: 100%;
            border-radius: 8px;
        }
        .section-tabs {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            gap: 8px;
            padding-bottom: 6px;
            margin: 12px 0 14px;
        }
        .section-tab {
            white-space: nowrap;
            flex-shrink: 0;
            padding: 8px 14px;
            font-size: 0.78rem;
            border-radius: 8px;
        }
        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
        .stat-card {
            padding: 10px 12px;
            border-radius: 10px;
        }
        .stat-card .label { font-size: 0.65rem; }
        .stat-card .value { font-size: 1.15rem; margin-top: 3px; }
        .stat-card .meta { font-size: 0.68rem; margin-top: 3px; }
        .chart-card, .table-card {
            padding: 12px 14px;
            border-radius: 12px;
        }
        .chart-container { height: 240px; }
        .th-full { display: none; }
        .th-compact { display: inline; }
        .data-table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            border-radius: 10px;
            scrollbar-width: thin;
        }
        table {
            width: 100%;
            min-width: 440px;
            table-layout: auto;
            border-collapse: collapse;
        }
        table.table-fit {
            min-width: 100%;
        }
        thead th, tbody td {
            padding: 8px 10px;
            font-size: 0.76rem;
            white-space: nowrap;
            word-break: normal;
            vertical-align: middle;
        }
        thead th {
            font-size: 0.68rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            font-weight: 800;
            padding: 8px 10px;
            background: #f8fafc;
            color: var(--text-soft);
        }
        th.numeric, td.numeric {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-weight: 600;
        }
        .badge-status {
            padding: 2px 7px;
            font-size: 0.65rem;
            white-space: nowrap;
        }
        .analysis-empty-card {
            padding: 32px 16px;
            border-radius: 12px;
        }
        .info-panel {
            padding: 12px 14px;
            border-radius: 12px;
        }
    }
    @media (max-width: 380px) {
        .filter-actions {
            grid-template-columns: 1fr;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="analysis-page">
    <div class="analysis-head">
        <h1>Analisis Indikator Pembangunan Makro</h1>
        <p>Analisis perkembangan indikator berdasarkan data capaian Kota Tasikmalaya.</p>
    </div>

    <section class="analysis-filter">
        <form method="GET" action="{{ $analysisRoute }}">
            <div class="filter-grid">
                <div class="field">
                    <label for="filter-indikator-analisis">Indikator</label>
                    <input type="hidden" name="indikator" id="filter-indikator-analisis" value="{{ $selectedIndikator?->id }}">
                    <div class="custom-select-container is-indikator" id="analisis-indikator-dropdown">
                        <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                            <span class="custom-select-label">{{ $selectedIndikator?->nama_indikator ?? 'Pilih Indikator' }}</span>
                            <i class="bi bi-chevron-down custom-select-arrow"></i>
                        </button>
                        <div class="custom-select-menu" role="listbox">
                            <div class="custom-select-search">
                                <i class="bi bi-search"></i>
                                <input type="text" class="custom-select-search-input" placeholder="Cari indikator..." autocomplete="off">
                            </div>
                            <div class="custom-select-options">
                                <button type="button" class="custom-select-option {{ !$selectedIndikator ? 'is-selected' : '' }}" data-value="" data-label="Pilih Indikator">
                                    <span>Pilih Indikator</span>
                                    @if(!$selectedIndikator) <i class="bi bi-check2 check-icon"></i> @endif
                                </button>
                                @foreach ($allIndikators as $indikator)
                                    <button type="button" class="custom-select-option {{ (int) ($selectedIndikator?->id ?? 0) === (int) $indikator->id ? 'is-selected' : '' }}" data-value="{{ $indikator->id }}" data-label="{{ $indikator->nama_indikator }}">
                                        <span>{{ $indikator->nama_indikator }}</span>
                                        @if((int) ($selectedIndikator?->id ?? 0) === (int) $indikator->id) <i class="bi bi-check2 check-icon"></i> @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label for="filter-tahun-mulai">Tahun Mulai</label>
                    <input type="hidden" name="tahun_mulai" id="filter-tahun-mulai" value="{{ $tahunMulai }}">
                    <div class="custom-select-container" id="analisis-tahun-mulai-dropdown">
                        <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                            <span class="custom-select-label">{{ $tahunMulai }}</span>
                            <i class="bi bi-chevron-down custom-select-arrow"></i>
                        </button>
                        <div class="custom-select-menu" role="listbox">
                            <div class="custom-select-options">
                                @foreach ($years as $year)
                                    <button type="button" class="custom-select-option {{ (int) $tahunMulai === (int) $year ? 'is-selected' : '' }}" data-value="{{ $year }}">
                                        <span>{{ $year }}</span>
                                        @if((int) $tahunMulai === (int) $year) <i class="bi bi-check2 check-icon"></i> @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label for="filter-tahun-akhir">Tahun Akhir</label>
                    <input type="hidden" name="tahun_akhir" id="filter-tahun-akhir" value="{{ $tahunAkhir }}">
                    <div class="custom-select-container" id="analisis-tahun-akhir-dropdown">
                        <button type="button" class="custom-select-trigger" aria-haspopup="listbox" aria-expanded="false">
                            <span class="custom-select-label">{{ $tahunAkhir }}</span>
                            <i class="bi bi-chevron-down custom-select-arrow"></i>
                        </button>
                        <div class="custom-select-menu" role="listbox">
                            <div class="custom-select-options">
                                @foreach ($years as $year)
                                    <button type="button" class="custom-select-option {{ (int) $tahunAkhir === (int) $year ? 'is-selected' : '' }}" data-value="{{ $year }}">
                                        <span>{{ $year }}</span>
                                        @if((int) $tahunAkhir === (int) $year) <i class="bi bi-check2 check-icon"></i> @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="field filter-actions">
                    <button type="submit" class="submit-btn"><i class="bi bi-search"></i> Tampilkan Analisis</button>
                    <a href="{{ $analysisRoute }}" class="reset-btn"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                </div>
            </div>
        </form>
    </section>

    @if ($selectedIndikator)
        <section class="info-panel">
            <h2>Informasi Indikator</h2>
            <div class="info-list">
                <div><span>Indikator:</span> <strong>{{ $selectedIndikator->nama_indikator }}</strong></div>
                <div><span>Satuan:</span> <strong>{{ $selectedIndikator->satuan ?? '-' }}</strong></div>
            </div>
        </section>
    @endif

    @if (!$selectedIndikator)
        <div class="analysis-empty-card">
            <div class="empty-icon-wrapper">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h3>Pilih Indikator & Rentang Tahun</h3>
            <p>Silakan pilih indikator dan rentang tahun untuk melihat analisis. Pantau perkembangan tren data dan pencapaian target pembangunan makro Kota Tasikmalaya secara komprehensif.</p>
        </div>
    @elseif ($capaians->isEmpty())
        <div class="analysis-empty-card">
            <div class="empty-icon-wrapper" style="background: #fef2f2; color: #ef4444; border-color: #fee2e2;">
                <i class="bi bi-clipboard-x"></i>
            </div>
            <h3>Data Belum Tersedia</h3>
            <p>Belum tersedia data untuk indikator dan periode yang dipilih. Silakan coba rentang tahun lain.</p>
        </div>
    @else
        <section class="stats-grid">
            <div class="stat-card muted">
                <span class="label">Nilai Awal</span>
                <span class="value">{{ $stats['nilaiAwal'] !== null ? number_format((float) $stats['nilaiAwal'], 2, ',', '.') : '-' }}</span>
                <span class="meta">Tahun {{ $stats['tahunAwal'] ?? '-' }}</span>
            </div>
            <div class="stat-card muted">
                <span class="label">Nilai Akhir</span>
                <span class="value">{{ $stats['nilaiAkhir'] !== null ? number_format((float) $stats['nilaiAkhir'], 2, ',', '.') : '-' }}</span>
                <span class="meta">Tahun {{ $stats['tahunAkhir'] ?? '-' }}</span>
            </div>
            <div class="stat-card muted">
                <span class="label">Perubahan</span>
                <span class="value">{{ $stats['perubahan'] !== null ? number_format((float) $stats['perubahan'], 2, ',', '.') : '-' }}</span>
                <span class="meta"><span class="badge-status {{ $stats['status'] === 'Meningkat' ? 'badge-up' : ($stats['status'] === 'Menurun' ? 'badge-down' : 'badge-stable') }}">{{ $stats['status'] }}</span></span>
            </div>
            <div class="stat-card muted">
                <span class="label">% Perubahan</span>
                <span class="value">{{ $stats['persenPerubahan'] !== null ? number_format((float) $stats['persenPerubahan'], 2, ',', '.') . '%' : '-' }}</span>
                <span class="meta">Dari tahun awal ke akhir</span>
            </div>
            <div class="stat-card muted">
                <span class="label">Nilai Tertinggi</span>
                <span class="value">{{ $stats['nilaiTertinggi'] !== null ? number_format((float) $stats['nilaiTertinggi'], 2, ',', '.') : '-' }}</span>
                <span class="meta">Tahun {{ $stats['tahunTertinggi'] ?? '-' }}</span>
            </div>
            <div class="stat-card muted">
                <span class="label">Nilai Terendah</span>
                <span class="value">{{ $stats['nilaiTerendah'] !== null ? number_format((float) $stats['nilaiTerendah'], 2, ',', '.') : '-' }}</span>
                <span class="meta">Tahun {{ $stats['tahunTerendah'] ?? '-' }}</span>
            </div>
            <div class="stat-card muted">
                <span class="label">Rata-rata</span>
                <span class="value">{{ $stats['rataRata'] !== null ? number_format((float) $stats['rataRata'], 2, ',', '.') : '-' }}</span>
                <span class="meta">Berdasarkan data tersedia</span>
            </div>
            <div class="stat-card muted">
                <span class="label">Jumlah Data</span>
                <span class="value">{{ $stats['jumlahData'] }}</span>
                <span class="meta">data</span>
            </div>
            <div class="stat-card muted">
                <span class="label">Tren Pertumbuhan</span>
                <span class="value">{{ $growthData['average'] !== null ? number_format((float) $growthData['average'], 2, ',', '.') . ($growthData['unit'] ?? '%') : '-' }}</span>
                <span class="meta"><span class="badge-status {{ ($growthData['trend'] ?? 'Stabil') === 'Meningkat' ? 'badge-up' : (($growthData['trend'] ?? 'Stabil') === 'Menurun' ? 'badge-down' : 'badge-stable') }}">{{ $growthData['trend'] ?? 'Stabil' }}</span></span>
            </div>
            <div class="stat-card muted">
                <span class="label">Proyeksi</span>
                <span class="value">{{ $projectionData['summary']['lastValue'] !== null ? number_format((float) $projectionData['summary']['lastValue'], 2, ',', '.') : '-' }}</span>
                <span class="meta">Tahun {{ $projectionData['summary']['lastYear'] ?? '-' }}</span>
            </div>
        </section>

        <div class="section-tabs">
            <button type="button" class="section-tab active" data-target="historis">Data Historis</button>
            <button type="button" class="section-tab" data-target="tren-pertumbuhan">Tren Pertumbuhan</button>
            <button type="button" class="section-tab" data-target="proyeksi">Proyeksi</button>
        </div>

        <section id="historis" class="chart-card analysis-section" data-section="historis">
            <h3>Perkembangan {{ $selectedIndikator->nama_indikator }}</h3>
            <p>Data capaian dan target pada rentang tahun yang dipilih.</p>
            <div class="chart-container">
                <canvas id="analisisChart"></canvas>
            </div>
        </section>

        <section class="chart-card analysis-section hidden" data-section="historis">
            <h3>Ringkasan Perubahan</h3>
            @php
                $changeAbs = $stats['perubahan'] ?? 0;
                $changeText = 'Nilai indikator tidak mengalami perubahan antara tahun awal dan tahun akhir.';
                if ($changeAbs > 0) {
                    $changeText = 'Nilai indikator meningkat sebesar ' . number_format((float) abs($changeAbs), 2, '.', '') . ' dari tahun ' . $stats['tahunAwal'] . ' sampai ' . $stats['tahunAkhir'] . '.';
                } elseif ($changeAbs < 0) {
                    $changeText = 'Nilai indikator menurun sebesar ' . number_format((float) abs($changeAbs), 2, '.', '') . ' dari tahun ' . $stats['tahunAwal'] . ' sampai ' . $stats['tahunAkhir'] . '.';
                }
            @endphp
            <p class="summary-change">{{ $changeText }}</p>
        </section>

        <section id="tren-pertumbuhan" class="chart-card analysis-section hidden" data-section="tren-pertumbuhan">
            <h3>Tren Pertumbuhan {{ $selectedIndikator->nama_indikator }}</h3>
            <p>Melihat pola pertumbuhan indikator berdasarkan data tahun {{ $tahunMulai }}–{{ $tahunAkhir }}.</p>
            <div class="chart-container">
                <canvas id="growthChart"></canvas>
            </div>
            <div class="stats-grid" style="margin-top: 18px; margin-bottom: 0;">
                <div class="stat-card muted">
                    <span class="label">Rata-rata Pertumbuhan</span>
                    <span class="value">{{ $growthData['average'] !== null ? number_format((float) $growthData['average'], 2, ',', '.') . ($growthData['unit'] ?? '%') : '-' }}</span>
                    <span class="meta">Berdasarkan seluruh tahun yang valid</span>
                </div>
                <div class="stat-card muted">
                    <span class="label">Tren Pertumbuhan</span>
                    <span class="value" style="font-size: 1.15rem;">{{ $growthData['trend'] ?? 'Stabil' }}</span>
                    <span class="meta">Kecenderungan keseluruhan</span>
                </div>
                <div class="stat-card muted">
                    <span class="label">Pertumbuhan Tertinggi</span>
                    <span class="value">{{ $growthData['highest'] !== null ? number_format((float) $growthData['highest'], 2, ',', '.') . ($growthData['unit'] ?? '%') : '-' }}</span>
                    <span class="meta">{{ $growthData['highestYear'] ? 'Tahun ' . $growthData['highestYear'] : '-' }}</span>
                </div>
                <div class="stat-card muted">
                    <span class="label">Pertumbuhan Terendah</span>
                    <span class="value">{{ $growthData['lowest'] !== null ? number_format((float) $growthData['lowest'], 2, ',', '.') . ($growthData['unit'] ?? '%') : '-' }}</span>
                    <span class="meta">{{ $growthData['lowestYear'] ? 'Tahun ' . $growthData['lowestYear'] : '-' }}</span>
                </div>
            </div>
        </section>

        <section class="table-card analysis-section hidden" data-section="tren-pertumbuhan">
            <h3>Data Pertumbuhan</h3>
            <p>Pertumbuhan dihitung dari nilai indikator tahun sebelumnya.</p>
            <div class="data-table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th class="numeric">Capaian</th>
                            <th class="numeric">Perubahan</th>
                            <th class="numeric">
                                <span class="th-full">Pertumbuhan ({{ $growthData['unit'] ?? '%' }})</span>
                                <span class="th-compact">{{ $growthData['unit'] ?? '%' }}</span>
                            </th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($growthData['rows'] as $row)
                            <tr>
                                <td>{{ $row['tahun'] }}</td>
                                <td class="numeric">{{ $row['capaian'] !== null ? number_format((float) $row['capaian'], 2, ',', '.') : '-' }}</td>
                                <td class="numeric">{{ $row['perubahan'] !== null ? number_format((float) $row['perubahan'], 2, ',', '.') : '-' }}</td>
                                <td class="numeric">{{ $row['pertumbuhan'] !== null ? number_format((float) $row['pertumbuhan'], 2, ',', '.') . ($growthData['unit'] ?? '%') : '-' }}</td>
                                <td>
                                    @php
                                        $growthStatus = 'Aktual';
                                        if ($row['perubahan'] !== null && $row['perubahan'] > 0) { $growthStatus = 'Naik'; }
                                        elseif ($row['perubahan'] !== null && $row['perubahan'] < 0) { $growthStatus = 'Turun'; }
                                    @endphp
                                    <span class="badge-status {{ $growthStatus === 'Naik' ? 'badge-up' : ($growthStatus === 'Turun' ? 'badge-down' : 'badge-stable') }}">{{ $growthStatus }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section id="proyeksi" class="chart-card analysis-section hidden" data-section="proyeksi">
            <h3>Proyeksi {{ $selectedIndikator->nama_indikator }}</h3>
            <p>Estimasi 5 tahun ke depan menggunakan metode regresi linear sederhana berdasarkan data capaian historis.</p>
            <div class="chart-container">
                <canvas id="projectionChart"></canvas>
            </div>
            <div class="stats-grid" style="margin-top: 18px; margin-bottom: 0;">
                <div class="stat-card muted">
                    <span class="label">Nilai Proyeksi Tahun Pertama</span>
                    <span class="value">{{ $projectionData['summary']['firstValue'] !== null ? number_format((float) $projectionData['summary']['firstValue'], 2, ',', '.') : '-' }}</span>
                    <span class="meta">{{ $projectionData['summary']['firstYear'] ? 'Tahun ' . $projectionData['summary']['firstYear'] : '-' }}</span>
                </div>
                <div class="stat-card muted">
                    <span class="label">Nilai Proyeksi Tahun Terakhir</span>
                    <span class="value">{{ $projectionData['summary']['lastValue'] !== null ? number_format((float) $projectionData['summary']['lastValue'], 2, ',', '.') : '-' }}</span>
                    <span class="meta">{{ $projectionData['summary']['lastYear'] ? 'Tahun ' . $projectionData['summary']['lastYear'] : '-' }}</span>
                </div>
                <div class="stat-card muted">
                    <span class="label">Perubahan Proyeksi</span>
                    <span class="value">{{ $projectionData['summary']['change'] !== null ? number_format((float) $projectionData['summary']['change'], 2, ',', '.') : '-' }}</span>
                    <span class="meta">Dari tahun pertama ke akhir proyeksi</span>
                </div>
                <div class="stat-card muted">
                    <span class="label">Pertumbuhan Proyeksi</span>
                    <span class="value">{{ $projectionData['summary']['growth'] !== null ? number_format((float) $projectionData['summary']['growth'], 2, ',', '.') . ($projectionData['unit'] ?? '%') : '-' }}</span>
                    <span class="meta">{{ $projectionData['summary']['status'] ?? 'Proyeksi' }}</span>
                </div>
            </div>
        </section>

        @if (!empty($projectionData['rows']))
            <section class="table-card analysis-section hidden" data-section="proyeksi">
                <h3>Tabel Proyeksi</h3>
                <div class="data-table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th class="numeric">
                                    <span class="th-full">Proyeksi Nilai</span>
                                    <span class="th-compact">Proyeksi</span>
                                </th>
                                <th class="numeric">Perubahan</th>
                                <th class="numeric">
                                    <span class="th-full">Pertumbuhan ({{ $projectionData['unit'] ?? '%' }})</span>
                                    <span class="th-compact">{{ $projectionData['unit'] ?? '%' }}</span>
                                </th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projectionData['rows'] as $projection)
                                <tr>
                                    <td>{{ $projection['tahun'] }}</td>
                                    <td class="numeric">{{ number_format((float) $projection['nilai'], 2, ',', '.') }}</td>
                                    <td class="numeric">{{ $projection['perubahan'] !== null ? number_format((float) $projection['perubahan'], 2, ',', '.') : '-' }}</td>
                                    <td class="numeric">{{ $projection['pertumbuhan'] !== null ? number_format((float) $projection['pertumbuhan'], 2, ',', '.') . ($projectionData['unit'] ?? '%') : '-' }}</td>
                                    <td>
                                        <span class="badge-status {{ $projection['status'] === 'Target tercapai' ? 'badge-up' : ($projection['status'] === 'Di bawah target' ? 'badge-down' : 'badge-stable') }}">{{ $projection['status'] ?: 'Proyeksi' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <section class="analysis-section hidden" data-section="proyeksi" style="background: #ffffff; border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow); padding: 14px 16px; margin-bottom: 18px;">
            <h3 style="margin: 0 0 8px; font-size: 0.95rem;">Kesimpulan</h3>
            @php
                $trendSummary = $growthData['trend'] ?? 'Stabil';
                $avgGrowth = $growthData['average'];
                $projectionFirst = $projectionData['summary']['firstValue'];
                $projectionLast = $projectionData['summary']['lastValue'];
                $projectionLastYear = $projectionData['summary']['lastYear'];
                $targetYear = $comparisonTarget['tahun'] ?? null;
                $targetValue = $comparisonTarget['target'] ?? null;
                $conclusion = 'Indikator menunjukkan tren ' . strtolower($trendSummary) . ' dalam periode yang dipilih.';
                if ($avgGrowth !== null) {
                    $conclusion .= ' Rata-rata pertumbuhan sebesar ' . number_format((float) $avgGrowth, 2, ',', '.') . '%.';
                }
                if ($projectionFirst !== null && $projectionLast !== null && $projectionLastYear) {
                    $conclusion .= ' Berdasarkan regresi linear, nilai diproyeksikan mencapai sekitar ' . number_format((float) $projectionLast, 2, ',', '.') . ' pada tahun ' . $projectionLastYear . '.';
                }
                if ($targetYear !== null && $targetValue !== null) {
                    $conclusion .= ' Target tahun ' . $targetYear . ' adalah ' . number_format((float) $targetValue, 2, ',', '.') . '.';
                }
            @endphp
            <p style="margin: 0; font-size: 0.82rem; color: var(--text-soft); line-height: 1.5;">{{ $conclusion }}</p>
        </section>

        <section class="table-card analysis-section" data-section="historis">
            <h3>Data Analisis</h3>
            <p>Perubahan dihitung dari capaian tahun sebelumnya.</p>
            <div class="data-table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th class="numeric">Target</th>
                            <th class="numeric">Capaian</th>
                            <th class="numeric">
                                <span class="th-full">Persentase Capaian terhadap Target</span>
                                <span class="th-compact">% Target</span>
                            </th>
                            <th class="numeric">
                                <span class="th-full">Perubahan dari Tahun Sebelumnya</span>
                                <span class="th-compact">Perubahan</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analysisData as $index => $row)
                            <tr>
                                <td>{{ $row['tahun'] }}</td>
                                <td class="numeric">{{ $row['target'] !== null ? number_format((float) $row['target'], 2, ',', '.') : '-' }}</td>
                                <td class="numeric">{{ $row['capaian'] !== null ? number_format((float) $row['capaian'], 2, ',', '.') : '-' }}</td>
                                <td class="numeric">{{ $row['persentaseTarget'] !== null ? number_format((float) $row['persentaseTarget'], 2, ',', '.') . '%' : '-' }}</td>
                                <td class="numeric">{{ $row['perubahan'] !== null ? number_format((float) $row['perubahan'], 2, ',', '.') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if ($comparisonTarget)
            <section class="table-card">
                <h3>Perbandingan Target</h3>
                <p>Target dan capaian pada tahun terakhir yang tersedia dalam periode.</p>
                <div class="data-table-responsive">
                    <table class="table-fit">
                        <thead>
                            <tr>
                                <th class="numeric">Target ({{ $comparisonTarget['tahun'] }})</th>
                                <th class="numeric">Capaian ({{ $comparisonTarget['tahun'] }})</th>
                                <th class="numeric">Selisih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="numeric">{{ $comparisonTarget['target'] !== null ? number_format((float) $comparisonTarget['target'], 2, ',', '.') : 'Belum tersedia' }}</td>
                                <td class="numeric">{{ $comparisonTarget['capaian'] !== null ? number_format((float) $comparisonTarget['capaian'], 2, ',', '.') : 'Belum tersedia' }}</td>
                                <td class="numeric">{{ $comparisonTarget['selisih'] !== null ? number_format((float) $comparisonTarget['selisih'], 2, ',', '.') : 'Belum tersedia' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if ($periodTargets->isNotEmpty())
            <section class="table-card">
                <h3>Target Berdasarkan Periode</h3>
                <div class="data-table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th><span class="th-full">Jenis Target</span><span class="th-compact">Jenis</span></th>
                                <th>Periode</th>
                                <th class="numeric"><span class="th-full">Nilai Target</span><span class="th-compact">Target</span></th>
                                <th>Sumber</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($periodTargets as $target)
                                <tr>
                                    <td>{{ $target->jenis_target ?? '-' }}</td>
                                    <td>{{ $target->periode ?? '-' }}</td>
                                    <td class="numeric">{{ $target->nilai_target_range ?? ($target->nilai_target !== null ? number_format((float) $target->nilai_target, 2, ',', '.') : 'Belum tersedia') }}</td>
                                    <td class="cell-sumber">{{ $target->sumber ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.querySelectorAll('.section-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
            const target = this.dataset.target;
            document.querySelectorAll('.section-tab').forEach(function(item) {
                item.classList.toggle('active', item === tab);
            });

            document.querySelectorAll('.analysis-section').forEach(function(section) {
                const shouldShow = section.dataset.section === target;
                section.classList.toggle('hidden', !shouldShow);
            });
        });
    });

    const labels = @json($chartYears);
    const capaianData = @json(array_values($chartCapaian));
    const targetData = @json(array_values($chartTarget));

    if (labels.length && document.getElementById('analisisChart')) {
        new Chart(document.getElementById('analisisChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Capaian',
                        data: capaianData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.10)',
                        fill: true,
                        tension: 0.3,
                        spanGaps: true,
                    },
                    {
                        label: 'Target',
                        data: targetData,
                        borderColor: '#f4c95d',
                        backgroundColor: 'rgba(244, 201, 93, 0.18)',
                        fill: false,
                        tension: 0.2,
                        spanGaps: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + (context.parsed.y ?? '-') + ' {{ $selectedIndikator->satuan ?? "" }}';
                            }
                        }
                    }
                },
                scales: {
                    x: { title: { display: true, text: 'Tahun' } },
                    y: { title: { display: true, text: 'Nilai' }, beginAtZero: false }
                }
            }
        });
    }

    const growthLabels = @json($growthData['chartLabels'] ?? []);
    const growthValues = @json($growthData['chartValues'] ?? []);
    const growthUnit = @json($growthData['unit'] ?? '%');
    if (growthLabels.length && document.getElementById('growthChart')) {
        const validGrowths = growthValues.filter(v => v !== null && !isNaN(v));
        let yMin = -1;
        let yMax = 1;
        if (validGrowths.length) {
            const minG = Math.min(0, ...validGrowths);
            const maxG = Math.max(0, ...validGrowths);
            const diff = maxG - minG;
            const padding = diff > 0 ? Math.max(diff * 0.15, 0.5) : 0.5;
            yMin = minG < 0 ? minG - padding : -padding * 0.5;
            yMax = maxG + padding;
        }

        new Chart(document.getElementById('growthChart'), {
            type: 'line',
            data: {
                labels: growthLabels,
                datasets: [{
                    label: 'Pertumbuhan (' + growthUnit + ')',
                    data: growthValues,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.18)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.25,
                    spanGaps: true,
                    pointRadius: 4,
                }, {
                    label: 'Referensi 0' + growthUnit,
                    data: growthLabels.map(() => 0),
                    borderColor: '#c5ccd6',
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    tension: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.parsed.y ?? 0;
                                return context.dataset.label + ': ' + Number(val).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + growthUnit;
                            }
                        }
                    }
                },
                scales: {
                    x: { title: { display: true, text: 'Tahun' } },
                    y: {
                        title: { display: true, text: 'Pertumbuhan (' + growthUnit + ')' },
                        suggestedMin: yMin,
                        suggestedMax: yMax,
                        ticks: {
                            callback: function(value) {
                                return Number(value).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + growthUnit;
                            }
                        }
                    }
                }
            }
        });
    }

    const projectionLabels = @json($projectionData['combinedLabels'] ?? []);
    const projectionActual = @json($projectionData['combinedActual'] ?? []);
    const projectionTarget = @json($projectionData['combinedTarget'] ?? []);
    const projectionSeries = @json($projectionData['combinedProjection'] ?? []);
    const projectionZonePlugin = {
        id: 'projectionZone',
        beforeDatasetsDraw(chart) {
            const firstProjectionIndex = projectionSeries.findIndex((value) => value !== null);
            if (firstProjectionIndex < 0 || !chart.chartArea || !chart.scales.x) return;

            const { ctx, chartArea, scales } = chart;
            const startX = scales.x.getPixelForValue(firstProjectionIndex);
            ctx.save();
            ctx.fillStyle = 'rgba(244, 201, 93, 0.14)';
            ctx.fillRect(startX, chartArea.top, chartArea.right - startX, chartArea.bottom - chartArea.top);
            ctx.restore();
        }
    };
    if (projectionLabels.length && document.getElementById('projectionChart')) {
        new Chart(document.getElementById('projectionChart'), {
            type: 'line',
            data: {
                labels: projectionLabels,
                datasets: [
                    {
                        label: 'Capaian',
                        data: projectionActual,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.10)',
                        fill: false,
                        tension: 0.25,
                        spanGaps: true,
                    },
                    {
                        label: 'Target',
                        data: projectionTarget,
                        borderColor: '#f4c95d',
                        backgroundColor: 'rgba(244, 201, 93, 0.18)',
                        fill: false,
                        tension: 0.2,
                        spanGaps: true,
                    },
                    {
                        label: 'Proyeksi',
                        data: projectionSeries,
                        borderColor: '#059669',
                        backgroundColor: '#059669',
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#059669',
                        pointRadius: 4,
                        borderWidth: 2.5,
                        borderDash: [6, 6],
                        fill: false,
                        tension: 0.2,
                        spanGaps: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y ?? 0;
                                const label = context.dataset.label;
                                const year = projectionLabels[context.dataIndex];
                                const status = label === 'Proyeksi' ? 'Proyeksi' : (projectionSeries[context.dataIndex] !== null ? 'Proyeksi' : 'Aktual');
                                return label + ': ' + value.toFixed(2) + ' (' + year + ' - ' + status + ')';
                            }
                        }
                    }
                },
                scales: {
                    x: { title: { display: true, text: 'Tahun' } },
                    y: { title: { display: true, text: 'Nilai' }, beginAtZero: false }
                }
            },
            plugins: [projectionZonePlugin]
        });
    }

    // Handler Custom Dropdown Analisis
    const initAnalisisSelect = (containerId, inputId) => {
        const container = document.getElementById(containerId);
        if (!container) return;
        const trigger = container.querySelector('.custom-select-trigger');
        const triggerLabel = trigger.querySelector('.custom-select-label');
        const hiddenInput = document.getElementById(inputId);
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
                const val = opt.getAttribute('data-value');
                hiddenInput.value = val;
                triggerLabel.textContent = opt.getAttribute('data-label') || opt.querySelector('span').textContent;
                
                options.forEach(o => {
                    o.classList.remove('is-selected');
                    const existingCheck = o.querySelector('.check-icon');
                    if (existingCheck) existingCheck.remove();
                });
                opt.classList.add('is-selected');
                const check = document.createElement('i');
                check.className = 'bi bi-check2 check-icon';
                opt.appendChild(check);

                toggle(false);
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

    initAnalisisSelect('analisis-indikator-dropdown', 'filter-indikator-analisis');
    initAnalisisSelect('analisis-tahun-mulai-dropdown', 'filter-tahun-mulai');
    initAnalisisSelect('analisis-tahun-akhir-dropdown', 'filter-tahun-akhir');
</script>
@endsection
