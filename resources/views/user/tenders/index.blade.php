@extends('layouts.app')

@section('content')
<main class="professional-theme">
    @isset($flash)
        <div class="alert alert-{{ $flash['type'] }} alert-dismissible fade show animate__animated animate__shakeX mt-3 shadow-sm rounded" role="alert">
            {{ $flash['message'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endisset

    <div class="table-data mt-2">
        <div class="order w-100">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden animate__animated animate__zoomIn">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fw-semibold"><i class='bx bxs-folder-open me-2'></i>Tenders</h3>
                    <span class="badge bg-light text-primary rounded-pill shadow-sm">{{ count($tenders) }} Tenders</span>
                </div>
                <div class="card-body p-0">
                    <div class="filter-section p-3 bg-light border-bottom">
                        <form id="tenderFilters" class="row g-3 align-items-end">
                            <!-- First Row of Filters -->
                            <div class="col-md-2 col-sm-6">
                                <label for="tenderType" class="form-label fw-medium text-dark mb-1 form-label-sm">Type</label>
                                <select id="tenderType" class="form-select form-select-sm shadow-sm">
                                    <option value="">All Types</option>
                                    @foreach (config('constants.TENDER_TYPES', []) as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label for="department" class="form-label fw-medium text-dark mb-1 form-label-sm">Department</label>
                                <select id="department" class="form-select form-select-sm shadow-sm">
                                    <option value="">All Depts</option>
                                    @foreach (config('constants.DEPARTMENTS', []) as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label for="state" class="form-label fw-medium text-dark mb-1 form-label-sm">State</label>
                                <select id="state" class="form-select form-select-sm shadow-sm">
                                    <option value="">All States</option>
                                    @foreach (config('constants.INDIAN_STATES_AND_UTS', []) as $state)
                                        <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label for="tenderNature" class="form-label fw-medium text-dark mb-1 form-label-sm">Nature</label>
                                <select id="tenderNature" class="form-select form-select-sm shadow-sm">
                                    <option value="">All Natures</option>
                                    @foreach (config('constants.TENDER_NATURE_OPTIONS', []) as $nature)
                                        <option value="{{ $nature }}">{{ $nature }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label for="procurementCategory" class="form-label fw-medium text-dark mb-1 form-label-sm">Procurement Category</label>
                                <select id="procurementCategory" class="form-select form-select-sm shadow-sm">
                                    <option value="">All Categories</option>
                                    @foreach (config('constants.PROCUREMENT_CATEGORIES', []) as $category)
                                        <option value="{{ $category }}">{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="col-md-2 col-sm-6">
                                <label for="tenderValueMin" class="form-label fw-medium text-dark mb-1 form-label-sm">Min Value (₹)</label>
                                <input type="number" id="tenderValueMin" class="form-control form-control-sm shadow-sm" placeholder="Min">
                            </div>
                            

                            <!-- Second Row of Filters -->
                           <!--  <div class="col-md-2 col-sm-6">
                                <label for="status" class="form-label fw-medium text-dark mb-1 form-label-sm">Status</label>
                                <select id="status" class="form-select form-select-sm shadow-sm">
                                    <option value="">All Statuses</option>
                                    <option value="Active">Active</option>
                                    <option value="Closed">Closed</option>
                                    <option value="Upcoming">Upcoming</option>
                                </select>
                            </div> -->
                           
                            <div class="col-md-2 col-sm-6">
                                <label for="tenderValueMax" class="form-label fw-medium text-dark mb-1 form-label-sm">Max Value (₹)</label>
                                <input type="number" id="tenderValueMax" class="form-control form-control-sm shadow-sm" placeholder="Max">
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label for="dateFrom" class="form-label fw-medium text-dark mb-1 form-label-sm">From Date</label>
                                <input type="date" id="dateFrom" class="form-control form-control-sm shadow-sm">
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label for="dateTo" class="form-label fw-medium text-dark mb-1 form-label-sm">To Date</label>
                                <input type="date" id="dateTo" class="form-control form-control-sm shadow-sm">
                            </div>
                            <div class="col-md-2 col-sm-6 d-flex align-items-end">
                                <button type="button" id="applyFilters" class="btn btn-primary btn-sm w-100 shadow-sm">Apply</button>
                            </div>
                            <div class="col-md-2 col-sm-6 d-flex align-items-end">
                                <button type="button" id="resetFilters" class="btn btn-outline-secondary btn-sm w-100 shadow-sm">Reset</button>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table id="tendersTable" class="table table-hover table-striped w-100 mb-0">
                            <thead class="bg-gradient-primary text-white">
                                <tr>
                                    <th data-name="sno">S.No</th>
                                    <th data-name="name">Tender Name</th>
                                    <th data-name="state">State</th>
                                    <th data-name="tenderwiz_id">TenderWiz ID</th>
                                    <th data-name="tender_reference_id">Reference ID</th>
                                    <th data-name="department">Dept</th>
                                    <th data-name="date">Date</th>
                                    <th data-name="days_remaining">Days Rem.</th>
                                    <th data-name="criteria_match">Match</th>
                                    <th data-name="action">Action</th>
                                    <th style="display: none;" data-name="tender_type">Tender Type</th>
                                    <th style="display: none;" data-name="tender_nature">Tender Nature</th>
                                    <th style="display: none;" data-name="procurement_category">Procurement Category</th>
                                    <th style="display: none;" data-name="raw_date">Raw Date</th>
                                    <th style="display: none;" data-name="status">Status</th>
                                    <th style="display: none;" data-name="tender_value">Tender Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tenders as $key => $tender)
                                    <tr class="animate__animated animate__fadeIn">
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $tender['tender_name'] }}</td>
                                        <td>{{ $tender['state'] ?? 'N/A' }}</td>
                                        <td>{{ $tender['tenderwiz_id'] ?? 'N/A' }}</td>
                                        <td>{{ $tender['tender_reference_id'] ?? 'N/A' }}</td>
                                        <td>{{ $tender['department'] ?? 'N/A' }}</td>
                                        <td>{{ $tender['display_date'] ?? 'N/A' }}</td>
                                        <td>{{ $tender['days_remaining'] === 0 ? 'Expired' : $tender['days_remaining'] . ' days' }}</td>
                                        <td>
                                            <a href="{{ route('user.tenders.certificate', $tender['id'] ?? '#') }}" class="circular-progress-link" style="text-decoration: none;">
                                                <div class="circular-progress" data-percentage="{{ $tender['criteria_match'] }}">
                                                    <svg class="progress-ring" width="50" height="50">
                                                        <circle class="progress-ring__background" cx="25" cy="25" r="20" stroke="#e9ecef" stroke-width="5" fill="transparent" />
                                                        <circle class="progress-ring__circle" cx="25" cy="25" r="20" stroke="#000" stroke-width="5" fill="transparent" />
                                                        <text x="25" y="30" font-size="12" text-anchor="middle" fill="#333">{{ $tender['criteria_match'] }}%</text>
                                                    </svg>
                                                    <span class="criteria-match-tooltip">Match: {{ $tender['criteria_match'] }}%</span>
                                                </div>
                                                <span class="criteria-match-value" style="display: none;">{{ $tender['criteria_match'] }}</span>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.tenders.show', $tender['id'] ?? '#') }}" class="btn btn-sm btn-outline-primary btn-action rounded-pill">
                                                <i class='bx bxs-show me-1'></i>View
                                            </a>
                                        </td>
                                        <td style="display: none;">{{ $tender['tender_type'] ?? 'N/A' }}</td>
                                        <td style="display: none;">{{ $tender['tender_nature'] ?? 'N/A' }}</td>
                                        <td style="display: none;">{{ $tender['procurement_category'] ?? 'N/A' }}</td>
                                        <td style="display: none;">{{ $tender['date'] ?? 'N/A' }}</td>
                                        <td style="display: none;">{{ $tender['status'] ?? 'N/A' }}</td>
                                        <td style="display: none;">{{ $tender['tender_value'] ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="16" class="text-center">No tenders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Reduced padding and font size for table cells */
        #tendersTable th,
        #tendersTable td {
            padding: 4px 8px;
            font-size: 13px;
            line-height: 1.2;
            vertical-align: middle;
        }

        /* Decreased height of table header cells */
        #tendersTable thead th {
            padding-top: 6px;
            padding-bottom: 6px;
            font-size: 14px;
        }

        #tendersTable tbody tr {
            height: 40px;
        }

        /* Adjusting form-label margins for compact filter row */
        .filter-section .form-label {
            margin-bottom: 0.2rem !important;
            font-size: 0.85rem;
        }

        /* Adjusting select and input field heights for compact filter row */
        .filter-section .form-select,
        .filter-section .form-control {
            height: calc(1.9rem + 2px);
            padding: 0.3rem 0.5rem;
            font-size: 0.85rem;
            border-radius: 0.25rem;
        }

        /* Ensure columns stack nicely on small screens */
        @media (max-width: 991.98px) {
            .filter-section .col-sm-6 {
                flex: 0 0 auto;
                width: 50%;
            }
        }

        @media (max-width: 575.98px) {
            .filter-section .col-sm-6 {
                width: 100%;
            }
        }

        .circular-progress {
            position: relative;
            width: 45px;
            height: 45px;
            margin: 0 auto;
        }

        .progress-ring__background {
            stroke: #e9ecef;
        }

        .progress-ring__circle {
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
            stroke-dasharray: 125.6;
            stroke-dashoffset: 125.6;
            transition: stroke-dashoffset 1s ease-in-out, stroke 0.3s ease;
        }

        .circular-progress svg text {
            font-size: 11px !important;
            y: 28px !important;
        }

        .circular-progress:hover .criteria-match-tooltip {
            visibility: visible;
            opacity: 1;
        }

        .criteria-match-tooltip {
            visibility: hidden;
            width: 80px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 4px;
            padding: 2px 5px;
            position: absolute;
            z-index: 1;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 12px;
        }

        .criteria-match-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0;
        }

        .circular-progress-link {
            display: inline-block;
            transition: transform 0.2s;
        }

        .circular-progress-link:hover {
            transform: scale(1.05);
        }

        .circular-progress-link:hover .progress-ring__circle {
            stroke: #0d6efd;
        }

        .circular-progress-link:hover text {
            fill: #0d6efd;
        }

        .filter-section {
            background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .filter-section .btn {
            transition: all 0.3s ease;
        }

        .filter-section .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</main>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize DataTables with client-side processing
            const table = $('#tendersTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 15, 20, 50],
                order: [[0, 'asc']],
                language: {
                    search: '<i class="bx bx-search me-2"></i>',
                    searchPlaceholder: 'Search tenders...',
                    paginate: {
                        previous: '<i class="bx bx-chevron-left"></i>',
                        next: '<i class="bx bx-chevron-right"></i>'
                    }
                },
                processing: false,
                serverSide: false,
                searching: true,
                initComplete: function() {
                    animateProgressBars();
                },
                drawCallback: function () {
                    $('#tendersTable tbody tr').each(function (index) {
                        $(this).css('animation-delay', (index * 0.05) + 's');
                        $(this).addClass('animate__animated animate__fadeIn');
                    });
                    animateProgressBars();
                    $('#tendersTable tbody tr').hover(
                        function () {
                            $(this).addClass('animate__animated animate__pulse');
                        },
                        function () {
                            $(this).removeClass('animate__animated animate__pulse');
                        }
                    );
                },
                columns: [
                    { data: 'sno', name: 'sno' },
                    { data: 'name', name: 'name' },
                    { data: 'state', name: 'state' },
                    { data: 'tenderwiz_id', name: 'tenderwiz_id' },
                    { data: 'tender_reference_id', name: 'tender_reference_id' },
                    { data: 'department', name: 'department' },
                    { data: 'date', name: 'date' },
                    { data: 'days_remaining', name: 'days_remaining' },
                    { data: 'criteria_match', name: 'criteria_match' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'tender_type', name: 'tender_type', visible: false },
                    { data: 'tender_nature', name: 'tender_nature', visible: false },
                    { data: 'procurement_category', name: 'procurement_category', visible: false },
                    { data: 'raw_date', name: 'raw_date', visible: false },
                    { data: 'status', name: 'status', visible: false },
                    { data: 'tender_value', name: 'tender_value', visible: false }
                ]
            });

            // Function to animate progress bars
            function animateProgressBars() {
                $('.circular-progress').each(function() {
                    const $progress = $(this);
                    const percentage = $progress.data('percentage') || 0;
                    const $circle = $progress.find('.progress-ring__circle');
                    const radius = 20;
                    const circumference = 2 * Math.PI * radius;
                    const offset = circumference - (percentage / 100) * circumference;
                    $circle.css('stroke-dashoffset', offset);
                    let strokeColor = '#ff6b6b';
                    if (percentage > 40 && percentage <= 70) {
                        strokeColor = '#f1c40f';
                    } else if (percentage > 70) {
                        strokeColor = '#4ecdc4';
                    }
                    $circle.css('stroke', strokeColor);
                });
            }

            // Custom filtering function
            function filterTable() {
                const tenderType = $('#tenderType').val() || '';
                const department = $('#department').val() || '';
                const state = $('#state').val() || '';
                const tenderNature = $('#tenderNature').val() || '';
                const procurementCategory = $('#procurementCategory').val() || '';
                const status = $('#status').val() || '';
                const tenderValueMin = $('#tenderValueMin').val();
                const tenderValueMax = $('#tenderValueMax').val();
                const dateFrom = $('#dateFrom').val();
                const dateTo = $('#dateTo').val();

                table.search('');
                $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(
                    (func) => func.name !== 'dateRangeFilter' && func.name !== 'valueRangeFilter'
                );

                table.column('tender_type:name').search(tenderType.toLowerCase());
                table.column('department:name').search(department.toLowerCase());
                table.column('state:name').search(state.toLowerCase());
                table.column('tender_nature:name').search(tenderNature.toLowerCase());
                table.column('procurement_category:name').search(procurementCategory.toLowerCase());
                table.column('status:name').search(status.toLowerCase());

                if (tenderValueMin || tenderValueMax) {
                    $.fn.dataTable.ext.search.push(
                        function valueRangeFilter(settings, data, dataIndex) {
                            const value = parseFloat(data[table.column('tender_value:name').index()]) || 0;
                            const min = tenderValueMin ? parseFloat(tenderValueMin) : null;
                            const max = tenderValueMax ? parseFloat(tenderValueMax) : null;
                            if (min && value < min) return false;
                            if (max && value > max) return false;
                            return true;
                        }
                    );
                }

                if (dateFrom || dateTo) {
                    $.fn.dataTable.ext.search.push(
                        function dateRangeFilter(settings, data, dataIndex) {
                            const rawDate = data[table.column('raw_date:name').index()] || '';
                            if (!rawDate) return true;
                            const date = new Date(rawDate);
                            const from = dateFrom ? new Date(dateFrom) : null;
                            const to = dateTo ? new Date(dateTo) : null;
                            if (from && date < from) return false;
                            if (to && date > to) return false;
                            return true;
                        }
                    );
                }

                table.draw();
            }

            // Apply filters when any filter changes or apply button is clicked
            $('#tenderType, #department, #state, #tenderNature, #procurementCategory, #status, #tenderValueMin, #tenderValueMax, #dateFrom, #dateTo').on('change', function() {
                filterTable();
            });

            $('#applyFilters').on('click', function() {
                filterTable();
            });

            // Reset all filters
            $('#resetFilters').on('click', function() {
                $('#tenderFilters')[0].reset();
                table.columns().search('');
                table.search('');
                $.fn.dataTable.ext.search = [];
                table.draw();
            });

            // Hover animations for buttons
            $('.btn-action, #applyFilters, #resetFilters').hover(
                function () {
                    $(this).addClass('animate__animated animate__pulse');
                },
                function () {
                    $(this).removeClass('animate__animated animate__pulse');
                }
            );
        });
    </script>
@endsection