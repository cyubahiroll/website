<?php
$pageTitle = 'Order Success';
include 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$orderId = $_SESSION['order_id'] ?? 0;

if (!$orderId) {
    header('Location: index.php');
    exit;
}

$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id = $orderId AND user_id = $user_id"));
$orderItems = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $orderId");
$total = $_SESSION['order_total'] ?? ($order ? $order['total_amount'] : 0);

include 'includes/header.php';
?>

<div class="container py-5 text-center">
    <div class="alert alert-success">
        <i class="fas fa-check-circle fa-3x mb-3"></i>
        <h3>Order Placed Successfully!</h3>
        <p>Your order #<?php echo $orderId; ?> has been placed.</p>
        <p>Total: <strong>$<?php echo number_format($total, 2); ?></strong></p>
        <p>Payment: Cash on Delivery</p>
    </div>
    
    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
</div>

<?php 
unset($_SESSION['order_id'], $_SESSION['order_total']);
include 'includes/footer.php'; 
?>