@extends('layouts.admin')

@section('title', 'Detail Indikator - ' . $indikator->nama_indikator)

@section('content')
<style>
    .detail-page { width: 100%; min-width: 0; overflow: auto; padding-bottom: 16px; }
    .detail-head {
        background: linear-gradient(110deg, #ffffff 0%, #f4faff 100%);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(15, 39, 71, 0.05);
        padding: 14px 18px;
        margin-bottom: 12px;
    }
    .detail-head-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .detail-head-title {
        flex: 1;
        min-width: 240px;
    }
    .title-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 5px;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border-radius: 6px;
        padding: 2px 8px;
        font-size: .68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .badge.utama { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge.komponen { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .badge.subindikator { background: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe; }
    .badge.parent { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; text-transform: none; }

    .detail-head-title h1 {
        margin: 0 0 3px;
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.2;
        color: var(--text-main);
    }
    .detail-head-title p {
        margin: 0;
        color: var(--text-soft);
        font-size: .74rem;
    }
    .detail-head-controls {
        display: flex;
        align-items: flex-end;
        gap: 10px;
    }
    .control-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .control-group label {
        color: var(--text-soft);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .select-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .select-wrapper select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        min-width: 250px;
        height: 38px;
        border: 1px solid #d8e2ee;
        border-radius: 8px;
        padding: 0 34px 0 12px;
        background: #ffffff;
        color: var(--text-main);
        font: inherit;
        font-size: .8rem;
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .select-wrapper select:focus {
        outline: 0;
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, .14);
    }
    .select-arrow {
        position: absolute;
        right: 12px;
        pointer-events: none;
        font-size: .68rem;
        color: #64748b;
    }
    .action-group {
        display: flex;
        align-items: center;
    }
    .head-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 38px;
        padding: 0 14px;
        border-radius: 8px;
        background: #f0f7ff;
        color: #0284c7;
        font-size: .8rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        border: 1px solid #bae6fd;
        box-shadow: 0 1px 2px rgba(15, 39, 71, 0.04);
        transition: all .15s ease;
    }
    .head-btn:hover {
        background: #0284c7;
        border-color: #0284c7;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(2, 132, 199, .18);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 12px;
    }
    .info-card, .chart-card, .table-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(15, 39, 71, 0.05);
    }
    .info-card {
        padding: 12px 14px;
        transition: border-color .15s, box-shadow .15s;
    }
    .info-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 39, 71, 0.06);
    }
    .info-label {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--text-soft);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .info-value {
        display: block;
        margin-top: 5px;
        color: var(--text-main);
        font-size: 1.12rem;
        font-weight: 800;
        letter-spacing: -.02em;
    }
    .info-value small {
        color: var(--text-soft);
        font-size: .72rem;
        font-weight: 600;
        margin-left: 2px;
    }

    .description {
        margin: 0 0 12px;
        padding: 10px 14px;
        border: 1px solid #edf2f7;
        border-left: 4px solid #0284c7;
        background: #f8fafc;
        color: #475569;
        font-size: .78rem;
        line-height: 1.5;
        border-radius: 8px;
    }
    .section-card { margin-bottom: 12px; }
    .card-heading {
        padding: 12px 16px;
        border-bottom: 1px solid #edf1f5;
        background: linear-gradient(110deg, #fffdf8 0%, #f9fcff 100%);
        border-top-left-radius: 9px;
        border-top-right-radius: 9px;
    }
    .card-heading h2 {
        margin: 0 0 2px;
        font-size: .9rem;
        font-weight: 800;
        color: var(--text-main);
    }
    .card-heading p { margin: 0; color: var(--text-soft); font-size: .7rem; }
    .chart-body { padding: 14px 16px; }
    .chart-container { position: relative; height: 270px; }
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 120px;
        color: var(--text-soft);
        font-size: .8rem;
        text-align: center;
    }
    .empty-state i { font-size: 1.6rem; color: #94a3b8; margin-bottom: 6px; }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: .8rem; }
    table.table-fit { min-width: 100%; }
    table.table-target { min-width: 440px; }
    .th-compact { display: none; }
    .cell-periode { white-space: nowrap; }
    .cell-sumber { white-space: normal; min-width: 120px; max-width: 220px; word-break: normal; line-height: 1.35; }
    th {
        background: #f8fafc;
        color: var(--text-soft);
        text-align: left;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
        white-space: nowrap;
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
    }
    th, td { padding: 9px 14px; border-bottom: 1px solid #edf1f5; white-space: nowrap; vertical-align: middle; }
    th.numeric, td.numeric { text-align: right; }
    tbody tr:hover { background: #f8fafc; }
    tbody tr:last-child td { border-bottom: 0; }
    .numeric { font-weight: 700; font-variant-numeric: tabular-nums; }
    .empty-table-cell { text-align: center; color: var(--text-soft); padding: 20px; font-style: italic; white-space: normal; }
    .target-badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 5px;
        font-size: .7rem;
        font-weight: 700;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #334155;
        white-space: nowrap;
    }

    @media (max-width: 900px) {
        .detail-head-inner { flex-direction: column; align-items: stretch; gap: 12px; }
        .detail-head-controls { width: 100%; flex-direction: column; align-items: stretch; gap: 8px; }
        .control-group { width: 100%; }
        .select-wrapper { width: 100%; }
        .select-wrapper select { width: 100%; min-width: 0; }
        .head-btn { width: 100%; justify-content: center; height: 40px; }
        .info-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .detail-page { padding-bottom: 24px; }
        .detail-head { padding: 12px 14px; border-radius: 12px; }
        .detail-head-title h1 { font-size: 1.15rem; line-height: 1.25; }
        .detail-head-title p { font-size: 0.74rem; }
        .select-wrapper select {
            height: 42px;
            font-size: 16px; /* Mencegah auto-zoom di iOS Safari */
            border-radius: 9px;
        }
        .head-btn {
            height: 42px;
            font-size: 0.84rem;
            border-radius: 9px;
        }
        .info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }
        .info-card {
            padding: 10px 12px;
            border-radius: 10px;
        }
        .info-label { font-size: 0.64rem; }
        .info-value { font-size: 1.05rem; margin-top: 3px; }
        .chart-container { height: 230px; }
        .chart-body { padding: 10px 12px; }
        .th-full { display: none; }
        .th-compact { display: inline; }
        .table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            border-radius: 10px;
            scrollbar-width: thin;
        }
        table {
            width: 100%;
            table-layout: auto;
            border-collapse: collapse;
        }
        table.table-fit {
            min-width: 100%;
        }
        table.table-target {
            min-width: 440px;
        }
        th, td {
            padding: 8px 10px;
            font-size: 0.76rem;
            white-space: nowrap;
            word-break: normal;
            vertical-align: middle;
        }
        th {
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
            font-weight: 700;
        }
        .cell-sumber {
            font-size: 0.74rem;
        }
    }
    @media (max-width: 380px) {
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

@php
    $isPublicUser = request()->routeIs('public.*') || (auth()->user() instanceof \App\Models\User);
    $backRoute = $isPublicUser ? route('public.dashboard') : route('indikator.index');
    $dashboardRoute = $isPublicUser ? route('public.dashboard') : route('dashboard');
    $detailRoute = $isPublicUser ? route('public.detail.indikator') : route('detail.indikator');
    $latestCapaian = $indikator->capaians->sortByDesc('tahun')->first();
@endphp

<div class="detail-page">
    <div class="detail-head">
        <div class="detail-head-inner">
            <div class="detail-head-title">
                <div class="title-meta">
                    <span class="badge {{ $indikator->jenis }}">{{ ucfirst($indikator->jenis) }}</span>
                    @if($indikator->parent)
                        <span class="badge parent"><i class="bi bi-diagram-2"></i> Induk: {{ $indikator->parent->nama_indikator }}</span>
                    @endif
                </div>
                <h1>{{ $indikator->nama_indikator }}</h1>
                <p>Ringkasan indikator dan data perkembangan capaian serta target Kota Tasikmalaya.</p>
            </div>

            <div class="detail-head-controls">
                <div class="control-group">
                    <label for="indikator-select">Pilih Indikator</label>
                    <div class="select-wrapper">
                        <select id="indikator-select" onchange="window.location.href = this.value">
                            @foreach($allIndicators as $item)
                                <option value="{{ $isPublicUser ? route('public.indikator.show', $item) : route('indikator.show', $item) }}" {{ $item->id == $indikator->id ? 'selected' : '' }}>{{ $item->nama_indikator }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down select-arrow"></i>
                    </div>
                </div>

                @unless($isPublicUser)
                    <div class="action-group">
                        <a class="head-btn" href="{{ route('indikator.edit', $indikator) }}">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    </div>
                @endunless
            </div>
        </div>
    </div>

    @if ($indikator->deskripsi)
        <p class="description">{{ $indikator->deskripsi }}</p>
    @endif

    <div class="info-grid">
        <div class="info-card">
            <span class="info-label"><i class="bi bi-tag"></i> Jenis Indikator</span>
            <strong class="info-value">
                <span class="badge {{ $indikator->jenis }}">{{ ucfirst($indikator->jenis) }}</span>
            </strong>
        </div>
        <div class="info-card">
            <span class="info-label"><i class="bi bi-rulers"></i> Satuan</span>
            <strong class="info-value">{{ $indikator->satuan ?? '-' }}</strong>
        </div>
        <div class="info-card">
            <span class="info-label"><i class="bi bi-graph-up-arrow"></i> Data capaian</span>
            <strong class="info-value">{{ $indikator->capaians->count() }} <small>data</small></strong>
        </div>
        <div class="info-card">
            <span class="info-label"><i class="bi bi-bullseye"></i> Data target</span>
            <strong class="info-value">{{ $indikator->targets->count() }} <small>data</small></strong>
        </div>
    </div>

    <section class="chart-card section-card">
        <div class="card-heading">
            <h2>Grafik Perkembangan Indikator</h2>
            <p>Riwayat capaian dan target tahunan 2005 - 2025.</p>
        </div>
        <div class="chart-body">
            @if ($indikator->capaians->isNotEmpty())
                <div class="chart-container">
                    <canvas id="indikatorDetailChart"></canvas>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-clipboard-x"></i>
                    <span>Belum tersedia data capaian untuk indikator ini.</span>
                </div>
            @endif
        </div>
    </section>

    <section class="table-card section-card">
        <div class="card-heading">
            <h2>Riwayat Data Indikator</h2>
            <p>Tahun yang tampil hanya berdasarkan data capaian yang tersedia.</p>
        </div>
        <div class="table-wrap">
            <table class="table-fit">
                <thead>
                    <tr>
                        <th style="width: 44px;">No</th>
                        <th class="numeric">Tahun</th>
                        <th class="numeric">Capaian</th>
                        <th class="numeric">Target</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="numeric">{{ $item['capaian']->tahun }}</td>
                            <td class="numeric" style="color: #0284c7;">{{ $item['capaian']->nilai !== null ? number_format((float) round($item['capaian']->nilai, 2), 2, ',', '.') : '-' }}</td>
                            <td class="numeric" style="color: #d97706;">{{ $item['target']?->nilai_target_range ?? ($item['target']?->nilai_target !== null ? number_format((float) round($item['target']->nilai_target, 2), 2, ',', '.') : '-') }}</td>
                            <td>{{ $indikator->satuan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-table-cell">Belum tersedia data capaian untuk indikator ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="table-card section-card">
        <div class="card-heading">
            <h2>Target Indikator</h2>
            <p>Termasuk target yang tidak memiliki tahun tunggal.</p>
        </div>
        <div class="table-wrap">
            <table class="table-target">
                <thead>
                    <tr>
                        <th><span class="th-full">Jenis Target</span><span class="th-compact">Jenis</span></th>
                        <th>Periode</th>
                        <th class="numeric">Tahun</th>
                        <th class="numeric"><span class="th-full">Nilai Target</span><span class="th-compact">Target</span></th>
                        <th>Sumber</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($indikator->targets as $target)
                        <tr>
                            <td><span class="target-badge">{{ $target->jenis_target }}</span></td>
                            <td class="cell-periode">{{ $target->periode ?? '-' }}</td>
                            <td class="numeric">{{ $target->tahun ?? '-' }}</td>
                            <td class="numeric" style="color: #d97706; font-weight: 700;">{{ $target->nilai_target_range ?? ($target->nilai_target !== null ? number_format((float) round($target->nilai_target, 2), 2, ',', '.') : '-') }}</td>
                            <td class="cell-sumber">{{ $target->sumber ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-table-cell">Belum tersedia data target untuk indikator ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@if ($indikator->capaians->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('indikatorDetailChart'), {
        type: 'line',
        data: {
            labels: {{ Js::from($chartLabels) }},
            datasets: [
                {
                    label: 'Capaian',
                    data: {{ Js::from($capaianData) }},
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, .08)',
                    pointBackgroundColor: '#0284c7',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                    tension: .3,
                    fill: true,
                    spanGaps: false
                },
                {
                    label: 'Target',
                    data: {{ Js::from($targetData) }},
                    borderColor: '#d97706',
                    backgroundColor: 'transparent',
                    pointBackgroundColor: '#d97706',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                    borderDash: [6, 4],
                    tension: .3,
                    fill: false,
                    spanGaps: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 14,
                        font: { size: 11, weight: '600' }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: (context) => ` ${context.dataset.label}: ${context.parsed.y !== null ? context.parsed.y : '-'} {{ $indikator->satuan ?? '' }}`
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { size: 10 } },
                    title: { display: true, text: 'Tahun', font: { size: 11, weight: '700' } }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { size: 10 } },
                    title: { display: true, text: 'Nilai ({{ $indikator->satuan ?? '' }})', font: { size: 11, weight: '700' } }
                }
            }
        }
    });
</script>
@endif
@endsection
