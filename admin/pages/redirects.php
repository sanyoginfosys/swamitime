<?php
$pageTitle = 'Redirect Management';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save') {
        $redirectId = isset($_POST['redirect_id']) ? (int)$_POST['redirect_id'] : 0;
        $oldUrl = sanitize($_POST['old_url'] ?? '');
        $newUrl = sanitize($_POST['new_url'] ?? '');
        $statusCode = (int)($_POST['status_code'] ?? 301);

        $errors = [];
        if (empty(trim($oldUrl))) $errors[] = 'Old URL is required.';
        if (empty(trim($newUrl))) $errors[] = 'New URL is required.';
        if (!in_array($statusCode, [301, 302])) $errors[] = 'Status code must be 301 or 302.';

        if (empty($errors)) {
            if ($redirectId) {
                $db->prepare("UPDATE redirects SET old_url=?, new_url=?, status_code=? WHERE id=?")
                    ->execute([$oldUrl, $newUrl, $statusCode, $redirectId]);
                set_flash('success', 'Redirect updated.');
            } else {
                $db->prepare("INSERT INTO redirects (old_url, new_url, status_code) VALUES (?, ?, ?)")
                    ->execute([$oldUrl, $newUrl, $statusCode]);
                set_flash('success', 'Redirect created.');
            }
        } else {
            set_flash('error', implode('<br>', $errors));
        }
        redirect(admin_url('pages/redirects.php'));
    }

    if ($action === 'delete') {
        $redirectId = (int)($_POST['redirect_id'] ?? 0);
        $db->prepare("DELETE FROM redirects WHERE id=?")->execute([$redirectId]);
        set_flash('success', 'Redirect deleted.');
        redirect(admin_url('pages/redirects.php'));
    }
}

$redirects = $db->query("SELECT * FROM redirects ORDER BY created_at DESC")->fetchAll();

$editRedirect = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM redirects WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editRedirect = $stmt->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-5">
        <div class="card form-card mb-4">
            <div class="card-header"><?php echo $editRedirect ? 'Edit Redirect' : 'Add Redirect'; ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="redirect_id" value="<?php echo $editRedirect['id'] ?? 0; ?>">
                    <div class="mb-3">
                        <label class="form-label">Old URL <span class="text-danger">*</span></label>
                        <input type="text" name="old_url" class="form-control" required placeholder="/old-page" value="<?php echo htmlspecialchars($editRedirect['old_url'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New URL <span class="text-danger">*</span></label>
                        <input type="text" name="new_url" class="form-control" required placeholder="/new-page" value="<?php echo htmlspecialchars($editRedirect['new_url'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status Code</label>
                        <select name="status_code" class="form-select">
                            <option value="301" <?php echo ($editRedirect['status_code']??301)==301?'selected':''; ?>>301 - Permanent</option>
                            <option value="302" <?php echo ($editRedirect['status_code']??0)==302?'selected':''; ?>>302 - Temporary</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-teal"><i class="bi bi-check-lg me-1"></i><?php echo $editRedirect ? 'Update' : 'Add'; ?></button>
                    <?php if ($editRedirect): ?>
                    <a href="redirects.php" class="btn btn-outline-secondary ms-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-table">
            <div class="card-header"><h5>Redirects</h5></div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr><th>Old URL</th><th>New URL</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($redirects)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No redirects configured.</td></tr>
                        <?php else: foreach ($redirects as $r): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($r['old_url']); ?></code></td>
                            <td><code><?php echo htmlspecialchars($r['new_url']); ?></code></td>
                            <td><span class="badge <?php echo $r['status_code']==301?'bg-primary':'bg-warning text-dark'; ?>"><?php echo $r['status_code']; ?></span></td>
                            <td><small><?php echo format_date($r['created_at'], 'd M Y'); ?></small></td>
                            <td>
                                <a href="?edit=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this redirect?');">
                                    <?php echo Security::csrf_field(); ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="redirect_id" value="<?php echo $r['id']; ?>">
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
