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
            'question'   => sanitize($_POST['question'] ?? ''),
            'answer'     => $_POST['answer'] ?? '',
            'category'   => sanitize($_POST['category'] ?? ''),
            'status'     => $_POST['status'] === 'active' ? 'active' : 'inactive',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];

        if ($data['question'] === '') {
            $errors[] = 'Question is required.';
        }
        if ($data['answer'] === '') {
            $errors[] = 'Answer is required.';
        }

        if (empty($errors)) {
            if ($id === 0) {
                $stmt = $db->prepare('INSERT INTO faqs (question, answer, category, status, sort_order) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$data['question'], $data['answer'], $data['category'], $data['status'], $data['sort_order']]);
                set_flash('success', 'FAQ created successfully.');
            } else {
                $stmt = $db->prepare('UPDATE faqs SET question = ?, answer = ?, category = ?, status = ?, sort_order = ? WHERE id = ?');
                $stmt->execute([$data['question'], $data['answer'], $data['category'], $data['status'], $data['sort_order'], $id]);
                set_flash('success', 'FAQ updated successfully.');
            }
            redirect(admin_url('faqs.php'));
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM faqs WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'FAQ deleted.');
        redirect(admin_url('faqs.php'));
    } elseif ($action === 'reorder') {
        $order = json_decode($_POST['order'] ?? '[]', true);
        foreach ($order as $index => $faqId) {
            $stmt = $db->prepare('UPDATE faqs SET sort_order = ? WHERE id = ?');
            $stmt->execute([$index + 1, (int) $faqId]);
        }
        set_flash('success', 'FAQ order updated.');
        redirect(admin_url('faqs.php'));
    }
}

$faqs = $db->query('SELECT * FROM faqs ORDER BY sort_order ASC, id ASC')->fetchAll();

$categories = array_unique(array_filter(array_column($faqs, 'category')));
sort($categories);

if ($editId > 0) {
    $stmt = $db->prepare('SELECT * FROM faqs WHERE id = ?');
    $stmt->execute([$editId]);
    $editData = $stmt->fetch();
}
?>
<div class="admin-page-header">
  <h2>FAQs</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">FAQs</li>
    </ol>
  </nav>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#faqModal" onclick="resetForm()">
    <i class="bi bi-plus-lg"></i> Add New FAQ
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

