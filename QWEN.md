# Laravel Filament Trucking Application

## Project Overview

This is a Laravel 12 web application built with the Filament admin panel framework, designed for trucking business management. The application leverages Laravel's elegant syntax and powerful features combined with Filament's intuitive admin interface to provide a comprehensive solution for managing trucking operations.

Key technologies and components:
- **Framework**: Laravel 12
- **Admin Panel**: Filament 4.0
- **Authorization**: Filament Shield for role-based permissions
- **Database**: SQLite (default, with capability to switch to MySQL/PostgreSQL)
- **Frontend**: Vite, Tailwind CSS, and vanilla JavaScript
- **PHP Version**: 8.2+

## Project Structure

```
trucking-app/
├── app/                    # Application source code
│   ├── Filament/          # Filament resources, pages, and schemas
│   │   └── Resources/     # Filament resources for Trucks, Drivers, Customers, Shipments
│   ├── Http/              # Controllers, middleware
│   ├── Models/            # Eloquent models (User, Truck, Driver, Customer, Shipment)
│   └── Providers/         # Service providers (including Filament\AdminPanelProvider.php)
├── config/                # Configuration files (including filament-shield.php)
├── database/              # Migrations, seeders, factories, and SQLite database
│   ├── factories/         # Model factories
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── public/                # Publicly accessible files
├── resources/             # Views, CSS, JS assets
├── routes/                # Route definitions
├── storage/               # Compiled templates, file uploads, cache
├── tests/                 # Test files
├── vendor/                # Composer dependencies
├── artisan                 # Laravel CLI tool
├── composer.json          # PHP dependencies and scripts
├── package.json           # Node.js dependencies and scripts
├── .env                   # Environment variables
└── README.md             # Project documentation
```

## Building and Running

### Prerequisites
- PHP 8.2+
- Composer
- Node.js and npm

### Setup Commands
1. **Initial Setup**:
   ```bash
   composer run setup
   ```
   This command runs:
   - `composer install` - Install PHP dependencies
   - Creates `.env` file from `.env.example`
   - Generates application key
   - Runs database migrations
   - Installs Node.js dependencies
   - Builds frontend assets

2. **Development Server**:
   ```bash
   composer run dev
   ```
   This starts:
   - Laravel development server
   - Queue listener
   - Laravel Pail for log streaming
   - Vite dev server
   All processes run concurrently with colored output

3. **Manual Setup Steps** (alternative to `composer run setup`):
   ```bash
   # Install dependencies
   composer install
   npm install

   # Set up environment
   cp .env.example .env
   php artisan key:generate

   # Install Filament and Shield
   php artisan filament:install --panels
   php artisan shield:install admin

   # Database setup
   php artisan migrate
   php artisan db:seed

   # Build assets
   npm run build
   ```

4. **Development Mode**:
   ```bash
   # Start development server
   php artisan serve

   # In another terminal, start Vite dev server
   npm run dev
   ```

5. **Testing**:
   ```bash
   composer run test
   # or
   php artisan test
   ```

## Key Features and Configuration

### Filament Admin Panel
- The application uses Filament 4.0 as the admin panel framework
- Provides an intuitive interface for managing trucking operations
- Includes built-in authentication and authorization
- Panel configured at `/admin` with login functionality

### Filament Shield (Permissions)
- Role-based access control is implemented via `bezhansalleh/filament-shield`
- Configured in `config/filament-shield.php`
- Features:
  - Super admin role with unrestricted access
  - Panel user role for basic access
  - Automatic permission generation for resources, pages, and widgets
  - Role management interface accessible at `/admin/shield/roles`

### Database Configuration
- Default database is SQLite (as seen in `.env`)
- Can be configured for MySQL, PostgreSQL, or other supported databases
- Migrations are located in `database/migrations/`
- Uses Laravel's schema migration system

### Frontend Assets
- Built with Tailwind CSS for styling
- Vite as the build tool
- JavaScript handled via the resources/js directory

## Trucking Application Features

### Models and Database Structure
The application includes the following models for trucking operations:

1. **Truck Model**:
   - `truck_number`: Unique identifier for the truck
   - `make`, `model`, `year`: Truck specifications
   - `license_plate`, `vin`: Registration information
   - `current_mileage`: Current mileage of the truck
   - `last_service_date`, `next_service_date`: Maintenance schedule
   - `status`: Available, in-use, maintenance, or retired

2. **Driver Model**:
   - `first_name`, `last_name`: Personal information
   - `license_number`, `license_expiry_date`: Driving credentials
   - `phone`, `email`: Contact information
   - `hire_date`, `dob`: Employment details
   - `address`, `city`, `state`, `zip_code`: Address information
   - `status`: Active, inactive, or suspended

3. **Customer Model**:
   - `name`, `company_name`: Customer identification
   - `phone`, `email`: Contact information
   - `address`, `city`, `state`, `zip_code`: Address information
   - `customer_type`: Regular, commercial, or residential

4. **Shipment Model**:
   - `shipment_number`: Unique identifier for the shipment
   - Foreign keys linking to customer, driver, and truck
   - Origin and destination addresses
   - `distance`, `weight`, `volume`: Shipment specifications
   - `estimated_cost`, `actual_cost`: Financial information
   - `pickup_datetime`, `delivery_datetime`: Timeline information
   - `status`: Pending, in-transit, delivered, or cancelled

### Filament Resources
The application includes Filament resources for managing all trucking entities:

1. **Truck Resource**:
   - Form with sections for truck information, maintenance details, and notes
   - Table with searchable and sortable columns
   - Status badges with color coding

2. **Driver Resource**:
   - Form with sections for personal information, driver details, and address
   - Table with searchable and sortable columns
   - Full name computed from first and last name

3. **Customer Resource**:
   - Form with sections for customer information, address, and details
   - Table with searchable and sortable columns
   - Customer type badges with color coding

4. **Shipment Resource**:
   - Form with sections for shipment info, origin/destination, details, and timeline
   - Relationship fields for customer, driver, and truck
   - Table with searchable and sortable columns
   - Status badges with color coding

### Authentication and Authorization
- Super admin user created during seeding with email `admin@example.com` and password `password`
- Role-based access control using Filament Shield
- Permission management for all resources

## Development Conventions

### Code Style
- Follows PSR-4 autoloading standards
- Laravel coding conventions
- Uses PHP 8.2+ features where appropriate
- Type declarations for function parameters and return types where possible

### Testing
- PHPUnit for unit and feature testing
- Tests located in `tests/` directory
- Factories in `database/factories/` for test data generation
- Use `composer run test` to run the test suite

### Environment Configuration
- Environment-specific configuration via `.env` file
- Application key generation with `php artisan key:generate`
- Database, cache, session, and mail configuration via environment variables

## Common Commands

- `php artisan migrate` - Run database migrations
- `php artisan migrate:rollback` - Rollback migrations
- `php artisan db:seed` - Seed the database
- `php artisan filament:install --panels` - Install Filament panels
- `php artisan shield:install admin` - Install Filament Shield
- `php artisan make:filament-resource ModelName --panel=admin` - Create Filament resource
- `php artisan tinker` - Interactive shell
- `php artisan cache:clear` - Clear application cache
- `php artisan config:clear` - Clear configuration cache
- `npm run build` - Build production assets
- `npm run dev` - Build development assets with hot reload