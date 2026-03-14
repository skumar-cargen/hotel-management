@php $isEdit = $role->exists; @endphp

<x-admin-layout
    title="{{ $isEdit ? 'Edit Role: ' . $role->name : 'Create Role' }}"
    pageTitle="{{ $isEdit ? 'Edit Role' : 'Create New Role' }}"
    pageDescription="{{ $isEdit ? 'Update role permissions for: ' . $role->name : 'Define a new role with specific permissions' }}"
>
    <x-slot:actions>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary">
            <i class='bx bx-arrow-back me-1'></i> Back to Roles
        </a>
    </x-slot:actions>

    <div data-total-permissions="{{ $permissions->flatten()->count() }}"></div>

    <form action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-4">
            {{-- Left Column: Role Info --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold"><i class='bx bx-shield-quarter me-1'></i> Role Information</h6>
                    </div>
                    <div class="card-body">
                        <x-form.input name="name" label="Role Name" :value="$role->name" required
                            placeholder="e.g., Content Manager" help="Unique name for this role" />

                        @if($isEdit)
                        <div class="mt-4 p-3" style="background:var(--bg-body);border-radius:var(--radius-md);">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.78rem;color:var(--text-secondary);">Assigned Users</span>
                                <span class="badge bg-primary">{{ $role->users->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.78rem;color:var(--text-secondary);">Permissions</span>
                                <span class="badge bg-success">{{ count($rolePermissions) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span style="font-size:0.78rem;color:var(--text-secondary);">Created</span>
                                <span style="font-size:0.78rem;">{{ $role->created_at?->format('M d, Y') }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class='bx bx-{{ $isEdit ? 'save' : 'plus' }} me-1'></i>
                                {{ $isEdit ? 'Update Role' : 'Create Role' }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Quick Select --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0 fw-bold"><i class='bx bx-check-double me-1'></i> Quick Select</h6>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                            <i class='bx bx-check-double me-1'></i> Select All Permissions
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                            <i class='bx bx-x me-1'></i> Deselect All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllView">
                            <i class='bx bx-show me-1'></i> Select All "View" Permissions
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllManage">
                            <i class='bx bx-cog me-1'></i> Select All "Manage" Permissions
                        </button>
                        <hr class="my-1">
                        <div class="text-muted text-center" style="font-size:0.72rem;">
                            <span id="selectedCount">{{ count($rolePermissions) }}</span> / {{ $permissions->flatten()->count() }} selected
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Permissions Grid --}}
            <div class="col-lg-8">
                @php
                    $categoryIcons = [
                        'Domains' => ['icon' => 'bx-globe', 'color' => '#667eea'],
                        'Locations' => ['icon' => 'bx-map', 'color' => '#a855f7'],
                        'Hotels' => ['icon' => 'bx-building', 'color' => '#3b82f6'],
                        'Room Types' => ['icon' => 'bx-bed', 'color' => '#db2777'],
                        'Pricing' => ['icon' => 'bx-dollar-circle', 'color' => '#d97706'],
                        'Bookings' => ['icon' => 'bx-calendar-check', 'color' => '#059669'],
                        'Users' => ['icon' => 'bx-group', 'color' => '#0891b2'],
                        'Reviews' => ['icon' => 'bx-message-dots', 'color' => '#f59e0b'],
                        'Analytics' => ['icon' => 'bx-bar-chart-alt-2', 'color' => '#8b5cf6'],
                        'Currencies' => ['icon' => 'bx-money', 'color' => '#059669'],
                        'Pages & SEO' => ['icon' => 'bx-file', 'color' => '#ec4899'],
                        'Settings' => ['icon' => 'bx-cog', 'color' => '#6b7280'],
                        'Other' => ['icon' => 'bx-dots-horizontal-rounded', 'color' => '#94a3b8'],
                    ];
                @endphp

                @foreach($permissions as $category => $perms)
                @php $cat = $categoryIcons[$category] ?? ['icon' => 'bx-circle', 'color' => '#94a3b8']; @endphp
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:0.5rem;background:{{ $cat['color'] }}15;color:{{ $cat['color'] }};display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                                <i class='bx {{ $cat['icon'] }}'></i>
                            </div>
                            <h6 class="mb-0 fw-bold" style="font-size:0.9rem;">{{ $category }}</h6>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:0.65rem;">{{ $perms->count() }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-primary text-decoration-none toggle-category" style="font-size:0.78rem;">
                            Toggle All
                        </button>
                    </div>
                    <div class="card-body py-3">
                        <div class="row g-2">
                            @foreach($perms as $permission)
                            @php
                                $isChecked = in_array($permission->id, $rolePermissions);
                                $permType = str_contains($permission->name, 'manage') ? 'manage'
                                    : (str_contains($permission->name, 'view') ? 'view'
                                    : (str_contains($permission->name, 'cancel') ? 'danger'
                                    : (str_contains($permission->name, 'refund') ? 'warning' : 'other')));
                                $typeColors = [
                                    'manage' => ['bg' => 'rgba(102,126,234,0.08)', 'border' => '#667eea', 'badge' => 'primary'],
                                    'view' => ['bg' => 'rgba(5,150,105,0.08)', 'border' => '#059669', 'badge' => 'success'],
                                    'danger' => ['bg' => 'rgba(220,38,38,0.08)', 'border' => '#dc2626', 'badge' => 'danger'],
                                    'warning' => ['bg' => 'rgba(217,119,6,0.08)', 'border' => '#d97706', 'badge' => 'warning'],
                                    'other' => ['bg' => 'rgba(107,114,128,0.08)', 'border' => '#6b7280', 'badge' => 'secondary'],
                                ];
                                $tc = $typeColors[$permType];
                            @endphp
                            <div class="col-sm-6 col-md-4">
                                <label class="permission-card d-flex align-items-center gap-2 p-2 rounded-3 cursor-pointer {{ $isChecked ? 'active' : '' }}"
                                    style="border:2px solid {{ $isChecked ? $tc['border'] : 'var(--border-light)' }};background:{{ $isChecked ? $tc['bg'] : 'transparent' }};transition:all 0.2s;cursor:pointer;">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        class="form-check-input permission-check m-0" data-type="{{ $permType }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                        style="width:18px;height:18px;flex-shrink:0;">
                                    <div>
                                        <div class="fw-semibold" style="font-size:0.8rem;line-height:1.3;">{{ $permission->name }}</div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </form>

    <x-slot:styles>
    <style>
        .permission-card:hover { border-color: #667eea !important; background: rgba(102,126,234,0.04) !important; }
        .permission-card.active { font-weight: 500; }
    </style>
    </x-slot:styles>

    <x-slot:scripts>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const allChecks = document.querySelectorAll('.permission-check');

            function updateCount() {
                const checked = document.querySelectorAll('.permission-check:checked').length;
                const el = document.getElementById('selectedCount');
                if (el) el.textContent = checked;
            }

            function updateCardStyle(checkbox) {
                const card = checkbox.closest('.permission-card');
                if (!card) return;
                if (checkbox.checked) {
                    card.classList.add('active');
                    card.style.borderColor = '#667eea';
                    card.style.background = 'rgba(102,126,234,0.08)';
                } else {
                    card.classList.remove('active');
                    card.style.borderColor = 'var(--border-light)';
                    card.style.background = 'transparent';
                }
            }

            allChecks.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateCardStyle(this);
                    updateCount();
                });
            });

            // Select All
            document.getElementById('selectAll')?.addEventListener('click', function() {
                allChecks.forEach(cb => { cb.checked = true; updateCardStyle(cb); });
                updateCount();
            });

            // Deselect All
            document.getElementById('deselectAll')?.addEventListener('click', function() {
                allChecks.forEach(cb => { cb.checked = false; updateCardStyle(cb); });
                updateCount();
            });

            // Select All View
            document.getElementById('selectAllView')?.addEventListener('click', function() {
                allChecks.forEach(cb => {
                    if (cb.dataset.type === 'view') { cb.checked = true; updateCardStyle(cb); }
                });
                updateCount();
            });

            // Select All Manage
            document.getElementById('selectAllManage')?.addEventListener('click', function() {
                allChecks.forEach(cb => {
                    if (cb.dataset.type === 'manage') { cb.checked = true; updateCardStyle(cb); }
                });
                updateCount();
            });

            // Toggle Category
            document.querySelectorAll('.toggle-category').forEach(btn => {
                btn.addEventListener('click', function() {
                    const card = this.closest('.card');
                    const checks = card.querySelectorAll('.permission-check');
                    const allChecked = [...checks].every(cb => cb.checked);
                    checks.forEach(cb => { cb.checked = !allChecked; updateCardStyle(cb); });
                    updateCount();
                });
            });
        });
    </script>
    </x-slot:scripts>
</x-admin-layout>
