<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Checkout';
include 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));

$cart = mysqli_query($conn, "SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");
if (mysqli_num_rows($cart) == 0) {
    header('Location: cart.php');
    exit;
}

$total = 0;
$items = [];
while ($c = mysqli_fetch_assoc($cart)) {
    $total += $c['price'] * $c['quantity'];
    $items[] = $c;
}

if (isset($_POST['place_order'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    
    mysqli_query($conn, "INSERT INTO orders (user_id, name, phone, address, total_amount, payment_method, status) VALUES ($user_id, '$name', '$phone', '$address', $total, 'Cash on Delivery', 'pending')");
    $orderId = $conn->insert_id;
    
    $itemsList = '';
    foreach ($items as $item) {
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES ($orderId, {$item['product_id']}, '{$item['name']}', {$item['quantity']}, {$item['price']})");
        $itemsList .= $item['name'] . ' x ' . $item['quantity'] . ' = $' . number_format($item['price'] * $item['quantity'], 2) . "\n";
    }
    
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
    
    $subject = "Order Confirmation - #$orderId";
    $message = "Dear $name,\n\nThank you for your order!\n\nOrder ID: $orderId\nTotal Amount: $" . number_format($total, 2) . "\n\nOrder Details:\n$itemsList\nShipping Address:\n$address\n\nPayment Method: Cash on Delivery\n\nWe will process your order shortly.\n\nBest regards,\neShop Team";
    $headers = "From: info@eshop.com\r\nContent-Type: text/plain; charset=UTF-8";
    mail($email, $subject, $message, $headers);
    
    $_SESSION['order_total'] = $total;
    $_SESSION['order_id'] = $orderId;
    $_SESSION['success'] = 'Order placed successfully! Check your email for confirmation.';
    header("Location: order_success.php");
    exit;
}

include 'includes/header.php';
?>

<div class="container py-5">
    <h2 class="mb-4"><i class="fas fa-shopping-bag me-2"></i>Checkout</h2>
    
    <div class="row">
        <div class="col-12">
            <form method="POST">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Delivery Address *</label>
                            <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><i class="fas fa-money-bill me-2"></i>Cash on Delivery</p>
                        <small class="text-muted">Pay when you receive your order</small>
                    </div>
                </div>
                
                <button type="submit" name="place_order" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-check-circle me-2"></i>Complete Checkout - $<?php echo number_format($total, 2); ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>