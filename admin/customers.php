<?php
require_once 'includes/check_login.php';
require_once '../includes/db.php';

// Handle Add Customer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_customer'])) {
    $company_name = $_POST['company_name'];
    $testimonial = $_POST['testimonial'];
    
    // Image Upload
    $logo_url = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $target_dir = "../assets/images/customers/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES["logo"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $logo_url = "assets/images/customers/" . $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO customers (company_name, logo_url, testimonial) VALUES (?, ?, ?)");
    $stmt->execute([$company_name, $logo_url, $testimonial]);
    
    header("Location: customers.php?msg=added");
    exit();
}

// Handle Delete Customer
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path to delete file
    $stmt = $pdo->prepare("SELECT logo_url FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    $customer = $stmt->fetch();
    
    if ($customer && !empty($customer['logo_url'])) {
        $file_path = "../" . $customer['logo_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: customers.php?msg=deleted");
    exit();
}

// Fetch Customers
$stmt = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC");
$customers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-header">
        <h1>Customer Partners</h1>
        <button class="btn-add" onclick="openModal()"><i class="fas fa-plus"></i> Add Partner</button>
    </div>
    
    <!-- Add styles locally for now as grid is specific -->
    <style>
         /* Grid for Customers */
        .customers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .customer-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center; position: relative; }
        .customer-logo { max-width: 100%; height: 80px; object-fit: contain; margin-bottom: 15px; }
        .customer-name { font-weight: 600; color: #333; margin-bottom: 5px; }
        .customer-testimonial { font-size: 0.85rem; color: #777; font-style: italic; margin-bottom: 15px; }
        .btn-delete-card { background: #e74c3c; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 0.85rem; display: inline-block; }
    </style>

    <div class="customers-grid">
        <?php foreach ($customers as $customer): ?>
        <div class="customer-card">
            <?php if ($customer['logo_url']): ?>
                <img src="../<?php echo htmlspecialchars($customer['logo_url']); ?>" class="customer-logo" alt="Logo">
            <?php else: ?>
                <div style="height: 80px; display: flex; align-items: center; justify-content: center; background: #eee; margin-bottom: 15px;">No Logo</div>
            <?php endif; ?>
            <div class="customer-name"><?php echo htmlspecialchars($customer['company_name']); ?></div>
            <?php if ($customer['testimonial']): ?>
                <div class="customer-testimonial">"<?php echo substr(htmlspecialchars($customer['testimonial']), 0, 50) . '...'; ?>"</div>
            <?php endif; ?>
            <a href="customers.php?delete=<?php echo $customer['id']; ?>" class="btn-delete-card" onclick="return confirm('Remove this partner?')">Remove</a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add Partner</h2>
        <form action="customers.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Company Name</label>
                <input type="text" name="company_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Logo</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Testimonial (Optional)</label>
                <textarea name="testimonial" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" name="add_customer" class="btn-submit">Save Partner</button>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('addCustomerModal').style.display = 'flex'; }
    function closeModal() { document.getElementById('addCustomerModal').style.display = 'none'; }
    window.onclick = function(event) {
        if (event.target == document.getElementById('addCustomerModal')) closeModal();
    }
</script>

<?php include 'includes/footer.php'; ?>
