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
            'title'            => sanitize($_POST['title'] ?? ''),
            'subtitle'         => sanitize($_POST['subtitle'] ?? ''),
            'button_text'      => sanitize($_POST['button_text'] ?? ''),
            'button_url'       => sanitize($_POST['button_url'] ?? ''),
            'button_style'     => $_POST['button_style'] === 'secondary' ? 'secondary' : 'primary',
            'background_style' => sanitize($_POST['background_style'] ?? 'default'),
            'page_location'    => sanitize($_POST['page_location'] ?? ''),
            'status'           => $_POST['status'] === 'active' ? 'active' : 'inactive',
        ];

        if ($data['title'] === '') {
            $errors[] = 'Title is required.';
        }
        if ($data['button_text'] === '') {
            $errors[] = 'Button text is required.';
        }
        if ($data['button_url'] === '') {
            $errors[] = 'Button URL is required.';
        }
        if ($data['page_location'] === '') {
            $errors[] = 'Page location is required.';
        }

        if (empty($errors)) {
            if ($id === 0) {
                $stmt = $db->prepare('INSERT INTO cta_blocks (title, subtitle, button_text, button_url, button_style, background_style, page_location, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$data['title'], $data['subtitle'], $data['button_text'], $data['button_url'], $data['button_style'], $data['background_style'], $data['page_location'], $data['status']]);
                set_flash('success', 'CTA block created successfully.');
            } else {
                $stmt = $db->prepare('UPDATE cta_blocks SET title = ?, subtitle = ?, button_text = ?, button_url = ?, button_style = ?, background_style = ?, page_location = ?, status = ? WHERE id = ?');
                $stmt->execute([$data['title'], $data['subtitle'], $data['button_text'], $data['button_url'], $data['button_style'], $data['background_style'], $data['page_location'], $data['status'], $id]);
                set_flash('success', 'CTA block updated successfully.');
            }
            redirect(admin_url('pages/cta-blocks.php'));
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM cta_blocks WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'CTA block deleted.');
        redirect(admin_url('pages/cta-blocks.php'));
    }
}

$blocks = $db->query('SELECT * FROM cta_blocks ORDER BY created_at DESC')->fetchAll();

if ($editId > 0) {
    $stmt = $db->prepare('SELECT * FROM cta_blocks WHERE id = ?');
    $stmt->execute([$editId]);
    $editData = $stmt->fetch();
}

$bgStyles     = ['default', 'gradient-blue', 'gradient-dark', 'light', 'dark', 'primary-bg'];
$buttonStyles = ['primary', 'secondary'];
$pages        = $db->query('SELECT slug, title FROM pages WHERE status = \'published\' ORDER BY title ASC')->fetchAll();
?>
<div class="admin-page-header">
  <h2>CTA Blocks</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">CTA Blocks</li>
    </ol>
  </nav>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ctaModal" onclick="resetForm()">
    <i class="bi bi-plus-lg"></i> Add New CTA
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
            <th>Title</th>
            <th>Button</th>
            <th>Location</th>
            <th>Style</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($blocks)): ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">No CTA blocks found.</td></tr>
          <?php endif; ?>
          <?php foreach ($blocks as $b): ?>
            <tr>
              <td><?php echo (int) $b['id']; ?></td>
              <td>
                <strong><?php echo sanitize($b['title']); ?></strong>
                <?php if ($b['subtitle']): ?>
                  <br><small class="text-muted"><?php echo sanitize($b['subtitle']); ?></small>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-light text-dark"><?php echo sanitize($b['button_text']); ?></span>
              </td>
              <td><code><?php echo sanitize($b['page_location']); ?></code></td>
              <td>
                <span class="badge bg-info text-dark"><?php echo sanitize($b['button_style']); ?></span>
                <span class="badge bg-secondary"><?php echo sanitize($b['background_style']); ?></span>
              </td>
              <td>
                <span class="badge <?php echo $b['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                  <?php echo $b['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                </span>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadEdit(<?php echo (int) $b['id']; ?>)" data-bs-toggle="modal" data-bs-target="#ctaModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo (int) $b['id']; ?>" data-title="<?php echo sanitize($b['title']); ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- CTA Edit Modal -->
