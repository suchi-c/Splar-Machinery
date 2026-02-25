<?php include 'includes/header.php'; ?>
<?php include 'includes/db.php'; ?>

<?php
// Fetch all blogs from the database
try {
    $stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC");
    $blogs = $stmt->fetchAll();
} catch (PDOException $e) {
    $blogs = [];
    // In production, log error: error_log($e->getMessage());
}
?>

<style>
    /* Blogs Page Specific Styles */
    .blog-header {
        background-color: var(--light-bg);
        padding: 60px 0;
        text-align: center;
        margin-bottom: 40px;
    }

    .blog-header h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: var(--dark-color);
    }

    .blog-header p {
        color: #666;
        font-size: 1.1rem;
    }

    .blog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 60px;
    }

    .blog-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .blog-thumbnail {
        height: 200px;
        background-color: #f0f0f0;
        background-size: cover;
        background-position: center;
    }

    .blog-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .blog-date {
        font-size: 0.85rem;
        color: #888;
        margin-bottom: 10px;
        display: block;
    }

    .blog-title {
        font-size: 1.25rem;
        margin-bottom: 10px;
        color: var(--dark-color);
        line-height: 1.4;
    }

    .blog-excerpt {
        color: #555;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .read-more-btn {
        display: inline-block;
        padding: 8px 0;
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: border-color 0.3s ease;
        align-self: flex-start;
    }

    .read-more-btn:hover {
        border-color: var(--primary-color);
    }

    /* Message when no blogs are found */
    .no-blogs {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px;
        background: #f9f9f9;
        border-radius: 8px;
        color: #666;
    }
</style>

<main>
    <section class="blog-header">
        <div class="container">
            <h1>Technical Insights & Innovation</h1>
            <p>Latest updates, technology trends, and company news.</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="blog-grid">
                <?php if (count($blogs) > 0): ?>
                    <?php foreach ($blogs as $blog): ?>
                        <div class="blog-card">
                            <div class="blog-thumbnail" style="background-image: url('<?php echo !empty($blog['image_url']) ? htmlspecialchars($blog['image_url']) : 'assets/images/default-blog.jpg'; ?>');"></div>
                            <div class="blog-content">
                                <span class="blog-date"><?php echo date('F j, Y', strtotime($blog['created_at'])); ?></span>
                                <h3 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h3>
                                <!-- create excerpt from content if needed or assume excerpt field exists (it wasn't in db.sql but requested in seed prompt, assuming content is used) -->
                                <?php 
                                    // Use excerpt if available (user prompt implied it), otherwise truncate content
                                    // db.sql didn't have excerpt, but the INSERT prompt asked for it. 
                                    // I'll check if 'excerpt' key exists, else truncate content.
                                    $excerpt = isset($blog['excerpt']) ? $blog['excerpt'] : substr(strip_tags($blog['content']), 0, 100) . '...';
                                ?>
                                <p class="blog-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
                                <a href="blog-details.php?id=<?php echo $blog['id']; ?>" class="read-more-btn">Read More</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-blogs">
                        <h3>No updates yet.</h3>
                        <p>Check back soon for the latest news and technical insights.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
