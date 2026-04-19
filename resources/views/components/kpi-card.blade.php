@props([
    'icon',
    'value',
    'label',
    'bgColor' => null,
    'iconColor' => null,
    'trend' => null,
    'trendDirection' => null,
    'sub' => null,
    'stripeGradient' => null,
])

<div class="kpi-card">
    @if($stripeGradient)
    <div class="kpi-stripe" style="background: {{ $stripeGradient }};"></div>
    @endif
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="kpi-icon" style="background:{{ $bgColor ?? '#f1f5f9' }};color:{{ $iconColor ?? '#64748b' }};">
                <i class='bx {{ $icon }}'></i>
            </div>
            @if(!is_null($trend))
            <span class="kpi-trend {{ $trendDirection === 'up' ? 'up' : ($trendDirection === 'down' ? 'down' : 'neutral') }}">
                @if($trendDirection === 'up')
                    <i class='bx bx-up-arrow-alt'></i>
                @elseif($trendDirection === 'down')
                    <i class='bx bx-down-arrow-alt'></i>
                @else
                    <i class='bx bx-trending-up'></i>
                @endif
                {{ $trend }}
            </span>
            @endif
        </div>
        <div class="kpi-value">{{ $value }}</div>
        <div class="kpi-label">{{ $label }}</div>
        @if($sub)
        <div class="kpi-sub">{{ $sub }}</div>
        @endif
    </div>
</div>
