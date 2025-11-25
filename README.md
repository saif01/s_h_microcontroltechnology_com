# Business Website Platform

A comprehensive, generic business website platform built according to SRS specifications. This platform can represent almost any type of business with configurable modules.

## 🚀 Features

### Core Features
- ✅ Dynamic pages management
- ✅ Menu/Navigation management
- ✅ Settings management
- ✅ Leads/Contact form management
- ✅ Newsletter subscriptions management
- ✅ Module system (enable/disable features)
- ✅ Admin authentication (Laravel Sanctum)
- ✅ SEO support (meta tags, OG tags)
- ✅ Role-based access control (RBAC)
- ✅ User management with permissions
- ✅ Login & Visitor logs tracking

### Optional Modules
- ✅ Services catalog with categories
- ✅ Products catalog with categories, tags, and specifications
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
- ✅ Product management with:
  - Multi-tab form (Basic Info, Media, Pricing, Categories & Tags, Specifications, Features, Downloads, FAQs, Warranty & Service, SEO, Settings)
  - Image upload with preview
  - File upload for downloads
  - Category and Tag management
  - Product details view
- ✅ Category management (hierarchical)
- ✅ Tag management
- ✅ Leads management and export (CSV)
- ✅ Newsletter subscriptions management and export (CSV)
- ✅ Settings management
- ✅ User, Role, and Permission management
- ✅ Login logs and Visitor logs
- ✅ Modern UI with Vuetify 3

### Public Website
- ✅ Responsive design (Vuetify 3)
- ✅ Dynamic homepage
- ✅ Services/Products display with:
  - Category filtering
  - Search functionality
  - Sorting options
  - Product comparison tool (compare up to 3 products)
- ✅ Product detail pages with:
  - Hero section
  - Product gallery with zoom
  - Key features
  - Technical specifications
  - Downloadable datasheets
  - Customer FAQs
  - Warranty & service information
- ✅ Contact forms
- ✅ Newsletter subscription (footer)
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

**Content Management:**
- `GET /api/v1/pages` - List pages
- `POST /api/v1/pages` - Create page
- `GET /api/v1/pages/{id}` - Get page
- `PUT /api/v1/pages/{id}` - Update page
- `DELETE /api/v1/pages/{id}` - Delete page

Similar CRUD for: `services`, `products`, `categories`, `tags`, etc.

**Product Management:**
- `GET /api/v1/products` - List products (with pagination, filtering, sorting)
- `POST /api/v1/products` - Create product
- `GET /api/v1/products/{id}` - Get product (by ID or slug)
- `PUT /api/v1/products/{id}` - Update product
- `DELETE /api/v1/products/{id}` - Delete product

**Category Management:**
- `GET /api/v1/categories` - List categories (supports filtering by type, parent_id, published)
- `POST /api/v1/categories` - Create category
- `GET /api/v1/categories/{id}` - Get category
- `PUT /api/v1/categories/{id}` - Update category
- `DELETE /api/v1/categories/{id}` - Delete category

**Tag Management:**
- `GET /api/v1/tags` - List tags (supports filtering by type, search)
- `POST /api/v1/tags` - Create tag
- `GET /api/v1/tags/{id}` - Get tag (by ID or slug)
- `PUT /api/v1/tags/{id}` - Update tag
- `DELETE /api/v1/tags/{id}` - Delete tag

**File Upload:**
- `POST /api/v1/upload/image` - Upload single image
- `POST /api/v1/upload/images` - Upload multiple images
- `POST /api/v1/upload/file` - Upload file (PDF, DOC, ZIP, etc.)
- `DELETE /api/v1/upload/image` - Delete image

**Leads Export:**
- `GET /api/v1/leads/export/csv` - Export leads to CSV

**Newsletter Subscriptions:**
- `GET /api/v1/newsletters` - List newsletter subscriptions (requires `view-leads` permission)
- `GET /api/v1/newsletters/{id}` - Get subscription details
- `PUT /api/v1/newsletters/{id}` - Update subscription status (requires `manage-leads` permission)
- `DELETE /api/v1/newsletters/{id}` - Delete subscription (requires `manage-leads` permission)
- `GET /api/v1/newsletters/export/csv` - Export subscriptions to CSV (requires `manage-leads` permission)

**User Management:**
- `GET /api/v1/users` - List users
- `POST /api/v1/users` - Create user
- `GET /api/v1/users/{id}` - Get user
- `PUT /api/v1/users/{id}` - Update user
- `DELETE /api/v1/users/{id}` - Delete user

**Role & Permission Management:**
- `GET /api/v1/roles` - List roles
- `POST /api/v1/roles` - Create role
- `PUT /api/v1/roles/{id}/permissions` - Sync role permissions
- `GET /api/v1/permissions` - List permissions
- `GET /api/v1/permissions/groups` - Get permission groups

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
- `POST /api/openapi/newsletter/subscribe` - Subscribe to newsletter

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
│   │   ├── Api/              # Admin API controllers
│   │   │   ├── auth/
│   │   │   ├── content/
│   │   │   ├── leads/
│   │   │   ├── NewsletterController.php
│   │   │   ├── logs/
│   │   │   ├── products/
│   │   │   ├── settings/
│   │   │   ├── upload/
│   │   │   └── users/
│   │   └── Public/            # Public website controllers
│   │       ├── pages/
│   │       ├── NewsletterController.php
│   │       ├── products/
│   │       └── services/
│   └── Middleware/            # Authentication & authorization
├── Models/                    # Eloquent models

database/
├── migrations/               # Database migrations
└── seeders/                  # Database seeders

resources/
├── js/
│   ├── components/
│   │   ├── admin/            # Admin panel components
│   │   │   ├── auth/
│   │   │   ├── content/
│   │   │   ├── leads/
│   │   │   ├── newsletters/
│   │   │   ├── logs/
│   │   │   ├── products/
│   │   │   ├── settings/
│   │   │   └── users/
│   │   └── public/           # Public website components
│   │       ├── pages/
│   │       ├── products/
│   │       └── services/
│   ├── mixins/               # Vue mixins
│   ├── plugins/              # Vue plugins (Vuetify, SweetAlert, ProgressBar)
│   ├── routes.js             # Vue Router configuration
│   └── utils/                # Utility functions
└── sass/
    └── app.scss              # Main stylesheet

routes/
├── api.php                   # API routes
└── web.php                   # Web routes (includes public API)

public/
└── uploads/                  # Uploaded files (images, documents)
    ├── products/
    └── ...
```

## 🔒 Security

- Admin routes protected with Laravel Sanctum
- Password hashing
- CSRF protection
- XSS protection
- SQL injection protection (Eloquent ORM)
- Role-based access control (RBAC)
- Permission-based route protection

## 📝 Notes

- This is a foundational structure that can be expanded
- All core models and relationships are set up
- Additional features can be added incrementally
- The platform is designed to be modular and configurable
- Product management includes comprehensive features for industrial/tech product websites
- File uploads are stored in `public/uploads/{folder}/` for easy access

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
