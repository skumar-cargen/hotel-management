<x-admin-layout title="Career Applications" pageTitle="Career Applications">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.careers.index') }}">Careers</a></li>
        <li class="breadcrumb-item active">Applications</li>
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

        /* Filter pills */
        .filter-pills { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 1.25rem; }
        .filter-pills .fp-btn {
            display: inline-flex; align-items: center; gap: 6px; padding: .45rem .85rem;
            border-radius: 2rem; border: 1.5px solid var(--border-light); background: #fff;
            font-size: .78rem; font-weight: 600; color: var(--text-secondary); cursor: pointer;
            transition: all .2s;
        }
        .filter-pills .fp-btn:hover { border-color: var(--accent-primary); color: var(--accent-primary); }
        .filter-pills .fp-btn.active { background: var(--accent-primary); color: #fff; border-color: var(--accent-primary); }
        .filter-pills .fp-btn .fp-count {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 20px; border-radius: 10px; font-size: .7rem;
            background: rgba(0,0,0,.08);
        }
        .filter-pills .fp-btn.active .fp-count { background: rgba(255,255,255,.25); }

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
        <div class="header-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);">
            <i class='bx bx-user-check'></i>
        </div>
        <div class="flex-grow-1">
            <h4>Career Applications</h4>
            <p>Review applications and connect with candidates</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.careers.index') }}" class="btn btn-light px-4">
                <i class='bx bx-briefcase me-1'></i> Manage Careers
            </a>
        </div>
    </div>

    <!-- Status Filters -->
    <div class="filter-pills">
        <button class="fp-btn active" data-status="">
            <i class='bx bx-list-ul'></i> All
        </button>
        <button class="fp-btn" data-status="new">
            <i class='bx bx-envelope'></i> New
        </button>
        <button class="fp-btn" data-status="reviewed">
            <i class='bx bx-check'></i> Reviewed
        </button>
        <button class="fp-btn" data-status="shortlisted">
            <i class='bx bx-star'></i> Shortlisted
        </button>
        <button class="fp-btn" data-status="rejected">
            <i class='bx bx-x'></i> Rejected
        </button>
    </div>

    <!-- Table -->
    <div class="card table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="data-table" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Phone</th>
                            <th>Career</th>
                            <th>Domain</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th width="80">Actions</th>
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
                    url: '{{ route("admin.career-applications.index") }}',
                    data: function(d) {
                        d.status = $('.filter-pills .fp-btn.active').data('status') || '';
                    }
                },
                columns: [
                    { data: 'applicant_info', name: 'name' },
                    { data: 'phone_display', name: 'phone', orderable: false },
                    { data: 'career_title', name: 'career.title', orderable: false },
                    { data: 'domain_name', name: 'domain.name', orderable: false },
                    { data: 'status_badge', name: 'status', orderable: true, searchable: false },
                    { data: 'applied_at', name: 'created_at', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 25,
                order: [[5, 'desc']]
            });

            // Status filter
            $('.filter-pills .fp-btn').on('click', function() {
                $('.filter-pills .fp-btn').removeClass('active');
                $(this).addClass('active');
                table.ajax.reload();
            });
        });
        </script>
    </x-slot:scripts>
</x-admin-layout>
