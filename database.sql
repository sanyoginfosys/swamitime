-- ============================================================================
-- SWAMITIME SOLUTIONS LTD - Database Schema
-- Version: 1.0.0
-- Engine: InnoDB | Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- ============================================================================
-- TABLE: admins
-- ============================================================================
CREATE TABLE `admins` (
    `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `username`        VARCHAR(50)     NOT NULL,
    `email`           VARCHAR(255)    NOT NULL,
    `password_hash`   VARCHAR(255)    NOT NULL,
    `full_name`       VARCHAR(150)    NOT NULL,
    `role`            ENUM('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
    `avatar`          VARCHAR(500)    DEFAULT NULL,
    `last_login`      DATETIME        DEFAULT NULL,
    `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `status`          ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admins_username` (`username`),
    UNIQUE KEY `uq_admins_email` (`email`),
    KEY `idx_admins_role` (`role`),
    KEY `idx_admins_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admins` (`username`, `email`, `password_hash`, `full_name`, `role`, `status`) VALUES
(
    'admin',
    'admin@swamitime.com',
    -- Password: Admin@2026! (generated via password_hash with BCRYPT cost 12)
    '$2y$12$R5UEJnUSbauvm.4jBt.Ziuwutq44k6vmBf/z5E0tVCECmMsPzIkwm',
    'Super Admin',
    'super_admin',
    'active'
);

-- ============================================================================
-- TABLE: pages
-- ============================================================================
CREATE TABLE `pages` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(255)    NOT NULL,
    `slug`             VARCHAR(255)    NOT NULL,
    `meta_title`       VARCHAR(255)    DEFAULT NULL,
    `meta_description` TEXT            DEFAULT NULL,
    `meta_keywords`    TEXT            DEFAULT NULL,
    `content`          LONGTEXT        DEFAULT NULL,
    `status`           ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `template`         VARCHAR(100)    DEFAULT 'default',
    `parent_id`        INT UNSIGNED    DEFAULT NULL,
    `sort_order`       INT             NOT NULL DEFAULT 0,
    `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pages_slug` (`slug`),
    KEY `idx_pages_status` (`status`),
    KEY `idx_pages_parent_id` (`parent_id`),
    KEY `idx_pages_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pages` (`id`, `title`, `slug`, `meta_title`, `meta_description`, `meta_keywords`, `content`, `status`, `template`, `parent_id`, `sort_order`) VALUES
(1,  'Home',                              'home',                                   'SWAMITIME SOLUTIONS LTD - Premium B2B Technology Consulting', 'SWAMITIME SOLUTIONS LTD provides premium workforce management, UKG support, IT consulting, web development, and SEO services for businesses across the UK.', 'UKG consulting, workforce management, IT consulting, web development, SEO, Swamitime', '<h2>Welcome to SWAMITIME SOLUTIONS LTD</h2><p>Your trusted partner for workforce management, UKG support, and digital transformation.</p>', 'published', 'home', NULL, 1),
(2,  'About Us',                          'about-us',                               'About SWAMITIME SOLUTIONS LTD', 'Learn about SWAMITIME SOLUTIONS LTD, a premium B2B technology consulting firm specialising in workforce management and digital solutions.', 'about Swamitime, technology consulting, workforce management', '<h2>About Us</h2><p>SWAMITIME SOLUTIONS LTD is a premium B2B technology consulting company delivering innovative workforce management and digital transformation solutions.</p>', 'published', 'default', NULL, 2),
(3,  'Services',                          'services',                               'Our Services - SWAMITIME SOLUTIONS LTD', 'Explore our comprehensive range of workforce management, UKG support, IT consulting, web development, and SEO services.', 'services, UKG, workforce management, IT consulting, web development', '<h2>Our Services</h2><p>We offer end-to-end workforce management and digital consulting services tailored to your business needs.</p>', 'published', 'default', NULL, 3),
(4,  'UKG Workforce Management Support',   'ukg-workforce-management-support',       'UKG Workforce Management Support - SWAMITIME SOLUTIONS LTD', 'Expert UKG workforce management support services to optimise your workforce operations and maximise ROI from your UKG investment.', 'UKG support, workforce management support, UKG services', '<h2>UKG Workforce Management Support</h2><p>Comprehensive support for your UKG workforce management systems, ensuring smooth operations and maximum value.</p>', 'published', 'default', 4, 1),
(5,  'Workforce Management Consulting',    'workforce-management-consulting',        'Workforce Management Consulting - SWAMITIME SOLUTIONS LTD', 'Strategic workforce management consulting to transform your people operations and drive business performance.', 'workforce management consulting, WFM consulting, HR consulting', '<h2>Workforce Management Consulting</h2><p>Strategic consulting services to help you optimise your workforce planning, scheduling, and time management processes.</p>', 'published', 'default', 4, 2),
(6,  'Implementation & Configuration',     'implementation-configuration-support',   'Implementation & Configuration Support - SWAMITIME SOLUTIONS LTD', 'Expert implementation and configuration support for UKG and workforce management systems.', 'UKG implementation, configuration, workforce management setup', '<h2>Implementation & Configuration</h2><p>Seamless implementation and configuration of workforce management systems tailored to your business requirements.</p>', 'published', 'default', 4, 3),
(7,  'Training & User Support',            'training-user-support',                  'Training & User Support - SWAMITIME SOLUTIONS LTD', 'Comprehensive training and user support services to empower your team with workforce management expertise.', 'training, user support, workforce management training', '<h2>Training & User Support</h2><p>Empower your team with expert-led training and ongoing user support for your workforce management systems.</p>', 'published', 'default', 4, 4),
(8,  'Managed Support Services',           'managed-support-services',               'Managed Support Services - SWAMITIME SOLUTIONS LTD', 'Fully managed workforce management support services giving you peace of mind and operational continuity.', 'managed support, outsourced support, workforce management managed services', '<h2>Managed Support Services</h2><p>Leave your workforce management support to us with our fully managed support services, ensuring operational continuity.</p>', 'published', 'default', 4, 5),
(9,  'Reporting & Data Support',           'reporting-data-support',                 'Reporting & Data Support - SWAMITIME SOLUTIONS LTD', 'Advanced reporting and data support services to unlock actionable insights from your workforce data.', 'reporting, data support, workforce analytics, BI reporting', '<h2>Reporting & Data Support</h2><p>Unlock the power of your workforce data with our advanced reporting and analytics support services.</p>', 'published', 'default', 4, 6),
(10, 'IT & Digital Solutions',             'it-digital-solutions',                   'IT & Digital Solutions - SWAMITIME SOLUTIONS LTD', 'Comprehensive IT consulting and digital transformation solutions to modernise your business technology.', 'IT consulting, digital transformation, business technology', '<h2>IT & Digital Solutions</h2><p>Transform your business with our comprehensive IT consulting and digital solutions, from infrastructure to application modernisation.</p>', 'published', 'default', 5, 1),
(11, 'Web Development',                    'web-development',                        'Web Development - SWAMITIME SOLUTIONS LTD', 'Professional web development services crafting high-performance websites and web applications for B2B businesses.', 'web development, website development, web applications', '<h2>Web Development</h2><p>Professional web development services delivering high-performance, responsive websites and web applications for your business.</p>', 'published', 'default', 5, 2),
(12, 'SEO & Digital Marketing',            'seo-digital-marketing',                  'SEO & Digital Marketing - SWAMITIME SOLUTIONS LTD', 'Result-driven SEO and digital marketing services to grow your online presence and generate qualified B2B leads.', 'SEO, digital marketing, search engine optimisation, B2B marketing', '<h2>SEO & Digital Marketing</h2><p>Drive growth with our result-oriented SEO and digital marketing strategies designed for B2B technology companies.</p>', 'published', 'default', 5, 3),
(13, 'Industries',                         'industries',                             'Industries We Serve - SWAMITIME SOLUTIONS LTD', 'Discover the industries we serve with our specialist workforce management and digital solutions.', 'industries, retail, hospitality, logistics, manufacturing, healthcare', '<h2>Industries We Serve</h2><p>We deliver specialist solutions across multiple industries, understanding the unique challenges of each sector.</p>', 'published', 'default', NULL, 4),
(14, 'Case Studies',                       'case-studies',                           'Case Studies - SWAMITIME SOLUTIONS LTD', 'Explore our case studies showcasing successful workforce management and digital transformation projects.', 'case studies, success stories, workforce management projects', '<h2>Case Studies</h2><p>Discover how we have helped businesses transform their workforce management and digital operations.</p>', 'published', 'default', NULL, 5),
(15, 'Blog',                               'blog',                                   'Blog - SWAMITIME SOLUTIONS LTD', 'Read our latest insights on workforce management, UKG, digital transformation, and business technology.', 'blog, insights, workforce management articles, UKG blog', '<h2>Blog</h2><p>Stay informed with the latest insights and thought leadership on workforce management and digital transformation.</p>', 'published', 'default', NULL, 6),
(16, 'Contact Us',                         'contact-us',                             'Contact SWAMITIME SOLUTIONS LTD', 'Get in touch with SWAMITIME SOLUTIONS LTD for premium workforce management and digital consulting services.', 'contact, get in touch, enquiry', '<h2>Contact Us</h2><p>We''d love to hear from you. Reach out to discuss how we can support your business.</p>', 'published', 'default', NULL, 7),
(17, 'Privacy Policy',                     'privacy-policy',                         'Privacy Policy - SWAMITIME SOLUTIONS LTD', 'Our privacy policy explains how we collect, use, and protect your personal data in compliance with UK GDPR.', 'privacy policy, data protection, GDPR', '<h2>Privacy Policy</h2><p>We are committed to protecting your privacy and handling your data transparently and securely.</p>', 'published', 'default', NULL, 8),
(18, 'Terms & Conditions',                 'terms-conditions',                       'Terms & Conditions - SWAMITIME SOLUTIONS LTD', 'Read our terms and conditions for using our website and engaging our services.', 'terms and conditions, legal', '<h2>Terms & Conditions</h2><p>By using our website and services, you agree to the following terms and conditions.</p>', 'published', 'default', NULL, 9),
(19, 'Cookie Policy',                      'cookie-policy',                          'Cookie Policy - SWAMITIME SOLUTIONS LTD', 'Information about how we use cookies on our website.', 'cookies, cookie policy', '<h2>Cookie Policy</h2><p>Our website uses cookies to enhance your browsing experience and provide essential functionality.</p>', 'published', 'default', NULL, 10),
(20, 'GDPR Compliance',                    'gdpr-compliance',                        'GDPR Compliance - SWAMITIME SOLUTIONS LTD', 'How SWAMITIME SOLUTIONS LTD complies with UK GDPR and EU data protection regulations. Learn about your data subject rights.', 'GDPR, data protection, data rights, subject access request, UK GDPR, data privacy', '<h2>GDPR Compliance</h2><p>SWAMITIME SOLUTIONS LTD is committed to protecting personal data and upholding the rights of individuals under UK GDPR and the Data Protection Act 2018.</p>', 'published', 'default', NULL, 11);

-- ============================================================================
-- TABLE: services
-- ============================================================================
CREATE TABLE `services` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(255)    NOT NULL,
    `slug`             VARCHAR(255)    NOT NULL,
    `icon`             VARCHAR(100)    DEFAULT NULL,
    `short_description` VARCHAR(500)   DEFAULT NULL,
    `content`          LONGTEXT        DEFAULT NULL,
    `meta_title`       VARCHAR(255)    DEFAULT NULL,
    `meta_description` TEXT            DEFAULT NULL,
    `meta_keywords`    TEXT            DEFAULT NULL,
    `featured_image`   VARCHAR(500)    DEFAULT NULL,
    `status`           ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order`       INT             NOT NULL DEFAULT 0,
    `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_services_slug` (`slug`),
    KEY `idx_services_status` (`status`),
    KEY `idx_services_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `services` (`id`, `title`, `slug`, `icon`, `short_description`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `sort_order`) VALUES
(1,  'UKG Workforce Management Support',   'ukg-workforce-management-support',   'fa-solid fa-headset',            'Expert UKG workforce management support maximising system performance and user satisfaction across your organisation.', '<p>Our UKG Workforce Management Support service provides comprehensive, expert-led support for your UKG Pro and UKG Dimensions environments. We help you get the most from your investment with proactive monitoring, rapid issue resolution, and continuous system optimisation.</p>', 'UKG Workforce Management Support - SWAMITIME SOLUTIONS LTD', 'Expert UKG workforce management support services to optimise your workforce operations.', 'UKG support, workforce management, UKG Pro, UKG Dimensions', 'active', 1),
(2,  'Workforce Management Consulting',    'workforce-management-consulting',    'fa-solid fa-chart-line',         'Strategic workforce management consulting to transform your people operations, reduce costs, and drive productivity.', '<p>Our Workforce Management Consulting service helps you design and implement strategies that align your workforce with business objectives. We assess current processes, identify improvement areas, and deliver actionable roadmaps for transformation.</p>', 'Workforce Management Consulting - SWAMITIME SOLUTIONS LTD', 'Strategic workforce management consulting to transform operations.', 'workforce management consulting, WFM strategy, HR consulting', 'active', 2),
(3,  'Implementation & Configuration',     'implementation-configuration-support', 'fa-solid fa-cogs',              'Seamless implementation and tailored configuration of UKG and workforce management solutions to match your business needs.', '<p>Our Implementation & Configuration service ensures your workforce management system is deployed smoothly and configured precisely to your requirements. From system setup to data migration and testing, we manage every step with rigour.</p>', 'Implementation & Configuration Support - SWAMITIME SOLUTIONS LTD', 'Seamless UKG implementation and tailored configuration services.', 'UKG implementation, system configuration, data migration', 'active', 3),
(4,  'Training & User Support',            'training-user-support',              'fa-solid fa-graduation-cap',    'Comprehensive training programmes and ongoing user support empowering your teams to excel with workforce management tools.', '<p>Our Training & User Support service delivers tailored learning experiences for managers, HR teams, and end users. We offer on-site, remote, and train-the-trainer programmes alongside a responsive helpdesk for ongoing user support.</p>', 'Training & User Support - SWAMITIME SOLUTIONS LTD', 'Comprehensive training and user support for workforce management systems.', 'UKG training, user support, workforce management training', 'active', 4),
(5,  'Managed Support Services',           'managed-support-services',           'fa-solid fa-shield-halved',     'Fully outsourced managed support services providing peace of mind and 24/7 operational continuity for your critical systems.', '<p>Our Managed Support Services provide complete operational ownership of your workforce management platforms. We handle incident management, patching, upgrades, and performance tuning so your team can focus on strategic priorities.</p>', 'Managed Support Services - SWAMITIME SOLUTIONS LTD', 'Fully managed workforce management support for operational continuity.', 'managed support, outsourced support, UKG managed services', 'active', 5),
(6,  'Reporting & Data Support',           'reporting-data-support',             'fa-solid fa-chart-bar',         'Advanced reporting and workforce analytics unlocking data-driven insights that improve decision-making across your business.', '<p>Our Reporting & Data Support service helps you harness the full power of your workforce data. We design custom reports, dashboards, and analytics that provide real-time visibility into attendance, scheduling, labour costs, and compliance.</p>', 'Reporting & Data Support - SWAMITIME SOLUTIONS LTD', 'Advanced workforce reporting and analytics support services.', 'workforce reporting, data analytics, BI reporting, compliance reporting', 'active', 6),
(7,  'IT Consulting',                      'it-consulting',                      'fa-solid fa-laptop-code',       'Comprehensive IT consulting services spanning infrastructure, cloud, cybersecurity, and digital workplace transformation.', '<p>Our IT Consulting service provides strategic guidance and hands-on delivery across your technology landscape. From infrastructure modernisation to cloud migration and cybersecurity, we help build resilient, future-ready IT environments.</p>', 'IT Consulting - SWAMITIME SOLUTIONS LTD', 'Comprehensive IT consulting services for business transformation.', 'IT consulting, cloud migration, cybersecurity, digital workplace', 'active', 7),
(8,  'Web Development',                    'web-development',                    'fa-solid fa-globe',             'Bespoke web development delivering high-performance, secure, and scalable websites and web applications for B2B enterprises.', '<p>Our Web Development service crafts responsive, secure, and SEO-optimised websites and web applications. Whether you need a corporate site, a client portal, or a custom application, we deliver solutions that drive business results.</p>', 'Web Development - SWAMITIME SOLUTIONS LTD', 'Professional web development for high-performance B2B websites.', 'web development, website design, web applications, B2B websites', 'active', 8),
(9,  'SEO & Digital Marketing',            'seo-digital-marketing',              'fa-solid fa-magnifying-glass-chart', 'Results-driven SEO and digital marketing strategies that increase visibility, traffic, and qualified leads for B2B companies.', '<p>Our SEO & Digital Marketing service delivers measurable growth through comprehensive SEO audits, keyword strategy, content marketing, and performance tracking. We focus on driving qualified B2B traffic that converts into leads.</p>', 'SEO & Digital Marketing - SWAMITIME SOLUTIONS LTD', 'Results-driven SEO and digital marketing for B2B growth.', 'SEO, digital marketing, B2B SEO, content marketing, lead generation', 'active', 9);

-- ============================================================================
-- TABLE: industries
-- ============================================================================
CREATE TABLE `industries` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(255)    NOT NULL,
    `slug`             VARCHAR(255)    NOT NULL,
    `icon`             VARCHAR(100)    DEFAULT NULL,
    `short_description` VARCHAR(500)   DEFAULT NULL,
    `content`          LONGTEXT        DEFAULT NULL,
    `challenges`       LONGTEXT        DEFAULT NULL,
    `solutions`        LONGTEXT        DEFAULT NULL,
    `meta_title`       VARCHAR(255)    DEFAULT NULL,
    `meta_description` TEXT            DEFAULT NULL,
    `status`           ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order`       INT             NOT NULL DEFAULT 0,
    `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_industries_slug` (`slug`),
    KEY `idx_industries_status` (`status`),
    KEY `idx_industries_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `industries` (`id`, `title`, `slug`, `icon`, `short_description`, `content`, `challenges`, `solutions`, `meta_title`, `meta_description`, `status`, `sort_order`) VALUES
(1, 'Retail',                        'retail',                        'fa-solid fa-store',               'Workforce management solutions tailored for the fast-paced retail sector, from high-street stores to nationwide chains.', '<p>The retail industry operates on tight margins where workforce efficiency directly impacts profitability. With fluctuating customer demand, seasonal peaks, and multi-location operations, managing your workforce effectively is critical to success.</p>', '<ul><li>Managing shift patterns across multiple store locations</li><li>Seasonal demand fluctuations requiring flexible scheduling</li><li>Compliance with working time regulations</li><li>High staff turnover and ongoing training needs</li><li>Integrating time and attendance with payroll systems</li></ul>', '<ul><li>Automated scheduling aligned with footfall data and sales forecasts</li><li>Real-time attendance tracking across all locations</li><li>Self-service portals for shift swaps and leave requests</li><li>Compliance dashboards ensuring full regulatory adherence</li><li>Seamless payroll integration reducing manual processing</li></ul>', 'Retail Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Specialist workforce management solutions for the retail industry.', 'active', 1),
(2, 'Hospitality',                   'hospitality',                   'fa-solid fa-hotel',               'Optimised workforce solutions for hotels, restaurants, and leisure businesses managing diverse rotas and seasonal demand.', '<p>The hospitality industry depends on delivering exceptional guest experiences, which starts with having the right people in the right place at the right time. Our solutions address the unique scheduling and compliance challenges of hospitality operations.</p>', '<ul><li>Complex shift patterns across front-of-house, kitchen, and housekeeping</li><li>Managing casual and zero-hours contract workers</li><li>Peak season staffing during holidays and events</li><li>Tip and service charge compliance tracking</li><li>Maintaining service levels during staff absences</li></ul>', '<ul><li>Dynamic scheduling tools optimising coverage across departments</li><li>Flexible workforce pools with automated availability matching</li><li>Absence management with intelligent shift coverage suggestions</li><li>Compliance tracking for working time regulations</li><li>Labour cost forecasting and budgeting tools</li></ul>', 'Hospitality Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce management solutions for hotels, restaurants, and leisure businesses.', 'active', 2),
(3, 'Logistics & Distribution',      'logistics-distribution',        'fa-solid fa-truck-fast',          'Workforce solutions for logistics and distribution centres managing shift workers, compliance, and operational efficiency.', '<p>Logistics and distribution operations run around the clock, requiring precise workforce planning to meet delivery deadlines, manage warehouse throughput, and maintain safety compliance across multiple shifts.</p>', '<ul><li>24/7 shift planning with fluctuating volumes</li><li>Driver hours and tachograph compliance</li><li>Warehouse health and safety training tracking</li><li>Agency worker management and cost control</li><li>Real-time visibility of workforce across sites</li></ul>', '<ul><li>Shift planning tools aligned with delivery and dispatch schedules</li><li>Automated driver hours compliance monitoring</li><li>Integrated training and certification tracking</li><li>Agency worker spend dashboards with cost controls</li><li>Centralised visibility of all site workforces</li></ul>', 'Logistics Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce management solutions for logistics and distribution operations.', 'active', 3),
(4, 'Manufacturing',                 'manufacturing',                 'fa-solid fa-industry',            'Manufacturing workforce management solutions covering shift-based production, skills tracking, and operational efficiency.', '<p>Manufacturing operations require meticulous workforce planning to maintain production targets, manage shift patterns, track skills and certifications, and ensure health and safety compliance across the factory floor.</p>', '<ul><li>Production line staffing and shift optimisation</li><li>Skills matrix management and certification tracking</li><li>Health and safety compliance and training records</li><li>Overtime management and cost control</li><li>Integration with production planning systems</li></ul>', '<ul><li>Production-aligned scheduling with skills-based assignments</li><li>Automated certification renewal alerts and tracking</li><li>Health and safety incident reporting and training management</li><li>Overtime budgeting and approval workflows</li><li>ERP and MES integration for seamless data flow</li></ul>', 'Manufacturing Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce management solutions for manufacturing operations.', 'active', 4),
(5, 'Healthcare & Care Services',    'healthcare-care-services',      'fa-solid fa-hospital',            'Compliant workforce solutions for healthcare providers managing complex rotas, qualifications, and regulatory requirements.', '<p>Healthcare providers face uniquely complex workforce challenges, from managing clinical rotas and agency staff to maintaining CQC compliance and tracking mandatory training and professional registrations.</p>', '<ul><li>Clinical shift rostering with skill-mix requirements</li><li>Mandatory training and professional registration tracking</li><li>Agency and bank staff management</li><li>CQC and regulatory compliance reporting</li><li>Safe staffing level monitoring and escalation</li></ul>', '<ul><li>Clinical rota management with automatic skill-mix validation</li><li>Training and registration expiry alerts with audit trails</li><li>Integrated agency and bank staff booking with cost tracking</li><li>Compliance dashboards aligned to regulatory frameworks</li><li>Safe staffing alerts with automated escalation workflows</li></ul>', 'Healthcare Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Compliant workforce management solutions for healthcare providers.', 'active', 5),
(6, 'Professional Services',         'professional-services',         'fa-solid fa-briefcase',           'Workforce solutions for professional services firms managing billable hours, project staffing, and resource utilisation.', '<p>Professional services firms need workforce management solutions that track billable time, optimise resource allocation across client projects, and provide visibility into utilisation rates and profitability.</p>', '<ul><li>Tracking billable and non-billable hours accurately</li><li>Resource allocation across multiple client projects</li><li>Utilisation rate monitoring and forecasting</li><li>Remote and hybrid working time tracking</li><li>Project profitability analysis</li></ul>', '<ul><li>Integrated time tracking with project and client codes</li><li>Resource management dashboards with capacity planning</li><li>Real-time utilisation and profitability reporting</li><li>Flexible time capture for remote and hybrid teams</li><li>Automated billing data feeds to finance systems</li></ul>', 'Professional Services Workforce Solutions - SWAMITIME SOLUTIONS LTD', 'Workforce management solutions for professional services firms.', 'active', 6),
(7, 'Small & Medium Businesses',     'small-medium-businesses',       'fa-solid fa-building',            'Scalable workforce solutions for growing SMEs needing enterprise-grade tools without the enterprise price tag.', '<p>Small and medium businesses need workforce management solutions that are affordable, easy to implement, and scalable as they grow. Our SME solutions provide enterprise-grade capability without the complexity or cost of traditional enterprise systems.</p>', '<ul><li>Limited HR and workforce management resources</li><li>Manual processes consuming valuable staff time</li><li>Compliance concerns as the business grows</li><li>Budget constraints limiting technology investment</li><li>Need for scalable solutions that grow with the business</li></ul>', '<ul><li>Cloud-based workforce management with quick deployment</li><li>Automated time tracking and absence management</li><li>Built-in compliance tools that scale with your business</li><li>Flexible pricing models suited to SME budgets</li><li>Modular approach allowing phased adoption</li></ul>', 'SME Workforce Management Solutions - SWAMITIME SOLUTIONS LTD', 'Scalable workforce management solutions for small and medium businesses.', 'active', 7);

-- ============================================================================
-- TABLE: case_studies
-- ============================================================================
CREATE TABLE `case_studies` (
    `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`           VARCHAR(255)    NOT NULL,
    `slug`            VARCHAR(255)    NOT NULL,
    `industry`        VARCHAR(100)    DEFAULT NULL,
    `challenge`       LONGTEXT        DEFAULT NULL,
    `solution`        LONGTEXT        DEFAULT NULL,
    `result`          LONGTEXT        DEFAULT NULL,
    `featured_image`  VARCHAR(500)    DEFAULT NULL,
    `status`          ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `sort_order`      INT             NOT NULL DEFAULT 0,
    `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_case_studies_slug` (`slug`),
    KEY `idx_case_studies_status` (`status`),
    KEY `idx_case_studies_industry` (`industry`),
    KEY `idx_case_studies_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `case_studies` (`id`, `title`, `slug`, `industry`, `challenge`, `solution`, `result`, `status`, `sort_order`) VALUES
(1, 'UKG Dimensions Implementation for National Retailer', 'confidential-client-retail-ukg-dimensions', 'Retail',
   'A leading UK retail chain with over 200 locations was struggling with fragmented time and attendance systems across their estate. Manual processes were causing payroll errors, compliance risks, and excessive administrative overhead. Their existing legacy system could not support modern flexible working patterns or provide real-time visibility of labour costs.',
   'SWAMITIME implemented UKG Dimensions across all 200+ locations, migrating from legacy systems with zero data loss. We configured role-based scheduling rules, integrated real-time attendance tracking with their ERP, and deployed self-service mobile access for all 15,000 employees. A phased rollout minimised operational disruption.',
   '<ul><li>35% reduction in payroll processing time</li><li>92% decrease in manual timesheet errors</li><li>Full compliance with working time regulations across all locations</li><li>£1.2M annual savings from reduced administrative overhead</li><li>Employee self-service adoption rate of 87% within 3 months</li></ul>',
   'published', 1),
(2, 'Managed Support Services for Logistics Provider', 'confidential-client-logistics-managed-support', 'Logistics & Distribution',
   'A mid-sized UK logistics company operating 15 distribution centres was struggling with ongoing system issues in their UKG Pro WFM environment. Their internal IT team lacked specialist UKG expertise, leading to extended system downtime, unresolved configuration issues, and escalating support costs from ad-hoc consultancy engagements.',
   'SWAMITIME provided a fully managed support service, including 24/7 monitoring, proactive incident management, regular system health checks, and a dedicated UKG support team. We established SLAs covering response and resolution times, created a knowledge base for common issues, and delivered monthly performance reviews.',
   '<ul><li>99.8% system uptime achieved within the first quarter</li><li>60% reduction in support-related costs</li><li>Average incident resolution time reduced from 48 hours to 4 hours</li><li>3 major system upgrades completed with zero business disruption</li><li>Internal IT team freed to focus on strategic initiatives</li></ul>',
   'published', 2),
(3, 'Workforce Analytics Transformation for Healthcare Provider', 'confidential-client-healthcare-analytics', 'Healthcare & Care Services',
   'A private healthcare group operating 12 care homes and 3 hospitals lacked visibility into workforce costs, agency spend, and staff utilisation. Disparate systems meant leadership could not access consolidated workforce data, making strategic planning and budget forecasting unreliable.',
   'SWAMITIME designed and deployed a comprehensive workforce analytics solution integrating data from UKG, HR systems, and finance platforms into a unified reporting environment. We built custom dashboards for operational managers, finance teams, and executive leadership, with automated scheduled reporting and real-time KPI tracking.',
   '<ul><li>28% reduction in agency staff spend through improved workforce planning</li><li>Real-time visibility of labour costs across all 15 sites</li><li>Automated compliance reporting saving 40 hours per month</li><li>Leadership dashboards enabling data-driven workforce decisions</li><li>ROI achieved within 6 months of deployment</li></ul>',
   'published', 3);

-- ============================================================================
-- TABLE: blog_categories
-- ============================================================================
CREATE TABLE `blog_categories` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)    NOT NULL,
    `slug`        VARCHAR(150)    NOT NULL,
    `description` TEXT            DEFAULT NULL,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_blog_categories_slug` (`slug`),
    KEY `idx_blog_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `blog_categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Workforce Management',     'workforce-management',     'Insights and best practices for effective workforce management strategies.'),
(2, 'UKG Support',              'ukg-support',              'Tips, updates, and guidance for UKG Pro and UKG Dimensions users.'),
(3, 'HR Technology',            'hr-technology',            'Exploring the latest in HR technology, tools, and platforms.'),
(4, 'Digital Transformation',   'digital-transformation',   'How digital transformation is reshaping business operations and workforce planning.'),
(5, 'Time & Attendance',        'time-attendance',          'Best practices for time and attendance tracking and compliance.'),
(6, 'Scheduling',               'scheduling',               'Optimising employee scheduling for productivity and satisfaction.'),
(7, 'Business Automation',      'business-automation',      'Leveraging automation to streamline business processes and reduce manual work.'),
(8, 'SEO & Digital Growth',     'seo-digital-growth',       'Strategies for improving online visibility and driving digital growth.');

-- ============================================================================
-- TABLE: blog_posts
-- ============================================================================
CREATE TABLE `blog_posts` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(255)    NOT NULL,
    `slug`             VARCHAR(255)    NOT NULL,
    `content`          LONGTEXT        DEFAULT NULL,
    `excerpt`          TEXT            DEFAULT NULL,
    `featured_image`   VARCHAR(500)    DEFAULT NULL,
    `category_id`      INT UNSIGNED    DEFAULT NULL,
    `tags`             TEXT            DEFAULT NULL,
    `meta_title`       VARCHAR(255)    DEFAULT NULL,
    `meta_description` TEXT            DEFAULT NULL,
    `author`           VARCHAR(150)    DEFAULT 'SWAMITIME SOLUTIONS LTD',
    `status`           ENUM('published','draft') NOT NULL DEFAULT 'draft',
    `published_at`     DATETIME        DEFAULT NULL,
    `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_blog_posts_slug` (`slug`),
    KEY `idx_blog_posts_status` (`status`),
    KEY `idx_blog_posts_category_id` (`category_id`),
    KEY `idx_blog_posts_published_at` (`published_at`),
    CONSTRAINT `fk_blog_posts_category` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `blog_posts` (`id`, `title`, `slug`, `excerpt`, `category_id`, `tags`, `meta_title`, `meta_description`, `author`, `status`, `published_at`) VALUES
(1, 'The Future of Workforce Management: Trends Shaping 2026 and Beyond',
    'future-workforce-management-trends-2026',
    'Explore the key trends transforming workforce management in 2026, from AI-powered scheduling to predictive analytics and employee experience platforms.',
    1,
    'workforce management, WFM trends, AI scheduling, predictive analytics, 2026',
    'The Future of Workforce Management: 2026 Trends - SWAMITIME SOLUTIONS LTD',
    'Discover the key workforce management trends for 2026, including AI, predictive analytics, and employee experience technologies.',
    'SWAMITIME SOLUTIONS LTD', 'published', '2026-01-15 09:00:00'),

(2, 'Maximising ROI from Your UKG Dimensions Investment',
    'maximising-roi-ukg-dimensions-investment',
    'Practical strategies for getting the most value from UKG Dimensions. Learn how to optimise configuration, user adoption, and reporting.',
    2,
    'UKG Dimensions, ROI, UKG optimisation, workforce management ROI',
    'Maximising ROI from UKG Dimensions - SWAMITIME SOLUTIONS LTD',
    'Practical strategies to maximise ROI from your UKG Dimensions investment through optimisation and user adoption.',
    'SWAMITIME SOLUTIONS LTD', 'published', '2026-02-10 09:00:00'),

(3, 'How AI is Transforming HR Technology in 2026',
    'ai-transforming-hr-technology-2026',
    'Artificial intelligence is revolutionising HR technology. Discover how AI-powered tools are improving recruitment, employee engagement, and workforce planning.',
    3,
    'AI, HR technology, artificial intelligence, HR tech, recruitment AI',
    'How AI is Transforming HR Technology in 2026 - SWAMITIME SOLUTIONS LTD',
    'Explore how artificial intelligence is transforming HR technology, from recruitment to workforce planning.',
    'SWAMITIME SOLUTIONS LTD', 'published', '2026-03-05 09:00:00'),

(4, '5 Workforce Scheduling Mistakes Costing Your Business Money',
    'workforce-scheduling-mistakes-costing-money',
    'Poor scheduling practices drain profitability. Learn the five most common scheduling mistakes and how to fix them with modern workforce management tools.',
    6,
    'scheduling mistakes, workforce scheduling, shift planning, labour costs',
    '5 Workforce Scheduling Mistakes Costing Your Business Money - SWAMITIME SOLUTIONS LTD',
    'Learn the five most common workforce scheduling mistakes that cost businesses money and how to fix them.',
    'SWAMITIME SOLUTIONS LTD', 'published', '2026-04-20 09:00:00'),

(5, 'SEO Strategies for B2B Technology Companies in 2026',
    'seo-strategies-b2b-technology-2026',
    'Effective SEO strategies tailored for B2B technology companies. Learn how to attract qualified leads through search engine optimisation.',
    8,
    'SEO, B2B SEO, digital marketing, lead generation, search engine optimisation',
    'SEO Strategies for B2B Technology Companies in 2026 - SWAMITIME SOLUTIONS LTD',
    'Effective SEO strategies for B2B technology companies to attract qualified leads through search engine optimisation.',
    'SWAMITIME SOLUTIONS LTD', 'published', '2026-05-01 09:00:00');

-- ============================================================================
-- TABLE: faqs
-- ============================================================================
CREATE TABLE `faqs` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `question`   TEXT            NOT NULL,
    `answer`     TEXT            NOT NULL,
    `category`   VARCHAR(100)    DEFAULT NULL,
    `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order` INT             NOT NULL DEFAULT 0,
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_faqs_status` (`status`),
    KEY `idx_faqs_category` (`category`),
    KEY `idx_faqs_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `status`, `sort_order`) VALUES
(1, 'What is UKG and how can it benefit my business?',
    'UKG (Ultimate Kronos Group) is a leading provider of workforce management and human capital management solutions. UKG Pro and UKG Dimensions help businesses manage time and attendance, scheduling, absence management, HR, payroll, and workforce analytics. Benefits include reduced administrative costs, improved compliance, better workforce visibility, and enhanced employee experience.',
    'UKG Support', 'active', 1),
(2, 'What workforce management services does SWAMITIME SOLUTIONS LTD offer?',
    'We offer a comprehensive suite of workforce management services including UKG support, workforce management consulting, implementation and configuration, training and user support, managed support services, and reporting and data support. We also provide IT consulting, web development, and SEO services.',
    'General', 'active', 2),
(3, 'Do you provide ongoing support after UKG implementation?',
    'Yes, absolutely. We offer Managed Support Services that provide continuous, 24/7 support for your UKG environment. This includes proactive monitoring, incident resolution, system health checks, upgrades, and performance optimisation. We can also provide ad-hoc support on a retainer or project basis.',
    'Managed Support', 'active', 3),
(4, 'How long does a typical UKG implementation take?',
    'Implementation timelines vary based on the size of your organisation, the complexity of your requirements, and the modules being deployed. A typical mid-market implementation can take 8-16 weeks. We follow a structured methodology including discovery, design, configuration, testing, training, and go-live to ensure a smooth deployment.',
    'Implementation', 'active', 4),
(5, 'What industries do you specialise in?',
    'We specialise in Retail, Hospitality, Logistics & Distribution, Manufacturing, Healthcare & Care Services, Professional Services, and Small & Medium Businesses. Our consultants have deep industry knowledge and understand the unique workforce challenges each sector faces.',
    'Industries', 'active', 5),
(6, 'Can you integrate UKG with our existing HR and payroll systems?',
    'Yes, we have extensive experience integrating UKG with a wide range of HR, payroll, ERP, and business systems. We use standard APIs, middleware, and custom integrations to ensure seamless data flow between your systems, eliminating double data entry and reducing errors.',
    'Implementation', 'active', 6),
(7, 'What training options do you provide for our team?',
    'We offer a range of training options including on-site classroom training, remote virtual training, train-the-trainer programmes, and self-paced e-learning materials. Training is tailored to different user groups including managers, HR teams, payroll staff, and end users. We also provide post-go-live floor-walking support.',
    'Training', 'active', 7),
(8, 'How do your managed support services differ from standard UKG support?',
    'Our Managed Support Services go beyond standard vendor support by providing a dedicated team of UKG specialists who proactively manage your environment. We offer faster response times, proactive health monitoring, regular system reviews, and a single point of accountability. We also provide business-as-usual configuration changes and ongoing optimisation.',
    'Managed Support', 'active', 8),
(9, 'What kind of reporting and analytics can you provide?',
    'We design custom reports, dashboards, and analytics covering time and attendance, scheduling efficiency, absence trends, labour costs, compliance metrics, and workforce productivity. We use UKG''s reporting tools as well as BI platforms like Power BI and Tableau to deliver actionable insights.',
    'Reporting', 'active', 9),
(10, 'How do I get started with SWAMITIME SOLUTIONS LTD?',
    'Simply contact us via our website enquiry form, email us at admin@swamitime.com, or call our office. We will arrange an initial consultation to understand your needs, discuss potential solutions, and provide a tailored proposal. There is no obligation, and initial consultations are free.',
    'General', 'active', 10);

-- ============================================================================
-- TABLE: testimonials
-- ============================================================================
CREATE TABLE `testimonials` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150)    NOT NULL,
    `company`    VARCHAR(255)    DEFAULT NULL,
    `role`       VARCHAR(150)    DEFAULT NULL,
    `content`    TEXT            NOT NULL,
    `rating`     TINYINT UNSIGNED DEFAULT 5,
    `avatar`     VARCHAR(500)    DEFAULT NULL,
    `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order` INT             NOT NULL DEFAULT 0,
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_testimonials_status` (`status`),
    KEY `idx_testimonials_rating` (`rating`),
    KEY `idx_testimonials_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `testimonials` (`id`, `name`, `company`, `role`, `content`, `rating`, `status`, `sort_order`) VALUES
(1, 'Client A', 'Confidential - UK Retail Company', 'Director, UK Retail Company',
    'SWAMITIME transformed our workforce management operations. Their UKG implementation was seamless, and the ongoing managed support has been exceptional. We have seen significant improvements in payroll accuracy and scheduling efficiency across all our stores.',
    5, 'active', 1),
(2, 'Client B', 'Confidential - UK Logistics Provider', 'Operations Director, UK Logistics Provider',
    'The managed support service from SWAMITIME has been a game-changer for our business. System downtime is virtually eliminated, and their team responds to issues faster than we ever experienced with direct vendor support. Highly recommended for any business relying on UKG.',
    5, 'active', 2),
(3, 'Client C', 'Confidential - UK Healthcare Group', 'HR Director, UK Healthcare Group',
    'The workforce analytics solution SWAMITIME delivered has given us unprecedented visibility into our staffing costs and utilisation. We have reduced agency spend significantly and now have the data we need for strategic workforce planning. A truly professional and knowledgeable team.',
    5, 'active', 3);

-- ============================================================================
-- TABLE: trust_metrics
-- ============================================================================
CREATE TABLE `trust_metrics` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(255)    NOT NULL,
    `value`       VARCHAR(50)     NOT NULL,
    `icon`        VARCHAR(100)    DEFAULT NULL,
    `description` TEXT            DEFAULT NULL,
    `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `sort_order`  INT             NOT NULL DEFAULT 0,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_trust_metrics_status` (`status`),
    KEY `idx_trust_metrics_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `trust_metrics` (`id`, `title`, `value`, `icon`, `description`, `status`, `sort_order`) VALUES
