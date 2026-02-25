<?php
require_once 'includes/db.php';

// ---------------------------------------------------------------
// Handle application form submission
// ---------------------------------------------------------------
$success_msg = '';
$error_msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_job_id'])) {
    $job_id = (int) $_POST['apply_job_id'];
    $name   = trim($_POST['applicant_name'] ?? '');
    $email  = trim($_POST['applicant_email'] ?? '');
    $phone  = trim($_POST['applicant_phone'] ?? '');

    // Basic validation
    if (!$name || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Please fill in all required fields with a valid email.';
    } elseif (empty($_FILES['resume']['name'])) {
        $error_msg = 'Please upload your Resume / CV.';
    } else {
        $allowed_ext = ['pdf', 'doc', 'docx'];
        $file_ext    = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $error_msg = 'Only PDF, DOC, and DOCX files are accepted for resumes.';
        } elseif ($_FILES['resume']['size'] > 5 * 1024 * 1024) {
            $error_msg = 'Resume file size must be under 5 MB.';
        } else {
            $upload_dir  = __DIR__ . '/uploads/resumes/';
            $unique_name = time() . '_' . mt_rand(1000, 9999) . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['resume']['name']);
            $dest        = $upload_dir . $unique_name;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($_FILES['resume']['tmp_name'], $dest)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO applications (job_id, name, email, phone, resume_path) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$job_id, $name, $email, $phone, 'uploads/resumes/' . $unique_name]);
                    $success_msg = 'Your application has been submitted successfully! We will be in touch.';
                } catch (PDOException $e) {
                    $error_msg = 'Database error. Please try again later.';
                }
            } else {
                $error_msg = 'Failed to upload resume. Please try again.';
            }
        }
    }
}

// ---------------------------------------------------------------
// Fetch active jobs
// ---------------------------------------------------------------
try {
    $stmt = $pdo->query("SELECT * FROM jobs WHERE status = 'Active' ORDER BY posted_at DESC");
    $jobs = $stmt->fetchAll();
} catch (PDOException $e) {
    $jobs = [];
}
?>
<?php include 'includes/header.php'; ?>

