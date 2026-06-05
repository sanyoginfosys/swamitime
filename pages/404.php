<?php
$slug = '404';
$pageData = null;
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $pageData = $stmt->fetch();
} catch (Exception $e) {
    $pageData = null;
}
$title = $pageData['title'] ?? 'Page Not Found';
?>
<!-- 404 Section -->
<section class="section" style="min-height: 70vh; display: flex; align-items: center; padding: 100px 0;">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-6">
                <div class="mb-4" style="font-family: var(--font-heading); font-size: 10rem; font-weight: 800; line-height: 1; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    404
                </div>
                <h2 class="mb-3">Page Not Found</h2>
                <p class="mb-4" style="color: var(--muted-text); font-size: 1.1rem; line-height: 1.7;">
                    The page you are looking for may have been moved, renamed, or is temporarily unavailable. Please check the URL for any errors, or use the navigation to find what you need.
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center mb-5">
                    <a href="/" class="btn" style="background: var(--gradient); color: #fff; border: none; padding: 14px 32px; border-radius: var(--radius-full); font-weight: 700; font-size: 1rem; display: inline-flex; align-items: center; gap: 8px; transition: var(--transition);">
                        <i class="bi bi-house-fill"></i> Return to Homepage
                    </a>
                    <a href="/contact-us" class="btn" style="background: transparent; color: var(--primary); border: 2px solid var(--primary); padding: 14px 32px; border-radius: var(--radius-full); font-weight: 600; font-size: 1rem; display: inline-flex; align-items: center; gap: 8px; transition: var(--transition);">
                        <i class="bi bi-envelope-fill"></i> Contact Us
                    </a>
                </div>
                <div class="mt-5 pt-4" style="border-top: 1px solid var(--soft-grey);">
                    <h5 class="mb-3">Search Our Website</h5>
                    <form action="/blog" method="GET" class="d-flex justify-content-center">
                        <div class="input-group" style="max-width: 450px;">
                            <input type="search" name="search" class="form-control" placeholder="Search articles and resources..." style="border-radius: var(--radius-full) 0 0 var(--radius-full); padding: 12px 20px; border: 1px solid var(--soft-grey);">
                            <button type="submit" class="btn" style="background: var(--gradient); color: #fff; border: none; border-radius: 0 var(--radius-full) var(--radius-full) 0; padding: 0 24px; display: flex; align-items: center; gap: 6px; font-weight: 600;">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
                <div class="mt-4">
                    <p style="font-size: 0.9rem; color: var(--text-light);">
                        Popular links:
                        <a href="/services" class="mx-2">Services</a> &bull;
                        <a href="/case-studies" class="mx-2">Case Studies</a> &bull;
                        <a href="/blog" class="mx-2">Blog</a> &bull;
                        <a href="/about-us" class="mx-2">About Us</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
