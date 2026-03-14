    <div class="docs-layout">
        <!-- TOC Sidebar -->
        <div class="docs-toc" id="apiToc">
            <h6>Getting Started</h6>
            <a href="#overview" class="toc-link active" data-tab="api"><i class='bx bx-info-circle'></i> Overview</a>
            <h6>Endpoints</h6>
            <a href="#domain-config" class="toc-link" data-tab="api"><i class='bx bx-cog'></i> Domain Config <span class="toc-count">1</span></a>
            <a href="#customer-auth" class="toc-link" data-tab="api"><i class='bx bx-lock-open'></i> Customer Auth <span class="toc-count">6</span></a>
            <a href="#customer-profile" class="toc-link" data-tab="api"><i class='bx bx-user'></i> Customer Profile <span class="toc-count">7</span></a>
            <a href="#hotels" class="toc-link" data-tab="api"><i class='bx bx-building'></i> Hotels <span class="toc-count">5</span></a>
            <a href="#locations" class="toc-link" data-tab="api"><i class='bx bx-map'></i> Locations <span class="toc-count">2</span></a>
            <a href="#availability" class="toc-link" data-tab="api"><i class='bx bx-calendar-check'></i> Availability <span class="toc-count">1</span></a>
            <a href="#pricing" class="toc-link" data-tab="api"><i class='bx bx-dollar-circle'></i> Pricing <span class="toc-count">1</span></a>
            <a href="#bookings" class="toc-link" data-tab="api"><i class='bx bx-receipt'></i> Bookings <span class="toc-count">5</span></a>
            <a href="#amenities" class="toc-link" data-tab="api"><i class='bx bx-star'></i> Amenities <span class="toc-count">1</span></a>
            <a href="#deals" class="toc-link" data-tab="api"><i class='bx bx-purchase-tag'></i> Deals <span class="toc-count">2</span></a>
            <a href="#testimonials" class="toc-link" data-tab="api"><i class='bx bx-message-rounded-dots'></i> Testimonials <span class="toc-count">1</span></a>
            <a href="#careers" class="toc-link" data-tab="api"><i class='bx bx-briefcase'></i> Careers <span class="toc-count">3</span></a>
            <a href="#pages" class="toc-link" data-tab="api"><i class='bx bx-file'></i> Pages <span class="toc-count">1</span></a>
            <a href="#contact" class="toc-link" data-tab="api"><i class='bx bx-envelope'></i> Contact <span class="toc-count">1</span></a>
            <a href="#newsletter" class="toc-link" data-tab="api"><i class='bx bx-mail-send'></i> Newsletter <span class="toc-count">2</span></a>
            <a href="#search" class="toc-link" data-tab="api"><i class='bx bx-search'></i> Search <span class="toc-count">1</span></a>
            <a href="#payment-callback" class="toc-link" data-tab="api"><i class='bx bx-credit-card'></i> Payment Callback <span class="toc-count">1</span></a>
        </div>

        <!-- Content -->
        <div class="docs-content">

            {{-- ================================================================ --}}
            {{-- OVERVIEW --}}
            {{-- ================================================================ --}}
            <div id="overview" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);"><i class='bx bx-info-circle'></i></div>
                    <div>
                        <h5>Overview</h5>
                        <p>Base URL, authentication, headers, response format</p>
                    </div>
                </div>

                <div class="endpoint-card open">
                    <div class="endpoint-body" style="display:block; border-top:none; padding-top:1rem;">
                        <h6 class="ep-section" style="margin-top:0;">Base URL</h6>
                        <div class="code-block">
                            <button class="copy-btn" onclick="copyCode(this)"><i class='bx bx-copy'></i> Copy</button>
                            <pre>https://&#123;&#123;domain&#125;&#125;/api/v1</pre>
                        </div>

                        <h6 class="ep-section">Required Headers</h6>
                        <table class="param-table">
                            <thead><tr><th>Header</th><th>Value</th><th>Description</th></tr></thead>
                            <tbody>
                                <tr><td><code>X-Domain</code></td><td><code>your-domain.com</code></td><td><span class="badge-req">Required</span> Domain identifier. Resolved by middleware, cached 1 hour.</td></tr>
                                <tr><td><code>Content-Type</code></td><td><code>application/json</code></td><td><span class="badge-req">Required</span> For POST/PUT requests.</td></tr>
                                <tr><td><code>Accept</code></td><td><code>application/json</code></td><td><span class="badge-req">Required</span> All requests should send this header.</td></tr>
                                <tr><td><code>Authorization</code></td><td><code>Bearer &#123;token&#125;</code></td><td><span class="badge-opt">Optional</span> Required for authenticated customer endpoints.</td></tr>
                            </tbody>
                        </table>

                        <h6 class="ep-section">Authentication</h6>
                        <p style="font-size:.82rem;color:var(--text-secondary);line-height:1.7;">
                            Public endpoints require only the <code>X-Domain</code> header. Customer-authenticated endpoints require a Sanctum bearer token obtained via the <code>/auth/login</code> or <code>/auth/register</code> endpoints. Include the token in the <code>Authorization</code> header as <code>Bearer &#123;token&#125;</code>.
                        </p>

                        <h6 class="ep-section">Success Response Format</h6>
                        <div class="code-block">
                            <span class="code-label">JSON</span>
                            <button class="copy-btn" onclick="copyCode(this)"><i class='bx bx-copy'></i> Copy</button>
                            <pre>{
    "data": { ... },
    "meta": {              // Only for paginated responses
        "current_page": 1,
        "last_page": 5,
        "per_page": 12,
        "total": 56
    }
}</pre>
                        </div>

                        <h6 class="ep-section">Error Response Format</h6>
                        <div class="code-block">
                            <span class="code-label">JSON — 422 Validation Error</span>
                            <button class="copy-btn" onclick="copyCode(this)"><i class='bx bx-copy'></i> Copy</button>
                            <pre>{
    "message": "The given data was invalid.",
    "errors": {
        "field_name": [
            "The field name is required."
        ]
    }
}</pre>
                        </div>
                        <div class="code-block">
                            <span class="code-label">JSON — 401 / 403 / 404</span>
                            <button class="copy-btn" onclick="copyCode(this)"><i class='bx bx-copy'></i> Copy</button>
                            <pre>{
    "error": "not_found",
    "message": "The requested resource was not found."
}</pre>
                        </div>

                        <h6 class="ep-section">Rate Limiting</h6>
                        <p style="font-size:.82rem;color:var(--text-secondary);line-height:1.7;">
                            All API routes are rate limited. When a limit is exceeded, the API returns <code>429 Too Many Requests</code>. Specific limits are documented per endpoint. Rate limit headers are included in responses:
                        </p>
                        <table class="param-table">
                            <thead><tr><th>Header</th><th>Description</th></tr></thead>
                            <tbody>
                                <tr><td><code>X-RateLimit-Limit</code></td><td>Maximum requests per window</td></tr>
                                <tr><td><code>X-RateLimit-Remaining</code></td><td>Remaining requests in current window</td></tr>
                                <tr><td><code>Retry-After</code></td><td>Seconds until the rate limit resets (only on 429)</td></tr>
                            </tbody>
                        </table>

                        <h6 class="ep-section">HTTP Status Codes</h6>
                        <table class="param-table">
                            <thead><tr><th>Code</th><th>Description</th></tr></thead>
                            <tbody>
                                <tr><td><code>200</code></td><td>Success</td></tr>
                                <tr><td><code>201</code></td><td>Created — Resource successfully created</td></tr>
                                <tr><td><code>401</code></td><td>Unauthorized — Invalid or missing authentication</td></tr>
                                <tr><td><code>403</code></td><td>Forbidden — Insufficient permissions</td></tr>
                                <tr><td><code>404</code></td><td>Not Found — Resource does not exist</td></tr>
                                <tr><td><code>422</code></td><td>Validation Error — Request data failed validation</td></tr>
                                <tr><td><code>429</code></td><td>Too Many Requests — Rate limit exceeded</td></tr>
                                <tr><td><code>500</code></td><td>Server Error — Internal server error</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================================ --}}
            {{-- DOMAIN CONFIG --}}
            {{-- ================================================================ --}}
            <div id="domain-config" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #6366f1, #818cf8);"><i class='bx bx-cog'></i></div>
                    <div><h5>Domain Configuration</h5><p>Retrieve domain settings, SEO, tracking IDs, and hero slides</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/domain/config',
                    'desc' => 'Get domain configuration',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'params' => [],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "domain": {
            "name": "Dubai Hotels",
            "slug": "dubai-hotels",
            "default_currency": "AED",
            "default_language": "en",
            "logo": "https://example.com/storage/logos/logo.png",
            "favicon": "https://example.com/storage/logos/favicon.ico"
        },
        "seo": {
            "meta_title": "Dubai Hotels - Best Deals",
            "meta_description": "Find the best hotel deals in Dubai",
            "meta_keywords": "dubai, hotels, booking",
            "canonical_url": "https://dubai-hotels.com",
            "og_image": "https://example.com/storage/og/image.jpg"
        },
        "tracking": {
            "google_analytics_id": "G-XXXXXXXXXX",
            "google_tag_manager_id": "GTM-XXXXXXX",
            "meta_pixel_id": "1234567890"
        },
        "pages": {
            "about_us": {
                "content": "<p>About us content...</p>",
                "meta_title": "About Us",
                "meta_description": "Learn about us",
                "canonical_url": "https://dubai-hotels.com/about"
            },
            "privacy_policy": null,
            "terms_conditions": null
        },
        "hero_slides": [
            {
                "image": "https://example.com/storage/slides/1.jpg",
                "title": "Welcome to Dubai",
                "subtitle": "Luxury Awaits",
                "description": "Discover premium hotels"
            }
        ]
    }
}'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- CUSTOMER AUTH --}}
            {{-- ================================================================ --}}
            <div id="customer-auth" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24);"><i class='bx bx-lock-open'></i></div>
                    <div><h5>Customer Authentication</h5><p>Register, login, OAuth, and password management</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/auth/register',
                    'desc' => 'Register a new customer',
                    'auth' => 'public',
                    'rate' => '5 / min',
                    'body' => [
                        ['field' => 'first_name', 'type' => 'string', 'req' => true, 'desc' => 'Max 255 characters'],
                        ['field' => 'last_name', 'type' => 'string', 'req' => true, 'desc' => 'Max 255 characters'],
                        ['field' => 'email', 'type' => 'string', 'req' => true, 'desc' => 'Valid email, unique among customers'],
                        ['field' => 'password', 'type' => 'string', 'req' => true, 'desc' => 'Min 8 characters'],
                        ['field' => 'password_confirmation', 'type' => 'string', 'req' => true, 'desc' => 'Must match password'],
                        ['field' => 'phone', 'type' => 'string', 'req' => false, 'desc' => 'Max 50 characters'],
                        ['field' => 'nationality', 'type' => 'string', 'req' => false, 'desc' => 'Max 100 characters'],
                    ],
                    'response_label' => '201 Created',
                    'response' => '{
    "data": {
        "customer": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com",
            "avatar_url": null
        },
        "token": "1|abc123def456...",
        "token_type": "Bearer"
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/auth/login',
                    'desc' => 'Login with email and password',
                    'auth' => 'public',
                    'rate' => '10 / min',
                    'body' => [
                        ['field' => 'email', 'type' => 'string', 'req' => true, 'desc' => 'Registered email address'],
                        ['field' => 'password', 'type' => 'string', 'req' => true, 'desc' => 'Account password'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "customer": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com",
            "avatar_url": null
        },
        "token": "2|xyz789ghi012...",
        "token_type": "Bearer"
    }
}',
                    'errors' => '401 — Invalid credentials or inactive account'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/auth/google',
                    'desc' => 'Login or register via Google OAuth',
                    'auth' => 'public',
                    'rate' => '10 / min',
                    'body' => [
                        ['field' => 'id_token', 'type' => 'string', 'req' => true, 'desc' => 'Google OAuth ID token'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "customer": {
            "id": 2,
            "first_name": "Jane",
            "last_name": "Smith",
            "email": "jane@gmail.com",
            "avatar_url": "https://lh3.googleusercontent.com/..."
        },
        "token": "3|mno345pqr678...",
        "token_type": "Bearer"
    }
}',
                    'errors' => '401 — Invalid Google token | 403 — Account deactivated'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/auth/forgot-password',
                    'desc' => 'Request a password reset link',
                    'auth' => 'public',
                    'rate' => '3 / min',
                    'body' => [
                        ['field' => 'email', 'type' => 'string', 'req' => true, 'desc' => 'Registered email address'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "Password reset link sent to your email."
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/auth/reset-password',
                    'desc' => 'Reset password with token',
                    'auth' => 'public',
                    'rate' => '5 / min',
                    'body' => [
                        ['field' => 'token', 'type' => 'string', 'req' => true, 'desc' => 'Reset token from email'],
                        ['field' => 'email', 'type' => 'string', 'req' => true, 'desc' => 'Email address'],
                        ['field' => 'password', 'type' => 'string', 'req' => true, 'desc' => 'New password, min 8 chars'],
                        ['field' => 'password_confirmation', 'type' => 'string', 'req' => true, 'desc' => 'Must match password'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "Password has been reset successfully."
    }
}',
                    'errors' => '422 — Invalid or expired token'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/customer/auth/logout',
                    'desc' => 'Logout (revoke current token)',
                    'auth' => 'token',
                    'rate' => 'Standard',
                    'params' => [],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "Logged out successfully."
    }
}'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- CUSTOMER PROFILE --}}
            {{-- ================================================================ --}}
            <div id="customer-profile" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);"><i class='bx bx-user'></i></div>
                    <div><h5>Customer Profile</h5><p>Profile management, avatar upload, password change, bookings history</p></div>
                </div>

                <div class="info-note"><strong>Authentication required:</strong> All endpoints in this section require a valid Bearer token.</div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/customer/profile',
                    'desc' => 'Get current customer profile',
                    'auth' => 'token',
                    'rate' => 'Standard',
                    'params' => [],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "phone": "+971501234567",
        "nationality": "US",
        "avatar_url": "https://example.com/storage/avatars/1.jpg",
        "has_password": true,
        "is_google_user": false,
        "email_verified_at": "2025-01-15T10:30:00.000000Z",
        "created_at": "2025-01-01T08:00:00.000000Z"
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'PUT',
                    'path' => '/api/v1/customer/profile',
                    'desc' => 'Update customer profile',
                    'auth' => 'token',
                    'rate' => 'Standard',
                    'body' => [
                        ['field' => 'first_name', 'type' => 'string', 'req' => false, 'desc' => 'Max 255 characters'],
                        ['field' => 'last_name', 'type' => 'string', 'req' => false, 'desc' => 'Max 255 characters'],
                        ['field' => 'phone', 'type' => 'string', 'req' => false, 'desc' => 'Max 50 characters'],
                        ['field' => 'nationality', 'type' => 'string', 'req' => false, 'desc' => 'Max 100 characters'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "Profile updated successfully.",
        "customer": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "phone": "+971501234567",
            "nationality": "US",
            "avatar_url": "https://example.com/storage/avatars/1.jpg"
        }
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/customer/avatar',
                    'desc' => 'Upload profile avatar',
                    'auth' => 'token',
                    'rate' => 'Standard',
                    'body' => [
                        ['field' => 'avatar', 'type' => 'file', 'req' => true, 'desc' => 'Image file: jpeg, png, jpg, webp. Max 2MB.'],
                    ],
                    'note' => 'Send as multipart/form-data, not JSON.',
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "Avatar uploaded successfully.",
        "avatar_url": "https://example.com/storage/avatars/1_abc123.jpg"
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'PUT',
                    'path' => '/api/v1/customer/password',
                    'desc' => 'Change password',
                    'auth' => 'token',
                    'rate' => 'Standard',
                    'body' => [
                        ['field' => 'current_password', 'type' => 'string', 'req' => true, 'desc' => 'Current password (only if customer has one)'],
                        ['field' => 'new_password', 'type' => 'string', 'req' => true, 'desc' => 'Min 8 characters'],
                        ['field' => 'new_password_confirmation', 'type' => 'string', 'req' => true, 'desc' => 'Must match new_password'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "Password changed successfully."
    }
}',
                    'errors' => '422 — Incorrect current password'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/customer/bookings',
                    'desc' => 'List customer bookings',
                    'auth' => 'token',
                    'rate' => 'Standard',
                    'query' => [
                        ['field' => 'status', 'type' => 'string', 'req' => false, 'desc' => 'Filter: pending, confirmed, cancelled, completed'],
                        ['field' => 'per_page', 'type' => 'integer', 'req' => false, 'desc' => 'Items per page (default: 10)'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": [
        {
            "reference_number": "BK-A1B2C3D4",
            "status": "confirmed",
            "hotel": {
                "name": "Grand Hotel Dubai",
                "slug": "grand-hotel-dubai",
                "star_rating": 5,
                "address": "Sheikh Zayed Road"
            },
            "room_type": {
                "name": "Deluxe Suite",
                "slug": "deluxe-suite"
            },
            "check_in_date": "2025-03-15",
            "check_out_date": "2025-03-18",
            "num_nights": 3,
            "num_adults": 2,
            "num_children": 0,
            "num_rooms": 1,
            "room_price_per_night": 850.00,
            "subtotal": 2550.00,
            "tax_amount": 127.50,
            "tax_percentage": 5,
            "tourism_fee": 60.00,
            "service_charge": 0.00,
            "total_amount": 2737.50,
            "currency": "AED",
            "special_requests": null,
            "booked_at": "2025-03-01T12:00:00.000000Z",
            "confirmed_at": "2025-03-01T12:05:00.000000Z",
            "cancelled_at": null
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/customer/bookings/{reference}',
                    'desc' => 'Get booking detail',
                    'auth' => 'token',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'reference', 'type' => 'string', 'desc' => 'Booking reference number (e.g. BK-A1B2C3D4)'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "reference_number": "BK-A1B2C3D4",
        "status": "confirmed",
        "hotel": { "name": "Grand Hotel Dubai", "slug": "grand-hotel-dubai", "star_rating": 5, "address": "Sheikh Zayed Road" },
        "room_type": { "name": "Deluxe Suite", "slug": "deluxe-suite" },
        "check_in_date": "2025-03-15",
        "check_out_date": "2025-03-18",
        "num_nights": 3,
        "num_adults": 2,
        "num_children": 0,
        "num_rooms": 1,
        "room_price_per_night": 850.00,
        "subtotal": 2550.00,
        "tax_amount": 127.50,
        "tax_percentage": 5,
        "tourism_fee": 60.00,
        "service_charge": 0.00,
        "total_amount": 2737.50,
        "currency": "AED",
        "special_requests": null,
        "booked_at": "2025-03-01T12:00:00.000000Z",
        "confirmed_at": "2025-03-01T12:05:00.000000Z",
        "cancelled_at": null
    }
}',
                    'errors' => '404 — Booking not found'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'DELETE',
                    'path' => '/api/v1/customer/account',
                    'desc' => 'Delete customer account',
                    'auth' => 'token',
                    'rate' => 'Standard',
                    'params' => [],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "Your account has been deleted."
    }
}'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- HOTELS --}}
            {{-- ================================================================ --}}
            <div id="hotels" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #3b82f6, #60a5fa);"><i class='bx bx-building'></i></div>
                    <div><h5>Hotels</h5><p>List, search, detail, reviews</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/hotels',
                    'desc' => 'List hotels',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'query' => [
                        ['field' => 'location', 'type' => 'string', 'req' => false, 'desc' => 'Filter by location slug'],
                        ['field' => 'star_rating', 'type' => 'integer', 'req' => false, 'desc' => 'Filter by star rating (1-5)'],
                        ['field' => 'featured', 'type' => 'boolean', 'req' => false, 'desc' => 'Show featured hotels only'],
                        ['field' => 'sort', 'type' => 'string', 'req' => false, 'desc' => 'price_asc, price_desc, rating, name'],
                        ['field' => 'per_page', 'type' => 'integer', 'req' => false, 'desc' => 'Items per page (default: 12)'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": [
        {
            "id": 1,
            "name": "Grand Hotel Dubai",
            "slug": "grand-hotel-dubai",
            "star_rating": 5,
            "short_description": "Luxury 5-star hotel on Sheikh Zayed Road",
            "address": "Sheikh Zayed Road, Dubai",
            "avg_rating": 4.7,
            "total_reviews": 128,
            "min_price": 750.00,
            "is_beach_access": true,
            "is_family_friendly": true,
            "primary_image": {
                "image_url": "https://example.com/storage/hotels/1/main.jpg",
                "alt_text": "Grand Hotel Dubai"
            },
            "location": {
                "name": "Downtown Dubai",
                "slug": "downtown-dubai",
                "city": "Dubai"
            },
            "deals": [
                {
                    "title": "Summer Sale",
                    "slug": "summer-sale",
                    "discount_type": "percentage",
                    "discount_value": 20,
                    "end_date": "2025-09-30"
                }
            ]
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 12,
        "total": 28
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/hotels/search',
                    'desc' => 'Search hotels by keyword',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'query' => [
                        ['field' => 'q', 'type' => 'string', 'req' => true, 'desc' => 'Search query (min 2 characters)'],
                        ['field' => 'per_page', 'type' => 'integer', 'req' => false, 'desc' => 'Items per page (default: 12)'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '// Same structure as GET /hotels'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/hotels/{slug}',
                    'desc' => 'Get hotel detail',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'slug', 'type' => 'string', 'desc' => 'Hotel slug'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "id": 1,
        "name": "Grand Hotel Dubai",
        "slug": "grand-hotel-dubai",
        "star_rating": 5,
        "description": "Full hotel description...",
        "short_description": "Luxury 5-star hotel",
        "address": "Sheikh Zayed Road, Dubai",
        "latitude": 25.2048,
        "longitude": 55.2708,
        "phone": "+971-4-123-4567",
        "email": "info@grandhotel.com",
        "website": "https://grandhotel.com",
        "check_in_time": "15:00",
        "check_out_time": "12:00",
        "cancellation_policy": "Free cancellation up to 48h before check-in",
        "is_beach_access": true,
        "is_family_friendly": true,
        "avg_rating": 4.7,
        "total_reviews": 128,
        "min_price": 750.00,
        "faq_data": "[{\"q\":\"Is parking free?\",\"a\":\"Yes.\"}]",
        "images": [
            {
                "id": 1,
                "category": "exterior",
                "image_url": "https://example.com/storage/hotels/1/ext.jpg",
                "thumbnail_url": "https://example.com/storage/hotels/1/ext_thumb.jpg",
                "alt_text": "Hotel exterior",
                "caption": "Main entrance",
                "is_primary": true
            }
        ],
        "amenities": [
            { "id": 1, "name": "Free WiFi", "slug": "free-wifi", "icon": "bx-wifi", "category": "general" }
        ],
        "room_types": [
            {
                "id": 1,
                "name": "Deluxe Suite",
                "slug": "deluxe-suite",
                "description": "Spacious suite with city view",
                "max_guests": 3,
                "max_adults": 2,
                "max_children": 1,
                "bed_type": "King",
                "room_size_sqm": 45,
                "base_price": 850.00,
                "display_price": 750.00,
                "total_rooms": 10,
                "amenities": [],
                "images": []
            }
        ],
        "location": {
            "id": 1,
            "name": "Downtown Dubai",
            "slug": "downtown-dubai",
            "city": "Dubai",
            "country": "UAE"
        },
        "reviews_summary": {
            "avg_rating": 4.7,
            "total_reviews": 128,
            "recent_reviews": [
                {
                    "id": 1,
                    "guest_name": "Ahmed M.",
                    "rating": 5,
                    "title": "Excellent stay!",
                    "comment": "Amazing experience...",
                    "is_verified": true,
                    "created_at": "2025-02-20T10:00:00.000000Z"
                }
            ]
        },
        "deals": [],
        "meta": {
            "title": "Grand Hotel Dubai",
            "description": "SEO description",
            "keywords": "dubai, hotel, luxury"
        }
    }
}',
                    'errors' => '404 — Hotel not found'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/hotels/{slug}/reviews',
                    'desc' => 'Get hotel reviews (paginated)',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'slug', 'type' => 'string', 'desc' => 'Hotel slug'],
                    ],
                    'query' => [
                        ['field' => 'per_page', 'type' => 'integer', 'req' => false, 'desc' => 'Items per page (default: 10)'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": [
        {
            "id": 1,
            "guest_name": "Ahmed M.",
            "rating": 5,
            "title": "Excellent stay!",
            "comment": "Amazing experience from check-in to check-out.",
            "is_verified": true,
            "created_at": "2025-02-20T10:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 10,
        "total": 28
    }
}',
                    'errors' => '404 — Hotel not found'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/hotels/{slug}/reviews',
                    'desc' => 'Submit a review',
                    'auth' => 'public',
                    'rate' => '5 / min',
                    'route_params' => [
                        ['field' => 'slug', 'type' => 'string', 'desc' => 'Hotel slug'],
                    ],
                    'body' => [
                        ['field' => 'guest_name', 'type' => 'string', 'req' => true, 'desc' => 'Reviewer name, max 100 chars'],
                        ['field' => 'guest_email', 'type' => 'string', 'req' => true, 'desc' => 'Reviewer email, max 255 chars'],
                        ['field' => 'rating', 'type' => 'integer', 'req' => true, 'desc' => '1 to 5'],
                        ['field' => 'title', 'type' => 'string', 'req' => false, 'desc' => 'Review title, max 255 chars'],
                        ['field' => 'comment', 'type' => 'string', 'req' => true, 'desc' => 'Review text, max 5000 chars'],
                        ['field' => 'booking_reference', 'type' => 'string', 'req' => false, 'desc' => 'Booking ref for verification, max 50 chars'],
                    ],
                    'response_label' => '201 Created',
                    'response' => '{
    "data": {
        "message": "Thank you for your review! It will be visible after moderation."
    }
}',
                    'errors' => '404 — Hotel not found'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- LOCATIONS --}}
            {{-- ================================================================ --}}
            <div id="locations" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #10b981, #34d399);"><i class='bx bx-map'></i></div>
                    <div><h5>Locations</h5><p>List and detail with attached hotels</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/locations',
                    'desc' => 'List all locations',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'params' => [],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": [
        {
            "id": 1,
            "name": "Downtown Dubai",
            "slug": "downtown-dubai",
            "city": "Dubai",
            "country": "UAE",
            "description": "The heart of Dubai...",
            "short_description": "Iconic skyline area",
            "image_url": "https://example.com/storage/locations/downtown.jpg",
            "latitude": 25.1972,
            "longitude": 55.2744,
            "is_featured": true,
            "hotel_count": 15,
            "meta": {
                "title": "Hotels in Downtown Dubai",
                "description": "Find hotels in Downtown",
                "keywords": "downtown, dubai, hotels"
            }
        }
    ]
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/locations/{slug}',
                    'desc' => 'Get location detail with hotels',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'slug', 'type' => 'string', 'desc' => 'Location slug'],
                    ],
                    'query' => [
                        ['field' => 'per_page', 'type' => 'integer', 'req' => false, 'desc' => 'Hotels per page (default: 12)'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "location": {
            "id": 1,
            "name": "Downtown Dubai",
            "slug": "downtown-dubai",
            "city": "Dubai",
            "country": "UAE",
            "description": "The heart of Dubai...",
            "short_description": "Iconic skyline area",
            "image_url": "https://example.com/storage/locations/downtown.jpg",
            "latitude": 25.1972,
            "longitude": 55.2744,
            "is_featured": true,
            "hotel_count": 15,
            "meta": { "title": "...", "description": "...", "keywords": "..." }
        },
        "hotels": [
            { "id": 1, "name": "Grand Hotel Dubai", "slug": "grand-hotel-dubai", "..." : "..." }
        ]
    },
    "meta": {
        "current_page": 1,
        "last_page": 2,
        "per_page": 12,
        "total": 15
    }
}',
                    'errors' => '404 — Location not found'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- AVAILABILITY --}}
            {{-- ================================================================ --}}
            <div id="availability" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #06b6d4, #22d3ee);"><i class='bx bx-calendar-check'></i></div>
                    <div><h5>Availability</h5><p>Check room availability for date ranges</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/availability',
                    'desc' => 'Check room availability',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'query' => [
                        ['field' => 'room_type_id', 'type' => 'integer', 'req' => true, 'desc' => 'Room type ID'],
                        ['field' => 'check_in', 'type' => 'date', 'req' => true, 'desc' => 'YYYY-MM-DD, today or later'],
                        ['field' => 'check_out', 'type' => 'date', 'req' => true, 'desc' => 'YYYY-MM-DD, after check_in'],
                        ['field' => 'num_rooms', 'type' => 'integer', 'req' => false, 'desc' => 'Number of rooms, 1-10 (default: 1)'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "room_type_id": 1,
        "room_type_name": "Deluxe Suite",
        "check_in": "2025-03-15",
        "check_out": "2025-03-18",
        "num_rooms": 1,
        "available": true,
        "dates": [
            { "date": "2025-03-15", "available_rooms": 5, "is_closed": false, "has_availability": true },
            { "date": "2025-03-16", "available_rooms": 3, "is_closed": false, "has_availability": true },
            { "date": "2025-03-17", "available_rooms": 7, "is_closed": false, "has_availability": true }
        ]
    }
}',
                    'errors' => '404 — Room type not found'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- PRICING --}}
            {{-- ================================================================ --}}
            <div id="pricing" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #8b5cf6, #a78bfa);"><i class='bx bx-dollar-circle'></i></div>
                    <div><h5>Pricing</h5><p>Calculate price breakdown with all applicable rules</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/pricing/calculate',
                    'desc' => 'Calculate price for a stay',
                    'auth' => 'public',
                    'rate' => '30 / min',
                    'body' => [
                        ['field' => 'room_type_id', 'type' => 'integer', 'req' => true, 'desc' => 'Room type ID'],
                        ['field' => 'check_in', 'type' => 'date', 'req' => true, 'desc' => 'YYYY-MM-DD, today or later'],
                        ['field' => 'check_out', 'type' => 'date', 'req' => true, 'desc' => 'YYYY-MM-DD, after check_in'],
                        ['field' => 'num_rooms', 'type' => 'integer', 'req' => false, 'desc' => 'Number of rooms, 1-10 (default: 1)'],
                    ],
                    'note' => 'Price rules applied in order: base price → availability override → domain markup → seasonal → date range → category → day of week → tax & tourism fee.',
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "basePrice": 850.00,
        "finalPerNight": 765.00,
        "numNights": 3,
        "numRooms": 1,
        "subtotal": 2295.00,
        "taxPercentage": 5,
        "taxAmount": 114.75,
        "tourismFee": 60.00,
        "serviceCharge": 0.00,
        "totalAmount": 2469.75,
        "breakdown": [
            { "date": "2025-03-15", "basePrice": 850.00, "finalPrice": 765.00 },
            { "date": "2025-03-16", "basePrice": 850.00, "finalPrice": 765.00 },
            { "date": "2025-03-17", "basePrice": 850.00, "finalPrice": 765.00 }
        ]
    }
}',
                    'errors' => '404 — Room type not found'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- BOOKINGS --}}
            {{-- ================================================================ --}}
            <div id="bookings" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #f97316, #fb923c);"><i class='bx bx-receipt'></i></div>
                    <div><h5>Bookings</h5><p>Create bookings, initiate payment, view confirmations, cancel</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/bookings',
                    'desc' => 'Create a booking',
                    'auth' => 'public',
                    'rate' => '10 / min',
                    'body' => [
                        ['field' => 'hotel_id', 'type' => 'integer', 'req' => true, 'desc' => 'Hotel ID'],
                        ['field' => 'room_type_id', 'type' => 'integer', 'req' => true, 'desc' => 'Room type ID'],
                        ['field' => 'check_in', 'type' => 'date', 'req' => true, 'desc' => 'YYYY-MM-DD, today or later'],
                        ['field' => 'check_out', 'type' => 'date', 'req' => true, 'desc' => 'YYYY-MM-DD, after check_in'],
                        ['field' => 'num_rooms', 'type' => 'integer', 'req' => true, 'desc' => '1 to 10'],
                        ['field' => 'num_adults', 'type' => 'integer', 'req' => true, 'desc' => 'Min 1'],
                        ['field' => 'num_children', 'type' => 'integer', 'req' => false, 'desc' => 'Min 0 (default: 0)'],
                        ['field' => 'guest_first_name', 'type' => 'string', 'req' => true, 'desc' => 'Max 100 characters'],
                        ['field' => 'guest_last_name', 'type' => 'string', 'req' => true, 'desc' => 'Max 100 characters'],
                        ['field' => 'guest_email', 'type' => 'string', 'req' => true, 'desc' => 'Valid email, max 255 chars'],
                        ['field' => 'guest_phone', 'type' => 'string', 'req' => true, 'desc' => 'Max 20 characters'],
                        ['field' => 'guest_nationality', 'type' => 'string', 'req' => false, 'desc' => 'Max 100 characters'],
                        ['field' => 'special_requests', 'type' => 'string', 'req' => false, 'desc' => 'Max 1000 characters'],
                    ],
                    'response_label' => '201 Created',
                    'response' => '{
    "data": {
        "reference_number": "BK-X9Y8Z7W6",
        "status": "pending",
        "hotel": { "name": "Grand Hotel Dubai", "slug": "grand-hotel-dubai", "star_rating": 5, "address": "Sheikh Zayed Road" },
        "room_type": { "name": "Deluxe Suite", "slug": "deluxe-suite" },
        "dates": { "check_in": "2025-03-15", "check_out": "2025-03-18", "num_nights": 3 },
        "guests": { "num_adults": 2, "num_children": 0, "num_rooms": 1 },
        "pricing": {
            "room_price_per_night": 765.00,
            "subtotal": 2295.00,
            "tax_percentage": 5,
            "tax_amount": 114.75,
            "tourism_fee": 60.00,
            "service_charge": 0.00,
            "total_amount": 2469.75,
            "currency": "AED"
        },
        "guest_info": {
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com",
            "phone": "+971501234567",
            "nationality": "US"
        },
        "special_requests": null,
        "booked_at": "2025-03-01T12:00:00.000000Z"
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/bookings/{reference}',
                    'desc' => 'Get booking details',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'reference', 'type' => 'string', 'desc' => 'Booking reference number'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '// Same structure as POST /bookings response',
                    'errors' => '404 — Booking not found'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/bookings/{reference}/pay',
                    'desc' => 'Initiate payment',
                    'auth' => 'public',
                    'rate' => '5 / min',
                    'route_params' => [
                        ['field' => 'reference', 'type' => 'string', 'desc' => 'Booking reference number'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "payment_id": "PAY-abc123def456",
        "redirect_url": "https://gateway.mashreq.com/pay/abc123..."
    }
}',
                    'errors' => '404 — Booking not found | 422 — Booking not in payable state'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/bookings/{reference}/confirmation',
                    'desc' => 'Get booking confirmation with payment info',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'reference', 'type' => 'string', 'desc' => 'Booking reference number'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "reference_number": "BK-X9Y8Z7W6",
        "status": "confirmed",
        "hotel": { "..." : "..." },
        "room_type": { "..." : "..." },
        "dates": { "..." : "..." },
        "guests": { "..." : "..." },
        "pricing": { "..." : "..." },
        "guest_info": { "..." : "..." },
        "special_requests": null,
        "booked_at": "2025-03-01T12:00:00.000000Z",
        "payments": [
            {
                "transaction_id": "TXN-789012",
                "payment_method": "card",
                "amount": 2469.75,
                "currency": "AED",
                "status": "completed",
                "paid_at": "2025-03-01T12:05:00.000000Z"
            }
        ],
        "confirmed_at": "2025-03-01T12:05:00.000000Z"
    }
}',
                    'errors' => '404 — Booking not found'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/bookings/{reference}/cancel',
                    'desc' => 'Cancel a booking',
                    'auth' => 'public',
                    'rate' => '5 / min',
                    'route_params' => [
                        ['field' => 'reference', 'type' => 'string', 'desc' => 'Booking reference number'],
                    ],
                    'body' => [
                        ['field' => 'guest_email', 'type' => 'string', 'req' => true, 'desc' => 'Guest email for verification'],
                        ['field' => 'cancellation_reason', 'type' => 'string', 'req' => false, 'desc' => 'Reason for cancellation'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "Booking has been cancelled successfully.",
        "reference_number": "BK-X9Y8Z7W6"
    }
}',
                    'errors' => '403 — Email verification failed | 404 — Booking not found | 422 — Booking cannot be cancelled'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- AMENITIES --}}
            {{-- ================================================================ --}}
            <div id="amenities" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #eab308, #facc15);"><i class='bx bx-star'></i></div>
                    <div><h5>Amenities</h5><p>List all amenities grouped by category</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/amenities',
                    'desc' => 'List amenities grouped by category',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'params' => [],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "general": [
            { "id": 1, "name": "Free WiFi", "slug": "free-wifi", "icon": "bx-wifi", "category": "general" },
            { "id": 2, "name": "Parking", "slug": "parking", "icon": "bx-car", "category": "general" }
        ],
        "room": [
            { "id": 10, "name": "Air Conditioning", "slug": "air-conditioning", "icon": "bx-wind", "category": "room" }
        ],
        "leisure": [
            { "id": 20, "name": "Swimming Pool", "slug": "swimming-pool", "icon": "bx-swim", "category": "leisure" }
        ]
    }
}'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- DEALS --}}
            {{-- ================================================================ --}}
            <div id="deals" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #ef4444, #f87171);"><i class='bx bx-purchase-tag'></i></div>
                    <div><h5>Deals</h5><p>Active promotions and special offers</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/deals',
                    'desc' => 'List active deals',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'query' => [
                        ['field' => 'hotel', 'type' => 'string', 'req' => false, 'desc' => 'Filter by hotel slug'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": [
        {
            "id": 1,
            "title": "Summer Sale",
            "slug": "summer-sale",
            "description": "Get 20% off all rooms this summer!",
            "discount_type": "percentage",
            "discount_value": 20,
            "start_date": "2025-06-01",
            "end_date": "2025-09-30",
            "hotels": [
                { "id": 1, "name": "Grand Hotel Dubai", "slug": "grand-hotel-dubai" }
            ]
        }
    ]
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/deals/{slug}',
                    'desc' => 'Get deal detail with hotels',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'slug', 'type' => 'string', 'desc' => 'Deal slug'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "id": 1,
        "title": "Summer Sale",
        "slug": "summer-sale",
        "description": "Get 20% off all rooms this summer!",
        "discount_type": "percentage",
        "discount_value": 20,
        "start_date": "2025-06-01",
        "end_date": "2025-09-30",
        "hotels": [
            {
                "id": 1,
                "name": "Grand Hotel Dubai",
                "slug": "grand-hotel-dubai",
                "star_rating": 5,
                "short_description": "Luxury 5-star hotel",
                "address": "Sheikh Zayed Road",
                "avg_rating": 4.7,
                "total_reviews": 128,
                "min_price": 750.00,
                "primary_image": { "image_url": "...", "alt_text": "..." },
                "location": { "name": "Downtown Dubai", "slug": "downtown-dubai", "city": "Dubai" },
                "deals": []
            }
        ]
    }
}',
                    'errors' => '404 — Deal not found'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- TESTIMONIALS --}}
            {{-- ================================================================ --}}
            <div id="testimonials" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #14b8a6, #2dd4bf);"><i class='bx bx-message-rounded-dots'></i></div>
                    <div><h5>Testimonials</h5><p>Featured guest testimonials</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/testimonials',
                    'desc' => 'List featured testimonials',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'params' => [],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": [
        {
            "id": 1,
            "guest_name": "Sarah K.",
            "rating": 5,
            "title": "Amazing Experience",
            "comment": "Our stay was absolutely wonderful...",
            "is_verified": true,
            "hotel": {
                "name": "Grand Hotel Dubai",
                "slug": "grand-hotel-dubai",
                "star_rating": 5
            },
            "created_at": "2025-02-15T08:30:00.000000Z"
        }
    ]
}'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- CAREERS --}}
            {{-- ================================================================ --}}
            <div id="careers" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #6366f1, #818cf8);"><i class='bx bx-briefcase'></i></div>
                    <div><h5>Careers</h5><p>Job listings and application submission</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/careers',
                    'desc' => 'List open positions',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'params' => [],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": [
        {
            "id": 1,
            "title": "Front Desk Manager",
            "slug": "front-desk-manager",
            "location": "Dubai, UAE",
            "job_type": "Full-time",
            "department": "Operations",
            "about_role": "We are looking for...",
            "responsibilities": "Manage front desk operations...",
            "requirements": "3+ years experience...",
            "what_we_offer": "Competitive salary...",
            "last_apply_date": "2025-04-30"
        }
    ]
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/careers/{slug}',
                    'desc' => 'Get career detail',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'slug', 'type' => 'string', 'desc' => 'Career slug'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '// Same structure as list item',
                    'errors' => '404 — Career not found'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/careers/{slug}/apply',
                    'desc' => 'Apply for a position',
                    'auth' => 'public',
                    'rate' => '5 / min',
                    'route_params' => [
                        ['field' => 'slug', 'type' => 'string', 'desc' => 'Career slug'],
                    ],
                    'body' => [
                        ['field' => 'name', 'type' => 'string', 'req' => true, 'desc' => 'Applicant name, max 100 chars'],
                        ['field' => 'email', 'type' => 'string', 'req' => true, 'desc' => 'Applicant email'],
                        ['field' => 'phone', 'type' => 'string', 'req' => true, 'desc' => 'Phone number, max 20 chars'],
                        ['field' => 'cover_letter', 'type' => 'string', 'req' => false, 'desc' => 'Cover letter, max 5000 chars'],
                        ['field' => 'resume', 'type' => 'file', 'req' => true, 'desc' => 'PDF, DOC, or DOCX. Max 5MB.'],
                    ],
                    'note' => 'Send as multipart/form-data, not JSON.',
                    'response_label' => '201 Created',
                    'response' => '{
    "data": {
        "message": "Your application has been submitted successfully. We will review it and get back to you."
    }
}',
                    'errors' => '404 — Career not found or no longer accepting applications'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- PAGES --}}
            {{-- ================================================================ --}}
            <div id="pages" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #64748b, #94a3b8);"><i class='bx bx-file'></i></div>
                    <div><h5>Pages</h5><p>Static content pages</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/pages/{slug}',
                    'desc' => 'Get page content',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'route_params' => [
                        ['field' => 'slug', 'type' => 'string', 'desc' => 'Page slug: about-us, privacy-policy, terms-conditions'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "content": "<h2>About Us</h2><p>We are a leading...</p>",
        "meta_title": "About Us - Dubai Hotels",
        "meta_description": "Learn about our company",
        "canonical_url": "https://dubai-hotels.com/about-us"
    }
}',
                    'errors' => '404 — Page not found'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- CONTACT --}}
            {{-- ================================================================ --}}
            <div id="contact" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #0ea5e9, #38bdf8);"><i class='bx bx-envelope'></i></div>
                    <div><h5>Contact</h5><p>Submit contact inquiry</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/contact',
                    'desc' => 'Submit a contact inquiry',
                    'auth' => 'public',
                    'rate' => '5 / min',
                    'body' => [
                        ['field' => 'name', 'type' => 'string', 'req' => true, 'desc' => 'Sender name, max 100 chars'],
                        ['field' => 'email', 'type' => 'string', 'req' => true, 'desc' => 'Sender email'],
                        ['field' => 'phone', 'type' => 'string', 'req' => false, 'desc' => 'Phone number, max 20 chars'],
                        ['field' => 'subject', 'type' => 'string', 'req' => true, 'desc' => 'Subject, max 255 chars'],
                        ['field' => 'message', 'type' => 'string', 'req' => true, 'desc' => 'Message body, max 5000 chars'],
                        ['field' => 'hotel_id', 'type' => 'integer', 'req' => false, 'desc' => 'Hotel ID if inquiry is about a specific hotel'],
                    ],
                    'response_label' => '201 Created',
                    'response' => '{
    "data": {
        "message": "Your inquiry has been submitted successfully. We will get back to you soon."
    }
}'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- NEWSLETTER --}}
            {{-- ================================================================ --}}
            <div id="newsletter" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #d946ef, #e879f9);"><i class='bx bx-mail-send'></i></div>
                    <div><h5>Newsletter</h5><p>Subscribe and unsubscribe from newsletter</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/newsletter/subscribe',
                    'desc' => 'Subscribe to newsletter',
                    'auth' => 'public',
                    'rate' => '10 / min',
                    'body' => [
                        ['field' => 'email', 'type' => 'string', 'req' => true, 'desc' => 'Email address to subscribe'],
                    ],
                    'note' => 'Returns 201 for new subscribers, 200 for re-subscriptions or already subscribed.',
                    'response_label' => '201 Created',
                    'response' => '{
    "data": {
        "message": "You have been subscribed to our newsletter successfully."
    }
}'
                ])

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/newsletter/unsubscribe',
                    'desc' => 'Unsubscribe from newsletter',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'body' => [
                        ['field' => 'email', 'type' => 'string', 'req' => true, 'desc' => 'Email address to unsubscribe'],
                    ],
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "message": "You have been unsubscribed from our newsletter."
    }
}',
                    'errors' => '404 — Subscription not found'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- SEARCH --}}
            {{-- ================================================================ --}}
            <div id="search" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #84cc16, #a3e635);"><i class='bx bx-search'></i></div>
                    <div><h5>Search</h5><p>Autocomplete suggestions for hotels and locations</p></div>
                </div>

                @include('admin.documentation._endpoint', [
                    'method' => 'GET',
                    'path' => '/api/v1/search/suggestions',
                    'desc' => 'Get search suggestions',
                    'auth' => 'public',
                    'rate' => 'Standard',
                    'query' => [
                        ['field' => 'q', 'type' => 'string', 'req' => true, 'desc' => 'Search query, 2-100 characters'],
                    ],
                    'note' => 'Returns max 5 hotels and 3 locations. Searches hotel name, address, location name, and city.',
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "hotels": [
            { "type": "hotel", "name": "Grand Hotel Dubai", "slug": "grand-hotel-dubai", "image": "https://example.com/storage/hotels/1/thumb.jpg" }
        ],
        "locations": [
            { "type": "location", "name": "Downtown Dubai", "slug": "downtown-dubai", "image": "https://example.com/storage/locations/downtown_thumb.jpg" }
        ]
    }
}'
                ])
            </div>

            {{-- ================================================================ --}}
            {{-- PAYMENT CALLBACK --}}
            {{-- ================================================================ --}}
            <div id="payment-callback" class="mb-section">
                <div class="section-header">
                    <div class="section-icon" style="background: linear-gradient(135deg, #78716c, #a8a29e);"><i class='bx bx-credit-card'></i></div>
                    <div><h5>Payment Callback</h5><p>Mashreq payment gateway webhook</p></div>
                </div>

                <div class="warn-note"><strong>Server-to-server only:</strong> This endpoint is called by the Mashreq payment gateway. It does not require the X-Domain header or any authentication token. Signature verification is handled internally.</div>

                @include('admin.documentation._endpoint', [
                    'method' => 'POST',
                    'path' => '/api/v1/payments/callback',
                    'desc' => 'Payment gateway callback',
                    'auth' => 'none',
                    'rate' => 'None',
                    'note' => 'Called by Mashreq payment gateway after payment processing. Do not call this endpoint from the frontend.',
                    'response_label' => '200 OK',
                    'response' => '{
    "data": {
        "reference_number": "BK-X9Y8Z7W6",
        "status": "confirmed",
        "redirect_url": "https://dubai-hotels.com/bookings/BK-X9Y8Z7W6/confirmation"
    }
}',
                    'errors' => '422 — Payment failed'
                ])
            </div>

        </div>
    </div>
