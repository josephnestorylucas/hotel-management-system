# Hotel Management System (HMS)

A comprehensive hotel operations platform built with Laravel 12, PostgreSQL, Tailwind CSS, and Alpine.js. Covers everything from front-desk bookings to accounting, procurement, restaurant/bar, laundry, events, and conference management.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.2+ / Laravel 12 |
| Frontend | Blade Templates / Tailwind CSS 3.4 / Alpine.js 3.x |
| Database | PostgreSQL (primary) / SQLite (bug reports) |
| Real-time | Laravel Reverb (WebSocket) |
| Build | Vite 5 |
| Queue | Database (default) / Redis supported |
| Media | Spatie Media Library |
| Payments | AzamPesa (Tanzania mobile money & bank cards) |
| SMS | Africa's Talking |
| QR Codes | simplesoftwareio/simple-qrcode |

---

## Modules

### Core Hotel Operations
- **Buildings / Floors / Rooms** — Physical structure management with real-time room status tracking (available, occupied, dirty, out_of_order, reserved)
- **Room Types** — Categories with pricing, max occupancy, and currency support
- **Reservations** — Future room holds with lifecycle: pending → confirmed → converted / cancelled / no_show
- **Bookings** — Active guest stays created from reservations or walk-ins: checked_in → checked_out / cancelled
- **Guests** — Full guest profiles with ID documents, photos, loyalty program, and organization linking
- **Booking Charges** — Itemized charges per booking (laundry, minibar, room service, restaurant, conference)
- **Payments** — Payment processing with AzamPesa integration (mobile money + bank cards)
- **Current Guests** — Real-time view of all checked-in guests

### Laundry
- Service & price management
- Full order lifecycle: created → processing → ready → delivered → collected → settled
- Task assignments and daily reports

### Restaurant & Bar
- Menu management with categories, options, and destination routing (kitchen/bar)
- Table management with status tracking
- Order lifecycle with POS for waiters and bartenders
- Kitchen & bar ticket queues
- Buffet packages and POS
- Kitchen stock management and damage reports

### Store / Inventory
- Product catalog with types and varieties
- Stock levels per location (Main Store, Kitchen, Bar)
- Stock movements, adjustments, and damage recording
- Internal usage requests with approval workflow
- Inter-location stock transfers with approval chain

### Procurement
- Supplier registry with TIN/VRN
- Local Purchase Orders (LPO) with multi-step approval
- Goods Received Notes (GRN) with confirmation workflow
- Accounts payable and supplier payment tracking

### Finance
- Financial dashboard with revenue overview
- Guest checkout processing with draft support
- Walk-in POS payments (restaurant, bar, laundry)
- Petty cash management with approval workflow
- Refund processing
- Unified receipt system (polymorphic across all modules)

### Accounting
- Chart of accounts (Assets, Liabilities, Equity, Revenue, Expenses)
- Double-entry journal entries (draft → posted → reversed)
- Invoices and payroll runs
- Bank reconciliation
- Reports: P&L, Balance Sheet, Cashflow, Trial Balance, VAT, AP Aging, General Ledger, Supplier Payables

### Conference & Events
- Organization management with verification workflow
- Conference hall bookings with hourly rates
- Event lifecycle: draft → scheduled → ongoing → completed / cancelled
- Event passes/tickets with tier system
- Attendee registration with bulk upload
- QR code check-in with scanner, manual, and staff override
- Badge printing and ticket PDF generation
- Pre-event, live, and post-event reports

### Housekeeping
- Room cleaning assignment and confirmation workflow
- Maintenance tracking (out_of_order rooms)
- Staff task management

### Other
- Loyalty program with points, tiers (Silver/Gold/Platinum), and discounts
- Bug reporter (floating button on all pages)
- In-app notifications with WebSocket broadcasting
- Multi-language support (English / Swahili)
- Admin broadcast messages to guests
- System settings management

---

## Roles (12)

