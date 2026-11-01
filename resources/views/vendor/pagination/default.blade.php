@if ($paginator->hasPages() || $paginator->total() > 0)
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        // Build smart, compact sliding window of page numbers (max 7 page items)
        $pages = [];
        if ($lastPage <= 7) {
            for ($i = 1; $i <= $lastPage; $i++) {
                $pages[] = $i;
            }
        } else {
            if ($currentPage <= 4) {
                for ($i = 1; $i <= 5; $i++) {
                    $pages[] = $i;
                }
                $pages[] = '...';
                $pages[] = $lastPage;
            } elseif ($currentPage >= $lastPage - 3) {
                $pages[] = 1;
                $pages[] = '...';
                for ($i = $lastPage - 4; $i <= $lastPage; $i++) {
                    $pages[] = $i;
                }
            } else {
                $pages[] = 1;
                $pages[] = '...';
                $pages[] = $currentPage - 1;
                $pages[] = $currentPage;
                $pages[] = $currentPage + 1;
                $pages[] = '...';
                $pages[] = $lastPage;
            }
        }
    @endphp

    <style>
        .custom-pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 16px;
            border-top: 1px solid #edf1f5;
            background: #ffffff;
            font-size: .8rem;
            color: var(--text-soft);
        }
        .pagination-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .pagination-info {
            font-weight: 500;
            color: #64748b;
            white-space: nowrap;
        }
        .pagination-info strong {
            color: var(--text-main);
            font-weight: 700;
        }
        .pagination-divider {
            width: 1px;
            height: 16px;
            background: #e2e8f0;
            display: inline-block;
            flex-shrink: 0;
        }
        .pagination-per-page {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .78rem;
            color: #64748b;
            white-space: nowrap;
        }
        .per-page-label {
            font-weight: 600;
            color: #475569;
        }
        .per-page-select {
            height: 30px;
            padding: 0 24px 0 8px;
            border: 1px solid #d8e2ee;
            border-radius: 6px;
            background: #fbfdff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") no-repeat right 6px center / 12px;
            appearance: none;
            -webkit-appearance: none;
            font: inherit;
            font-size: .78rem;
            font-weight: 600;
            color: var(--text-main);
            cursor: pointer;
            transition: border-color .15s;
        }
        .per-page-select:focus {
            outline: 0;
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, .14);
        }
        .per-page-text {
            color: #64748b;
        }
        .pagination-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
            white-space: nowrap;
        }
        .pagination-btn, .pagination-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 7px;
            font-size: .78rem;
            font-weight: 600;
            text-decoration: none;
            color: #475569;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            transition: all .15s ease;
            user-select: none;
        }
        .pagination-btn {
            gap: 5px;
            padding: 0 10px;
        }
        .pagination-btn:hover:not(.disabled), .pagination-link:hover:not(.active) {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
            transform: translateY(-1px);
        }
        .pagination-link.active {
            background: #10b981;
            border-color: #10b981;
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(16, 185, 129, .25);
            cursor: default;
        }
        .pagination-btn.disabled {
            color: #cbd5e1;
            background: #f8fafc;
            border-color: #f1f5f9;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .pagination-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            height: 32px;
            color: #94a3b8;
            font-size: .85rem;
        }
        @media (max-width: 900px) {
            .pagination-btn-label {
                display: none;
            }
            .pagination-btn {
                padding: 0 8px;
            }
        }
        @media (max-width: 640px) {
            .custom-pagination-container {
                flex-direction: column;
                align-items: center;
                gap: 10px;
                text-align: center;
            }
            .pagination-left {
                flex-direction: column;
                gap: 6px;
            }
            .pagination-divider {
                display: none;
            }
        }
    </style>

    <nav class="custom-pagination-container" role="navigation" aria-label="Navigasi Halaman">
        <div class="pagination-left">
            <div class="pagination-info">
                @if ($paginator->total() > 0)
                    Menampilkan <strong>{{ $paginator->firstItem() }}</strong> &ndash; <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
                @else
                    Menampilkan <strong>0</strong> data
                @endif
            </div>

            <span class="pagination-divider"></span>

            <div class="pagination-per-page">
                <label for="per_page_select" class="per-page-label">Tampilkan:</label>
                <select id="per_page_select" class="per-page-select" onchange="window.location.href=this.value">
                    @php
                        $options = array_unique([10, 15, 25, 50, 100, (int) $paginator->perPage()]);
                        sort($options);
                    @endphp
                    @foreach ($options as $size)
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}" @selected($paginator->perPage() == $size)>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
                <span class="per-page-text">baris</span>
            </div>
        </div>

        @if ($paginator->hasPages())
            <div class="pagination-nav">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="pagination-btn disabled" aria-disabled="true" title="Halaman Sebelumnya">
                        <i class="bi bi-chevron-left"></i> <span class="pagination-btn-label">Sebelumnya</span>
                    </span>
                @else
                    <a class="pagination-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Halaman Sebelumnya">
                        <i class="bi bi-chevron-left"></i> <span class="pagination-btn-label">Sebelumnya</span>
                    </a>
                @endif

                {{-- Compact Page Numbers --}}
                @foreach ($pages as $item)
                    @if ($item === '...')
                        <span class="pagination-ellipsis" aria-disabled="true">&hellip;</span>
                    @elseif ($item == $currentPage)
                        <span class="pagination-link active" aria-current="page">{{ $item }}</span>
                    @else
                        <a class="pagination-link" href="{{ $paginator->url($item) }}">{{ $item }}</a>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a class="pagination-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Halaman Selanjutnya">
                        <span class="pagination-btn-label">Selanjutnya</span> <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span class="pagination-btn disabled" aria-disabled="true" title="Halaman Selanjutnya">
                        <span class="pagination-btn-label">Selanjutnya</span> <i class="bi bi-chevron-right"></i>
                    </span>
                @endif
            </div>
        @endif
    </nav>
@endif
