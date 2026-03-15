<x-admin-layout title="Newsletter Subscribers" pageTitle="Newsletter Subscribers">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item active">Newsletter Subscribers</li>
    </x-slot:breadcrumb>

    <style>
        :root { --accent-primary: #4f46e5; --border-light: #e5e7eb; --text-muted: #9ca3af; }
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
        .page-header .header-stats { display: flex; gap: 1.5rem; margin-left: auto; position: relative; z-index: 1; }
        .page-header .stat-item { text-align: center; }
        .page-header .stat-value { color: #fff; font-size: 1.5rem; font-weight: 700; line-height: 1; }
        .page-header .stat-label { color: rgba(255,255,255,0.4); font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; margin-top: .25rem; }

        .filter-bar {
            display: flex; align-items: center; gap: .75rem; padding: 1rem 1.5rem;
            background: #fff; border-radius: 12px; margin-bottom: 1rem;
            border: 1px solid var(--border-light);
        }
        .filter-bar .search-box {
            position: relative; flex: 1; max-width: 320px;
        }
        .filter-bar .search-box i {
            position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
            color: var(--text-muted); font-size: 1.1rem;
        }
        .filter-bar .search-box input {
            padding-left: 2.25rem; border-radius: 8px; border: 2px solid var(--border-light);
            font-size: .85rem; height: 38px; width: 100%; transition: border-color .2s;
        }
        .filter-bar .search-box input:focus {
            border-color: var(--accent-primary); outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,.1);
        }
        .filter-bar .filter-sep { width: 1px; height: 24px; background: var(--border-light); }
        .filter-bar .status-wrap select {
            border-radius: 8px; border: 2px solid var(--border-light); font-size: .85rem; height: 38px; min-width: 140px;
        }
        .filter-bar .domain-wrap select {
            border-radius: 8px; border: 2px solid var(--border-light); font-size: .85rem; height: 38px; min-width: 180px;
        }
        .filter-bar .btn-reset {
            background: none; border: 1px solid var(--border-light); border-radius: 8px;
            padding: .4rem .75rem; font-size: .82rem; color: var(--text-muted);
            cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: .35rem;
        }
        .filter-bar .btn-reset:hover { border-color: #ef4444; color: #ef4444; }
        .filter-bar .btn-export {
            background: none; border: 1px solid var(--border-light); border-radius: 8px;
            padding: .4rem .75rem; font-size: .82rem; color: var(--text-muted);
            cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: .35rem;
            margin-left: auto;
        }
        .filter-bar .btn-export:hover { border-color: #10b981; color: #10b981; }

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
        .table-card .dataTables_wrapper .dataTables_filter { display: none; }
        .table-card .dataTables_wrapper .dataTables_length select { border-radius: 8px; border: 2px solid var(--border-light); padding: .3rem .5rem; font-size: .85rem; }
        .table-card .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; font-size: .82rem; padding: .35rem .75rem !important; }
        .table-card .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--accent-primary) !important; color: #fff !important; border-color: var(--accent-primary) !important; }
        .table-card .dataTables_wrapper .dataTables_info { font-size: .8rem; color: var(--text-muted); }
    </style>

    <div class="page-header">
        <div class="header-icon" style="background: linear-gradient(135deg, #8b5cf6, #d946ef);">
            <i class='bx bx-mail-send'></i>
        </div>
        <div>
            <h4>Newsletter Subscribers</h4>
            <p>View and manage newsletter subscriptions across all domains</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class='bx bx-search'></i>
            <input type="text" class="form-control" id="search-input" placeholder="Search by email...">
        </div>
        <div class="filter-sep"></div>
        <div class="status-wrap">
            <select class="form-select" id="status-filter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Unsubscribed</option>
            </select>
        </div>
        <div class="domain-wrap">
            <select class="form-select select2" id="domain-filter" data-placeholder="All Domains">
                <option value="">All Domains</option>
            </select>
        </div>
        <button type="button" class="btn-reset" id="reset-btn" title="Reset filters">
            <i class='bx bx-reset'></i> Reset
        </button>
        <button type="button" class="btn-export" id="export-btn" title="Export CSV">
            <i class='bx bx-download'></i> Export
        </button>
    </div>

    <!-- Table -->
    <div class="card table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="data-table" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Domain</th>
                            <th>Status</th>
                            <th>Subscribed</th>
                            <th>Unsubscribed</th>
                            <th>IP Address</th>
                            <th width="140">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
        $(document).ready(function() {
            // Load domains for filter
            $.get('{{ route("admin.api.search.domains") }}', function(data) {
                var sel = $('#domain-filter');
                (data.results || data).forEach(function(d) {
                    sel.append('<option value="' + d.id + '">' + d.text + '</option>');
                });
            });

            var table = $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.newsletter-subscribers.index") }}',
                    data: function(d) {
                        d.search_custom = $('#search-input').val();
                        d.status = $('#status-filter').val();
                        d.domain_id = $('#domain-filter').val();
                    }
                },
                columns: [
                    { data: 'email', name: 'email' },
                    { data: 'domain_name', name: 'domain.name', searchable: false },
                    { data: 'status_badge', name: 'is_active', searchable: false },
                    { data: 'subscribed_at', name: 'subscribed_at' },
                    { data: 'unsubscribed_at', name: 'unsubscribed_at', searchable: false },
                    { data: 'ip_address', name: 'ip_address' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 25,
                order: [[3, 'desc']]
            });

            // Search with debounce
            var searchTimer;
            $('#search-input').on('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() { table.draw(); }, 400);
            });

            $('#status-filter, #domain-filter').on('change', function() {
                table.draw();
            });

            $('#reset-btn').on('click', function() {
                $('#search-input').val('');
                $('#status-filter').val('');
                $('#domain-filter').val('').trigger('change');
                table.draw();
            });

            // Toggle active/inactive
            $(document).on('click', '.toggle-btn', function() {
                var btn = $(this);
                var id = btn.data('id');
                var newStatus = btn.data('status');
                $.ajax({
                    url: '{{ url("admin/newsletter-subscribers") }}/' + id,
                    method: 'PUT',
                    data: { _token: '{{ csrf_token() }}', is_active: newStatus },
                    success: function(res) {
                        toastr.success(res.message);
                        table.draw(false);
                    },
                    error: function() { toastr.error('Something went wrong.'); }
                });
            });

            // Delete
            $(document).on('click', '.delete-btn', function() {
                var id = $(this).data('id');
                if (!confirm('Are you sure you want to delete this subscriber?')) return;
                $.ajax({
                    url: '{{ url("admin/newsletter-subscribers") }}/' + id,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        toastr.success(res.message);
                        table.draw(false);
                    },
                    error: function() { toastr.error('Something went wrong.'); }
                });
            });

            // Export CSV
            $('#export-btn').on('click', function() {
                var params = new URLSearchParams({
                    search_custom: $('#search-input').val(),
                    status: $('#status-filter').val(),
                    domain_id: $('#domain-filter').val(),
                    export: 'csv'
                });
                window.location.href = '{{ route("admin.newsletter-subscribers.index") }}?' + params.toString();
            });
        });
        </script>
    </x-slot:scripts>
</x-admin-layout>
