<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<?php
// Fetch all customers/partners from the database
try {
    $stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    $customers = [];
    // In production, log error
}
?>

<style>
    /* Customer & Testimonials Page Styles */
    .customers-header {
        background-color: var(--light-bg);
        padding: 60px 0;
        text-align: center;
        margin-bottom: 50px;
    }

    .customers-header h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: var(--dark-color);
    }

    .customers-header p {
        color: #666;
        font-size: 1.1rem;
        max-width: 700px;
        margin: 0 auto;
    }

    /* Partner Logos Section */
    .partners-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); /* Responsive grid */
        gap: 40px;
        margin-bottom: 80px;
        align-items: center;
        justify-content: center;
    }

    .partner-logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 120px; /* Fixed height for consistency */
    }

    .partner-logo {
        max-width: 100%;
        max-height: 100%;
        filter: grayscale(100%); /* Start in grayscale */
        transition: filter 0.4s ease, transform 0.3s ease;
        opacity: 0.8;
    }

    .partner-logo-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: #ddd;
    }

    .partner-logo-container:hover .partner-logo {
        filter: grayscale(0%); /* Full color on hover */
        opacity: 1;
        transform: scale(1.05);
    }

    /* Testimonials Section */
    .testimonials-section {
        background-color: #f9f9f9;
        padding: 80px 0;
        margin-top: 50px;
    }

    .section-title {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-title h2 {
        font-size: 2rem;
        color: var(--dark-color);
        margin-bottom: 10px;
    }

    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .testimonial-card {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        position: relative;
    }
    
    .testimonial-card::before {
        content: "\201C"; /* Big quote mark */
        font-size: 80px;
        color: #f0f0f0;
        position: absolute;
        top: -10px;
        left: 20px;
        font-family: serif;
        z-index: 0;
    }

    .testimonial-text {
        font-size: 1.05rem;
        color: #555;
        font-style: italic;
        line-height: 1.6;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .client-info {
        display: flex;
        align-items: center;
        margin-top: 20px;
    }

    .client-details h4 {
        margin: 0;
        font-size: 1rem;
        color: var(--dark-color);
    }

    .client-details span {
        font-size: 0.85rem;
        color: #888;
    }

    .no-partners {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 8px;
        color: #555;
        font-size: 1.1rem;
    }
</style>

<main>
    <section class="customers-header">
        <div class="container">
            <h1>Our Trusted Partners</h1>
            <p>We are proud to collaborate with industry leaders to deliver excellence in automation and manufacturing.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <!-- Partners Grid -->
            <div class="partners-grid">
                <?php if (count($customers) > 0): ?>
                    <?php foreach ($customers as $customer): ?>
                        <div class="partner-logo-container">
                            <img src="<?php echo !empty($customer['logo_url']) ? htmlspecialchars($customer['logo_url']) : 'assets/images/default-logo.png'; ?>" 
                                 alt="<?php echo htmlspecialchars($customer['company_name']); ?>" 
                                 class="partner-logo"
                                 title="<?php echo htmlspecialchars($customer['company_name']); ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-partners">
                        <p>We have partnered with industry leaders across India.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-title">
                <h2>What Our Clients Say</h2>
                <p>Real feedback from the people we work with.</p>
            </div>

            <div class="testimonials-grid">
                <!-- Loop through customers who have testimonials -->
                 <?php 
                 $hasTestimonials = false;
                 foreach ($customers as $customer): 
                    if (!empty($customer['testimonial'])):
                        $hasTestimonials = true;
                 ?>
                    <div class="testimonial-card">
                        <blockquote class="testimonial-text">
                            "<?php echo htmlspecialchars($customer['testimonial']); ?>"
                        </blockquote>
                        <div class="client-info">
                            <div class="client-details">
                                <!-- Assuming company_name is the only identifier for now, as designation wasn't in db.sql -->
                                <h4>Representative</h4>
                                <span><?php echo htmlspecialchars($customer['company_name']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php 
                    endif;
                 endforeach; 
                 ?>
                 
                 <?php if (!$hasTestimonials): ?>
                    <!-- Fallback if no testimonials exist yet -->
                    <div class="testimonial-card">
                        <blockquote class="testimonial-text">
                            "SPLAR Machinery provided us with exceptional service and high-quality automation solutions that transformed our production line."
                        </blockquote>
                        <div class="client-info">
                            <div class="client-details">
                                <h4>Operations Manager</h4>
                                <span>Solar Tech Industries</span>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <blockquote class="testimonial-text">
                            "Their technical expertise in laser cutting machinery is unmatched. Support has been prompt and reliable."
                        </blockquote>
                        <div class="client-info">
                            <div class="client-details">
                                <h4>Plant Head</h4>
                                <span>Precision Metafab</span>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <blockquote class="testimonial-text">
                            "Reliable machines and great after-sales support. Highly recommended for capacitor manufacturing equipment."
                        </blockquote>
                        <div class="client-info">
                            <div class="client-details">
                                <h4>Director</h4>
                                <span>ElectroCap Solutions</span>
                            </div>
                        </div>
                    </div>
                 <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
