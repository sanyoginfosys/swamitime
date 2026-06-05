<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$db = getDB();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        if (empty($_FILES['files']['name'][0])) {
            $errors[] = 'Please select at least one file to upload.';
        } else {
            $uploaded = 0;
            $userId   = (int) ($_SESSION['admin_id'] ?? 0);

            foreach ($_FILES['files']['name'] as $i => $name) {
                if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $file = [
                    'name'     => $_FILES['files']['name'][$i],
                    'tmp_name' => $_FILES['files']['tmp_name'][$i],
                    'size'     => $_FILES['files']['size'][$i],
                    'error'    => $_FILES['files']['error'][$i],
                ];

                $upload = upload_file($file, 'uploads/media', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'zip'], MAX_UPLOAD_SIZE);
                if ($upload['success']) {
                    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                    $fileType   = in_array($extension, $imageTypes) ? 'image' : 'document';

                    $stmt = $db->prepare('INSERT INTO media_library (filename, original_name, file_path, file_type, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$upload['filename'], $name, $upload['path'], $fileType, $file['size'], $userId ?: null]);
                    $uploaded++;
                } else {
                    $errors[] = $name . ': ' . $upload['error'];
                }
            }

            if ($uploaded > 0) {
                set_flash('success', $uploaded . ' file(s) uploaded successfully.');
            }
            redirect(admin_url('media/'));
        }
    } elseif ($action === 'update_alt') {
        $id      = (int) ($_POST['id'] ?? 0);
        $altText = sanitize($_POST['alt_text'] ?? '');
        $stmt = $db->prepare('UPDATE media_library SET alt_text = ? WHERE id = ?');
        $stmt->execute([$altText, $id]);
        set_flash('success', 'Alt text updated.');
        redirect(admin_url('media/'));
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $stmt = $db->prepare('SELECT file_path FROM media_library WHERE id = ?');
        $stmt->execute([$id]);
        $filePath = $stmt->fetchColumn();
        if ($filePath) {
            delete_file($filePath);
        }
        $stmt = $db->prepare('DELETE FROM media_library WHERE id = ?');
        $stmt->execute([$id]);
        set_flash('success', 'File deleted.');
        redirect(admin_url('media/'));
    }
}

$page    = max(1, (int) ($_GET['p'] ?? 1));
$perPage = 24;

$countStmt = $db->query('SELECT COUNT(*) FROM media_library');
$total     = (int) $countStmt->fetchColumn();
$pagination = paginate($total, $perPage, $page);

$stmt = $db->prepare("SELECT * FROM media_library ORDER BY created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}");
$stmt->execute();
$media = $stmt->fetchAll();

$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
?>
<div class="admin-page-header">
  <h2>Media Library</h2>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?php echo admin_url(); ?>">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Media Library</li>
    </ol>
  </nav>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
    <i class="bi bi-cloud-upload"></i> Upload Files
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
    <?php if (empty($media)): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-images fa-3x mb-3"></i>
        <p>No media files uploaded yet. Click "Upload Files" to get started.</p>
      </div>
    <?php else: ?>
      <div class="row g-3" id="mediaGrid">
        <?php foreach ($media as $m): ?>
          <?php
            $ext = strtolower(pathinfo($m['filename'], PATHINFO_EXTENSION));
            $isImage = in_array($ext, $imageExtensions, true);
            $fileUrl = get_image_url($m['file_path']);
            $sizeStr = $m['file_size'] > 1048576
              ? round($m['file_size'] / 1048576, 1) . ' MB'
              : round($m['file_size'] / 1024, 1) . ' KB';
          ?>
          <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card h-100 media-card" data-id="<?php echo (int) $m['id']; ?>">
              <div class="card-img-top media-thumb bg-light d-flex align-items-center justify-content-center" style="height:140px;overflow:hidden;">
                <?php if ($isImage): ?>
                  <img src="<?php echo $fileUrl; ?>" class="img-fluid" style="max-height:140px;object-fit:contain;" alt="<?php echo sanitize($m['alt_text'] ?? ''); ?>" loading="lazy">
                <?php else: ?>
                  <div class="text-center text-muted">
                    <i class="bi bi-file fa-3x"></i>
                    <div class="small mt-1">.<?php echo $ext; ?></div>
                  </div>
                <?php endif; ?>
              </div>
              <div class="card-body p-2 small">
                <div class="text-truncate fw-bold" title="<?php echo sanitize($m['original_name']); ?>"><?php echo sanitize($m['original_name']); ?></div>
                <div class="text-muted d-flex justify-content-between">
                  <span><?php echo $sizeStr; ?></span>
                  <span><?php echo format_date($m['created_at'], 'd/m/y'); ?></span>
                </div>
              </div>
              <div class="card-footer p-1 d-flex justify-content-around">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Copy URL" onclick="copyUrl('<?php echo $fileUrl; ?>', this)">
                  <i class="bi bi-clipboard"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" title="Edit Alt Text" data-bs-toggle="modal" data-bs-target="#altModal"
                        data-id="<?php echo (int) $m['id']; ?>"
                        data-alt="<?php echo sanitize($m['alt_text'] ?? ''); ?>"
                        data-name="<?php echo sanitize($m['original_name']); ?>">
                  <i class="bi bi-tag"></i>
                </button>
                <a href="<?php echo $fileUrl; ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="View">
                  <i class="bi bi-eye"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteMediaModal"
                        data-id="<?php echo (int) $m['id']; ?>"
                        data-name="<?php echo sanitize($m['original_name']); ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($pagination['total_pages'] > 1): ?>
        <nav class="mt-3">
          <ul class="pagination justify-content-center">
            <li class="page-item <?php echo !$pagination['has_previous'] ? 'disabled' : ''; ?>">
              <a class="page-link" href="?p=<?php echo $pagination['current_page'] - 1; ?>">&laquo;</a>
            </li>
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
              <li class="page-item <?php echo $i === $pagination['current_page'] ? 'active' : ''; ?>">
                <a class="page-link" href="?p=<?php echo $i; ?>"><?php echo $i; ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?php echo !$pagination['has_next'] ? 'disabled' : ''; ?>">
              <a class="page-link" href="?p=<?php echo $pagination['current_page'] + 1; ?>">&raquo;</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data" id="uploadForm">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="upload">
        <div class="modal-header">
          <h5 class="modal-title">Upload Files</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="upload-zone border border-2 border-dashed rounded p-5 text-center bg-light" id="dropZone">
            <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted mb-3"></i>
            <p class="mb-2">Drag and drop files here or click to browse</p>
            <p class="text-muted small mb-0">Supported: JPG, PNG, GIF, WebP, SVG, PDF, DOC, DOCX, XLS, XLSX, CSV, TXT, ZIP (Max 10 MB each)</p>
            <input type="file" id="fileInput" name="files[]" class="form-control mt-3" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip">
          </div>
          <div id="uploadPreview" class="row g-2 mt-3"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Alt Text Modal -->
