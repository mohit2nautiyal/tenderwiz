$(document).ready(function () {
    // Initialize DataTables
    const table = $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10, // Fixed page length
        order: [[0, 'desc']],
        language: {
            search: '',
            searchPlaceholder: 'Search users...',
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
                    columns: ':not(.no-export)' // Exclude Actions column
                },
                title: 'users_export'
            }
        ],
        drawCallback: function () {
            // Reapply animations on page change
            $('#usersTable tbody tr').each(function (index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
                $(this).addClass('animate__animated animate__fadeIn');
            });

            // Add hover animations for table rows
            $('#usersTable tbody tr').hover(
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
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'mobile_no', name: 'mobile_no' },
            { data: 'company_name', name: 'company_name' },
            { data: 'subscription_end_date', name: 'subscription_end_date' },
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