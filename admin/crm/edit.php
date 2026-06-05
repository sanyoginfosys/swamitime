<?php
$pageTitle = 'Add/Edit Lead';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();
$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$lead = null;

if ($editId) {
    $stmt = $db->prepare("SELECT * FROM crm_leads WHERE id = ?");
    $stmt->execute([$editId]);
    $lead = $stmt->fetch();
    if (!$lead) { set_flash('error', 'Lead not found.'); redirect(admin_url('crm/')); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();

    $fullName = sanitize($_POST['full_name'] ?? '');
    $companyName = sanitize($_POST['company_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $serviceInterested = sanitize($_POST['service_interested'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    $leadSource = sanitize($_POST['lead_source'] ?? 'website');
    $leadStatus = sanitize($_POST['lead_status'] ?? 'new');
    $priority = sanitize($_POST['priority'] ?? 'medium');
    $assignedUserId = $_POST['assigned_user_id'] ? (int)$_POST['assigned_user_id'] : null;
    $followUpDate = sanitize($_POST['follow_up_date'] ?? '') ?: null;

    $errors = [];
    $required = ['full_name', 'email'];
    foreach ($required as $field) {
        if (empty($$field)) $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
    }
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    $validStatuses = ['new','contacted','qualified','proposal_sent','won','lost'];
    if (!in_array($leadStatus, $validStatuses)) $errors[] = 'Invalid lead status.';
    $validPriorities = ['low','medium','high'];
    if (!in_array($priority, $validPriorities)) $errors[] = 'Invalid priority.';

    if (empty($errors)) {
        if ($editId) {
            $convertedAt = null;
            $oldStatusStmt = $db->prepare("SELECT lead_status FROM crm_leads WHERE id=?");
            $oldStatusStmt->execute([$editId]);
            $oldStatus = $oldStatusStmt->fetchColumn();
            if ($leadStatus === 'won' && $oldStatus !== 'won') {
                $convertedAt = date('Y-m-d H:i:s');
            }
            $stmt = $db->prepare("
                UPDATE crm_leads SET
                    full_name=?, company_name=?, email=?, phone=?, service_interested=?,
                    message=?, lead_source=?, lead_status=?, priority=?,
                    assigned_user_id=?, follow_up_date=?, converted_at=COALESCE(?, converted_at),
                    updated_at=NOW()
                WHERE id=?
            ");
            $stmt->execute([
                $fullName, $companyName, $email, $phone, $serviceInterested,
                $message, $leadSource, $leadStatus, $priority,
                $assignedUserId, $followUpDate, $convertedAt, $editId
            ]);
            set_flash('success', 'Lead updated successfully.');
        } else {
            $stmt = $db->prepare("
                INSERT INTO crm_leads (full_name, company_name, email, phone, service_interested, message, lead_source, lead_status, priority, assigned_user_id, follow_up_date, converted_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $convertedAt = $leadStatus === 'won' ? date('Y-m-d H:i:s') : null;
            $stmt->execute([
                $fullName, $companyName, $email, $phone, $serviceInterested,
                $message, $leadSource, $leadStatus, $priority,
                $assignedUserId, $followUpDate, $convertedAt
            ]);
            $editId = $db->lastInsertId();
            set_flash('success', 'Lead created successfully.');
        }

        if (isset($_POST['note']) && trim($_POST['note']) !== '' && $editId) {
            $noteText = sanitize($_POST['note']);
            $noteStmt = $db->prepare("INSERT INTO crm_notes (lead_id, user_id, note) VALUES (?, ?, ?)");
            $noteStmt->execute([$editId, $_SESSION['admin_id'] ?? null, $noteText]);
        }

        redirect(admin_url('crm/view.php?id=' . $editId));
    } else {
        set_flash('error', implode('<br>', $errors));
    }
}

$services = $db->query("SELECT title FROM services WHERE status='active' ORDER BY sort_order")->fetchAll(PDO::FETCH_COLUMN);
$admins = $db->query("SELECT id, full_name FROM admins WHERE status='active' ORDER BY full_name")->fetchAll();
$notes = [];
if ($editId) {
    $noteStmt = $db->prepare("SELECT cn.*, a.full_name as user_name FROM crm_notes cn LEFT JOIN admins a ON cn.user_id = a.id WHERE cn.lead_id = ? ORDER BY cn.created_at DESC");
    $noteStmt->execute([$editId]);
    $notes = $noteStmt->fetchAll();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card form-card mb-4">
            <div class="card-header"><?php echo $editId ? 'Edit Lead' : 'Add New Lead'; ?></div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" required value="<?php echo htmlspecialchars($_POST['full_name'] ?? $lead['full_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($_POST['company_name'] ?? $lead['company_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? $lead['email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? $lead['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Service Interested In</label>
                            <select name="service_interested" class="form-select">
                                <option value="">Select service...</option>
                                <?php foreach ($services as $svc): $sel = ($_POST['service_interested'] ?? $lead['service_interested'] ?? '') === $svc; ?>
                                <option value="<?php echo htmlspecialchars($svc); ?>" <?php echo $sel ? 'selected' : ''; ?>><?php echo htmlspecialchars($svc); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lead Source</label>
                            <select name="lead_source" class="form-select">
                                <?php foreach (['website','contact_form','referral','linkedin','other'] as $src): ?>
                                <option value="<?php echo $src; ?>" <?php echo ($_POST['lead_source'] ?? $lead['lead_source'] ?? 'website') === $src ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $src)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lead Status</label>
                            <select name="lead_status" class="form-select">
                                <?php foreach (['new','contacted','qualified','proposal_sent','won','lost'] as $ls): ?>
                                <option value="<?php echo $ls; ?>" <?php echo ($_POST['lead_status'] ?? $lead['lead_status'] ?? 'new') === $ls ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $ls)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <?php foreach (['low','medium','high'] as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo ($_POST['priority'] ?? $lead['priority'] ?? 'medium') === $p ? 'selected' : ''; ?>><?php echo ucfirst($p); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Follow-up Date</label>
                            <input type="date" name="follow_up_date" class="form-control" value="<?php echo htmlspecialchars($_POST['follow_up_date'] ?? ($lead['follow_up_date'] ? substr($lead['follow_up_date'], 0, 10) : '')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_user_id" class="form-select">
                                <option value="">Unassigned</option>
                                <?php foreach ($admins as $a): ?>
                                <option value="<?php echo $a['id']; ?>" <?php echo ($_POST['assigned_user_id'] ?? $lead['assigned_user_id'] ?? '') == $a['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['full_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Message / Notes</label>
                            <textarea name="message" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['message'] ?? $lead['message'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-teal"><i class="bi bi-check-lg me-1"></i><?php echo $editId ? 'Update Lead' : 'Create Lead'; ?></button>
                        <a href="<?php echo $editId ? 'view.php?id=' . $editId : 'index.php'; ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($editId): ?>
    <div class="col-lg-4">
        <div class="card form-card mb-4">
            <div class="card-header">Add Note</div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="full_name" value="<?php echo htmlspecialchars($lead['full_name']); ?>">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($lead['email']); ?>">
                    <textarea name="note" class="form-control mb-2" rows="3" placeholder="Enter note..."></textarea>
                    <button type="submit" class="btn btn-teal btn-sm w-100">Add Note</button>
                </form>
            </div>
        </div>
        <?php if (!empty($notes)): ?>
        <div class="card form-card">
            <div class="card-header">Activity Timeline</div>
            <div class="card-body">
                <div class="activity-timeline">
                    <?php foreach ($notes as $note): ?>
                    <div class="activity-item">
                        <div class="activity-header">
                            <strong><?php echo htmlspecialchars($note['user_name'] ?? 'System'); ?></strong>
                            <span class="time"><?php echo time_ago($note['created_at']); ?></span>
                        </div>
                        <div class="activity-body"><?php echo nl2br(htmlspecialchars($note['note'])); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
