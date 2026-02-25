<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

// Fetch Statistics
try {
    // 1. Total Products
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $total_products = $stmt->fetchColumn();

    // 2. New Enquiries (Assuming 'New' status)
    $stmt = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'New'");
    $new_enquiries = $stmt->fetchColumn();

    // 3. Blog Posts
    $stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
    $total_blogs = $stmt->fetchColumn();

    // 4. Active Customers
    $stmt = $pdo->query("SELECT COUNT(*) FROM customers");
    $total_customers = $stmt->fetchColumn();

    // 5. Recent Activity (Latest 5 Enquiries)
    $stmt = $pdo->query("SELECT * FROM enquiries ORDER BY submitted_at DESC LIMIT 5");
    $recent_enquiries = $stmt->fetchAll();

} catch (PDOException $e) {
    // Handle error
    $total_products = $total_blogs = $total_customers = $new_enquiries = 0;
    $recent_enquiries = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body>

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <header class="dashboard-header">
            <h1>Dashboard</h1>
            <div class="user-info">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></strong>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card card-blue">
                <div class="stat-info">
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Products</p>
                </div>
                <div class="stat-icon"><i class="fas fa-box"></i></div>
            </div>
            
            <div class="stat-card card-orange">
                <div class="stat-info">
                    <h3><?php echo $new_enquiries; ?></h3>
                    <p>New Enquiries</p>
                </div>
                <div class="stat-icon"><i class="fas fa-envelope"></i></div>
            </div>

            <div class="stat-card card-green">
                <div class="stat-info">
                    <h3><?php echo $total_blogs; ?></h3>
                    <p>Blog Posts</p>
                </div>
                <div class="stat-icon"><i class="fas fa-pen-nib"></i></div>
            </div>

            <div class="stat-card card-red">
                <div class="stat-info">
                    <h3><?php echo $total_customers; ?></h3>
                    <p>Active Customers</p>
                </div>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <div class="section-header">
                <h3>Recent Enquiries</h3>
            </div>
            <?php if (count($recent_enquiries) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_enquiries as $enquiry): ?>
                            <tr>
                                <td><?php echo date('M j, Y', strtotime($enquiry['submitted_at'])); ?></td>
                                <td><?php echo htmlspecialchars($enquiry['name']); ?></td>
                                <td><?php echo htmlspecialchars($enquiry['subject']); ?></td>
                                <td>
                                    <?php 
                                        $statusClass = 'status-new';
                                        if ($enquiry['status'] == 'Read') $statusClass = 'status-read';
                                        if ($enquiry['status'] == 'Replied') $statusClass = 'status-replied';
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo $enquiry['status']; ?></span>
                                </td>
                                <td><a href="enquiries.php" style="color: #3498db; text-decoration: none;">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="padding: 20px; text-align: center; color: #777;">No recent enquiries found.</p>
            <?php endif; ?>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
