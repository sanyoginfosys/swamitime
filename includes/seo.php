<?php

class SEO
{
    public static function getPageSEO(string $pageSlug): array
    {
        $db = self::db();
        if (!$db) {
            return [];
        }

        $stmt = $db->prepare("
            SELECT
                p.id,
                p.title,
                p.content,
                p.meta_title        AS page_meta_title,
                p.meta_description  AS page_meta_description,
                p.meta_keywords     AS page_meta_keywords,
                s.meta_title,
                s.meta_description,
                s.meta_keywords,
                s.og_title,
                s.og_description,
                s.og_image,
                s.twitter_card,
                s.canonical_url,
                s.schema_markup
            FROM pages p
            LEFT JOIN seo_settings s ON s.page_type = 'page' AND s.page_id = p.id
            WHERE p.slug = ? AND p.status = 'published'
            LIMIT 1
        ");
        $stmt->execute([$pageSlug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return [];
        }

        return self::buildSEOData($row, 'page');
    }

    public static function getServiceSEO(int $serviceId): array
    {
        $db = self::db();
        if (!$db) {
            return [];
        }
        $stmt = $db->prepare("
            SELECT
                sv.id,
                sv.title,
                sv.slug,
                sv.content,
                sv.short_description,
                sv.meta_title        AS service_meta_title,
                sv.meta_description  AS service_meta_description,
                sv.meta_keywords     AS service_meta_keywords,
                sv.featured_image,
                s.meta_title,
                s.meta_description,
                s.meta_keywords,
                s.og_title,
                s.og_description,
                s.og_image,
                s.twitter_card,
                s.canonical_url,
                s.schema_markup
            FROM services sv
            LEFT JOIN seo_settings s ON s.page_type = 'service' AND s.page_id = sv.id
            WHERE sv.id = ? AND sv.status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$serviceId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return [];
        }

        return self::buildSEOData($row, 'service');
    }

    public static function getBlogSEO(int $postId): array
    {
        $db = self::db();
        if (!$db) {
            return [];
        }
        $stmt = $db->prepare("
            SELECT
                bp.id,
                bp.title,
                bp.slug,
                bp.content,
                bp.excerpt,
                bp.featured_image,
                bp.published_at,
                bp.tags,
                bp.meta_title        AS post_meta_title,
                bp.meta_description  AS post_meta_description,
                bp.author,
                bc.name              AS category_name,
                s.meta_title,
                s.meta_description,
                s.meta_keywords,
                s.og_title,
                s.og_description,
                s.og_image,
                s.twitter_card,
                s.canonical_url,
                s.schema_markup
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bc.id = bp.category_id
            LEFT JOIN seo_settings s ON s.page_type = 'blog_post' AND s.page_id = bp.id
            WHERE bp.id = ? AND bp.status = 'published'
            LIMIT 1
        ");
        $stmt->execute([$postId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return [];
        }

        return self::buildSEOData($row, 'blog_post');
    }

    public static function metaTags(array $seoData): string
    {
        $tags = '';

        if (!empty($seoData['meta_title'])) {
            $tags .= '<title>' . htmlspecialchars($seoData['meta_title']) . '</title>' . "\n";
        }

        if (!empty($seoData['meta_description'])) {
            $tags .= '<meta name="description" content="' . htmlspecialchars($seoData['meta_description']) . '">' . "\n";
        }

        if (!empty($seoData['meta_keywords'])) {
            $tags .= '<meta name="keywords" content="' . htmlspecialchars($seoData['meta_keywords']) . '">' . "\n";
        }

        if (!empty($seoData['canonical_url'])) {
            $tags .= '<link rel="canonical" href="' . htmlspecialchars($seoData['canonical_url']) . '">' . "\n";
        }

        $tags .= '<meta name="robots" content="index, follow">' . "\n";
        $tags .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";

        return $tags;
    }

    public static function ogTags(array $seoData): string
    {
        $tags   = '';
        $title  = $seoData['og_title']         ?? $seoData['meta_title'] ?? $seoData['title'] ?? '';
        $desc   = $seoData['og_description']   ?? $seoData['meta_description'] ?? '';
        $image  = $seoData['og_image']         ?? ($seoData['site_url'] ?? '') . '/assets/images/og-default.jpg';
        $url    = $seoData['canonical_url']    ?? self::currentUrl();
        $type   = $seoData['og_type']          ?? 'website';
        $site   = $seoData['site_name']        ?? '';

        $tags .= '<meta property="og:type" content="' . htmlspecialchars($type) . '">' . "\n";

        if ($site !== '') {
            $tags .= '<meta property="og:site_name" content="' . htmlspecialchars($site) . '">' . "\n";
        }

        if ($title !== '') {
            $tags .= '<meta property="og:title" content="' . htmlspecialchars($title) . '">' . "\n";
        }

        if ($desc !== '') {
            $tags .= '<meta property="og:description" content="' . htmlspecialchars($desc) . '">' . "\n";
        }

        if ($image !== '') {
            $tags .= '<meta property="og:image" content="' . htmlspecialchars($image) . '">' . "\n";
        }

        if ($url !== '') {
            $tags .= '<meta property="og:url" content="' . htmlspecialchars($url) . '">' . "\n";
        }

        return $tags;
    }

    public static function twitterTags(array $seoData): string
    {
        $tags   = '';
        $card   = $seoData['twitter_card'] ?? 'summary_large_image';
        $title  = $seoData['og_title']     ?? $seoData['meta_title'] ?? $seoData['title'] ?? '';
        $desc   = $seoData['og_description'] ?? $seoData['meta_description'] ?? '';
        $image  = $seoData['og_image']     ?? ($seoData['site_url'] ?? '') . '/assets/images/og-default.jpg';

        $tags .= '<meta name="twitter:card" content="' . htmlspecialchars($card) . '">' . "\n";

        if ($title !== '') {
            $tags .= '<meta name="twitter:title" content="' . htmlspecialchars($title) . '">' . "\n";
        }

        if ($desc !== '') {
            $tags .= '<meta name="twitter:description" content="' . htmlspecialchars($desc) . '">' . "\n";
        }

        if ($image !== '') {
            $tags .= '<meta name="twitter:image" content="' . htmlspecialchars($image) . '">' . "\n";
        }

        return $tags;
    }

    public static function schemaMarkup(string $type, array $data): string
    {
        $schema = match ($type) {
            'Organization' => [
                '@context'  => 'https://schema.org',
                '@type'     => 'Organization',
                'name'      => $data['name']      ?? ($data['site_name'] ?? ''),
                'url'       => $data['url']       ?? ($data['site_url'] ?? ''),
                'logo'      => $data['logo']      ?? '',
                'contactPoint' => [
                    '@type'        => 'ContactPoint',
                    'telephone'    => $data['phone'] ?? '',
                    'contactType'  => 'customer service',
                    'email'        => $data['email'] ?? '',
                ],
                'sameAs' => $data['sameAs'] ?? [],
            ],
            'WebSite' => [
                '@context'    => 'https://schema.org',
                '@type'       => 'WebSite',
                'name'        => $data['name'] ?? ($data['site_name'] ?? ''),
                'url'         => $data['url'] ?? ($data['site_url'] ?? ''),
                'description' => $data['description'] ?? '',
            ],
            'Article', 'BlogPosting' => [
                '@context'      => 'https://schema.org',
                '@type'         => $type,
                'headline'      => $data['headline'] ?? $data['title'] ?? '',
                'description'   => $data['description'] ?? $data['meta_description'] ?? '',
                'image'         => $data['image'] ?? '',
                'datePublished' => $data['datePublished'] ?? $data['published_at'] ?? '',
                'dateModified'  => $data['dateModified'] ?? $data['updated_at'] ?? '',
                'author' => [
                    '@type' => 'Organization',
                    'name'  => $data['author'] ?? ($data['site_name'] ?? ''),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name'  => $data['site_name'] ?? '',
                    'logo'  => ['@type' => 'ImageObject', 'url' => $data['logo'] ?? ''],
                ],
            ],
            'BreadcrumbList' => [
                '@context'        => 'https://schema.org',
                '@type'           => 'BreadcrumbList',
                'itemListElement' => array_map(
                    fn(int $i, array $item): array => [
                        '@type'    => 'ListItem',
                        'position' => $i + 1,
                        'name'     => $item['name'],
                        'item'     => $item['url'] ?? '',
                    ],
                    array_keys($data['items'] ?? []),
                    $data['items'] ?? [],
                ),
            ],
            'Service' => [
                '@context'      => 'https://schema.org',
                '@type'         => 'Service',
                'name'          => $data['name'] ?? $data['title'] ?? '',
                'description'   => $data['description'] ?? $data['meta_description'] ?? '',
                'provider'      => [
                    '@type' => 'Organization',
                    'name'  => $data['site_name'] ?? '',
                ],
                'serviceType'   => $data['serviceType'] ?? '',
            ],
            'FAQPage' => [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => array_map(
                    fn(array $faq): array => [
                        '@type'           => 'Question',
                        'name'            => $faq['question'],
                        'acceptedAnswer'  => [
                            '@type' => 'Answer',
                            'text'  => $faq['answer'],
                        ],
                    ],
                    $data['faqs'] ?? [],
                ),
            ],
            default => [],
        };

        if (empty($schema)) {
            return '';
        }

        $json = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR,
        );

        return '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
    }

    public static function breadcrumbs(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<nav aria-label="Breadcrumb"><ol class="breadcrumb">';

        $count = count($items);

        foreach ($items as $i => $item) {
            $isLast = ($i === $count - 1);

            if ($isLast) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">'
                      . htmlspecialchars($item['name'])
                      . '</li>';
            } else {
                $url = $item['url'] ?? '#';
                $html .= '<li class="breadcrumb-item">'
                      . '<a href="' . htmlspecialchars($url) . '">'
                      . htmlspecialchars($item['name'])
                      . '</a>'
                      . '</li>';
            }
        }

        $html .= '</ol></nav>';

        return $html;
    }

    public static function generateSitemap(): string
    {
        $db      = self::db();
        $siteUrl = rtrim(self::getSiteSetting('site_url') ?? 'https://swamitime.com', '/');
        $urls    = [];

        $pages = $db->query("SELECT slug, updated_at FROM pages WHERE status = 'published' ORDER BY sort_order");
        while ($row = $pages->fetch(PDO::FETCH_ASSOC)) {
            $loc = $row['slug'] === 'home' ? $siteUrl . '/' : $siteUrl . '/' . $row['slug'];
            $urls[] = ['loc' => $loc, 'lastmod' => $row['updated_at'], 'changefreq' => 'monthly', 'priority' => '0.8'];
        }

        $services = $db->query("SELECT slug, updated_at FROM services WHERE status = 'active' ORDER BY sort_order");
        while ($row = $services->fetch(PDO::FETCH_ASSOC)) {
            $urls[] = [
                'loc'        => $siteUrl . '/services/' . $row['slug'],
                'lastmod'    => $row['updated_at'],
                'changefreq' => 'monthly',
                'priority'   => '0.7',
            ];
        }

        $industries = $db->query("SELECT slug, updated_at FROM industries WHERE status = 'active' ORDER BY sort_order");
        while ($row = $industries->fetch(PDO::FETCH_ASSOC)) {
            $urls[] = [
                'loc'        => $siteUrl . '/industries/' . $row['slug'],
                'lastmod'    => $row['updated_at'],
                'changefreq' => 'monthly',
                'priority'   => '0.6',
            ];
        }

        $blogPosts = $db->query("SELECT slug, updated_at FROM blog_posts WHERE status = 'published' ORDER BY published_at DESC");
        while ($row = $blogPosts->fetch(PDO::FETCH_ASSOC)) {
            $urls[] = [
                'loc'        => $siteUrl . '/blog/' . $row['slug'],
                'lastmod'    => $row['updated_at'],
                'changefreq' => 'weekly',
                'priority'   => '0.5',
            ];
        }

        $caseStudies = $db->query("SELECT slug, updated_at FROM case_studies WHERE status = 'published' ORDER BY sort_order");
        while ($row = $caseStudies->fetch(PDO::FETCH_ASSOC)) {
            $urls[] = [
                'loc'        => $siteUrl . '/case-studies/' . $row['slug'],
                'lastmod'    => $row['updated_at'],
                'changefreq' => 'monthly',
                'priority'   => '0.6',
            ];
        }

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . substr($url['lastmod'], 0, 10) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    public static function canonical(?string $url = null): string
    {
        if ($url !== null) {
            return '<link rel="canonical" href="' . htmlspecialchars($url) . '">' . "\n";
        }

        $currentUrl = self::currentUrl();

        if ($currentUrl === '') {
            return '';
        }

        return '<link rel="canonical" href="' . htmlspecialchars($currentUrl) . '">' . "\n";
    }

    public static function head(?string $pageSlug = null, array $override = []): string
    {
        $seoData = [];

        if ($pageSlug !== null) {
            $seoData = self::getPageSEO($pageSlug);
        }

        $seoData = array_merge($seoData, $override);

        if (empty($seoData['site_name'])) {
            $seoData['site_name'] = self::getSiteSetting('site_name') ?? 'SWAMITIME SOLUTIONS LTD';
        }

        if (empty($seoData['site_url'])) {
            $seoData['site_url'] = self::getSiteSetting('site_url') ?? 'https://swamitime.com';
        }

        $output  = self::metaTags($seoData);
        $output .= self::ogTags($seoData);
        $output .= self::twitterTags($seoData);

        if (!empty($seoData['schema_markup'])) {
            $output .= '<script type="application/ld+json">' . "\n"
                     . $seoData['schema_markup'] . "\n"
                     . '</script>' . "\n";
        }

        return $output;
    }

    // ---------------------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------------------

    private static function buildSEOData(array $row, string $type): array
    {
        $siteName = self::getSiteSetting('site_name') ?? 'SWAMITIME SOLUTIONS LTD';
        $siteUrl  = self::getSiteSetting('site_url') ?? 'https://swamitime.com';

        $prefix = match ($type) {
            'service'   => 'service_',
            'blog_post' => 'post_',
            default     => 'page_',
        };

        $metaTitle       = $row['meta_title']       ?: ($row[$prefix . 'meta_title'] ?? '');
        $metaDescription = $row['meta_description'] ?: ($row[$prefix . 'meta_description'] ?? '');
        $metaKeywords    = $row['meta_keywords']    ?: ($row[$prefix . 'meta_keywords'] ?? '');

        if ($metaTitle === '' && isset($row['title'])) {
            $metaTitle = $row['title'] . ' - ' . $siteName;
        }

        return [
            'title'            => $row['title'] ?? '',
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_keywords'    => $metaKeywords,
            'og_title'         => $row['og_title']         ?: $metaTitle,
            'og_description'   => $row['og_description']   ?: $metaDescription,
            'og_image'         => $row['og_image']         ?: ($siteUrl . '/assets/images/og-default.jpg'),
            'twitter_card'     => $row['twitter_card']     ?: 'summary_large_image',
            'canonical_url'    => $row['canonical_url']    ?: '',
            'schema_markup'    => $row['schema_markup']    ?: '',
            'site_name'        => $siteName,
            'site_url'         => $siteUrl,
            'published_at'     => $row['published_at']     ?? null,
            'featured_image'   => $row['featured_image']   ?? null,
            'excerpt'          => $row['excerpt']          ?? ($row['short_description'] ?? null),
            'category_name'    => $row['category_name']    ?? null,
            'author'           => $row['author']           ?? null,
            'tags'             => $row['tags']             ?? null,
        ];
    }

    private static function currentUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST']   ?? 'localhost';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';

        return $scheme . '://' . $host . $uri;
    }

    private static function getSiteSetting(string $key): ?string
    {
        $db = self::db();
        if (!$db) {
            return null;
        }
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1");
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['setting_value'] ?? null;
    }

    private static function db(): ?PDO
    {
        global $db;

        if ($db instanceof PDO) {
            return $db;
        }

        if (function_exists('getDB')) {
            return getDB();
        }

        return null;
    }
}
