<x-admin-layout title="Roles & Permissions" pageTitle="Roles & Permissions" pageDescription="Manage user roles and their permissions">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item active">Roles & Permissions</li>
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

        .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card {
            border: none; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06); padding: 1.25rem;
            display: flex; align-items: center; gap: .875rem; background: #fff;
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; flex-shrink: 0;
        }
        .stat-card .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text-primary); line-height: 1.2; }
        .stat-card .stat-label { font-size: .72rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; font-weight: 600; }

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
        .table-card .dataTables_wrapper .dataTables_filter input { border-radius: 8px; border: 2px solid var(--border-light); padding: .4rem .75rem; font-size: .85rem; transition: border-color .2s; }
        .table-card .dataTables_wrapper .dataTables_filter input:focus { border-color: var(--accent-primary); outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
        .table-card .dataTables_wrapper .dataTables_length select { border-radius: 8px; border: 2px solid var(--border-light); padding: .3rem .5rem; font-size: .85rem; }
        .table-card .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; font-size: .82rem; padding: .35rem .75rem !important; }
        .table-card .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--accent-primary) !important; color: #fff !important; border-color: var(--accent-primary) !important; }
        .table-card .dataTables_wrapper .dataTables_info { font-size: .8rem; color: var(--text-muted); }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-icon" style="background: linear-gradient(135deg, #6366f1, #818cf8);">
            <i class='bx bx-shield-quarter'></i>
        </div>
        <div class="flex-grow-1">
            <h4>Roles & Permissions</h4>
            <p>Manage user roles and their access permissions</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-light px-4">
                <i class='bx bx-plus me-1'></i> New Role
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(99,102,241,.1); color: #6366f1;">
                <i class='bx bx-shield-quarter'></i>
            </div>
            <div>
                <div class="stat-value"><span id="totalRoles">&mdash;</span></div>
                <div class="stat-label">Total Roles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(5,150,105,.1); color: #059669;">
                <i class='bx bx-lock-open-alt'></i>
            </div>
            <div>
                <div class="stat-value"><span id="totalPermissions">&mdash;</span></div>
                <div class="stat-label">Permissions</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="rolesTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Role Name</th>
                            <th>Permissions</th>
                            <th class="text-center">Users</th>
                            <th class="text-center" width="100">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <x-slot:scripts>
    <script>
        $(document).ready(function() {
            const table = $('#rolesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.roles.index') }}',
                columns: [
                    { data: 'id', name: 'id', width: '60px' },
                    { data: 'name', name: 'name', render: function(data) {
                        const icon = data === 'Admin' ? 'bxs-crown' : 'bx-shield-quarter';
                        const color = data === 'Admin' ? '#d97706' : '#667eea';
                        return '<div class="d-flex align-items-center gap-2">'
                            + '<div style="width:32px;height:32px;border-radius:0.5rem;background:' + color + '15;color:' + color + ';display:flex;align-items:center;justify-content:center;font-size:1rem;">'
                            + '<i class="bx ' + icon + '"></i></div>'
                            + '<span class="fw-semibold">' + data + '</span></div>';
                    }},
                    { data: 'permissions_list', name: 'permissions_list', orderable: false, searchable: false },
                    { data: 'users_badge', name: 'users_badge', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center', width: '100px' },
                ],
                order: [],
                drawCallback: function(settings) {
                    document.getElementById('totalRoles').textContent = settings._iRecordsTotal;
                }
            });

            fetch('{{ route('admin.roles.create') }}').then(r => r.text()).then(html => {
                const match = html.match(/data-total-permissions="(\d+)"/);
                if (match) document.getElementById('totalPermissions').textContent = match[1];
            }).catch(() => {});
        });
    </script>
    </x-slot:scripts>
</x-admin-layout>
