<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
            $stmt->execute([$name, $slug]);
            header("Location: categories.php?msg=added");
            exit();
        } catch (PDOException $e) {
            $error = "Category already exists or invalid name.";
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: categories.php?msg=deleted");
        exit();
    } catch (PDOException $e) {
        $error = "Error deleting category.";
    }
}

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Category Management</h1>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <p class="alert alert-success">
            <?php echo $_GET['msg'] == 'added' ? 'Category created successfully!' : 'Category deleted successfully!'; ?>
        </p>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <style>
        .cat-container { display: flex; gap: 30px; }
        .cat-form { flex: 1; }
        .cat-list { flex: 2; }
        @media (max-width: 768px) { .cat-container { flex-direction: column; } }
    </style>

    <div class="cat-container">
        <!-- Add Category Form -->
        <div class="card cat-form">
            <div class="card-header">
                <h3>Add New Category</h3>
            </div>
            <form action="categories.php" method="POST">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Laser Machinery" required>
                </div>
                <button type="submit" name="add_category" class="btn-primary" style="width: 100%;">Add Category</button>
            </form>
        </div>

        <!-- Category List -->
        <div class="card cat-list">
            <div class="card-header">
                <h3>Existing Categories</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                        <td>
                            <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Delete this category?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
