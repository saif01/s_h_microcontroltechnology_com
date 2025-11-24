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
  - ServicesPage.vue, ServiceDetailPage.vue
  - ProductsPage.vue (product listing with filters, search, and comparison)
  - ProductDetailPage.vue (detailed product view with gallery, specs, FAQs, warranty)
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

### Public API (`/api/openapi/`)

- `GET /api/openapi/home` - Homepage data
- `GET /api/openapi/pages/{slug}` - Get page by slug
- `GET /api/openapi/services` - List services
- `GET /api/openapi/services/{slug}` - Get service by slug
- `GET /api/openapi/products` - List products (supports category filter, search, sorting)
- `GET /api/openapi/products/{slug}` - Get product by slug (includes categories, tags, specifications, downloads)
- `GET /api/openapi/settings` - Get public settings
- `POST /api/openapi/contact` - Submit contact form

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
- Services/Products display with filtering and search
- Product comparison tool (compare up to 3 products side-by-side)
- Product detail pages with:
  - Hero section with product overview
  - Product gallery with image zoom
  - Key features and technical specifications
  - Downloadable datasheets and documentation
  - Customer FAQs section
  - Warranty & service information
- Contact forms

✅ **Frontend Architecture:**
- Modular plugin system (Vuetify, ProgressBar, SweetAlert)
- Utility functions for axios configuration
- Organized component structure
- Mixins for shared functionality
- Centralized CSS variables for theming
- Responsive design with compact tables
- Modern, clean UI design for public pages
- Product comparison functionality
- Advanced filtering and search capabilities

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
    │   ├── ServiceDetailPage.vue  # Service detail page
    │   ├── ProductsPage.vue       # Products listing with filters, search, comparison
    │   ├── ProductDetailPage.vue  # Product detail with gallery, specs, FAQs, warranty
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

## Product Pages Features

### ProductsPage.vue - Product Listing

**Features:**
- **Hero Section**: Modern hero with gradient background, product range title, and premium quality badge
- **Category Filtering**: Filter products by category with icon-based category buttons
- **Search Functionality**: Real-time search across product titles, descriptions, SKU, and specifications
- **Sorting Options**: Sort by newest, price (low to high/high to low), name (A-Z/Z-A), or featured
- **Product Cards**: 
  - Product images with hover effects
  - Quick specifications preview
  - Price display with optional old price
  - Featured product badges
  - Quick action buttons (view, compare)
- **Product Comparison Tool**: 
  - Compare up to 3 products side-by-side
  - Comparison dialog showing:
    - Price comparison
    - Key specifications
    - Technical differences
    - Recommended use-cases
    - Quick access to product details
- **Responsive Design**: Optimized for desktop, tablet, and mobile devices
- **Sticky Filter Bar**: Filter bar becomes sticky on scroll for easy access

### ProductDetailPage.vue - Product Details

**Features:**
- **Hero Section**: Full-width hero with product title, category badge, SKU, and price card
- **Product Gallery**: 
  - Main product image with zoom functionality
  - Thumbnail navigation
  - Image zoom dialog for detailed viewing
- **Key Features Section**: Highlighted product features with icons
- **Quick Specifications**: Quick specs preview in sidebar
- **Tabbed Content**:
  - **Overview**: Detailed product description and benefits
  - **Technical Specs**: Complete technical specifications table
  - **Features**: Detailed feature list with descriptions
  - **Downloads**: Downloadable datasheets, manuals, and documentation (PDF, ZIP, etc.)
  - **FAQs**: Expandable FAQ section with common questions
  - **Warranty & Service**: 
    - Warranty coverage details
    - Service and support information
    - Trust badges (Warranty, Delivery, Support)
- **Related Products**: Display related products at the bottom
- **Trust Badges**: Warranty, delivery, and support information
- **Responsive Design**: Fully responsive layout for all devices

**Technical Implementation:**
- Integrated with `/api/openapi/products` and `/api/openapi/products/{slug}` endpoints
- Handles missing data gracefully with fallback content
- Uses Vuetify components for consistent UI
- Optimized performance with computed properties
- Clean, maintainable code structure

## Notes

- This is a foundational structure that can be expanded
- All core models and relationships are set up
- Admin authentication is working
- Public API endpoints are ready
- Vue components are fully configured with routes
- Product pages feature modern, clean design suitable for business/industrial websites
- Additional admin and public components can be added incrementally