<div class="modal fade" id="altModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="update_alt">
        <input type="hidden" name="id" id="altId">
        <div class="modal-header">
          <h5 class="modal-title">Edit Alt Text</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small">File: <strong id="altFileName"></strong></p>
          <label for="altTextInput" class="form-label">Alt Text</label>
          <input type="text" id="altTextInput" name="alt_text" class="form-control" maxlength="500" placeholder="Describe the image...">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteMediaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <?php echo Security::csrf_field(); ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteMediaId">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong id="deleteMediaName"></strong>?</p>
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
function copyUrl(url, btn) {
  navigator.clipboard.writeText(url).then(function () {
    var icon = btn.querySelector('i');
    icon.className = 'bi bi-check text-success';
    setTimeout(function () { icon.className = 'bi bi-clipboard'; }, 1500);
  }).catch(function () {
    var temp = document.createElement('input');
    temp.value = url;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    document.body.removeChild(temp);
    var icon = btn.querySelector('i');
    icon.className = 'bi bi-check text-success';
    setTimeout(function () { icon.className = 'bi bi-clipboard'; }, 1500);
  });
}

document.addEventListener('DOMContentLoaded', function () {
  var dropZone = document.getElementById('dropZone');
  var fileInput = document.getElementById('fileInput');
  var preview = document.getElementById('uploadPreview');

  if (dropZone) {
    dropZone.addEventListener('dragover', function (e) {
      e.preventDefault();
      dropZone.classList.add('border-primary');
    });
    dropZone.addEventListener('dragleave', function () {
      dropZone.classList.remove('border-primary');
    });
    dropZone.addEventListener('drop', function (e) {
      e.preventDefault();
      dropZone.classList.remove('border-primary');
      if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        showPreview(e.dataTransfer.files);
      }
    });
    dropZone.addEventListener('click', function (e) {
      if (e.target !== fileInput) {
        fileInput.click();
      }
    });
    fileInput.addEventListener('change', function () {
      showPreview(fileInput.files);
    });
  }

  function showPreview(files) {
    preview.innerHTML = '';
    Array.from(files).forEach(function (f) {
      var col = document.createElement('div');
      col.className = 'col-6 col-md-4 col-lg-3';
      col.innerHTML = '<div class="card"><div class="card-body p-2 text-center small"><i class="bi bi-file fa-2x text-muted mb-1"></i><div class="text-truncate">' + f.name + '</div><div class="text-muted">' + (f.size / 1024).toFixed(1) + ' KB</div></div></div>';
      preview.appendChild(col);
    });
  }

  var altModal = document.getElementById('altModal');
  if (altModal) {
    altModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('altId').value = button.getAttribute('data-id');
      document.getElementById('altTextInput').value = button.getAttribute('data-alt') || '';
      document.getElementById('altFileName').textContent = button.getAttribute('data-name');
    });
  }

  var deleteMediaModal = document.getElementById('deleteMediaModal');
  if (deleteMediaModal) {
    deleteMediaModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      document.getElementById('deleteMediaId').value = button.getAttribute('data-id');
      document.getElementById('deleteMediaName').textContent = button.getAttribute('data-name');
    });
  }
});
</script>

<style>
.upload-zone { cursor: pointer; transition: border-color 0.2s; }
.upload-zone:hover { border-color: #0d6efd !important; }
.media-card { transition: box-shadow 0.2s; }
.media-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.border-dashed { border-style: dashed !important; }
</style>
