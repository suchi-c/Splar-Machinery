<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

// Fetch Categories for dropdown
$cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $cat_stmt->fetchAll();

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    
    // Image Upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = "assets/images/products/" . $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, category, description, image_url) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $category, $description, $image_url]);
    
    header("Location: products.php?msg=added");
    exit();
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path to delete file
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if ($product && !empty($product['image_url'])) {
        $file_path = "../" . $product['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: products.php?msg=deleted");
    exit();
}

// Fetch Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Product Management</h1>
        <button class="btn-add" onclick="openModal()"><i class="fas fa-plus"></i> Add Product</button>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <p class="alert alert-success">
            <?php echo $_GET['msg'] == 'added' ? 'Product added successfully!' : ($_GET['msg'] == 'deleted' ? 'Product deleted successfully!' : 'Product updated successfully!'); ?>
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td>
                    <?php if ($product['image_url']): ?>
                        <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" class="thumb" alt="Product">
                    <?php else: ?>
                        <span style="color: #999;">No Image</span>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td><?php echo substr(htmlspecialchars($product['description']), 0, 50) . '...'; ?></td>
                <td>
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="action-btn btn-edit">Edit</a>
                    <a href="products.php?delete=<?php echo $product['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Product</h2>
        <form action="products.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Product Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <button type="submit" name="add_product" class="btn-submit">Save Product</button>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('addProductModal').style.display = 'flex'; }
    function closeModal() { document.getElementById('addProductModal').style.display = 'none'; }
    
    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('addProductModal')) {
            closeModal();
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
