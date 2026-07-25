@props(['id', 'url', 'columns'])

<div class="card shadow-sm border-light overflow-hidden">
    <!-- Table Header with Search on Right -->
    <div class="card-header bg-light border-bottom d-flex flex-wrap align-items-center gap-3 py-3 px-4">
        <div class="d-flex align-items-center gap-2">
            <div class=" bg-opacity-10 text-primary p-2 rounded-3">
                <i class="fas fa-users"></i>
            </div>
            <span class="text-muted small">Manage your database records</span>
        </div>

        <div class="flex-grow-1"></div>

        <!-- Search Box -->
        <div class="position-relative" style="width: 250px; max-width: 100%;">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                <i class="fas fa-search fa-xs"></i>
            </span>
            <input type="text" id="search-input-{{ $id }}" placeholder="Search records..."
                class="form-control form-control-sm ps-5" style="border-radius: 5px;">
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive p-3">
        <table id="{{ $id }}"
            class="table table-hover table-striped align-middle datatable-bootstrap display responsive nowrap w-100"
            style="width:100%">
            <thead class="table-light text-uppercase tracking-wider" style="font-size: 0.8rem;">
                <tr>
                    @foreach($columns as $col)
                        <th scope="col" class="px-3 py-2.5">
                            <div class="d-flex align-items-center gap-1">
                                <span>{{ $col['title'] }}</span>
                                @if($col['title'] !== 'Action' && $col['title'] !== 'STATUS')
                                    <i class="fas fa-sort text-muted small opacity-50"></i>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
        </table>
    </div>

    <!-- Table Footer with Info & Pagination -->
    <div
        class="card-footer bg-light d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 px-4 py-3 border-top border-light">
        <div class="d-flex align-items-center gap-3 text-muted small">
            <div class="d-flex align-items-center gap-2">
                <span>Show</span>
                <select id="length-select-{{ $id }}" class="form-select form-select-sm w-auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
            <div class="d-none d-sm-block text-muted" id="info-text-{{ $id }}">
                Showing 0 to 0 of 0 entries
            </div>
        </div>
        <div class="d-flex align-items-center gap-1" id="pagination-{{ $id }}">
            <button class="btn btn-sm btn-outline-secondary px-2 py-1 disabled">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="btn btn-sm btn-primary px-3 py-1">1</button>
            <button class="btn btn-sm btn-outline-secondary px-2 py-1 disabled">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

@once
    @push('styles')
        {{--
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css"> --}}
        <style>
            /* Custom Bootstrap Datatable Overrides */
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                display: none !important;
            }

            table.dataTable tbody tr {
                background-color: transparent !important;
                border-bottom: 1px solid rgba(0, 0, 0, .05);
                transition: background-color 0.15s ease;
            }

            table.dataTable.no-footer {
                border-bottom: none !important;
            }

            table.dataTable tbody td {
                padding: 0.75rem 1rem !important;
                vertical-align: middle;
            }

            table.dataTable thead th {
                padding: 0.75rem 1rem !important;
            }

            .dataTables_wrapper .dataTables_processing {
                background: rgba(255, 255, 255, 0.9) !important;
                border-radius: 0.5rem !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
                padding: 1rem !important;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 1050;
            }

            /* Status badge colors compatibility */
            .badge-active {
                background-color: #dcfce7 !important;
                color: #166534 !important;
                border: 1px solid #86efac !important;
            }

            .badge-inactive {
                background-color: #fee2e2 !important;
                color: #991b1b !important;
                border: 1px solid #fca5a5 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        $(document).ready(function () {
            const tableId = '#{{ $id }}';
            const searchInput = '#search-input-{{ $id }}';
            const lengthSelect = '#length-select-{{ $id }}';
            const paginationContainer = '#pagination-{{ $id }}';
            const infoText = '#info-text-{{ $id }}';

            // Initialize DataTable
            const table = $(tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ $url }}",
                },
                columns: @json($columns),
                language: {
                    search: "",
                    searchPlaceholder: "Search records...",
                    processing: `
                        <div class="d-flex align-items-center justify-content-center gap-2 py-4">
                            <div class="spinner-border text-primary spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="text-muted font-medium small">Loading...</span>
                        </div>
                    `,
                    emptyTable: `
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted font-medium mb-1">No records found</p>
                            <p class="text-muted small">Try adjusting your search or filters</p>
                        </div>
                    `
                },
                dom: 't',
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                drawCallback: function () {
                    updatePaginationAndInfo();
                }
            });

            // Custom Search Handler
            $(searchInput).on('keyup', function () {
                table.search(this.value).draw();
            });

            // Custom Length Handler
            $(lengthSelect).on('change', function () {
                table.page.len(parseInt(this.value)).draw();
            });

            // Update Pagination & Info UI
            function updatePaginationAndInfo() {
                const info = table.page.info();

                // Update info text
                const start = info.recordsTotal > 0 ? info.start + 1 : 0;
                const end = Math.min(info.end, info.recordsTotal);
                $(infoText).text(`Showing ${start} to ${end} of ${info.recordsTotal} entries`);

                // Build pagination
                const totalPages = info.pages;
                const currentPage = info.page + 1;
                let paginationHtml = '';

                // Previous button
                paginationHtml += `
                    <button class="btn btn-sm btn-outline-secondary px-2 py-1 ${currentPage <= 1 ? 'disabled' : ''}" 
                            onclick="goToPage(${currentPage - 1}, tableId)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                `;

                // Page numbers
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, currentPage + 2);

                if (startPage > 1) {
                    paginationHtml += `<button class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="goToPage(1, tableId)">1</button>`;
                    if (startPage > 2) {
                        paginationHtml += `<span class="px-1 text-muted">...</span>`;
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const isActive = i === currentPage;
                    paginationHtml += `
                        <button class="btn btn-sm ${isActive ? 'btn-primary' : 'btn-outline-secondary'} px-3 py-1" 
                                onclick="goToPage(${i}, tableId)">
                            ${i}
                        </button>
                    `;
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        paginationHtml += `<span class="px-1 text-muted">...</span>`;
                    }
                    paginationHtml += `<button class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="goToPage(${totalPages}, tableId)">${totalPages}</button>`;
                }

                // Next button
                paginationHtml += `
                    <button class="btn btn-sm btn-outline-secondary px-2 py-1 ${currentPage >= totalPages ? 'disabled' : ''}" 
                            onclick="goToPage(${currentPage + 1}, tableId)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;

                $(paginationContainer).html(paginationHtml);
            }

            // Global function for pagination clicks
            window.goToPage = function (page, tableId) {
                const table = $(tableId).DataTable();
                table.page(page - 1).draw('page');
            };

            // Store table ID for global access
            window.tableId = tableId;

            // Initial render
            updatePaginationAndInfo();

            // Re-render on window resize
            $(window).on('resize', function () {
                table.columns.adjust();
            });
        });
    </script>
@endpush