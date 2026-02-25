<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<?php
// Get category from URL, default to 'all'
$category_slug = isset($_GET['cat']) ? $_GET['cat'] : 'all';

// Fetch all categories for filter buttons
try {
    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $all_categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    $all_categories = [];
}

// Prepare SQL query based on category
$products = [];
$page_title = "Our Products";

try {
    if ($category_slug == 'all') {
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        $products = $stmt->fetchAll();
    } else {
        // Find category name from slug
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE slug = ?");
        $stmt->execute([$category_slug]);
        $cat_row = $stmt->fetch();
        
        if ($cat_row) {
            $category_name = $cat_row['name'];
            $page_title = $category_name . " Machinery";
            
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
            $stmt->execute([$category_name]);
            $products = $stmt->fetchAll();
        } else {
            // Category not found, show no products or redirect
            $products = [];
        }
    }
} catch (PDOException $e) {
    // Log error
}
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($page_title); ?></h1>
            <p>Explore our cutting-edge manufacturing solutions.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <!-- Dynamic Category Filter Links -->
            <div style="text-align: center; margin-bottom: 40px; display: flex; justify-content: center; flex-wrap: wrap;">
                <a href="products.php" class="btn"
                    style="background: <?php echo $category_slug == 'all' ? 'var(--dark-color)' : '#ccc'; ?>; margin: 5px;">All</a>
                
                <?php foreach ($all_categories as $cat): ?>
                    <a href="products.php?cat=<?php echo htmlspecialchars($cat['slug']); ?>" class="btn"
                        style="background: <?php echo $category_slug == $cat['slug'] ? 'var(--dark-color)' : '#ccc'; ?>; margin: 5px;">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="product-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-img" style="background-image: url('<?php echo !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/images/hero1.png'; ?>');"></div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p><?php echo substr(htmlspecialchars($product['description']), 0, 80) . '...'; ?></p>
                                <a href="product-details.php?id=<?php echo $product['id']; ?>" class="btn">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; background: #f9f9f9; border-radius: 8px; color: #666;">
                        <h3>No products found in this category.</h3>
                        <p>We are constantly updating our inventory. Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>