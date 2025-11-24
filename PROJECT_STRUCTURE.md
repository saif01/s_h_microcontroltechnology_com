# Business Website Platform - Project Structure

This is a comprehensive business website platform built according to the SRS document. The platform supports multiple business types with configurable modules.

## Technology Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 with Vuetify 3
- **Database**: MySQL/PostgreSQL/SQLite
- **Authentication**: Laravel Sanctum (API tokens)
- **Build Tool**: Vite
- **UI Framework**: Vuetify 3
- **Additional Libraries**: SweetAlert2, Vue Progress Bar

## Project Structure

### Backend (Laravel)

#### Models (`app/Models/`)
- **Core Models**: Page, Module, Menu, Setting, Lead, User
- **Optional Modules**: Service, Product, Portfolio, BlogPost, Faq
- **Extended Modules**: Career, JobApplication, Booking, Event, EventRegistration, Branch
- **Supporting**: Category, Tag, Media
- **Logging**: LoginLog, VisitorLog
- **RBAC**: Role, Permission

#### Controllers

**API Controllers** (`app/Http/Controllers/Api/`): Admin panel API endpoints
- `auth/AuthController.php` - Authentication (login/logout/user)
- `content/PageController.php` - Pages management
- `content/ServiceController.php` - Services management
- `products/ProductController.php` - Products management
- `products/CategoryController.php` - Categories management
- `products/TagController.php` - Tags management
- `leads/LeadController.php` - Leads management and export
- `settings/SettingController.php` - Settings management
- `upload/UploadController.php` - File and image uploads
- `users/UserController.php` - User management
- `users/RoleController.php` - Role management
- `users/PermissionController.php` - Permission management
- `logs/LoginLogController.php` - Login logs
- `logs/VisitorLogController.php` - Visitor logs

**Public Controllers** (`app/Http/Controllers/Public/`): Public website endpoints
- `pages/HomeController.php` - Homepage data
- `pages/PageController.php` - Public pages
- `pages/ContactController.php` - Contact form submission
- `products/ProductController.php` - Public products listing and details
- `services/ServiceController.php` - Public services listing and details

#### Routes
- `routes/api.php`: Admin API routes (protected with Sanctum) and public API routes
- `routes/web.php`: Public API routes + Vue SPA catch-all

#### Database
- All migrations created for core and optional modules
- Seeders for initial modules and admin user
- Pivot tables for many-to-many relationships (category_product, tag_product, etc.)

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

**Admin Components** (`resources/js/components/admin/`):
- `AdminLayout.vue` - Admin layout (sidebar, app bar, footer)
- `AdminDashboard.vue` - Dashboard with statistics
- `auth/AdminLogin.vue` - Admin login page
- `content/AdminPages.vue` - Pages management
- `content/AdminServices.vue` - Services management
- `products/AdminProducts.vue` - Products management (11-tab form)
- `products/AdminCategories.vue` - Categories management
- `products/AdminTags.vue` - Tags management
- `leads/AdminLeads.vue` - Leads management
- `users/AdminUsers.vue` - User management
- `users/AdminRoles.vue` - Role management
- `users/AdminPermissions.vue` - Permission management
- `settings/AdminSettings.vue` - Settings management
- `logs/AdminLoginLogs.vue` - Login logs
- `logs/AdminVisitorLogs.vue` - Visitor logs

**Public Components** (`resources/js/components/public/`):
- `PublicLayout.vue` - Public website layout
- `pages/HomePage.vue` - Homepage component
- `pages/AboutPage.vue` - About page
- `pages/ContactPage.vue` - Contact page
- `services/ServicesPage.vue` - Services listing
- `services/ServiceDetailPage.vue` - Service detail page
- `products/ProductsPage.vue` - Products listing with filters, search, and comparison
- `products/ProductDetailPage.vue` - Product detail with gallery, specs, FAQs, warranty

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

- Similar CRUD for services, products, categories, tags, leads, users, roles, permissions

