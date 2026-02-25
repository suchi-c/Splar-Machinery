<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

// Handle Mark as Read
if (isset($_GET['mark_read'])) {
    $id = $_GET['mark_read'];
    
    $stmt = $pdo->prepare("UPDATE enquiries SET status = 'Read' WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: enquiries.php?msg=updated");
    exit();
}

// Fetch Enquiries
$stmt = $pdo->query("SELECT * FROM enquiries ORDER BY submitted_at DESC");
$enquiries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Messages Inbox</h1>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <p class="alert alert-success">
            Status updated successfully.
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Sender</th>
                <th>Subject & Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($enquiries as $msg): ?>
            <tr>
                <td style="width: 120px;"><?php echo date('M j, Y', strtotime($msg['submitted_at'])); ?></td>
                <td style="width: 200px;">
                    <strong><?php echo htmlspecialchars($msg['name']); ?></strong><br>
                    <span style="font-size: 0.9rem; color: #666;"><?php echo htmlspecialchars($msg['email']); ?></span><br>
                    <span style="font-size: 0.9rem; color: #666;"><?php echo htmlspecialchars($msg['phone']); ?></span>
                </td>
                <td>
                    <strong><?php echo htmlspecialchars($msg['subject']); ?></strong>
                    <p style="margin-top: 5px; color: #444;"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                </td>
                <td style="width: 100px;">
                    <?php if ($msg['status'] == 'New'): ?>
                        <span class="status-badge status-new">New</span>
                    <?php else: ?>
                        <span class="status-badge status-read">Read</span>
                    <?php endif; ?>
                </td>
                <td style="width: 120px;">
                    <?php if ($msg['status'] == 'New'): ?>
                        <a href="enquiries.php?mark_read=<?php echo $msg['id']; ?>" class="action-btn btn-edit">Mark Read</a>
                    <?php else: ?>
                        <span style="color: #ccc;">No Action</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
