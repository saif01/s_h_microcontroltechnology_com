# Business Website Platform - Project Structure

This is a comprehensive business website platform built according to the SRS document. The platform supports multiple business types with configurable modules.

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 with Vuetify 3
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Sanctum (API tokens)
- **Build Tool**: Vite

## Project Structure

### Backend (Laravel)

#### Models (`app/Models/`)
- **Core Models**: Page, Module, Menu, Setting, Lead, User
- **Optional Modules**: Service, Product, Portfolio, BlogPost, Faq
- **Extended Modules**: Career, JobApplication, Booking, Event, EventRegistration, Branch
- **Supporting**: Category, Tag, Media

#### Controllers
- **API Controllers** (`app/Http/Controllers/Api/`): Admin panel API endpoints
  - AuthController (login/logout)
  - PageController, ServiceController, ProductController
  - LeadController, SettingController
  
- **Public Controllers** (`app/Http/Controllers/Public/`): Public website endpoints
  - HomeController, PageController, ServiceController, ProductController
  - ContactController

#### Routes
- `routes/api.php`: Admin API routes (protected with Sanctum)
- `routes/web.php`: Public API routes + Vue SPA catch-all

#### Database
- All migrations created for core and optional modules
- Seeders for initial modules and admin user

### Frontend (Vue 3)

#### Main Entry Point
- **`resources/js/app.js`**: Main application entry point
  - Initializes Vue app instance
  - Registers all plugins and utilities
  - Mounts the application

#### Utilities (`resources/js/utils/`)
- **`axios.config.js`**: Axios HTTP client configuration
  - Base URL configuration (development/production)
  - Default headers setup
  - Request interceptors (authentication tokens)
  - Response interceptors (error handling, CORS, 401 errors)

#### Plugins (`resources/js/plugins/`)
- **`vuetify.js`**: Vuetify UI framework configuration
  - Component registration
  - Directive registration
  - Theme configuration

- **`progressBar.js`**: Vue Progress Bar plugin configuration
  - Progress bar options (color, thickness, location)
  - Router helper functions for progress bar access
  - Setup functions for router hooks

- **`sweetalert.js`**: SweetAlert2 plugin configuration
  - Toast notifications setup
  - Global Swal and Toast exposure
  - Configuration for alerts and notifications

#### Components
- **Admin Components** (`resources/js/components/admin/`):
  - AdminLayout.vue (admin navigation with sidebar, app bar, footer)
  - AdminDashboard.vue (dashboard with statistics)
  - AdminPages.vue, AdminServices.vue, AdminProducts.vue
  - AdminLeads.vue, AdminUsers.vue
  - AdminRoles.vue, AdminPermissions.vue
  - AdminSettings.vue, AdminLogin.vue

- **Public Components** (`resources/js/components/public/`):
  - PublicLayout.vue (public website layout)
  - HomePage.vue (homepage component)
  - ServicesPage.vue, ProductsPage.vue
  - ContactPage.vue

#### Mixins (`resources/js/mixins/`)
- **`adminPaginationMixin.js`**: Shared pagination logic for admin components

#### Routes (`resources/js/routes.js`)
- Public routes configuration
- Admin routes configuration (with authentication guards)
- Router navigation hooks for progress bar
- Route meta information handling

## Setup Instructions

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

Copy `.env.example` to `.env` and configure:
- Database connection
- APP_URL
- Mail settings

```bash
php artisan key:generate
```

### 3. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
```

This will create:
- All database tables
- Module definitions (disabled by default)
- Admin user (email: admin@example.com, password: password)

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

## API Endpoints

### Admin API (`/api/v1/`)

**Authentication:**
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/logout` - Logout (requires auth)
- `GET /api/v1/auth/user` - Get current user (requires auth)

**Protected Routes (require authentication):**
- `GET /api/v1/pages` - List pages
- `POST /api/v1/pages` - Create page
- `GET /api/v1/pages/{id}` - Get page
- `PUT /api/v1/pages/{id}` - Update page
- `DELETE /api/v1/pages/{id}` - Delete page

- Similar CRUD for services, products, leads, etc.

### Public API (`/api/public/`)

- `GET /api/public/home` - Homepage data
- `GET /api/public/pages/{slug}` - Get page by slug
- `GET /api/public/services` - List services
- `GET /api/public/services/{slug}` - Get service by slug
- `GET /api/public/products` - List products
- `POST /api/public/contact` - Submit contact form

## Features Implemented

✅ **Core Features:**
- Pages management
- Menus management
- Settings management
- Leads management
- Module system (enable/disable modules)

