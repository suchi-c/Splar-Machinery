<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

// Handle Add Blog
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_blog'])) {
    $title = $_POST['title'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $content = $_POST['content'];
    
    // Image Upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/blog/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "assets/images/blog/" . $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO blogs (title, slug, content, image_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $slug, $content, $image_url]);
    
    header("Location: blogs.php?msg=added");
    exit();
}

// Handle Delete Blog
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path to delete file
    $stmt = $pdo->prepare("SELECT image_url FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch();
    
    if ($blog && !empty($blog['image_url'])) {
        $file_path = "../" . $blog['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: blogs.php?msg=deleted");
    exit();
}

// Fetch Blogs
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC");
$blogs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Blog Management</h1>
        <button class="btn-add" onclick="toggleForm()"><i class="fas fa-plus"></i> New Post</button>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <p class="alert alert-success">
            <?php echo $_GET['msg'] == 'added' ? 'Blog post published!' : ($_GET['msg'] == 'deleted' ? 'Blog post deleted successfully!' : 'Blog updated successfully!'); ?>
        </p>
    <?php endif; ?>

    <!-- Add Blog Form -->
    <div id="blogForm" class="form-container">
        <h2>Write New Post</h2>
        <form action="blogs.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Featured Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Content</label>
                <textarea name="content" id="editor" class="form-control" rows="10" required></textarea>
                <script>CKEDITOR.replace('editor');</script>
            </div>
            <button type="submit" name="add_blog" class="btn-submit">Publish Post</button>
            <button type="button" onclick="toggleForm()" style="background: #95a5a6; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Cancel</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($blogs as $blog): ?>
            <tr>
                <td>
                    <?php if ($blog['image_url']): ?>
                        <img src="../<?php echo htmlspecialchars($blog['image_url']); ?>" class="thumb" alt="Blog">
                    <?php else: ?>
                        <span style="color: #999;">No Image</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($blog['title']); ?></td>
                <td><?php echo date('M j, Y', strtotime($blog['created_at'])); ?></td>
                <td>
                    <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="action-btn btn-edit">Edit</a>
                    <a href="blogs.php?delete=<?php echo $blog['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this post?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function toggleForm() {
        var form = document.getElementById('blogForm');
        form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        
        // Scroll to form if showing
        if(form.style.display === 'block') {
            form.scrollIntoView({behavior: 'smooth'});
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
