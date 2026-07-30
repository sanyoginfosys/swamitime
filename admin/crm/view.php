<?php
$pageTitle = 'Lead Details';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/security.php';
Auth::requireLogin();

$db = getDB();
$leadId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$leadId) { set_flash('error', 'Invalid lead ID.');     redirect(admin_url('crm-leads.php')); }

$stmt = $db->prepare("SELECT l.*, a.full_name as assigned_name FROM crm_leads l LEFT JOIN admins a ON l.assigned_user_id = a.id WHERE l.id = ?");
$stmt->execute([$leadId]);
$lead = $stmt->fetch();
if (!$lead) { set_flash('error', 'Lead not found.');     redirect(admin_url('crm-leads.php')); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Security::validate_csrf();
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_note') {
            $noteText = sanitize($_POST['note'] ?? '');
            if (trim($noteText) !== '') {
                $noteStmt = $db->prepare("INSERT INTO crm_notes (lead_id, user_id, note) VALUES (?, ?, ?)");
                $noteStmt->execute([$leadId, $_SESSION['admin_id'] ?? null, $noteText]);
                set_flash('success', 'Note added.');
            }
        } elseif ($_POST['action'] === 'change_status') {
            $newStatus = sanitize($_POST['new_status'] ?? '');
            $validStatuses = ['new','contacted','qualified','proposal_sent','won','lost'];
            if (in_array($newStatus, $validStatuses)) {
                $convertedAt = null;
                if ($newStatus === 'won' && $lead['lead_status'] !== 'won') {
                    $convertedAt = date('Y-m-d H:i:s');
                }
                $stmt = $db->prepare("UPDATE crm_leads SET lead_status=?, converted_at=COALESCE(?, converted_at), updated_at=NOW() WHERE id=?");
                $stmt->execute([$newStatus, $convertedAt, $leadId]);
                set_flash('success', 'Status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)) . '.');
            }
        }
    }
    redirect(admin_url('crm/view.php?id=' . $leadId));
}

$noteStmt = $db->prepare("SELECT cn.*, a.full_name as user_name FROM crm_notes cn LEFT JOIN admins a ON cn.user_id = a.id WHERE cn.lead_id = ? ORDER BY cn.created_at DESC");
$noteStmt->execute([$leadId]);
$notes = $noteStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <div class="card form-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Lead Details</span>
                <div>
                    <a href="edit.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
                    <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-envelope me-1"></i>Send Email</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <p class="fw-semibold"><?php echo htmlspecialchars($lead['full_name']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Company</label>
                        <p><?php echo htmlspecialchars($lead['company_name'] ?: '—'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <p><a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>"><?php echo htmlspecialchars($lead['email']); ?></a></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <p><?php echo htmlspecialchars($lead['phone'] ?: '—'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Service Interested</label>
                        <p><?php echo htmlspecialchars($lead['service_interested'] ?: '—'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Lead Source</label>
                        <p><?php echo ucfirst(str_replace('_', ' ', $lead['lead_source'])); ?></p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Priority</label>
                        <p><span class="badge badge-priority badge-<?php echo $lead['priority']; ?>"><?php echo ucfirst($lead['priority']); ?></span></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Assigned To</label>
                        <p><?php echo htmlspecialchars($lead['assigned_name'] ?? 'Unassigned'); ?></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Follow-up Date</label>
                        <p><?php echo $lead['follow_up_date'] ? format_date($lead['follow_up_date'], 'd M Y') : '—'; ?></p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Created</label>
                        <p><?php echo format_date($lead['created_at'], 'd M Y H:i'); ?></p>
                    </div>
                    <?php if ($lead['converted_at']): ?>
                    <div class="col-md-4">
                        <label class="form-label">Converted</label>
                        <p class="text-success fw-semibold"><?php echo format_date($lead['converted_at'], 'd M Y H:i'); ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="col-12">
                        <label class="form-label">Message</label>
                        <div class="p-3 bg-light rounded"><?php echo nl2br(htmlspecialchars($lead['message'] ?: 'No message provided.')); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header">Activity Timeline</div>
            <div class="card-body">
                <?php if (empty($notes)): ?>
                <p class="text-muted mb-0">No notes yet.</p>
                <?php else: ?>
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
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card form-card mb-4">
            <div class="card-header">Current Status</div>
            <div class="card-body text-center">
                <span class="badge badge-status badge-<?php echo $lead['lead_status']; ?> fs-6 px-3 py-2"><?php echo ucfirst(str_replace('_', ' ', $lead['lead_status'])); ?></span>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header">Change Status</div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="change_status">
                    <?php foreach (['new','contacted','qualified','proposal_sent','won','lost'] as $s): ?>
                    <button type="submit" name="new_status" value="<?php echo $s; ?>" class="btn btn-sm w-100 mb-2 <?php echo $lead['lead_status'] === $s ? 'btn-teal' : 'btn-outline-secondary'; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $s)); ?>
                    </button>
                    <?php endforeach; ?>
                </form>
            </div>
        </div>

        <div class="card form-card mb-4">
            <div class="card-header">Add Note</div>
            <div class="card-body">
                <form method="POST">
                    <?php echo Security::csrf_field(); ?>
                    <input type="hidden" name="action" value="add_note">
                    <textarea name="note" class="form-control mb-2" rows="3" placeholder="Enter note..." required></textarea>
                    <button type="submit" class="btn btn-teal btn-sm w-100">Add Note</button>
                </form>
            </div>
        </div>

        <div class="card form-card">
            <div class="card-header">Quick Actions</div>
            <div class="card-body">
                <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>?subject=Following up - SWAMITIME SOLUTIONS LTD" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                    <i class="bi bi-envelope me-1"></i>Send Email
                </a>
                <a href="edit.php?id=<?php echo $lead['id']; ?>" class="btn btn-outline-primary btn-sm w-100 mb-2">
                    <i class="bi bi-pencil me-1"></i>Edit Lead
                </a>
                <a href="#" class="btn btn-outline-secondary btn-sm w-100" onclick="alert('Schedule follow-up feature coming soon.');return false;">
                    <i class="bi bi-calendar-plus me-1"></i>Schedule Follow-up
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
