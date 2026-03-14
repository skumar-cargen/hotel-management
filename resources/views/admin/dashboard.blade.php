<x-admin-layout title="Dashboard" pageTitle="">
    <x-slot:styles>
    <style>
        /* ── Dashboard Variables ─────────────────────────── */
        :root {
            --dash-primary: #1e40af;
            --dash-primary-light: #3b82f6;
            --dash-dark: #0f172a;
            --dash-success: #059669;
            --dash-warning: #d97706;
            --dash-danger: #dc2626;
            --dash-info: #0891b2;
            --dash-muted: #64748b;
            --dash-border: #e2e8f0;
            --dash-bg: #f8fafc;
            --dash-card-radius: 1rem;
            --dash-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
            --dash-shadow-lg: 0 12px 32px rgba(0,0,0,0.1);
        }

        /* ── Welcome Banner ──────────────────────────────── */
        .dash-welcome {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 30%, #1e40af 60%, #6366f1 100%);
            border-radius: 1.25rem;
            color: #fff;
            padding: 2.25rem 2.75rem;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.75rem;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .dash-welcome::before {
            content: '';
            position: absolute;
            top: -60%;
            right: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, transparent 60%);
        }
        .dash-welcome::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
        }
        .dash-welcome h2 { font-size: 1.65rem; font-weight: 800; margin-bottom: 0.3rem; position: relative; z-index: 1; letter-spacing: -0.02em; }
        .dash-welcome p { color: rgba(255,255,255,0.6); margin-bottom: 0; font-size: 0.9rem; position: relative; z-index: 1; }
        .dash-welcome .date-badge {
            display: inline-flex; align-items: center; gap: 0.45rem;
            background: rgba(255,255,255,0.12); padding: 0.4rem 1rem;
            border-radius: 2rem; font-size: 0.78rem; color: rgba(255,255,255,0.85);
            position: relative; z-index: 1; margin-bottom: 0.85rem;
            backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.08);
        }
        .dash-welcome .live-dot {
            width: 8px; height: 8px; background: #22c55e; border-radius: 50%;
            display: inline-block; animation: pulse-dot 2s ease-in-out infinite;
            box-shadow: 0 0 8px rgba(34,197,94,0.5);
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        .welcome-stats {
            display: flex; gap: 2.5rem; margin-top: 1.5rem;
            position: relative; z-index: 1;
        }
        .welcome-stats .ws-item {
            text-align: center;
            background: rgba(255,255,255,0.08);
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.06);
        }
        .welcome-stats .ws-value { font-size: 1.5rem; font-weight: 800; line-height: 1.2; }
        .welcome-stats .ws-label { font-size: 0.68rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.08em; margin-top: 0.15rem; }

        /* ── Pending Alert Bar ────────────────────────────── */
        .pending-bar {
            display: flex; flex-wrap: wrap; gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .pending-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.6rem 1.15rem; border-radius: 0.75rem;
            font-size: 0.82rem; font-weight: 600;
            text-decoration: none; transition: all 0.2s;
            border: 1px solid transparent;
        }
        .pending-badge:hover { transform: translateY(-2px); box-shadow: var(--dash-shadow-lg); }

        /* ── Section Header ───────────────────────────────── */
        .section-header {
            display: flex; align-items: center; gap: 0.65rem;
            margin-bottom: 1.25rem; padding-bottom: 0.85rem;
            border-bottom: 2px solid var(--dash-border);
        }
        .section-header h5 { font-size: 1.05rem; font-weight: 800; color: var(--dash-dark); margin: 0; letter-spacing: -0.01em; }
        .section-header .section-icon {
            width: 36px; height: 36px; border-radius: 0.625rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }

        /* ── KPI Cards ────────────────────────────────────── */
        .kpi-card {
            border: none; border-radius: 1.1rem;
            box-shadow: var(--dash-shadow);
            transition: all 0.3s ease; overflow: hidden;
            position: relative; background: #fff;
            border: 1px solid rgba(0,0,0,0.03);
        }
        .kpi-card:hover { transform: translateY(-4px); box-shadow: var(--dash-shadow-lg); }
        .kpi-card .kpi-stripe {
            position: absolute; top: 0; left: 0; right: 0;
            height: 4px;
        }
        .kpi-card .card-body { padding: 1.5rem 1.65rem; }
        .kpi-card .kpi-icon {
            width: 52px; height: 52px; border-radius: 0.85rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .kpi-card .kpi-value { font-size: 1.75rem; font-weight: 800; color: var(--dash-dark); line-height: 1.2; letter-spacing: -0.02em; }
        .kpi-card .kpi-label { color: var(--dash-muted); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 0.25rem; }
        .kpi-card .kpi-sub { font-size: 0.74rem; color: #94a3b8; margin-top: 0.25rem; }
        .kpi-trend { font-size: 0.72rem; font-weight: 700; display: inline-flex; align-items: center; gap: 2px; padding: 0.2rem 0.5rem; border-radius: 0.4rem; }
        .kpi-trend.up { color: #059669; background: #ecfdf5; }
        .kpi-trend.down { color: #dc2626; background: #fef2f2; }
        .kpi-trend.neutral { color: #64748b; background: #f1f5f9; }

        /* ── Chart Cards ──────────────────────────────────── */
        .chart-card {
            border: none; border-radius: 1.1rem;
            box-shadow: var(--dash-shadow); background: #fff;
            border: 1px solid rgba(0,0,0,0.03);
            transition: box-shadow 0.25s;
        }
        .chart-card:hover { box-shadow: var(--dash-shadow-lg); }
        .chart-card .card-header {
            background: none; border-bottom: 1px solid #f1f5f9;
            padding: 1.15rem 1.5rem;
        }
        .chart-card .card-header h6 { font-size: 0.92rem; font-weight: 700; color: var(--dash-dark); margin: 0; }
        .chart-card .chart-area { position: relative; padding: 1.25rem 1.5rem; }

        /* ── Data Tables ──────────────────────────────────── */
        .data-card {
            border: none; border-radius: 1.1rem;
            box-shadow: var(--dash-shadow); background: #fff;
            border: 1px solid rgba(0,0,0,0.03);
            transition: box-shadow 0.25s;
        }
        .data-card:hover { box-shadow: var(--dash-shadow-lg); }
        .data-card .card-header {
            background: none; border-bottom: 1px solid #f1f5f9;
            padding: 1.15rem 1.5rem;
        }
        .data-card .card-body { padding: 1.25rem 1.5rem; }
        .data-card .table { margin-bottom: 0; }
        .data-card .table thead th {
            font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em;
            color: var(--dash-muted); font-weight: 700; border-bottom: 2px solid #f1f5f9;
            padding: 0.85rem 1.1rem; white-space: nowrap;
        }
        .data-card .table tbody td {
            font-size: 0.835rem; padding: 0.85rem 1.1rem; vertical-align: middle;
            border-bottom: 1px solid #f8fafc;
        }
        .data-card .table tbody tr:hover { background: #f8fafc; }

        /* ── Rank List ────────────────────────────────────── */
        .rank-item {
            display: flex; align-items: center; gap: 0.85rem;
            padding: 0.8rem 0; border-bottom: 1px solid #f1f5f9;
        }
        .rank-item:last-child { border-bottom: none; }
        .rank-num {
            width: 32px; height: 32px; border-radius: 0.5rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem; font-weight: 800; flex-shrink: 0;
        }
        .rank-num.gold { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; box-shadow: 0 2px 6px rgba(251,191,36,0.3); }
        .rank-num.silver { background: linear-gradient(135deg, #f1f5f9, #e2e8f0); color: #475569; }
        .rank-num.bronze { background: linear-gradient(135deg, #fed7aa, #fdba74); color: #9a3412; box-shadow: 0 2px 6px rgba(251,146,60,0.25); }
        .rank-num.dim { background: #f8fafc; color: #94a3b8; }

        /* ── Review Items ─────────────────────────────────── */
        .review-item { border-bottom: 1px solid #f1f5f9; padding: 0.85rem 0; }
        .review-item:last-child { border-bottom: none; }

        /* ── Status Badge ─────────────────────────────────── */
        .status-badge {
            font-size: 0.68rem; font-weight: 700; padding: 0.25rem 0.6rem;
            border-radius: 0.4rem; text-transform: uppercase; letter-spacing: 0.03em;
        }

        /* ── Quick Action Cards ───────────────────────────── */
        .action-card {
            border: 2px dashed #e2e8f0; border-radius: 0.875rem;
            transition: all 0.25s ease; background: #fff;
        }
        .action-card:hover { border-color: var(--dash-primary-light); background: #eff6ff; transform: translateY(-3px); box-shadow: 0 4px 12px rgba(59,130,246,0.12); }
        .action-icon { width: 44px; height: 44px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }

        /* ── Progress Bar ─────────────────────────────────── */
        .mini-progress {
            height: 7px; border-radius: 4px; background: #f1f5f9; overflow: hidden;
        }
        .mini-progress .bar { height: 100%; border-radius: 4px; transition: width 0.6s ease; }

        /* ── Legend Dot ────────────────────────────────────── */
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; flex-shrink: 0; }

        /* ── Responsive ───────────────────────────────────── */
        @media (max-width: 768px) {
            .dash-welcome { padding: 1.5rem; }
            .dash-welcome h2 { font-size: 1.25rem; }
            .welcome-stats { gap: 0.75rem; flex-wrap: wrap; }
            .welcome-stats .ws-item { padding: 0.5rem 0.75rem; }
            .kpi-card .card-body { padding: 1.25rem; }
            .kpi-card .kpi-value { font-size: 1.3rem; }
        }
    </style>
    </x-slot:styles>

    {{-- ═══════════ WELCOME BANNER ═══════════ --}}
    <div class="dash-welcome d-flex justify-content-between align-items-center">
        <div style="position:relative;z-index:1;">
            <h2>Welcome back, {{ Auth::user()->name }}!</h2>
            <p>Here's your business performance overview for today.</p>
        </div>
        <div class="date-badge" style="margin-bottom:0;">
            <span class="live-dot"></span>
            <i class='bx bx-calendar'></i> {{ now()->format('l, F d, Y') }}
        </div>
    </div>

    {{-- ═══════════ PENDING ACTIONS ═══════════ --}}
    @if($pendingBookings > 0 || $pendingReviews > 0)
    <div class="pending-bar">
        @if($pendingBookings > 0)
        <a href="{{ route('admin.bookings.index') }}" class="pending-badge bg-warning bg-opacity-10 text-warning">
            <i class='bx bx-time-five'></i>
            {{ $pendingBookings }} pending booking{{ $pendingBookings > 1 ? 's' : '' }} need attention
        </a>
        @endif
        @if($pendingReviews > 0)
        <a href="{{ route('admin.reviews.index') }}" class="pending-badge bg-info bg-opacity-10 text-info">
            <i class='bx bx-message-dots'></i>
            {{ $pendingReviews }} review{{ $pendingReviews > 1 ? 's' : '' }} awaiting approval
        </a>
        @endif
    </div>
    @endif

    {{-- ═══════════ PRIMARY KPI CARDS ═══════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="kpi-stripe" style="background: linear-gradient(90deg, #3b82f6, #60a5fa);"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#eff6ff;color:#3b82f6;"><i class='bx bxs-wallet'></i></div>
                        <span class="kpi-trend {{ $revenueGrowth >= 0 ? 'up' : 'down' }}">
                            <i class='bx bx-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}-arrow-alt'></i> {{ abs($revenueGrowth) }}%
                        </span>
                    </div>
                    <div class="kpi-value">AED {{ number_format($totalRevenue) }}</div>
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-sub">AED {{ number_format($monthRevenue) }} this month</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="kpi-stripe" style="background: linear-gradient(90deg, #059669, #34d399);"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#ecfdf5;color:#059669;"><i class='bx bxs-calendar-check'></i></div>
                        <span class="kpi-trend {{ $bookingGrowth >= 0 ? 'up' : 'down' }}">
                            <i class='bx bx-{{ $bookingGrowth >= 0 ? 'up' : 'down' }}-arrow-alt'></i> {{ abs($bookingGrowth) }}%
                        </span>
                    </div>
                    <div class="kpi-value">{{ number_format($totalBookings) }}</div>
                    <div class="kpi-label">Total Bookings</div>
                    <div class="kpi-sub">{{ $monthBookings }} this month &middot; {{ $weekBookings }} this week</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="kpi-stripe" style="background: linear-gradient(90deg, #d97706, #fbbf24);"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#fffbeb;color:#d97706;"><i class='bx bxs-receipt'></i></div>
                        <span class="kpi-trend neutral"><i class='bx bx-trending-up'></i> avg</span>
                    </div>
                    <div class="kpi-value">AED {{ number_format($avgBookingValue) }}</div>
                    <div class="kpi-label">Avg Booking Value</div>
                    <div class="kpi-sub">{{ round($avgNightsStay, 1) }} nights avg stay</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card">
                <div class="kpi-stripe" style="background: linear-gradient(90deg, #dc2626, #f87171);"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="kpi-icon" style="background:#fef2f2;color:#dc2626;"><i class='bx bx-x-circle'></i></div>
                        <span class="kpi-trend {{ $cancellationRate > 10 ? 'down' : 'up' }}">{{ $cancellationRate }}%</span>
                    </div>
                    <div class="kpi-value">{{ $cancellationRate }}%</div>
                    <div class="kpi-label">Cancellation Rate</div>
                    <div class="kpi-sub">{{ $pendingBookings }} bookings pending</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ SECONDARY KPI ROW ═══════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="kpi-card">
                <div class="card-body py-4 text-center">
                    <div class="kpi-icon mx-auto mb-2" style="background:#eff6ff;color:#3b82f6;width:46px;height:46px;font-size:1.2rem;">
                        <i class='bx bxs-building-house'></i>
                    </div>
                    <div class="fw-bold" style="font-size:1.35rem;color:var(--dash-dark);">{{ $totalHotels }}</div>
                    <div style="font-size:0.72rem;color:var(--dash-muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Hotels</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="kpi-card">
                <div class="card-body py-4 text-center">
                    <div class="kpi-icon mx-auto mb-2" style="background:#f0fdf4;color:#059669;width:46px;height:46px;font-size:1.2rem;">
                        <i class='bx bx-globe'></i>
                    </div>
                    <div class="fw-bold" style="font-size:1.35rem;color:var(--dash-dark);">{{ $activeDomains }}</div>
                    <div style="font-size:0.72rem;color:var(--dash-muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Domains</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="kpi-card">
                <div class="card-body py-4 text-center">
                    <div class="kpi-icon mx-auto mb-2" style="background:#fdf4ff;color:#a855f7;width:46px;height:46px;font-size:1.2rem;">
                        <i class='bx bx-map-pin'></i>
                    </div>
                    <div class="fw-bold" style="font-size:1.35rem;color:var(--dash-dark);">{{ $totalLocations }}</div>
                    <div style="font-size:0.72rem;color:var(--dash-muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Locations</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="kpi-card">
                <div class="card-body py-4 text-center">
                    <div class="kpi-icon mx-auto mb-2" style="background:#fce7f3;color:#db2777;width:46px;height:46px;font-size:1.2rem;">
                        <i class='bx bx-bed'></i>
                    </div>
                    <div class="fw-bold" style="font-size:1.35rem;color:var(--dash-dark);">{{ $totalRoomTypes }}</div>
                    <div style="font-size:0.72rem;color:var(--dash-muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Room Types</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="kpi-card">
                <div class="card-body py-4 text-center">
                    <div class="kpi-icon mx-auto mb-2" style="background:#fffbeb;color:#f59e0b;width:46px;height:46px;font-size:1.2rem;">
                        <i class='bx bx-star'></i>
                    </div>
                    <div class="fw-bold" style="font-size:1.35rem;color:var(--dash-dark);">{{ $totalReviews }}</div>
                    <div style="font-size:0.72rem;color:var(--dash-muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Reviews</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="kpi-card">
                <div class="card-body py-4 text-center">
                    <div class="kpi-icon mx-auto mb-2" style="background:#f0f9ff;color:#0891b2;width:46px;height:46px;font-size:1.2rem;">
                        <i class='bx bx-group'></i>
                    </div>
                    <div class="fw-bold" style="font-size:1.35rem;color:var(--dash-dark);">{{ $totalUsers }}</div>
                    <div style="font-size:0.72rem;color:var(--dash-muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Users</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ REVENUE ANALYTICS SECTION ═══════════ --}}
    <div class="section-header">
        <div class="section-icon" style="background:#eff6ff;color:#3b82f6;"><i class='bx bx-line-chart'></i></div>
        <h5>Revenue Analytics</h5>
    </div>

    <div class="row g-4 mb-4">
        {{-- Revenue Trend (30 Days) --}}
        <div class="col-lg-8">
            <div class="chart-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6><i class='bx bx-trending-up me-1 text-primary'></i> Revenue Trend — Last 30 Days</h6>
                    <span class="text-muted" style="font-size:0.78rem;">AED {{ number_format($monthRevenue, 2) }} this month</span>
                </div>
                <div class="chart-area" style="height:310px;">
                    <canvas id="revenueChart30"></canvas>
                </div>
            </div>
        </div>

        {{-- Monthly Revenue (12 Months) --}}
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="card-header">
                    <h6><i class='bx bx-bar-chart-alt-2 me-1 text-success'></i> Monthly Revenue — 12 Months</h6>
                </div>
                <div class="chart-area" style="height:310px;">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ BOOKING ANALYTICS SECTION ═══════════ --}}
    <div class="section-header">
        <div class="section-icon" style="background:#ecfdf5;color:#059669;"><i class='bx bx-calendar-check'></i></div>
        <h5>Booking Analytics</h5>
    </div>

    {{-- Bookings Trend (30 Days) — full width --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="card-header">
                    <h6><i class='bx bx-bar-chart me-1 text-info'></i> Bookings — Last 30 Days</h6>
                </div>
                <div class="chart-area" style="height:310px;">
                    <canvas id="bookingsChart30"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Status & Day of Week --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="card-header">
                    <h6><i class='bx bx-pie-chart-alt-2 me-1 text-warning'></i> Booking Status</h6>
                </div>
                <div class="chart-area d-flex align-items-center justify-content-center" style="height:300px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="card-header">
                    <h6><i class='bx bx-calendar-week me-1 text-danger'></i> Bookings by Day</h6>
                </div>
                <div class="chart-area" style="height:300px;">
                    <canvas id="dayOfWeekChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ HOTEL EARNINGS SECTION ═══════════ --}}
    <div class="section-header">
        <div class="section-icon" style="background:#fffbeb;color:#d97706;"><i class='bx bx-dollar-circle'></i></div>
        <h5>Hotel Earnings Report</h5>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="data-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class='bx bx-building-house me-1 text-primary'></i> Top Hotels by Revenue</h6>
                    <a href="{{ route('admin.hotels.index') }}" class="btn btn-sm btn-outline-primary">View All Hotels</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Hotel</th>
                                <th>Location</th>
                                <th class="text-center">Stars</th>
                                <th class="text-center">Rating</th>
                                <th class="text-center">Bookings</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Avg Value</th>
                                <th class="text-center">Share</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $maxHotelRev = max($hotelEarnings->max('total_revenue'), 1); @endphp
                            @forelse($hotelEarnings as $index => $hotel)
                            <tr>
                                <td>
                                    <div class="rank-num {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'dim')) }}" style="width:28px;height:28px;font-size:0.7rem;">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="fw-semibold text-decoration-none text-dark">
                                        {{ Str::limit($hotel->name, 30) }}
                                    </a>
                                </td>
                                <td class="text-muted">{{ $hotel->location?->name ?? '—' }}</td>
                                <td class="text-center">
                                    @for($i = 1; $i <= ($hotel->star_rating ?? 0); $i++)
                                    <i class='bx bxs-star text-warning' style="font-size:0.65rem;"></i>
                                    @endfor
                                </td>
                                <td class="text-center">
                                    @if($hotel->avg_rating > 0)
                                    <span class="badge" style="background:#fffbeb;color:#d97706;font-size:0.72rem;">
                                        <i class='bx bxs-star' style="font-size:0.6rem;"></i> {{ number_format($hotel->avg_rating, 1) }}
                                    </span>
                                    @else
                                    <span class="text-muted" style="font-size:0.7rem;">—</span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">{{ number_format($hotel->total_bookings) }}</td>
                                <td class="text-end fw-bold" style="color:var(--dash-primary);">AED {{ number_format($hotel->total_revenue) }}</td>
                                <td class="text-end">AED {{ number_format($hotel->avg_booking_value) }}</td>
                                <td style="width:120px;">
                                    <div class="mini-progress">
                                        <div class="bar" style="width:{{ round(($hotel->total_revenue / $maxHotelRev) * 100) }}%;background:linear-gradient(90deg,#3b82f6,#60a5fa);"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($hotel->is_active)
                                    <span class="status-badge" style="background:#ecfdf5;color:#059669;">Active</span>
                                    @else
                                    <span class="status-badge" style="background:#fef2f2;color:#dc2626;">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class='bx bx-building-house fs-1 d-block mb-2 opacity-25'></i>
                                    No hotel data available yet. Add hotels to see earnings.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ DOMAIN & LOCATION PERFORMANCE ═══════════ --}}
    <div class="section-header">
        <div class="section-icon" style="background:#f0fdf4;color:#059669;"><i class='bx bx-globe'></i></div>
        <h5>Domain & Location Performance</h5>
    </div>

    <div class="row g-4 mb-4">
        {{-- Revenue by Domain Chart --}}
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="card-header">
                    <h6><i class='bx bx-globe me-1 text-primary'></i> Revenue by Domain</h6>
                </div>
                <div class="chart-area" style="height:300px;">
                    <canvas id="domainRevenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Revenue by Location Chart --}}
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="card-header">
                    <h6><i class='bx bx-map me-1 text-success'></i> Revenue by Location</h6>
                </div>
                <div class="chart-area" style="height:300px;">
                    <canvas id="locationRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Domain Performance Table --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="data-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class='bx bx-globe me-1 text-info'></i> Domain Performance</h6>
                    <a href="{{ route('admin.domains.index') }}" class="btn btn-sm btn-outline-primary">Manage Domains</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Domain</th>
                                <th class="text-center">Hotels</th>
                                <th class="text-center">Bookings</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($domainPerformance as $domain)
                            <tr>
                                <td>
                                    <div class="fw-semibold" style="font-size:0.85rem;">{{ $domain->name }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">{{ $domain->domain }}</div>
                                </td>
                                <td class="text-center">{{ $domain->hotels_count }}</td>
                                <td class="text-center">{{ $domain->bookings_count }}</td>
                                <td class="text-end fw-bold" style="color:var(--dash-primary);">AED {{ number_format($domain->domain_revenue) }}</td>
                                <td class="text-center">
                                    <span class="status-badge" style="background:#ecfdf5;color:#059669;">Active</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No active domains</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Revenue by Room Type --}}
        <div class="col-lg-5">
            <div class="chart-card h-100">
                <div class="card-header">
                    <h6><i class='bx bx-bed me-1 text-danger'></i> Revenue by Room Type</h6>
                </div>
                <div class="chart-area" style="height:300px;">
                    <canvas id="roomTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ GUEST INSIGHTS SECTION ═══════════ --}}
    <div class="section-header">
        <div class="section-icon" style="background:#fdf4ff;color:#a855f7;"><i class='bx bx-user-circle'></i></div>
        <h5>Guest Insights</h5>
    </div>

    <div class="row g-4 mb-4">
        {{-- Guest Nationality --}}
        <div class="col-lg-5">
            <div class="chart-card h-100">
                <div class="card-header">
                    <h6><i class='bx bx-world me-1 text-info'></i> Guest Nationalities</h6>
                </div>
                <div class="chart-area" style="height:300px;">
                    <canvas id="nationalityChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Hourly Booking Pattern --}}
        <div class="col-lg-4">
            <div class="chart-card h-100">
                <div class="card-header">
                    <h6><i class='bx bx-time me-1 text-warning'></i> Booking Hours (30 Days)</h6>
                </div>
                <div class="chart-area" style="height:300px;">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Payment Stats --}}
        <div class="col-lg-3">
            <div class="data-card h-100">
                <div class="card-header">
                    <h6 class="fw-bold mb-0"><i class='bx bx-credit-card me-1 text-success'></i> Payment Summary</h6>
                </div>
                <div class="card-body">
                    @php
                        $paymentColorMap = [
                            'completed' => ['bg' => '#ecfdf5', 'color' => '#059669', 'icon' => 'bx-check-circle'],
                            'pending' => ['bg' => '#fffbeb', 'color' => '#d97706', 'icon' => 'bx-time-five'],
                            'processing' => ['bg' => '#eff6ff', 'color' => '#3b82f6', 'icon' => 'bx-loader-alt'],
                            'failed' => ['bg' => '#fef2f2', 'color' => '#dc2626', 'icon' => 'bx-x-circle'],
                            'refunded' => ['bg' => '#f1f5f9', 'color' => '#64748b', 'icon' => 'bx-undo'],
                        ];
                    @endphp
                    @forelse($paymentStats as $status => $stat)
                    @php $pc = $paymentColorMap[$status] ?? ['bg' => '#f1f5f9', 'color' => '#64748b', 'icon' => 'bx-circle']; @endphp
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color:#f1f5f9 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;border-radius:0.5rem;background:{{ $pc['bg'] }};color:{{ $pc['color'] }};display:flex;align-items:center;justify-content:center;font-size:0.9rem;">
                                <i class='bx {{ $pc['icon'] }}'></i>
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:0.8rem;">{{ ucfirst($status) }}</div>
                                <div style="font-size:0.68rem;color:var(--dash-muted);">{{ $stat->count }} transactions</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="font-size:0.82rem;color:{{ $pc['color'] }};">AED {{ number_format($stat->total) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class='bx bx-credit-card fs-1 d-block mb-2 opacity-25'></i>
                        No payment data yet
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ TOP PERFORMERS & ACTIVITY ═══════════ --}}
    <div class="section-header">
        <div class="section-icon" style="background:#fce7f3;color:#db2777;"><i class='bx bx-trophy'></i></div>
        <h5>Top Performers & Recent Activity</h5>
    </div>

    <div class="row g-4 mb-4">
        {{-- Top Hotels Ranking --}}
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="card-header">
                    <h6 class="fw-bold mb-0"><i class='bx bx-trophy me-1 text-warning'></i> Top Hotels by Bookings</h6>
                </div>
                <div class="card-body">
                    @forelse($topHotels as $index => $hotel)
                    <div class="rank-item">
                        <div class="rank-num {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'dim')) }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:0.85rem;">{{ Str::limit($hotel->name, 24) }}</div>
                            <div class="text-muted" style="font-size:0.68rem;">
                                @for($i = 1; $i <= ($hotel->star_rating ?? 0); $i++)
                                <i class='bx bxs-star text-warning' style="font-size:0.6rem;"></i>
                                @endfor
                            </div>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:0.7rem;">{{ $hotel->bookings_count }}</span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class='bx bx-hotel fs-1 d-block mb-2 opacity-25'></i>No hotels yet
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Top Locations Ranking --}}
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="card-header">
                    <h6 class="fw-bold mb-0"><i class='bx bx-map me-1 text-danger'></i> Top Locations</h6>
                </div>
                <div class="card-body">
                    @forelse($topLocations as $index => $location)
                    <div class="rank-item">
                        <div class="rank-num {{ $index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'dim')) }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:0.85rem;">{{ $location->name }}</div>
                            <div class="text-muted" style="font-size:0.68rem;">{{ $location->city }}</div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.7rem;">{{ $location->hotels_count }} hotels</span>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class='bx bx-map-pin fs-1 d-block mb-2 opacity-25'></i>No locations yet
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="card-header">
                    <h6 class="fw-bold mb-0"><i class='bx bx-rocket me-1 text-info'></i> Quick Actions</h6>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('admin.hotels.create') }}" class="action-card d-flex align-items-center gap-3 p-3 text-decoration-none text-dark">
                        <div class="action-icon bg-primary bg-opacity-10 text-primary"><i class='bx bx-plus'></i></div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.85rem;">Add New Hotel</div>
                            <div class="text-muted" style="font-size:0.68rem;">List a new property</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.locations.create') }}" class="action-card d-flex align-items-center gap-3 p-3 text-decoration-none text-dark">
                        <div class="action-icon bg-success bg-opacity-10 text-success"><i class='bx bx-map-pin'></i></div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.85rem;">Add Location</div>
                            <div class="text-muted" style="font-size:0.68rem;">New area or district</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.domains.create') }}" class="action-card d-flex align-items-center gap-3 p-3 text-decoration-none text-dark">
                        <div class="action-icon bg-warning bg-opacity-10 text-warning"><i class='bx bx-globe'></i></div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.85rem;">Add Domain</div>
                            <div class="text-muted" style="font-size:0.68rem;">Setup a new website</div>
                        </div>
                    </a>
                    <a href="{{ route('admin.bookings.index') }}" class="action-card d-flex align-items-center gap-3 p-3 text-decoration-none text-dark">
                        <div class="action-icon bg-info bg-opacity-10 text-info"><i class='bx bx-list-check'></i></div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.85rem;">Manage Bookings</div>
                            <div class="text-muted" style="font-size:0.68rem;">View & process bookings</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════ RECENT ACTIVITY ═══════════ --}}
    <div class="section-header">
        <div class="section-icon" style="background:#f0f9ff;color:#0891b2;"><i class='bx bx-history'></i></div>
        <h5>Recent Activity</h5>
    </div>

    <div class="row g-4 mb-4">
        {{-- Recent Bookings Table --}}
        <div class="col-lg-8">
            <div class="data-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class='bx bx-receipt me-1 text-primary'></i> Latest Bookings</h6>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Guest</th>
                                <th>Hotel</th>
                                <th>Domain</th>
                                <th>Check-in</th>
                                <th class="text-end">Amount</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-semibold text-decoration-none" style="color:var(--dash-primary);">
                                        #{{ $booking->reference_number }}
                                    </a>
                                </td>
                                <td>{{ $booking->guest_first_name }} {{ Str::limit($booking->guest_last_name, 1, '.') }}</td>
                                <td>{{ Str::limit($booking->hotel?->name ?? '—', 18) }}</td>
                                <td><span class="text-muted" style="font-size:0.78rem;">{{ $booking->domain?->name ?? '—' }}</span></td>
                                <td>{{ $booking->check_in_date?->format('M d, Y') }}</td>
                                <td class="text-end fw-bold">AED {{ number_format($booking->total_amount, 0) }}</td>
                                <td class="text-center">
                                    @php
                                        $sc = [
                                            'pending' => ['bg' => '#fffbeb', 'color' => '#d97706'],
                                            'paid' => ['bg' => '#f0f9ff', 'color' => '#0891b2'],
                                            'confirmed' => ['bg' => '#ecfdf5', 'color' => '#059669'],
                                            'cancelled' => ['bg' => '#fef2f2', 'color' => '#dc2626'],
                                            'refunded' => ['bg' => '#f1f5f9', 'color' => '#64748b'],
                                        ];
                                        $st = $sc[$booking->status] ?? ['bg' => '#f1f5f9', 'color' => '#64748b'];
                                    @endphp
                                    <span class="status-badge" style="background:{{ $st['bg'] }};color:{{ $st['color'] }};">{{ ucfirst($booking->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No bookings yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Reviews --}}
        <div class="col-lg-4">
            <div class="data-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class='bx bx-message-square-dots me-1 text-warning'></i> Latest Reviews</h6>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @forelse($recentReviews as $review)
                    <div class="review-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold" style="font-size:0.82rem;">{{ $review->guest_name }}</span>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                <i class='bx {{ $i <= $review->rating ? 'bxs-star text-warning' : 'bx-star text-muted' }}' style="font-size:0.68rem;"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ Str::limit($review->comment, 70) }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span style="font-size:0.68rem;color:#94a3b8;">{{ $review->hotel?->name }}</span>
                            @if(!$review->is_approved)
                            <span class="status-badge" style="background:#fffbeb;color:#d97706;">Pending</span>
                            @else
                            <span class="status-badge" style="background:#ecfdf5;color:#059669;">Approved</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class='bx bx-message-dots fs-1 d-block mb-2 opacity-25'></i>No reviews yet
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <x-slot:scripts>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const COLORS = {
                primary: '#3b82f6', primaryLight: 'rgba(59,130,246,0.12)',
                success: '#059669', successLight: 'rgba(5,150,105,0.12)',
                warning: '#d97706', warningLight: 'rgba(217,119,6,0.12)',
                danger: '#dc2626', dangerLight: 'rgba(220,38,38,0.12)',
                info: '#0891b2', infoLight: 'rgba(8,145,178,0.12)',
                purple: '#7c3aed', purpleLight: 'rgba(124,58,237,0.12)',
                pink: '#db2777', pinkLight: 'rgba(219,39,119,0.12)',
                muted: '#94a3b8', border: '#f1f5f9',
            };

            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#e2e8f0',
                        bodyColor: '#fff',
                        titleFont: { size: 11, weight: '600' },
                        bodyFont: { size: 13, weight: '700' },
                        padding: 14,
                        cornerRadius: 10,
                        displayColors: false,
                        boxPadding: 6,
                    }
                },
            };

            // ── 1. Revenue Trend (30 Days) ─────────────────────────
            const rev30Ctx = document.getElementById('revenueChart30');
            if (rev30Ctx) {
                const rev30Grad = rev30Ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
                rev30Grad.addColorStop(0, 'rgba(99,102,241,0.3)');
                rev30Grad.addColorStop(0.5, 'rgba(59,130,246,0.08)');
                rev30Grad.addColorStop(1, 'rgba(59,130,246,0)');
                new Chart(rev30Ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_column($revenueChart30, 'label')) !!},
                        datasets: [{
                            label: 'Revenue (AED)',
                            data: {!! json_encode(array_column($revenueChart30, 'value')) !!},
                            borderColor: '#6366f1',
                            backgroundColor: rev30Grad,
                            borderWidth: 3, tension: 0.4, fill: true,
                            pointBackgroundColor: '#6366f1',
                            pointBorderColor: '#fff', pointBorderWidth: 2.5,
                            pointRadius: 4, pointHoverRadius: 7,
                            pointHoverBackgroundColor: '#4f46e5',
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        plugins: {
                            ...chartDefaults.plugins,
                            tooltip: {
                                ...chartDefaults.plugins.tooltip,
                                callbacks: { label: ctx => 'AED ' + ctx.parsed.y.toLocaleString() }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: COLORS.border },
                                ticks: { font: { size: 10 }, color: COLORS.muted, callback: v => v >= 1000 ? (v/1000)+'k' : v }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 9 }, color: COLORS.muted, maxRotation: 45, maxTicksLimit: 15 }
                            }
                        }
                    }
                });
            }

            // ── 2. Monthly Revenue (12 Months) ─────────────────────
            const monthlyCtx = document.getElementById('monthlyRevenueChart');
            if (monthlyCtx) {
                const mBarColors = ['#6366f1','#3b82f6','#0891b2','#059669','#d97706','#dc2626','#7c3aed','#db2777','#0d9488','#ea580c','#6366f1','#3b82f6'];
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_column($monthlyRevenue, 'short')) !!},
                        datasets: [{
                            label: 'Revenue',
                            data: {!! json_encode(array_column($monthlyRevenue, 'revenue')) !!},
                            backgroundColor: mBarColors,
                            borderColor: mBarColors,
                            borderWidth: 2,
                            borderRadius: 8, borderSkipped: false,
                            barThickness: 18,
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        plugins: {
                            ...chartDefaults.plugins,
                            tooltip: {
                                ...chartDefaults.plugins.tooltip,
                                callbacks: { label: ctx => 'AED ' + ctx.parsed.y.toLocaleString() }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true, grid: { color: COLORS.border },
                                ticks: { font: { size: 10 }, color: COLORS.muted, callback: v => v >= 1000 ? (v/1000)+'k' : v }
                            },
                            x: { grid: { display: false }, ticks: { font: { size: 10 }, color: COLORS.muted } }
                        }
                    }
                });
            }

            // ── 3. Bookings Trend (30 Days) ────────────────────────
            const book30Ctx = document.getElementById('bookingsChart30');
            if (book30Ctx) {
                const book30Grad = book30Ctx.getContext('2d').createLinearGradient(0, 0, 0, 230);
                book30Grad.addColorStop(0, 'rgba(16,185,129,0.3)');
                book30Grad.addColorStop(0.5, 'rgba(5,150,105,0.08)');
                book30Grad.addColorStop(1, 'rgba(5,150,105,0)');
                new Chart(book30Ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode(array_column($bookingsChart30, 'label')) !!},
                        datasets: [{
                            label: 'Bookings',
                            data: {!! json_encode(array_column($bookingsChart30, 'value')) !!},
                            borderColor: '#10b981',
                            backgroundColor: book30Grad,
                            borderWidth: 3, tension: 0.4, fill: true,
                            pointRadius: 3, pointHoverRadius: 6,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#fff', pointBorderWidth: 2,
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        scales: {
                            y: { beginAtZero: true, grid: { color: COLORS.border }, ticks: { font: { size: 10 }, color: COLORS.muted, stepSize: 1 } },
                            x: { grid: { display: false }, ticks: { font: { size: 9 }, color: COLORS.muted, maxTicksLimit: 10, maxRotation: 45 } }
                        }
                    }
                });
            }

            // ── 4. Booking Status Doughnut ─────────────────────────
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const statusData = @json($bookingsByStatus);
                const statusBgMap = { pending: '#fbbf24', paid: '#22d3ee', confirmed: '#34d399', cancelled: '#f87171', refunded: '#a78bfa' };
                const statusBorderMap = { pending: '#f59e0b', paid: '#0891b2', confirmed: '#059669', cancelled: '#dc2626', refunded: '#7c3aed' };
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: Object.keys(statusData).map(s => statusBgMap[s] || '#94a3b8'),
                            borderColor: Object.keys(statusData).map(s => statusBorderMap[s] || '#64748b'),
                            borderWidth: 3, hoverOffset: 12, hoverBorderWidth: 4,
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        cutout: '62%',
                        plugins: {
                            ...chartDefaults.plugins,
                            legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true, pointStyle: 'circle', font: { size: 11, weight: '600' }, color: '#334155' } }
                        }
                    }
                });
            }

            // ── 5. Bookings by Day of Week ─────────────────────────
            const dowCtx = document.getElementById('dayOfWeekChart');
            if (dowCtx) {
                const dowData = @json($bookingsByDay);
                const dowColors = ['#f87171','#fbbf24','#34d399','#22d3ee','#6366f1','#a78bfa','#f472b6'];
                const dowBorders = ['#dc2626','#d97706','#059669','#0891b2','#4f46e5','#7c3aed','#db2777'];
                new Chart(dowCtx, {
                    type: 'bar',
                    data: {
                        labels: dowData.map(d => d.label),
                        datasets: [{
                            label: 'Bookings',
                            data: dowData.map(d => d.value),
                            backgroundColor: dowColors,
                            borderColor: dowBorders,
                            borderWidth: 2, borderRadius: 8, borderSkipped: false,
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        scales: {
                            y: { beginAtZero: true, grid: { color: COLORS.border }, ticks: { font: { size: 10 }, color: COLORS.muted, stepSize: 1 } },
                            x: { grid: { display: false }, ticks: { font: { size: 11 }, color: COLORS.muted } }
                        }
                    }
                });
            }

            // ── 6. Revenue by Domain ───────────────────────────────
            const domRevCtx = document.getElementById('domainRevenueChart');
            if (domRevCtx) {
                const domData = @json($revenueByDomain);
                const domBarColors = ['#6366f1','#3b82f6','#0891b2','#059669','#d97706','#dc2626','#7c3aed','#db2777','#0d9488','#ea580c'];
                new Chart(domRevCtx, {
                    type: 'bar',
                    data: {
                        labels: domData.map(d => d.name.length > 20 ? d.name.substr(0, 20) + '...' : d.name),
                        datasets: [{
                            label: 'Revenue',
                            data: domData.map(d => parseFloat(d.total_revenue)),
                            backgroundColor: domData.map((d,i) => domBarColors[i % domBarColors.length]),
                            borderColor: domData.map((d,i) => domBarColors[i % domBarColors.length]),
                            borderWidth: 2, borderRadius: 8, borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        ...chartDefaults,
                        plugins: {
                            ...chartDefaults.plugins,
                            tooltip: {
                                ...chartDefaults.plugins.tooltip,
                                callbacks: { label: ctx => 'AED ' + ctx.parsed.x.toLocaleString() }
                            }
                        },
                        scales: {
                            x: { beginAtZero: true, grid: { color: COLORS.border }, ticks: { font: { size: 10 }, color: COLORS.muted, callback: v => v >= 1000 ? (v/1000)+'k' : v } },
                            y: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#334155' } }
                        }
                    }
                });
            }

            // ── 7. Revenue by Location ─────────────────────────────
            const locRevCtx = document.getElementById('locationRevenueChart');
            if (locRevCtx) {
                const locData = @json($revenueByLocation);
                const locBarColors = ['#059669','#0d9488','#0891b2','#10b981','#14b8a6','#047857','#065f46','#0f766e','#115e59','#064e3b'];
                new Chart(locRevCtx, {
                    type: 'bar',
                    data: {
                        labels: locData.map(l => l.name.length > 20 ? l.name.substr(0, 20) + '...' : l.name),
                        datasets: [{
                            label: 'Revenue',
                            data: locData.map(l => parseFloat(l.total_revenue)),
                            backgroundColor: locData.map((l,i) => locBarColors[i % locBarColors.length]),
                            borderColor: locData.map((l,i) => locBarColors[i % locBarColors.length]),
                            borderWidth: 2, borderRadius: 8, borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        ...chartDefaults,
                        plugins: {
                            ...chartDefaults.plugins,
                            tooltip: {
                                ...chartDefaults.plugins.tooltip,
                                callbacks: { label: ctx => 'AED ' + ctx.parsed.x.toLocaleString() }
                            }
                        },
                        scales: {
                            x: { beginAtZero: true, grid: { color: COLORS.border }, ticks: { font: { size: 10 }, color: COLORS.muted, callback: v => v >= 1000 ? (v/1000)+'k' : v } },
                            y: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#334155' } }
                        }
                    }
                });
            }

            // ── 8. Revenue by Room Type ────────────────────────────
            const rtCtx = document.getElementById('roomTypeChart');
            if (rtCtx) {
                const rtData = @json($revenueByRoomType);
                const rtColors = [COLORS.primary, COLORS.success, COLORS.warning, COLORS.danger, COLORS.info, COLORS.purple, COLORS.pink, '#f97316', '#06b6d4', '#8b5cf6'];
                new Chart(rtCtx, {
                    type: 'doughnut',
                    data: {
                        labels: rtData.map(r => r.name),
                        datasets: [{
                            data: rtData.map(r => parseFloat(r.total_revenue)),
                            backgroundColor: rtColors.slice(0, rtData.length),
                            borderWidth: 0, hoverOffset: 8,
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        cutout: '55%',
                        plugins: {
                            ...chartDefaults.plugins,
                            legend: { position: 'right', labels: { padding: 10, usePointStyle: true, pointStyle: 'circle', font: { size: 10 } } },
                            tooltip: {
                                ...chartDefaults.plugins.tooltip,
                                callbacks: { label: ctx => ctx.label + ': AED ' + ctx.parsed.toLocaleString() }
                            }
                        }
                    }
                });
            }

            // ── 9. Guest Nationality ───────────────────────────────
            const natCtx = document.getElementById('nationalityChart');
            if (natCtx) {
                const natData = @json($guestNationality);
                const natLabels = Object.keys(natData);
                const natValues = Object.values(natData);
                const natColors = ['#6366f1', '#059669', '#d97706', '#dc2626', '#0891b2', '#7c3aed', '#db2777', '#f97316', '#06b6d4', '#8b5cf6'];
                new Chart(natCtx, {
                    type: 'bar',
                    data: {
                        labels: natLabels,
                        datasets: [{
                            label: 'Guests',
                            data: natValues,
                            backgroundColor: natColors.slice(0, natLabels.length),
                            borderColor: natColors.slice(0, natLabels.length),
                            borderWidth: 2, borderRadius: 8, borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        ...chartDefaults,
                        scales: {
                            x: { beginAtZero: true, grid: { color: COLORS.border }, ticks: { font: { size: 10 }, color: COLORS.muted, stepSize: 1 } },
                            y: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#334155' } }
                        }
                    }
                });
            }

            // ── 10. Hourly Booking Pattern ──────────────────────────
            const hourCtx = document.getElementById('hourlyChart');
            if (hourCtx) {
                const hourData = @json($hourlyData);
                const hourGrad = hourCtx.getContext('2d').createLinearGradient(0, 0, 0, 260);
                hourGrad.addColorStop(0, 'rgba(124,58,237,0.3)');
                hourGrad.addColorStop(0.5, 'rgba(124,58,237,0.08)');
                hourGrad.addColorStop(1, 'rgba(124,58,237,0)');
                new Chart(hourCtx, {
                    type: 'line',
                    data: {
                        labels: hourData.map(h => h.label),
                        datasets: [{
                            label: 'Bookings',
                            data: hourData.map(h => h.value),
                            borderColor: '#7c3aed',
                            backgroundColor: hourGrad,
                            borderWidth: 3, tension: 0.4, fill: true,
                            pointRadius: 3, pointHoverRadius: 6,
                            pointBackgroundColor: '#7c3aed',
                            pointBorderColor: '#fff', pointBorderWidth: 2,
                        }]
                    },
                    options: {
                        ...chartDefaults,
                        scales: {
                            y: { beginAtZero: true, grid: { color: COLORS.border }, ticks: { font: { size: 10 }, color: COLORS.muted, stepSize: 1 } },
                            x: { grid: { display: false }, ticks: { font: { size: 9 }, color: COLORS.muted, maxTicksLimit: 12 } }
                        }
                    }
                });
            }
        });
    </script>
    </x-slot:scripts>
</x-admin-layout>
