<nav class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/images/logo2.png" alt="SPLAR Logo" class="sidebar-logo">
        <h2>Admin Panel</h2>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li><a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>"><i class="fas fa-tags"></i> <span>Categories</span></a></li>
        <li><a href="blogs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'blogs.php' ? 'active' : ''; ?>"><i class="fas fa-pen-nib"></i> <span>Blogs</span></a></li>
        <li><a href="customers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> <span>Customers</span></a></li>
        <li><a href="enquiries.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'enquiries.php' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> <span>Enquiries</span></a></li>
        <li><a href="manage_jobs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_jobs.php' ? 'active' : ''; ?>"><i class="fas fa-briefcase"></i> <span>Manage Jobs</span></a></li>
        <li><a href="applications.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>"><i class="fas fa-inbox"></i> <span>Applications</span></a></li>
        <li><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</nav>