✅ **Optional Modules:**
- Services catalog
- Products catalog
- Portfolio/Projects
- Blog/News
- FAQ
- Careers & Job Applications
- Booking/Appointments
- Events & Registrations
- Multi-location/Branches

✅ **Admin Panel:**
- Authentication (Sanctum)
- Dashboard with statistics
- CRUD operations for content
- Leads management and export
- Role-based permissions system
- User management
- Modern UI with gradient design
- Progress bar for route navigation
- Toast notifications

✅ **Public Website:**
- Dynamic homepage
- Page system
- Services/Products display
- Contact forms

✅ **Frontend Architecture:**
- Modular plugin system (Vuetify, ProgressBar, SweetAlert)
- Utility functions for axios configuration
- Organized component structure
- Mixins for shared functionality
- Centralized CSS variables for theming
- Responsive design with compact tables

## Frontend File Structure

```
resources/js/
├── app.js                          # Main entry point (cleaned and organized)
├── bootstrap.js                    # Bootstrap configuration
├── routes.js                       # Vue Router configuration
│
├── utils/                          # Utility functions
│   └── axios.config.js            # Axios HTTP client configuration
│
├── plugins/                        # Vue plugins
│   ├── vuetify.js                 # Vuetify UI framework setup
│   ├── progressBar.js             # Progress bar plugin setup
│   └── sweetalert.js              # SweetAlert2 plugin setup
│
├── mixins/                         # Vue mixins for shared logic
│   └── adminPaginationMixin.js    # Pagination mixin for admin components
│
└── components/
    ├── app.vue                     # Root component
    ├── admin/                      # Admin panel components
    │   ├── AdminLayout.vue        # Admin layout (sidebar, app bar, footer)
    │   ├── AdminDashboard.vue     # Dashboard
    │   ├── AdminPages.vue         # Pages management
    │   ├── AdminServices.vue      # Services management
    │   ├── AdminProducts.vue      # Products management
    │   ├── AdminLeads.vue         # Leads management
    │   ├── AdminUsers.vue         # User management
    │   ├── AdminRoles.vue         # Role management
    │   ├── AdminPermissions.vue   # Permission management
    │   ├── AdminSettings.vue      # Settings management
    │   └── AdminLogin.vue         # Admin login page
    │
    ├── public/                     # Public website components
    │   ├── PublicLayout.vue       # Public layout
    │   ├── HomePage.vue           # Homepage
    │   ├── ServicesPage.vue       # Services listing
    │   ├── ProductsPage.vue       # Products listing
    │   └── ContactPage.vue        # Contact page
    │
    └── pages/                      # Additional page components
        ├── dashboard.vue
        └── about/
            └── index.vue
```

## Styling & Assets

### CSS/SASS Files
- **`resources/sass/app.scss`**: Main stylesheet
  - Bootstrap import
  - Vuetify styles
  - CSS custom properties (variables) for admin theme colors
  - Admin table styles (compact, bordered, responsive)
  - Footer styles

- **`resources/css/app.css`**: Additional styles
  - Common admin table styles
  - Progress bar styles
  - Navigation menu adjustments

### CSS Variables (Centralized in `app.scss`)
- `--admin-gradient-start`: Primary gradient start color (#2c73d2)
- `--admin-gradient-end`: Primary gradient end color (#008f7a)
- `--admin-gradient-primary`: Complete gradient definition
- `--admin-overlay-*`: Various overlay opacity values
- `--admin-text-primary`: Primary text color (#ffffff)

## Next Steps (To Complete)

1. ✅ **Frontend Architecture**: Completed modular plugin system
2. ✅ **Admin UI Design**: Completed modern gradient design with animations
3. ✅ **Progress Bar**: Implemented for route navigation
4. ✅ **Table Styles**: Added compact, bordered, responsive table design
5. **File Upload**: Implement media library and file upload functionality
6. **Email Notifications**: Configure and test email sending for leads
7. **SEO**: Implement SEO meta tags management
8. **Settings UI**: Enhance admin UI for managing site settings

## Default Admin Credentials

- **Email**: admin@example.com
- **Password**: password

⚠️ **Change these immediately in production!**

## Module Configuration

Modules are stored in the `modules` table. To enable a module:

```php
Module::where('name', 'services')->update(['enabled' => true]);
```

Or via API/Admin panel (when implemented).

## Notes

- This is a foundational structure that can be expanded
- All core models and relationships are set up
- Admin authentication is working
- Public API endpoints are ready
- Vue components need routes configuration
- Additional admin and public components can be added incrementally

