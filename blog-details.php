<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<?php
// Get blog ID from URL
$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$blog = null;

if ($blog_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = :id");
        $stmt->execute([':id' => $blog_id]);
        $blog = $stmt->fetch();
    } catch (PDOException $e) {
        // Log error
    }
}

// Redirect if not found
if (!$blog) {
    echo "<script>window.location.href = 'blogs.php';</script>";
    exit;
}
?>

<main>
    <section class="section">
        <div class="container">
            <a href="blogs.php" class="back-link">&larr; Back to Blogs</a>
            
            <div class="blog-post-wrapper">
                <!-- Blog Header -->
                <div class="blog-header">
                    <h1 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
                    <div class="blog-meta">
                        <span><i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($blog['created_at'])); ?></span>
                        <!-- Author removed as it wasn't in DB schema primarily, can be added back if needed or static 'Admin' -->
                    </div>
                </div>

                <!-- Featured Image -->
                <?php if (!empty($blog['image_url'])): ?>
                    <div class="blog-image-container">
                        <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="blog-featured-image">
                    </div>
                <?php endif; ?>

                <!-- Content -->
                <div class="blog-content">
                    <?php echo $blog['content']; // Outputting raw HTML from rich text editor ?>
                </div>

                <!-- Share/Footer of Post -->
                <div class="blog-footer-actions">
                    <a href="contact.php?subject=Enquiry regarding blog: <?php echo urlencode($blog['title']); ?>" class="btn">Have Questions? Contact Us</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
