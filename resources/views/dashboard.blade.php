@extends('layouts.admin')

@section('title', 'Dashboard Makro Kota Tasikmalaya')

@section('content')
@php
    $isPublicContext = request()->routeIs('public.*') || (auth()->user() instanceof \App\Models\User);
    $dashboardRoute = $isPublicContext ? route('public.dashboard') : route('dashboard');
    $analysisRoute = $isPublicContext ? route('public.analisis') : route('analisis.index');
@endphp
<style>
    .dashboard-page { width: 100%; height: 100%; min-width: 0; overflow-y: auto; padding: 0; display: flex; flex-direction: column; }
    .dashboard-head, .indicator-panel, .rail-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 10px; box-shadow: 0 4px 16px rgba(15, 39, 71, 0.05); }
    .dashboard-head { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 10px 14px; margin-bottom: 8px; background: linear-gradient(110deg, #fffef7 0%, #f0f7f3 100%); }
    .eyebrow { display: inline-flex; align-items: center; gap: 5px; margin-bottom: 2px; color: #10b981; font-size: .62rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .dashboard-head h1 { margin: 0 0 2px; font-size: 1.15rem; letter-spacing: -.02em; }
    .dashboard-head p { margin: 0; color: var(--text-soft); font-size: .72rem; }
    .year-filter { position: relative; display: flex; align-items: center; gap: 6px; padding: 2px 4px 2px 8px; border: 1px solid #d8e2ee; border-radius: 8px; background: rgba(255,255,255,.95); white-space: nowrap; box-shadow: 0 1px 2px rgba(15,23,42,0.04); }
    .year-filter label { color: var(--text-soft); font-size: .68rem; font-weight: 700; margin-right: 2px; }
    .year-select-container { position: relative; }
    .year-select-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        min-width: 84px;
        height: 28px;
        padding: 0 8px;
        border: 1px solid #a7f3d0;
        border-radius: 6px;
        background: #ecfdf5;
        color: #065f46;
        font: inherit;
        font-size: .76rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
    }
    .year-select-trigger:focus, .year-select-container.is-open .year-select-trigger {
        outline: 0;
        border-color: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, .2);
    }
    .year-select-trigger .arrow-icon {
        font-size: .62rem;
        color: #10b981;
        transition: transform .2s ease;
    }
    .year-select-container.is-open .arrow-icon {
        transform: rotate(180deg);
    }
    .year-select-menu {
        position: absolute;
        top: calc(100% + 4px);
        right: 0;
        min-width: 130px;
        max-height: 240px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(15, 39, 71, 0.12), 0 2px 6px rgba(0, 0, 0, 0.04);
        z-index: 1000;
        display: none;
        padding: 4px;
    }
    .year-select-container.is-open .year-select-menu {
        display: block;
        animation: dropFade .15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .year-select-option {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 8px;
        border: 0;
        border-radius: 5px;
        background: transparent;
        color: var(--text-main);
        font: inherit;
        font-size: .74rem;
        font-weight: 500;
        cursor: pointer;
        text-align: left;
        transition: background .12s ease, color .12s ease;
    }
    .year-select-option:hover {
        background: #ecfdf5;
        color: #065f46;
    }
    .year-select-option.is-selected {
        background: #d1fae5;
        color: #065f46;
        font-weight: 700;
    }
    .year-select-option .check-icon {
        color: #10b981;
        font-size: .8rem;
    }
    @keyframes dropFade { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
    .dashboard-layout { display: grid; grid-template-columns: minmax(0, 1.8fr) minmax(210px, .72fr); gap: 10px; align-items: stretch; flex: 1; min-height: 0; }
    .indicator-panel { padding: 10px 12px; height: 100%; display: flex; flex-direction: column; }
    .section-title { display: flex; align-items: center; justify-content: space-between; margin: 0 2px 6px; }
    .section-title h2 { margin: 0; font-size: .84rem; letter-spacing: -.01em; }
    .section-title span { color: var(--text-soft); font-size: .68rem; }
    .indicator-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        grid-template-rows: repeat(3, minmax(0, 1fr));
        gap: 8px;
        flex: 1;
        min-height: 0;
    }
    .indicator-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: auto;
        min-height: 0;
        padding: 6px 8px;
        overflow: hidden;
        background: #fbfcfd;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: transform .18s ease, box-shadow .18s ease;
    }
    .indicator-card:nth-child(3n+1) {
        background: #fffef8;
    }
    .indicator-card:nth-child(3n+2) {
        background: #f8fbfd;
    }
    .indicator-card:nth-child(3n+3) {
        background: #fcfcfc;
    }
    .indicator-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(15, 39, 71, .08);
    }
    .indicator-top { display: contents; }
    .indicator-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        order: 1;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e8f8f0 0%, #d1fae5 100%);
        color: #10b981;
        font-size: .62rem;
    }
    .indicator-card:nth-child(3n+2) .indicator-icon {
        background: linear-gradient(135deg, #dcfce7 0%, #d1fae5 100%);
        color: #15803d;
    }
    .indicator-card:nth-child(3n+3) .indicator-icon {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #b45309;
    }
    .indicator-name {
        order: 3;
        margin: 2px 0 0;
        max-width: 100%;
        font-size: .62rem;
        line-height: 1.2;
        color: var(--text-main);
        font-weight: 700;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: .02em;
    }
    .indicator-value {
        order: 2;
        margin: 3px 0 0;
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -.03em;
        text-align: center;
    }
    .indicator-value small {
        display: inline;
        margin-left: 3px;
        color: var(--text-soft);
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
    }
    .indicator-meta {
        display: none;
    }
    .status {
        display: none;
    }
    .detail-link {
        order: 4;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 5px;
        padding: 4px 10px;
        border-radius: 6px;
        background: #ecfdf5;
        color: #10b981;
        border: 1px solid #a7f3d0;
        font-size: .62rem;
        font-weight: 800;
        line-height: 1;
        text-decoration: none;
        transition: background .15s ease, color .15s ease, transform .15s ease, border-color .15s ease;
    }
    .detail-link:hover {
        background: #10b981;
        color: #ffffff;
        border-color: #10b981;
        transform: translateY(-1px);
    }
    .dashboard-rail { display: grid; grid-template-rows: 1fr 1fr; gap: 8px; height: 100%; }
    .rail-card { padding: 10px; min-height: 0; display: flex; flex-direction: column; }
    .rail-card.database { background: linear-gradient(160deg, #fffdf5 0%, #fff8e3 55%, #ffffff 100%); }
    .rail-card.summary { background: linear-gradient(160deg, #f0f9f4 0%, #ffffff 100%); }
    .rail-card.database .rail-button { margin-top: auto; }
    .analysis-invite { display: flex; align-items: flex-start; gap: 7px; margin: 3px 0 5px; padding: 6px 8px; border-radius: 7px; background: rgba(255,255,255,.78); }
    .analysis-invite-icon { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; flex: 0 0 24px; border-radius: 6px; background: #ffcb56; color: #6b4b00; font-size: .7rem; }
    .analysis-invite strong, .analysis-invite small { display: block; }
    .analysis-invite strong { font-size: .68rem; }
    .analysis-invite small { margin-top: 1px; color: var(--text-soft); font-size: .58rem; line-height: 1.25; }
    .rail-card h3 { margin: 0; font-size: .82rem; }
    .rail-card > p { margin: 2px 0 6px; color: var(--text-soft); font-size: .65rem; }
    .rail-heading { display: flex; align-items: center; justify-content: space-between; gap: 6px; }
    .rail-heading i { color: var(--text-soft); font-size: .85rem; }
    .rail-button { display: flex; align-items: center; justify-content: center; gap: 5px; width: 100%; margin-top: auto; padding: 6px 10px; border-radius: 6px; background: #ffcb56; color: #6b4b00; font-size: .66rem; font-weight: 800; text-decoration: none; }
    .rail-button:hover { background: #eab13a; color: #4a3500; }
    .summary-list { display: grid; gap: 4px; margin-top: 5px; }
    .summary-row { display: flex; align-items: center; gap: 7px; padding: 5px 8px; border: 1px solid #edf1f5; border-radius: 6px; background: #f8fafc; font-size: .66rem; }
    .summary-row-icon { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; flex: 0 0 22px; border-radius: 5px; background: #ecfdf5; color: #10b981; font-size: .68rem; }
    .summary-row:nth-child(2) .summary-row-icon { background: #d1fae5; color: #059669; }
    .summary-row:nth-child(3) .summary-row-icon { background: #fff4d2; color: #a87908; }
    .summary-row:nth-child(4) .summary-row-icon { background: #ecfdf5; color: #047857; }
    .summary-row span { color: var(--text-soft); }
    .summary-row strong { margin-left: auto; color: var(--text-main); font-size: .8rem; }
    @media (max-width: 900px) {
        .dashboard-page {
            height: auto;
            min-height: 100%;
            overflow-y: visible;
        }
        .dashboard-layout {
            grid-template-columns: 1fr;
            height: auto;
            min-height: 0;
            gap: 12px;
        }
        .indicator-panel {
            height: auto;
        }
        .dashboard-rail {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-template-rows: auto;
            height: auto;
        }
        .indicator-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-template-rows: auto;
            height: auto;
        }
    }

    @media (max-width: 600px) {
        .dashboard-page {
            padding-bottom: 24px;
        }
        .dashboard-head {
            align-items: stretch;
            flex-direction: column;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
        }
        .dashboard-head h1 {
            font-size: 1.15rem;
            line-height: 1.25;
        }
        .dashboard-head p {
            font-size: 0.76rem;
        }
        .year-filter {
            justify-content: space-between;
            width: 100%;
            padding: 6px 10px;
            border-radius: 8px;
        }
        .year-filter label {
            font-size: 0.78rem;
        }
        .year-filter select {
            font-size: 16px; /* Mencegah auto-zoom di iOS Safari */
            height: 38px;
            min-width: 100px;
        }
        .indicator-panel {
            padding: 12px;
            border-radius: 12px;
        }
        .indicator-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .indicator-card {
            padding: 12px 14px;
            border-radius: 10px;
        }
        .indicator-icon {
            width: 26px;
            height: 26px;
            font-size: 0.7rem;
        }
        .indicator-name {
            font-size: 0.72rem;
            margin: 4px 0 0;
        }
        .indicator-value {
            font-size: 1.45rem;
            margin: 4px 0 0;
        }
        .indicator-value small {
            font-size: 0.65rem;
        }
        .detail-link {
            margin-top: 8px;
            padding: 8px 14px;
            font-size: 0.74rem;
            width: 100%;
            min-height: 36px;
            border-radius: 7px;
        }
        .dashboard-rail {
            grid-template-columns: 1fr;
            gap: 10px;
            height: auto;
        }
        .rail-card {
            padding: 14px;
            border-radius: 12px;
        }
        .rail-card h3 {
            font-size: 0.9rem;
        }
        .analysis-invite {
            padding: 8px 10px;
            gap: 8px;
        }
        .rail-button {
            height: 42px;
            font-size: 0.8rem;
            border-radius: 8px;
            margin-top: 8px;
        }
        .summary-row {
            padding: 8px 10px;
            font-size: 0.75rem;
        }
        .summary-row strong {
            font-size: 0.88rem;
        }
    }
</style>
<div class="dashboard-page">
    <div class="dashboard-head">
        <div>
            <div class="eyebrow"><i class="bi bi-grid-1x2-fill"></i> Ringkasan data</div>
            <h1>Dashboard Makro Kota Tasikmalaya</h1>
            <p>Ringkasan indikator pembangunan Kota Tasikmalaya.</p>
        </div>
        <form class="year-filter" method="GET" action="{{ $dashboardRoute }}" id="dashboard-year-form">
            <label for="tahun-trigger">Tahun</label>
            <input type="hidden" name="tahun" id="tahun-input" value="{{ $tahun }}">
            <div class="year-select-container" id="dashboard-year-dropdown">
                <button type="button" class="year-select-trigger" id="tahun-trigger" aria-haspopup="listbox" aria-expanded="false">
                    <span>{{ $tahun ?: 'Pilih tahun' }}</span>
                    <i class="bi bi-chevron-down arrow-icon"></i>
                </button>
                <div class="year-select-menu" role="listbox">
                    <button type="button" class="year-select-option {{ empty($tahun) ? 'is-selected' : '' }}" data-value="">
                        <span>Pilih tahun</span>
                        @if(empty($tahun)) <i class="bi bi-check2 check-icon"></i> @endif
                    </button>
                    @foreach($years as $year)
                        <button type="button" class="year-select-option {{ (string)$tahun === (string)$year ? 'is-selected' : '' }}" data-value="{{ $year }}">
                            <span>{{ $year }}</span>
                            @if((string)$tahun === (string)$year) <i class="bi bi-check2 check-icon"></i> @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </form>
    </div>

    <div class="dashboard-layout">
        <section class="indicator-panel">
            <div class="section-title">
                <h2>Indikator Pembangunan</h2>
                <span>Data tahun {{ $tahun }}</span>
            </div>
            <div class="indicator-grid">
                @forelse ($dataDashboard as $item)
                    @php
                        $isPublicContext = request()->routeIs('public.*') || (auth()->user() instanceof \App\Models\User);
                        $satuan = trim((string) $item['indikator']->satuan);
                        $satuanRingkas = str_ireplace(
                            ['triliun rupiah', 'miliar rupiah', 'juta rupiah', 'ribu rupiah', 'triliun rp', 'miliar rp', 'juta rp', 'ribu rp', 'persen', 'per tahun'],
                            ['T Rp', 'M Rp', 'Jt Rp', 'Rb Rp', 'T Rp', 'M Rp', 'Jt Rp', 'Rb Rp', '%', '/th'],
                            $satuan
                        );
                        $detailRoute = $isPublicContext
                            ? route('public.indikator.show', $item['indikator'])
                            : route('indikator.show', $item['indikator']);
                    @endphp
                    <article class="indicator-card">
                        <div class="indicator-top">
                            <span class="indicator-icon"><i class="bi bi-bar-chart-line-fill"></i></span>
                            <h3 class="indicator-name">{{ $item['indikator']->nama_indikator }}</h3>
                        </div>
                        <div class="indicator-value">
                            {{ $item['capaian'] !== null ? number_format((float) round($item['capaian'], 2), 2, ',', '.') : '-' }}
                            <small>{{ $item['capaian'] !== null ? $satuanRingkas : '' }}</small>
                        </div>
                        <div class="indicator-meta">
                            <span>Target: {{ $item['target'] !== null ? number_format((float) round($item['target'], 2), 2, ',', '.') : '-' }}</span>
                            <span>Tahun: {{ $tahun }}</span>
                        </div>
                        <span class="status {{ $item['status'] === 'Tercapai' ? 'good' : ($item['status'] === 'Belum Tercapai' ? 'pending' : '') }}">{{ $item['status'] }}</span>
                        <a class="detail-link" href="{{ $detailRoute }}">Lihat Detail</a>
                    </article>
                @empty
                    <p>Belum ada indikator utama.</p>
                @endforelse
            </div>
        </section>

        <aside class="dashboard-rail">
            <section class="rail-card database">
                <div class="rail-heading">
                    <h3>Analisis Indikator</h3>
                    <i class="bi bi-bar-chart-line-fill"></i>
                </div>
                <p>Jelajahi perkembangan indikator pembangunan secara lebih mendalam.</p>
                <div class="analysis-invite">
                    <span class="analysis-invite-icon"><i class="bi bi-graph-up-arrow"></i></span>
                    <div><strong>Temukan insight data</strong><small>Bandingkan capaian, target, dan perubahan indikator berdasarkan rentang tahun.</small></div>
                </div>
                <div class="analysis-invite">
                    <span class="analysis-invite-icon"><i class="bi bi-bullseye"></i></span>
                    <div><strong>Bandingkan target</strong><small>Lihat capaian dan target indikator dalam periode yang dipilih.</small></div>
                </div>
                <div class="analysis-invite">
                    <span class="analysis-invite-icon"><i class="bi bi-arrow-up-right"></i></span>
                    <div><strong>Pantau perubahan</strong><small>Amati tren kenaikan atau penurunan data dari tahun ke tahun.</small></div>
                </div>
                @php
                    $analysisRoute = $isPublicContext ? route('public.analisis') : route('analisis.index');
                @endphp
                <a class="rail-button" href="{{ $analysisRoute }}">Mulai Analisis Data <i class="bi bi-arrow-up-right"></i></a>
            </section>

            <section class="rail-card summary">
                <div class="rail-heading">
                    <h3>Ringkasan Data</h3>
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <p>Jumlah data yang tercatat dalam database</p>
                <div class="summary-list">
                    <div class="summary-row"><span class="summary-row-icon"><i class="bi bi-bar-chart-line-fill"></i></span><span>Data indikator</span><strong>{{ $totalIndikator }}</strong></div>
                    <div class="summary-row"><span class="summary-row-icon"><i class="bi bi-clipboard-data"></i></span><span>Data capaian</span><strong>{{ $totalCapaian }}</strong></div>
                    <div class="summary-row"><span class="summary-row-icon"><i class="bi bi-bullseye"></i></span><span>Data target</span><strong>{{ $totalTarget }}</strong></div>
                    <div class="summary-row"><span class="summary-row-icon"><i class="bi bi-calendar3"></i></span><span>Tahun terbaru</span><strong>{{ $tahunTerbaru }}</strong></div>
                </div>
            </section>
        </aside>
    </div>
</div>
<script>
    (() => {
        const container = document.getElementById('dashboard-year-dropdown');
        if (!container) return;
        const trigger = container.querySelector('.year-select-trigger');
        const hiddenInput = document.getElementById('tahun-input');
        const form = document.getElementById('dashboard-year-form');

        const toggleDropdown = (open) => {
            const isOpen = open ?? !container.classList.contains('is-open');
            container.classList.toggle('is-open', isOpen);
            trigger.setAttribute('aria-expanded', String(isOpen));
        };

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleDropdown();
        });

        container.querySelectorAll('.year-select-option').forEach((opt) => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = opt.getAttribute('data-value');
                hiddenInput.value = val;
                toggleDropdown(false);
                form.submit();
            });
        });

        document.addEventListener('click', (e) => {
            if (!container.contains(e.target)) toggleDropdown(false);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && container.classList.contains('is-open')) {
                toggleDropdown(false);
                trigger.focus();
            }
        });
    })();
</script>
@endsection
