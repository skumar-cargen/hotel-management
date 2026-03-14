@php $isEdit = isset($category) && $category->exists; @endphp

<x-admin-layout :title="$isEdit ? 'Edit Blog Category' : 'Create Blog Category'" :pageTitle="$isEdit ? 'Edit: ' . $category->name : 'Create Blog Category'">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.blog-categories.index') }}">Blog Categories</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Create' }}</li>
    </x-slot:breadcrumb>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-info: #0891b2; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }

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
        .card { border: none; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04); }

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

        .save-bar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.25rem; background: #fff; border-radius: 16px;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,.06);
            border: none; margin-top: 1.5rem;
        }
    </style>

    <form action="{{ $isEdit ? route('admin.blog-categories.update', $category) : route('admin.blog-categories.store') }}" method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">
            {{-- Main --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                                <i class='bx bx-category'></i>
                            </div>
                            <div>
                                <h6>Category Details</h6>
                                <p>Name and description for this blog category</p>
                            </div>
                        </div>

                        <x-form.input name="name" label="Name" :value="old('name', $category->name ?? '')" required placeholder="e.g. Travel Tips" class="mb-3" />

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Brief description of this category (optional)">{{ old('description', $category->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Settings --}}
                <div class="card">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(0,200,83,0.1); color: var(--accent-success);">
                                <i class='bx bx-cog'></i>
                            </div>
                            <div>
                                <h6>Settings</h6>
                                <p>Status</p>
                            </div>
                        </div>

                        <div class="toggle-card">
                            <div class="toggle-label">
                                <div class="tl-icon"><i class='bx bx-power-off'></i></div>
                                <span>Active</span>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" role="switch"
                                       {{ old('is_active', $isEdit ? $category->is_active : true) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Info (edit only) --}}
                @if($isEdit)
                <div class="card mt-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="sh-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                                <i class='bx bx-bar-chart-alt-2'></i>
                            </div>
                            <div>
                                <h6>Quick Info</h6>
                            </div>
                        </div>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Slug</span>
                                <strong style="color: var(--text-secondary);">{{ $category->slug }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Posts</span>
                                <strong style="color: var(--text-secondary);">{{ $category->posts()->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f1f5f9;">
                                <span>Created</span>
                                <strong style="color: var(--text-secondary);">{{ $category->created_at->format('M j, Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span>Updated</span>
                                <strong style="color: var(--text-secondary);">{{ $category->updated_at->format('M j, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Save Bar --}}
        <div class="save-bar">
            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-light px-4">
                <i class='bx bx-arrow-back me-1'></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class='bx bx-save me-1'></i> {{ $isEdit ? 'Update Category' : 'Create Category' }}
            </button>
        </div>
    </form>
</x-admin-layout>
