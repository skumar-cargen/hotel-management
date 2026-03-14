<x-admin-layout :title="$domain->name" :pageTitle="$domain->name">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.index') }}">Domains</a></li>
        <li class="breadcrumb-item active">{{ $domain->name }}</li>
    </x-slot:breadcrumb>

    <x-slot:actions>
        <a href="{{ route('admin.domains.edit', $domain) }}" class="btn btn-primary btn-sm"><i class='bx bx-edit me-1'></i> Edit</a>
    </x-slot:actions>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold text-primary">{{ $domain->hotels_count }}</div>
                    <div class="text-muted small">Hotels</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold text-success">{{ $domain->locations_count }}</div>
                    <div class="text-muted small">Locations</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="fs-3 fw-bold text-warning">{{ $domain->bookings_count }}</div>
                    <div class="text-muted small">Bookings</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header"><h6 class="mb-0">Domain Details</h6></div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr><th width="200">Domain URL</th><td><a href="https://{{ $domain->domain }}" target="_blank">{{ $domain->domain }}</a></td></tr>
                <tr><th>Slug</th><td>{{ $domain->slug }}</td></tr>
                <tr><th>Language</th><td>{{ $domain->default_language }}</td></tr>
                <tr><th>Status</th><td><span class="badge bg-{{ $domain->is_active ? 'success' : 'secondary' }}">{{ $domain->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                <tr><th>Primary</th><td>{{ $domain->is_primary ? 'Yes' : 'No' }}</td></tr>
                <tr><th>Meta Title</th><td>{{ $domain->meta_title ?? '-' }}</td></tr>
                <tr><th>Created</th><td>{{ $domain->created_at->format('M d, Y') }}</td></tr>
            </table>
        </div>
    </div>
</x-admin-layout>
