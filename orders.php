<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'My Orders';
include 'config/config.php';

$user_id = $_SESSION['user_id'];
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/account.css">

<div class="container py-5">
    <h2 class="mb-4"><i class="fas fa-box me-2"></i>My Orders</h2>
    
    <?php if (mysqli_num_rows($orders) == 0): ?>
    <div class="alert alert-info">
        <p>You haven't placed any orders yet.</p>
        <a href="products.php" class="btn btn-primary">Start Shopping</a>
    </div>
    <?php else: ?>
    <div class="row">
        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></h6>
                    <span class="badge bg-<?php echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'delivered' ? 'success' : 'primary'); ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
                <div class="card-body">
                    <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>Payment:</strong> <?php echo $order['payment_method']; ?></p>
                    <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                    
                    <?php 
                    $items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = " . $order['id']);
                    if (mysqli_num_rows($items) > 0): ?>
                    <hr>
                    <p class="mb-2"><strong>Items:</strong></p>
                    <?php while ($item = mysqli_fetch_assoc($items)): ?>
                    <small><?php echo $item['product_name']; ?> x <?php echo $item['quantity']; ?></small><br>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>