<x-admin-layout title="Analytics" pageTitle="Analytics">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item active">Analytics</li>
    </x-slot:breadcrumb>

    <x-slot:styles>
        <link href="{{ asset('assets/css/flatpickr.min.css') }}" rel="stylesheet">
        <style>
            .flatpickr-calendar {
                border: none !important; border-radius: 0.85rem !important;
                box-shadow: 0 10px 40px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06) !important;
                font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
                z-index: 9999 !important;
            }
            .flatpickr-months {
                background: linear-gradient(135deg, #667eea, #8b5cf6);
                padding: 6px 0; border-radius: 0.85rem 0.85rem 0 0; overflow: hidden;
            }
            .flatpickr-months .flatpickr-month { height: 38px; }
            .flatpickr-current-month { font-size: 0.95rem; font-weight: 700; color: #fff !important; padding-top: 4px; }
            .flatpickr-current-month .flatpickr-monthDropdown-months { background: transparent; color: #fff; font-weight: 700; -webkit-appearance: none; }
            .flatpickr-current-month .flatpickr-monthDropdown-months option { background: #fff; color: #1a1d29; }
            .flatpickr-current-month input.cur-year { color: #fff !important; font-weight: 700; }
            .flatpickr-months .flatpickr-prev-month, .flatpickr-months .flatpickr-next-month { fill: #fff !important; color: #fff !important; padding: 6px 10px; }
            .flatpickr-months .flatpickr-prev-month:hover svg, .flatpickr-months .flatpickr-next-month:hover svg { fill: rgba(255,255,255,0.7) !important; }
            .flatpickr-weekdays { background: linear-gradient(135deg, #667eea, #8b5cf6); }
            span.flatpickr-weekday { color: rgba(255,255,255,0.7) !important; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.03em; }
            .flatpickr-innerContainer { padding: 4px; }
            .flatpickr-day {
                border-radius: 8px !important; font-weight: 500; font-size: 0.84rem;
                color: #374151; border: none; height: 36px; line-height: 36px; transition: all 0.15s;
            }
            .flatpickr-day:hover { background: #eef2ff; color: #667eea; }
            .flatpickr-day.today { background: rgba(102,126,234,0.1); color: #667eea; border: none; }
            .flatpickr-day.today:hover { background: rgba(102,126,234,0.2); }
            .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
                background: linear-gradient(135deg, #667eea, #8b5cf6) !important;
                color: #fff !important; border: none !important; box-shadow: 0 2px 8px rgba(102,126,234,0.35);
            }
            .flatpickr-day.inRange { background: rgba(102,126,234,0.1) !important; box-shadow: none !important; border: none !important; color: #667eea; }
            .flatpickr-day.flatpickr-disabled, .flatpickr-day.prevMonthDay, .flatpickr-day.nextMonthDay { color: #d1d5db !important; }
            .date-input-wrap { position: relative; }
            .date-input-wrap .form-control { padding-right: 2.5rem; cursor: pointer; }
            .date-input-wrap .fp-icon {
                position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                color: var(--text-muted); pointer-events: none; font-size: 1.1rem; z-index: 2;
            }
        </style>
    </x-slot:styles>

    <style>
        :root { --accent-primary: #4f46e5; --accent-success: #059669; --accent-info: #0891b2; --accent-warning: #d97706; --accent-danger: #dc2626; --border-light: #e5e7eb; --text-primary: #111827; --text-secondary: #4b5563; --text-muted: #9ca3af; }
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

        .filter-card {
            border: 2px solid var(--border-light); border-radius: 16px; padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem; background: #fff;
        }

        .stat-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        @media (max-width: 992px) { .stat-cards { grid-template-columns: repeat(2, 1fr); } }
        .stat-card {
            border: none; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06); padding: 1.5rem;
            background: #fff; position: relative; overflow: hidden;
        }
        .stat-card .stat-stripe { position: absolute; top: 0; left: 0; right: 0; height: 4px; }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin-bottom: .75rem;
        }
        .stat-card .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text-primary); }
        .stat-card .stat-label { font-size: .72rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; font-weight: 600; }

        .chart-card {
            border: none; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06); background: #fff;
        }
        .chart-card .card-header {
            background: none; border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem;
        }
        .chart-card .card-header h6 { font-weight: 700; color: var(--text-primary); margin: 0; }
        .chart-card .card-body { padding: 1.25rem 1.5rem; }

        .section-divider {
            display: flex; align-items: center; gap: 1rem;
            margin: 2.5rem 0 1.5rem; padding: 1rem 0;
            border-top: 2px solid #f1f5f9;
        }
        .section-divider .section-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #667eea, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem; flex-shrink: 0;
        }
        .section-divider h5 { font-weight: 700; margin: 0; color: #111827; }
        .section-divider p { font-size: .82rem; color: #9ca3af; margin: 0; }

        .seo-stat-cards {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;
        }
        @media (max-width: 992px) { .seo-stat-cards { grid-template-columns: repeat(2, 1fr); } }

        .seo-table-card .table { margin: 0; }
        .seo-table-card .table thead th {
            background: #f8fafc; font-size: .75rem; text-transform: uppercase;
            letter-spacing: .05em; color: #64748b; font-weight: 700; border-bottom: 2px solid #e2e8f0;
            padding: .75rem 1rem;
        }
        .seo-table-card .table tbody td {
            padding: .65rem 1rem; font-size: .85rem; color: #334155;
            border-bottom: 1px solid #f1f5f9; vertical-align: middle;
        }
        .seo-table-card .table tbody tr:hover { background: #f8fafc; }
        .seo-table-card .keyword-badge {
            background: #eef2ff; color: #4f46e5; padding: .25rem .6rem;
            border-radius: 6px; font-weight: 600; font-size: .8rem;
        }
        .seo-table-card .page-path {
            font-family: 'SFMono-Regular', monospace; font-size: .8rem; color: #0891b2;
        }
        .seo-table-card .no-data {
            padding: 2rem; text-align: center; color: #9ca3af; font-size: .9rem;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="header-icon" style="background: linear-gradient(135deg, #0891b2, #22d3ee);">
            <i class='bx bx-line-chart'></i>
        </div>
        <div class="flex-grow-1">
            <h4>Analytics</h4>
            <p>Track performance metrics across your domains</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <form method="GET">
            <div class="row g-3 align-items-end">
                <x-form.select name="domain_id" label="Domain" :options="$domains->pluck('name', 'id')->toArray()" :selected="$domainId" placeholder="All Domains" class="col-lg-3 col-md-6" />
                <div class="col-lg-3 col-md-6">
                    <label for="date_from" class="form-label">From</label>
                    <div class="date-input-wrap">
                        <input type="text" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}" placeholder="Start date" autocomplete="off" readonly>
                        <i class='bx bx-calendar fp-icon'></i>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="date_to" class="form-label">To</label>
                    <div class="date-input-wrap">
                        <input type="text" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}" placeholder="End date" autocomplete="off" readonly>
                        <i class='bx bx-calendar fp-icon'></i>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill" style="padding: 0.55rem 0.85rem; font-size: 0.875rem; border-width: 2px;"><i class='bx bx-filter me-1'></i> Apply</button>
                    <a href="{{ route('admin.analytics') }}" class="btn btn-outline-secondary flex-fill" style="padding: 0.55rem 0.85rem; font-size: 0.875rem; border-width: 2px;"><i class='bx bx-reset me-1'></i> Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Stat Cards -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #3b82f6, #60a5fa);"></div>
            <div class="stat-icon" style="background: rgba(59,130,246,.1); color: #3b82f6;">
                <i class='bx bxs-wallet'></i>
            </div>
            <div class="stat-value">AED {{ number_format($summary['total_revenue'], 2) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #059669, #34d399);"></div>
            <div class="stat-icon" style="background: rgba(5,150,105,.1); color: #059669;">
                <i class='bx bxs-calendar-check'></i>
            </div>
            <div class="stat-value">{{ number_format($summary['total_bookings']) }}</div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #6366f1, #818cf8);"></div>
            <div class="stat-icon" style="background: rgba(99,102,241,.1); color: #6366f1;">
                <i class='bx bx-show'></i>
            </div>
            <div class="stat-value">{{ number_format($summary['total_page_views']) }}</div>
            <div class="stat-label">Page Views</div>
        </div>
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #d97706, #fbbf24);"></div>
            <div class="stat-icon" style="background: rgba(217,119,6,.1); color: #d97706;">
                <i class='bx bxs-receipt'></i>
            </div>
            <div class="stat-value">AED {{ number_format($summary['avg_booking_value'], 2) }}</div>
            <div class="stat-label">Avg Booking Value</div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card chart-card mb-4">
        <div class="card-header">
            <h6>Revenue Over Time</h6>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    <!-- Revenue by Domain & Bookings by Status -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card chart-card h-100">
                <div class="card-header">
                    <h6>Revenue by Domain</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueByDomainChart" height="280"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card h-100">
                <div class="card-header">
                    <h6>Bookings by Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="bookingsByStatusChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Hotel (Top 10) -->
    <div class="card chart-card mb-4">
        <div class="card-header">
            <h6>Revenue by Hotel (Top 10)</h6>
        </div>
        <div class="card-body">
            <canvas id="revenueByHotelChart" height="100"></canvas>
        </div>
    </div>

    <!-- Hotel Performance (Top 10) -->
    <div class="card chart-card mb-4">
        <div class="card-header">
            <h6>Hotel Performance (Top 10)</h6>
        </div>
        <div class="card-body">
            <canvas id="hotelPerformanceChart" height="100"></canvas>
        </div>
    </div>

    <!-- SEO Analytics Section -->
    <div class="section-divider">
        <div class="section-icon"><i class='bx bx-search-alt'></i></div>
        <div>
            <h5>SEO Analytics</h5>
            <p>Search engine performance metrics</p>
        </div>
    </div>

    <!-- SEO Stat Cards -->
    <div class="seo-stat-cards">
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #059669, #34d399);"></div>
            <div class="stat-icon" style="background: rgba(5,150,105,.1); color: #059669;">
                <i class='bx bx-trending-up'></i>
            </div>
            <div class="stat-value">{{ number_format($seoSummary['total_organic_traffic']) }}</div>
            <div class="stat-label">Organic Traffic</div>
        </div>
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #3b82f6, #60a5fa);"></div>
            <div class="stat-icon" style="background: rgba(59,130,246,.1); color: #3b82f6;">
                <i class='bx bx-show'></i>
            </div>
            <div class="stat-value">{{ number_format($seoSummary['total_impressions']) }}</div>
            <div class="stat-label">Search Impressions</div>
        </div>
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #6366f1, #818cf8);"></div>
            <div class="stat-icon" style="background: rgba(99,102,241,.1); color: #6366f1;">
                <i class='bx bx-pointer'></i>
            </div>
            <div class="stat-value">{{ number_format($seoSummary['total_clicks']) }}</div>
            <div class="stat-label">Search Clicks</div>
        </div>
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #0891b2, #22d3ee);"></div>
            <div class="stat-icon" style="background: rgba(8,145,178,.1); color: #0891b2;">
                <i class='bx bx-target-lock'></i>
            </div>
            <div class="stat-value">{{ $seoSummary['avg_ctr'] }}%</div>
            <div class="stat-label">Click-Through Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #d97706, #fbbf24);"></div>
            <div class="stat-icon" style="background: rgba(217,119,6,.1); color: #d97706;">
                <i class='bx bx-sort-alt-2'></i>
            </div>
            <div class="stat-value">{{ $seoSummary['avg_position'] }}</div>
            <div class="stat-label">Avg Position</div>
        </div>
        <div class="stat-card">
            <div class="stat-stripe" style="background: linear-gradient(90deg, #dc2626, #f87171);"></div>
            <div class="stat-icon" style="background: rgba(220,38,38,.1); color: #dc2626;">
                <i class='bx bx-log-out'></i>
            </div>
            <div class="stat-value">{{ $seoSummary['avg_bounce_rate'] }}%</div>
            <div class="stat-label">Bounce Rate</div>
        </div>
    </div>

    <!-- Organic Traffic & CTR Over Time -->
    <div class="card chart-card mb-4">
        <div class="card-header">
            <h6>Organic Traffic & CTR Over Time</h6>
        </div>
        <div class="card-body">
            <canvas id="seoTrafficChart" height="100"></canvas>
        </div>
    </div>

    <!-- Top Keywords & Top Landing Pages -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card chart-card seo-table-card h-100">
                <div class="card-header">
                    <h6>Top Keywords</h6>
                </div>
                <div class="card-body p-0">
                    @if($topKeywords->count())
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Keyword</th>
                                        <th class="text-end">Clicks</th>
                                        <th class="text-end">Impressions</th>
                                        <th class="text-end">CTR</th>
                                        <th class="text-end">Avg Position</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topKeywords as $kw)
                                        <tr>
                                            <td><span class="keyword-badge">{{ $kw['keyword'] }}</span></td>
                                            <td class="text-end fw-semibold">{{ number_format($kw['clicks']) }}</td>
                                            <td class="text-end">{{ number_format($kw['impressions']) }}</td>
                                            <td class="text-end">{{ $kw['ctr'] }}%</td>
                                            <td class="text-end">{{ $kw['position'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="no-data">
                            <i class='bx bx-search-alt' style="font-size: 2rem; display: block; margin-bottom: .5rem;"></i>
                            No keyword data available for this period
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card seo-table-card h-100">
                <div class="card-header">
                    <h6>Top Landing Pages</h6>
                </div>
                <div class="card-body p-0">
                    @if($topLandingPages->count())
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Page</th>
                                        <th class="text-end">Views</th>
                                        <th class="text-end">Bounce Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topLandingPages as $lp)
                                        <tr>
                                            <td><span class="page-path">{{ $lp['page'] }}</span></td>
                                            <td class="text-end fw-semibold">{{ number_format($lp['views']) }}</td>
                                            <td class="text-end">{{ $lp['bounce_rate'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="no-data">
                            <i class='bx bx-file' style="font-size: 2rem; display: block; margin-bottom: .5rem;"></i>
                            No landing page data available for this period
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script src="{{ asset('assets/js/flatpickr.min.js') }}"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Flatpickr date pickers
            var fromInput = document.getElementById('date_from');
            var toInput = document.getElementById('date_to');

            var fromPicker = flatpickr(fromInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M j, Y',
                allowInput: false,
                disableMobile: true,
                appendTo: document.body,
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        toPicker.set('minDate', selectedDates[0]);
                    } else {
                        toPicker.set('minDate', null);
                    }
                }
            });

            var toPicker = flatpickr(toInput, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'M j, Y',
                allowInput: false,
                disableMobile: true,
                appendTo: document.body,
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        fromPicker.set('maxDate', selectedDates[0]);
                    } else {
                        fromPicker.set('maxDate', null);
                    }
                }
            });

            if (fromInput.value) {
                toPicker.set('minDate', fromInput.value);
            }
            if (toInput.value) {
                fromPicker.set('maxDate', toInput.value);
            }

            // Format date for chart labels: "2026-02-28..." → "Feb 28, 2026"
            var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            function formatChartDate(dateStr) {
                // Handle both "2026-02-28" and "2026-02-28T00:00:00.000000Z" formats
                var parts = dateStr.substring(0, 10).split('-');
                if (parts.length !== 3) return dateStr;
                var monthIndex = parseInt(parts[1], 10) - 1;
                var day = parseInt(parts[2], 10);
                return months[monthIndex] + ' ' + day + ', ' + parts[0];
            }

            // Vibrant color palettes
            var barPalette = [
                ['#6366f1','#a78bfa'], ['#3b82f6','#60a5fa'], ['#0891b2','#22d3ee'],
                ['#059669','#34d399'], ['#d97706','#fbbf24'], ['#dc2626','#f87171'],
                ['#7c3aed','#c4b5fd'], ['#db2777','#f472b6'], ['#0d9488','#5eead4'],
                ['#ea580c','#fb923c']
            ];
            var greenPalette = [
                ['#059669','#34d399'], ['#0d9488','#5eead4'], ['#0891b2','#22d3ee'],
                ['#10b981','#6ee7b7'], ['#14b8a6','#99f6e4'], ['#047857','#34d399'],
                ['#065f46','#10b981'], ['#0f766e','#2dd4bf'], ['#115e59','#14b8a6'],
                ['#064e3b','#059669']
            ];

            // Helper: create vertical gradient for canvas
            function createGradient(ctx, c1, c2) {
                var g = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height);
                g.addColorStop(0, c1); g.addColorStop(1, c2); return g;
            }
            function createHGradient(ctx, c1, c2) {
                var g = ctx.createLinearGradient(0, 0, ctx.canvas.width, 0);
                g.addColorStop(0, c1); g.addColorStop(1, c2); return g;
            }

            // Shared tooltip config
            var aedTooltip = {
                backgroundColor: '#0f172a',
                titleColor: '#e2e8f0',
                bodyColor: '#fff',
                titleFont: { size: 12, weight: '600' },
                bodyFont: { size: 13, weight: '700' },
                cornerRadius: 10,
                padding: 14,
                displayColors: true,
                boxPadding: 6,
                callbacks: {
                    label: function(item) { return ' ' + item.dataset.label + ': AED ' + Number(item.raw).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
                }
            };

            // ── Revenue Over Time (line) ──
            var data = @json($analytics);
            var revenueCtx = document.getElementById('revenueChart').getContext('2d');
            var lineGrad = revenueCtx.createLinearGradient(0, 0, 0, 340);
            lineGrad.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
            lineGrad.addColorStop(0.5, 'rgba(99, 102, 241, 0.08)');
            lineGrad.addColorStop(1, 'rgba(99, 102, 241, 0)');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: data.map(function(d) { return formatChartDate(d.date); }),
                    datasets: [{
                        label: 'Revenue (AED)',
                        data: data.map(function(d) { return d.revenue; }),
                        borderColor: '#6366f1',
                        backgroundColor: lineGrad,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2.5,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#4f46e5',
                        pointHoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a', titleColor: '#e2e8f0', bodyColor: '#fff',
                            titleFont: { size: 12, weight: '600' }, bodyFont: { size: 13, weight: '700' },
                            cornerRadius: 10, padding: 14, displayColors: false,
                            callbacks: {
                                title: function(items) { return items[0].label; },
                                label: function(item) { return 'AED ' + Number(item.raw).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8', callback: function(v) { return v >= 1000 ? (v/1000)+'k' : v; } } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94a3b8', maxTicksLimit: 15 } }
                    }
                }
            });

            // ── Revenue by Domain (horizontal bar) ──
            var domainData = @json($revenueByDomain);
            var domCtx = document.getElementById('revenueByDomainChart').getContext('2d');
            var domBg = domainData.map(function(d, i) {
                var c = barPalette[i % barPalette.length];
                return createHGradient(domCtx, c[0], c[1]);
            });
            var domBorder = domainData.map(function(d, i) { return barPalette[i % barPalette.length][0]; });
            new Chart(domCtx, {
                type: 'bar',
                data: {
                    labels: domainData.map(function(d) { return d.name; }),
                    datasets: [{
                        label: 'Revenue',
                        data: domainData.map(function(d) { return d.revenue; }),
                        backgroundColor: domBg,
                        borderColor: domBorder,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 30,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: aedTooltip,
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8', callback: function(v) { return v >= 1000 ? (v/1000)+'k' : v; } } },
                        y: { grid: { display: false }, ticks: { font: { size: 12, weight: '600' }, color: '#334155' } }
                    }
                }
            });

            // ── Revenue by Hotel Top 10 (horizontal bar) ──
            var hotelRevData = @json($revenueByHotel);
            var hRevCtx = document.getElementById('revenueByHotelChart').getContext('2d');
            var hRevBg = hotelRevData.map(function(d, i) {
                var c = greenPalette[i % greenPalette.length];
                return createHGradient(hRevCtx, c[0], c[1]);
            });
            var hRevBorder = hotelRevData.map(function(d, i) { return greenPalette[i % greenPalette.length][0]; });
            new Chart(hRevCtx, {
                type: 'bar',
                data: {
                    labels: hotelRevData.map(function(d) { return d.name; }),
                    datasets: [{
                        label: 'Revenue',
                        data: hotelRevData.map(function(d) { return d.revenue; }),
                        backgroundColor: hRevBg,
                        borderColor: hRevBorder,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: 30,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: aedTooltip,
                    },
                    scales: {
                        x: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false }, ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8', callback: function(v) { return v >= 1000 ? (v/1000)+'k' : v; } } },
                        y: { grid: { display: false }, ticks: { font: { size: 12, weight: '600' }, color: '#334155' } }
                    }
                }
            });

            // ── Hotel Performance Top 10 (grouped bar, dual Y) ──
            var perfData = @json($hotelPerformance);
            var pCtx = document.getElementById('hotelPerformanceChart').getContext('2d');
            var bookGrad = createGradient(pCtx, '#f59e0b', '#fcd34d');
            var revGrad = createGradient(pCtx, '#0891b2', '#67e8f9');
            new Chart(pCtx, {
                type: 'bar',
                data: {
                    labels: perfData.map(function(d) { return d.name; }),
                    datasets: [
                        {
                            label: 'Bookings',
                            data: perfData.map(function(d) { return d.bookings; }),
                            backgroundColor: bookGrad,
                            borderColor: '#f59e0b',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Revenue (AED)',
                            data: perfData.map(function(d) { return d.revenue; }),
                            backgroundColor: revGrad,
                            borderColor: '#0891b2',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true, position: 'top', labels: { font: { size: 12, weight: '600' }, usePointStyle: true, pointStyle: 'rectRounded', padding: 20, color: '#334155' } },
                        tooltip: {
                            backgroundColor: '#0f172a', titleColor: '#e2e8f0', bodyColor: '#fff',
                            titleFont: { size: 12, weight: '600' }, bodyFont: { size: 13, weight: '700' },
                            cornerRadius: 10, padding: 14, displayColors: true, boxPadding: 6,
                            callbacks: {
                                label: function(item) {
                                    if (item.dataset.yAxisID === 'y1') {
                                        return ' ' + item.dataset.label + ': AED ' + Number(item.raw).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                    }
                                    return ' ' + item.dataset.label + ': ' + item.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, position: 'left', grid: { color: '#f1f5f9', drawBorder: false }, title: { display: true, text: 'Bookings', font: { size: 12, weight: '600' }, color: '#f59e0b' }, ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8' } },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue (AED)', font: { size: 12, weight: '600' }, color: '#0891b2' }, ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8', callback: function(v) { return v >= 1000 ? (v/1000)+'k' : v; } } },
                        x: { grid: { display: false }, ticks: { font: { size: 11, weight: '500' }, color: '#334155', maxRotation: 45 } }
                    }
                }
            });

            // ── Organic Traffic & CTR Over Time (dual Y-axis) ──
            var seoData = @json($analytics);
            var seoCtx = document.getElementById('seoTrafficChart').getContext('2d');
            var organicGrad = seoCtx.createLinearGradient(0, 0, 0, 340);
            organicGrad.addColorStop(0, 'rgba(5, 150, 105, 0.3)');
            organicGrad.addColorStop(0.5, 'rgba(5, 150, 105, 0.08)');
            organicGrad.addColorStop(1, 'rgba(5, 150, 105, 0)');

            // Calculate daily CTR
            var seoCtrData = seoData.map(function(d) {
                return d.search_impressions > 0
                    ? Math.round((d.search_clicks / d.search_impressions) * 10000) / 100
                    : 0;
            });

            new Chart(seoCtx, {
                type: 'line',
                data: {
                    labels: seoData.map(function(d) { return formatChartDate(d.date); }),
                    datasets: [
                        {
                            label: 'Organic Traffic',
                            data: seoData.map(function(d) { return d.organic_traffic; }),
                            borderColor: '#059669',
                            backgroundColor: organicGrad,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#059669',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2.5,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            yAxisID: 'y',
                        },
                        {
                            label: 'CTR %',
                            data: seoCtrData,
                            borderColor: '#0891b2',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2.5,
                            borderDash: [6, 3],
                            pointBackgroundColor: '#0891b2',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            yAxisID: 'y1',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true, position: 'top', labels: { font: { size: 12, weight: '600' }, usePointStyle: true, pointStyle: 'circle', padding: 20, color: '#334155' } },
                        tooltip: {
                            backgroundColor: '#0f172a', titleColor: '#e2e8f0', bodyColor: '#fff',
                            titleFont: { size: 12, weight: '600' }, bodyFont: { size: 13, weight: '700' },
                            cornerRadius: 10, padding: 14, displayColors: true, boxPadding: 6,
                            callbacks: {
                                label: function(item) {
                                    if (item.dataset.yAxisID === 'y1') {
                                        return ' ' + item.dataset.label + ': ' + item.raw + '%';
                                    }
                                    return ' ' + item.dataset.label + ': ' + Number(item.raw).toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true, position: 'left',
                            grid: { color: '#f1f5f9', drawBorder: false },
                            title: { display: true, text: 'Organic Traffic', font: { size: 12, weight: '600' }, color: '#059669' },
                            ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8', callback: function(v) { return v >= 1000 ? (v/1000)+'k' : v; } }
                        },
                        y1: {
                            beginAtZero: true, position: 'right',
                            grid: { drawOnChartArea: false },
                            title: { display: true, text: 'CTR %', font: { size: 12, weight: '600' }, color: '#0891b2' },
                            ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8', callback: function(v) { return v + '%'; } }
                        },
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94a3b8', maxTicksLimit: 15 } }
                    }
                }
            });

            // ── Bookings by Status (doughnut) ──
            var statusData = @json($bookingsByStatus);
            var statusColors = {
                'Pending':   { bg: '#fbbf24', border: '#f59e0b' },
                'Paid':      { bg: '#22d3ee', border: '#0891b2' },
                'Confirmed': { bg: '#34d399', border: '#059669' },
                'Cancelled': { bg: '#f87171', border: '#dc2626' },
                'Refunded':  { bg: '#a78bfa', border: '#7c3aed' }
            };
            var fallback = { bg: '#94a3b8', border: '#64748b' };
            new Chart(document.getElementById('bookingsByStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: statusData.map(function(d) { return d.status; }),
                    datasets: [{
                        data: statusData.map(function(d) { return d.count; }),
                        backgroundColor: statusData.map(function(d) { return (statusColors[d.status] || fallback).bg; }),
                        borderColor: statusData.map(function(d) { return (statusColors[d.status] || fallback).border; }),
                        borderWidth: 3,
                        hoverOffset: 12,
                        hoverBorderWidth: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { display: true, position: 'bottom', labels: { font: { size: 13, weight: '600' }, usePointStyle: true, pointStyle: 'circle', padding: 20, color: '#334155' } },
                        tooltip: {
                            backgroundColor: '#0f172a', titleColor: '#e2e8f0', bodyColor: '#fff',
                            titleFont: { size: 12, weight: '600' }, bodyFont: { size: 14, weight: '700' },
                            cornerRadius: 10, padding: 14, displayColors: true, boxPadding: 6,
                            callbacks: {
                                label: function(item) { return ' ' + item.label + ': ' + item.raw + ' bookings'; }
                            }
                        }
                    }
                }
            });
        });
        </script>
    </x-slot:scripts>
</x-admin-layout>
