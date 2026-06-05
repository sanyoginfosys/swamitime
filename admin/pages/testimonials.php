<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$errors   = [];
$editData = null;
$editId   = (int) ($_GET['edit'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id   = (int) ($_POST['id'] ?? 0);
        $data = [
            'name'       => sanitize($_POST['name'] ?? ''),
            'company'    => sanitize($_POST['company'] ?? ''),
            'role'       => sanitize($_POST['role'] ?? ''),
            'content'    => $_POST['content'] ?? '',
            'rating'     => max(1, min(5, (int) ($_POST['rating'] ?? 5))),
            'status'     => $_POST['status'] === 'active' ? 'active' : 'inactive',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];

        if ($data['name'] === '') {
            $errors[] = 'Name is required.';
        }
        if ($data['content'] === '') {
            $errors[] = 'Content is required.';
        }

        $avatarPath = '';
        if ($id > 0) {
            $stmt = $db->prepare('SELECT avatar FROM testimonials WHERE id = ?');
            $stmt->execute([$id]);
            $avatarPath = $stmt->fetchColumn() ?: '';
        }

        if (!empty($_FILES['avatar']['name'])) {
            $upload = upload_file($_FILES['avatar'], 'uploads/testimonials', ['jpg', 'jpeg', 'png', 'gif', 'webp'], MAX_UPLOAD_SIZE);
            if ($upload['success']) {
                if ($avatarPath && $avatarPath !== $upload['path']) {
                    delete_file($avatarPath);
                }
                $avatarPath = $upload['path'];
            } else {
                $errors[] = $upload['error'];
            }
        }

        if (empty($errors)) {
            if ($id === 0) {
                $stmt = $db->prepare('INSERT INTO testimonials (name, company, role, content, rating, avatar, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$data['name'], $data['company'], $data['role'], $data['content'], $data['rating'], $avatarPath, $data['status'], $data['sort_order']]);
                set_flash('success', 'Testimonial created successfully.');
            } else {
                $stmt = $db->prepare('UPDATE testimonials SET name = ?, company = ?, role = ?, content = ?, rating = ?, avatar = ?, status = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([$data['name'], $data['company'], $data['role'], $data['content'], $data['rating'], $avatarPath, $data['status'], $data['sort_order'], $id]);
                set_flash('success', 'Testimonial updated successfully.');
            }
            redirect(admin_url('testimonials.php'));
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT avatar FROM testimonials WHERE id = ?');
        $stmt->execute([$id]);
        $avatar = $stmt->fetchColumn();
        if ($avatar) {
            delete_file($avatar);
        }
        $stmt = $db->prepare('DELETE FROM testimonials WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'Testimonial deleted.');
        redirect(admin_url('testimonials.php'));
    }
}

$page   = max(1, (int) ($_GET['p'] ?? 1));
$perPage = 20;

$countStmt = $db->query('SELECT COUNT(*) FROM testimonials');
$total     = (int) $countStmt->fetchColumn();
$pagination = paginate($total, $perPage, $page);

$stmt = $db->prepare("SELECT * FROM testimonials ORDER BY sort_order ASC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
$stmt->execute();
$testimonials = $stmt->fetchAll();

if ($editId > 0) {
    $stmt = $db->prepare('SELECT * FROM testimonials WHERE id = ?');
    $stmt->execute([$editId]);
    $editData = $stmt->fetch();
}
?>
<div class="admin-page-header">
  <h2>Testimonials</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Testimonials</li>
    </ol>
  </nav>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#testimonialModal" onclick="resetForm()">
    <i class="bi bi-plus-lg"></i> Add New Testimonial
  </button>
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

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:60px">ID</th>
            <th>Name</th>
            <th>Company</th>
            <th>Role</th>
            <th>Rating</th>
            <th>Status</th>
            <th style="width:80px">Order</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($testimonials)): ?>
            <tr><td colspan="8" class="text-center py-4 text-muted">No testimonials found.</td></tr>
          <?php endif; ?>
          <?php foreach ($testimonials as $t): ?>
            <tr>
              <td><?php echo (int) $t['id']; ?></td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <?php if (!empty($t['avatar'])): ?>
                    <img src="<?php echo get_image_url($t['avatar']); ?>" class="rounded-circle" style="width:32px;height:32px;object-fit:cover;">
                  <?php endif; ?>
                  <strong><?php echo sanitize($t['name']); ?></strong>
                </div>
              </td>
              <td><?php echo sanitize($t['company'] ?? '—'); ?></td>
              <td><?php echo sanitize($t['role'] ?? '—'); ?></td>
              <td>
                <?php for ($s = 1; $s <= 5; $s++): ?>
                  <i class="bi bi-star-fill <?php echo $s <= (int) $t['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                <?php endfor; ?>
              </td>
              <td>
                <span class="badge <?php echo $t['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                  <?php echo $t['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                </span>
              </td>
              <td><?php echo (int) $t['sort_order']; ?></td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadEdit(<?php echo (int) $t['id']; ?>)" data-bs-toggle="modal" data-bs-target="#testimonialModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo (int) $t['id']; ?>" data-title="<?php echo sanitize($t['name']); ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($pagination['total_pages'] > 1): ?>
      <nav>
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
              <a class="page-link" href="?p=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</div>

<!-- Testimonial Edit Modal -->
<div class="modal fade" id="testimonialModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data" novalidate id="testimonialForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="tmId" value="0">
        <div class="modal-header">
          <h5 class="modal-title" id="testimonialModalLabel">Add / Edit Testimonial</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="tmName" class="form-label">Name <span class="text-danger">*</span></label>
              <input type="text" id="tmName" name="name" class="form-control" required maxlength="150">
            </div>
            <div class="col-md-6">
              <label for="tmCompany" class="form-label">Company</label>
              <input type="text" id="tmCompany" name="company" class="form-control" maxlength="255">
            </div>
            <div class="col-md-6">
              <label for="tmRole" class="form-label">Role</label>
              <input type="text" id="tmRole" name="role" class="form-control" maxlength="150">
            </div>
            <div class="col-md-6">
              <label for="tmRating" class="form-label">Rating</label>
              <select id="tmRating" name="rating" class="form-select">
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
              </select>
            </div>
            <div class="col-12">
              <label for="tmContent" class="form-label">Content <span class="text-danger">*</span></label>
              <textarea id="tmContent" name="content" class="form-control" rows="4" required></textarea>
            </div>
            <div class="col-md-6">
              <label for="tmAvatar" class="form-label">Avatar</label>
              <input type="file" id="tmAvatar" name="avatar" class="form-control" accept="image/*">
              <div id="tmAvatarPreview" class="mt-2"></div>
            </div>
            <div class="col-md-3">
              <label for="tmStatus" class="form-label">Status</label>
              <select id="tmStatus" name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="tmSortOrder" class="form-label">Sort Order</label>
              <input type="number" id="tmSortOrder" name="sort_order" class="form-control" value="0">
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
          <p>Delete testimonial from <strong id="deleteTitle"></strong>?</p>
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
var testimonialData = <?php echo json_encode($testimonials); ?>;

function resetForm() {
  document.getElementById('tmId').value = '0';
  document.getElementById('tmName').value = '';
  document.getElementById('tmCompany').value = '';
  document.getElementById('tmRole').value = '';
  document.getElementById('tmContent').value = '';
  document.getElementById('tmRating').value = '5';
  document.getElementById('tmAvatar').value = '';
  document.getElementById('tmAvatarPreview').innerHTML = '';
  document.getElementById('tmStatus').value = 'active';
  document.getElementById('tmSortOrder').value = '0';
  document.getElementById('testimonialModalLabel').textContent = 'Add New Testimonial';
}

function loadEdit(id) {
  var item = testimonialData.find(function(i) { return parseInt(i.id) === id; });
  if (!item) return;
  document.getElementById('tmId').value = item.id;
  document.getElementById('tmName').value = item.name;
  document.getElementById('tmCompany').value = item.company || '';
  document.getElementById('tmRole').value = item.role || '';
  document.getElementById('tmContent').value = item.content;
  document.getElementById('tmRating').value = item.rating;
  document.getElementById('tmAvatar').value = '';
  document.getElementById('tmStatus').value = item.status;
  document.getElementById('tmSortOrder').value = item.sort_order;
  document.getElementById('testimonialModalLabel').textContent = 'Edit Testimonial';

  var preview = document.getElementById('tmAvatarPreview');
  preview.innerHTML = item.avatar
    ? '<img src="<?php echo BASE_URL; ?>/' + item.avatar + '" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">'
    : '';
}

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
    var modal = new bootstrap.Modal(document.getElementById('testimonialModal'));
    modal.show();
    loadEdit(<?php echo $editId; ?>);
  <?php endif; ?>
});
</script>
