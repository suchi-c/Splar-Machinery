<?php include 'includes/header.php'; ?>
<?php 
include 'includes/db.php'; 

// Fetch featured products (latest 4)
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
    $featured_products = $stmt->fetchAll();
} catch (PDOException $e) {
    $featured_products = [];
}
?>

<main>
    <!-- Hero Section -->
    <!-- Hero Section -->
    <section class="hero">
        <div class="slide active" style="background-image: url('assets/images/hero1.png');"></div>
        <div class="slide" style="background-image: url('assets/images/hero2.png');"></div>
        <div class="slide" style="background-image: url('assets/images/hero3.png');"></div>
        
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <h1>Precision Engineering for Tomorrow</h1>
            <p>Advanced machinery solutions for Solar, Capacitor, and Laser industries.</p>
            <a href="products.php" class="btn">Explore Our Products</a>
        </div>

        <!-- Slider Controls -->
        <button class="prev"><i class="fas fa-chevron-left"></i></button>
        <button class="next"><i class="fas fa-chevron-right"></i></button>
        
        <div class="dots-container">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </section>

    <!-- SECTION 1: Innovation at a Glance (Icon Grid) -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Innovation at a Glance</h2>
                <p>Delivering excellence across multiple high-tech industries.</p>
            </div>
            
            <div class="icon-grid">
                <div class="icon-card">
                    <i class="fas fa-solar-panel"></i>
                    <h3>Solar Technology</h3>
                    <p>High-efficiency automated lines for solar panel production and assembly.</p>
                </div>
                <div class="icon-card">
                    <i class="fas fa-microchip"></i>
                    <h3>Capacitor Machinery</h3>
                    <p>Precision winding and assembly machines for electronic components.</p>
                </div>
                <div class="icon-card">
                    <i class="fas fa-bolt"></i>
                    <h3>Laser Systems</h3>
                    <p>Cutting-edge laser technology for welding, cutting, and engraving.</p>
                </div>
                <div class="icon-card">
                    <i class="fas fa-robot"></i>
                    <h3>Automation</h3>
                    <p>Custom robotic solutions to streamline your manufacturing process.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: Featured Products -->
    <section class="section" style="background-color: #f9f9f9;">
        <div class="container">
            <div class="section-title">
                <h2>Featured Machines</h2>
                <p>Our latest and most advanced equipment.</p>
            </div>
            
            <div class="product-grid">
                <?php if (count($featured_products) > 0): ?>
                    <?php foreach ($featured_products as $product): ?>
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
                    <p style="text-align: center; width: 100%;">No products available at the moment.</p>
                <?php endif; ?>
            </div>
            
            <div style="text-align: center; margin-top: 50px;">
                <a href="products.php" class="btn" style="background: var(--dark-color);">View All Products</a>
            </div>
        </div>
    </section>

    <!-- SECTION 3: The Splar Impact (Stats Counter) -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h2>500+</h2>
                    <p>Projects Delivered</p>
                </div>
                <div class="stat-item">
                    <h2>20+</h2>
                    <p>Years Experience</p>
                </div>
                <div class="stat-item">
                    <h2>100+</h2>
                    <p>Happy Clients</p>
                </div>
                <div class="stat-item">
                    <h2>24/7</h2>
                    <p>Support System</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: Interactive CTA -->
    <section class="cta-split">
        <div class="cta-image" style="background-image: url('assets/images/hero1.png');"></div>
        <div class="cta-content">
            <h2>Ready to Upgrade Your Production?</h2>
            <p>Partner with Splar Machinery for world-class engineering solutions. Whether you need a single machine or a full turnkey line, we are here to help you scale.</p>
            <a href="contact.php" class="btn">Get a Quote Today</a>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>