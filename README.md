# Business Website Platform

A comprehensive, generic business website platform built according to SRS specifications. This platform can represent almost any type of business with configurable modules.

## 🚀 Features

### Core Features
- ✅ Dynamic pages management
- ✅ Menu/Navigation management
- ✅ Settings management
- ✅ Leads/Contact form management
- ✅ Module system (enable/disable features)
- ✅ Admin authentication (Laravel Sanctum)
- ✅ SEO support (meta tags, OG tags)

### Optional Modules
- ✅ Services catalog
- ✅ Products catalog
- ✅ Portfolio/Projects
- ✅ Blog/News
- ✅ FAQ
- ✅ Careers & Job Applications
- ✅ Booking/Appointments
- ✅ Events & Registrations
- ✅ Multi-location/Branches
- ✅ Media library

### Admin Panel
- ✅ Dashboard with statistics
- ✅ Content management (CRUD)
- ✅ Leads management and export
- ✅ Settings management
- ✅ Role-based access control

### Public Website
- ✅ Responsive design (Vuetify)
- ✅ Dynamic homepage
- ✅ Services/Products display
- ✅ Contact forms
- ✅ SEO optimized

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL/PostgreSQL/SQLite

## 🔧 Installation

### 1. Clone and Install Dependencies

```bash
git clone <repository-url>
cd s_h_micro_control
composer install
npm install
```

### 2. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env` file:
- Database connection
- `APP_URL`
- Mail settings (for notifications)

### 3. Database Setup

```bash
php artisan migrate
php artisan db:seed
```

This will create:
- All database tables
- Module definitions (disabled by default)
- Default admin user

### 4. Build Assets

```bash
npm run dev  # Development
# or
npm run build  # Production
```

### 5. Start Development Server

```bash
php artisan serve
npm run dev
```

Visit:
- Public website: `http://localhost:8000`
- Admin panel: `http://localhost:8000/admin/login`

## 🔑 Default Admin Credentials

- **Email**: admin@example.com
- **Password**: password

⚠️ **Change these immediately in production!**

## 📚 API Documentation

### Admin API (`/api/v1/`)

All admin endpoints require authentication via Bearer token.

**Authentication:**
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/logout` - Logout
- `GET /api/v1/auth/user` - Get current user

**Resources (CRUD):**
- `GET /api/v1/pages` - List pages
- `POST /api/v1/pages` - Create page
- `GET /api/v1/pages/{id}` - Get page
- `PUT /api/v1/pages/{id}` - Update page
- `DELETE /api/v1/pages/{id}` - Delete page

Similar endpoints for: `services`, `products`, `leads`, etc.

**Leads Export:**
- `GET /api/v1/leads/export/csv` - Export leads to CSV

### Public API (`/api/openapi/`)

- `GET /api/openapi/home` - Homepage data
- `GET /api/openapi/pages/{slug}` - Get page by slug
- `GET /api/openapi/services` - List services
- `GET /api/openapi/services/{slug}` - Get service by slug
- `GET /api/openapi/products` - List products (supports category filter, search, sorting)
- `GET /api/openapi/products/{slug}` - Get product by slug (includes categories, tags, specifications, downloads)
- `GET /api/openapi/settings` - Get public settings
- `POST /api/openapi/contact` - Submit contact form

## 🎨 Module Configuration

Modules are stored in the `modules` table. To enable a module:

**Via Code:**
```php
Module::where('name', 'services')->update(['enabled' => true]);
```

**Via Database:**
```sql
UPDATE modules SET enabled = 1 WHERE name = 'services';
```

**Available Modules:**
- `services` - Services catalog
- `products` - Products catalog
- `portfolio` - Portfolio/Projects
- `blog` - Blog/News
- `faq` - FAQ
- `careers` - Careers & Recruitment
- `booking` - Appointment booking
- `events` - Events & Registrations
- `branches` - Multi-location/Branches
- `ecommerce` - E-commerce (future)
- `multilanguage` - Multi-language support (future)

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/          # Admin API controllers
│   │   └── Public/       # Public website controllers
│   └── Middleware/       # Authentication & authorization
├── Models/               # Eloquent models

database/
├── migrations/           # Database migrations
└── seeders/             # Database seeders

resources/
├── js/
│   ├── components/
│   │   ├── admin/       # Admin panel components
│   │   └── public/      # Public website components
│   └── routes.js        # Vue Router configuration
└── views/
    └── welcome.blade.php

routes/
├── api.php              # API routes
└── web.php              # Web routes (includes public API)
```

## 🔒 Security

- Admin routes protected with Laravel Sanctum
- Password hashing
- CSRF protection
- XSS protection
- SQL injection protection (Eloquent ORM)
- Role-based access control

## 📝 Notes

- This is a foundational structure that can be expanded
- All core models and relationships are set up
- Additional features can be added incrementally
- The platform is designed to be modular and configurable

## 🛠️ Development

### Adding a New Module

1. Create migration: `php artisan make:migration create_[module]_table`
2. Create model: `php artisan make:model [Module]`
3. Create controller: `php artisan make:controller Api/[Module]Controller --api`
4. Add to ModuleSeeder
5. Create Vue components
6. Update routes

### Testing

```bash
php artisan test
```

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