<div class="alert alert-info small">
  <i class="bi bi-grip-vertical"></i> Drag rows to reorder FAQs. Order saves automatically.
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover align-middle" id="faqTable">
        <thead class="table-light">
          <tr>
            <th style="width:40px"></th>
            <th style="width:60px">ID</th>
            <th>Question</th>
            <th>Category</th>
            <th>Status</th>
            <th style="width:80px">Order</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="faqSortable">
          <?php if (empty($faqs)): ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">No FAQs found.</td></tr>
          <?php endif; ?>
          <?php foreach ($faqs as $faq): ?>
            <tr data-id="<?php echo (int) $faq['id']; ?>">
              <td><i class="bi bi-grip-vertical text-muted" style="cursor:grab;"></i></td>
              <td><?php echo (int) $faq['id']; ?></td>
              <td><strong><?php echo sanitize($faq['question']); ?></strong></td>
              <td>
                <?php if ($faq['category']): ?>
                  <span class="badge bg-info text-dark"><?php echo sanitize($faq['category']); ?></span>
                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge <?php echo $faq['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                  <?php echo $faq['status'] === 'active' ? 'Active' : 'Inactive'; ?>
                </span>
              </td>
              <td><?php echo (int) $faq['sort_order']; ?></td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadEdit(<?php echo (int) $faq['id']; ?>)" data-bs-toggle="modal" data-bs-target="#faqModal">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo (int) $faq['id']; ?>" data-question="<?php echo sanitize(truncate($faq['question'], 60)); ?>">
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

<!-- FAQ Edit Modal -->
<div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" novalidate id="faqForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" id="faqId" value="0">
        <div class="modal-header">
          <h5 class="modal-title" id="faqModalLabel">Add / Edit FAQ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="faqQuestion" class="form-label">Question <span class="text-danger">*</span></label>
            <input type="text" id="faqQuestion" name="question" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="faqAnswer" class="form-label">Answer <span class="text-danger">*</span></label>
            <textarea id="faqAnswer" name="answer" class="form-control" rows="6"></textarea>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="faqCategory" class="form-label">Category</label>
              <input type="text" id="faqCategory" name="category" class="form-control" list="faqCategoryList" maxlength="100">
              <datalist id="faqCategoryList">
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo sanitize($cat); ?>">
                <?php endforeach; ?>
              </datalist>
            </div>
            <div class="col-md-3">
              <label for="faqStatus" class="form-label">Status</label>
              <select id="faqStatus" name="status" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="faqSortOrder" class="form-label">Sort Order</label>
              <input type="number" id="faqSortOrder" name="sort_order" class="form-control" value="0">
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
          <p>Delete FAQ: <strong id="deleteQuestion"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<form method="post" id="reorderForm" style="display:none;">
  <?php echo Security::csrf_field(); ?>
  <input type="hidden" name="action" value="reorder">
  <input type="hidden" name="order" id="reorderData">
</form>

<script>
var faqData = <?php echo json_encode($faqs); ?>;

function resetForm() {
  document.getElementById('faqId').value = '0';
  document.getElementById('faqQuestion').value = '';
  document.getElementById('faqAnswer').value = '';
  document.getElementById('faqCategory').value = '';
  document.getElementById('faqStatus').value = 'active';
  document.getElementById('faqSortOrder').value = '0';
  document.getElementById('faqModalLabel').textContent = 'Add New FAQ';
}

function loadEdit(id) {
  var item = faqData.find(function(i) { return parseInt(i.id) === id; });
  if (!item) return;
  document.getElementById('faqId').value = item.id;
  document.getElementById('faqQuestion').value = item.question;
  document.getElementById('faqAnswer').value = item.answer;
  document.getElementById('faqCategory').value = item.category || '';
  document.getElementById('faqStatus').value = item.status;
  document.getElementById('faqSortOrder').value = item.sort_order;
  document.getElementById('faqModalLabel').textContent = 'Edit FAQ';
}

document.addEventListener('DOMContentLoaded', function () {
  var deleteModal = document.getElementById('deleteModal');
  if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('deleteId').value = button.getAttribute('data-id');
      document.getElementById('deleteQuestion').textContent = button.getAttribute('data-question');
    });
  }

  <?php if ($editId > 0 && $editData): ?>
    var modal = new bootstrap.Modal(document.getElementById('faqModal'));
    modal.show();
    loadEdit(<?php echo $editId; ?>);
  <?php endif; ?>
});

(function () {
  var el = document.getElementById('faqSortable');
  if (!el) return;
  var dragRow = null;

  el.addEventListener('dragstart', function (e) {
    dragRow = e.target.closest('tr');
    if (!dragRow) return;
    dragRow.classList.add('opacity-50');
    e.dataTransfer.effectAllowed = 'move';
  });

  el.addEventListener('dragover', function (e) {
    e.preventDefault();
    var target = e.target.closest('tr');
    if (!target || target === dragRow) return;
    var rect = target.getBoundingClientRect();
    var next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
    el.insertBefore(dragRow, next ? target.nextSibling : target);
  });

  el.addEventListener('dragend', function () {
    if (dragRow) {
      dragRow.classList.remove('opacity-50');
      dragRow = null;
    }
    var ids = [];
    el.querySelectorAll('tr[data-id]').forEach(function (row) {
      ids.push(row.getAttribute('data-id'));
    });
    document.getElementById('reorderData').value = JSON.stringify(ids);
    document.getElementById('reorderForm').submit();
  });
})();
</script>

<style>
#faqSortable tr { cursor: default; }
#faqSortable tr[draggable="true"]:hover { background-color: #f8f9fa; }
</style>

<script>
document.querySelectorAll('#faqSortable tr[data-id]').forEach(function(row) {
  row.setAttribute('draggable', 'true');
});
</script>
