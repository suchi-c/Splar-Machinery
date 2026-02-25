<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

// ── Filter by job ────────────────────────────────────────────
$filter_job_id = isset($_GET['job_id']) && is_numeric($_GET['job_id']) ? (int)$_GET['job_id'] : 0;

// ── Fetch all jobs for the filter dropdown ───────────────────
try {
    $stmt_jobs = $pdo->query("SELECT id, title FROM jobs ORDER BY title ASC");
    $all_jobs  = $stmt_jobs->fetchAll();
} catch (PDOException $e) {
    $all_jobs = [];
}

// ── Fetch applications (with job title via JOIN) ─────────────
try {
    if ($filter_job_id > 0) {
        $stmt = $pdo->prepare("
            SELECT a.*, j.title AS job_title
            FROM applications a
            JOIN jobs j ON j.id = a.job_id
            WHERE a.job_id = ?
            ORDER BY a.applied_at DESC
        ");
        $stmt->execute([$filter_job_id]);
    } else {
        $stmt = $pdo->query("
            SELECT a.*, j.title AS job_title
            FROM applications a
            JOIN jobs j ON j.id = a.job_id
            ORDER BY a.applied_at DESC
        ");
    }
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $applications = [];
}
?>
<?php include 'includes/header.php'; ?>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <header class="dashboard-header">
        <h1><i class="fas fa-inbox" style="color:#f39c12;margin-right:10px;"></i>Application Inbox</h1>
        <div class="user-info">Welcome, <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></strong></div>
    </header>

    <!-- ── Filter Bar ────────────────────────────────────────── -->
    <div style="background:#fff;border-radius:12px;padding:18px 24px;box-shadow:0 2px 14px rgba(0,0,0,0.07);border:1px solid #eaecf0;margin-bottom:26px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
        <label style="font-size:0.85rem;font-weight:700;color:#555;white-space:nowrap;"><i class="fas fa-filter" style="color:#f39c12;"></i> &nbsp;Filter by Position:</label>
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <select name="job_id" onchange="this.form.submit()" style="padding:9px 14px;border:1px solid #d0d9e0;border-radius:7px;font-size:0.9rem;background:#fff;min-width:220px;">
                <option value="0">— All Positions —</option>
                <?php foreach ($all_jobs as $j): ?>
                    <option value="<?= $j['id'] ?>" <?= $filter_job_id == $j['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($j['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($filter_job_id): ?>
                <a href="applications.php" style="font-size:0.82rem;color:#e74c3c;text-decoration:none;"><i class="fas fa-times"></i> Clear Filter</a>
            <?php endif; ?>
        </form>
        <span style="margin-left:auto;font-size:0.85rem;color:#888;">
            Showing <strong><?= count($applications) ?></strong> application<?= count($applications) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <!-- ── Applications Table ────────────────────────────────── -->
    <div style="background:#fff;border-radius:12px;box-shadow:0 2px 14px rgba(0,0,0,0.07);border:1px solid #eaecf0;overflow:hidden;">
        <?php if (empty($applications)): ?>
            <div style="text-align:center;padding:60px 20px;color:#aaa;">
                <i class="fas fa-folder-open" style="font-size:2.5rem;margin-bottom:14px;display:block;"></i>
                <p style="font-size:1rem;margin:0;">No applications found<?= $filter_job_id ? ' for this position' : '' ?>.</p>
            </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:13px 20px;text-align:left;font-size:0.77rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">#</th>
                    <th style="padding:13px 20px;text-align:left;font-size:0.77rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Candidate</th>
                    <th style="padding:13px 20px;text-align:left;font-size:0.77rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Applied For</th>
                    <th style="padding:13px 20px;text-align:left;font-size:0.77rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Date Applied</th>
                    <th style="padding:13px 20px;text-align:left;font-size:0.77rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Resume</th>
                    <th style="padding:13px 20px;text-align:left;font-size:0.77rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Details</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $i => $app): ?>
                <tr style="border-top:1px solid #f0f4f8;" onmouseover="this.style.background='#fafcff'" onmouseout="this.style.background=''">
                    <td style="padding:14px 20px;font-size:0.85rem;color:#aaa;"><?= $i + 1 ?></td>
                    <td style="padding:14px 20px;">
                        <button onclick="openModal(<?= $app['id'] ?>)"
                            style="background:none;border:none;padding:0;cursor:pointer;text-align:left;">
                            <span style="display:flex;align-items:center;gap:10px;">
                                <span style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#f39c12,#e67e22);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.9rem;flex-shrink:0;">
                                    <?= strtoupper(substr($app['name'], 0, 1)) ?>
                                </span>
                                <span>
                                    <strong style="color:#1a2a3a;font-size:0.93rem;"><?= htmlspecialchars($app['name']) ?></strong><br>
                                    <small style="color:#999;font-size:0.78rem;"><?= htmlspecialchars($app['email']) ?></small>
                                </span>
                            </span>
                        </button>
                    </td>
                    <td style="padding:14px 20px;font-size:0.9rem;color:#444;"><?= htmlspecialchars($app['job_title']) ?></td>
                    <td style="padding:14px 20px;font-size:0.85rem;color:#777;"><?= date('M j, Y', strtotime($app['applied_at'])) ?></td>
                    <td style="padding:14px 20px;">
                        <a href="../<?= htmlspecialchars($app['resume_path']) ?>" download
                           style="display:inline-flex;align-items:center;gap:6px;background:#e3f2fd;color:#1565c0;padding:7px 14px;border-radius:6px;font-size:0.8rem;font-weight:600;text-decoration:none;">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </td>
                    <td style="padding:14px 20px;">
                        <button onclick="openModal(<?= $app['id'] ?>)"
                            style="background:#f5f7fa;border:1px solid #e0e6ed;color:#555;padding:7px 14px;border-radius:6px;font-size:0.8rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>

                <!-- Hidden modal data (JSON encoded per row) -->
                <script>
                window.__appData = window.__appData || {};
                window.__appData[<?= $app['id'] ?>] = {
                    name:      <?= json_encode($app['name']) ?>,
                    email:     <?= json_encode($app['email']) ?>,
                    phone:     <?= json_encode($app['phone'] ?: 'Not provided') ?>,
                    job_title: <?= json_encode($app['job_title']) ?>,
                    date:      <?= json_encode(date('F j, Y, g:i a', strtotime($app['applied_at']))) ?>,
                    resume:    <?= json_encode('../' . $app['resume_path']) ?>
                };
                </script>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div><!-- /main-content -->

<!-- ── Detail Modal ─────────────────────────────────────────── -->
<div id="app-modal-overlay" onclick="closeModal()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9998;backdrop-filter:blur(3px);"></div>
<div id="app-modal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:16px;padding:32px;width:90%;max-width:500px;z-index:9999;box-shadow:0 20px 60px rgba(0,0,0,0.25);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <h3 id="modal-title" style="margin:0;color:#1a2a3a;font-size:1.1rem;"></h3>
        <button onclick="closeModal()" style="background:none;border:none;font-size:1.4rem;color:#aaa;cursor:pointer;line-height:1;">&times;</button>
    </div>
    <div id="modal-body" style="display:flex;flex-direction:column;gap:14px;"></div>
    <div style="margin-top:24px;display:flex;gap:10px;flex-wrap:wrap;">
        <a id="modal-resume-link" href="#" download style="display:inline-flex;align-items:center;gap:7px;background:linear-gradient(135deg,#f39c12,#e67e22);color:#fff;padding:10px 22px;border-radius:8px;font-size:0.88rem;font-weight:700;text-decoration:none;">
            <i class="fas fa-download"></i> Download Resume
        </a>
        <button onclick="closeModal()" style="background:#f5f7fa;border:1px solid #d0d9e0;color:#666;padding:10px 20px;border-radius:8px;font-size:0.88rem;font-weight:600;cursor:pointer;">Close</button>
    </div>
</div>

<style>
.modal-row { display:flex; align-items:flex-start; gap:12px; padding:12px 14px; background:#f8fafc; border-radius:8px; border:1px solid #eaedf0; }
.modal-row i { color:#f39c12; width:18px; flex-shrink:0; margin-top:2px; }
.modal-row .ml { display:flex; flex-direction:column; gap:2px; }
.modal-row .ml-label { font-size:0.73rem; text-transform:uppercase; letter-spacing:.5px; color:#aaa; font-weight:700; }
.modal-row .ml-value { font-size:0.93rem; color:#1a2a3a; font-weight:600; word-break:break-word; }
</style>

<script>
function openModal(id) {
    const data = window.__appData[id];
    if (!data) return;
    document.getElementById('modal-title').textContent = '👤 ' + data.name;
    document.getElementById('modal-resume-link').href = data.resume;
    const body = document.getElementById('modal-body');
    body.innerHTML = `
        <div class="modal-row"><i class="fas fa-user"></i><div class="ml"><span class="ml-label">Full Name</span><span class="ml-value">${escHtml(data.name)}</span></div></div>
        <div class="modal-row"><i class="fas fa-envelope"></i><div class="ml"><span class="ml-label">Email</span><span class="ml-value">${escHtml(data.email)}</span></div></div>
        <div class="modal-row"><i class="fas fa-phone"></i><div class="ml"><span class="ml-label">Phone</span><span class="ml-value">${escHtml(data.phone)}</span></div></div>
        <div class="modal-row"><i class="fas fa-briefcase"></i><div class="ml"><span class="ml-label">Applied For</span><span class="ml-value">${escHtml(data.job_title)}</span></div></div>
        <div class="modal-row"><i class="fas fa-calendar-alt"></i><div class="ml"><span class="ml-label">Date Applied</span><span class="ml-value">${escHtml(data.date)}</span></div></div>
    `;
    document.getElementById('app-modal-overlay').style.display = 'block';
    document.getElementById('app-modal').style.display        = 'block';
}
function closeModal() {
    document.getElementById('app-modal-overlay').style.display = 'none';
    document.getElementById('app-modal').style.display         = 'none';
}
function escHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
</script>

<?php include 'includes/footer.php'; ?>
