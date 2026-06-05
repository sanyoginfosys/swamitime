<?php
// Ensure we're loaded through the admin wrapper (page-edit.php)
if (!defined('ADMIN_PAGES_EDIT')) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/page-edit.php' . $qs);
    exit;
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$id      = (int) ($_GET['id'] ?? 0);
$isNew   = $id === 0;
$pageData = [
    'id' => 0, 'title' => '', 'slug' => '', 'meta_title' => '', 'meta_description' => '',
    'meta_keywords' => '', 'content' => '', 'status' => 'draft', 'template' => 'default',
    'parent_id' => null, 'sort_order' => 0,
];

if (!$isNew) {
    $stmt = $db->prepare('SELECT * FROM pages WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        $pageData = $row;
    } else {
        set_flash('error', 'Page not found.');
        redirect(admin_url('pages/'));
    }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();

    $data = [
        'title'            => sanitize($_POST['title'] ?? ''),
        'slug'             => sanitize($_POST['slug'] ?? ''),
        'meta_title'       => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? ''),
        'meta_keywords'    => sanitize($_POST['meta_keywords'] ?? ''),
        'content'          => $_POST['content'] ?? '',
        'status'           => $_POST['status'] === 'published' ? 'published' : 'draft',
        'template'         => sanitize($_POST['template'] ?? 'default'),
        'parent_id'        => $_POST['parent_id'] !== '' ? (int) $_POST['parent_id'] : null,
        'sort_order'       => (int) ($_POST['sort_order'] ?? 0),
    ];

    if ($data['title'] === '') {
        $errors[] = 'Title is required.';
    }
    if ($data['slug'] === '') {
        $data['slug'] = create_slug($data['title']);
    }

    $dupStmt = $db->prepare('SELECT id FROM pages WHERE slug = ? AND id != ?');
    $dupStmt->execute([$data['slug'], $id]);
    if ($dupStmt->fetch()) {
        $errors[] = 'A page with this slug already exists.';
    }

    if (empty($errors)) {
        if ($isNew) {
            $stmt = $db->prepare('INSERT INTO pages (title, slug, meta_title, meta_description, meta_keywords, content, status, template, parent_id, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$data['title'], $data['slug'], $data['meta_title'], $data['meta_description'], $data['meta_keywords'], $data['content'], $data['status'], $data['template'], $data['parent_id'], $data['sort_order']]);
            $id = (int) $db->lastInsertId();
            set_flash('success', 'Page created successfully.');
        } else {
            $stmt = $db->prepare('UPDATE pages SET title = ?, slug = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, content = ?, status = ?, template = ?, parent_id = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([$data['title'], $data['slug'], $data['meta_title'], $data['meta_description'], $data['meta_keywords'], $data['content'], $data['status'], $data['template'], $data['parent_id'], $data['sort_order'], $id]);
            set_flash('success', 'Page updated successfully.');
        }
        redirect(admin_url('page-edit.php?id=' . $id));
    }

    $pageData = array_merge($pageData, $data);
}

$templates = ['default', 'home', 'full-width', 'sidebar', 'landing'];
$allPages  = $db->query('SELECT id, title FROM pages ORDER BY sort_order ASC')->fetchAll();
?>
<div class="admin-page-header">
  <h2><?php echo $isNew ? 'Add New Page' : 'Edit Page'; ?></h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?php echo admin_url('pages/'); ?>">Pages</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo $isNew ? 'New Page' : sanitize($pageData['title']); ?></li>
    </ol>
  </nav>
</div>

<?php foreach (get_flash() as $flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
    <?php echo sanitize($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endforeach; ?>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?php echo sanitize($e); ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" novalidate id="pageForm">
  <?php echo Security::csrf_field(); ?>

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Page Content</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo sanitize($pageData['title']); ?>" required maxlength="255" oninput="document.getElementById('slug').value = slugify(this.value)">
          </div>
          <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" class="form-control" value="<?php echo sanitize($pageData['slug']); ?>" maxlength="255">
            <div class="form-text">Auto-generated from title. Can be customised.</div>
          </div>
          <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea id="content" name="content" class="form-control" rows="20"><?php echo htmlspecialchars($pageData['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">SEO Settings</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" class="form-control" value="<?php echo sanitize($pageData['meta_title'] ?? ''); ?>" maxlength="255">
          </div>
          <div class="mb-3">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea id="meta_description" name="meta_description" class="form-control" rows="3" maxlength="320"><?php echo sanitize($pageData['meta_description'] ?? ''); ?></textarea>
          </div>
          <div class="mb-3">
            <label for="meta_keywords" class="form-label">Meta Keywords</label>
            <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="<?php echo sanitize($pageData['meta_keywords'] ?? ''); ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Publish Settings</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
              <option value="draft" <?php echo $pageData['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
              <option value="published" <?php echo $pageData['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="template" class="form-label">Template</label>
            <select id="template" name="template" class="form-select">
              <?php foreach ($templates as $tpl): ?>
                <option value="<?php echo $tpl; ?>" <?php echo $pageData['template'] === $tpl ? 'selected' : ''; ?>><?php echo ucfirst($tpl); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="parent_id" class="form-label">Parent Page</label>
            <select id="parent_id" name="parent_id" class="form-select">
              <option value="">None (Top Level)</option>
              <?php foreach ($allPages as $ap): ?>
                <?php if ((int) $ap['id'] !== $id): ?>
                  <option value="<?php echo (int) $ap['id']; ?>" <?php echo (int) $pageData['parent_id'] === (int) $ap['id'] ? 'selected' : ''; ?>><?php echo sanitize($ap['title']); ?></option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?php echo (int) $pageData['sort_order']; ?>">
          </div>
        </div>
      </div>

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> <?php echo $isNew ? 'Create Page' : 'Update Page'; ?></button>
        <?php if (!$isNew): ?>
          <a href="<?php echo site_url($pageData['slug']); ?>" target="_blank" class="btn btn-outline-info"><i class="bi bi-eye"></i> Preview Page</a>
        <?php endif; ?>
        <a href="<?php echo admin_url('pages/'); ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>

<script>
function slugify(text) {
  return text.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-').replace(/^-+|-+$/g, '');
}

document.getElementById('pageForm').addEventListener('submit', function (e) {
  var title = document.getElementById('title').value.trim();
  if (!title) {
    e.preventDefault();
    alert('Title is required.');
    return false;
  }
});
</script>
