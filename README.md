# SWAMITIME SOLUTIONS LTD - Website & Admin Panel

A premium B2B technology consulting website for SWAMITIME SOLUTIONS LTD, providing workforce management consulting, independent UKG-related support, and digital business solutions.

## Features

### Public Website
- 19 fully responsive, SEO-optimized pages
- Premium B2B design with modern SaaS-style layout
- AI/digital transformation focused branding
- Mobile-first responsive design
- Dynamic contact form with CRM integration
- Blog system with categories and tags
- Case studies portfolio
- Industry solutions pages (7 industries)
- UKG Workforce Management Support page with legal disclaimer
- Social sharing, breadcrumbs, schema markup
- Newsletter subscription
- Cookie consent ready

### Admin Panel
- Secure authentication with role-based access (Super Admin, Admin, Editor)
- Dashboard with key metrics and recent activity
- Full content management (Pages, Services, Industries, Case Studies, FAQs, Testimonials)
- Blog management with categories and tags
- CRM system with lead management and pipeline tracking
- Contact form enquiry management with CSV export
- Media library with drag-and-drop upload
- AI SEO module (OpenAI/DeepSeek/Anthropic integration ready)
- SEO settings per page with SERP preview
- Analytics & tracking settings (Google Analytics, Meta Pixel, LinkedIn)
- Menu and footer management
- User management
- Site settings (branding, social, legal text)
- 301/302 redirect management

### Security
- CSRF protection on all forms
- Honeypot spam protection
- Rate limiting
- SQL injection prevention (prepared statements)
- XSS protection
- Secure password hashing (bcrypt)
- Session security
- Input sanitization
- File upload validation

## Technology Stack

- **Backend:** PHP 8+
- **Database:** MySQL 5.7+ / MariaDB 10+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **CSS Framework:** Bootstrap 5.3
- **Icons:** Bootstrap Icons
- **Fonts:** Inter, Plus Jakarta Sans (Google Fonts)
- **Animations:** AOS (Animate on Scroll)

## Installation

### Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10+
- Apache with mod_rewrite enabled (or Nginx with equivalent configuration)
- cPanel hosting (recommended) or any LAMP/LEMP stack

### Step 1: Upload Files

Upload all files to your web hosting root directory (e.g., `public_html/` or `www/`).

### Step 2: Create Database

1. Create a new MySQL database via cPanel or phpMyAdmin
2. Import the database schema:
   - Open phpMyAdmin
   - Select your database
   - Go to Import tab
   - Choose `database.sql` from the project files
   - Click Go

### Step 3: Configure Database Connection

Edit `config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');     // Your database host
define('DB_NAME', 'your_db_name');  // Your database name
define('DB_USER', 'your_db_user');  // Your database username
define('DB_PASS', 'your_db_pass');  // Your database password
```

Also update:
```php
define('BASE_URL', 'https://yourdomain.com');  // Your website URL
define('ADMIN_EMAIL', 'your@email.com');       // Admin email address
```

### Step 4: Set File Permissions

Ensure these directories are writable by the web server:
- `uploads/` (and all subdirectories) - CHMOD 755 or 777
- The web server needs write access for file uploads

### Step 5: Access Admin Panel

1. Navigate to `https://yourdomain.com/admin/login.php`
2. Login with the default credentials:

   **Username:** `admin`  
   **Password:** `Admin@2026!`

3. **IMPORTANT:** Change the admin password immediately after first login.

### Step 6: Complete Setup

1. Go to Admin → Settings → Site Settings and update:
   - Company name, email, phone, address
   - Social media links
   - Logo and favicon
   - Footer disclaimer text
   
2. Go to Admin → Analytics and configure:
   - Google Analytics Measurement ID
   - Google Tag Manager ID
   - Meta Pixel ID
   
3. Go to Admin → SEO & AI → AI SEO Tools and configure:
   - AI API key (OpenAI, DeepSeek, or Anthropic)
   - Select your preferred AI model

4. Update robots.txt: Change `https://swamitime.com` to your actual domain

## Directory Structure

```
swamitime/
├── admin/                  # Admin panel
│   ├── assets/            # Admin CSS/JS
│   ├── blog/              # Blog management
│   ├── crm/               # CRM system
│   ├── includes/          # Admin layout
│   ├── media/             # Media library
│   ├── pages/             # Content management
│   ├── seo/               # AI SEO tools
│   ├── settings/          # Site settings
│   ├── users/             # User management
│   ├── index.php          # Admin dashboard
│   └── login.php          # Admin login
├── api/                   # API endpoints
│   ├── contact-submit.php # Contact form handler
│   └── newsletter.php     # Newsletter handler
├── assets/                # Frontend assets
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript
│   ├── images/            # Images
│   └── icons/             # Icons
├── includes/              # Core PHP files
│   ├── auth.php           # Authentication
│   ├── config.php         # Configuration
│   ├── db.php             # Database connection
│   ├── footer.php         # Site footer
│   ├── functions.php      # Helper functions
│   ├── header.php         # Site header/navigation
│   ├── security.php       # Security functions
│   └── seo.php            # SEO helpers
├── pages/                 # Page templates
│   ├── home.php           # Homepage
│   ├── about.php          # About Us
│   ├── services.php       # Services overview
│   ├── contact.php        # Contact Us
│   ├── blog.php           # Blog listing
│   ├── blog-post.php      # Single blog post
│   ├── case-studies.php   # Case studies listing
│   ├── case-study-detail.php # Single case study
│   ├── industries.php     # Industries overview
│   ├── industry-detail.php # Single industry
│   ├── ukg-support.php    # UKG support page
│   ├── workforce-consulting.php
│   ├── implementation.php
│   ├── training.php
│   ├── managed-support.php
│   ├── reporting.php
│   ├── it-solutions.php
│   ├── web-development.php
│   ├── seo-marketing.php
│   ├── privacy.php        # Privacy policy
│   ├── terms.php          # Terms & conditions
│   ├── cookie.php         # Cookie policy
│   ├── 404.php            # 404 error page
│   └── dynamic.php        # Dynamic page template
├── uploads/               # Uploaded files
│   ├── blog/
│   ├── media/
│   └── icons/
├── .htaccess              # Apache configuration
├── robots.txt             # Robots rules
├── sitemap.xml            # XML sitemap (PHP generated)
├── config.php             # Site configuration
├── database.sql           # Database schema + sample data
├── index.php              # Main router
└── README.md              # This file
```

## Important Legal Disclaimer

SWAMITIME SOLUTIONS LTD provides independent technology consulting and workforce management support. UKG and related product names are trademarks of their respective owners. SWAMITIME SOLUTIONS LTD does not claim to be an official UKG partner, reseller, representative, or certified consultant unless confirmed in writing by the company owner.

This disclaimer is included on the UKG Support page and in the website footer.

## Customization

### Changing Colors
Edit `assets/css/style.css` and update the CSS variables in the `:root` section.

### Adding Pages
1. Create a new PHP file in `pages/`
2. Add the route to `index.php` in the `$routes` array
3. The page will automatically appear in the admin panel for content editing

### SEO Optimization
All pages have dynamic meta titles, descriptions, and keywords configurable from the admin panel. Schema markup is generated automatically for pages, blog posts, and services.

## Support

For technical support or questions about this website system, contact SWAMITIME SOLUTIONS LTD.

## License

Proprietary software. All rights reserved. © 2026 SWAMITIME SOLUTIONS LTD.
