# Laravel 12 Marketplace - Complete Project Documentation

This comprehensive documentation provides guidance for working with the Laravel 12 Marketplace application, a professional e-commerce platform designed specifically for the Indonesian market.

## Table of Contents
- [Quick Start](#quick-start)
- [Architecture Overview](#architecture-overview)
- [Database Structure](#database-structure)
- [Development Commands](#development-commands)
- [Features Overview](#features-overview)
- [Frontend Architecture](#frontend-architecture)
- [API Integration](#api-integration)
- [Security Features](#security-features)

## Quick Start

### Development Commands

#### Primary Development
- `composer dev` - Start full development environment (Laravel server, queue worker, logs, and Vite frontend build)
- `php artisan serve` - Start Laravel development server only
- `npm run dev` - Start Vite development server for frontend assets
- `npm run build` - Build production assets

#### Quick Setup & Maintenance
- `composer setup` - Complete project setup (key generation + fresh migration + auto-sync)
- `composer fresh` - Quick fresh migration with seeders (includes auto wilayah + RajaOngkir sync)
- `composer sync-wilayah` - Quick wilayah data re-import
- `composer sync-rajaongkir` - Quick RajaOngkir data sync

#### Testing & Code Quality
- `composer test` or `php artisan test` - Run PHPUnit test suite
- `./vendor/bin/pint` - Laravel Pint (PHP CS Fixer) for code formatting
- `./vendor/bin/pint --test` - Check code formatting without making changes

## Architecture Overview

### Multi-Role Authentication System
This marketplace application implements a sophisticated role-based authentication system supporting three distinct user types:

- **Owner**: Top-level administrative access with full system control
- **Admin**: Administrative access for content and system management
- **Customer**: End-user access for shopping and order management

### Application Structure
```
app/Http/Controllers/
├── AuthController.php              # Authentication (login/register)
├── HomeController.php              # Public pages (homepage/shop/product detail)
├── Owner/
│   └── OwnerDashboardController.php
├── Admin/
│   ├── AdminDashboardController.php
│   ├── CategoryController.php      # Category management
│   ├── ProductController.php       # Product management
│   ├── SystemSettingsController.php # System configuration
│   ├── UserController.php          # User management
│   └── BannerController.php        # Content management
└── Customer/
    ├── CustomerDashboardController.php
    ├── AddressController.php       # Address management
    ├── CartController.php          # Shopping cart
    ├── CheckoutController.php      # Order processing
    ├── ProfileController.php       # Profile management
    └── WishlistController.php      # Wishlist functionality
```

## Database Structure

### Core Entities and Relationships

#### **User Management**
- **User Model**: Multi-role authentication (owner/admin/customer)
- **Relationships**: addresses, wishlists, cartItems, orders, paymentTransactions
- **Business Logic**: Role-based access control, default address management

#### **Product Catalog**
- **Category Model**: Hierarchical structure with parent-child relationships
  - Two-tier system: Parent categories for navigation, subcategories for product assignment
  - Auto-generates slugs, supports unlimited nesting
  - Validation prevents products from being assigned to parent categories

- **Product Model**: Complete product management
  - Fields: name, slug, SKU, pricing, inventory, weight, dimensions
  - JSON attributes for flexible product properties
  - Advanced image management with WebP optimization
  - Stock tracking with low-stock alerts

- **ProductImage Model**: Professional image management
  - Multiple images per product with primary image designation
  - Automatic WebP conversion and optimization
  - File metadata storage and automatic cleanup

#### **E-commerce Operations**
- **Cart Model**: Shopping cart functionality with quantity management
- **Wishlist Model**: User wishlist with unique constraints
- **Order Model**: Complete order lifecycle management
  - Auto-generated order numbers
  - Status tracking: pending → paid → processing → shipped → delivered
  - Shipping integration with tracking numbers
  - JSON fields for flexible address and metadata storage

- **OrderItem Model**: Order line items with product snapshot
  - Historical price protection
  - Product data captured at time of purchase
  - Automatic subtotal calculations

#### **Payment Processing**
- **PaymentTransaction Model**: Comprehensive Midtrans integration
  - Transaction lifecycle management
  - Fraud detection and status tracking
  - Customer and item details storage
  - PDF receipt generation

#### **Address Management (Indonesian Focus)**
- **CustomerAddress Model**: Complete Indonesian address hierarchy
- **Location Models**: Province → City → District → Village
  - Full wilayah.id integration (91,000+ records)
  - RajaOngkir API mapping for shipping calculations
  - Optimized 3-second import via streaming parser

#### **System Configuration**
- **SystemSetting Model**: Application configuration with caching
  - Type casting for different data types
  - Public/private setting separation
  - RajaOngkir and Midtrans integration settings

- **Banner Model**: Homepage content management
  - Dynamic banner slider system
  - Image upload with automatic optimization
  - Sort ordering and styling options

### Database Relationships Summary

**One-to-Many Relationships:**
- User → CustomerAddress, Wishlist, Cart, Order, PaymentTransaction
- Category → Product (and self-referencing parent-children)
- Product → ProductImage
- Order → OrderItem
- Province → City → District → Village (hierarchical)

**Key Constraints:**
- Unique constraints: Email, product slug/SKU, order number, category slug
- Composite unique: Cart and Wishlist prevent duplicate user-product combinations
- Foreign key constraints with cascade deletes for data integrity

## Features Overview

### 🛍️ Public Store Frontend

#### **Homepage** (`/`)
- **Template**: Professional Furni Bootstrap template integration
- **Dynamic Content**: Database-driven banner slider and content sections
- **Featured Products**: Displays up to 8 featured products with WebP images
- **Responsive Design**: Mobile-first Bootstrap layout
- **Role-based Navigation**: Automatic dashboard redirection based on user role

#### **Shop Page** (`/shop`)
- **Modern Clean Design**: White background with professional styling
- **Advanced Category System**: Hierarchical navigation with dropdown menus
- **Search & Filtering**: Full-text search, category filters, sorting options
- **Product Grid**: Responsive 4-column desktop, 2-column mobile layout
- **Discount Badges**: Visual sale indicators with percentage calculations
- **URL Persistence**: All filters maintain state through parameters

#### **Product Detail Page** (`/product/{id}`)
- **Minimalist Design**: Clean layout with sticky image gallery
- **Image Gallery System**: Large main image with thumbnail navigation
- **Interactive Features**: AJAX wishlist integration, smooth image switching
- **Product Information**: Comprehensive details with specifications grid
- **Related Products**: Auto-generated product recommendations

### 🛒 Shopping Features

#### **Wishlist System**
- **AJAX Integration**: Real-time add/remove functionality
- **Visual Feedback**: Heart icons with color changes for wishlisted items
- **Authentication**: Middleware protection with guest redirects
- **Status Checking**: Auto-check wishlist status on page load
- **Shop Integration**: Wishlist filter in category navigation

#### **Shopping Cart**
- **Real-time Updates**: Dynamic cart counter in navigation
- **Quantity Management**: Adjust quantities with automatic subtotal calculation
- **Product Snapshots**: Price protection with historical data
- **Session Persistence**: Cart maintained across sessions
- **Clean Interface**: Modern cart design with item management

#### **Checkout Process**
- **Address Management**: Complete Indonesian address system
- **Shipping Calculation**: RajaOngkir API integration for accurate costs
- **Payment Processing**: Midtrans payment gateway with multiple methods
- **Order Tracking**: Complete status tracking from order to delivery
- **Receipt Generation**: PDF receipts and email notifications

### 🔐 User Management

#### **Multi-Role Authentication**
- **Role-based Access**: Owner/Admin/Customer with hierarchical permissions
- **Profile Management**: Complete user profile updates with password changes
- **Email Verification**: Manual verification control for admin users
- **Address Management**: Complete CRUD for customer addresses with modal interface

#### **Customer Features**
- **Dashboard**: Personalized customer dashboard with order history
- **Address Book**: Multiple addresses with default address management
- **Order Tracking**: Real-time order status updates
- **Profile Updates**: Secure profile and password management

### ⚙️ Admin Management System

#### **System Settings** (`/admin/settings`)
- **Business Configuration**: App name, tax rates, currency, business hours
- **Shipping Settings**: RajaOngkir integration with origin location picker
- **Payment Settings**: Midtrans gateway configuration with sandbox testing
- **API Integration**: Built-in connection testing for external services
- **Location Picker**: Visual province/city/district selection modal

#### **Category Management** (`/admin/categories`)
- **Hierarchical Structure**: Two-tier parent-child relationship system
- **Fashion Categories**: Pre-seeded with comprehensive fashion taxonomy
- **JSGrid Interface**: Professional data grid with filtering and sorting
- **Validation**: Prevents products from being assigned to parent categories
- **Auto Slug Generation**: SEO-friendly URL slugs

#### **Product Management** (`/admin/products`)
- **Complete Product CRUD**: Comprehensive product creation and management
- **Advanced Image System**: Multiple images with WebP optimization
- **Stock Management**: Inventory tracking with low stock alerts
- **Bulk Operations**: Bulk stock updates with multi-select interface
- **Category Integration**: Products linked to hierarchical category system
- **Search & Filtering**: Advanced filtering by name, SKU, category, status

#### **User Management** (`/admin/users`)
- **Role-based Management**: Create and manage all user types
- **Security Features**: Self-deletion protection, secure password handling
- **Advanced Filtering**: Filter by role, email verification status
- **Professional Interface**: JSGrid with responsive design
- **Email Verification**: Manual verification control for admin oversight

#### **Content Management** (`/admin/banners`)
- **Dynamic Banner Slider**: Replace static hero with manageable carousel
- **Image Management**: Professional upload with validation and cleanup
- **Styling Options**: Text position, colors, button configuration
- **Sort Ordering**: Manual sequence control for slider display

### 📊 API Integration

#### **RajaOngkir Integration** (Indonesian Shipping)
- **API Version**: Updated to V2 (2024) with new endpoints
- **Coverage**: 89.5% provinces, 87% cities mapped successfully
- **Smart Sync**: Fuzzy matching for province/city names with context validation
- **Quota Management**: Efficient sync using ~35 API calls per full sync
- **Working API Key**: Pre-configured for immediate development use

#### **Midtrans Payment Gateway**
- **Environment**: Sandbox ready with pre-configured credentials
- **Payment Methods**: Credit cards, virtual accounts, e-wallets (GoPay, ShopeePay, QRIS)
- **Connection Testing**: Built-in API validation with proper error handling
- **Transaction Tracking**: Complete payment lifecycle management

#### **Wilayah.id Integration** (Indonesian Addresses)
- **Complete Database**: 91,000+ records (provinces, cities, districts, villages)
- **Performance**: 3-4 second import via memory-efficient streaming parser
- **Auto-sync**: Automatic import during database seeding
- **Relationship Validation**: Proper foreign key relationships with rollback protection

## Frontend Architecture

### **Template Integration**
- **Base Template**: Furni Bootstrap template for public pages
- **Admin Interface**: KaiaAdmin template for management panels
- **CSS Framework**: Bootstrap 5 with custom styling
- **Icons**: Font Awesome 6 integration
- **Notifications**: Notiflix for professional user feedback

### **Asset Management**
- **Build Tool**: Vite with Laravel plugin
- **CSS**: TailwindCSS v4 for utility-first styling
- **JavaScript**: jQuery for DOM manipulation and AJAX
- **Images**: Automatic WebP conversion with Intervention Image v3
- **Performance**: Optimized asset bundling and caching

### **Responsive Design**
- **Mobile-First**: Progressive enhancement for all screen sizes
- **Breakpoints**: Bootstrap responsive grid system
- **Navigation**: Collapsible mobile navigation with role-based menus
- **Forms**: Responsive form layouts with proper validation states

### **UI/UX Components**
- **Global Notiflix**: Unified notification system across all pages
- **JSGrid Integration**: Professional data grids for admin interfaces
- **Modal System**: Bootstrap modals for forms and content display
- **Loading States**: Visual feedback for all AJAX operations

## Security Features

### **Authentication & Authorization**
- **Multi-Role System**: Hierarchical permission structure
- **Middleware Protection**: Route-level access control
- **Password Security**: Bcrypt hashing with minimum requirements
- **Session Management**: Secure session handling with database storage
- **CSRF Protection**: All forms and AJAX requests properly protected

### **Data Validation**
- **Frontend Validation**: Real-time form validation with Bootstrap styling
- **Backend Validation**: Comprehensive Laravel validation rules
- **File Upload Security**: Strict file type and size validation
- **SQL Injection Protection**: Eloquent ORM with parameterized queries

### **API Security**
- **Rate Limiting**: API quota management for external services
- **Credential Management**: Secure storage of API keys and secrets
- **Error Handling**: Graceful error states without information disclosure
- **Connection Testing**: Safe API testing without exposing credentials

## Performance Optimizations

### **Database Performance**
- **Query Optimization**: Eager loading to prevent N+1 queries
- **Proper Indexing**: Strategic indexes on frequently queried fields
- **Caching**: System settings cached for performance
- **Relationship Optimization**: Efficient foreign key relationships

### **Image Processing**
- **WebP Conversion**: Automatic format conversion for optimal file sizes
- **Compression**: Configurable quality settings (85% default)
- **Smart Resizing**: Max 1200px width while maintaining aspect ratio
- **Lazy Loading**: Progressive image loading for better page performance

### **Frontend Performance**
- **Asset Bundling**: Vite for optimized JavaScript and CSS bundles
- **CDN Integration**: Font Awesome and external libraries from CDN
- **Minification**: Automatic asset minification in production
- **Caching Headers**: Proper browser caching for static assets

## Recent Updates (2025-09-17)

### **Navigation & UI Improvements**
- ✅ **Mobile Dropdown Fix**: Fixed white text visibility in mobile category dropdowns
- ✅ **Navigation Styling**: Enhanced custom navbar with proper color contrast
- ✅ **Responsive Behavior**: Improved mobile navigation experience
- ✅ **Dropdown Menus**: Clean white background with dark text for better readability

### **Layout Enhancements**
- ✅ **Fixed Navbar**: Positioned navbar with proper body padding compensation
- ✅ **Back to Top**: Smooth scroll-to-top button with floating design
- ✅ **Cart Counter**: Dynamic cart item counter with real-time updates
- ✅ **User Authentication**: Role-based navigation with dropdown menus

### **Content Management**
- ✅ **Banner System**: Dynamic homepage banners with admin management
- ✅ **Content Sections**: Manageable homepage content sections
- ✅ **Image Processing**: Advanced image handling with WebP optimization
- ✅ **Admin Interface**: Complete admin panel with JSGrid integration

## Development Workflow

### **Quick Setup (Recommended)**
```bash
git clone <repository>
cd marketplace-laravel12
composer install
npm install
cp .env.example .env
composer setup    # Auto key generation + migration + wilayah + RajaOngkir sync
composer dev       # Start development environment
```

### **API Keys Pre-configured**
The `.env.example` includes working API keys for immediate development:
- ✅ **RajaOngkir**: `8c8add072dfe923147fdfdbf3a8fd448`
- ✅ **Midtrans Sandbox**: Complete credentials included
- 🔄 **Auto-sync**: System automatically syncs data during setup

### **Testing & Quality Assurance**
- Uses PHP 8.2+ and Laravel 12
- PHPUnit test suite with SQLite in-memory database
- Laravel Pint for code formatting
- Comprehensive error handling and logging
- Database transactions for data integrity

## Important Technical Notes

### **System Requirements**
- PHP 8.2 or higher
- MySQL 8.0 or higher
- Node.js 18+ with NPM
- Composer 2.0+
- GD extension for image processing

### **Configuration Details**
- Queue system: Database driver
- Session storage: Database
- Cache driver: File (Redis recommended for production)
- Default timezone: Asia/Jakarta (Indonesian market focus)

### **Production Considerations**
- Configure Redis for caching and sessions
- Set up proper SSL certificates
- Configure email sending (SMTP/SES)
- Implement proper backup strategies
- Monitor API quotas for RajaOngkir and Midtrans

### **Maintenance Commands**
```bash
# Daily maintenance
php artisan queue:work --timeout=60  # Process background jobs
php artisan cache:clear              # Clear application cache
php artisan config:cache             # Cache configuration files

# Database maintenance
php artisan migrate                  # Run new migrations
php artisan db:seed --class=SystemSettingsSeeder  # Update system settings

# Asset maintenance
npm run build                        # Build production assets
php artisan storage:link             # Ensure storage symlink exists
```

This Laravel 12 Marketplace represents a production-ready e-commerce solution specifically designed for the Indonesian market, with comprehensive shipping integration, payment processing, and modern administrative interfaces. The application follows Laravel best practices and provides a solid foundation for scaling to enterprise-level requirements.

---

*Last Updated: September 17, 2025*
*Version: Laravel 12 Marketplace v1.0*
*Developers: Full-stack development team with Indonesian market expertise*