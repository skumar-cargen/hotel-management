<x-admin-layout title="Bookings" pageTitle="Bookings">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item active">Bookings</li>
    </x-slot:breadcrumb>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-info: #0891b2; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }
        .page-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #1e40af 100%);
            border-radius: 16px; padding: 1.75rem 2rem; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 1.25rem; position: relative; overflow: hidden;
        }
        .page-header::before {
            content: ''; position: absolute; top: -60%; right: -5%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%);
        }
        .page-header .header-icon {
            width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff; flex-shrink: 0; position: relative; z-index: 1;
        }
        .page-header h4 { color: #fff; font-weight: 700; font-size: 1.25rem; margin: 0; position: relative; z-index: 1; }
        .page-header p { color: rgba(255,255,255,0.5); font-size: .85rem; margin: .25rem 0 0; position: relative; z-index: 1; }
        .page-header .header-actions { position: relative; z-index: 1; }

        .filter-bar {
            background: #fff; border-radius: 16px; padding: 1rem 1.5rem; margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
        }
        .filter-bar .search-box { position: relative; flex: 1; min-width: 220px; }
        .filter-bar .search-box i {
            position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
            font-size: 1.1rem; color: var(--text-muted); pointer-events: none;
        }
        .filter-bar .search-box input {
            padding-left: 2.5rem; border-radius: 10px; border: 2px solid var(--border-light);
            font-size: .85rem; height: 42px; width: 100%; transition: border-color .2s;
        }
        .filter-bar .search-box input:focus { border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
        .filter-bar .filter-sep { width: 1px; height: 28px; background: var(--border-light); flex-shrink: 0; }
        .filter-bar .status-wrap { min-width: 170px; }
        .filter-bar .status-wrap .select2-container--bootstrap-5 .select2-selection { border-radius: 10px; border: 2px solid var(--border-light); height: 42px; }
        .filter-bar .status-wrap .select2-container--bootstrap-5 .select2-selection:focus-within { border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
        .btn-reset {
            height: 42px; padding: 0 1.15rem; border-radius: 10px; border: 2px solid var(--border-light);
            background: #fff; color: var(--text-secondary); font-size: .85rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: .4rem; transition: all .2s; white-space: nowrap;
        }
        .btn-reset:hover { border-color: var(--accent-danger); color: var(--accent-danger); background: #fef2f2; }
        .btn-reset i { font-size: 1.05rem; }

        .table-card { border: none; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04); overflow: hidden; }
        .table-card .card-body { padding: 0; }
        .table-card table.dataTable thead th {
            font-size: .75rem; text-transform: uppercase; letter-spacing: .05em;
            color: #64748b; font-weight: 700; background: #f8fafc; border-bottom: 2px solid #e2e8f0;
            padding: .875rem 1rem; white-space: nowrap;
        }
        .table-card table.dataTable tbody td { padding: .875rem 1rem; font-size: .875rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .table-card table.dataTable tbody tr:hover { background: #f8fafc; }
        .table-card .dataTables_wrapper { padding: 1.25rem 1.5rem; }
        .table-card .dataTables_wrapper .dataTables_filter { margin-left: auto; }
        .table-card .dataTables_wrapper .dataTables_filter input { border-radius: 8px; border: 2px solid var(--border-light); padding: .4rem .75rem; font-size: .85rem; transition: border-color .2s; }
        .table-card .dataTables_wrapper .dataTables_filter input:focus { border-color: var(--accent-primary); outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
        .table-card .dataTables_wrapper .dataTables_length select { border-radius: 8px; border: 2px solid var(--border-light); padding: .3rem .5rem; font-size: .85rem; }
        .table-card .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; font-size: .82rem; padding: .35rem .75rem !important; }
        .table-card .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--accent-primary) !important; color: #fff !important; border-color: var(--accent-primary) !important; }
        .table-card .dataTables_wrapper .dataTables_info { font-size: .8rem; color: var(--text-muted); }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-icon" style="background: linear-gradient(135deg, #059669, #34d399);">
            <i class='bx bx-calendar-check'></i>
        </div>
        <div class="flex-grow-1">
            <h4>Bookings</h4>
            <p>Track and manage all guest reservations</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class='bx bx-search'></i>
            <input type="text" class="form-control" id="search-input" placeholder="Search ref, guest name, email...">
        </div>
        <div class="filter-sep"></div>
        <div class="status-wrap">
            <select class="form-select select2" id="status-filter" data-placeholder="All Status">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
        <button type="button" class="btn-reset" id="reset-btn" title="Reset filters">
            <i class='bx bx-reset'></i> Reset
        </button>
        <div class="ms-auto">
            <a href="{{ route('admin.bookings.export') }}" class="btn btn-outline-success px-4" id="export-btn">
                <i class='bx bx-download me-1'></i> Export
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="card table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="data-table" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Guest</th>
                            <th>Hotel</th>
                            <th>Check-in</th>
                            <th>Nights</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
        $(document).ready(function() {
            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.bookings.index") }}',
                    data: function(d) {
                        d.search_custom = $('#search-input').val();
                        d.status = $('#status-filter').val();
                    }
                },
                columns: [
                    { data: 'reference_number', name: 'reference_number' },
                    { data: 'guest_name', name: 'guest_name' },
                    { data: 'hotel_name', name: 'hotel_name' },
                    { data: 'check_in_date', name: 'check_in_date' },
                    { data: 'num_nights', name: 'num_nights' },
                    { data: 'total_amount', name: 'total_amount' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 25,
                order: []
            });

            var searchTimer;
            $('#search-input').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() { table.draw(); }, 400);
            });

            $('#status-filter').on('change', function() {
                table.draw();
            });

            $('#reset-btn').on('click', function() {
                $('#search-input').val('');
                $('#status-filter').val('').trigger('change');
            });
        });
        </script>
    </x-slot:scripts>
</x-admin-layout>
