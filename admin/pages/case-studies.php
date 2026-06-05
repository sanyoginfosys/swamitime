<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$errors   = [];
$editData = null;
$editId   = (int) ($_GET['edit'] ?? 0);

$industries = $db->query('SELECT title FROM industries WHERE status = \'active\' ORDER BY sort_order ASC')->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id   = (int) ($_POST['id'] ?? 0);
        $data = [
            'title'          => sanitize($_POST['title'] ?? ''),
            'slug'           => sanitize($_POST['slug'] ?? ''),
            'industry'       => sanitize($_POST['industry'] ?? ''),
            'challenge'      => $_POST['challenge'] ?? '',
            'solution'       => $_POST['solution'] ?? '',
            'result'         => $_POST['result'] ?? '',
            'status'         => $_POST['status'] === 'published' ? 'published' : 'draft',
            'sort_order'     => (int) ($_POST['sort_order'] ?? 0),
        ];

        if ($data['title'] === '') {
            $errors[] = 'Title is required.';
        }
        if ($data['slug'] === '') {
            $data['slug'] = create_slug($data['title']);
        }

        $dupStmt = $db->prepare('SELECT id FROM case_studies WHERE slug = ? AND id != ?');
        $dupStmt->execute([$data['slug'], $id]);
        if ($dupStmt->fetch()) {
            $errors[] = 'A case study with this slug already exists.';
        }

        $featuredImage = ($id > 0 ? ($db->prepare('SELECT featured_image FROM case_studies WHERE id = ?')->execute([$id]) !== false ? $db->query("SELECT featured_image FROM case_studies WHERE id = $id")->fetchColumn() : '') : '');

        if (!empty($_FILES['featured_image']['name'])) {
            $upload = upload_file($_FILES['featured_image'], 'uploads/case-studies', ['jpg', 'jpeg', 'png', 'gif', 'webp'], MAX_UPLOAD_SIZE);
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
            if ($id === 0) {
                $stmt = $db->prepare('INSERT INTO case_studies (title, slug, industry, challenge, solution, result, featured_image, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$data['title'], $data['slug'], $data['industry'], $data['challenge'], $data['solution'], $data['result'], $featuredImage, $data['status'], $data['sort_order']]);
                set_flash('success', 'Case study created successfully.');
            } else {
                $stmt = $db->prepare('UPDATE case_studies SET title = ?, slug = ?, industry = ?, challenge = ?, solution = ?, result = ?, featured_image = ?, status = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([$data['title'], $data['slug'], $data['industry'], $data['challenge'], $data['solution'], $data['result'], $featuredImage, $data['status'], $data['sort_order'], $id]);
                set_flash('success', 'Case study updated successfully.');
            }
            redirect(admin_url('case-studies.php'));
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $img = $db->prepare('SELECT featured_image FROM case_studies WHERE id = ?');
        $img->execute([$id]);
        $imgPath = $img->fetchColumn();
        if ($imgPath) {
            delete_file($imgPath);
        }
        $stmt = $db->prepare('DELETE FROM case_studies WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'Case study deleted.');
        redirect(admin_url('case-studies.php'));
    }
}

$page   = max(1, (int) ($_GET['p'] ?? 1));
$perPage = 20;

$countStmt = $db->query('SELECT COUNT(*) FROM case_studies');
$total     = (int) $countStmt->fetchColumn();
$pagination = paginate($total, $perPage, $page);

$stmt = $db->prepare("SELECT * FROM case_studies ORDER BY sort_order ASC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
$stmt->execute();
$caseStudies = $stmt->fetchAll();

if ($editId > 0) {
    $stmt = $db->prepare('SELECT * FROM case_studies WHERE id = ?');
    $stmt->execute([$editId]);
    $editData = $stmt->fetch();
}
?>
<div class="admin-page-header">
  <h2>Case Studies</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Case Studies</li>
    </ol>
  </nav>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#caseStudyModal" onclick="resetForm()">
    <i class="bi bi-plus-lg"></i> Add New Case Study
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
            <th>Industry</th>
            <th>Status</th>
            <th style="width:80px">Order</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($caseStudies)): ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">No case studies found.</td></tr>
          <?php endif; ?>
          <?php foreach ($caseStudies as $cs): ?>
            <tr>
              <td><?php echo (int) $cs['id']; ?></td>
              <td><strong><?php echo sanitize($cs['title']); ?></strong></td>
              <td><?php echo sanitize($cs['industry'] ?? '—'); ?></td>
              <td>
                <span class="badge <?php echo $cs['status'] === 'published' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                  <?php echo $cs['status'] === 'published' ? 'Published' : 'Draft'; ?>
                </span>
              </td>
              <td><?php echo (int) $cs['sort_order']; ?></td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadEdit(<?php echo (int) $cs['id']; ?>)" data-bs-toggle="modal" data-bs-target="#caseStudyModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="<?php echo site_url('case-studies/' . $cs['slug']); ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-box-arrow-up-right"></i></a>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo (int) $cs['id']; ?>" data-title="<?php echo sanitize($cs['title']); ?>"><i class="bi bi-trash"></i></button>
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

<!-- Case Study Edit Modal -->
<div class="modal fade" id="caseStudyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data" novalidate id="caseStudyForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="csId" value="0">
        <div class="modal-header">
          <h5 class="modal-title" id="caseStudyModalLabel">Add / Edit Case Study</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label for="csTitle" class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" id="csTitle" name="title" class="form-control" required maxlength="255" oninput="document.getElementById('csSlug').value = slugify(this.value)">
            </div>
            <div class="col-md-4">
              <label for="csSlug" class="form-label">Slug</label>
              <input type="text" id="csSlug" name="slug" class="form-control" maxlength="255">
            </div>
            <div class="col-md-6">
              <label for="csIndustry" class="form-label">Industry</label>
              <input type="text" id="csIndustry" name="industry" class="form-control" list="industryList" maxlength="100">
              <datalist id="industryList">
                <?php foreach ($industries as $ind): ?>
                  <option value="<?php echo sanitize($ind); ?>">
                <?php endforeach; ?>
              </datalist>
            </div>
            <div class="col-md-6">
              <label for="csStatus" class="form-label">Status</label>
              <select id="csStatus" name="status" class="form-select">
                <option value="published">Published</option>
                <option value="draft">Draft</option>
              </select>
            </div>
            <div class="col-12">
              <label for="csChallenge" class="form-label">Challenge</label>
              <textarea id="csChallenge" name="challenge" class="form-control" rows="4"></textarea>
            </div>
            <div class="col-12">
              <label for="csSolution" class="form-label">Solution</label>
              <textarea id="csSolution" name="solution" class="form-control" rows="4"></textarea>
            </div>
            <div class="col-12">
              <label for="csResult" class="form-label">Result</label>
              <textarea id="csResult" name="result" class="form-control" rows="4"></textarea>
            </div>
            <div class="col-md-6">
              <label for="csFeaturedImage" class="form-label">Featured Image</label>
              <input type="file" id="csFeaturedImage" name="featured_image" class="form-control" accept="image/*">
              <div id="csImagePreview" class="mt-2"></div>
            </div>
            <div class="col-md-6">
              <label for="csSortOrder" class="form-label">Sort Order</label>
              <input type="number" id="csSortOrder" name="sort_order" class="form-control" value="0">
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
var caseStudyData = <?php echo json_encode($caseStudies); ?>;

function slugify(text) {
  return text.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-').replace(/^-+|-+$/g, '');
}

function resetForm() {
  document.getElementById('csId').value = '0';
  document.getElementById('csTitle').value = '';
  document.getElementById('csSlug').value = '';
  document.getElementById('csIndustry').value = '';
  document.getElementById('csChallenge').value = '';
  document.getElementById('csSolution').value = '';
  document.getElementById('csResult').value = '';
  document.getElementById('csFeaturedImage').value = '';
  document.getElementById('csImagePreview').innerHTML = '';
  document.getElementById('csStatus').value = 'published';
  document.getElementById('csSortOrder').value = '0';
  document.getElementById('caseStudyModalLabel').textContent = 'Add New Case Study';
}

function loadEdit(id) {
  var item = caseStudyData.find(function(i) { return parseInt(i.id) === id; });
  if (!item) return;
  document.getElementById('csId').value = item.id;
  document.getElementById('csTitle').value = item.title;
  document.getElementById('csSlug').value = item.slug;
  document.getElementById('csIndustry').value = item.industry || '';
  document.getElementById('csChallenge').value = item.challenge || '';
  document.getElementById('csSolution').value = item.solution || '';
  document.getElementById('csResult').value = item.result || '';
  document.getElementById('csFeaturedImage').value = '';
  document.getElementById('csStatus').value = item.status;
  document.getElementById('csSortOrder').value = item.sort_order;
  document.getElementById('caseStudyModalLabel').textContent = 'Edit: ' + item.title;

  var preview = document.getElementById('csImagePreview');
  preview.innerHTML = item.featured_image
    ? '<img src="<?php echo BASE_URL; ?>/' + item.featured_image + '" class="img-thumbnail" style="max-height:100px;">'
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
    var modal = new bootstrap.Modal(document.getElementById('caseStudyModal'));
    modal.show();
    loadEdit(<?php echo $editId; ?>);
  <?php endif; ?>
});
</script>
