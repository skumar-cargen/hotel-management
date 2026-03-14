@php $isEdit = isset($user) && $user->exists; @endphp
<x-admin-layout :title="$isEdit ? 'Edit User' : 'Add User'" :pageTitle="$isEdit ? 'Edit User: ' . $user->name : 'Add User'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Add' }}</li>
    </x-slot:breadcrumb>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-info: #0891b2; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }

        /* ── Section Headers ──────────────────────────── */
        .section-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 1.25rem; padding-bottom: 0.75rem;
            border-bottom: 1.5px solid var(--border-light);
        }
        .section-header .sh-icon {
            width: 34px; height: 34px; border-radius: 0.55rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .section-header h6 { font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin: 0; }
        .section-header p { font-size: 0.75rem; color: var(--text-muted); margin: 0; }

        /* ── Premium Cards ────────────────────────────── */
        .card {
            border: none; border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        }

        /* ── Toggle Switches ──────────────────────────── */
        .toggle-card {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.85rem 1rem; border-radius: 0.65rem;
            border: 1.5px solid var(--border-light); background: #fff; transition: all 0.2s;
        }
        .toggle-card:has(input:checked) { border-color: var(--accent-success); background: rgba(0,200,83,0.04); }
        .toggle-card .toggle-label { display: flex; align-items: center; gap: 10px; }
        .toggle-card .toggle-label .tl-icon {
            width: 32px; height: 32px; border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; background: #f4f5f8; color: var(--text-muted); transition: all 0.2s;
        }
        .toggle-card:has(input:checked) .tl-icon { background: rgba(0,200,83,0.12); color: var(--accent-success); }
        .toggle-card .toggle-label span { font-size: 0.85rem; font-weight: 600; }

        /* ── Domain Chips ─────────────────────────────── */
        .domain-check-item {
            display: flex; align-items: center; gap: 10px;
            padding: 0.6rem 0.85rem; border-radius: 0.6rem;
            border: 1.5px solid var(--border-light); background: #fff;
            transition: all 0.2s; cursor: pointer;
        }
        .domain-check-item:hover { border-color: var(--accent-primary); background: #fafaff; }
        .domain-check-item:has(input:checked) {
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, rgba(102,126,234,0.06), rgba(139,92,246,0.06));
        }
        .domain-check-item input { margin: 0; flex-shrink: 0; width: 16px; height: 16px; }
        .domain-check-item .dci-name { font-size: 0.82rem; font-weight: 600; color: var(--text-primary); }
        .domain-check-item .dci-url { font-size: 0.68rem; color: var(--text-muted); }

        /* ── Password Hint ────────────────────────────── */
        .pw-hint {
            display: flex; align-items: center; gap: 6px;
            margin-top: 6px; font-size: 0.72rem; color: var(--text-muted);
        }
        .pw-hint i { font-size: 0.85rem; }

        /* ── User Avatar ──────────────────────────────── */
        .user-avatar-lg {
            width: 64px; height: 64px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 700; color: #fff;
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            flex-shrink: 0;
        }

        /* ── Quick Info Rows ──────────────────────────── */
        .qi-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.55rem 0; border-bottom: 1px solid #f1f5f9;
            font-size: 0.78rem;
        }
        .qi-row:last-child { border-bottom: none; }
        .qi-row .qi-label { color: var(--text-muted); }
        .qi-row .qi-value { font-weight: 600; color: var(--text-secondary); }

        /* ── Role Guide Items ─────────────────────────── */
        .role-guide-item {
            display: flex; align-items: flex-start; gap: 8px;
            padding: 0.45rem 0; font-size: 0.75rem; color: var(--text-muted); line-height: 1.5;
        }

        /* ── Save Bar ─────────────────────────────────── */
        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 16px;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,.06);
            border: none; margin-top: 1.5rem;
        }
    </style>

    <form action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
            {{-- ═══ Main Content ═══ --}}
            <div class="col-lg-8">
                {{-- Account Information --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(102,126,234,0.1); color: var(--accent-primary);">
                                <i class='bx bx-user'></i>
                            </div>
                            <div>
                                <h6>Account Information</h6>
                                <p>Name, email, and basic account details</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <x-form.input name="name" label="Full Name" :value="old('name', $isEdit ? $user->name : '')" required placeholder="e.g. John Doe" class="col-md-6" />
                            <x-form.input name="email" label="Email Address" type="email" :value="old('email', $isEdit ? $user->email : '')" required placeholder="e.g. john@example.com" class="col-md-6" />
                            <x-form.input name="phone" label="Phone" :value="old('phone', $isEdit ? $user->phone : '')" placeholder="+971 50 123 4567" class="col-md-6" />
                            <x-form.select name="role" label="Role" :options="$roles->pluck('name', 'name')->map(fn($v) => ucfirst($v))->toArray()" :selected="old('role', $isEdit ? $user->roles->first()?->name : '')" placeholder="Select Role" required class="col-md-6" id="roleSelect" />
                        </div>
                    </div>
                </div>

                {{-- Security --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(239,68,68,0.1); color: var(--accent-danger);">
                                <i class='bx bx-lock-alt'></i>
                            </div>
                            <div>
                                <h6>Security</h6>
                                <p>{{ $isEdit ? 'Leave blank to keep current password' : 'Set a secure password for this account' }}</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <x-form.input name="password" label="Password" type="password" :required="!$isEdit" minlength="8" placeholder="Minimum 8 characters" class="col-md-6" />
                            <x-form.input name="password_confirmation" label="Confirm Password" type="password" :required="!$isEdit" minlength="8" placeholder="Re-enter password" class="col-md-6" />
                        </div>
                        <div class="pw-hint">
                            <i class='bx bx-info-circle'></i>
                            <span>Use a mix of letters, numbers, and symbols for a strong password</span>
                        </div>
                    </div>
                </div>

                {{-- Domain Access --}}
                <div class="card mt-3" id="domainAssignmentCard">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(118,75,162,0.1); color: #764ba2;">
                                <i class='bx bx-globe'></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Domain Access</h6>
                                <p id="domainHelpText">Select which domains this user can manage</p>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.72rem; font-weight: 700;" id="domainCount">
                                {{ count($userDomains ?? []) }} selected
                            </span>
                        </div>

                        @if(isset($domains) && $domains->count() > 0)
                            <div class="row g-2" style="max-height: 320px; overflow-y: auto;">
                                @foreach($domains as $domain)
                                <div class="col-md-6">
                                    <label class="domain-check-item w-100">
                                        <input type="checkbox" name="domains[]" value="{{ $domain->id }}"
                                               class="form-check-input domain-check"
                                               {{ in_array($domain->id, old('domains', $userDomains ?? [])) ? 'checked' : '' }}>
                                        <div class="flex-grow-1">
                                            <div class="dci-name">{{ $domain->name }}</div>
                                            <div class="dci-url">{{ $domain->domain }}</div>
                                        </div>
                                        @if($domain->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.6rem;">Active</span>
                                        @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size: 0.6rem;">Inactive</span>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class='bx bx-globe' style="font-size: 2rem; color: var(--border-light);"></i>
                                <p class="mb-0 mt-1" style="font-size: 0.8rem; color: var(--text-muted);">No domains available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ═══ Sidebar ═══ --}}
            <div class="col-lg-4">
                {{-- Status --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                <i class='bx bx-cog'></i>
                            </div>
                            <div>
                                <h6>Status</h6>
                                <p>Control user access</p>
                            </div>
                        </div>

                        <div class="toggle-card">
                            <div class="toggle-label">
                                <div class="tl-icon"><i class='bx bx-power-off'></i></div>
                                <span>Active</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch"
                                       {{ old('is_active', $isEdit ? $user->is_active : true) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Profile Card (edit only) --}}
                @if($isEdit)
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                                <i class='bx bx-id-card'></i>
                            </div>
                            <div>
                                <h6>User Profile</h6>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom: 1px solid #f1f5f9;">
                            <div class="user-avatar-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">{{ $user->name }}</div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $user->email }}</div>
                                @if($user->roles->first())
                                <span class="badge bg-primary bg-opacity-10 text-primary mt-1" style="font-size: 0.68rem;">{{ $user->roles->first()->name }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="qi-row">
                            <span class="qi-label">Member Since</span>
                            <span class="qi-value">{{ $user->created_at?->format('M j, Y') }}</span>
                        </div>
                        <div class="qi-row">
                            <span class="qi-label">Last Login</span>
                            <span class="qi-value">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</span>
                        </div>
                        <div class="qi-row">
                            <span class="qi-label">Managed Domains</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.68rem;">{{ $user->domains->count() }}</span>
                        </div>
                        <div class="qi-row">
                            <span class="qi-label">Account Status</span>
                            @if($user->is_active)
                                <span class="badge bg-success" style="font-size: 0.68rem;">Active</span>
                            @else
                                <span class="badge bg-danger" style="font-size: 0.68rem;">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- Roles Guide --}}
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,176,255,0.1); color: var(--accent-info);">
                                <i class='bx bx-bulb'></i>
                            </div>
                            <div>
                                <h6>Roles Guide</h6>
                            </div>
                        </div>
                        <div class="role-guide-item">
                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.6rem; min-width: 78px; flex-shrink: 0;">Admin</span>
                            <span>Full access to all features and domains</span>
                        </div>
                        <div class="role-guide-item">
                            <span class="badge bg-info bg-opacity-10 text-info" style="font-size: 0.6rem; min-width: 78px; flex-shrink: 0;">Content</span>
                            <span>Manage hotels, locations, rooms, reviews</span>
                        </div>
                        <div class="role-guide-item">
                            <span class="badge bg-warning bg-opacity-10 text-warning" style="font-size: 0.6rem; min-width: 78px; flex-shrink: 0;">Pricing</span>
                            <span>Manage pricing rules and view bookings</span>
                        </div>
                        <div class="role-guide-item">
                            <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.6rem; min-width: 78px; flex-shrink: 0;">Domain Mgr</span>
                            <span>Manage assigned domains and their content</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update User' : 'Create User' }}
            </button>
        </div>
    </form>

    <x-slot:scripts>
    <script>
        $(function() {
            var $roleSelect = $('#roleSelect');
            var domainCard = document.getElementById('domainAssignmentCard');
            var domainHelpText = document.getElementById('domainHelpText');

            function updateDomainCount() {
                var count = document.querySelectorAll('.domain-check:checked').length;
                var el = document.getElementById('domainCount');
                if (el) el.textContent = count + ' selected';
            }

            function toggleDomainVisibility() {
                if (!$roleSelect.length || !domainCard) return;
                var role = $roleSelect.val();
                if (role === 'Admin') {
                    domainCard.style.opacity = '0.5';
                    domainCard.style.pointerEvents = 'none';
                    if (domainHelpText) domainHelpText.textContent = 'Admin role has access to all domains automatically';
                } else {
                    domainCard.style.opacity = '1';
                    domainCard.style.pointerEvents = 'auto';
                    if (domainHelpText) domainHelpText.textContent = 'Select which domains this user can manage';
                }
            }

            // Select2 fires jQuery change event, not native
            $roleSelect.on('change', toggleDomainVisibility);
            toggleDomainVisibility();

            $(document).on('change', '.domain-check', updateDomainCount);

            // Phone: allow only digits + - ( ) space
            var phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('keydown', function(e) {
                    if ([8, 9, 13, 27, 46, 35, 36, 37, 38, 39, 40].includes(e.keyCode)) return;
                    if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].includes(e.keyCode)) return;
                    var allowed = '0123456789+-() ';
                    if (!allowed.includes(e.key)) e.preventDefault();
                });
                phoneInput.addEventListener('paste', function(e) {
                    var self = this;
                    setTimeout(function() { self.value = self.value.replace(/[^0-9+\-\s()]/g, ''); }, 0);
                });
            }
        });
    </script>
    </x-slot:scripts>
</x-admin-layout>
