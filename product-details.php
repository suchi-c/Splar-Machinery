<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<?php
// Get product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
    } catch (PDOException $e) {
        // Log error
    }
}

// Handle not found
if (!$product) {
    echo "<script>window.location.href = 'products.php';</script>";
    exit;
}
?>

<style>
    /* Product Details Specific Styles */
    .product-details-container {
        display: flex;
        flex-wrap: wrap;
        gap: 50px;
        align-items: flex-start;
        padding-top: 40px;
        padding-bottom: 60px;
    }

    .product-image-col {
        flex: 1;
        min-width: 350px;
    }

    .product-image-large {
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .product-info-col {
        flex: 1;
        min-width: 350px;
    }

    .product-category-badge {
        display: inline-block;
        background: #eee;
        color: #555;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .product-title {
        font-size: 2.2rem;
        margin-bottom: 20px;
        color: var(--dark-color);
    }

    .product-description {
        font-size: 1.1rem;
        line-height: 1.7;
        color: #555;
        margin-bottom: 30px;
    }

    .product-features h3 {
        font-size: 1.3rem;
        margin-bottom: 15px;
        color: var(--dark-color);
        border-bottom: 2px solid var(--primary-color);
        display: inline-block;
        padding-bottom: 5px;
    }

    /* Override feature lists depending on how they are stored (HTML or text) */
    .product-features ul {
        list-style: disc;
        padding-left: 20px;
        color: #555;
        margin-bottom: 30px;
    }

    .btn-enquire {
        display: inline-block;
        background: var(--primary-color);
        color: white;
        padding: 15px 35px;
        font-size: 1.1rem;
        border-radius: 5px;
        text-decoration: none;
        transition: background 0.3s ease;
        text-align: center;
    }

    .btn-enquire:hover {
        background: #e67e22; /* Slightly darker orange */
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: #666;
        text-decoration: none;
    }
    .back-link:hover { color: var(--primary-color); }
</style>

<main>
    <section class="section">
        <div class="container">
            <a href="products.php" class="back-link">&larr; Back to Products</a>

            <div class="product-details-container">
                <div class="product-image-col">
                    <img src="<?php echo !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'assets/images/hero1.png'; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image-large">
                </div>
                
                <div class="product-info-col">
                    <span class="product-category-badge"><?php echo htmlspecialchars($product['category']); ?></span>
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="product-description">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </div>

                    <?php if (!empty($product['features'])): ?>
                    <div class="product-features">
                        <h3>Key Features & Specifications</h3>
                        <div class="features-content">
                            <!-- Assuming features might be HTML or simple text. If trusted HTML from admin, output direct. For now, nl2br safe output -->
                            <?php echo nl2br(htmlspecialchars($product['features'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div style="margin-top: 30px;">
                        <a href="contact.php?subject=Enquiry for <?php echo urlencode($product['name']); ?>" class="btn-enquire">Request a Quote</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