| Role | Key Capabilities |
|------|-----------------|
| `admin` | Full system access, user management, settings |
| `manager` | Approvals, reports, operational oversight |
| `front_desk` | Bookings, reservations, guests, payments |
| `supervisor` | Task supervision, cleaning queue, kitchen queue |
| `house_help` | Internal requests, room cleaning |
| `store_manager` | Full store control, procurement, reports |
| `store_keeper` | Stock operations, restock, transfers, GRN |
| `restaurant_manager` | Menu, orders, kitchen, bar, buffet |
| `waiter` | View menus, create orders, buffet POS |
| `bar_tender` | Bar POS, drink orders, damage reports |
| `laundry_manager` | Laundry services and orders |
| `ACCOUNTANT` | Full financial records, reports, payroll |

---

## Database

130+ migrations covering:
- `users`, `roles` — Authentication & RBAC
- `buildings`, `floors`, `rooms`, `room_types` — Hotel structure
- `guests`, `reservations`, `bookings`, `booking_charges` — Guest operations
- `payments`, `payment_items`, `receipts`, `walkin_transactions` — Finance
- `products`, `stock_levels`, `stock_movements`, `stock_transfers` — Inventory
- `suppliers`, `local_purchase_orders`, `goods_received_notes` — Procurement
- `accounts`, `journal_entries`, `journal_lines`, `invoices`, `payroll_runs` — Accounting
- `menu_items`, `orders`, `order_items`, `tables` — Restaurant & Bar
- `laundry_services`, `laundry_orders`, `laundry_order_items` — Laundry
- `organizations`, `events`, `attendances`, `conference_bookings` — Events & Conferences
- `sessions`, `cache`, `jobs` — Infrastructure

All models use UUID primary keys via the `HasUuid` trait.

---

## Installation

### Requirements
- PHP 8.2+
- Composer 2.x
- PostgreSQL 14+
- Node.js 18+ & NPM

### Setup

```bash
# Clone
git clone <repository-url> hotel-management-system
cd hotel-management-system

# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=hotel_management
# DB_USERNAME=your_user
# DB_PASSWORD=your_password

# Create database
psql -U postgres -c "CREATE DATABASE hotel_management;"

# Migrate and seed
php artisan migrate
php artisan db:seed

# Build frontend
npm run build

# Storage link
php artisan storage:link

# Start server
php artisan serve
```

Or use the all-in-one setup:
```bash
composer setup
```

### Development (all services)
```bash
composer dev
```
This starts the server, queue worker, log tail, Vite, and Reverb simultaneously.

---

## Default Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@hotel.com | password |
| Front Desk | frontdesk@hotel.com | password |
| Supervisor | supervisor@hotel.com | password |

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/       # 45 controllers across 15 modules
│   ├── Middleware/         # Role, Security, Locale, Auth
│   └── Requests/          # Form requests
├── Models/                # 82 Eloquent models
├── Services/              # Accounting, Payment, Notification, SMS, Receipt
├── Jobs/                  # 8 queued jobs (email, SMS, broadcasts)
├── Events/                # WebSocket broadcast events
├── Observers/             # Booking & Reservation observers
├── Policies/              # Booking authorization
├── Traits/                # HasUuid
├── Helpers/               # CurrencyHelper
├── Support/               # PhoneNumber normalization
├── Mail/                  # 11 mailable classes
└── View/                  # AppLayout, GuestLayout

config/                    # 17 config files
database/
├── migrations/            # 130+ migrations
├── seeders/               # Sample data seeders
└── factories/             # Model factories
resources/
├── views/                 # Blade templates (layouts, partials, per-module)
├── lang/
│   ├── en/                # 16 English translation files
│   └── sw/                # 16 Swahili translation files
├── css/                   # Tailwind CSS
└── js/                    # Alpine.js + Axios
routes/
├── web.php                # 150+ routes
├── api.php                # Bug report API
├── console.php            # Artisan commands
└── channels.php           # WebSocket channels
tests/
├── Feature/               # 20+ feature tests
└── Unit/                  # Unit tests
```

---

## Production Deployment

### Optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### Environment
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
```

### Queue Worker
```bash
php artisan queue:work
```

### Scheduled Tasks
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## License

Proprietary — Internal use only.
