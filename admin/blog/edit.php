<?php
if (!defined('ADMIN_BLOG_EDIT')) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/blog-post-edit.php' . $qs);
    exit;
}
$pageTitle = 'Add/Edit Blog Post';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = null;

if ($editId) {
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$editId]);
    $post = $stmt->fetch();
    if (!$post) { set_flash('error', 'Post not found.'); redirect(admin_url('blog/')); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();

    $title = sanitize($_POST['title'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');
    if (empty($slug)) $slug = create_slug($title);
    $categoryId = $_POST['category_id'] ? (int)$_POST['category_id'] : null;
    $tags = sanitize($_POST['tags'] ?? '');
    $excerpt = sanitize($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $metaTitle = sanitize($_POST['meta_title'] ?? '');
    $metaDescription = sanitize($_POST['meta_description'] ?? '');
    $author = sanitize($_POST['author'] ?? '');
    $status = sanitize($_POST['status'] ?? 'draft');
    $publishedAt = sanitize($_POST['published_at'] ?? '') ?: null;
    $featuredImage = $post['featured_image'] ?? null;

    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload = upload_file($_FILES['featured_image'], 'uploads/blog', ['jpg','jpeg','png','gif','webp'], MAX_UPLOAD_SIZE);
        if ($upload['success']) {
            $featuredImage = $upload['path'];
        }
    }

    $errors = [];
    if (empty(trim($title))) $errors[] = 'Title is required.';
    if (!in_array($status, ['published', 'draft'])) $errors[] = 'Invalid status.';

    if (empty($errors)) {
        if ($status === 'published' && !$publishedAt) $publishedAt = date('Y-m-d H:i:s');

        if ($editId) {
            $stmt = $db->prepare("
                UPDATE blog_posts SET
                    title=?, slug=?, content=?, excerpt=?, featured_image=?,
                    category_id=?, tags=?, meta_title=?, meta_description=?,
                    author=?, status=?, published_at=?, updated_at=NOW()
                WHERE id=?
            ");
            $stmt->execute([$title, $slug, $content, $excerpt, $featuredImage, $categoryId, $tags, $metaTitle, $metaDescription, $author, $status, $publishedAt, $editId]);
            set_flash('success', 'Post updated successfully.');
        } else {
            $stmt = $db->prepare("
                INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, category_id, tags, meta_title, meta_description, author, status, published_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $slug, $content, $excerpt, $featuredImage, $categoryId, $tags, $metaTitle, $metaDescription, $author, $status, $publishedAt]);
            $editId = $db->lastInsertId();
            set_flash('success', 'Post created successfully.');
        }
        redirect(admin_url('blog-post-edit.php?id=' . $editId));
    } else {
        set_flash('error', implode('<br>', $errors));
    }
}

$categories = $db->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();
$admins = $db->query("SELECT id, full_name FROM admins WHERE status='active' ORDER BY full_name")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-9">
        <div class="card form-card mb-4">
            <div class="card-header"><?php echo $editId ? 'Edit Post' : 'New Post'; ?></div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php echo Security::csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($_POST['title'] ?? $post['title'] ?? ''); ?>" onkeyup="document.getElementById('autoSlug').value = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g,'').replace(/[\s-]+/g,'-').replace(/^-|-$/g,'');">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Slug</label>
                            <input type="text" id="autoSlug" name="slug" class="form-control" value="<?php echo htmlspecialchars($_POST['slug'] ?? $post['slug'] ?? ''); ?>" placeholder="auto-generated">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Select category...</option>
                                <?php foreach ($categories as $cat): $sel = ($_POST['category_id'] ?? $post['category_id'] ?? '') == $cat['id']; ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $sel ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tags (comma-separated)</label>
                            <input type="text" name="tags" class="form-control" placeholder="e.g. UKG, workforce management" value="<?php echo htmlspecialchars($_POST['tags'] ?? $post['tags'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Excerpt</label>
                            <textarea name="excerpt" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['excerpt'] ?? $post['excerpt'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Content <small class="text-muted fw-normal">(HTML supported)</small></label>
                            <textarea name="content" class="form-control" rows="15"><?php echo htmlspecialchars($_POST['content'] ?? $post['content'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6 class="fw-bold">SEO Settings</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($_POST['meta_title'] ?? $post['meta_title'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['meta_description'] ?? $post['meta_description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-teal"><i class="bi bi-check-lg me-1"></i><?php echo $editId ? 'Update' : 'Publish'; ?></button>
                        <a href="<?php echo admin_url('blog-posts.php'); ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card form-card mb-4">
            <div class="card-header">Publishing</div>
            <div class="card-body">
                <form id="publishForm" method="POST" enctype="multipart/form-data" onsubmit="syncForm(this);">
                    <?php echo Security::csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?php echo ($_POST['status'] ?? $post['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo ($_POST['status'] ?? $post['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Published Date</label>
                        <input type="datetime-local" name="published_at" class="form-control" value="<?php $pv = $_POST['published_at'] ?? $post['published_at'] ?? ''; echo $pv ? date('Y-m-d\TH:i', strtotime($pv)) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <select name="author" class="form-select">
                            <option value="">Select author...</option>
                            <?php foreach ($admins as $a): ?>
                            <option value="<?php echo htmlspecialchars($a['full_name']); ?>" <?php echo ($_POST['author'] ?? $post['author'] ?? '') === $a['full_name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['full_name']); ?></option>
                            <?php endforeach; ?>
                            <option value="SWAMITIME SOLUTIONS LTD" <?php echo ($_POST['author'] ?? $post['author'] ?? 'SWAMITIME SOLUTIONS LTD') === 'SWAMITIME SOLUTIONS LTD' ? 'selected' : ''; ?>>SWAMITIME SOLUTIONS LTD</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="card form-card mb-4">
            <div class="card-header">Featured Image</div>
            <div class="card-body">
                <?php if (!empty($post['featured_image'])): ?>
                <img src="<?php echo BASE_URL . '/' . ltrim($post['featured_image'], '/'); ?>" class="img-fluid rounded mb-2" alt="Featured">
                <?php endif; ?>
                <input type="file" name="featured_image" class="form-control form-control-sm" form="publishForm">
            </div>
        </div>
    </div>
</div>
<script>
function syncForm(sidebarForm) {
    const mainForm = sidebarForm.closest('.row').querySelector('.col-lg-9 form');
    const sidebarFields = sidebarForm.querySelectorAll('input[name], select[name], textarea[name]');
    sidebarFields.forEach(field => {
        let mainField = mainForm.querySelector('[name="' + field.name + '"]');
        if (!mainField) {
            mainField = document.createElement('input');
            mainField.type = 'hidden';
            mainField.name = field.name;
            mainField.value = field.value;
            mainForm.appendChild(mainField);
        } else {
            mainField.value = field.value;
        }
    });
}
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
