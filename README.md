# Hotel Management System (HMS) - Phase One

## System Overview

The Hotel Management System (HMS) is a comprehensive digital platform designed to streamline hotel operations. Phase One establishes the foundational core of the system, focusing on structural integrity, room control, booking management, and role-based access control.

### Purpose

HMS Phase One acts as a **digital front-desk and room control center** that:
- Maintains the physical structure of the hotel digitally
- Tracks room states in real-time
- Prevents booking conflicts and double bookings
- Enforces role-based authorization for all operations
- Provides operational visibility through dashboards and analytics

### Architecture

**Technology Stack:**
- **Backend:** Laravel 11
- **Database:** PostgreSQL 14+
- **Admin Panel:** Filament PHP v3
- **Frontend:** Blade Templates + Tailwind CSS
- **JavaScript:** Alpine.js
- **Charts:** Chart.js (via Filament)

**System Layers:**
- **Presentation Layer:** Filament Admin Panel
- **Application Layer:** Laravel Controllers & Resources
- **Data Layer:** PostgreSQL with Eloquent ORM

---
composer require africastalking/africastalking barryvdh/laravel-dompdf
## Phase One Implementation

### ✅ Implemented Modules

#### 1. Building Management
**Purpose:** Digital representation of hotel's physical structure

**Features:**
- Create and manage multiple buildings
- Unique building codes
- Address tracking
- Enable/disable buildings
- View floor distribution per building

**Database Tables:**
- `buildings` - Building information and status

**Access Control:**
- **Admin:** Full access (create, edit, delete, view)
- **Supervisor:** View only
- **Front Desk:** No access

---

#### 2. Floor Management
**Purpose:** Organize rooms by floor within buildings

**Features:**
- Create floors linked to buildings
- Floor numbering system
- Floor activation/deactivation
- Room count per floor
- Unique floor numbers within buildings

**Database Tables:**
- `floors` - Floor information linked to buildings

**Access Control:**
- **Admin:** Full access
- **Supervisor:** View only
- **Front Desk:** No access

---

#### 3. Room Type Management
**Purpose:** Define room categories with pricing

**Features:**
- Multiple room types (Single, Double, Deluxe, Suite, Family)
- Base rate configuration
- Maximum occupancy settings
- Room type descriptions
- Unique type codes

**Database Tables:**
- `room_types` - Room type definitions and pricing

**Access Control:**
- **Admin:** Full access
- **Supervisor:** View only
- **Front Desk:** No access

---

#### 4. Room Management
**Purpose:** Real-time room inventory and status control

**Features:**
- Room creation linked to floors and types
- Unique room numbering per floor
- Real-time status tracking
- Room activation/deactivation
- Status lifecycle management

**Room Statuses:**
- `available` - Ready for booking
- `reserved` - Assigned to future reservation
- `occupied` - Guest checked in
- `dirty` - Requires housekeeping
- `out_of_order` - Maintenance required

**Database Tables:**
- `rooms` - Room inventory and current status

**Access Control:**
- **Admin:** Full access
- **Supervisor:** Full access (including status overrides)
- **Front Desk:** View only

**Business Rules:**
- Rooms cannot be deleted if linked to reservations
- Only active rooms appear in booking availability
- Status transitions enforced by reservation state changes

---

#### 5. Reservation & Booking Management
**Purpose:** Time-bound room allocation with conflict prevention

**Features:**
- Walk-in and advance bookings
- Guest information capture
- Date range selection with validation
- Automatic room availability checking
- Reservation status workflow
- Total amount calculation
- Room assignment (optional at creation)

**Reservation Statuses:**
- `pending` - Awaiting confirmation
- `confirmed` - Reservation confirmed
- `checked_in` - Guest arrived
- `checked_out` - Guest departed
- `cancelled` - Reservation cancelled
- `no_show` - Guest failed to arrive

**Database Tables:**
- `reservations` - Booking records with guest data

**Automatic Features:**
- Unique reservation number generation (RES-XXXXXX)
- Overlap detection prevents double bookings
- Room status auto-updates on check-in/check-out
- User attribution (created_by tracking)

**Access Control:**
- **Admin:** Full access
- **Supervisor:** Full access (including cancellations)
- **Front Desk:** Create, edit, check-in, check-out

**Business Rules:**
- No overlapping reservations for same room
- Check-out date must be after check-in date
- Room availability filtered by date range
- Cancelled reservations release rooms immediately

---

#### 6. Role-Based Access Control (RBAC)
**Purpose:** Security and operational authorization

**Roles Implemented:**

| Role | Description | Access Level |
|------|-------------|--------------|
| **Admin** | System Administrator | Full system access including user management |
| **Supervisor** | Operations Manager | Operational oversight with override capabilities |
| **Front Desk** | Reception Staff | Daily booking and guest operations |

**Database Tables:**
- `roles` - Role definitions
- `users` - User accounts with role assignment

