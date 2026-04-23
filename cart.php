<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = 'Shopping Cart';
include 'config/config.php';

if (isset($_GET['add'])) {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    $productId = (int)$_GET['add'];
    $size = $_GET['size'] ?? 'M';
    $userId = $_SESSION['user_id'];
    
    $check = mysqli_query($conn, "SELECT id FROM cart WHERE user_id = $userId AND product_id = $productId AND size = '$size'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id = $userId AND product_id = $productId");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity, size) VALUES ($userId, $productId, 1, '$size')");
    }
    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $id = (int)$_GET['remove'];
        $userId = $_SESSION['user_id'];
        mysqli_query($conn, "DELETE FROM cart WHERE id = $id AND user_id = $userId");
        header('Location: cart.php');
        exit;
    }
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$cartItems = mysqli_query($conn, "SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $userId");
$total = 0;

include 'includes/header.php';
?>

<style>
.cart-summary {
    background: white;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    padding: 25px;
    position: sticky;
    top: 100px;
}

.cart-summary h4 {
    font-size: 1.2rem;
    margin-bottom: 20px;
    color: #333;
}

.cart-summary > div {
    margin-bottom: 10px;
    color: #555;
}

.cart-summary > div:last-child {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    margin-bottom: 0;
}

.cart-summary .total-row {
    font-size: 1.2rem;
    font-weight: 700;
    color: #333;
    padding-top: 15px;
    border-top: 2px solid #e5e5e5;
    margin-top: 15px;
}

.btn-checkout {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 8px;
    font-weight: 600;
    flex: 1;
    text-decoration: none;
    text-align: center;
}

.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-cancel {
    background: #f1f5f9;
    color: #64748b;
    border: none;
    padding: 14px 20px;
    border-radius: 8px;
    font-weight: 600;
    flex: 1;
    text-decoration: none;
    text-align: center;
}

.btn-cancel:hover {
    background: #e2e8f0;
}
</style>

<div class="cart-page">
    <div class="container">
        <h2>Shopping Cart</h2>
        
        <?php if (mysqli_num_rows($cartItems) == 0): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Your cart is empty.</p>
            <a href="products.php" class="btn-checkout" style="width: auto; padding: 12px 30px;">Continue Shopping</a>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <?php while ($item = mysqli_fetch_assoc($cartItems)):
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                ?>
                <div class="cart-item">
                    <div class="d-flex align-items-center">
                        <?php if ($item['image']): ?>
                        <img src="assets/images/products/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="price">$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></p>
                            <p class="size">Size: <?php echo $item['size']; ?></p>
                        </div>
                        <div class="text-end">
                            <h4 class="item-total">$<?php echo number_format($itemTotal, 2); ?></h4>
                            <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn-remove">Remove</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4>Order Summary</h4>
                    <div class="row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="row">
                        <span>Shipping</span>
                        <span class="text-success">Free</span>
                    </div>
                    <div class="row total-row">
                        <span>Total</span>
                        <span>$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div>
                        <a href="index.php" class="btn-cancel">Cancel</a>
                        <a href="checkout.php" class="btn-checkout">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>