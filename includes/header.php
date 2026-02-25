<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Splar Machinery - Leading Manufacturer</title>
    <link rel="icon" href="assets/images/logo2.png" type="image/png">
    <link rel="shortcut icon" href="assets/images/logo2.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <div class="container navbar">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/logo2.png" alt="Solar Machinery Logo">
                </a>
            </div>
            <div class="menu-toggle" id="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
            <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
            <ul class="nav-links">
                <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About Us</a></li>
                <li>
                    <a href="products.php" class="<?php echo ($current_page == 'products.php' || $current_page == 'product-details.php') ? 'active' : ''; ?>">Products</a>
                    <ul class="dropdown-menu">
                        <li><a href="products.php?cat=solar">Solar Panel Machinery</a></li>
                        <li><a href="products.php?cat=capacitor">Capacitor Manufacturing Machinery</a></li>
                        <li><a href="products.php?cat=laser">Laser Technology Machine</a></li>
                        <li><a href="products.php?cat=automation">Automation Machines</a></li>
                    </ul>
                </li>
                <li><a href="blogs.php" class="<?php echo $current_page == 'blogs.php' ? 'active' : ''; ?>">Blogs</a></li>
                <li><a href="customers.php" class="<?php echo $current_page == 'customers.php' ? 'active' : ''; ?>">Customers</a></li>
                <li><a href="careers.php" class="<?php echo $current_page == 'careers.php' ? 'active' : ''; ?>">Careers</a></li>
                <li><a href="contact.php" class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact Us</a></li>
            </ul>
        </div>
    </header>