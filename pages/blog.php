<?php
$slug = 'blog';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'Insights & Resources';

$current_page = max(1, (int) ($_GET['page'] ?? 1));
$per_page = 6;
$search = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

$posts = [];
$total_posts = 0;
$categories = [];

try {
    $db = getDB();
    $categoryStmt = $db->query("SELECT DISTINCT name, slug FROM blog_categories ORDER BY name ASC");
    $categories = $categoryStmt->fetchAll();

    $where = "WHERE p.status = 'published'";
    $params = [];
    if ($search) {
        $where .= " AND (p.title LIKE ? OR p.excerpt LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }
    if ($category_filter) {
        $where .= " AND c.slug = ?";
        $params[] = $category_filter;
    }

    $countStmt = $db->prepare("SELECT COUNT(*) FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id $where");
    $countStmt->execute($params);
    $total_posts = (int) $countStmt->fetchColumn();

    $offset = ($current_page - 1) * $per_page;
    $dataStmt = $db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM blog_posts p LEFT JOIN blog_categories c ON p.category_id = c.id $where ORDER BY p.published_at DESC LIMIT $per_page OFFSET $offset");
    $dataStmt->execute($params);
    $posts = $dataStmt->fetchAll();
} catch (Exception $e) {
    $posts = [];
    $total_posts = 0;
    $categories = [];
}

if (empty($categories)) {
    $categories = [
        ['name' => 'Workforce Management', 'slug' => 'workforce-management'],
        ['name' => 'Time & Attendance', 'slug' => 'time-attendance'],
        ['name' => 'Scheduling', 'slug' => 'scheduling'],
        ['name' => 'HR Technology', 'slug' => 'hr-technology'],
    ];
}

if (empty($posts)) {
    $allFallbackPosts = [
        ['title' => 'How Workforce Management Systems Help Businesses Reduce Manual Admin', 'slug' => 'workforce-management-systems-reduce-manual-admin', 'category_name' => 'Workforce Management', 'category_slug' => 'workforce-management', 'excerpt' => 'Discover how modern workforce management platforms automate time-consuming administrative tasks, freeing your HR and operations teams to focus on strategic priorities.', 'author_name' => 'SwamiTime Editorial Team', 'published_at' => '2026-05-15', 'read_time' => '6 min read'],
        ['title' => 'What to Check Before Implementing a Time and Attendance System', 'slug' => 'what-to-check-before-implementing-time-attendance', 'category_name' => 'Time & Attendance', 'category_slug' => 'time-attendance', 'excerpt' => 'A practical checklist for organisations evaluating time and attendance solutions, covering integration requirements, compliance considerations, and employee adoption.', 'author_name' => 'SwamiTime Editorial Team', 'published_at' => '2026-05-01', 'read_time' => '5 min read'],
        ['title' => 'How Better Scheduling Improves Workforce Productivity', 'slug' => 'better-scheduling-improves-workforce-productivity', 'category_name' => 'Scheduling', 'category_slug' => 'scheduling', 'excerpt' => 'Explore the direct link between effective scheduling practices and measurable improvements in productivity, employee engagement, and operational efficiency.', 'author_name' => 'SwamiTime Editorial Team', 'published_at' => '2026-04-20', 'read_time' => '7 min read'],
        ['title' => 'Why Reporting Matters in Workforce Management', 'slug' => 'why-reporting-matters-in-workforce-management', 'category_name' => 'HR Technology', 'category_slug' => 'hr-technology', 'excerpt' => 'Understand how data-driven reporting transforms workforce decision-making, from compliance tracking to strategic workforce planning.', 'author_name' => 'SwamiTime Editorial Team', 'published_at' => '2026-04-10', 'read_time' => '5 min read'],
        ['title' => 'How HR Technology Supports Smarter Business Operations', 'slug' => 'hr-technology-supports-smarter-business-operations', 'category_name' => 'HR Technology', 'category_slug' => 'hr-technology', 'excerpt' => 'An overview of how integrated HR technology platforms are reshaping business operations, enabling data-driven people management and operational excellence.', 'author_name' => 'SwamiTime Editorial Team', 'published_at' => '2026-03-25', 'read_time' => '6 min read'],
    ];

    if ($category_filter) {
        $allFallbackPosts = array_filter($allFallbackPosts, function ($p) use ($category_filter) {
            return $p['category_slug'] === $category_filter;
        });
    }
    if ($search) {
        $searchLower = strtolower($search);
        $allFallbackPosts = array_filter($allFallbackPosts, function ($p) use ($searchLower) {
            return strpos(strtolower($p['title']), $searchLower) !== false || strpos(strtolower($p['excerpt']), $searchLower) !== false;
        });
    }
    $allFallbackPosts = array_values($allFallbackPosts);
    $total_posts = count($allFallbackPosts);
    $total_pages = max(1, (int) ceil($total_posts / $per_page));
    $offset = ($current_page - 1) * $per_page;
    $posts = array_slice($allFallbackPosts, $offset, $per_page);
} else {
    $total_pages = max(1, (int) ceil($total_posts / $per_page));
}
$pagination = paginate($total_posts, $per_page, $current_page);
$total_pages = $pagination['total_pages'] ?? 1;
?>
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Insights &amp; Resources</h1>
        <p>Expert perspectives on workforce management, HR technology, and digital solutions to help you make informed decisions for your organisation.</p>
    </div>
</section>

<!-- Breadcrumbs -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Blog</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Blog Content -->
<section class="section">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <form method="GET" action="/blog" class="d-flex">
                    <input type="search" name="search" class="form-control me-2" placeholder="Search articles..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" style="border-radius: var(--radius-full); padding: 10px 20px; border: 1px solid var(--soft-grey);">
                    <button type="submit" class="btn" style="background: var(--gradient); color: #fff; border-radius: var(--radius-full); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border: none; flex-shrink: 0;">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if ($category_filter): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                </form>
            </div>
            <div class="col-lg-6">
                <div class="d-flex flex-wrap gap-2">
                    <a href="/blog" class="btn <?php echo empty($category_filter) ? 'btn-teal-light' : 'btn-outline-secondary'; ?> btn-sm rounded-pill">All</a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="/blog?category=<?php echo htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8'); ?><?php echo $search ? '&search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') : ''; ?>" class="btn <?php echo ($category_filter === $cat['slug']) ? 'btn-teal-light' : 'btn-outline-secondary'; ?> btn-sm rounded-pill">
                        <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if (empty($posts)): ?>
        <div class="text-center py-5">
            <i class="bi bi-journal-text" style="font-size: 3rem; color: var(--soft-grey);"></i>
            <h3 class="mt-3">No articles found</h3>
            <p class="text-muted">Try adjusting your search or filter criteria.</p>
            <a href="/blog" class="btn-link-teal">View all articles <i class="bi bi-arrow-right"></i></a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($posts as $index => $post): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 100; ?>">
                <div class="blog-card">
                    <div class="blog-card-img">
                        <span class="category-badge"><?php echo htmlspecialchars($post['category_name'] ?? 'General', ENT_QUOTES, 'UTF-8'); ?></span>
                        <i class="bi bi-newspaper" style="font-size: 3rem; color: rgba(255,255,255,0.4); position: relative; z-index: 0;"></i>
                        <?php if (!empty($post['read_time'])): ?>
                        <span class="read-time"><?php echo htmlspecialchars($post['read_time'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="blog-card-body">
                        <span class="date"><i class="bi bi-calendar3"></i> <?php echo htmlspecialchars(format_date($post['published_at'] ?? '', 'd M Y'), ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3><a href="/blog/<?php echo htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></a></h3>
                        <p><?php echo htmlspecialchars($post['excerpt'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="author-row">
                            <div class="author-avatar"><i class="bi bi-person-fill"></i></div>
                            <span class="author-name"><?php echo htmlspecialchars($post['author_name'] ?? 'SwamiTime Team', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <nav class="mt-5" aria-label="Blog pagination">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="/blog?page=<?php echo $current_page - 1; ?><?php echo $search ? '&search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') : ''; ?><?php echo $category_filter ? '&category=' . htmlspecialchars($category_filter, ENT_QUOTES, 'UTF-8') : ''; ?>" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                    <a class="page-link" href="/blog?page=<?php echo $i; ?><?php echo $search ? '&search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') : ''; ?><?php echo $category_filter ? '&category=' . htmlspecialchars($category_filter, ENT_QUOTES, 'UTF-8') : ''; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="/blog?page=<?php echo $current_page + 1; ?><?php echo $search ? '&search=' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') : ''; ?><?php echo $category_filter ? '&category=' . htmlspecialchars($category_filter, ENT_QUOTES, 'UTF-8') : ''; ?>" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <style>
        .pagination .page-link { color: var(--primary); border-color: var(--soft-grey); border-radius: var(--radius-sm) !important; margin: 0 3px; }
        .pagination .page-item.active .page-link { background: var(--gradient); border-color: transparent; color: #fff; }
        .pagination .page-link:hover { background: var(--light-bg); color: var(--primary-dark); }
        .pagination .page-item.disabled .page-link { color: var(--text-light); }
        .btn-teal-light { background: var(--primary); color: #fff; border-color: var(--primary); }
        .btn-teal-light:hover { background: var(--primary-dark); color: #fff; }
        </style>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <h2>Want Expert Workforce Insights?</h2>
        <p>Subscribe to our newsletter for practical articles, industry updates, and insights delivered straight to your inbox.</p>
        <a href="/contact-us" class="btn-white">Get in Touch <i class="bi bi-arrow-right"></i></a>
        <div class="cta-trust">No spam &bull; Unsubscribe anytime &bull; Industry expertise</div>
    </div>
</section>
