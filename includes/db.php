<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private static bool $usingFallback = false;

    private function __construct()
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Try MySQL first
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            // MySQL unavailable — fall back to SQLite file-based database
            error_log('MySQL unavailable, using SQLite fallback: ' . $e->getMessage());
            $dbDir = BASE_PATH . '/data';
            if (!is_dir($dbDir)) { mkdir($dbDir, 0755, true); }
            $dbPath = $dbDir . '/swamitime.sqlite';
            $isFresh = !file_exists($dbPath);
            $this->pdo = new PDO('sqlite:' . $dbPath, null, null, $options);
            $this->pdo->exec('PRAGMA journal_mode=WAL; PRAGMA foreign_keys=ON;');
            self::$usingFallback = true;
            // Register MySQL-compatible functions for SQLite
            $this->pdo->sqliteCreateFunction('NOW', function() { return date('Y-m-d H:i:s'); }, 0);
            $this->initSqliteSchema();
            if ($isFresh) { $this->seedSqliteData(); }
        }
    }

    private function initSqliteSchema(): void
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS pages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
                meta_title TEXT, meta_description TEXT, meta_keywords TEXT,
                content TEXT, status TEXT DEFAULT 'published',
                template TEXT DEFAULT 'default', parent_id INTEGER,
                sort_order INTEGER DEFAULT 0, created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS services (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
                icon TEXT, short_description TEXT, content TEXT,
                meta_title TEXT, meta_description TEXT, meta_keywords TEXT,
                featured_image TEXT, status TEXT DEFAULT 'active',
                sort_order INTEGER DEFAULT 0, created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS industries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
                icon TEXT, short_description TEXT, content TEXT,
                challenges TEXT, solutions TEXT,
                meta_title TEXT, meta_description TEXT,
                status TEXT DEFAULT 'active', sort_order INTEGER DEFAULT 0,
                created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS case_studies (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
                industry TEXT, challenge TEXT, solution TEXT, result TEXT,
                featured_image TEXT, status TEXT DEFAULT 'published',
                sort_order INTEGER DEFAULT 0, created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS blog_posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
                content TEXT, excerpt TEXT, featured_image TEXT,
                category_id INTEGER, tags TEXT,
                meta_title TEXT, meta_description TEXT,
                author TEXT, status TEXT DEFAULT 'published',
                published_at TEXT, created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS blog_categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
                description TEXT, created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS faqs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                question TEXT, answer TEXT, category TEXT,
                status TEXT DEFAULT 'active', sort_order INTEGER DEFAULT 0,
                created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS testimonials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT, company TEXT, role TEXT,
                content TEXT, rating INTEGER DEFAULT 5,
                avatar TEXT, status TEXT DEFAULT 'active',
                sort_order INTEGER DEFAULT 0, created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS trust_metrics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT, value TEXT, icon TEXT,
                description TEXT, status TEXT DEFAULT 'active',
                sort_order INTEGER DEFAULT 0, created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS cta_blocks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT, subtitle TEXT, button_text TEXT,
                button_url TEXT, button_style TEXT DEFAULT 'primary',
                background_style TEXT, page_location TEXT,
                status TEXT DEFAULT 'active', created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS enquiries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT, company_name TEXT, email TEXT,
                phone TEXT, service_required TEXT, budget_range TEXT,
                preferred_contact TEXT, message TEXT,
                gdpr_consent INTEGER DEFAULT 0, ip_address TEXT,
                status TEXT DEFAULT 'new', created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS crm_leads (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT, company_name TEXT, email TEXT,
                phone TEXT, service_interested TEXT, message TEXT,
                lead_source TEXT DEFAULT 'website',
                lead_status TEXT DEFAULT 'new',
                priority TEXT DEFAULT 'medium',
                assigned_user_id INTEGER, follow_up_date TEXT,
                converted_at TEXT, enquiry_id INTEGER,
                created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS crm_notes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                lead_id INTEGER, user_id INTEGER,
                note TEXT, created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS site_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT NOT NULL UNIQUE,
                setting_value TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS seo_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                page_type TEXT, page_id INTEGER,
                meta_title TEXT, meta_description TEXT,
                meta_keywords TEXT, og_title TEXT, og_description TEXT,
                og_image TEXT, twitter_card TEXT,
                canonical_url TEXT, schema_markup TEXT,
                created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS analytics_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                google_analytics_id TEXT, google_tag_manager_id TEXT,
                google_search_console_meta TEXT, meta_pixel_id TEXT,
                linkedin_insight_tag TEXT,
                custom_header_scripts TEXT, custom_footer_scripts TEXT,
                updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS ai_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                api_provider TEXT, api_key TEXT, model_name TEXT,
                temperature REAL DEFAULT 0.7,
                max_tokens INTEGER DEFAULT 2000,
                is_active INTEGER DEFAULT 0,
                created_at TEXT, updated_at TEXT
            );
            CREATE TABLE IF NOT EXISTS ai_suggestions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                page_type TEXT, page_id INTEGER,
                suggestion_type TEXT, suggestion_content TEXT,
                is_approved INTEGER DEFAULT 0,
                is_applied INTEGER DEFAULT 0, created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS media_library (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                filename TEXT, original_name TEXT,
                file_path TEXT, file_type TEXT,
                file_size INTEGER, alt_text TEXT,
                uploaded_by INTEGER, created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS menu_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT, url TEXT, parent_id INTEGER,
                sort_order INTEGER DEFAULT 0,
                location TEXT DEFAULT 'header',
                status TEXT DEFAULT 'active', created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS footer_links (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT, url TEXT, section TEXT,
                sort_order INTEGER DEFAULT 0,
                status TEXT DEFAULT 'active', created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS redirects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                old_url TEXT, new_url TEXT,
                status_code INTEGER DEFAULT 301, created_at TEXT
            );
            CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE, email TEXT NOT NULL,
                password_hash TEXT NOT NULL, full_name TEXT,
                role TEXT DEFAULT 'admin', avatar TEXT,
                last_login TEXT, created_at TEXT, updated_at TEXT,
                status TEXT DEFAULT 'active'
            );
        ");
    }

    private function seedSqliteData(): void
    {
        $now = date('Y-m-d H:i:s');
        // Seed default admin for SQLite fallback
        $hash = password_hash('Admin@2026!', PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO admins (username, email, password_hash, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@swamitime.com', $hash, 'Super Admin', 'super_admin', 'active']);

        // Seed pages
        $pages = [
            [1, 'Home', 'home', 'SWAMITIME SOLUTIONS LTD - Premium B2B Technology Consulting', 'SWAMITIME SOLUTIONS LTD provides premium workforce management, UKG support, IT consulting, web development, and SEO services.', 'UKG consulting, workforce management, IT consulting, web development, SEO, Swamitime', '<h2>Welcome to SWAMITIME SOLUTIONS LTD</h2><p>Your trusted partner for workforce management, UKG support, and digital transformation.</p>', 'published', 'home', null, 1],
            [2, 'About Us', 'about-us', 'About SWAMITIME SOLUTIONS LTD', 'Learn about SWAMITIME SOLUTIONS LTD, a premium B2B technology consulting firm.', 'about Swamitime, technology consulting, workforce management', '<h2>About Us</h2><p>SWAMITIME SOLUTIONS LTD is a premium B2B technology consulting company.</p>', 'published', 'default', null, 2],
            [3, 'Services', 'services', 'Our Services - SWAMITIME SOLUTIONS LTD', 'Explore our workforce management, UKG support, IT consulting, web development, and SEO services.', 'services, UKG, workforce management, IT consulting, web development', '<h2>Our Services</h2><p>We offer end-to-end workforce management and digital consulting services.</p>', 'published', 'default', null, 3],
            [4, 'UKG Workforce Management Support', 'ukg-workforce-management-support', 'UKG Workforce Management Support - SWAMITIME SOLUTIONS LTD', 'Expert UKG workforce management support services.', 'UKG support, workforce management support', '<h2>UKG Workforce Management Support</h2><p>Comprehensive support for your UKG systems.</p>', 'published', 'default', 3, 1],
            [5, 'IT & Digital Solutions', 'it-digital-solutions', 'IT & Digital Solutions - SWAMITIME SOLUTIONS LTD', 'Comprehensive IT consulting and digital transformation solutions.', 'IT consulting, digital transformation', '<h2>IT & Digital Solutions</h2><p>Transform your business with our IT consulting and digital solutions.</p>', 'published', 'default', 3, 2],
            [6, 'Web Development', 'web-development', 'Web Development - SWAMITIME SOLUTIONS LTD', 'Professional web development services.', 'web development, web applications', '<h2>Web Development</h2><p>Professional web development services delivering high-performance websites.</p>', 'published', 'default', 5, 1],
            [7, 'SEO & Digital Marketing', 'seo-digital-marketing', 'SEO & Digital Marketing - SWAMITIME SOLUTIONS LTD', 'Result-driven SEO and digital marketing services.', 'SEO, digital marketing', '<h2>SEO & Digital Marketing</h2><p>Drive growth with our result-oriented SEO and digital marketing strategies.</p>', 'published', 'default', 5, 2],
            [8, 'Industries', 'industries', 'Industries We Serve - SWAMITIME SOLUTIONS LTD', 'Discover the industries we serve.', 'industries, retail, hospitality, logistics', '<h2>Industries We Serve</h2><p>We deliver specialist solutions across multiple industries.</p>', 'published', 'default', null, 4],
            [9, 'Case Studies', 'case-studies', 'Case Studies - SWAMITIME SOLUTIONS LTD', 'Explore our case studies.', 'case studies, success stories', '<h2>Case Studies</h2><p>Discover how we have helped businesses transform their operations.</p>', 'published', 'default', null, 5],
            [10, 'Blog', 'blog', 'Blog - SWAMITIME SOLUTIONS LTD', 'Read our latest insights.', 'blog, insights, workforce management', '<h2>Blog</h2><p>Stay informed with the latest insights on workforce management.</p>', 'published', 'default', null, 6],
            [11, 'Contact Us', 'contact-us', 'Contact SWAMITIME SOLUTIONS LTD', 'Get in touch with SWAMITIME SOLUTIONS LTD.', 'contact, get in touch', '<h2>Contact Us</h2><p>We would love to hear from you.</p>', 'published', 'default', null, 7],
            [12, 'Privacy Policy', 'privacy-policy', 'Privacy Policy - SWAMITIME SOLUTIONS LTD', 'Our privacy policy.', 'privacy policy, data protection', '<h2>Privacy Policy</h2><p>We are committed to protecting your privacy.</p>', 'published', 'default', null, 8],
            [13, 'Terms & Conditions', 'terms-conditions', 'Terms & Conditions - SWAMITIME SOLUTIONS LTD', 'Read our terms and conditions.', 'terms and conditions, legal', '<h2>Terms & Conditions</h2><p>By using our website, you agree to the following terms.</p>', 'published', 'default', null, 9],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO pages (id, title, slug, meta_title, meta_description, meta_keywords, content, status, template, parent_id, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($pages as $p) { $stmt->execute([$p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8], $p[9], $p[10], $now, $now]); }

        // Seed services
        $services = [
            [1, 'UKG Workforce Management Support', 'ukg-workforce-management-support', 'fa-solid fa-headset', 'Expert UKG workforce management support maximising system performance.', '<p>Our UKG Workforce Management Support service provides comprehensive support for your UKG Pro and UKG Dimensions environments.</p>', 'UKG Workforce Management Support - SWAMITIME SOLUTIONS LTD', 'Expert UKG workforce management support services.', 'UKG support, workforce management, UKG Pro, UKG Dimensions', 'active', 1],
            [2, 'Workforce Management Consulting', 'workforce-management-consulting', 'fa-solid fa-chart-line', 'Strategic workforce management consulting to transform your people operations.', '<p>Our Workforce Management Consulting service helps you design strategies that align your workforce with business objectives.</p>', 'Workforce Management Consulting - SWAMITIME SOLUTIONS LTD', 'Strategic workforce management consulting.', 'workforce management consulting, WFM strategy', 'active', 2],
            [3, 'Implementation & Configuration', 'implementation-configuration-support', 'fa-solid fa-cogs', 'Seamless implementation and tailored configuration of UKG solutions.', '<p>Our Implementation & Configuration service ensures your workforce management system is deployed smoothly.</p>', 'Implementation & Configuration Support - SWAMITIME SOLUTIONS LTD', 'Seamless UKG implementation services.', 'UKG implementation, system configuration', 'active', 3],
            [4, 'Training & User Support', 'training-user-support', 'fa-solid fa-graduation-cap', 'Comprehensive training programmes empowering your teams.', '<p>Our Training & User Support service delivers tailored learning experiences.</p>', 'Training & User Support - SWAMITIME SOLUTIONS LTD', 'Comprehensive training for workforce management systems.', 'UKG training, user support', 'active', 4],
            [5, 'Managed Support Services', 'managed-support-services', 'fa-solid fa-shield-halved', 'Fully outsourced managed support services for operational continuity.', '<p>Our Managed Support Services provide complete operational ownership of your workforce management platforms.</p>', 'Managed Support Services - SWAMITIME SOLUTIONS LTD', 'Fully managed workforce management support.', 'managed support, UKG managed services', 'active', 5],
            [6, 'Reporting & Data Support', 'reporting-data-support', 'fa-solid fa-chart-bar', 'Advanced reporting and workforce analytics.', '<p>Our Reporting & Data Support service helps you harness the power of your workforce data.</p>', 'Reporting & Data Support - SWAMITIME SOLUTIONS LTD', 'Advanced workforce reporting and analytics.', 'workforce reporting, data analytics', 'active', 6],
            [7, 'IT Consulting', 'it-consulting', 'fa-solid fa-laptop-code', 'Comprehensive IT consulting services.', '<p>Our IT Consulting service provides strategic guidance across your technology landscape.</p>', 'IT Consulting - SWAMITIME SOLUTIONS LTD', 'Comprehensive IT consulting services.', 'IT consulting, cloud migration', 'active', 7],
            [8, 'Web Development', 'web-development', 'fa-solid fa-globe', 'Bespoke web development for B2B enterprises.', '<p>Our Web Development service crafts responsive, secure, and SEO-optimised websites.</p>', 'Web Development - SWAMITIME SOLUTIONS LTD', 'Professional web development for B2B.', 'web development, B2B websites', 'active', 8],
            [9, 'SEO & Digital Marketing', 'seo-digital-marketing', 'fa-solid fa-magnifying-glass-chart', 'Results-driven SEO and digital marketing strategies.', '<p>Our SEO & Digital Marketing service delivers measurable growth through comprehensive SEO audits.</p>', 'SEO & Digital Marketing - SWAMITIME SOLUTIONS LTD', 'Results-driven SEO for B2B growth.', 'SEO, digital marketing, B2B SEO', 'active', 9],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO services (id, title, slug, icon, short_description, content, meta_title, meta_description, meta_keywords, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($services as $s) { $stmt->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $s[6], $s[7], $s[8], $s[9], $s[10], $now, $now]); }

        // Seed industries
        $industries = [
            [1, 'Retail', 'retail', 'fa-solid fa-store', 'Workforce management solutions for the fast-paced retail sector.', '<p>The retail industry operates on tight margins where workforce efficiency impacts profitability.</p>', 'Challenges include shift management across locations, seasonal demand, and compliance.', 'Solutions include automated scheduling, real-time attendance, and self-service portals.', 'Retail Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce solutions for retail.', 'active', 1],
            [2, 'Hospitality', 'hospitality', 'fa-solid fa-hotel', 'Optimised workforce solutions for hotels, restaurants, and leisure.', '<p>The hospitality industry depends on exceptional guest experiences.</p>', 'Challenges include complex shift patterns, casual workers, and peak season staffing.', 'Solutions include dynamic scheduling, flexible workforce pools, and absence management.', 'Hospitality Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce solutions for hospitality.', 'active', 2],
            [3, 'Logistics & Distribution', 'logistics-distribution', 'fa-solid fa-truck-fast', 'Workforce solutions for logistics and distribution centres.', '<p>Logistics operations require precise workforce planning to meet delivery deadlines.</p>', 'Challenges include 24/7 shift planning, driver hours compliance, and agency management.', 'Solutions include shift planning tools, compliance monitoring, and training tracking.', 'Logistics Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce solutions for logistics.', 'active', 3],
            [4, 'Manufacturing', 'manufacturing', 'fa-solid fa-industry', 'Manufacturing workforce management solutions.', '<p>Manufacturing requires meticulous workforce planning to maintain production targets.</p>', 'Challenges include production line staffing, skills tracking, and overtime management.', 'Solutions include production-aligned scheduling, skills-based assignments, and ERP integration.', 'Manufacturing Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce solutions for manufacturing.', 'active', 4],
            [5, 'Healthcare & Care Services', 'healthcare-care-services', 'fa-solid fa-hospital', 'Compliant workforce solutions for healthcare providers.', '<p>Healthcare providers face complex workforce challenges managing clinical rotas.</p>', 'Challenges include clinical shift rostering, training tracking, and CQC compliance.', 'Solutions include clinical rota management, training alerts, and compliance dashboards.', 'Healthcare Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce solutions for healthcare.', 'active', 5],
            [6, 'Professional Services', 'professional-services', 'fa-solid fa-briefcase', 'Workforce solutions for professional services firms.', '<p>Professional services firms need solutions that track billable time and resource allocation.</p>', 'Challenges include tracking billable hours, resource allocation, and utilisation monitoring.', 'Solutions include integrated time tracking, resource dashboards, and profitability reporting.', 'Professional Services Workforce Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce solutions for professional services.', 'active', 6],
            [7, 'Small & Medium Businesses', 'small-medium-businesses', 'fa-solid fa-building', 'Scalable workforce solutions for growing SMEs.', '<p>SMEs need workforce management solutions that are affordable and easy to implement.</p>', 'Challenges include limited resources, manual processes, and compliance concerns.', 'Solutions include cloud-based management, automated tracking, and flexible pricing.', 'SME Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce solutions for small and medium businesses.', 'active', 7],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO industries (id, title, slug, icon, short_description, content, challenges, solutions, meta_title, meta_description, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($industries as $ind) { $stmt->execute([$ind[0], $ind[1], $ind[2], $ind[3], $ind[4], $ind[5], $ind[6], $ind[7], $ind[8], $ind[9], $ind[10], $ind[11], $now, $now]); }

        // Seed case studies
        $cases = [
            [1, 'UKG Dimensions Implementation for National Retailer', 'confidential-client-retail-ukg-dimensions', 'Retail', 'A leading UK retail chain with over 200 locations was struggling with fragmented time and attendance systems.', 'SWAMITIME implemented UKG Dimensions across all 200+ locations, migrating from legacy systems with zero data loss.', '<ul><li>35% reduction in payroll processing time</li><li>92% decrease in manual errors</li><li>£1.2M annual savings</li></ul>', 'published', 1],
            [2, 'Managed Support Services for Logistics Provider', 'confidential-client-logistics-managed-support', 'Logistics & Distribution', 'A mid-sized UK logistics company with 15 distribution centres struggled with UKG Pro WFM system issues.', 'SWAMITIME provided fully managed support including 24/7 monitoring, proactive incident management, and system health checks.', '<ul><li>99.8% system uptime</li><li>60% reduction in support costs</li><li>Resolution time reduced from 48h to 4h</li></ul>', 'published', 2],
            [3, 'Workforce Analytics Transformation for Healthcare Provider', 'confidential-client-healthcare-analytics', 'Healthcare & Care Services', 'A private healthcare group lacked visibility into workforce costs and agency spend.', 'SWAMITIME designed and deployed a comprehensive workforce analytics solution integrating data from UKG, HR, and finance systems.', '<ul><li>28% reduction in agency spend</li><li>40 hours/month saved in reporting</li><li>ROI within 6 months</li></ul>', 'published', 3],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO case_studies (id, title, slug, industry, challenge, solution, result, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($cases as $c) { $stmt->execute([$c[0], $c[1], $c[2], $c[3], $c[4], $c[5], $c[6], $c[7], $c[8], $now, $now]); }

        // Seed blog categories
        $categories = [
            [1, 'Workforce Management', 'workforce-management', 'Insights on workforce management strategies.'],
            [2, 'UKG Support', 'ukg-support', 'Tips and guidance for UKG users.'],
            [3, 'HR Technology', 'hr-technology', 'Exploring HR technology and platforms.'],
            [4, 'Digital Transformation', 'digital-transformation', 'How digital transformation reshapes business operations.'],
            [5, 'Time & Attendance', 'time-attendance', 'Best practices for time tracking.'],
            [6, 'Scheduling', 'scheduling', 'Optimising employee scheduling.'],
            [7, 'Business Automation', 'business-automation', 'Leveraging automation to streamline processes.'],
            [8, 'SEO & Digital Growth', 'seo-digital-growth', 'Strategies for improving online visibility.'],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO blog_categories (id, name, slug, description, created_at) VALUES (?, ?, ?, ?, ?)");
        foreach ($categories as $cat) { $stmt->execute([$cat[0], $cat[1], $cat[2], $cat[3], $now]); }

        // Seed blog posts
        $posts = [
            [1, 'The Future of Workforce Management: Trends Shaping 2026 and Beyond', 'future-workforce-management-trends-2026', 'Explore the key trends transforming workforce management in 2026.', 1, 'workforce management, WFM trends, 2026', 'The Future of Workforce Management: 2026 Trends', 'Discover key workforce management trends for 2026.', 'SWAMITIME SOLUTIONS LTD', 'published', '2026-01-15 09:00:00'],
            [2, 'Maximising ROI from Your UKG Dimensions Investment', 'maximising-roi-ukg-dimensions-investment', 'Practical strategies for getting the most value from UKG Dimensions.', 2, 'UKG Dimensions, ROI, UKG optimisation', 'Maximising ROI from UKG Dimensions', 'Practical strategies to maximise ROI from UKG Dimensions.', 'SWAMITIME SOLUTIONS LTD', 'published', '2026-02-10 09:00:00'],
            [3, 'How AI is Transforming HR Technology in 2026', 'ai-transforming-hr-technology-2026', 'Discover how AI-powered tools are improving HR technology.', 3, 'AI, HR technology, artificial intelligence', 'How AI is Transforming HR Technology in 2026', 'Explore how AI is transforming HR technology.', 'SWAMITIME SOLUTIONS LTD', 'published', '2026-03-05 09:00:00'],
            [4, '5 Workforce Scheduling Mistakes Costing Your Business Money', 'workforce-scheduling-mistakes-costing-money', 'Learn the five most common scheduling mistakes costing businesses money.', 6, 'scheduling mistakes, workforce scheduling', 'Workforce Scheduling Mistakes Costing Money', 'Learn common scheduling mistakes and how to fix them.', 'SWAMITIME SOLUTIONS LTD', 'published', '2026-04-20 09:00:00'],
            [5, 'SEO Strategies for B2B Technology Companies in 2026', 'seo-strategies-b2b-technology-2026', 'Effective SEO strategies for B2B technology companies.', 8, 'SEO, B2B SEO, digital marketing', 'SEO Strategies for B2B Technology Companies in 2026', 'Effective SEO strategies for B2B technology companies.', 'SWAMITIME SOLUTIONS LTD', 'published', '2026-05-01 09:00:00'],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO blog_posts (id, title, slug, excerpt, category_id, tags, meta_title, meta_description, author, status, published_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($posts as $post) { $stmt->execute([$post[0], $post[1], $post[2], $post[3], $post[4], $post[5], $post[6], $post[7], $post[8], $post[9], $post[10], $now, $now]); }

        // Seed FAQs
        $faqs = [
            [1, 'What is UKG and how can it benefit my business?', 'UKG provides workforce management solutions including time tracking, scheduling, absence management, and analytics. Benefits include reduced costs, improved compliance, and better workforce visibility.', 'UKG Support', 'active', 1],
            [2, 'What workforce management services does SWAMITIME offer?', 'We offer UKG support, consulting, implementation, training, managed support, reporting, IT consulting, web development, and SEO services.', 'General', 'active', 2],
            [3, 'Do you provide ongoing support after UKG implementation?', 'Yes, we offer 24/7 Managed Support Services including monitoring, incident resolution, system health checks, and performance optimisation.', 'Managed Support', 'active', 3],
            [4, 'How long does a typical UKG implementation take?', 'A typical mid-market implementation takes 8-16 weeks following our structured methodology of discovery, design, configuration, testing, training, and go-live.', 'Implementation', 'active', 4],
            [5, 'What industries do you specialise in?', 'Retail, Hospitality, Logistics & Distribution, Manufacturing, Healthcare & Care Services, Professional Services, and SMEs.', 'Industries', 'active', 5],
            [6, 'Can you integrate UKG with our existing systems?', 'Yes, we integrate UKG with HR, payroll, ERP, and business systems using APIs and middleware.', 'Implementation', 'active', 6],
            [7, 'What training options do you provide?', 'We offer on-site, remote, train-the-trainer, and self-paced e-learning tailored for managers, HR, payroll, and end users.', 'Training', 'active', 7],
            [8, 'How do your managed support services differ from standard UKG support?', 'We provide proactive management, faster response, health monitoring, regular reviews, and a single point of accountability.', 'Managed Support', 'active', 8],
            [9, 'What kind of reporting and analytics can you provide?', 'Custom reports covering time tracking, scheduling, absence, labour costs, compliance, and productivity using UKG tools and BI platforms.', 'Reporting', 'active', 9],
            [10, 'How do I get started with SWAMITIME SOLUTIONS LTD?', 'Contact us via our website enquiry form or email admin@swamitime.com for a free initial consultation.', 'General', 'active', 10],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO faqs (id, question, answer, category, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($faqs as $f) { $stmt->execute([$f[0], $f[1], $f[2], $f[3], $f[4], $f[5], $now, $now]); }

        // Seed testimonials
        $testimonials = [
            [1, 'Client A', 'Confidential - UK Retail Company', 'Director', 'SWAMITIME transformed our workforce management operations. Their UKG implementation was seamless, and the ongoing managed support has been exceptional.', 5, 'active', 1],
            [2, 'Client B', 'Confidential - UK Logistics Provider', 'Operations Director', 'The managed support service from SWAMITIME has been a game-changer. System downtime is virtually eliminated, and their team responds faster than direct vendor support.', 5, 'active', 2],
            [3, 'Client C', 'Confidential - UK Healthcare Group', 'HR Director', 'The workforce analytics solution has given us unprecedented visibility into staffing costs. We have reduced agency spend significantly.', 5, 'active', 3],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO testimonials (id, name, company, role, content, rating, status, sort_order, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($testimonials as $t) { $stmt->execute([$t[0], $t[1], $t[2], $t[3], $t[4], $t[5], $t[6], $t[7], $now]); }

        // Seed trust metrics
        $metrics = [
            [1, 'Smarter Workforce Processes', '100%', 'fa-solid fa-brain', 'Intelligent, automated workforce processes that drive smarter decision-making.', 'active', 1],
            [2, 'Seamless Technology Integration', '100%', 'fa-solid fa-plug', 'Flawless integration of workforce platforms with existing business systems.', 'active', 2],
            [3, 'Compliance & Governance', '100%', 'fa-solid fa-shield', 'Full adherence to UK GDPR and regulatory requirements.', 'active', 3],
            [4, 'Client Satisfaction', '4.9/5', 'fa-solid fa-star', 'Exceptional client satisfaction driven by quality and reliability.', 'active', 4],
            [5, 'Projects Delivered on Time', '98%', 'fa-solid fa-clock', 'On-time delivery across all projects, every time.', 'active', 5],
            [6, 'Experienced Consultants', '10+', 'fa-solid fa-users', 'Collectively, our consultants bring decades of workforce management expertise.', 'active', 6],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO trust_metrics (id, title, value, icon, description, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($metrics as $m) { $stmt->execute([$m[0], $m[1], $m[2], $m[3], $m[4], $m[5], $m[6], $now, $now]); }

        // Seed CTA blocks
        $cta = [
            [1, 'Ready to Transform Your Workforce Management?', 'Let us help you optimise your workforce operations with expert UKG support and consulting.', 'Get Started Today', '#contact', 'primary', 'gradient', 'home', 'active'],
            [2, 'Need UKG Support?', 'Our team of certified UKG specialists is ready to help.', 'Contact Us', '#contact', 'primary', 'gradient', 'services', 'active'],
            [3, 'Let\'s Discuss Your Project', 'Get in touch for a free, no-obligation consultation.', 'Get in Touch', '#contact', 'primary', 'gradient', 'contact', 'active'],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO cta_blocks (id, title, subtitle, button_text, button_url, button_style, background_style, page_location, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($cta as $c) { $stmt->execute([$c[0], $c[1], $c[2], $c[3], $c[4], $c[5], $c[6], $c[7], $c[8], $now, $now]); }

        // Seed menu items
        $menuItems = [
            [1, 'Home', '/', null, 0, 'header', 'active'],
            [2, 'About Us', '/about-us', null, 1, 'header', 'active'],
            [3, 'Services', '/services', null, 2, 'header', 'active'],
            [4, 'UKG Support', '/ukg-workforce-management-support', 3, 0, 'header', 'active'],
            [5, 'IT & Digital', '/it-digital-solutions', 3, 1, 'header', 'active'],
            [6, 'Industries', '/industries', null, 3, 'header', 'active'],
            [7, 'Case Studies', '/case-studies', null, 4, 'header', 'active'],
            [8, 'Blog', '/blog', null, 5, 'header', 'active'],
            [9, 'Contact Us', '/contact-us', null, 6, 'header', 'active'],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO menu_items (id, title, url, parent_id, sort_order, location, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($menuItems as $mi) { $stmt->execute([$mi[0], $mi[1], $mi[2], $mi[3], $mi[4], $mi[5], $mi[6], $now]); }

        // Seed footer links
        $footerLinks = [
            [1, 'About Us', '/about-us', 'company', 0, 'active'],
            [2, 'Case Studies', '/case-studies', 'company', 1, 'active'],
            [3, 'Blog', '/blog', 'company', 2, 'active'],
            [4, 'Contact Us', '/contact-us', 'company', 3, 'active'],
            [5, 'UKG Support', '/ukg-workforce-management-support', 'services', 0, 'active'],
            [6, 'Workforce Consulting', '/workforce-management-consulting', 'services', 1, 'active'],
            [7, 'IT Consulting', '/it-consulting', 'services', 2, 'active'],
            [8, 'Web Development', '/web-development', 'services', 3, 'active'],
            [9, 'Privacy Policy', '/privacy-policy', 'legal', 0, 'active'],
            [10, 'Terms & Conditions', '/terms-conditions', 'legal', 1, 'active'],
            [11, 'Cookie Policy', '/cookie-policy', 'legal', 2, 'active'],
            [12, 'GDPR Compliance', '/gdpr-compliance', 'legal', 3, 'active'],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO footer_links (id, title, url, section, sort_order, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($footerLinks as $fl) { $stmt->execute([$fl[0], $fl[1], $fl[2], $fl[3], $fl[4], $fl[5], $now]); }

        // Seed site settings — only essential ones
        $settings = [
            ['site_name', 'SWAMITIME SOLUTIONS LTD'],
            ['site_tagline', 'Premium B2B Technology Consulting'],
            ['site_description', 'Premium workforce management, UKG support, IT consulting, web development, and SEO services.'],
            ['social_linkedin', 'https://www.linkedin.com/company/swamitime-solutions-ltd'],
            ['social_twitter', 'https://twitter.com/swamitime'],
            ['social_facebook', 'https://www.facebook.com/swamitime'],
        ];
        $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO site_settings (setting_key, setting_value, updated_at) VALUES (?, ?, ?)");
        foreach ($settings as $s) { $stmt->execute([$s[0], $s[1], $now]); }
    }

    public static function isFallback(): bool
    {
        return self::$usingFallback;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    private function __clone(): void {}

    public function __wakeup(): void
    {
        throw new \RuntimeException('Cannot unserialize singleton');
    }
}

function getDB(): PDO
{
    return Database::getInstance()->getConnection();
}
