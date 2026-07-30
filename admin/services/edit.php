<?php
if (!defined('ADMIN_SERVICES_EDIT')) {
    $qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/service-edit.php' . $qs);
    exit;
}
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$id         = (int) ($_GET['id'] ?? 0);
$isNew      = $id === 0;
$serviceData = [
    'id' => 0, 'title' => '', 'slug' => '', 'icon' => '', 'short_description' => '',
    'content' => '', 'meta_title' => '', 'meta_description' => '', 'meta_keywords' => '',
    'featured_image' => '', 'status' => 'active', 'sort_order' => 0,
];

if (!$isNew) {
    $stmt = $db->prepare('SELECT * FROM services WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        $serviceData = $row;
    } else {
        set_flash('error', 'Service not found.');
        redirect(admin_url('services.php'));
    }
}

$errors    = [];
$iconList  = [
    'fa-solid fa-headset', 'fa-solid fa-chart-line', 'fa-solid fa-cogs',
    'fa-solid fa-graduation-cap', 'fa-solid fa-shield-halved', 'fa-solid fa-chart-bar',
    'fa-solid fa-laptop-code', 'fa-solid fa-globe', 'fa-solid fa-magnifying-glass-chart',
    'fa-solid fa-building', 'fa-solid fa-store', 'fa-solid fa-hotel',
    'fa-solid fa-truck-fast', 'fa-solid fa-industry', 'fa-solid fa-hospital',
    'fa-solid fa-briefcase', 'fa-solid fa-brain', 'fa-solid fa-clock',
    'fa-solid fa-eye', 'fa-solid fa-sliders',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();

    $data = [
        'title'            => sanitize($_POST['title'] ?? ''),
        'slug'             => sanitize($_POST['slug'] ?? ''),
        'icon'             => sanitize($_POST['icon'] ?? ''),
        'short_description' => sanitize($_POST['short_description'] ?? ''),
        'content'          => $_POST['content'] ?? '',
        'meta_title'       => sanitize($_POST['meta_title'] ?? ''),
        'meta_description' => sanitize($_POST['meta_description'] ?? ''),
        'meta_keywords'    => sanitize($_POST['meta_keywords'] ?? ''),
        'status'           => $_POST['status'] === 'active' ? 'active' : 'inactive',
        'sort_order'       => (int) ($_POST['sort_order'] ?? 0),
    ];

    if ($data['title'] === '') {
        $errors[] = 'Title is required.';
    }
    if ($data['slug'] === '') {
        $data['slug'] = create_slug($data['title']);
    }

    $dupStmt = $db->prepare('SELECT id FROM services WHERE slug = ? AND id != ?');
    $dupStmt->execute([$data['slug'], $id]);
    if ($dupStmt->fetch()) {
        $errors[] = 'A service with this slug already exists.';
    }

    $featuredImage = $serviceData['featured_image'] ?? '';

    if (!empty($_FILES['featured_image']['name'])) {
        $upload = upload_file($_FILES['featured_image'], 'uploads/services', ['jpg', 'jpeg', 'png', 'gif', 'webp'], MAX_UPLOAD_SIZE);
        if ($upload['success']) {
            if ($featuredImage && $featuredImage !== $upload['path']) {
                delete_file($featuredImage);
            }
            $featuredImage = $upload['path'];
        } else {
            $errors[] = $upload['error'];
        }
    }

    if (empty($errors)) {
        if ($isNew) {
            $stmt = $db->prepare('INSERT INTO services (title, slug, icon, short_description, content, meta_title, meta_description, meta_keywords, featured_image, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$data['title'], $data['slug'], $data['icon'], $data['short_description'], $data['content'], $data['meta_title'], $data['meta_description'], $data['meta_keywords'], $featuredImage, $data['status'], $data['sort_order']]);
            $id = (int) $db->lastInsertId();
            set_flash('success', 'Service created successfully.');
        } else {
            $stmt = $db->prepare('UPDATE services SET title = ?, slug = ?, icon = ?, short_description = ?, content = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, featured_image = ?, status = ?, sort_order = ? WHERE id = ?');
            $stmt->execute([$data['title'], $data['slug'], $data['icon'], $data['short_description'], $data['content'], $data['meta_title'], $data['meta_description'], $data['meta_keywords'], $featuredImage, $data['status'], $data['sort_order'], $id]);
            set_flash('success', 'Service updated successfully.');
        }
        redirect(admin_url('service-edit.php?id=' . $id));
    }

    $serviceData = array_merge($serviceData, $data);
    $serviceData['featured_image'] = $featuredImage;
}
?>
<div class="admin-page-header">
  <h2><?php echo $isNew ? 'Add New Service' : 'Edit Service'; ?></h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?php echo admin_url('services/'); ?>">Services</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo $isNew ? 'New Service' : sanitize($serviceData['title']); ?></li>
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

<form method="post" enctype="multipart/form-data" novalidate id="serviceForm">
  <?php echo Security::csrf_field(); ?>

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Service Content</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo sanitize($serviceData['title']); ?>" required maxlength="255" oninput="document.getElementById('slug').value = slugify(this.value)">
          </div>
          <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" id="slug" name="slug" class="form-control" value="<?php echo sanitize($serviceData['slug']); ?>" maxlength="255">
          </div>
          <div class="mb-3">
            <label for="short_description" class="form-label">Short Description</label>
            <textarea id="short_description" name="short_description" class="form-control" rows="3" maxlength="500"><?php echo sanitize($serviceData['short_description'] ?? ''); ?></textarea>
            <div class="form-text">Brief summary shown in service listings.</div>
          </div>
          <div class="mb-3">
            <label for="content" class="form-label">Full Content</label>
            <textarea id="content" name="content" class="form-control" rows="20"><?php echo htmlspecialchars($serviceData['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">SEO Settings</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label for="meta_title" class="form-label">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" class="form-control" value="<?php echo sanitize($serviceData['meta_title'] ?? ''); ?>" maxlength="255">
          </div>
          <div class="mb-3">
            <label for="meta_description" class="form-label">Meta Description</label>
            <textarea id="meta_description" name="meta_description" class="form-control" rows="3" maxlength="320"><?php echo sanitize($serviceData['meta_description'] ?? ''); ?></textarea>
          </div>
          <div class="mb-3">
            <label for="meta_keywords" class="form-label">Meta Keywords</label>
            <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="<?php echo sanitize($serviceData['meta_keywords'] ?? ''); ?>">
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Service Settings</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label for="icon" class="form-label">Icon</label>
            <div class="input-group">
              <span class="input-group-text"><i id="iconPreview" class="<?php echo sanitize($serviceData['icon'] ?? ''); ?>"></i></span>
              <input type="text" id="icon" name="icon" class="form-control" value="<?php echo sanitize($serviceData['icon'] ?? ''); ?>" oninput="document.getElementById('iconPreview').className = this.value" placeholder="fa-solid fa-headset">
            </div>
            <div class="form-text mt-1">Enter a Font Awesome class or select below.</div>
            <div class="icon-grid mt-2" style="max-height:150px;overflow-y:auto;">
              <?php foreach ($iconList as $ic): ?>
                <button type="button" class="btn btn-sm btn-outline-secondary m-1 icon-select" data-icon="<?php echo $ic; ?>" title="<?php echo $ic; ?>">
                  <i class="<?php echo $ic; ?>"></i>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="featured_image" class="form-label">Featured Image</label>
            <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
            <?php if (!empty($serviceData['featured_image'])): ?>
              <div class="mt-2">
                <img src="<?php echo get_image_url($serviceData['featured_image']); ?>" class="img-thumbnail" style="max-height:120px;">
              </div>
            <?php endif; ?>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
              <option value="active" <?php echo $serviceData['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
              <option value="inactive" <?php echo $serviceData['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?php echo (int) $serviceData['sort_order']; ?>">
          </div>
        </div>
      </div>

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> <?php echo $isNew ? 'Create Service' : 'Update Service'; ?></button>
        <?php if (!$isNew): ?>
          <a href="<?php echo site_url('services/' . $serviceData['slug']); ?>" target="_blank" class="btn btn-outline-info"><i class="bi bi-eye"></i> Preview Service</a>
        <?php endif; ?>
        <a href="<?php echo admin_url('services/'); ?>" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </div>
  </div>
</form>

<script>
function slugify(text) {
  return text.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-').replace(/^-+|-+$/g, '');
}

document.querySelectorAll('.icon-select').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var icon = this.getAttribute('data-icon');
    document.getElementById('icon').value = icon;
    document.getElementById('iconPreview').className = icon;
  });
});

document.getElementById('serviceForm').addEventListener('submit', function (e) {
  var title = document.getElementById('title').value.trim();
  if (!title) {
    e.preventDefault();
    alert('Title is required.');
  }
});
</script>
