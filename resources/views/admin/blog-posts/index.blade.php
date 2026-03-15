<x-admin-layout title="Blog Posts" pageTitle="Blog Posts">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item active">Blog Posts</li>
    </x-slot:breadcrumb>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-info: #0891b2; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }

        /* ── Premium Page Header ──────────────────────── */
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
        .header-stat { display: inline-flex; align-items: center; gap: .4rem; background: rgba(255,255,255,0.08); padding: .35rem .85rem; border-radius: 2rem; font-size: .78rem; color: rgba(255,255,255,0.7); margin-top: .5rem; }
        .header-stat i { font-size: .9rem; }

        /* ── Premium Table Card ───────────────────────── */
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
        <div class="header-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);">
            <i class='bx bx-news'></i>
        </div>
        <div class="flex-grow-1">
            <h4>Blog Posts</h4>
            <p>Manage blog articles and content</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-light px-4">
                <i class='bx bx-plus me-1'></i> Add Post
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
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Domains</th>
                            <th>Status</th>
                            <th>Published</th>
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
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.blog-posts.index") }}',
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'category_name', name: 'category_name', orderable: false, searchable: false },
                    { data: 'author_name', name: 'author_name', orderable: false, searchable: false },
                    { data: 'domain_list', name: 'domain_list', orderable: false, searchable: false },
                    { data: 'status_label', name: 'status', orderable: true, searchable: false },
                    { data: 'published_date', name: 'published_at', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                responsive: true,
                pageLength: 25,
                order: []
            });
        });
        </script>
    </x-slot:scripts>
</x-admin-layout>
