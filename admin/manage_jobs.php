<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

$page_msg   = '';
$page_error = '';
$edit_job   = null;

// ---------------------------------------------------------------
// Handle POST actions
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── ADD NEW JOB ──────────────────────────────────────────
    if ($action === 'add') {
        $title       = trim($_POST['title'] ?? '');
        $type        = $_POST['type'] ?? 'Full-time';
        $location    = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$title || !$location || !$description) {
            $page_error = 'All fields are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO jobs (title, type, location, description, status) VALUES (?, ?, ?, ?, 'Active')");
                $stmt->execute([$title, $type, $location, $description]);
                $page_msg = 'Job posted successfully!';
            } catch (PDOException $e) {
                $page_error = 'Error adding job: ' . $e->getMessage();
            }
        }
    }

    // ── UPDATE JOB ───────────────────────────────────────────
    elseif ($action === 'update') {
        $id          = (int) ($_POST['job_id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $type        = $_POST['type'] ?? 'Full-time';
        $location    = trim($_POST['location'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (!$id || !$title || !$location || !$description) {
            $page_error = 'All fields are required.';
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE jobs SET title=?, type=?, location=?, description=? WHERE id=?");
                $stmt->execute([$title, $type, $location, $description, $id]);
                $page_msg = 'Job updated successfully!';
            } catch (PDOException $e) {
                $page_error = 'Error updating job.';
            }
        }
    }

    // ── DELETE JOB ───────────────────────────────────────────
    elseif ($action === 'delete') {
        $id = (int) ($_POST['job_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM jobs WHERE id=?");
            $stmt->execute([$id]);
            $page_msg = 'Job deleted.';
        } catch (PDOException $e) {
            $page_error = 'Error deleting job.';
        }
    }

    // ── TOGGLE STATUS ────────────────────────────────────────
    elseif ($action === 'toggle_status') {
        $id         = (int) ($_POST['job_id'] ?? 0);
        $new_status = ($_POST['current_status'] === 'Active') ? 'Closed' : 'Active';
        try {
            $stmt = $pdo->prepare("UPDATE jobs SET status=? WHERE id=?");
            $stmt->execute([$new_status, $id]);
            $page_msg = "Job marked as <strong>{$new_status}</strong>.";
        } catch (PDOException $e) {
            $page_error = 'Error updating status.';
        }
    }
}

// ── GET JOB FOR EDITING (GET request) ────────────────────────
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    try {
        $stmt    = $pdo->prepare("SELECT * FROM jobs WHERE id=?");
        $stmt->execute([$edit_id]);
        $edit_job = $stmt->fetch();
    } catch (PDOException $e) {
        $edit_job = null;
    }
}

// ── FETCH ALL JOBS ────────────────────────────────────────────
try {
    $stmt = $pdo->query("SELECT * FROM jobs ORDER BY posted_at DESC");
    $jobs = $stmt->fetchAll();
} catch (PDOException $e) {
    $jobs = [];
}
?>
<?php include 'includes/header.php'; ?>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <header class="dashboard-header">
        <h1><i class="fas fa-briefcase" style="color:#f39c12;margin-right:10px;"></i>Manage Jobs</h1>
        <div class="user-info">Welcome, <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></strong></div>
    </header>

    <?php if ($page_msg): ?>
        <div style="background:#d4edda;color:#155724;border-left:4px solid #28a745;padding:12px 18px;border-radius:7px;margin-bottom:22px;font-weight:500;">
            <i class="fas fa-check-circle"></i> <?= $page_msg ?>
        </div>
    <?php endif; ?>
    <?php if ($page_error): ?>
        <div style="background:#f8d7da;color:#721c24;border-left:4px solid #dc3545;padding:12px 18px;border-radius:7px;margin-bottom:22px;font-weight:500;">
            <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($page_error) ?>
        </div>
    <?php endif; ?>

    <!-- ── POST / EDIT FORM ─────────────────────────────────── -->
    <div class="form-card" style="background:#fff;border-radius:12px;padding:28px 30px;box-shadow:0 2px 14px rgba(0,0,0,0.07);margin-bottom:32px;border:1px solid #eaecf0;">
        <h3 style="margin:0 0 22px;color:#1a2a3a;font-size:1.05rem;">
            <?= $edit_job ? '<i class="fas fa-edit" style="color:#f39c12"></i> Edit Job' : '<i class="fas fa-plus-circle" style="color:#f39c12"></i> Post a New Job' ?>
        </h3>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $edit_job ? 'update' : 'add' ?>">
            <?php if ($edit_job): ?>
                <input type="hidden" name="job_id" value="<?= $edit_job['id'] ?>">
            <?php endif; ?>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;">
                <div>
                    <label style="display:block;font-size:0.8rem;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Job Title *</label>
                    <input type="text" name="title" placeholder="e.g. Mechanical Engineer" style="width:100%;padding:10px 14px;border:1px solid #d0d9e0;border-radius:7px;font-size:0.93rem;box-sizing:border-box;" required value="<?= htmlspecialchars($edit_job['title'] ?? '') ?>">
                </div>
                <div>
                    <label style="display:block;font-size:0.8rem;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Job Type *</label>
                    <select name="type" style="width:100%;padding:10px 14px;border:1px solid #d0d9e0;border-radius:7px;font-size:0.93rem;background:#fff;box-sizing:border-box;">
                        <?php foreach (['Full-time','Part-time','Contract','Internship'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($edit_job['type'] ?? 'Full-time') === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:0.8rem;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Location *</label>
                <input type="text" name="location" placeholder="e.g. Pune, India" style="width:100%;padding:10px 14px;border:1px solid #d0d9e0;border-radius:7px;font-size:0.93rem;box-sizing:border-box;" required value="<?= htmlspecialchars($edit_job['location'] ?? '') ?>">
            </div>

            <div style="margin-bottom:22px;">
                <label style="display:block;font-size:0.8rem;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Job Description *</label>
                <textarea name="description" rows="7" placeholder="Describe the role, responsibilities, and requirements..." style="width:100%;padding:10px 14px;border:1px solid #d0d9e0;border-radius:7px;font-size:0.93rem;resize:vertical;box-sizing:border-box;" required><?= htmlspecialchars($edit_job['description'] ?? '') ?></textarea>
            </div>

            <div style="display:flex;gap:12px;align-items:center;">
                <button type="submit" style="background:linear-gradient(135deg,#f39c12,#e67e22);color:#fff;border:none;padding:11px 26px;border-radius:8px;font-size:0.93rem;font-weight:700;cursor:pointer;">
                    <i class="fas <?= $edit_job ? 'fa-save' : 'fa-plus' ?>"></i>
                    <?= $edit_job ? ' Save Changes' : ' Post Job' ?>
                </button>
                <?php if ($edit_job): ?>
                    <a href="manage_jobs.php" style="color:#888;text-decoration:underline;font-size:0.88rem;">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- ── JOB LIST TABLE ───────────────────────────────────── -->
    <div style="background:#fff;border-radius:12px;box-shadow:0 2px 14px rgba(0,0,0,0.07);border:1px solid #eaecf0;overflow:hidden;">
        <div style="padding:18px 24px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="margin:0;color:#1a2a3a;font-size:1rem;">All Job Postings (<?= count($jobs) ?>)</h3>
            <a href="../careers.php" target="_blank" style="font-size:0.82rem;color:#3498db;text-decoration:none;"><i class="fas fa-external-link-alt"></i> View Live Page</a>
        </div>

        <?php if (empty($jobs)): ?>
            <p style="padding:40px;text-align:center;color:#aaa;">No jobs posted yet. Use the form above to add one.</p>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:12px 20px;text-align:left;font-size:0.78rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Title</th>
                    <th style="padding:12px 20px;text-align:left;font-size:0.78rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Type</th>
                    <th style="padding:12px 20px;text-align:left;font-size:0.78rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Location</th>
                    <th style="padding:12px 20px;text-align:left;font-size:0.78rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Status</th>
                    <th style="padding:12px 20px;text-align:left;font-size:0.78rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Posted</th>
                    <th style="padding:12px 20px;text-align:left;font-size:0.78rem;text-transform:uppercase;letter-spacing:.6px;color:#888;font-weight:700;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($jobs as $job): ?>
                <tr style="border-top:1px solid #f0f4f8;transition:background 0.15s;" onmouseover="this.style.background='#fafcff'" onmouseout="this.style.background=''">
                    <td style="padding:14px 20px;font-weight:600;color:#1a2a3a;"><?= htmlspecialchars($job['title']) ?></td>
                    <td style="padding:14px 20px;font-size:0.88rem;color:#555;"><?= htmlspecialchars($job['type']) ?></td>
                    <td style="padding:14px 20px;font-size:0.88rem;color:#555;"><?= htmlspecialchars($job['location']) ?></td>
                    <td style="padding:14px 20px;">
                        <?php if ($job['status'] === 'Active'): ?>
                            <span style="background:#e8f5e9;color:#2e7d32;padding:4px 12px;border-radius:50px;font-size:0.75rem;font-weight:700;">● Active</span>
                        <?php else: ?>
                            <span style="background:#fce4ec;color:#b71c1c;padding:4px 12px;border-radius:50px;font-size:0.75rem;font-weight:700;">● Closed</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:14px 20px;font-size:0.82rem;color:#888;"><?= date('M j, Y', strtotime($job['posted_at'])) ?></td>
                    <td style="padding:14px 20px;">
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <!-- Edit -->
                            <a href="manage_jobs.php?edit=<?= $job['id'] ?>" style="display:inline-flex;align-items:center;gap:5px;background:#e3f2fd;color:#1565c0;padding:6px 13px;border-radius:6px;font-size:0.8rem;font-weight:600;text-decoration:none;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <!-- Toggle Status -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                <input type="hidden" name="current_status" value="<?= $job['status'] ?>">
                                <button type="submit" style="display:inline-flex;align-items:center;gap:5px;background:<?= $job['status']==='Active' ? '#fff3e0' : '#f3e5f5' ?>;color:<?= $job['status']==='Active' ? '#e65100' : '#6a1b9a' ?>;padding:6px 13px;border-radius:6px;font-size:0.8rem;font-weight:600;border:none;cursor:pointer;">
                                    <i class="fas fa-toggle-<?= $job['status']==='Active' ? 'on' : 'off' ?>"></i>
                                    <?= $job['status']==='Active' ? 'Close' : 'Reopen' ?>
                                </button>
                            </form>
                            <!-- Delete -->
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this job? All applications will also be removed.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                <button type="submit" style="display:inline-flex;align-items:center;gap:5px;background:#fce4ec;color:#b71c1c;padding:6px 13px;border-radius:6px;font-size:0.8rem;font-weight:600;border:none;cursor:pointer;">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div><!-- /main-content -->

<?php include 'includes/footer.php'; ?>
