<x-admin-layout title="Email Configuration" pageTitle="Email Configuration">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item active">Email Configuration</li>
    </x-slot:breadcrumb>

    <style>
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
        .save-bar {
            display: flex; align-items: center; justify-content: flex-end;
            padding: 1rem 1.25rem; background: #fff; border-radius: 0.85rem;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
            border: 1.5px solid var(--border-light); margin-top: 1.5rem;
        }
    </style>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            {{-- Notifications --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(245,158,11,0.1); color: var(--accent-warning);">
                                <i class='bx bx-bell'></i>
                            </div>
                            <div>
                                <h6>Notifications</h6>
                                <p>Where system alerts (e.g. new customer registrations) are delivered</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <x-form.input
                                name="notification_email"
                                label="Admin Notification Email"
                                type="email"
                                :value="old('notification_email', $settings['notification_email'])"
                                required
                                placeholder="e.g. info@southtravels.com"
                                help="New customer registration alerts will be sent to this address."
                                class="col-md-8"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="save-bar">
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> Save Settings
            </button>
        </div>
    </form>
</x-admin-layout>