**File Upload:**
- `POST /api/v1/upload/image` - Upload single image
- `POST /api/v1/upload/images` - Upload multiple images
- `POST /api/v1/upload/file` - Upload file (PDF, DOC, ZIP, etc.)
- `DELETE /api/v1/upload/image` - Delete image

**Leads Export:**
- `GET /api/v1/leads/export/csv` - Export leads to CSV

**Logs:**
- `GET /api/v1/login-logs` - List login logs
- `GET /api/v1/login-logs/statistics` - Get login statistics
- `GET /api/v1/visitor-logs` - List visitor logs
- `GET /api/v1/visitor-logs/statistics` - Get visitor statistics

### Public API (`/api/openapi/`)

- `GET /api/openapi/home` - Homepage data
- `GET /api/openapi/pages/{slug}` - Get page by slug
- `GET /api/openapi/services` - List services
- `GET /api/openapi/services/{slug}` - Get service by slug
- `GET /api/openapi/products` - List products (supports category filter, search, sorting)
- `GET /api/openapi/products/{slug}` - Get product by slug (includes categories, tags, specifications, downloads)
- `GET /api/openapi/categories` - List categories (supports type filter, pagination)
- `GET /api/openapi/settings` - Get public settings
- `POST /api/openapi/contact` - Submit contact form

## Features Implemented

✅ **Core Features:**
- Pages management
- Menus management
- Settings management
- Leads management
- Module system (enable/disable modules)
- Role-based access control (RBAC)
- User management
- Login and Visitor logs

✅ **Optional Modules:**
- Services catalog
- Products catalog with categories and tags
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
- Product management with 11-tab form:
  - Basic Info, Media, Pricing, Categories & Tags
  - Specifications, Features, Downloads, FAQs
  - Warranty & Service, SEO, Settings
- Category management (hierarchical)
- Tag management
- Image and file upload with preview
- Leads management and export
- Role-based permissions system
- User management
- Login and Visitor logs
- Modern UI with gradient design
- Progress bar for route navigation
- Toast notifications (SweetAlert2)

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
- Responsive design

✅ **Frontend Architecture:**
- Modular plugin system (Vuetify, ProgressBar, SweetAlert)
- Utility functions for axios configuration
- Organized component structure (admin and public folders organized by feature)
- Mixins for shared functionality
- Centralized CSS variables for theming
- Responsive design with compact tables
- Modern, clean UI design for public pages
- Product comparison functionality
- Advanced filtering and search capabilities

## Frontend File Structure

```
resources/js/
├── app.js                          # Main entry point
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
    ├── PageLoader.vue              # Page loader component
    │
    ├── admin/                      # Admin panel components
    │   ├── AdminLayout.vue        # Admin layout (sidebar, app bar, footer)
    │   ├── AdminDashboard.vue    # Dashboard
    │   │
    │   ├── auth/                   # Authentication
    │   │   └── AdminLogin.vue     # Admin login page
    │   │
    │   ├── content/                # Content management
    │   │   ├── AdminPages.vue     # Pages management
    │   │   └── AdminServices.vue  # Services management
    │   │
    │   ├── products/               # Product management
    │   │   ├── AdminProducts.vue  # Products management (11-tab form)
    │   │   ├── AdminCategories.vue # Categories management
    │   │   └── AdminTags.vue      # Tags management
    │   │
    │   ├── leads/                  # Leads management
    │   │   └── AdminLeads.vue     # Leads management
    │   │
    │   ├── users/                  # User management
    │   │   ├── AdminUsers.vue     # User management
    │   │   ├── AdminRoles.vue     # Role management
    │   │   └── AdminPermissions.vue # Permission management
    │   │
    │   ├── settings/              # Settings
    │   │   └── AdminSettings.vue # Settings management
    │   │
    │   └── logs/                   # Logs
    │       ├── AdminLoginLogs.vue # Login logs
    │       └── AdminVisitorLogs.vue # Visitor logs
    │
    └── public/                     # Public website components
        ├── PublicLayout.vue       # Public layout
        │
        ├── pages/                  # Page components
        │   ├── HomePage.vue       # Homepage
        │   ├── AboutPage.vue      # About page
        │   └── ContactPage.vue    # Contact page
        │
        ├── products/               # Product pages
        │   ├── ProductsPage.vue   # Products listing with filters, search, comparison
        │   └── ProductDetailPage.vue # Product detail with gallery, specs, FAQs, warranty
        │
        └── services/               # Service pages
            ├── ServicesPage.vue   # Services listing
            └── ServiceDetailPage.vue # Service detail page
```

