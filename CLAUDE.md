# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Dubai Apartments** — Admin panel for a multi-domain hotel listing platform built on **Laravel 12** (PHP 8.2+). One centralized admin panel controls ~70 hotel listing domains, each with its own pricing markup and configuration.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| Database | MySQL (`dubai_apartments`) |
| Admin Frontend | Bootstrap 5, Boxicons, jQuery, Select2, DataTables, Chart.js, Alpine.js |
| Auth | Laravel Breeze (Blade, login/logout only) |
| Roles | Spatie Laravel Permission (6 roles) |
| Build | Vite 7 |

## Commands

### First-time setup
```
composer setup
```

### Development server
```
composer dev
```

### Run tests
```
composer test
php artisan test --filter=ExampleTest
```

### Linting
```
./vendor/bin/pint
```

### Custom Artisan commands
- `php artisan pricing:execute-scheduled` — Execute due scheduled price updates
- `php artisan analytics:aggregate` — Flush cached analytics and aggregate daily stats

## Architecture

### Route Structure
- `/` redirects to `/admin/dashboard`
- **Admin panel** (`/admin`): `Admin\` controllers — 13 resource controllers behind `auth+verified` middleware
- **Public API** (`/api/v1`): Domain-scoped endpoints for frontend sites (hotels, bookings, pricing, availability)
- **Auth**: Login/logout only (Breeze)

### Admin Modules

| Module | Controller | Features |
|--------|-----------|----------|
| **Dashboard** | `DashboardController` | 40+ metrics, revenue charts, top hotels/domains/locations, recent bookings/reviews |
| **Domains** | `DomainController` | CRUD for listing domains (name, URL, language, active status) |
| **Locations** | `LocationController` | CRUD with image upload, domain assignment, SEO fields, canonical URL |
| **Hotels** | `HotelController` | CRUD with image management (10 categories), amenity/domain sync, SEO, canonical URL |
| **Room Types** | `RoomTypeController` | Nested under hotels, CRUD with amenities, pricing, availability |
| **Amenities** | `AmenityController` | CRUD — icons, categories, sort order |
| **Pricing Rules** | `PricingRuleController` | CRUD + bulk update — 5 rule types (domain_markup, seasonal, date_range, category, day_of_week) |
| **Bookings** | `BookingController` | Index with search/status filters, show, update status, refund, export |
| **Users** | `UserController` | CRUD with role assignment, domain scoping |
| **Roles** | `RoleController` | CRUD with grouped permission management |
| **Reviews** | `ReviewController` | Moderation — approve/reject, admin reply |
| **Analytics** | `AnalyticsController` | Domain analytics with date filtering |
| **Search** | `SearchController` | 5 AJAX endpoints for Select2 (domains, locations, hotels, room-types, users) |

### API Modules

| Endpoint | Controller | Purpose |
|----------|-----------|---------|
| `GET domain/config` | `DomainConfigController` | Domain configuration + tracking IDs |
| `GET hotels` | `HotelController` | List/search/detail with pricing |
| `GET locations` | `LocationController` | List/detail with hotels |
| `GET availability` | `AvailabilityController` | Room availability check |
| `POST pricing/calculate` | `PricingController` | Price breakdown calculation |
| `POST bookings` | `BookingController` | Create booking, payment initiation, confirmation |
| `GET amenities` | `AmenityController` | Amenities grouped by category |
| `POST payments/callback` | `PaymentCallbackController` | Mashreq gateway callback |

### Key Models (16 in `app/Models/`)
- `Domain` → belongsToMany Hotels/Locations, hasMany Bookings/PricingRules/Analytics
- `Hotel` → belongsTo Location, hasMany RoomTypes/Images/Reviews/Bookings, belongsToMany Amenities/Domains
- `Location` → hasMany Hotels, belongsToMany Domains
- `RoomType` → belongsTo Hotel, hasMany RoomAvailability/Images, belongsToMany Amenities
- `Booking` → belongsTo Domain/Hotel/RoomType, hasMany Payments
- `Review` → belongsTo Hotel/Booking
- `PricingRule` → Flexible rules (domain_markup, seasonal, date_range, category, day_of_week)
- `Payment` → belongsTo Booking (Mashreq gateway integration)
- `HotelImage` → belongsTo Hotel (10 category types)
- `DomainAnalytics` → belongsTo Domain (daily aggregated metrics)
- Supporting: `Amenity`, `RoomAvailability`, `RoomTypeImage`, `ScheduledPriceUpdate`, `ActivityLog`, `User`

### Services (`app/Services/`)
- `PricingService` — Price calculation engine: base price → availability override → pricing rules by priority → tax/tourism fee
- `MashreqPaymentService` — Payment initiation, callback handling, refund processing

### Traits (`app/Traits/`)
- `ScopesByDomain` — Domain-based authorization scoping for hotels, bookings, reviews
- `ApiResponses` — Consistent JSON response formatting for API controllers

### Middleware
- `ResolveDomainApi` — Resolves domain from `X-Domain` header for API routes, caches 1 hour

### Database
- MySQL: `dubai_apartments` (root, no password on local XAMPP)
- 28 migrations total
- Denormalized fields on `hotels`: `avg_rating`, `total_reviews`, `min_price` (updated via `HotelObserver`)
- Composite indexes on high-traffic queries
- Soft deletes on: Domain, Hotel, Location, RoomType, Booking, Review, PricingRule

### Views Layout
- **Admin**: `layouts/admin.blade.php` + `layouts/sidebar.blade.php` (Bootstrap 5, dark sidebar)
- **Auth**: `layouts/guest.blade.php` (login page)
- **Components**: `form/input`, `form/select` (auto-Select2), `form/select2-ajax`, `form/textarea`, `form/checkbox`, `form/color`, `form/image-upload`

### Roles & Permissions (Spatie)

**6 Roles**: Admin, Price Manager, Content Editor, SEO Manager, Support Staff, Domain Manager

**20 Permissions**:
- Domain: manage, view
- Location: manage, view
- Hotel: manage, view
- Room: manage, view
- Pricing: manage, view
- Booking: manage, view, cancel, refund
- User: manage, view
- Review: manage, view
- Analytics: view
- Settings: manage

### Seeders
- `RolesAndPermissionsSeeder` — 20 permissions, 6 roles
- `AdminUserSeeder` — admin@dubaihotels.com / password
- `CurrencySeeder` — AED, USD, EUR, GBP, SAR
- `AmenitySeeder` — 28 amenities across 6 categories
- `TestDataSeeder` — 3 domains, locations, hotels, room types, bookings, reviews, pricing rules, analytics
