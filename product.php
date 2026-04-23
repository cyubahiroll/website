<?php
$pageTitle = 'Product Details - eShop';
include 'config/config.php';
include 'includes/header.php';

$product_id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: products.php');
    exit;
}

$sizes = $product['sizes'] ? array_map('trim', explode(',', $product['sizes'])) : [];
?>

<link rel="stylesheet" href="assets/css/home.css">

<style>
.product-detail-page {
    padding: 40px 0;
    background: #f8fafc;
}
.product-detail-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.08);
    overflow: hidden;
}
.product-image-wrapper {
    position: relative;
    background: #f1f5f9;
    border-radius: 20px 0 0 20px;
    overflow: hidden;
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.product-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.product-image-wrapper .placeholder {
    color: #cbd5e1;
    font-size: 5rem;
}
.product-info-wrapper {
    padding: 40px;
}
.product-breadcrumb {
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.product-breadcrumb a {
    color: #64748b;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 500;
    padding: 8px 18px;
    background: #f1f5f9;
    border-radius: 25px;
    transition: all 0.3s;
}
.product-breadcrumb a:hover {
    background: #2563eb;
    color: white;
}
.product-breadcrumb a.current {
    color: #1e293b;
    background: #e2e8f0;
    pointer-events: none;
}
.product-category-tag {
    display: inline-block;
    background: #dbeafe;
    color: #2563eb;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.product-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin: 15px 0;
    line-height: 1.3;
}
.product-price-tag {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2563eb;
    margin: 20px 0;
}
.product-short-desc {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.7;
    margin-bottom: 25px;
}
.size-selector {
    margin: 30px 0;
}
.size-selector h6 {
    font-weight: 600;
    color: #334155;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}
.size-selector h6 i {
    margin-right: 8px;
    color: #2563eb;
}
.size-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.size-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    background: white;
    font-weight: 600;
    color: #475569;
}
.size-btn:hover, .size-btn.selected {
    border-color: #2563eb;
    background: #2563eb;
    color: white;
}
.size-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.product-quantity {
    margin: 25px 0;
}
.product-quantity h6 {
    font-weight: 600;
    color: #334155;
    margin-bottom: 15px;
}
.quantity-control {
    display: flex;
    align-items: center;
    gap: 0;
    width: fit-content;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
}
.quantity-control button {
    width: 45px;
    height: 45px;
    border: none;
    background: #f8fafc;
    color: #475569;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
}
.quantity-control button:hover {
    background: #2563eb;
    color: white;
}
.quantity-control input {
    width: 60px;
    height: 45px;
    border: none;
    text-align: center;
    font-weight: 600;
    font-size: 1rem;
    background: white;
}
.product-action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}
.product-action-buttons .btn {
    padding: 15px 35px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
}
.product-action-buttons .btn-cart {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
@media (max-width: 992px) {
    .product-image-wrapper {
        border-radius: 20px 20px 0 0;
        min-height: 350px;
    }
    .product-info-wrapper {
        padding: 30px;
    }
    .product-title {
        font-size: 1.5rem;
    }
    .product-price-tag {
        font-size: 2rem;
    }
    .product-action-buttons .btn {
        padding: 12px 25px;
    }
}
</style>

<div class="container product-detail-page">
    <div class="product-detail-card">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="product-image-wrapper">
                    <?php if ($product['image']): ?>
                    <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                    <i class="fas fa-image placeholder"></i>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="product-info-wrapper">
                    <div class="product-breadcrumb">
                        <a href="index.php">Home</a>
                        <a href="products.php">Products</a>
                        <a href="#" class="current"><?php echo htmlspecialchars($product['name']); ?></a>
                    </div>
                    
                    <span class="product-category-tag"><?php echo htmlspecialchars($product['category']); ?></span>
                    
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    
                    <div class="product-price-tag">$<?php echo number_format($product['price'], 2); ?></div>
                    
                    <p class="product-short-desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    
                    <?php if (count($sizes) > 0): ?>
                    <div class="size-selector">
                        <h6><i class="fas fa-ruler"></i> Select Size</h6>
                        <div class="size-options">
                            <?php foreach ($sizes as $size): ?>
                            <span class="size-btn" onclick="selectSize(this)"><?php echo $size; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="product-quantity">
                        <h6><i class="fas fa-cubes me-2"></i> Quantity</h6>
                        <div class="quantity-control">
                            <button onclick="changeQty(-1)">-</button>
                            <input type="number" id="qty" value="1" min="1">
                            <button onclick="changeQty(1)">+</button>
                        </div>
                    </div>
                    
                    <div class="product-action-buttons">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-primary btn-cart">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </a>
                        <a href="cart.php?add=<?php echo $product['id']; ?>&buy=now" class="btn btn-outline-primary">
                            <i class="fas fa-bolt"></i> Buy Now
                        </a>
                        <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-cart">
                            <i class="fas fa-sign-in-alt"></i> Login to Buy
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectSize(btn) {
    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
}

function changeQty(val) {
    var input = document.getElementById('qty');
    var newVal = parseInt(input.value) + val;
    if (newVal >= 1) {
        input.value = newVal;
    }
}
</script>

<?php include 'includes/footer.php'; ?>