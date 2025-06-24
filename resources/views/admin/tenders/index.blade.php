@extends('layouts.app')

@section('content')
<main class="professional-theme">
    @isset($flash)
        <div class="alert alert-{{ $flash['type'] }} alert-dismissible fade show animate__animated animate__shakeX mt-3 shadow-sm rounded" role="alert">
            {{ $flash['message'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endisset

    <div class="head-title animate__animated animate__fadeInDown">
        <div class="left">
            <!-- <h3 class="display-7 fw-bold text-dark">Tender List</h3> -->
        </div>
        <a href="{{ route('admin.tenders.create') }}" class="btn btn-primary btn-md rounded-pill shadow-sm animate__animated animate__pulse animate__infinite">
            <i class='bx bxs-plus-circle me-2'></i>Add New Tender
        </a>
    </div>

    <div class="table-data mt-4">
        <div class="order w-100">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden animate__animated animate__zoomIn">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fw-semibold"><i class='bx bxs-folder-open me-2'></i>Tenders</h3>
                    <span class="badge bg-light text-primary rounded-pill shadow-sm">{{ count($tenders) }} Tenders</span>
                </div>
                <div class="card-body p-0">
                    <div class="controls-section p-3 bg-light border-bottom d-flex justify-content-end align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <!-- DataTables search and buttons will be injected here -->
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="tendersTable" class="table table-hover table-striped w-100 mb-0">
                            <thead class="bg-gradient-primary text-white">
                                <tr>
                                    <th data-name="id">ID</th>
                                    <th data-name="description">Description</th>
                                    <th data-name="state">State</th>
                                    <th data-name="tenderwiz_id">TenderWiz ID</th>
                                    <th data-name="tender_reference_id">Tender Reference ID</th>
                                    <th data-name="department">Department</th>
                                    <th data-name="date">Date</th>
                                    <th data-name="action" class="no-export">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tenders as $tender)
                                    <tr class="animate__animated animate__fadeIn">
                                        <td>{{ $tender['id'] }}</td>
                                        <td>{{ Str::limit($tender['tender_description'], 50) }}</td>
                                        <td>{{ $tender['state'] ?? 'N/A' }}</td>
                                        <td>{{ $tender['tenderwiz_id'] ?? 'N/A' }}</td>
                                        <td>{{ $tender['tender_reference_id'] ?? 'N/A' }}</td>
                                        <td>{{ $tender['department'] ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($tender['date'])->format('d M, Y') }}</td>
                                        <td class="no-export">
                                            <a href="{{ route('admin.tenders.edit', $tender['id']) }}" class="btn btn-sm btn-outline-primary btn-action rounded-pill me-2">
                                                <i class='bx bxs-edit me-1'></i>Edit
                                            </a>
                                            <form action="{{ route('admin.tenders.destroy', $tender['id']) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action rounded-pill" onclick="return confirm('Are you sure you want to delete this tender?')">
                                                    <i class='bx bxs-trash me-1'></i>Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No tenders found.</td>
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

        #tendersTable thead th {
            padding-top: 6px;
            padding-bottom: 6px;
            font-size: 14px;
        }

        #tendersTable tbody tr {
            height: 40px;
        }

        /* Professional Theme Overrides */
        .professional-theme .head-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .professional-theme .head-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a2526;
        }

        .professional-theme .btn-primary {
            background: linear-gradient(90deg, var(--blue), #2a73c0);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .professional-theme .btn-primary:hover {
            background: linear-gradient(90deg, #2a73c0, var(--blue));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(60, 145, 230, 0.3);
        }

        .professional-theme .card {
            background: #fff;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .professional-theme .card-header {
            padding: 1rem 1.5rem;
        }

        .professional-theme .alert {
            border-left: 4px solid;
            padding: 1rem;
        }

        .professional-theme .alert-success {
            border-color: #28a745;
        }

        .professional-theme .alert-danger {
            border-color: #dc3545;
        }

        /* Controls Section */
        .controls-section {
            background: #f8f9fa;
        }

        .controls-section .dataTables_filter label {
            position: relative;
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }

        .controls-section .dataTables_filter input {
            border-radius: 20px;
            padding: 0.25rem 2rem 0.25rem 2.5rem;
            font-size: 0.8rem;
            width: 200px;
            height: calc(1.8rem + 2px);
        }

        .controls-section .bx-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .controls-section .dt-buttons {
            display: inline-flex;
            align-items: center;
            margin-left: 1rem;
        }

        .controls-section .btn-outline-success {
            border-color: #28a745;
            color: #28a745;
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .controls-section .btn-outline-success:hover {
            background: #28a745;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        /* DataTables Styling */
        .dataTables_wrapper {
            margin-top: 0;
            padding: 0 1.5rem 1rem;
        }

        .dataTables_paginate .paginate_button {
            background: transparent !important;
            color: #495057 !important;
            border: none !important;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .dataTables_paginate .paginate_button:hover {
            color: var(--blue) !important;
            transform: scale(1.1);
        }

        .dataTables_paginate .paginate_button.current {
            background: var(--blue) !important;
            color: #fff !important;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(60, 145, 230, 0.2);
        }

        .dataTables_paginate .paginate_button.disabled {
            color: #adb5bd !important;
        }

        .dataTables_info {
            color: #6c757d;
            font-size: 0.875rem;
            padding-top: 0.75rem;
        }

        .btn-action {
            padding: 0.375rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-primary {
            border-color: var(--blue);
            color: var(--blue);
        }

        .btn-outline-primary:hover {
            background: var(--blue);
            color: #fff;
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: #fff;
        }

        /* Ensure full-width table */
        .table-data .order {
            width: 100% !important;
        }

        #tendersTable {
            width: 100% !important;
        }

        /* Responsive Adjustments */
        @media screen and (max-width: 576px) {
            .controls-section .dataTables_filter input {
                width: 150px;
            }

            .controls-section .btn-outline-success, .btn-action {
                padding: 0.25rem 0.75rem;
                font-size: 0.8rem;
            }

            #tendersTable th,
            #tendersTable td {
                padding: 0.5rem;
            }

            .controls-section .dt-buttons {
                margin-left: 0.5rem;
            }
        }
    </style>
</main>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <script>
        $(document).ready(function () {
            // Initialize DataTables
            const table = $('#tendersTable').DataTable({
                responsive: true,
                pageLength: 10, // Fixed page length
                order: [[0, 'desc']],
                language: {
                    search: '',
                    searchPlaceholder: 'Search tenders...',
                    paginate: {
                        previous: '<i class="bx bx-chevron-left"></i>',
                        next: '<i class="bx bx-chevron-right"></i>'
                    }
                },
                dom: '<"controls-section"fB>rtip', // Place search and buttons in controls-section
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bx bxs-file-export me-1"></i>Export Excel',
                        className: 'btn btn-sm btn-outline-success',
                        exportOptions: {
                            columns: ':not(.no-export)' // Explicitly exclude the 'Actions' column from export
                        },
                        title: 'tenders_export'
                    }
                ],
                drawCallback: function () {
                    // Reapply animations on page change
                    $('#tendersTable tbody tr').each(function (index) {
                        $(this).css('animation-delay', (index * 0.05) + 's');
                        $(this).addClass('animate__animated animate__fadeIn');
                    });

                    // Add hover animations for table rows
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
                    { data: 'id', name: 'id' },
                    { data: 'description', name: 'description' },
                    { data: 'state', name: 'state' },
                    { data: 'tenderwiz_id', name: 'tenderwiz_id' },
                    { data: 'tender_reference_id', name: 'tender_reference_id' },
                    { data: 'department', name: 'department' },
                    { data: 'date', name: 'date' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Move search and buttons to controls-section
            $('.controls-section .d-flex').append($('.dataTables_filter, .dt-buttons'));

            // Hover animations for buttons
            $('.btn-action, .dt-button').hover(
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