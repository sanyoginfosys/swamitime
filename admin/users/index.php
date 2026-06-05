<?php
$pageTitle = 'User Management';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireSuperAdmin();

$db = getDB();
$currentUserId = $_SESSION['admin_id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = sanitize($_POST['action'] ?? '');

    if ($action === 'save') {
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $fullName = sanitize($_POST['full_name'] ?? '');
        $role = sanitize($_POST['role'] ?? 'admin');
        $status = sanitize($_POST['status'] ?? 'active');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if (empty($username)) $errors[] = 'Username is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (!in_array($role, ['super_admin','admin','editor'])) $errors[] = 'Invalid role.';

        if (empty($errors)) {
            if ($userId) {
                if ($password) {
                    $hash = Auth::hashPassword($password);
                    $db->prepare("UPDATE admins SET username=?, email=?, full_name=?, role=?, status=?, password_hash=?, updated_at=NOW() WHERE id=?")
                        ->execute([$username, $email, $fullName, $role, $status, $hash, $userId]);
                } else {
                    $db->prepare("UPDATE admins SET username=?, email=?, full_name=?, role=?, status=?, updated_at=NOW() WHERE id=?")
                        ->execute([$username, $email, $fullName, $role, $status, $userId]);
                }
                set_flash('success', 'User updated.');
            } else {
                if (empty($password)) $errors[] = 'Password is required for new users.';
                if (empty($errors)) {
                    $hash = Auth::hashPassword($password);
                    $db->prepare("INSERT INTO admins (username, email, password_hash, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)")
                        ->execute([$username, $email, $hash, $fullName, $role, $status]);
                    set_flash('success', 'User created.');
                }
            }
        }
        if (!empty($errors)) set_flash('error', implode('<br>', $errors));
        redirect(admin_url('users.php'));
    }

    if ($action === 'toggle_status') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId != $currentUserId) {
            $stmt = $db->prepare("SELECT status FROM admins WHERE id=?");
            $stmt->execute([$userId]);
            $currentStatus = $stmt->fetchColumn();
            $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';
            $stmt2 = $db->prepare("UPDATE admins SET status=? WHERE id=?");
            $stmt2->execute([$newStatus, $userId]);
            set_flash('success', 'Status toggled.');
        } else {
            set_flash('error', 'Cannot change your own status.');
        }
        redirect(admin_url('users.php'));
    }
}

$users = $db->query("SELECT * FROM admins ORDER BY created_at DESC")->fetchAll();
$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-header">
  <h2>User Management</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Users</li>
    </ol>
  </nav>
  <a href="<?php echo admin_url('users.php'); ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add New User</a>
</div>

<?php foreach (get_flash() as $flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
    <?php echo sanitize($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endforeach; ?>

<div class="row">
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0"><?php echo $editUser ? 'Edit User' : 'Add User'; ?></h5></div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="user_id" value="<?php echo $editUser['id'] ?? 0; ?>">
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($editUser['username'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($editUser['full_name'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="admin" <?php echo ($editUser['role']??'')==='admin'?'selected':''; ?>>Admin</option>
                            <option value="editor" <?php echo ($editUser['role']??'')==='editor'?'selected':''; ?>>Editor</option>
                            <option value="super_admin" <?php echo ($editUser['role']??'')==='super_admin'?'selected':''; ?>>Super Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <?php echo $editUser ? '<small class="text-muted">(leave blank to keep current)</small>' : '<span class="text-danger">*</span>'; ?></label>
                        <input type="password" name="password" class="form-control" <?php echo $editUser ? '' : 'required'; ?>>
                    </div>
                    <?php if ($editUser): ?>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo $editUser['status']==='active'?'selected':''; ?>>Active</option>
                            <option value="inactive" <?php echo $editUser['status']==='inactive'?'selected':''; ?>>Inactive</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-teal"><i class="bi bi-check-lg me-1"></i><?php echo $editUser ? 'Update' : 'Create'; ?></button>
                    <?php if ($editUser): ?>
                    <a href="<?php echo admin_url('users.php'); ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr><th>Username</th><th>Email</th><th>Role</th><th>Last Login</th><th>Status</th><th class="text-end">Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No users found.</td></tr>
                            <?php else: foreach ($users as $u): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo $u['role']==='super_admin'?'bg-danger':($u['role']==='admin'?'bg-primary':'bg-secondary'); ?>">
                                        <?php echo str_replace('_',' ',ucfirst($u['role'])); ?>
                                    </span>
                                </td>
                                <td><?php echo $u['last_login'] ? format_date($u['last_login'], 'd M Y H:i') : 'Never'; ?></td>
                                <td>
                                    <span class="badge <?php echo $u['status']==='active'?'bg-success':'bg-secondary'; ?>">
                                        <?php echo ucfirst($u['status']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="?edit=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Toggle user status?');">
                                        <?php echo Security::csrf_field(); ?>
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-warning" <?php echo $u['id']==$currentUserId?'disabled':''; ?> title="<?php echo $u['id']==$currentUserId?'Cannot change own status':'Toggle status'; ?>">
                                            <i class="bi bi-power"></i>
                                        </button>
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
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
