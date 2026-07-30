<?php
$pageTitle = 'Blog Posts';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();

$perPage = 20;
$page = max(1, (int)($_GET['page'] ?? 1));

$categoryFilter = sanitize($_GET['category'] ?? '');
$statusFilter = sanitize($_GET['status'] ?? '');
$search = sanitize($_GET['search'] ?? '');

$where = [];
$params = [];

if ($categoryFilter) { $where[] = 'p.category_id = ?'; $params[] = $categoryFilter; }
if ($statusFilter) { $where[] = 'p.status = ?'; $params[] = $statusFilter; }
if ($search) { $where[] = 'p.title LIKE ?'; $params[] = "%$search%"; }

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    Security::validate_csrf();
    $postId = (int)($_POST['post_id'] ?? 0);
    $newStatus = sanitize($_POST['new_status'] ?? '');
    if ($postId && in_array($newStatus, ['published', 'draft'])) {
        $db->prepare("UPDATE blog_posts SET status = ?, published_at = CASE WHEN ?='published' AND published_at IS NULL THEN NOW() ELSE published_at END WHERE id = ?")->execute([$newStatus, $newStatus, $postId]);
        set_flash('success', 'Post status updated.');
    }
    redirect(admin_url('blog-posts.php'));
}

$countStmt = $db->prepare("SELECT COUNT(*) FROM blog_posts p $whereClause");
$countStmt->execute($params);
$totalPosts = $countStmt->fetchColumn();
$pagination = paginate($totalPosts, $perPage, $page);

$stmt = $db->prepare("
    SELECT p.*, c.name as category_name
    FROM blog_posts p
    LEFT JOIN blog_categories c ON p.category_id = c.id
    $whereClause
    ORDER BY p.created_at DESC
    LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}
");
$stmt->execute($params);
$posts = $stmt->fetchAll();

$categories = $db->query("SELECT id, name FROM blog_categories ORDER BY name")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="card card-table mb-4">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5>Blog Posts</h5>
        <a href="<?php echo admin_url('blog-post-edit.php'); ?>" class="btn btn-sm btn-teal"><i class="bi bi-plus-lg me-1"></i>Add New Post</a>
    </div>
    <div class="card-body border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-lg-4 col-md-6">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by title..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label mb-1">Category</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $categoryFilter == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>
            <div class="col-lg-3 col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-sm btn-teal"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="?" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($posts)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No posts found.</td></tr>
                <?php else: foreach ($posts as $post): ?>
                <tr>
                    <td>
                        <a href="<?php echo admin_url('blog-post-edit.php?id=' . $post['id']); ?>" class="fw-semibold text-decoration-none"><?php echo htmlspecialchars($post['title']); ?></a>
                    </td>
                    <td><?php echo htmlspecialchars($post['category_name'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($post['author'] ?? '—'); ?></td>
                    <td>
                        <form method="POST" class="d-inline toggle-status-form">
                            <?php echo Security::csrf_field(); ?>
                            <input type="hidden" name="action" value="toggle_status">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <input type="hidden" name="new_status" value="<?php echo $post['status'] === 'published' ? 'draft' : 'published'; ?>">
                            <button type="submit" class="badge badge-status badge-<?php echo $post['status']; ?> border-0" style="cursor:pointer;" title="Toggle status">
                                <?php echo ucfirst($post['status']); ?>
                            </button>
                        </form>
                    </td>
                    <td><?php echo $post['published_at'] ? format_date($post['published_at'], 'd M Y') : '—'; ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/blog/<?php echo htmlspecialchars($post['slug']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                        <a href="<?php echo admin_url('blog-post-edit.php?id=' . $post['id']); ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="if(confirm('Delete this post?')){location.href='?delete=<?php echo $post['id']; ?>&csrf=<?php echo $_SESSION['csrf_token']??''; ?>'}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pagination['total_pages'] > 1):
        $queryStr = http_build_query(array_filter(['category'=>$categoryFilter,'status'=>$statusFilter,'search'=>$search]));
    ?>
    <div class="card-footer">
        <nav>
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <li class="page-item <?php echo !$pagination['has_previous'] ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?<?php echo $queryStr; ?>&page=<?php echo $page-1; ?>">&laquo;</a>
                </li>
                <?php for ($i=1; $i<=$pagination['total_pages']; $i++): ?>
                <li class="page-item <?php echo $i===$page?'active':''; ?>">
                    <a class="page-link" href="?<?php echo $queryStr; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?php echo !$pagination['has_next']?'disabled':''; ?>">
                    <a class="page-link" href="?<?php echo $queryStr; ?>&page=<?php echo $page+1; ?>">&raquo;</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php
if (isset($_GET['delete']) && isset($_GET['csrf'])) {
    if (hash_equals($_SESSION['csrf_token'] ?? '', $_GET['csrf'] ?? '')) {
        $delId = (int)$_GET['delete'];
        $db->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$delId]);
        set_flash('success', 'Post deleted.');
        redirect(admin_url('blog-posts.php'));
    }
}
require_once __DIR__ . '/../includes/footer.php'; ?>