<div class="modal fade" id="ctaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" novalidate id="ctaForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="ctaId" value="0">
        <div class="modal-header">
          <h5 class="modal-title" id="ctaModalLabel">Add / Edit CTA Block</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label for="ctaTitle" class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" id="ctaTitle" name="title" class="form-control" required maxlength="255">
            </div>
            <div class="col-12">
              <label for="ctaSubtitle" class="form-label">Subtitle</label>
              <textarea id="ctaSubtitle" name="subtitle" class="form-control" rows="2" maxlength="500"></textarea>
            </div>
            <div class="col-md-6">
              <label for="ctaButtonText" class="form-label">Button Text <span class="text-danger">*</span></label>
              <input type="text" id="ctaButtonText" name="button_text" class="form-control" required maxlength="100">
            </div>
            <div class="col-md-6">
              <label for="ctaButtonUrl" class="form-label">Button URL <span class="text-danger">*</span></label>
              <input type="text" id="ctaButtonUrl" name="button_url" class="form-control" required maxlength="500" placeholder="/contact-us">
            </div>
            <div class="col-md-4">
              <label for="ctaButtonStyle" class="form-label">Button Style</label>
              <select id="ctaButtonStyle" name="button_style" class="form-select">
                <option value="primary">Primary</option>
                <option value="secondary">Secondary</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="ctaBgStyle" class="form-label">Background Style</label>
              <select id="ctaBgStyle" name="background_style" class="form-select">
                <?php foreach ($bgStyles as $s): ?>
                  <option value="<?php echo $s; ?>"><?php echo ucfirst(str_replace('-', ' ', $s)); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="ctaPageLocation" class="form-label">Page Location <span class="text-danger">*</span></label>
              <input type="text" id="ctaPageLocation" name="page_location" class="form-control" list="pageLocationList" placeholder="home_bottom">
              <datalist id="pageLocationList">
                <option value="home_bottom">
                <option value="services_bottom">
                <option value="services_page">
                <?php foreach ($pages as $p): ?>
                  <option value="<?php echo sanitize($p['slug']); ?>_bottom">
                <?php endforeach; ?>
              </datalist>
            </div>
            <div class="col-md-6">
              <label for="ctaStatus" class="form-label">Status</label>
              <select id="ctaStatus" name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
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
          <p>Delete CTA block <strong id="deleteTitle"></strong>?</p>
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
var ctaData = <?php echo json_encode($blocks); ?>;

function resetForm() {
  document.getElementById('ctaId').value = '0';
  document.getElementById('ctaTitle').value = '';
  document.getElementById('ctaSubtitle').value = '';
  document.getElementById('ctaButtonText').value = '';
  document.getElementById('ctaButtonUrl').value = '';
  document.getElementById('ctaButtonStyle').value = 'primary';
  document.getElementById('ctaBgStyle').value = 'default';
  document.getElementById('ctaPageLocation').value = '';
  document.getElementById('ctaStatus').value = 'active';
  document.getElementById('ctaModalLabel').textContent = 'Add New CTA Block';
}

function loadEdit(id) {
  var item = ctaData.find(function(i) { return parseInt(i.id) === id; });
  if (!item) return;
  document.getElementById('ctaId').value = item.id;
  document.getElementById('ctaTitle').value = item.title;
  document.getElementById('ctaSubtitle').value = item.subtitle || '';
  document.getElementById('ctaButtonText').value = item.button_text;
  document.getElementById('ctaButtonUrl').value = item.button_url;
  document.getElementById('ctaButtonStyle').value = item.button_style;
  document.getElementById('ctaBgStyle').value = item.background_style;
  document.getElementById('ctaPageLocation').value = item.page_location;
  document.getElementById('ctaStatus').value = item.status;
  document.getElementById('ctaModalLabel').textContent = 'Edit CTA Block';
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
    var modal = new bootstrap.Modal(document.getElementById('ctaModal'));
    modal.show();
    loadEdit(<?php echo $editId; ?>);
  <?php endif; ?>
});
</script>
