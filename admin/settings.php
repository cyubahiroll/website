<?php
$pageTitle = 'Settings';
include 'config.php';

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

$settings = [
    'store_name' => 'eShop',
    'store_email' => 'info@eshop.com',
    'store_phone' => '+1 234 567 890',
    'store_address' => '123 Store Street, City, Country',
    'currency' => 'USD',
    'tax_rate' => '10'
];

$checkTable = mysqli_query($conn, "SHOW TABLES LIKE 'settings'");
if (mysqli_num_rows($checkTable) > 0) {
    $result = mysqli_query($conn, "SELECT * FROM settings WHERE id = 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $settings = mysqli_fetch_assoc($result);
    }
}

if (isset($_POST['save'])) {
    $store_name = $_POST['store_name'];
    $store_email = $_POST['store_email'];
    $store_phone = $_POST['store_phone'];
    $store_address = $_POST['store_address'];
    $currency = $_POST['currency'];
    $tax_rate = $_POST['tax_rate'];
    
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY,
        store_name VARCHAR(100),
        store_email VARCHAR(100),
        store_phone VARCHAR(20),
        store_address TEXT,
        currency VARCHAR(10),
        tax_rate DECIMAL(5,2)
    )");
    
    $existing = mysqli_query($conn, "SELECT id FROM settings WHERE id = 1");
    if (mysqli_num_rows($existing) > 0) {
        mysqli_query($conn, "UPDATE settings SET store_name='$store_name', store_email='$store_email', store_phone='$store_phone', store_address='$store_address', currency='$currency', tax_rate='$tax_rate' WHERE id = 1");
    } else {
        mysqli_query($conn, "INSERT INTO settings (id, store_name, store_email, store_phone, store_address, currency, tax_rate) VALUES (1, '$store_name', '$store_email', '$store_phone', '$store_address', '$currency', '$tax_rate')");
    }
    
    $_SESSION['success'] = 'Settings saved successfully';
    header('Location: settings.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="page-title">
            <h1><i class="fas fa-cog me-2"></i>Settings</h1>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="content-card">
            <form method="POST">
                <div class="card-header">
                    <h3><i class="fas fa-store me-2"></i>Store Settings</h3>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Store Name</label>
                            <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($settings['store_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Store Email</label>
                            <input type="email" name="store_email" class="form-control" value="<?php echo htmlspecialchars($settings['store_email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="store_phone" class="form-control" value="<?php echo htmlspecialchars($settings['store_phone']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <select name="currency" class="form-select">
                                <option value="USD" <?php echo ($settings['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                                <option value="EUR" <?php echo ($settings['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                                <option value="GBP" <?php echo ($settings['currency'] ?? '') === 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                                <option value="INR" <?php echo ($settings['currency'] ?? '') === 'INR' ? 'selected' : ''; ?>>INR (₹)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" step="0.01" class="form-control" value="<?php echo $settings['tax_rate']; ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="store_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['store_address']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" name="save" class="btn-primary">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
</body>
</html>