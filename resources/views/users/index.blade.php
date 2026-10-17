@extends('layouts.admin')

@section('title', 'Data User Terdaftar')

@section('content')
<style>
    .users-page { width: 100%; min-width: 0; }
    
    .page-heading, .toolbar, .table-wrap, .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
    }
    
    .page-heading {
        padding: 18px 22px;
        margin-bottom: 12px;
        background: linear-gradient(110deg, #ffffff 0%, #f4faff 100%);
    }

    .heading-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .72rem;
        font-weight: 700;
        color: #0369a1;
        background: #e0f2fe;
        border: 1px solid #bae6fd;
        padding: 3px 9px;
        border-radius: 6px;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    
    .page-heading h1 {
        margin: 0 0 4px;
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -.02em;
        color: var(--text-main);
    }
    
    .page-heading p {
        margin: 0;
        color: var(--text-soft);
        font-size: .84rem;
        line-height: 1.4;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .stat-card {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 39, 71, 0.08);
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .stat-icon.blue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .stat-icon.green {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-info { min-width: 0; }
    .stat-label {
        font-size: .72rem;
        font-weight: 800;
        color: var(--text-soft);
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 2px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -.02em;
        line-height: 1.1;
    }

    .toolbar {
        display: flex;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        margin-bottom: 12px;
    }

    .filters {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        flex: 1;
    }

    .search-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 260px;
        width: 360px;
        max-width: 480px;
    }

    .search-input-wrapper .search-icon {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        font-size: .88rem;
        pointer-events: none;
        transition: transform .2s ease, color .2s ease;
    }

    .search-input-wrapper input {
        width: 100%;
        height: 38px;
        border: 1px solid #d8e2ee;
        border-radius: 8px;
        padding: 0 34px 0 34px;
        color: var(--text-main);
        background: #fbfdff;
        font: inherit;
        font-size: .84rem;
        transition: border-color .15s, box-shadow .15s, background-color .15s;
    }

    .search-input-wrapper input:focus {
        outline: 0;
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, .14);
        background: #fff;
    }

    .search-input-wrapper input::-webkit-search-decoration,
    .search-input-wrapper input::-webkit-search-cancel-button,
    .search-input-wrapper input::-webkit-search-results-button,
    .search-input-wrapper input::-webkit-search-results-decoration {
        -webkit-appearance: none;
        appearance: none;
        display: none;
    }

    .clear-search-btn {
        position: absolute;
        right: 10px;
        background: transparent;
        border: 0;
        color: #94a3b8;
        font-size: .82rem;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        padding: 0;
        line-height: 1;
        transition: color .15s ease, background-color .15s ease;
    }

    .clear-search-btn:hover {
        color: #0f172a;
        background-color: #f1f5f9;
    }

    .clear-search-btn.is-visible {
        display: flex;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .is-spinning {
        animation: spin .75s linear infinite;
        color: #0284c7 !important;
    }

    .search-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .reset-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        padding: 0 14px;
        border-radius: 8px;
        font-size: .84rem;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        gap: 6px;
        transition: all .15s ease;
        box-sizing: border-box;
        background: #edf2f7;
        color: #475569;
        border: 1px solid #dfe7ee;
    }

    .reset-btn:hover {
        background: #e2e8f0;
        color: #334155;
    }

    .toolbar-info {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .badge-total {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .8rem;
        font-weight: 600;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        white-space: nowrap;
    }

    .badge-total strong {
        color: #0284c7;
        font-weight: 800;
    }

    .table-wrap { overflow: auto; }
    table { width: 100%; border-collapse: collapse; font-size: .84rem; }
    
    th {
        background: #f8fafc;
        color: var(--text-soft);
        text-align: left;
        font-size: .72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
        padding: 12px 16px;
        border-bottom: 1px solid #edf1f5;
        white-space: nowrap;
    }

    td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf1f5;
        vertical-align: middle;
    }

    tbody tr {
        transition: background-color .12s ease;
    }

    tbody tr:hover { background: #fbfdff; }
    tbody tr:last-child td { border-bottom: 0; }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .88rem;
        font-weight: 800;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    }

    .user-name {
        font-weight: 700;
        color: var(--text-main);
        font-size: .88rem;
        white-space: nowrap;
    }

    .user-email-cell {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #334155;
        font-size: .84rem;
        font-weight: 500;
    }

    .user-email-cell i {
        color: #64748b;
        font-size: .86rem;
    }

    .badge-role {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 3px 10px;
        font-size: .72rem;
        font-weight: 700;
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .badge-date {
        color: var(--text-soft);
        font-size: .8rem;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .badge-date .date-main {
        font-weight: 600;
        color: var(--text-main);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-date .date-main i {
        color: #94a3b8;
        font-size: .78rem;
    }

    .badge-date .time-rel {
        font-size: .72rem;
        color: #94a3b8;
    }

    .delete-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #fca5a5;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: .78rem;
        font-weight: 700;
        background: #fee2e2;
        color: #dc2626;
        cursor: pointer;
        transition: all .15s ease;
        box-shadow: 0 1px 2px rgba(220, 38, 38, 0.08);
    }

    .delete-btn i {
        color: #dc2626;
        font-size: .84rem;
        transition: color .15s ease;
    }

    .delete-btn:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(220, 38, 38, 0.25);
    }

    .delete-btn:hover i {
        color: #ffffff;
    }

    .empty-state {
        text-align: center;
        padding: 48px 16px;
        color: var(--text-soft);
    }

    .empty-icon {
        font-size: 2.4rem;
        color: #cbd5e1;
        margin-bottom: 8px;
    }

    .empty-state h4 {
        margin: 0 0 4px;
        font-size: .95rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .empty-state p {
        margin: 0;
        font-size: .82rem;
        color: var(--text-soft);
    }

    @media (max-width: 768px) {
        .page-heading {
            padding: 14px 16px;
            margin-bottom: 10px;
        }

        .page-heading h1 {
            font-size: 1.2rem;
        }

        .page-heading p {
            font-size: .8rem;
        }

        .stats-row {
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .stat-card {
            padding: 14px 16px;
            gap: 12px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            font-size: 1.2rem;
        }

        .stat-value {
            font-size: 1.35rem;
        }

        .toolbar {
            flex-direction: column;
            align-items: stretch;
            padding: 10px 12px;
            gap: 10px;
            margin-bottom: 10px;
        }

        .filters {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
            width: 100%;
        }

        .search-input-wrapper {
            width: 100%;
            min-width: 0;
            max-width: none;
        }

        .search-input-wrapper input {
            height: 42px;
            font-size: 16px; /* Mencegah auto-zoom di iOS Safari */
            padding-left: 36px;
            padding-right: 38px;
            border-radius: 9px;
        }

        .clear-search-btn {
            width: 32px;
            height: 32px;
            right: 5px;
            font-size: .9rem;
        }

        .search-actions {
            width: 100%;
        }

        .reset-btn {
            width: 100%;
            height: 38px;
            justify-content: center;
            font-size: .82rem;
            border-radius: 8px;
        }

        .toolbar-info {
            width: 100%;
            justify-content: flex-start;
        }

        .badge-total {
            width: 100%;
            justify-content: center;
            padding: 7px 10px;
            font-size: .78rem;
            border-radius: 8px;
        }

        .table-wrap {
            border-radius: 12px;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 680px;
        }

        th, td {
            padding: 10px 12px;
        }

        .empty-state {
            padding: 32px 14px;
        }
    }

    @media (max-width: 480px) {
        .page-heading {
            padding: 12px 14px;
            border-radius: 12px;
        }

        .heading-badge {
            font-size: .68rem;
            padding: 2px 7px;
            margin-bottom: 4px;
        }

        .page-heading h1 {
            font-size: 1.1rem;
        }

        .page-heading p {
            font-size: .76rem;
        }

        .stat-card {
            padding: 12px 14px;
            border-radius: 12px;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            font-size: 1.1rem;
            border-radius: 10px;
        }

        .stat-value {
            font-size: 1.25rem;
        }

        .delete-btn {
            padding: 6px 10px;
            font-size: .74rem;
        }
    }
</style>

<div class="users-page">
    <div class="page-heading">
        <div class="heading-badge"><i class="bi bi-shield-check"></i> Admin Panel</div>
        <h1>Data User Terdaftar</h1>
        <p>Kelola dan pantau seluruh akun pengguna publik yang terdaftar pada sistem Dashboard Makro Kota Tasikmalaya.</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Total User Terdaftar</div>
                <div class="stat-value">{{ number_format($totalUsers) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">Pendaftar Bulan Ini</div>
                <div class="stat-value">{{ number_format($newUsersThisMonth) }}</div>
            </div>
        </div>
    </div>

    <div class="toolbar">
        <form class="filters" method="GET" action="{{ route('users.index') }}" id="user-search-form" onsubmit="return false;">
            <div class="search-input-wrapper">
                <i class="bi bi-search search-icon" id="search-status-icon"></i>
                <input type="search" id="user-search-input" name="search" value="{{ request('search') }}" placeholder="Ketik nama atau email pengguna..." autocomplete="off">
                <button type="button" id="clear-search-btn" class="clear-search-btn {{ request('search') ? 'is-visible' : '' }}" title="Hapus pencarian" aria-label="Hapus pencarian">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="search-actions">
                <a href="{{ route('users.index') }}" class="reset-btn" id="reset-filter-btn" style="{{ request()->filled('search') ? '' : 'display: none;' }}">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>
        <div class="toolbar-info">
            <span class="badge-total">
                <i class="bi bi-people-fill"></i> Total: <strong id="total-users-count">{{ number_format($users->total()) }}</strong> Pengguna
            </span>
        </div>
    </div>

    <div class="table-wrap" id="users-table-container">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th style="width: 120px;">Role</th>
                    <th style="width: 190px;">Tanggal Terdaftar</th>
                    <th style="width: 110px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $index => $user)
                    <tr>
                        <td style="text-align: center; font-weight: 600; color: var(--text-soft);">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="user-profile">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="user-name">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="user-email-cell">
                                <i class="bi bi-envelope"></i>
                                <span>{{ $user->email }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge-role">
                                <i class="bi bi-person-check-fill"></i>
                                {{ ucfirst($user->role ?: 'publik') }}
                            </span>
                        </td>
                        <td>
                            <div class="badge-date">
                                <span class="date-main">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $user->created_at?->format('d M Y, H:i') ?? '-' }}
                                </span>
                                <span class="time-rel">{{ $user->created_at?->diffForHumans() }}</span>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <form method="POST" action="{{ route('users.destroy', $user) }}" class="delete-form" data-confirm-title="Hapus akun pengguna?" data-confirm-message="Akun pengguna '{{ $user->name }}' ({{ $user->email }}) akan dihapus secara permanen dan tidak dapat login kembali. Apakah Anda yakin?">
                                @csrf
                                @method('DELETE')
                                <button class="delete-btn" type="submit" title="Hapus pengguna">
                                    <i class="bi bi-trash3-fill"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-person-x"></i></div>
                                <h4>Data Pengguna Tidak Ditemukan</h4>
                                <p>{{ request('search') ? 'Tidak ada akun pengguna yang cocok dengan kata kunci "' . request('search') . '"' : 'Belum ada akun pengguna publik yang terdaftar di sistem.' }}</p>
                                @if (request('search'))
                                    <div style="margin-top: 12px;">
                                        <button type="button" class="reset-btn" id="empty-reset-btn" style="display: inline-flex; margin: 0 auto;">
                                            <i class="bi bi-arrow-counterclockwise"></i> Tampilkan Semua Pengguna
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $users->links() }}
    </div>
</div>

@include('components.delete-confirm-modal')

<script>
    (() => {
        const searchInput = document.getElementById('user-search-input');
        const statusIcon = document.getElementById('search-status-icon');
        const clearBtn = document.getElementById('clear-search-btn');
        const resetFilterBtn = document.getElementById('reset-filter-btn');
        const tableContainer = document.getElementById('users-table-container');
        const totalBadge = document.getElementById('total-users-count');

        let debounceTimer = null;
        let abortController = null;

        const performLiveSearch = (query) => {
            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();

            if (statusIcon) {
                statusIcon.className = 'bi bi-arrow-repeat search-icon is-spinning';
            }

            const url = new URL('{{ route("users.index") }}', window.location.origin);
            if (query.trim()) {
                url.searchParams.set('search', query.trim());
            }

            fetch(url.toString(), {
                signal: abortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then((res) => res.text())
            .then((html) => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTable = doc.getElementById('users-table-container');
                if (newTable && tableContainer) {
                    tableContainer.innerHTML = newTable.innerHTML;
                }

                const newCount = doc.getElementById('total-users-count');
                if (newCount && totalBadge) {
                    totalBadge.textContent = newCount.textContent;
                }

                if (resetFilterBtn) {
                    resetFilterBtn.style.display = query.trim() ? 'inline-flex' : 'none';
                }

                window.history.replaceState({}, '', url.toString());
            })
            .catch((err) => {
                if (err.name !== 'AbortError') {
                    console.error('Search error:', err);
                }
            })
            .finally(() => {
                if (statusIcon) {
                    statusIcon.className = 'bi bi-search search-icon';
                }
            });
        };

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const val = e.target.value;
                if (clearBtn) {
                    clearBtn.classList.toggle('is-visible', val.length > 0);
                }
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    performLiveSearch(val);
                }, 280);
            });

            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    performLiveSearch(searchInput.value);
                }
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                clearBtn.classList.remove('is-visible');
                searchInput.focus();
                performLiveSearch('');
            });
        }

        if (resetFilterBtn) {
            resetFilterBtn.addEventListener('click', (e) => {
                e.preventDefault();
                searchInput.value = '';
                if (clearBtn) clearBtn.classList.remove('is-visible');
                performLiveSearch('');
            });
        }

        document.addEventListener('click', (e) => {
            if (e.target.closest('#empty-reset-btn')) {
                e.preventDefault();
                searchInput.value = '';
                if (clearBtn) clearBtn.classList.remove('is-visible');
                performLiveSearch('');
            }
        });

        if (tableContainer) {
            tableContainer.addEventListener('click', (e) => {
                const link = e.target.closest('.pagination a, a.page-link');
                if (!link) return;
                e.preventDefault();
                const targetUrl = link.getAttribute('href');
                if (!targetUrl) return;

                if (statusIcon) {
                    statusIcon.className = 'bi bi-arrow-repeat search-icon is-spinning';
                }

                fetch(targetUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then((res) => res.text())
                .then((html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.getElementById('users-table-container');
                    if (newTable) {
                        tableContainer.innerHTML = newTable.innerHTML;
                    }
                    window.history.pushState({}, '', targetUrl);
                })
                .finally(() => {
                    if (statusIcon) {
                        statusIcon.className = 'bi bi-search search-icon';
                    }
                });
            });
        }
    })();
</script>
@endsection
