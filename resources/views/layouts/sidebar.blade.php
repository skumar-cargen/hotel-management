<aside class="admin-sidebar" id="adminSidebar">
    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class='bx bxs-building-house'></i>
        </div>
        <div class="brand-text">
            <h4>Dubai<span>Apartments</span></h4>
            <p>Admin Panel</p>
        </div>
        <button class="sidebar-collapse-btn d-none d-lg-flex" id="sidebarCollapseBtn" title="Collapse sidebar">
            <i class='bx bx-chevrons-left'></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav" id="sidebarNav">
        {{-- Main --}}
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                <div class="nav-icon"><i class='bx bxs-dashboard'></i></div>
                <span class="nav-text">Dashboard</span>
                @if(($pendingBookings ?? 0) > 0)
                <span class="nav-badge bg-danger">{{ $pendingBookings ?? 0 }}</span>
                @endif
            </a>
        </div>

        {{-- Multi-Domain --}}
        @can('manage domains')
        <div class="nav-section">
            <div class="nav-section-title">Multi-Domain</div>
            <a href="{{ route('admin.domains.index') }}" class="nav-item {{ request()->routeIs('admin.domains.*') ? 'active' : '' }}" data-tooltip="Domains">
                <div class="nav-icon"><i class='bx bx-globe'></i></div>
                <span class="nav-text">Domains</span>
            </a>
        </div>
        @endcan

        {{-- Content Management --}}
        <div class="nav-section">
            <div class="nav-section-title">Content</div>
            @can('manage locations')
            <a href="{{ route('admin.locations.index') }}" class="nav-item {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" data-tooltip="Locations">
                <div class="nav-icon"><i class='bx bx-map'></i></div>
                <span class="nav-text">Locations</span>
            </a>
            @endcan
            @can('manage hotels')
            <a href="{{ route('admin.hotels.index') }}" class="nav-item {{ request()->routeIs('admin.hotels.*') || request()->routeIs('admin.room-types.*') ? 'active' : '' }}" data-tooltip="Hotels">
                <div class="nav-icon"><i class='bx bx-building'></i></div>
                <span class="nav-text">Hotels</span>
            </a>
            <a href="{{ route('admin.amenities.index') }}" class="nav-item {{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}" data-tooltip="Amenities">
                <div class="nav-icon"><i class='bx bx-star'></i></div>
                <span class="nav-text">Amenities</span>
            </a>
            @endcan
            @can('manage careers')
            <a href="{{ route('admin.careers.index') }}" class="nav-item {{ request()->routeIs('admin.careers.*') ? 'active' : '' }}" data-tooltip="Careers">
                <div class="nav-icon"><i class='bx bx-briefcase'></i></div>
                <span class="nav-text">Careers</span>
            </a>
            <a href="{{ route('admin.career-applications.index') }}" class="nav-item {{ request()->routeIs('admin.career-applications.*') ? 'active' : '' }}" data-tooltip="Applications">
                <div class="nav-icon"><i class='bx bx-user-check'></i></div>
                <span class="nav-text">Applications</span>
                @if(($newApplications ?? 0) > 0)
                <span class="nav-badge bg-info">{{ $newApplications ?? 0 }}</span>
                @endif
            </a>
            @endcan
            @can('manage blogs')
            <a href="{{ route('admin.blog-posts.index') }}" class="nav-item {{ request()->routeIs('admin.blog-posts.*') ? 'active' : '' }}" data-tooltip="Blog Posts">
                <div class="nav-icon"><i class='bx bx-news'></i></div>
                <span class="nav-text">Blog Posts</span>
            </a>
            <a href="{{ route('admin.blog-categories.index') }}" class="nav-item {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}" data-tooltip="Blog Categories">
                <div class="nav-icon"><i class='bx bx-category'></i></div>
                <span class="nav-text">Blog Categories</span>
            </a>
            @endcan
        </div>

        {{-- Revenue --}}
        @can('manage pricing')
        <div class="nav-section">
            <div class="nav-section-title">Revenue</div>
            <a href="{{ route('admin.pricing-rules.index') }}" class="nav-item {{ request()->routeIs('admin.pricing-rules.*') ? 'active' : '' }}" data-tooltip="Pricing">
                <div class="nav-icon"><i class='bx bx-dollar-circle'></i></div>
                <span class="nav-text">Pricing Rules</span>
            </a>
            <a href="{{ route('admin.deals.index') }}" class="nav-item {{ request()->routeIs('admin.deals.*') ? 'active' : '' }}" data-tooltip="Deals">
                <div class="nav-icon"><i class='bx bx-purchase-tag'></i></div>
                <span class="nav-text">Deals</span>
            </a>
        </div>
        @endcan

        {{-- Bookings & Reviews --}}
        @can('manage bookings')
        <div class="nav-section">
            <div class="nav-section-title">Bookings</div>
            <a href="{{ route('admin.bookings.index') }}" class="nav-item {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" data-tooltip="Bookings">
                <div class="nav-icon"><i class='bx bx-calendar-check'></i></div>
                <span class="nav-text">All Bookings</span>
            </a>
        @endcan
            @can('manage reviews')
            <a href="{{ route('admin.reviews.index') }}" class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" data-tooltip="Reviews">
                <div class="nav-icon"><i class='bx bx-message-square-dots'></i></div>
                <span class="nav-text">Reviews</span>
                @if(($pendingReviews ?? 0) > 0)
                <span class="nav-badge bg-warning">{{ $pendingReviews ?? 0 }}</span>
                @endif
            </a>
            <a href="{{ route('admin.testimonials.index') }}" class="nav-item {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}" data-tooltip="Testimonials">
                <div class="nav-icon"><i class='bx bx-award'></i></div>
                <span class="nav-text">Testimonials</span>
            </a>
            @endcan
        </div>

        {{-- Inquiries --}}
        <div class="nav-section">
            <div class="nav-section-title">Inquiries</div>
            <a href="{{ route('admin.contact-inquiries.index') }}" class="nav-item {{ request()->routeIs('admin.contact-inquiries.*') ? 'active' : '' }}" data-tooltip="Contact Inquiries">
                <div class="nav-icon"><i class='bx bx-envelope'></i></div>
                <span class="nav-text">Contact Messages</span>
            </a>
        </div>

        {{-- Administration --}}
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            @can('view customers')
            <a href="{{ route('admin.customers.index') }}" class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" data-tooltip="Customers">
                <div class="nav-icon"><i class='bx bx-user-circle'></i></div>
                <span class="nav-text">Customers</span>
            </a>
            @endcan
            @can('manage users')
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" data-tooltip="Users">
                <div class="nav-icon"><i class='bx bx-group'></i></div>
                <span class="nav-text">Users</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" data-tooltip="Roles">
                <div class="nav-icon"><i class='bx bx-shield-quarter'></i></div>
                <span class="nav-text">Roles & Permissions</span>
            </a>
            @endcan
            @can('view analytics')
            <a href="{{ route('admin.analytics') }}" class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" data-tooltip="Analytics">
                <div class="nav-icon"><i class='bx bx-bar-chart-alt-2'></i></div>
                <span class="nav-text">Analytics</span>
            </a>
            @endcan
        </div>

        {{-- Documentation --}}
        <div class="nav-section">
            <div class="nav-section-title">Help</div>
            <a href="{{ route('admin.documentation') }}" class="nav-item {{ request()->routeIs('admin.documentation') ? 'active' : '' }}" data-tooltip="Documentation">
                <div class="nav-icon"><i class='bx bx-book-open'></i></div>
                <span class="nav-text">Documentation</span>
            </a>
        </div>

    </nav>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ Auth::user()->roles->first()?->name ?? 'Admin' }}</div>
            </div>
        </div>
    </div>
</aside>
