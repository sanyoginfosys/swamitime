<?php
if (!defined('ADMIN_MENU')) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/menu.php' . $qs);
    exit;
}
$pageTitle = 'Menu Management';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save') {
        $menuId = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
        $title = sanitize($_POST['title'] ?? '');
        $url = sanitize($_POST['url'] ?? '');
        $parentId = $_POST['parent_id'] ? (int)$_POST['parent_id'] : null;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $location = sanitize($_POST['location'] ?? 'header');
        $status = sanitize($_POST['status'] ?? 'active');

        if (empty(trim($title))) {
            set_flash('error', 'Title is required.');
        } elseif ($menuId) {
            $db->prepare("UPDATE menu_items SET title=?, url=?, parent_id=?, sort_order=?, location=?, status=? WHERE id=?")
                ->execute([$title, $url, $parentId, $sortOrder, $location, $status, $menuId]);
            set_flash('success', 'Menu item updated.');
        } else {
            $db->prepare("INSERT INTO menu_items (title, url, parent_id, sort_order, location, status) VALUES (?, ?, ?, ?, ?, ?)")
                ->execute([$title, $url, $parentId, $sortOrder, $location, $status]);
            set_flash('success', 'Menu item created.');
        }
        redirect(admin_url('menu.php'));
    }

    if ($action === 'delete') {
        $menuId = (int)($_POST['menu_id'] ?? 0);
        $db->prepare("UPDATE menu_items SET parent_id=NULL WHERE parent_id=?")->execute([$menuId]);
        $db->prepare("DELETE FROM menu_items WHERE id=?")->execute([$menuId]);
        set_flash('success', 'Menu item deleted.');
        redirect(admin_url('menu.php'));
    }
}

$menuItems = $db->query("
    SELECT m.*, p.title as parent_title
    FROM menu_items m
    LEFT JOIN menu_items p ON m.parent_id = p.id
    ORDER BY m.location, m.sort_order
")->fetchAll();

$parents = $db->query("SELECT id, title FROM menu_items WHERE parent_id IS NULL ORDER BY location, sort_order")->fetchAll();

$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-5">
        <div class="card form-card mb-4">
            <div class="card-header"><?php echo $editItem ? 'Edit Menu Item' : 'Add Menu Item'; ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="menu_id" value="<?php echo $editItem['id'] ?? 0; ?>">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($editItem['title'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="text" name="url" class="form-control" value="<?php echo htmlspecialchars($editItem['url'] ?? ''); ?>" placeholder="/page-slug or https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent</label>
                        <select name="parent_id" class="form-select">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($parents as $p): if ($editItem && $p['id'] == $editItem['id']) continue; ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo ($editItem['parent_id']??'')==$p['id']?'selected':''; ?>><?php echo htmlspecialchars($p['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $editItem['sort_order'] ?? 0; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Location</label>
                            <select name="location" class="form-select">
                                <option value="header" <?php echo ($editItem['location']??'header')==='header'?'selected':''; ?>>Header</option>
                                <option value="footer" <?php echo ($editItem['location']??'')==='footer'?'selected':''; ?>>Footer</option>
                                <option value="both" <?php echo ($editItem['location']??'')==='both'?'selected':''; ?>>Both</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo ($editItem['status']??'active')==='active'?'selected':''; ?>>Active</option>
                                <option value="inactive" <?php echo ($editItem['status']??'')==='inactive'?'selected':''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-teal mt-3"><i class="bi bi-check-lg me-1"></i><?php echo $editItem ? 'Update' : 'Add'; ?></button>
                    <?php if ($editItem): ?>
                    <a href="<?php echo admin_url('menu.php'); ?>" class="btn btn-outline-secondary mt-3 ms-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-table">
            <div class="card-header"><h5>Menu Items</h5></div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Title</th><th>URL</th><th>Location</th><th>Parent</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($menuItems)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No menu items found.</td></tr>
                        <?php else: foreach ($menuItems as $mi): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo $mi['parent_title'] ? '— ' : ''; ?><?php echo htmlspecialchars($mi['title']); ?></td>
                            <td><code><?php echo htmlspecialchars($mi['url']); ?></code></td>
                            <td><span class="badge bg-secondary"><?php echo ucfirst($mi['location']); ?></span></td>
                            <td><small><?php echo htmlspecialchars($mi['parent_title'] ?? '—'); ?></small></td>
                            <td><?php echo $mi['sort_order']; ?></td>
                            <td><span class="badge <?php echo $mi['status']==='active'?'bg-success':'bg-secondary'; ?>"><?php echo ucfirst($mi['status']); ?></span></td>
                            <td>
                                <a href="?edit=<?php echo $mi['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this menu item?');">
                                    <?php echo Security::csrf_field(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="menu_id" value="<?php echo $mi['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
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