**Security Features:**
- Mandatory role assignment for all users
- Inactive users cannot log in
- All actions attributed to authenticated users
- Resource-level authorization checks
- Menu visibility based on role

**User Management:**
- **Admin Only:** Create, edit, deactivate users
- Password hashing (bcrypt)
- Email uniqueness enforcement
- Active/inactive user status

---

### 📊 Dashboard & Analytics

#### Widgets Implemented

**1. Stats Overview Widget**
- Total rooms count
- Occupied rooms with occupancy percentage
- Available rooms ready for booking
- Reserved rooms (upcoming arrivals)
- Today's expected check-ins
- Today's expected check-outs

**2. Room Status Chart**
- Doughnut chart showing room distribution
- Real-time status breakdown
- Color-coded categories

**3. Reservation Status Chart**
- Pie chart of all reservation states
- Status distribution visualization

**4. Occupancy Trend Chart**
- 7-day occupancy rate trend line
- Historical performance tracking
- Percentage-based visualization

**5. Room Type Distribution**
- Bar chart of rooms by type
- Inventory composition view

**6. Revenue Overview** (Admin & Supervisor only)
- Today's revenue
- Weekly revenue
- Monthly revenue
- Pending revenue from future bookings

**7. Upcoming Arrivals Table**
- Next 7 days arrivals
- Reservation details
- Room assignment status
- Quick action buttons

**8. Today's Activity Table**
- Today's check-ins and check-outs
- Real-time activity tracking
- Quick check-in/check-out actions

**Widget Visibility Matrix:**

| Widget | Admin | Supervisor | Front Desk |
|--------|:-----:|:----------:|:----------:|
| Stats Overview | ✓ | ✓ | ✓ |
| Room Status Chart | ✓ | ✓ | ✓ |
| Reservation Status | ✓ | ✓ | ✓ |
| Occupancy Trend | ✓ | ✓ | ✓ |
| Revenue Overview | ✓ | ✓ | ✗ |
| Room Type Distribution | ✓ | ✓ | ✓ |
| Upcoming Arrivals | ✓ | ✓ | ✓ |
| Today's Activity | ✓ | ✓ | ✓ |

---

### 🗄️ Database Schema

**Tables Created:**
1. `roles` - User roles and permissions
2. `users` - System users (extended)
3. `buildings` - Building structures
4. `floors` - Floor organization
5. `room_types` - Room categories
6. `rooms` - Room inventory
7. `reservations` - Booking records

**Relationships:**
- Users → Roles (Many-to-One)
- Floors → Buildings (Many-to-One)
- Rooms → Floors (Many-to-One)
- Rooms → Room Types (Many-to-One)
- Reservations → Rooms (Many-to-One)
- Reservations → Users (created_by)

**Constraints:**
- Foreign key constraints on all relationships
- Unique constraints on codes and room numbers
- Cascade deletes on building → floors
- Restrict deletes on rooms with reservations
- Date validation (check-out after check-in)

---

### 🎯 Sample Data

The system includes comprehensive seeders with realistic data:

**Users:**
- Admin: admin@hotel.com
- Front Desk: frontdesk@hotel.com
- Supervisor: supervisor@hotel.com
- Default password: `password`

**Buildings:**
- Main Building (MAIN) - 4 floors
- West Wing (WEST) - 3 floors

**Room Types:**
- Standard Single ($80/night)
- Standard Double ($120/night)
- Deluxe ($180/night)
- Suite ($300/night)
- Family Room ($220/night)

**Rooms:**
- 70 total rooms across both buildings
- 10 rooms per floor
- Mixed room type distribution

**Reservations:**
- Current checked-in guests
- Upcoming confirmed reservations
- Pending bookings
- Historical check-outs
- Sample cancellations

---

## Installation Guide

### System Requirements

**Server Requirements:**
- PHP 8.2 or higher
- Composer 2.x
- PostgreSQL 14+
- Node.js 18+ & NPM
- Web server (Apache/Nginx)

**PHP Extensions:**
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_PGSQL
- Tokenizer
- XML

---

### Step-by-Step Installation

#### 1. Clone or Download Project
```bash
# If using Git
git clone <repository-url> hotel-management-system
cd hotel-management-system

# If downloaded as ZIP
unzip hotel-management-system.zip
cd hotel-management-system
```

#### 2. Install PHP Dependencies
```bash
composer install
```

#### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4. Configure Database
Edit `.env` file with your PostgreSQL credentials:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hotel_management
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

#### 5. Create Database
```bash
# Access PostgreSQL
psql -U postgres

# Create database
CREATE DATABASE hotel_management;

# Exit PostgreSQL
\q
```

#### 6. Run Migrations
```bash
php artisan migrate
```

