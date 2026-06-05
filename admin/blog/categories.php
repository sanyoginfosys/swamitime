<?php
$pageTitle = 'Blog Categories';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save') {
        $catId = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
        $name = sanitize($_POST['name'] ?? '');
        $slug = sanitize($_POST['slug'] ?? '');
        if (empty($slug)) $slug = create_slug($name);
        $description = sanitize($_POST['description'] ?? '');

        if (empty(trim($name))) {
            set_flash('error', 'Category name is required.');
        } elseif ($catId) {
            $db->prepare("UPDATE blog_categories SET name=?, slug=?, description=? WHERE id=?")->execute([$name, $slug, $description, $catId]);
            set_flash('success', 'Category updated.');
        } else {
            $db->prepare("INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?)")->execute([$name, $slug, $description]);
            set_flash('success', 'Category created.');
        }
    } elseif ($action === 'delete') {
        $catId = (int)($_POST['cat_id'] ?? 0);
        $postCount = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE category_id = ?");
        $postCount->execute([$catId]);
        if ($postCount->fetchColumn() > 0) {
            set_flash('error', 'Cannot delete category with existing posts. Reassign posts first.');
        } else {
            $db->prepare("DELETE FROM blog_categories WHERE id = ?")->execute([$catId]);
            set_flash('success', 'Category deleted.');
        }
    }
    redirect(admin_url('blog-categories.php'));
}

$categories = $db->query("
    SELECT c.*, COUNT(p.id) as post_count
    FROM blog_categories c
    LEFT JOIN blog_posts p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.name
")->fetchAll();

$editCat = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM blog_categories WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editCat = $stmt->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-5">
        <div class="card form-card mb-4">
            <div class="card-header"><?php echo $editCat ? 'Edit Category' : 'Add Category'; ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="cat_id" value="<?php echo $editCat['id'] ?? 0; ?>">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($editCat['name'] ?? ''); ?>" onkeyup="document.getElementById('catSlug').value = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g,'').replace(/[\s-]+/g,'-').replace(/^-|-$/g,'');">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" id="catSlug" name="slug" class="form-control" value="<?php echo htmlspecialchars($editCat['slug'] ?? ''); ?>" placeholder="auto-generated">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($editCat['description'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-teal"><i class="bi bi-check-lg me-1"></i><?php echo $editCat ? 'Update' : 'Add'; ?></button>
                    <?php if ($editCat): ?>
                    <a href="categories.php" class="btn btn-outline-secondary ms-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-table">
            <div class="card-header"><h5>All Categories</h5></div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>ID</th><th>Name</th><th>Slug</th><th>Posts</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No categories found.</td></tr>
                        <?php else: foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                            <td><?php echo $cat['post_count']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $cat['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <?php if ($cat['post_count'] == 0): ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                    <?php echo Security::csrf_field(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="cat_id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Cannot delete - has posts"><i class="bi bi-trash"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