## Backend File Structure

```
app/Http/Controllers/
├── Api/                            # Admin API controllers
│   ├── auth/
│   │   └── AuthController.php
│   ├── content/
│   │   ├── PageController.php
│   │   └── ServiceController.php
│   ├── leads/
│   │   └── LeadController.php
│   ├── logs/
│   │   ├── LoginLogController.php
│   │   └── VisitorLogController.php
│   ├── products/
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   └── TagController.php
│   ├── settings/
│   │   └── SettingController.php
│   ├── upload/
│   │   └── UploadController.php
│   └── users/
│       ├── UserController.php
│       ├── RoleController.php
│       └── PermissionController.php
│
└── Public/                         # Public website controllers
    ├── pages/
    │   ├── HomeController.php
    │   ├── PageController.php
    │   └── ContactController.php
    ├── products/
    │   └── ProductController.php
    └── services/
        └── ServiceController.php
```

## Styling & Assets

### CSS/SASS Files
- **`resources/sass/app.scss`**: Main stylesheet
  - Bootstrap import
  - Vuetify styles
  - CSS custom properties (variables) for admin theme colors
  - Admin table styles (compact, bordered, responsive)
  - Footer styles
  - SweetAlert2 z-index overrides

### CSS Variables (Centralized in `app.scss`)
- `--admin-gradient-start`: Primary gradient start color (#2c73d2)
- `--admin-gradient-end`: Primary gradient end color (#008f7a)
- `--admin-gradient-primary`: Complete gradient definition
- `--admin-overlay-*`: Various overlay opacity values
- `--admin-text-primary`: Primary text color (#ffffff)

### File Uploads
- Images and files are uploaded to `public/uploads/{folder}/`
- Product images: `public/uploads/products/`
- Files are named with prefix: `{prefix}-{random}.{ext}`
- Supports single image, multiple images, and general file uploads

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
- Handles paginated category responses correctly

## Admin Product Management

### AdminProducts.vue - Product Management

**Features:**
- **11-Tab Form**:
  1. Basic Info - Title, slug, SKU, descriptions
  2. Media - Thumbnail and gallery images with preview
  3. Pricing - Price, price range, show price toggle
  4. Categories & Tags - Multi-select with auto-creation
  5. Specifications - Dynamic key-value pairs
  6. Features - Key features list
  7. Downloads - File uploads with preview
  8. FAQs - Question-answer pairs
  9. Warranty & Service - Warranty information
  10. SEO - Meta tags and OG image
  11. Settings - Published, featured, stock, order
- **Image Upload**: 
  - File input with preview before upload
  - Uploads on form submission
  - Supports thumbnail and gallery images
  - Files named with product name prefix
- **File Upload**: 
  - Download section supports file uploads
  - Auto-detects file type and size
  - Preview before submission
- **Product Details View**: Read-only view of all product information
- **Edit from Details**: Seamless transition from details to edit view

## Notes

- This is a foundational structure that can be expanded
- All core models and relationships are set up
- Admin authentication is working
- Public API endpoints are ready
- Vue components are fully configured with routes
- Product pages feature modern, clean design suitable for business/industrial websites
- Admin and public folders are organized by feature for better maintainability
- File uploads are stored in public directory for easy access
- Additional admin and public components can be added incrementally
