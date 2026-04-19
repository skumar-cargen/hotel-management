@props([
    'status',
    'colorMap' => null,
])

@php
    $defaultColorMap = [
        'pending'   => ['bg' => '#fffbeb', 'color' => '#d97706'],
        'confirmed' => ['bg' => '#ecfdf5', 'color' => '#059669'],
        'paid'      => ['bg' => '#f0f9ff', 'color' => '#0891b2'],
        'cancelled' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
        'refunded'  => ['bg' => '#f1f5f9', 'color' => '#64748b'],
        'active'    => ['bg' => '#ecfdf5', 'color' => '#059669'],
        'inactive'  => ['bg' => '#fef2f2', 'color' => '#dc2626'],
        'draft'     => ['bg' => '#f1f5f9', 'color' => '#64748b'],
        'published' => ['bg' => '#ecfdf5', 'color' => '#059669'],
        'completed' => ['bg' => '#ecfdf5', 'color' => '#059669'],
        'processing'=> ['bg' => '#eff6ff', 'color' => '#3b82f6'],
        'failed'    => ['bg' => '#fef2f2', 'color' => '#dc2626'],
    ];

    $map = $colorMap ?? $defaultColorMap;
    $normalizedStatus = strtolower($status);
    $colors = $map[$normalizedStatus] ?? ['bg' => '#f1f5f9', 'color' => '#64748b'];
@endphp

<span {{ $attributes->merge(['class' => 'status-badge']) }} style="background:{{ $colors['bg'] }};color:{{ $colors['color'] }};font-size:0.68rem;font-weight:700;padding:0.25rem 0.6rem;border-radius:0.4rem;text-transform:uppercase;letter-spacing:0.03em;">
    {{ ucfirst($status) }}
</span>
