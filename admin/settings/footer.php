<?php
if (!defined('ADMIN_FOOTER_SETTINGS')) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/footer-settings.php' . $qs);
    exit;
}
$pageTitle = 'Footer Links';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save') {
        $linkId = isset($_POST['link_id']) ? (int)$_POST['link_id'] : 0;
        $title = sanitize($_POST['title'] ?? '');
        $url = sanitize($_POST['url'] ?? '');
        $section = sanitize($_POST['section'] ?? 'quick_links');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $status = sanitize($_POST['status'] ?? 'active');

        if (empty(trim($title))) {
            set_flash('error', 'Title is required.');
        } elseif ($linkId) {
            $db->prepare("UPDATE footer_links SET title=?, url=?, section=?, sort_order=?, status=? WHERE id=?")
                ->execute([$title, $url, $section, $sortOrder, $status, $linkId]);
            set_flash('success', 'Footer link updated.');
        } else {
            $db->prepare("INSERT INTO footer_links (title, url, section, sort_order, status) VALUES (?, ?, ?, ?, ?)")
                ->execute([$title, $url, $section, $sortOrder, $status]);
            set_flash('success', 'Footer link created.');
        }
        redirect(admin_url('footer-settings.php'));
    }

    if ($action === 'delete') {
        $linkId = (int)($_POST['link_id'] ?? 0);
        $db->prepare("DELETE FROM footer_links WHERE id=?")->execute([$linkId]);
        set_flash('success', 'Footer link deleted.');
        redirect(admin_url('footer-settings.php'));
    }
}

$sections = ['quick_links' => 'Quick Links', 'services' => 'Services', 'legal' => 'Legal'];

$links = $db->query("SELECT * FROM footer_links ORDER BY section, sort_order")->fetchAll();

$editLink = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM footer_links WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editLink = $stmt->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-5">
        <div class="card form-card mb-4">
            <div class="card-header"><?php echo $editLink ? 'Edit Footer Link' : 'Add Footer Link'; ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="link_id" value="<?php echo $editLink['id'] ?? 0; ?>">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($editLink['title'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="text" name="url" class="form-control" value="<?php echo htmlspecialchars($editLink['url'] ?? ''); ?>" placeholder="/page-slug">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Section</label>
                        <select name="section" class="form-select">
                            <?php foreach ($sections as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($editLink['section']??'quick_links')===$key?'selected':''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $editLink['sort_order'] ?? 0; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo ($editLink['status']??'active')==='active'?'selected':''; ?>>Active</option>
                                <option value="inactive" <?php echo ($editLink['status']??'')==='inactive'?'selected':''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-teal mt-3"><i class="bi bi-check-lg me-1"></i><?php echo $editLink ? 'Update' : 'Add'; ?></button>
                    <?php if ($editLink): ?>
                    <a href="<?php echo admin_url('footer-settings.php'); ?>" class="btn btn-outline-secondary mt-3 ms-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <?php foreach ($sections as $secKey => $secLabel):
            $secLinks = array_filter($links, fn($l) => $l['section'] === $secKey);
        ?>
        <div class="card card-table mb-4">
            <div class="card-header"><h5><?php echo $secLabel; ?></h5></div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Title</th><th>URL</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($secLinks)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No links in this section.</td></tr>
                        <?php else: foreach ($secLinks as $fl): ?>
                        <tr>
                            <td class="fw-semibold"><?php echo htmlspecialchars($fl['title']); ?></td>
                            <td><code><?php echo htmlspecialchars($fl['url']); ?></code></td>
                            <td><?php echo $fl['sort_order']; ?></td>
                            <td><span class="badge <?php echo $fl['status']==='active'?'bg-success':'bg-secondary'; ?>"><?php echo ucfirst($fl['status']); ?></span></td>
                            <td>
                                <a href="?edit=<?php echo $fl['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this link?');">
                                    <?php echo Security::csrf_field(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="link_id" value="<?php echo $fl['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