Expected output:
```
Migration table created successfully.
Migrating: 2024_01_01_000001_create_roles_table
Migrated:  2024_01_01_000001_create_roles_table
Migrating: 2024_01_01_000002_add_role_to_users_table
Migrated:  2024_01_01_000002_add_role_to_users_table
... (continues for all migrations)
```

#### 7. Seed Database with Sample Data
```bash
php artisan db:seed
```

Expected output:
```
Seeding: Database\Seeders\RoleSeeder
Seeded:  Database\Seeders\RoleSeeder
Seeding: Database\Seeders\UserSeeder
Seeded:  Database\Seeders\UserSeeder
... (continues for all seeders)
```

#### 8. Install Frontend Dependencies
```bash
npm install
npm run build
```

#### 9. Create Storage Link
```bash
php artisan storage:link
```

#### 10. Set Directory Permissions
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Or if using current user
chmod -R 775 storage bootstrap/cache
```

#### 11. Start Development Server
```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

---

### Accessing the System

#### Admin Panel URL
```
http://localhost:8000/admin
```

#### Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@hotel.com | password |
| Supervisor | supervisor@hotel.com | password |
| Front Desk | frontdesk@hotel.com | password |

---

### Post-Installation Steps

#### 1. Change Default Passwords
```bash
# Access the system as admin
# Navigate to Users section
# Edit each user and update password
```

#### 2. Configure Application Settings
Edit `.env` for production:
```env
APP_NAME="Your Hotel Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

#### 3. Set Up Email (Optional)
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourhotel.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### 4. Configure Backup (Recommended)
```bash
# Install backup package
composer require spatie/laravel-backup

# Publish configuration
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"

# Configure backup destinations in config/backup.php
```

#### 5. Set Up Scheduled Tasks
Add to crontab for automated tasks:
```bash
crontab -e

# Add this line
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

---

### Production Deployment

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName yourhotel.com
    DocumentRoot /var/www/hotel-management-system/public

    <Directory /var/www/hotel-management-system/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/hotel-error.log
    CustomLog ${APACHE_LOG_DIR}/hotel-access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourhotel.com;
    root /var/www/hotel-management-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### SSL Setup (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Generate SSL certificate
sudo certbot --apache -d yourhotel.com
```

#### Optimize for Production
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

---

## Troubleshooting

### Common Issues

**Issue: "Permission denied" errors**
```bash
# Solution: Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Issue: "Database connection refused"**
```bash
# Solution: Check PostgreSQL service
sudo systemctl status postgresql
sudo systemctl start postgresql

# Verify credentials in .env file
```

**Issue: "Class not found" errors**
```bash
# Solution: Regenerate autoload files
composer dump-autoload
php artisan clear-compiled
```

**Issue: "Route not found"**
```bash
# Solution: Clear cache
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

**Issue: Widgets not displaying**
```bash
# Solution: Clear view cache
php artisan view:clear
php artisan filament:cache-components
```

---

## System Verification Checklist

After installation, verify:

- [ ] Can log in with all three user roles
- [ ] Can create a building
- [ ] Can create floors within building
- [ ] Can create room types
- [ ] Can create rooms
- [ ] Can create a reservation
- [ ] Room availability updates correctly
- [ ] No double bookings possible
- [ ] Check-in updates room status to occupied
- [ ] Check-out updates room status to dirty
- [ ] Dashboard displays all widgets
- [ ] Charts render with data
- [ ] Role-based access works (test each role)

---

## Development Notes

### Code Structure
```
app/
├── Filament/
│   ├── Resources/     # CRUD resources
│   ├── Widgets/       # Dashboard widgets
│   └── Pages/         # Custom pages
├── Models/            # Eloquent models
├── Observers/         # Model observers
└── Providers/         # Service providers

database/
├── migrations/        # Database schema
└── seeders/          # Sample data

resources/
└── views/            # Blade templates
```

### Key Files
- `app/Models/User.php` - User model with role methods
- `app/Observers/ReservationObserver.php` - Automatic status updates
- `config/filament.php` - Filament configuration
- `database/seeders/DatabaseSeeder.php` - Master seeder

---

## Support & Maintenance

### Logs Location
```
storage/logs/laravel.log
```

### Clear All Cache
```bash
php artisan optimize:clear
```

### Database Backup
```bash
pg_dump -U username hotel_management > backup_$(date +%Y%m%d).sql
```

### Database Restore
```bash
psql -U username hotel_management < backup_file.sql
```

---

## What's Next? (Future Phases)

**Phase Two** will include:
- Housekeeping workflows
- Payment processing
- Financial reports
- Guest profiles expansion

**Phase Three** will include:
- Inventory management
- Procurement system
- Conference & events
- External integrations

---

## License & Support

**Document Version:** 1.0
**Last Updated:** November 2025
**Laravel Version:** 11.x
**Filament Version:** 3.x
**PHP Version:** 8.2+

For issues or questions, contact your technical support team.

---

**System Status: ✅ OPERATIONAL**

Phase One implementation is complete and ready for production use.