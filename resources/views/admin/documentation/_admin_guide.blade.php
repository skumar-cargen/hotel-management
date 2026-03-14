{{-- ================================================================ --}}
{{-- ADMIN PANEL GUIDE --}}
{{-- ================================================================ --}}

<div class="docs-layout">
    <!-- TOC Sidebar -->
    <div class="docs-toc" id="adminToc">
        <h6>Overview</h6>
        <a href="#admin-overview" class="toc-link active" data-tab="admin"><i class='bx bx-info-circle'></i> Overview</a>
        <h6>Modules</h6>
        <a href="#admin-dashboard" class="toc-link" data-tab="admin"><i class='bx bxs-dashboard'></i> Dashboard</a>
        <a href="#admin-domains" class="toc-link" data-tab="admin"><i class='bx bx-globe'></i> Domains</a>
        <a href="#admin-locations" class="toc-link" data-tab="admin"><i class='bx bx-map'></i> Locations</a>
        <a href="#admin-hotels" class="toc-link" data-tab="admin"><i class='bx bx-building'></i> Hotels</a>
        <a href="#admin-rooms" class="toc-link" data-tab="admin"><i class='bx bx-bed'></i> Room Types</a>
        <a href="#admin-amenities" class="toc-link" data-tab="admin"><i class='bx bx-star'></i> Amenities</a>
        <a href="#admin-pricing" class="toc-link" data-tab="admin"><i class='bx bx-dollar-circle'></i> Pricing Rules</a>
        <a href="#admin-bookings" class="toc-link" data-tab="admin"><i class='bx bx-calendar-check'></i> Bookings</a>
        <a href="#admin-reviews" class="toc-link" data-tab="admin"><i class='bx bx-message-square-dots'></i> Reviews</a>
        <a href="#admin-users" class="toc-link" data-tab="admin"><i class='bx bx-group'></i> Users & Roles</a>
        <a href="#admin-analytics" class="toc-link" data-tab="admin"><i class='bx bx-bar-chart-alt-2'></i> Analytics</a>
        <a href="#admin-commands" class="toc-link" data-tab="admin"><i class='bx bx-terminal'></i> Artisan Commands</a>
    </div>

    <!-- Content -->
    <div class="docs-content">

        {{-- OVERVIEW --}}
        <div id="admin-overview" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#7c3aed,#a78bfa);"><i class='bx bx-info-circle'></i></div>
                <div><h5>Overview & Tech Stack</h5><p>Platform architecture, technologies, and role system</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p><strong>Dubai Apartments</strong> is a centralized admin panel that manages approximately 70 hotel listing domains. Each domain operates as a separate hotel listing website with its own URL, language, currency, and pricing markup. Hotels, locations, amenities, room types, bookings, and reviews are all managed from this single admin interface.</p>
                    <p>The platform follows a multi-tenant architecture where a single hotel can be listed on multiple domains, each with different pricing rules. Bookings are scoped to the domain they originated from, and admin users can be restricted to specific domains.</p>

                    <h6 class="doc-section-title">Technology Stack</h6>
                    <div class="tech-badges">
                        <span class="tech-badge"><i class='bx bxl-php'></i> PHP 8.2+</span>
                        <span class="tech-badge"><i class='bx bxl-php'></i> Laravel 12</span>
                        <span class="tech-badge"><i class='bx bxs-data'></i> MySQL</span>
                        <span class="tech-badge"><i class='bx bxl-bootstrap'></i> Bootstrap 5</span>
                        <span class="tech-badge"><i class='bx bxl-jquery'></i> jQuery</span>
                        <span class="tech-badge"><i class='bx bx-table'></i> DataTables</span>
                        <span class="tech-badge"><i class='bx bx-search-alt'></i> Select2</span>
                        <span class="tech-badge"><i class='bx bx-line-chart'></i> Chart.js</span>
                        <span class="tech-badge"><i class='bx bxl-javascript'></i> Alpine.js</span>
                        <span class="tech-badge"><i class='bx bx-package'></i> Vite 7</span>
                        <span class="tech-badge"><i class='bx bx-shield-quarter'></i> Spatie Permission</span>
                    </div>

                    <h6 class="doc-section-title">Roles & Access Control</h6>
                    <p>6 predefined roles with 20 permissions. Users can also be scoped to specific domains.</p>
                    <table class="param-table">
                        <thead><tr><th>Role</th><th>Description</th><th>Key Access</th></tr></thead>
                        <tbody>
                            <tr><td><strong>Admin</strong></td><td>Full unrestricted access</td><td>All modules, all domains</td></tr>
                            <tr><td><strong>Price Manager</strong></td><td>Manages pricing rules and scheduled price changes</td><td>Pricing rules, bulk updates</td></tr>
                            <tr><td><strong>Content Editor</strong></td><td>Creates and edits hotels, locations, room types, amenities</td><td>Hotels, locations, amenities, room types</td></tr>
                            <tr><td><strong>SEO Manager</strong></td><td>Manages SEO fields &mdash; meta titles, descriptions, keywords, canonical URLs</td><td>SEO fields on hotels &amp; locations</td></tr>
                            <tr><td><strong>Support Staff</strong></td><td>Handles bookings and reviews</td><td>Bookings, reviews</td></tr>
                            <tr><td><strong>Domain Manager</strong></td><td>Configures domain settings and hotel/location assignment</td><td>Domain settings, assignment</td></tr>
                        </tbody>
                    </table>

                    <h6 class="doc-section-title">20 Permissions</h6>
                    <table class="param-table">
                        <thead><tr><th>Module</th><th>Permissions</th></tr></thead>
                        <tbody>
                            <tr><td>Domains</td><td><code>manage domains</code>, <code>view domains</code></td></tr>
                            <tr><td>Locations</td><td><code>manage locations</code>, <code>view locations</code></td></tr>
                            <tr><td>Hotels</td><td><code>manage hotels</code>, <code>view hotels</code></td></tr>
                            <tr><td>Room Types</td><td><code>manage rooms</code>, <code>view rooms</code></td></tr>
                            <tr><td>Pricing</td><td><code>manage pricing</code>, <code>view pricing</code></td></tr>
                            <tr><td>Bookings</td><td><code>manage bookings</code>, <code>view bookings</code>, <code>cancel bookings</code>, <code>refund bookings</code></td></tr>
                            <tr><td>Users</td><td><code>manage users</code>, <code>view users</code></td></tr>
                            <tr><td>Reviews</td><td><code>manage reviews</code>, <code>view reviews</code></td></tr>
                            <tr><td>Analytics</td><td><code>view analytics</code></td></tr>
                            <tr><td>Settings</td><td><code>manage settings</code></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- DASHBOARD --}}
        <div id="admin-dashboard" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#2563eb,#60a5fa);"><i class='bx bxs-dashboard'></i></div>
                <div><h5>Dashboard</h5><p>Central hub with 40+ metrics, charts, and activity feeds</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>The dashboard is the landing page after login. It provides a real-time overview of the entire platform's performance with comparisons to previous periods.</p>

                    <h6 class="doc-section-title">Summary Statistics</h6>
                    <ul class="feature-list">
                        <li><strong>Total Hotels</strong> &mdash; All hotel properties (active and inactive)</li>
                        <li><strong>Total Bookings</strong> &mdash; All-time booking count across all domains</li>
                        <li><strong>Total Revenue</strong> &mdash; Sum of confirmed/paid booking amounts</li>
                        <li><strong>Active Domains</strong> &mdash; Domains currently marked as active</li>
                        <li><strong>Total Locations, Reviews, Room Types, Users</strong></li>
                    </ul>

                    <h6 class="doc-section-title">Time-Based Metrics</h6>
                    <ul class="feature-list">
                        <li>Today's revenue &amp; bookings vs yesterday (growth %)</li>
                        <li>This week, this month, year-to-date totals</li>
                        <li>Average booking value &amp; average nights per stay</li>
                        <li>Cancellation rate, pending bookings, pending reviews</li>
                    </ul>

                    <h6 class="doc-section-title">Charts & Visualizations</h6>
                    <ul class="feature-list">
                        <li><strong>Revenue chart</strong> &mdash; Last 30 days (daily) and last 12 months (monthly)</li>
                        <li><strong>Booking breakdown</strong> &mdash; By status, by day of week, hourly patterns</li>
                        <li><strong>Guest nationality</strong> &mdash; Top 10 nationalities</li>
                        <li><strong>Payment status</strong> &mdash; Breakdown by payment state</li>
                    </ul>

                    <h6 class="doc-section-title">Top Performers</h6>
                    <ul class="feature-list">
                        <li>Top 10 domains by revenue</li>
                        <li>Top 15 hotels by earnings with avg rating</li>
                        <li>Top 10 locations by revenue</li>
                        <li>Top 10 room types by revenue</li>
                        <li>Recent bookings &amp; recent reviews feeds</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- DOMAINS --}}
        <div id="admin-domains" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#059669,#34d399);"><i class='bx bx-globe'></i></div>
                <div><h5>Domains</h5><p>Manage listing domains with branding, SEO, and hero slides</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Each domain represents a separate hotel listing website. Domains have their own branding (logo, favicon), SEO metadata, content pages (About, Privacy, Terms), and hero carousel slides.</p>

                    <h6 class="doc-section-title">CRUD Operations</h6>
                    <p>Full create, read, update, and delete with DataTables listing.</p>

                    <h6 class="doc-section-title">Form Fields</h6>
                    <table class="param-table">
                        <thead><tr><th>Field</th><th>Type</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td><code>name</code></td><td>text</td><td><span class="badge-req">Required</span> Display name</td></tr>
                            <tr><td><code>domain</code></td><td>text</td><td><span class="badge-req">Required</span> Domain URL (unique)</td></tr>
                            <tr><td><code>default_language</code></td><td>select</td><td><span class="badge-req">Required</span> Language code (en, ar, etc.)</td></tr>
                            <tr><td><code>is_active</code></td><td>toggle</td><td>Enable/disable domain</td></tr>
                            <tr><td><code>logo</code></td><td>file</td><td>PNG/JPG/SVG/WebP, max 2MB</td></tr>
                            <tr><td><code>favicon</code></td><td>file</td><td>ICO/PNG/SVG, max 512KB</td></tr>
                            <tr><td><code>meta_title</code></td><td>text</td><td>SEO title</td></tr>
                            <tr><td><code>meta_description</code></td><td>textarea</td><td>SEO description</td></tr>
                            <tr><td><code>canonical_url</code></td><td>text</td><td>Canonical URL</td></tr>
                        </tbody>
                    </table>

                    <h6 class="doc-section-title">Content Pages (WYSIWYG)</h6>
                    <p>Each domain has three content pages with Summernote rich text editor:</p>
                    <ul class="feature-list">
                        <li><strong>About Us</strong> &mdash; Content + meta title, description, canonical URL</li>
                        <li><strong>Privacy Policy</strong> &mdash; Content + meta title, description, canonical URL</li>
                        <li><strong>Terms &amp; Conditions</strong> &mdash; Content + meta title, description, canonical URL</li>
                    </ul>

                    <h6 class="doc-section-title">Hero Slides</h6>
                    <p>Manage homepage hero carousel slides per domain:</p>
                    <ul class="feature-list">
                        <li>Upload image with title, subtitle, and description</li>
                        <li>Drag-and-drop reordering via sort_order</li>
                        <li>Toggle active/inactive per slide</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- LOCATIONS --}}
        <div id="admin-locations" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#0891b2,#22d3ee);"><i class='bx bx-map'></i></div>
                <div><h5>Locations</h5><p>Geographic areas with domain assignment and SEO</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Locations represent geographic areas (e.g., Downtown Dubai, Dubai Marina). Hotels are assigned to locations, and locations are assigned to domains.</p>

                    <h6 class="doc-section-title">Form Fields</h6>
                    <table class="param-table">
                        <thead><tr><th>Field</th><th>Type</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td><code>name</code></td><td>text</td><td><span class="badge-req">Required</span> Location name</td></tr>
                            <tr><td><code>city</code></td><td>text</td><td><span class="badge-req">Required</span> City name</td></tr>
                            <tr><td><code>country</code></td><td>text</td><td><span class="badge-req">Required</span> Country name</td></tr>
                            <tr><td><code>description</code></td><td>textarea</td><td>Full description</td></tr>
                            <tr><td><code>short_description</code></td><td>text</td><td>Max 255 characters</td></tr>
                            <tr><td><code>latitude / longitude</code></td><td>number</td><td>Geo-coordinates</td></tr>
                            <tr><td><code>image</code></td><td>file</td><td>Featured image</td></tr>
                            <tr><td><code>domains</code></td><td>checkboxes</td><td>Assign to multiple domains</td></tr>
                            <tr><td><code>is_active / is_featured</code></td><td>toggle</td><td>Status flags</td></tr>
                            <tr><td><code>meta_title, meta_description, meta_keywords, canonical_url</code></td><td>text/textarea</td><td>SEO fields</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- HOTELS --}}
        <div id="admin-hotels" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#3b82f6,#60a5fa);"><i class='bx bx-building'></i></div>
                <div><h5>Hotels</h5><p>Full hotel management with images, amenities, and domain sync</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Hotels are the core entity. Each hotel belongs to a location, has room types, images across 10 categories, amenities, and can be listed on multiple domains.</p>

                    <h6 class="doc-section-title">Form Tabs</h6>
                    <ul class="feature-list">
                        <li><strong>Basic Info</strong> &mdash; Name, star rating (1-5), location (Select2 AJAX), address, description, short description</li>
                        <li><strong>Contact &amp; Location</strong> &mdash; Phone, email, website, check-in/check-out times, latitude/longitude</li>
                        <li><strong>Settings</strong> &mdash; Active, featured, beach access, family friendly flags, cancellation policy</li>
                        <li><strong>Amenities</strong> &mdash; Checkboxes grouped by category</li>
                        <li><strong>SEO</strong> &mdash; Meta title, description, keywords, canonical URL, FAQ data (JSON)</li>
                        <li><strong>Domains</strong> &mdash; Assign to multiple domains via checkboxes</li>
                    </ul>

                    <h6 class="doc-section-title">Image Management</h6>
                    <p>Upload and manage images across 10 categories:</p>
                    <div class="img-cat-grid">
                        <span class="img-cat-item">General</span>
                        <span class="img-cat-item">Bedroom</span>
                        <span class="img-cat-item">Bathroom</span>
                        <span class="img-cat-item">Lobby</span>
                        <span class="img-cat-item">Dining</span>
                        <span class="img-cat-item">Pool</span>
                        <span class="img-cat-item">Gym</span>
                        <span class="img-cat-item">Exterior</span>
                        <span class="img-cat-item">Spa</span>
                        <span class="img-cat-item">Other</span>
                    </div>
                    <ul class="feature-list" style="margin-top:.5rem;">
                        <li>Upload multiple images per category</li>
                        <li>Set primary image (shown in listings)</li>
                        <li>Edit alt text and caption per image</li>
                        <li>Delete individual images</li>
                    </ul>

                    <h6 class="doc-section-title">Denormalized Fields</h6>
                    <p>These fields are auto-calculated by <code>HotelObserver</code>:</p>
                    <ul class="feature-list">
                        <li><code>avg_rating</code> &mdash; Average of approved review ratings</li>
                        <li><code>total_reviews</code> &mdash; Count of approved reviews</li>
                        <li><code>min_price</code> &mdash; Lowest room type base price</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ROOM TYPES --}}
        <div id="admin-rooms" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);"><i class='bx bx-bed'></i></div>
                <div><h5>Room Types</h5><p>Nested under hotels &mdash; pricing, capacity, amenities</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Room types are always managed within a hotel context. Each room type has its own pricing, capacity limits, images, and amenities.</p>

                    <h6 class="doc-section-title">Form Fields</h6>
                    <table class="param-table">
                        <thead><tr><th>Field</th><th>Type</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td><code>name</code></td><td>text</td><td><span class="badge-req">Required</span> Room type name</td></tr>
                            <tr><td><code>description</code></td><td>textarea</td><td>Full description</td></tr>
                            <tr><td><code>base_price</code></td><td>number</td><td><span class="badge-req">Required</span> Price per night (AED)</td></tr>
                            <tr><td><code>max_guests</code></td><td>number</td><td><span class="badge-req">Required</span> Max total guests</td></tr>
                            <tr><td><code>max_adults / max_children</code></td><td>number</td><td><span class="badge-req">Required</span> Adult/child capacity</td></tr>
                            <tr><td><code>bed_type</code></td><td>text</td><td>e.g., King, Twin, Queen</td></tr>
                            <tr><td><code>room_size_sqm</code></td><td>number</td><td>Room size in square meters</td></tr>
                            <tr><td><code>total_rooms</code></td><td>number</td><td><span class="badge-req">Required</span> Inventory count</td></tr>
                            <tr><td><code>images</code></td><td>file (multiple)</td><td>Room images, max 5MB each</td></tr>
                            <tr><td><code>amenities</code></td><td>checkboxes</td><td>Room-level amenities</td></tr>
                            <tr><td><code>is_active</code></td><td>toggle</td><td>Active/inactive</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- AMENITIES --}}
        <div id="admin-amenities" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#eab308,#facc15);"><i class='bx bx-star'></i></div>
                <div><h5>Amenities</h5><p>Manage amenity catalog with icons and categories</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Amenities can be assigned to both hotels and room types. They are organized by category and rendered with Boxicons.</p>
                    <table class="param-table">
                        <thead><tr><th>Field</th><th>Type</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td><code>name</code></td><td>text</td><td><span class="badge-req">Required</span> Amenity name</td></tr>
                            <tr><td><code>icon</code></td><td>text</td><td>Boxicons class (e.g., <code>bx-wifi</code>)</td></tr>
                            <tr><td><code>category</code></td><td>text</td><td>Category grouping (general, room, leisure, etc.)</td></tr>
                            <tr><td><code>is_active</code></td><td>toggle</td><td>Active/inactive</td></tr>
                        </tbody>
                    </table>
                    <p style="margin-top:.5rem;font-size:.82rem;color:var(--text-secondary);">Slug is auto-generated from the name. 28 amenities seeded across 6 categories.</p>
                </div>
            </div>
        </div>

        {{-- PRICING RULES --}}
        <div id="admin-pricing" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class='bx bx-dollar-circle'></i></div>
                <div><h5>Pricing Rules</h5><p>5 rule types with flexible targeting and priority system</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Pricing rules adjust hotel prices dynamically. Rules are applied in priority order by the <code>PricingService</code>: base price &rarr; availability override &rarr; pricing rules &rarr; tax &amp; tourism fee.</p>

                    <h6 class="doc-section-title">5 Rule Types</h6>
                    <table class="param-table">
                        <thead><tr><th>Type</th><th>Trigger</th><th>Example</th></tr></thead>
                        <tbody>
                            <tr><td><code>domain_markup</code></td><td>Applied per domain</td><td>+10% for premium domains</td></tr>
                            <tr><td><code>seasonal</code></td><td>Date range (recurring)</td><td>+15% during peak season</td></tr>
                            <tr><td><code>date_range</code></td><td>Specific date period</td><td>-20% for Eid holiday week</td></tr>
                            <tr><td><code>category</code></td><td>Room category/type</td><td>+50 AED for suites</td></tr>
                            <tr><td><code>day_of_week</code></td><td>Specific weekdays</td><td>+10% on Fri &amp; Sat</td></tr>
                        </tbody>
                    </table>

                    <h6 class="doc-section-title">Scope & Targeting</h6>
                    <p>Each rule can optionally target a specific domain, hotel, room type, or location. Leave blank to apply globally.</p>

                    <h6 class="doc-section-title">Form Fields</h6>
                    <table class="param-table">
                        <thead><tr><th>Field</th><th>Type</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td><code>name</code></td><td>text</td><td><span class="badge-req">Required</span> Rule name</td></tr>
                            <tr><td><code>type</code></td><td>radio</td><td><span class="badge-req">Required</span> One of the 5 rule types</td></tr>
                            <tr><td><code>adjustment_type</code></td><td>select</td><td><span class="badge-req">Required</span> <code>percentage</code> or <code>fixed_amount</code></td></tr>
                            <tr><td><code>adjustment_value</code></td><td>number</td><td><span class="badge-req">Required</span> Amount or percentage value</td></tr>
                            <tr><td><code>priority</code></td><td>number</td><td>Higher = applied first (default: 0)</td></tr>
                            <tr><td><code>domain_id</code></td><td>Select2</td><td>Target domain (optional)</td></tr>
                            <tr><td><code>hotel_id</code></td><td>Select2</td><td>Target hotel (optional)</td></tr>
                            <tr><td><code>room_type_id</code></td><td>Select2</td><td>Target room type (optional)</td></tr>
                            <tr><td><code>start_date / end_date</code></td><td>date</td><td>For date_range and seasonal types</td></tr>
                            <tr><td><code>days_of_week</code></td><td>checkboxes</td><td>For day_of_week type (Mon-Sun)</td></tr>
                            <tr><td><code>is_active</code></td><td>toggle</td><td>Enable/disable rule</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- BOOKINGS --}}
        <div id="admin-bookings" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#f97316,#fb923c);"><i class='bx bx-calendar-check'></i></div>
                <div><h5>Bookings & Payments</h5><p>View, manage status, record payments, process refunds</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Bookings are created via the public API and managed here. The admin panel provides listing with filters, detail view, status management, and manual payment recording.</p>

                    <h6 class="doc-section-title">Booking Status Flow</h6>
                    <div class="status-flow">
                        <span class="status-step" style="background:#f59e0b;">pending</span>
                        <i class='bx bx-right-arrow-alt status-arrow'></i>
                        <span class="status-step" style="background:#3b82f6;">paid</span>
                        <i class='bx bx-right-arrow-alt status-arrow'></i>
                        <span class="status-step" style="background:#22c55e;">confirmed</span>
                        <i class='bx bx-right-arrow-alt status-arrow'></i>
                        <span class="status-step" style="background:#8b5cf6;">completed</span>
                    </div>
                    <div class="status-flow" style="margin-top:.25rem;">
                        <span style="font-size:.75rem;color:var(--text-muted);">Or:</span>
                        <span class="status-step" style="background:#ef4444;">cancelled</span>
                        <span class="status-step" style="background:#6b7280;">refunded</span>
                    </div>

                    <h6 class="doc-section-title">Features</h6>
                    <ul class="feature-list">
                        <li><strong>Search &amp; filter</strong> &mdash; By reference number, guest name, email, and status</li>
                        <li><strong>Status update</strong> &mdash; Change booking status with automatic timestamp tracking</li>
                        <li><strong>Manual cash payment</strong> &mdash; Record cash, bank transfer, card, or cheque payments</li>
                        <li><strong>Refund processing</strong> &mdash; Quick refund action</li>
                        <li><strong>Detail view</strong> &mdash; Guest info, stay details, pricing breakdown, payment history</li>
                        <li><strong>Domain scoping</strong> &mdash; Users see only bookings from their assigned domains</li>
                    </ul>

                    <h6 class="doc-section-title">Payment Methods (Manual)</h6>
                    <table class="param-table">
                        <thead><tr><th>Method</th><th>Transaction ID Prefix</th></tr></thead>
                        <tbody>
                            <tr><td>Cash</td><td><code>CASH-</code></td></tr>
                            <tr><td>Bank Transfer</td><td><code>BT-</code></td></tr>
                            <tr><td>Card (POS)</td><td><code>POS-</code></td></tr>
                            <tr><td>Cheque</td><td><code>CHQ-</code></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- REVIEWS --}}
        <div id="admin-reviews" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#06b6d4,#22d3ee);"><i class='bx bx-message-square-dots'></i></div>
                <div><h5>Reviews</h5><p>Moderation &mdash; approve, reject, and reply to guest reviews</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Reviews are submitted via the public API and require admin moderation before they become visible on the website.</p>
                    <ul class="feature-list">
                        <li><strong>Approve / Reject</strong> &mdash; Toggle review visibility</li>
                        <li><strong>Admin Reply</strong> &mdash; Add official responses to reviews (sets <code>replied_at</code> timestamp)</li>
                        <li><strong>Star Rating Display</strong> &mdash; 1-5 stars with visual filled/empty stars</li>
                        <li><strong>Auto Recalculation</strong> &mdash; Approving/deleting reviews triggers <code>HotelObserver</code> to update <code>avg_rating</code> and <code>total_reviews</code></li>
                        <li><strong>Domain Scoping</strong> &mdash; Users see only reviews for hotels in their assigned domains</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- USERS & ROLES --}}
        <div id="admin-users" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#ec4899,#f472b6);"><i class='bx bx-group'></i></div>
                <div><h5>Users & Roles</h5><p>User management with role assignment and domain scoping</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <h6 class="doc-section-title">User Management</h6>
                    <table class="param-table">
                        <thead><tr><th>Field</th><th>Type</th><th>Description</th></tr></thead>
                        <tbody>
                            <tr><td><code>name</code></td><td>text</td><td><span class="badge-req">Required</span> Full name</td></tr>
                            <tr><td><code>email</code></td><td>email</td><td><span class="badge-req">Required</span> Unique email</td></tr>
                            <tr><td><code>password</code></td><td>password</td><td><span class="badge-req">Required</span> Min 8 chars (with confirmation)</td></tr>
                            <tr><td><code>phone</code></td><td>text</td><td>Phone number</td></tr>
                            <tr><td><code>role</code></td><td>select</td><td><span class="badge-req">Required</span> Single role assignment</td></tr>
                            <tr><td><code>domains</code></td><td>checkboxes</td><td>Domain access restriction</td></tr>
                            <tr><td><code>is_active</code></td><td>toggle</td><td>Enable/disable account</td></tr>
                        </tbody>
                    </table>

                    <h6 class="doc-section-title">Role Management</h6>
                    <ul class="feature-list">
                        <li>Create custom roles with permission assignment</li>
                        <li>Permissions displayed in organized categories (Domains, Hotels, Bookings, etc.)</li>
                        <li>Admin role is protected and cannot be deleted</li>
                        <li>Each role shows user count in the listing</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ANALYTICS --}}
        <div id="admin-analytics" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#6366f1,#818cf8);"><i class='bx bx-bar-chart-alt-2'></i></div>
                <div><h5>Analytics</h5><p>Domain analytics with SEO metrics and date filtering</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <p>Analytics aggregates daily <code>DomainAnalytics</code> records. Filter by domain and date range (default: last 30 days).</p>

                    <h6 class="doc-section-title">Metrics</h6>
                    <ul class="feature-list">
                        <li><strong>Revenue</strong> &mdash; Total revenue, by domain, by hotel (top 10)</li>
                        <li><strong>Bookings</strong> &mdash; Total bookings, breakdown by status, hotel performance table</li>
                        <li><strong>Page Views</strong> &mdash; Total page views across domains</li>
                        <li><strong>Average Booking Value</strong> &mdash; Revenue / bookings</li>
                    </ul>

                    <h6 class="doc-section-title">SEO Summary</h6>
                    <ul class="feature-list">
                        <li>Organic traffic, impressions, clicks</li>
                        <li>Avg CTR (click-through rate), avg position, avg bounce rate</li>
                        <li>Top 10 keywords (with clicks, impressions, CTR, position)</li>
                        <li>Top 10 landing pages (with views, bounce rate)</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ARTISAN COMMANDS --}}
        <div id="admin-commands" class="mb-section">
            <div class="section-header">
                <div class="section-icon" style="background:linear-gradient(135deg,#78716c,#a8a29e);"><i class='bx bx-terminal'></i></div>
                <div><h5>Artisan Commands</h5><p>Custom CLI commands for scheduled tasks</p></div>
            </div>
            <div class="doc-card">
                <div class="doc-card-body">
                    <h6 class="doc-section-title">Development</h6>
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode(this)"><i class='bx bx-copy'></i> Copy</button>
                        <pre>composer setup          # First-time setup (migrate, seed, etc.)
composer dev            # Start development server
composer test           # Run test suite</pre>
                    </div>

                    <h6 class="doc-section-title">Scheduled Commands</h6>
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode(this)"><i class='bx bx-copy'></i> Copy</button>
                        <pre># Execute due scheduled price updates
php artisan pricing:execute-scheduled

# Flush cached analytics and aggregate daily stats
php artisan analytics:aggregate</pre>
                    </div>

                    <h6 class="doc-section-title">Linting</h6>
                    <div class="code-block">
                        <button class="copy-btn" onclick="copyCode(this)"><i class='bx bx-copy'></i> Copy</button>
                        <pre>./vendor/bin/pint       # Laravel Pint code style fixer</pre>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