(1, 'Smarter Workforce Processes',      '100%',  'fa-solid fa-brain',              'We deliver intelligent, automated workforce processes that eliminate manual inefficiencies and drive smarter decision-making.', 'active', 1),
(2, 'Reduced Manual Admin',             '85%',   'fa-solid fa-clock',               'Our solutions dramatically reduce manual administrative tasks, freeing your team to focus on strategic, value-adding activities.', 'active', 2),
(3, 'Improved System Visibility',       '100%',  'fa-solid fa-eye',                 'Gain complete visibility across your workforce operations with real-time dashboards and comprehensive reporting capabilities.', 'active', 3),
(4, 'Better Operational Control',       '100%',  'fa-solid fa-sliders',             'Take full control of your operations with tools that give you precision management of scheduling, attendance, and labour costs.', 'active', 4);

-- ============================================================================
-- TABLE: cta_blocks
-- ============================================================================
CREATE TABLE `cta_blocks` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(255)    NOT NULL,
    `subtitle`         VARCHAR(500)    DEFAULT NULL,
    `button_text`      VARCHAR(100)    NOT NULL,
    `button_url`       VARCHAR(500)    NOT NULL,
    `button_style`     ENUM('primary','secondary') NOT NULL DEFAULT 'primary',
    `background_style` VARCHAR(100)    DEFAULT 'default',
    `page_location`    VARCHAR(100)    NOT NULL,
    `status`           ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_cta_blocks_status` (`status`),
    KEY `idx_cta_blocks_page_location` (`page_location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cta_blocks` (`id`, `title`, `subtitle`, `button_text`, `button_url`, `button_style`, `background_style`, `page_location`, `status`) VALUES
(1, 'Ready to Transform Your Workforce Management?',
    'Book a free consultation with our experts and discover how we can help optimise your operations.',
    'Book a Free Consultation', '/contact-us', 'primary', 'gradient-blue', 'home_bottom', 'active'),
(2, 'Need Expert UKG Support?',
    'Our team of certified UKG specialists is ready to help you get the most from your investment.',
    'Get UKG Support', '/contact-us', 'primary', 'gradient-dark', 'services_bottom', 'active'),
(3, 'Let''s Discuss Your Project',
    'Tell us about your challenges and we will show you how our solutions can deliver measurable results.',
    'Start the Conversation', '/contact-us', 'secondary', 'light', 'services_page', 'active');

-- ============================================================================
-- TABLE: enquiries
-- ============================================================================
CREATE TABLE `enquiries` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `full_name`         VARCHAR(150)    NOT NULL,
    `company_name`      VARCHAR(255)    DEFAULT NULL,
    `email`             VARCHAR(255)    NOT NULL,
    `phone`             VARCHAR(30)     DEFAULT NULL,
    `service_required`  VARCHAR(255)    DEFAULT NULL,
    `budget_range`      VARCHAR(100)    DEFAULT NULL,
    `preferred_contact` ENUM('email','phone','either') NOT NULL DEFAULT 'email',
    `message`           TEXT            DEFAULT NULL,
    `gdpr_consent`      TINYINT(1)      NOT NULL DEFAULT 0,
    `status`            ENUM('new','read','replied','archived') NOT NULL DEFAULT 'new',
    `ip_address`        VARCHAR(45)     DEFAULT NULL,
    `created_at`        DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_enquiries_status` (`status`),
    KEY `idx_enquiries_email` (`email`),
    KEY `idx_enquiries_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: crm_leads
-- ============================================================================
CREATE TABLE `crm_leads` (
    `id`                 INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `full_name`          VARCHAR(150)    NOT NULL,
    `company_name`       VARCHAR(255)    DEFAULT NULL,
    `email`              VARCHAR(255)    NOT NULL,
    `phone`              VARCHAR(30)     DEFAULT NULL,
    `service_interested` VARCHAR(255)    DEFAULT NULL,
    `message`            TEXT            DEFAULT NULL,
    `lead_source`        ENUM('website','contact_form','referral','linkedin','other') NOT NULL DEFAULT 'website',
    `lead_status`        ENUM('new','contacted','qualified','proposal_sent','won','lost') NOT NULL DEFAULT 'new',
    `priority`           ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
    `assigned_user_id`   INT UNSIGNED    DEFAULT NULL,
    `follow_up_date`     DATETIME        DEFAULT NULL,
    `converted_at`       DATETIME        DEFAULT NULL,
    `enquiry_id`         INT UNSIGNED    DEFAULT NULL,
    `created_at`         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_crm_leads_lead_status` (`lead_status`),
    KEY `idx_crm_leads_priority` (`priority`),
    KEY `idx_crm_leads_assigned_user_id` (`assigned_user_id`),
    KEY `idx_crm_leads_lead_source` (`lead_source`),
    KEY `idx_crm_leads_follow_up_date` (`follow_up_date`),
    KEY `idx_crm_leads_email` (`email`),
    KEY `idx_crm_leads_enquiry_id` (`enquiry_id`),
    CONSTRAINT `fk_crm_leads_assigned_user` FOREIGN KEY (`assigned_user_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_crm_leads_enquiry` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: crm_notes
-- ============================================================================
CREATE TABLE `crm_notes` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `lead_id`    INT UNSIGNED    NOT NULL,
    `user_id`    INT UNSIGNED    DEFAULT NULL,
    `note`       TEXT            NOT NULL,
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_crm_notes_lead_id` (`lead_id`),
    KEY `idx_crm_notes_user_id` (`user_id`),
    KEY `idx_crm_notes_created_at` (`created_at`),
    CONSTRAINT `fk_crm_notes_lead` FOREIGN KEY (`lead_id`) REFERENCES `crm_leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_crm_notes_user` FOREIGN KEY (`user_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: site_settings
-- ============================================================================
CREATE TABLE `site_settings` (
    `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `setting_key`  VARCHAR(100)    NOT NULL,
    `setting_value` TEXT           DEFAULT NULL,
    `updated_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_site_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name',             'SWAMITIME SOLUTIONS LTD'),
('site_tagline',          'Premium B2B Technology Consulting for Workforce Management & Digital Solutions'),
('site_description',      'SWAMITIME SOLUTIONS LTD is a premium B2B technology consulting company delivering expert workforce management, UKG support, IT consulting, web development, and SEO services to businesses across the United Kingdom.'),
('site_url',              'https://swamitime.com'),
('admin_email',           'admin@swamitime.com'),
('phone',                 '+44 (0)20 XXXX XXXX'),
('address',               'London, United Kingdom'),
('working_hours',         'Monday - Friday: 09:00 - 18:00'),
('social_facebook',       'https://facebook.com/swamitime'),
('social_linkedin',       'https://linkedin.com/company/swamitime'),
('social_twitter',        'https://twitter.com/swamitime'),
('logo_path',             '/assets/images/logo.svg'),
('favicon_path',          '/assets/images/favicon.ico'),
('footer_disclaimer',     'SWAMITIME SOLUTIONS LTD is a company registered in England and Wales. Registered office: London, United Kingdom. © 2026 SWAMITIME SOLUTIONS LTD. All rights reserved. The information contained on this website is for general information purposes only. While we endeavour to keep the information up to date and correct, we make no representations or warranties of any kind, express or implied, about the completeness, accuracy, reliability, suitability, or availability with respect to the website or the information, products, services, or related graphics contained on the website for any purpose. Any reliance you place on such information is therefore strictly at your own risk. In no event will we be liable for any loss or damage including without limitation, indirect or consequential loss or damage, or any loss or damage whatsoever arising from loss of data or profits arising out of, or in connection with, the use of this website. Through this website you are able to link to other websites which are not under the control of SWAMITIME SOLUTIONS LTD. We have no control over the nature, content, and availability of those sites. The inclusion of any links does not necessarily imply a recommendation or endorse the views expressed within them.'),
('cookie_consent_text',   'We use cookies to enhance your browsing experience, serve personalised content, and analyse our traffic. By clicking "Accept All", you consent to our use of cookies. You can manage your preferences in Cookie Settings.'),
('privacy_text',          'SWAMITIME SOLUTIONS LTD is committed to protecting and respecting your privacy. This policy explains when and why we collect personal information, how we use it, the conditions under which we may disclose it to others, and how we keep it secure. We comply with the UK General Data Protection Regulation (UK GDPR) and the Data Protection Act 2018. We will never sell your personal data to third parties.'),
('terms_text',            'By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this website. SWAMITIME SOLUTIONS LTD reserves the right to change these terms and conditions at any time, and you agree to abide by the most recent version of this Terms and Conditions Agreement each time you view and use the website.');

-- ============================================================================
-- TABLE: seo_settings
-- ============================================================================
CREATE TABLE `seo_settings` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `page_type`        VARCHAR(50)     NOT NULL COMMENT 'page, service, industry, blog_post, case_study',
    `page_id`          INT UNSIGNED    NOT NULL,
    `meta_title`       VARCHAR(255)    DEFAULT NULL,
    `meta_description` TEXT            DEFAULT NULL,
    `meta_keywords`    TEXT            DEFAULT NULL,
    `og_title`         VARCHAR(255)    DEFAULT NULL,
    `og_description`   TEXT            DEFAULT NULL,
    `og_image`         VARCHAR(500)    DEFAULT NULL,
    `twitter_card`     VARCHAR(50)     DEFAULT 'summary_large_image',
    `canonical_url`    VARCHAR(500)    DEFAULT NULL,
    `schema_markup`    TEXT            DEFAULT NULL,
    `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_seo_settings_page_type_page_id` (`page_type`, `page_id`),
    KEY `idx_seo_settings_page_type` (`page_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: analytics_settings
-- ============================================================================
CREATE TABLE `analytics_settings` (
    `id`                           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `google_analytics_id`          VARCHAR(50)     DEFAULT NULL,
    `google_tag_manager_id`        VARCHAR(50)     DEFAULT NULL,
    `google_search_console_meta`   VARCHAR(500)    DEFAULT NULL,
    `meta_pixel_id`                VARCHAR(50)     DEFAULT NULL,
    `linkedin_insight_tag`         VARCHAR(50)     DEFAULT NULL,
    `custom_header_scripts`        TEXT            DEFAULT NULL,
    `custom_footer_scripts`        TEXT            DEFAULT NULL,
    `updated_at`                   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `analytics_settings` (`id`) VALUES (1);

-- ============================================================================
-- TABLE: ai_settings
-- ============================================================================
CREATE TABLE `ai_settings` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `api_provider`  ENUM('openai','deepseek','anthropic','custom') NOT NULL DEFAULT 'openai',
    `api_key`       VARCHAR(500)    DEFAULT NULL,
    `model_name`    VARCHAR(100)    DEFAULT NULL,
    `temperature`   DECIMAL(3,2)    DEFAULT 0.7,
    `max_tokens`    INT UNSIGNED    DEFAULT 2000,
    `is_active`     TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ai_settings` (`id`, `is_active`) VALUES (1, 0);

-- ============================================================================
-- TABLE: ai_suggestions
-- ============================================================================
CREATE TABLE `ai_suggestions` (
    `id`                 INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `page_type`          VARCHAR(50)     NOT NULL,
    `page_id`            INT UNSIGNED    NOT NULL,
    `suggestion_type`    ENUM('keyword','title','meta_description','meta_keywords','blog_topic','faq','alt_text','internal_link','content_improvement') NOT NULL,
    `suggestion_content` TEXT            NOT NULL,
    `is_approved`        TINYINT(1)      NOT NULL DEFAULT 0,
    `is_applied`         TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at`         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ai_suggestions_page` (`page_type`, `page_id`),
    KEY `idx_ai_suggestions_type` (`suggestion_type`),
    KEY `idx_ai_suggestions_approved` (`is_approved`),
    KEY `idx_ai_suggestions_applied` (`is_applied`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: media_library
-- ============================================================================
CREATE TABLE `media_library` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `filename`      VARCHAR(255)    NOT NULL,
    `original_name` VARCHAR(255)    NOT NULL,
    `file_path`     VARCHAR(500)    NOT NULL,
    `file_type`     VARCHAR(50)     NOT NULL,
    `file_size`     INT UNSIGNED    NOT NULL DEFAULT 0,
    `alt_text`      VARCHAR(500)    DEFAULT NULL,
    `uploaded_by`   INT UNSIGNED    DEFAULT NULL,
    `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_media_library_file_type` (`file_type`),
    KEY `idx_media_library_uploaded_by` (`uploaded_by`),
    KEY `idx_media_library_created_at` (`created_at`),
    CONSTRAINT `fk_media_library_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: menu_items
-- ============================================================================
CREATE TABLE `menu_items` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(100)    NOT NULL,
    `url`        VARCHAR(500)    NOT NULL,
    `parent_id`  INT UNSIGNED    DEFAULT NULL,
    `sort_order` INT             NOT NULL DEFAULT 0,
    `location`   ENUM('header','footer','both') NOT NULL DEFAULT 'header',
    `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_menu_items_parent_id` (`parent_id`),
    KEY `idx_menu_items_location` (`location`),
    KEY `idx_menu_items_status` (`status`),
    KEY `idx_menu_items_sort_order` (`sort_order`),
    CONSTRAINT `fk_menu_items_parent` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menu_items` (`id`, `title`, `url`, `parent_id`, `sort_order`, `location`, `status`) VALUES
-- Header: Top-level
(1,  'Home',               '/',                       NULL, 1, 'header', 'active'),
(2,  'About Us',           '/about-us',               NULL, 2, 'header', 'active'),
(3,  'Services',           '/services',               NULL, 3, 'header', 'active'),
(4,  'Industries',         '/industries',             NULL, 4, 'header', 'active'),
(5,  'Case Studies',       '/case-studies',           NULL, 5, 'header', 'active'),
(6,  'Blog',               '/blog',                   NULL, 6, 'header', 'active'),
(7,  'Contact Us',         '/contact-us',             NULL, 7, 'header', 'active'),
-- Services children (parent_id = 3)
(8,  'UKG Workforce Management Support',  '/services/ukg-workforce-management-support',  3, 1, 'header', 'active'),
(9,  'Workforce Management Consulting',   '/services/workforce-management-consulting',   3, 2, 'header', 'active'),
(10, 'Implementation & Configuration',    '/services/implementation-configuration-support', 3, 3, 'header', 'active'),
(11, 'Training & User Support',           '/services/training-user-support',             3, 4, 'header', 'active'),
(12, 'Managed Support Services',          '/services/managed-support-services',          3, 5, 'header', 'active'),
(13, 'Reporting & Data Support',          '/services/reporting-data-support',            3, 6, 'header', 'active'),
(14, 'IT Consulting',                     '/services/it-consulting',                     3, 7, 'header', 'active'),
(15, 'Web Development',                   '/services/web-development',                   3, 8, 'header', 'active'),
(16, 'SEO & Digital Marketing',           '/services/seo-digital-marketing',             3, 9, 'header', 'active'),
-- Footer: Legal
(17, 'Privacy Policy',     '/privacy-policy',     NULL, 1, 'footer', 'active'),
(18, 'Terms & Conditions', '/terms-conditions',   NULL, 2, 'footer', 'active'),
(19, 'Cookie Policy',      '/cookie-policy',      NULL, 3, 'footer', 'active'),
-- Footer: Quick Links
(20, 'Home',               '/',                   NULL, 4, 'footer', 'active'),
(21, 'About Us',           '/about-us',           NULL, 5, 'footer', 'active'),
(22, 'Services',           '/services',           NULL, 6, 'footer', 'active'),
(23, 'Blog',               '/blog',               NULL, 7, 'footer', 'active'),
(24, 'Contact Us',         '/contact-us',         NULL, 8, 'footer', 'active');

-- ============================================================================
-- TABLE: footer_links
-- ============================================================================
CREATE TABLE `footer_links` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(100)    NOT NULL,
    `url`        VARCHAR(500)    NOT NULL,
    `section`    ENUM('quick_links','services','legal') NOT NULL,
    `sort_order` INT             NOT NULL DEFAULT 0,
    `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_footer_links_section` (`section`),
    KEY `idx_footer_links_status` (`status`),
    KEY `idx_footer_links_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `footer_links` (`id`, `title`, `url`, `section`, `sort_order`, `status`) VALUES
-- Quick Links
(1,  'Home',                              '/',                                     'quick_links', 1, 'active'),
(2,  'About Us',                          '/about-us',                            'quick_links', 2, 'active'),
(3,  'Industries',                        '/industries',                          'quick_links', 3, 'active'),
(4,  'Case Studies',                      '/case-studies',                        'quick_links', 4, 'active'),
(5,  'Blog',                              '/blog',                                'quick_links', 5, 'active'),
(6,  'Contact Us',                        '/contact-us',                          'quick_links', 6, 'active'),
-- Services
(7,  'UKG Workforce Management Support',  '/services/ukg-workforce-management-support',  'services', 1, 'active'),
(8,  'Workforce Management Consulting',   '/services/workforce-management-consulting',   'services', 2, 'active'),
(9,  'Implementation & Configuration',    '/services/implementation-configuration-support', 'services', 3, 'active'),
(10, 'Training & User Support',           '/services/training-user-support',             'services', 4, 'active'),
(11, 'Managed Support Services',          '/services/managed-support-services',          'services', 5, 'active'),
(12, 'Reporting & Data Support',          '/services/reporting-data-support',            'services', 6, 'active'),
(13, 'IT Consulting',                     '/services/it-consulting',                     'services', 7, 'active'),
(14, 'Web Development',                   '/services/web-development',                   'services', 8, 'active'),
(15, 'SEO & Digital Marketing',           '/services/seo-digital-marketing',             'services', 9, 'active'),
-- Legal
(16, 'Privacy Policy',                    '/privacy-policy',                      'legal', 1, 'active'),
(17, 'Terms & Conditions',                '/terms-conditions',                    'legal', 2, 'active'),
(18, 'Cookie Policy',                     '/cookie-policy',                       'legal', 3, 'active');

-- ============================================================================
-- TABLE: redirects
-- ============================================================================
CREATE TABLE `redirects` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `old_url`     VARCHAR(500)    NOT NULL,
    `new_url`     VARCHAR(500)    NOT NULL,
    `status_code` SMALLINT UNSIGNED NOT NULL DEFAULT 301 COMMENT '301=permanent, 302=temporary',
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_redirects_old_url` (`old_url`(255)),
    KEY `idx_redirects_status_code` (`status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE: analytics_data
-- ============================================================================
CREATE TABLE `analytics_data` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `page_url`    VARCHAR(500)    NOT NULL,
    `page_title`  VARCHAR(255)    DEFAULT NULL,
    `visitor_ip`  VARCHAR(45)     DEFAULT NULL,
    `user_agent`  TEXT            DEFAULT NULL,
    `referrer`    VARCHAR(500)    DEFAULT NULL,
    `session_id`  VARCHAR(100)    DEFAULT NULL,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_analytics_data_page_url` (`page_url`(255)),
    KEY `idx_analytics_data_session_id` (`session_id`),
    KEY `idx_analytics_data_created_at` (`created_at`),
    KEY `idx_analytics_data_visitor_ip` (`visitor_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Re-enable foreign key checks
-- ============================================================================
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
