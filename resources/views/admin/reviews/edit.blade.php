<x-admin-layout title="Edit Review" pageTitle="Edit Review">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </x-slot:breadcrumb>

    <div class="row g-4">
        {{-- Review Details (Read-Only) --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class='bx bx-info-circle me-1'></i> Review Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="140">Guest Name</th>
                            <td>{{ $review->guest_name }}</td>
                        </tr>
                        <tr>
                            <th>Guest Email</th>
                            <td>{{ $review->guest_email }}</td>
                        </tr>
                        <tr>
                            <th>Hotel</th>
                            <td>{{ $review->hotel->name }}</td>
                        </tr>
                        <tr>
                            <th>Rating</th>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class='bx {{ $i <= $review->rating ? 'bxs-star text-warning' : 'bx-star text-muted' }}'></i>
                                @endfor
                                <span class="ms-1 text-muted">({{ $review->rating }}/5)</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td>{{ $review->title ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Comment</th>
                            <td>{{ $review->comment }}</td>
                        </tr>
                        <tr>
                            <th>Submitted</th>
                            <td>{{ $review->created_at->format('M d, Y \a\t h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Moderation Form --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class='bx bx-edit me-1'></i> Moderation</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_approved" value="1" class="form-check-input" id="is_approved"
                                       {{ old('is_approved', $review->is_approved) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_approved">
                                    Approved
                                    <span class="badge bg-{{ old('is_approved', $review->is_approved) ? 'success' : 'warning' }} ms-1" id="status-badge">
                                        {{ old('is_approved', $review->is_approved) ? 'Approved' : 'Pending' }}
                                    </span>
                                </label>
                            </div>
                            @error('is_approved')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <x-form.textarea name="admin_reply" label="Admin Reply" :value="old('admin_reply', $review->admin_reply)" rows="6" placeholder="Write a response to this review..." />
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary"><i class='bx bx-save me-1'></i> Update Review</button>
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-light ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const checkbox = document.getElementById('is_approved');
                const badge = document.getElementById('status-badge');
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        badge.className = 'badge bg-success ms-1';
                        badge.textContent = 'Approved';
                    } else {
                        badge.className = 'badge bg-warning ms-1';
                        badge.textContent = 'Pending';
                    }
                });
            });
        </script>
    </x-slot:scripts>
</x-admin-layout>
