<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

// Fetch product data
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

if (!$product) {
    header("Location: products.php");
    exit();
}

// Fetch Categories for dropdown
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $cat_stmt->fetchAll();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    // Image Upload
    $image_url = $product['image_url']; // Keep old image by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image if exists
            if (!empty($product['image_url']) && file_exists("../" . $product['image_url'])) {
                unlink("../" . $product['image_url']);
            }
            $image_url = "assets/images/products/" . $file_name;
        }
    }

    $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, description = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$name, $category, $description, $image_url, $id]);
    
    header("Location: products.php?msg=updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - SPLAR Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reuse generic styles */
        :root { --sidebar-bg: #2c3e50; --sidebar-color: #ecf0f1; --light-bg: #f4f6f9; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--light-bg); display: flex; margin: 0; }
        .sidebar { width: 250px; background: var(--sidebar-bg); color: var(--sidebar-color); height: 100vh; position: fixed; display: flex; flex-direction: column; padding-top: 20px; }
        .sidebar-header { text-align: center; padding-bottom: 20px; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 15px 25px; color: var(--sidebar-color); text-decoration: none; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #34495e; border-left: 4px solid #3498db; }
        .sidebar-menu i { width: 30px; margin-right: 10px; }
        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 30px; }
        
        .form-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 20px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 1rem; }
        .btn-cancel { background: #95a5a6; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; width: 100%; box-sizing: border-box; margin-top: 10px; }
    </style>
</head>
<body>

<nav class="sidebar">
    <div class="sidebar-header">
        <h2>SPLAR Admin</h2>
    </div>
    <ul class="sidebar-menu">
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li><a href="products.php" class="active"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="categories.php"><i class="fas fa-tags"></i> <span>Categories</span></a></li>
        <li><a href="blogs.php"><i class="fas fa-pen-nib"></i> <span>Blogs</span></a></li>
        <li><a href="customers.php"><i class="fas fa-users"></i> <span>Customers</span></a></li>
        <li><a href="enquiries.php"><i class="fas fa-envelope"></i> <span>Enquiries</span></a></li>
        <li><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</nav>

<div class="main-content">
    <div class="form-container">
        <h2>Edit Product</h2>
        <form action="edit_product.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>" 
                            <?php echo $cat['name'] == $product['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Current Image</label><br>
                <?php if ($product['image_url']): ?>
                    <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" style="width: 100px; border-radius: 4px; margin-top: 5px;">
                <?php else: ?>
                    <span>No Image</span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Change Image (Optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" name="update_product" class="btn-submit">Update Product</button>
            <a href="products.php" class="btn-cancel">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>
