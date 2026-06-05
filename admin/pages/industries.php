<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$errors    = [];
$editData  = null;
$editId    = (int) ($_GET['edit'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id  = (int) ($_POST['id'] ?? 0);
        $data = [
            'title'             => sanitize($_POST['title'] ?? ''),
            'slug'              => sanitize($_POST['slug'] ?? ''),
            'icon'              => sanitize($_POST['icon'] ?? ''),
            'short_description' => sanitize($_POST['short_description'] ?? ''),
            'content'           => $_POST['content'] ?? '',
            'challenges'        => $_POST['challenges'] ?? '',
            'solutions'         => $_POST['solutions'] ?? '',
            'meta_title'        => sanitize($_POST['meta_title'] ?? ''),
            'meta_description'  => sanitize($_POST['meta_description'] ?? ''),
            'status'            => $_POST['status'] === 'active' ? 'active' : 'inactive',
            'sort_order'        => (int) ($_POST['sort_order'] ?? 0),
        ];

        if ($data['title'] === '') {
            $errors[] = 'Title is required.';
        }
        if ($data['slug'] === '') {
            $data['slug'] = create_slug($data['title']);
        }

        $dupStmt = $db->prepare('SELECT id FROM industries WHERE slug = ? AND id != ?');
        $dupStmt->execute([$data['slug'], $id]);
        if ($dupStmt->fetch()) {
            $errors[] = 'An industry with this slug already exists.';
        }

        if (empty($errors)) {
            if ($id === 0) {
                $stmt = $db->prepare('INSERT INTO industries (title, slug, icon, short_description, content, challenges, solutions, meta_title, meta_description, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$data['title'], $data['slug'], $data['icon'], $data['short_description'], $data['content'], $data['challenges'], $data['solutions'], $data['meta_title'], $data['meta_description'], $data['status'], $data['sort_order']]);
                set_flash('success', 'Industry created successfully.');
            } else {
                $stmt = $db->prepare('UPDATE industries SET title = ?, slug = ?, icon = ?, short_description = ?, content = ?, challenges = ?, solutions = ?, meta_title = ?, meta_description = ?, status = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([$data['title'], $data['slug'], $data['icon'], $data['short_description'], $data['content'], $data['challenges'], $data['solutions'], $data['meta_title'], $data['meta_description'], $data['status'], $data['sort_order'], $id]);
                set_flash('success', 'Industry updated successfully.');
            }
            redirect(admin_url('industries.php'));
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM industries WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'Industry deleted.');
        redirect(admin_url('industries.php'));
    }
}

$industries = $db->query('SELECT * FROM industries ORDER BY sort_order ASC')->fetchAll();

if ($editId > 0) {
    $stmt = $db->prepare('SELECT * FROM industries WHERE id = ?');
    $stmt->execute([$editId]);
    $editData = $stmt->fetch();
}

$iconList = [
    'fa-solid fa-store', 'fa-solid fa-hotel', 'fa-solid fa-truck-fast',
    'fa-solid fa-industry', 'fa-solid fa-hospital', 'fa-solid fa-briefcase',
    'fa-solid fa-building', 'fa-solid fa-chart-line', 'fa-solid fa-headset',
    'fa-solid fa-globe', 'fa-solid fa-shield-halved',
];
?>
<div class="admin-page-header">
  <h2>Industries</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Industries</li>
    </ol>
  </nav>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#industryModal" onclick="resetForm()">
    <i class="bi bi-plus-lg"></i> Add New Industry
  </button>
</div>

<?php foreach (get_flash() as $flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
    <?php echo sanitize($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endforeach; ?>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:60px">ID</th>
            <th>Title</th>
            <th style="width:60px">Icon</th>
            <th>Status</th>
            <th style="width:80px">Order</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($industries)): ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">No industries found.</td></tr>
          <?php endif; ?>
          <?php foreach ($industries as $ind): ?>
            <tr>
              <td><?php echo (int) $ind['id']; ?></td>
              <td><strong><?php echo sanitize($ind['title']); ?></strong></td>
              <td><i class="<?php echo sanitize($ind['icon'] ?? ''); ?> fa-lg"></i></td>
              <td>
                <span class="badge <?php echo $ind['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                  <?php echo $ind['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                </span>
              </td>
              <td><?php echo (int) $ind['sort_order']; ?></td>
              <td class="text-end">
                <a href="?edit=<?php echo (int) $ind['id']; ?>" class="btn btn-sm btn-outline-primary" onclick="loadEdit(<?php echo (int) $ind['id']; ?>)" data-bs-toggle="modal" data-bs-target="#industryModal"><i class="bi bi-pencil"></i></a>
                <a href="<?php echo site_url('industries/' . $ind['slug']); ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-box-arrow-up-right"></i></a>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo (int) $ind['id']; ?>" data-title="<?php echo sanitize($ind['title']); ?>"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Industry Edit Modal -->
<div class="modal fade" id="industryModal" tabindex="-1" aria-labelledby="industryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form method="post" novalidate id="industryForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="indId" value="0">
        <div class="modal-header">
          <h5 class="modal-title" id="industryModalLabel">Add / Edit Industry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="indTitle" class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" id="indTitle" name="title" class="form-control" required maxlength="255" oninput="document.getElementById('indSlug').value = slugify(this.value)">
            </div>
            <div class="col-md-6">
              <label for="indSlug" class="form-label">Slug</label>
              <input type="text" id="indSlug" name="slug" class="form-control" maxlength="255">
            </div>
            <div class="col-md-6">
              <label for="indIcon" class="form-label">Icon</label>
              <div class="input-group">
                <span class="input-group-text"><i id="indIconPreview" class=""></i></span>
                <input type="text" id="indIcon" name="icon" class="form-control" oninput="document.getElementById('indIconPreview').className = this.value" placeholder="fa-solid fa-store">
              </div>
              <div class="icon-grid mt-2" style="max-height:120px;overflow-y:auto;">
                <?php foreach ($iconList as $ic): ?>
                  <button type="button" class="btn btn-sm btn-outline-secondary m-1 icon-pick" data-icon="<?php echo $ic; ?>" data-target="indIcon" data-preview="indIconPreview">
                    <i class="<?php echo $ic; ?>"></i>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="col-md-6">
              <label for="indShortDesc" class="form-label">Short Description</label>
              <textarea id="indShortDesc" name="short_description" class="form-control" rows="2" maxlength="500"></textarea>
            </div>
            <div class="col-12">
              <label for="indContent" class="form-label">Content</label>
              <textarea id="indContent" name="content" class="form-control" rows="4"></textarea>
            </div>
            <div class="col-md-6">
              <label for="indChallenges" class="form-label">Challenges</label>
              <textarea id="indChallenges" name="challenges" class="form-control" rows="4"></textarea>
            </div>
            <div class="col-md-6">
              <label for="indSolutions" class="form-label">Solutions</label>
              <textarea id="indSolutions" name="solutions" class="form-control" rows="4"></textarea>
            </div>
            <div class="col-md-6">
              <label for="indMetaTitle" class="form-label">Meta Title</label>
              <input type="text" id="indMetaTitle" name="meta_title" class="form-control" maxlength="255">
            </div>
            <div class="col-md-6">
              <label for="indMetaDesc" class="form-label">Meta Description</label>
              <input type="text" id="indMetaDesc" name="meta_description" class="form-control" maxlength="320">
            </div>
            <div class="col-md-6">
              <label for="indStatus" class="form-label">Status</label>
              <select id="indStatus" name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="indSortOrder" class="form-label">Sort Order</label>
              <input type="number" id="indSortOrder" name="sort_order" class="form-control" value="0">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong id="deleteTitle"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
var industryData = <?php echo json_encode($industries); ?>;

function slugify(text) {
  return text.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-').replace(/^-+|-+$/g, '');
}

function resetForm() {
  document.getElementById('indId').value = '0';
  document.getElementById('indTitle').value = '';
  document.getElementById('indSlug').value = '';
  document.getElementById('indIcon').value = '';
  document.getElementById('indIconPreview').className = '';
  document.getElementById('indShortDesc').value = '';
  document.getElementById('indContent').value = '';
  document.getElementById('indChallenges').value = '';
  document.getElementById('indSolutions').value = '';
  document.getElementById('indMetaTitle').value = '';
  document.getElementById('indMetaDesc').value = '';
  document.getElementById('indStatus').value = 'active';
  document.getElementById('indSortOrder').value = '0';
  document.getElementById('industryModalLabel').textContent = 'Add New Industry';
}

function loadEdit(id) {
  var item = industryData.find(function(i) { return parseInt(i.id) === id; });
  if (!item) return;
  document.getElementById('indId').value = item.id;
  document.getElementById('indTitle').value = item.title;
  document.getElementById('indSlug').value = item.slug;
  document.getElementById('indIcon').value = item.icon || '';
  document.getElementById('indIconPreview').className = item.icon || '';
  document.getElementById('indShortDesc').value = item.short_description || '';
  document.getElementById('indContent').value = item.content || '';
  document.getElementById('indChallenges').value = item.challenges || '';
  document.getElementById('indSolutions').value = item.solutions || '';
  document.getElementById('indMetaTitle').value = item.meta_title || '';
  document.getElementById('indMetaDesc').value = item.meta_description || '';
  document.getElementById('indStatus').value = item.status;
  document.getElementById('indSortOrder').value = item.sort_order;
  document.getElementById('industryModalLabel').textContent = 'Edit: ' + item.title;
}

document.querySelectorAll('.icon-pick').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var icon = this.getAttribute('data-icon');
    var target = this.getAttribute('data-target');
    var preview = this.getAttribute('data-preview');
    document.getElementById(target).value = icon;
    document.getElementById(preview).className = icon;
  });
});

document.addEventListener('DOMContentLoaded', function () {
  var deleteModal = document.getElementById('deleteModal');
  if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('deleteId').value = button.getAttribute('data-id');
      document.getElementById('deleteTitle').textContent = button.getAttribute('data-title');
    });
  }

  <?php if ($editId > 0 && $editData): ?>
    var modal = new bootstrap.Modal(document.getElementById('industryModal'));
    modal.show();
    loadEdit(<?php echo $editId; ?>);
  <?php endif; ?>
});
</script>