<style>
/* ── Careers Page Styles ── */
.careers-hero {
    background: linear-gradient(135deg, #0a1628 0%, #1a3a5c 60%, #0d2b45 100%);
    padding: 90px 0 60px;
    text-align: center;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.careers-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('assets/images/pattern.png') repeat;
    opacity: 0.04;
}
.careers-hero h1 {
    font-size: 2.8rem;
    font-weight: 800;
    margin-bottom: 12px;
    letter-spacing: -0.5px;
}
.careers-hero h1 span { color: #f39c12; }
.careers-hero p {
    font-size: 1.15rem;
    opacity: 0.82;
    max-width: 560px;
    margin: 0 auto;
}
.careers-hero .hero-badge {
    display: inline-block;
    background: rgba(243,156,18,0.15);
    border: 1px solid rgba(243,156,18,0.4);
    color: #f39c12;
    padding: 6px 18px;
    border-radius: 50px;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.careers-section {
    padding: 70px 0 80px;
    background: #f5f7fa;
}
.careers-section .container { max-width: 900px; }

/* Flash messages */
.flash { padding: 14px 20px; border-radius: 8px; margin-bottom: 28px; font-weight: 500; }
.flash.success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
.flash.error   { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }

/* No jobs state */
.no-jobs {
    text-align: center;
    padding: 60px 20px;
    color: #777;
}
.no-jobs i { font-size: 3rem; color: #ccc; margin-bottom: 16px; display: block; }

/* Job Card / Accordion */
.job-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.07);
    margin-bottom: 20px;
    overflow: hidden;
    border: 1px solid #e8ecf0;
    transition: box-shadow 0.25s ease, transform 0.2s ease;
}
.job-card:hover { box-shadow: 0 6px 28px rgba(0,0,0,0.11); transform: translateY(-2px); }
.job-card.open  { border-color: #f39c12; }

.job-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 22px 26px;
    cursor: pointer;
    user-select: none;
    gap: 16px;
}
.job-header-left { flex: 1; }
.job-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1a2a3a;
    margin: 0 0 6px;
}
.job-meta { display: flex; flex-wrap: wrap; gap: 12px; }
.job-meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.82rem;
    color: #666;
    font-weight: 500;
}
.job-meta i { color: #f39c12; }

.job-badge {
    padding: 5px 14px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}
.badge-fulltime  { background: #e8f5e9; color: #2e7d32; }
.badge-parttime  { background: #e3f2fd; color: #1565c0; }
.badge-contract  { background: #fff3e0; color: #e65100; }
.badge-internship{ background: #f3e5f5; color: #6a1b9a; }

.job-toggle-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f5f7fa;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, transform 0.3s;
    flex-shrink: 0;
}
.job-card.open .job-toggle-icon {
    background: #f39c12;
    color: #fff;
    transform: rotate(180deg);
}

.job-body {
    display: none;
    padding: 0 26px 26px;
    border-top: 1px solid #f0f0f0;
    animation: slideDown 0.25s ease;
}
.job-card.open .job-body { display: block; }
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.job-description {
    color: #444;
    line-height: 1.8;
    font-size: 0.95rem;
    margin: 18px 0 22px;
    white-space: pre-line;
}

.btn-apply {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: #fff;
    border: none;
    padding: 11px 26px;
    border-radius: 8px;
    font-size: 0.92rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    letter-spacing: 0.3px;
}
.btn-apply:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(243,156,18,0.4); }

/* Application Form */
.apply-form-wrapper {
    display: none;
    margin-top: 22px;
    background: #f8fafc;
    border-radius: 10px;
    padding: 26px 28px;
    border: 1px dashed #d1dce8;
    animation: slideDown 0.25s ease;
}
.apply-form-wrapper h4 {
    color: #1a2a3a;
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 18px;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}
@media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="tel"],
.form-group input[type="file"] {
    padding: 10px 14px;
    border: 1px solid #d0d9e0;
    border-radius: 7px;
    font-size: 0.93rem;
    background: #fff;
    color: #333;
    transition: border-color 0.2s;
    outline: none;
}
.form-group input:focus { border-color: #f39c12; box-shadow: 0 0 0 3px rgba(243,156,18,0.12); }
.form-group .hint { font-size: 0.75rem; color: #999; }
.submit-row {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 8px;
}
.btn-submit {
    background: linear-gradient(135deg, #1a3a5c, #0a1628);
    color: #fff;
    border: none;
    padding: 11px 28px;
    border-radius: 8px;
    font-size: 0.92rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(26,58,92,0.3); }
.btn-cancel-form {
    background: transparent;
    border: none;
    color: #888;
    cursor: pointer;
    font-size: 0.88rem;
    text-decoration: underline;
}

/* Why Join Us */
.why-section {
    background: #fff;
    padding: 70px 0;
}
.why-section h2 {
    text-align: center;
    font-size: 2rem;
    font-weight: 800;
    color: #1a2a3a;
    margin-bottom: 8px;
}
.why-section .sub {
    text-align: center;
    color: #777;
    margin-bottom: 50px;
    font-size: 1rem;
}
.perks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 26px;
    max-width: 960px;
    margin: 0 auto;
}
.perk-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 30px 24px;
    text-align: center;
    border: 1px solid #eaedf0;
    transition: all 0.25s;
}
.perk-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); border-color: #f39c12; }
.perk-icon {
    width: 58px;
    height: 58px;
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    color: #fff;
    font-size: 1.3rem;
}
.perk-card h4 { font-size: 1rem; font-weight: 700; color: #1a2a3a; margin: 0 0 8px; }
.perk-card p  { font-size: 0.85rem; color: #777; line-height: 1.6; margin: 0; }
</style>

<!-- Hero -->
<section class="careers-hero">
    <div class="container">
        <div class="hero-badge">We're Hiring</div>
        <h1>Build the Future at <span>Splar Machinery</span></h1>
        <p>Join a team of passionate engineers and innovators driving the next generation of industrial machinery.</p>
    </div>
</section>

<!-- Why Join Us -->
<section class="why-section">
    <div class="container">
        <h2>Why Work With Us?</h2>
        <p class="sub">More than a job — a place to grow, innovate, and make an impact.</p>
        <div class="perks-grid">
            <div class="perk-card">
                <div class="perk-icon"><i class="fas fa-rocket"></i></div>
                <h4>Innovation-First</h4>
                <p>Work on cutting-edge solar and automation machinery that shapes the industry.</p>
            </div>
            <div class="perk-card">
                <div class="perk-icon"><i class="fas fa-graduation-cap"></i></div>
                <h4>Continuous Learning</h4>
                <p>Access to technical training, certifications, and leadership development programs.</p>
            </div>
            <div class="perk-card">
                <div class="perk-icon"><i class="fas fa-hand-holding-heart"></i></div>
                <h4>Great Benefits</h4>
                <p>Competitive salary, health insurance, PF, and performance-linked bonuses.</p>
            </div>
            <div class="perk-card">
                <div class="perk-icon"><i class="fas fa-users"></i></div>
                <h4>Collaborative Culture</h4>
                <p>A flat, inclusive team where every idea is heard and every person matters.</p>
            </div>
        </div>
    </div>
</section>

<!-- Job Listings -->
<section class="careers-section">
    <div class="container">

        <?php if ($success_msg): ?>
            <div class="flash success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="flash error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <?php if (empty($jobs)): ?>
            <div class="no-jobs">
                <i class="fas fa-briefcase"></i>
                <h3>No Open Positions Right Now</h3>
                <p>Check back soon — exciting opportunities are coming. You can also email your profile to <a href="mailto:careers@splarmachinery.com">careers@splarmachinery.com</a>.</p>
            </div>
        <?php else: ?>
            <?php
            $badge_map = [
                'Full-time'  => 'badge-fulltime',
                'Part-time'  => 'badge-parttime',
                'Contract'   => 'badge-contract',
                'Internship' => 'badge-internship',
            ];
            foreach ($jobs as $job):
                $badge_cls = $badge_map[$job['type']] ?? 'badge-fulltime';
                $posted    = date('M j, Y', strtotime($job['posted_at']));
            ?>
            <div class="job-card" id="job-card-<?= $job['id'] ?>">
                <!-- Accordion Header -->
                <div class="job-header" onclick="toggleJob(<?= $job['id'] ?>)">
                    <div class="job-header-left">
                        <h3 class="job-title"><?= htmlspecialchars($job['title']) ?></h3>
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?></span>
                            <span><i class="fas fa-calendar-alt"></i> Posted <?= $posted ?></span>
                        </div>
                    </div>
                    <span class="job-badge <?= $badge_cls ?>"><?= htmlspecialchars($job['type']) ?></span>
                    <div class="job-toggle-icon"><i class="fas fa-chevron-down"></i></div>
                </div>

                <!-- Accordion Body -->
                <div class="job-body">
                    <p class="job-description"><?= nl2br(htmlspecialchars($job['description'])) ?></p>

                    <!-- Apply Now Button -->
                    <button class="btn-apply" onclick="showApplyForm(<?= $job['id'] ?>)">
                        <i class="fas fa-paper-plane"></i> Apply Now
                    </button>

                    <!-- Application Form (hidden by default) -->
                    <div class="apply-form-wrapper" id="apply-form-<?= $job['id'] ?>">
                        <h4><i class="fas fa-user-edit"></i> &nbsp;Apply for: <?= htmlspecialchars($job['title']) ?></h4>
                        <form method="POST" enctype="multipart/form-data" action="careers.php#job-card-<?= $job['id'] ?>">
                            <input type="hidden" name="apply_job_id" value="<?= $job['id'] ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input type="text" name="applicant_name" placeholder="John Doe" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Address *</label>
                                    <input type="email" name="applicant_email" placeholder="john@example.com" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" name="applicant_phone" placeholder="+91 9876543210">
                                </div>
                                <div class="form-group">
                                    <label>Resume / CV *</label>
                                    <input type="file" name="resume" accept=".pdf,.doc,.docx" required>
                                    <span class="hint">PDF, DOC or DOCX — max 5 MB</span>
                                </div>
                            </div>
                            <div class="submit-row">
                                <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Submit Application</button>
                                <button type="button" class="btn-cancel-form" onclick="hideApplyForm(<?= $job['id'] ?>)">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
function toggleJob(id) {
    const card = document.getElementById('job-card-' + id);
    const isOpen = card.classList.contains('open');
    // Close all
    document.querySelectorAll('.job-card').forEach(c => c.classList.remove('open'));
    document.querySelectorAll('.apply-form-wrapper').forEach(f => f.style.display = 'none');
    // Open clicked if it was closed
    if (!isOpen) card.classList.add('open');
}
function showApplyForm(id) {
    const form = document.getElementById('apply-form-' + id);
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
}
function hideApplyForm(id) {
    document.getElementById('apply-form-' + id).style.display = 'none';
}

// Auto-open card if there's a hash in URL (after form submission)
window.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#job-card-')) {
        const id = hash.replace('#job-card-', '');
        const card = document.getElementById('job-card-' + id);
        if (card) {
            card.classList.add('open');
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    <?php if ($success_msg || $error_msg): ?>
    window.scrollTo({ top: 0, behavior: 'smooth' });
    <?php endif; ?>
});
</script>

<?php include 'includes/footer.php'; ?>
