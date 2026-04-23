<?php
$pageTitle = 'eShop - Your Online Store';
include 'config/config.php';
include 'includes/header.php';

$categories = [
    ['name' => 'Electronics', 'icon' => 'fa-laptop'],
    ['name' => 'Mobile Phones', 'icon' => 'fa-mobile-alt'],
    ['name' => 'Laptops', 'icon' => 'fa-laptop'],
    ['name' => 'Tablets', 'icon' => 'fa-tablet-alt'],
    ['name' => 'Phone Accessories', 'icon' => 'fa-headphones'],
    ['name' => 'Computer Accessories', 'icon' => 'fa-mouse'],
    ['name' => 'Gaming Consoles', 'icon' => 'fa-gamepad'],
    ['name' => 'Video Games', 'icon' => 'fa-gamepad'],
    ['name' => 'Cameras', 'icon' => 'fa-camera'],
    ['name' => 'Headphones & Earbuds', 'icon' => 'fa-headphones'],
    ['name' => 'Home Appliances', 'icon' => 'fa-blender'],
    ['name' => 'Kitchen Appliances', 'icon' => 'fa-blender'],
    ['name' => 'Televisions', 'icon' => 'fa-tv'],
    ['name' => 'Smart Home Devices', 'icon' => 'fa-house-signal'],
    ['name' => 'Fashion (Men)', 'icon' => 'fa-shirt'],
    ['name' => 'Fashion (Women)', 'icon' => 'fa-shirt'],
    ['name' => 'Shoes (Men)', 'icon' => 'fa-shoe-prints'],
    ['name' => 'Shoes (Women)', 'icon' => 'fa-shoe-prints'],
    ['name' => 'Bags & Handbags', 'icon' => 'fa-bag-shopping'],
    ['name' => 'Watches', 'icon' => 'fa-clock'],
    ['name' => 'Jewelry', 'icon' => 'fa-gem'],
    ['name' => 'Beauty Products', 'icon' => 'fa-spa'],
    ['name' => 'Fitness Equipment', 'icon' => 'fa-dumbbell'],
    ['name' => 'Books', 'icon' => 'fa-book'],
];

$allProducts = mysqli_query($conn, "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 20");
$banners = mysqli_query($conn, "SELECT * FROM banners WHERE status = 'active' ORDER BY sort_order ASC");
$productsByCategory = [];
while ($p = mysqli_fetch_assoc($allProducts)) {
    $cat = $p['category'] ?? 'Electronics';
    if (!isset($productsByCategory[$cat])) $productsByCategory[$cat] = [];
    if (count($productsByCategory[$cat]) < 4) $productsByCategory[$cat][] = $p;
}
?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1>Welcome to eShop</h1>
            <p>Discover amazing products at unbeatable prices!</p>
            <a href="products.php" class="btn btn-light btn-lg">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</div>

<style>
.hero-section {
    margin-bottom: 40px;
}
.hero-section .carousel-item {
    min-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-size: cover !important;
}
.hero-content {
    text-align: center;
    color: white;
    padding: 40px;
    max-width: 700px;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}
.hero-content h1 {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 20px;
    animation: fadeInUp 0.8s ease;
}
.hero-content p {
    font-size: 1.5rem;
    margin-bottom: 35px;
    opacity: 0.95;
    animation: fadeInUp 0.8s ease 0.2s both;
}
.hero-content .btn {
    padding: 18px 45px;
    font-size: 1.15rem;
    border-radius: 50px;
    font-weight: 700;
    animation: fadeInUp 0.8s ease 0.4s both;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.hero-content .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.carousel-indicators {
    bottom: 30px;
}
.carousel-indicators button {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    margin: 0 5px;
    background: rgba(255,255,255,0.5);
    border: 2px solid white;
    transition: all 0.3s;
}
.carousel-indicators button.active {
    background: white;
    transform: scale(1.2);
}
.carousel-control-prev,
.carousel-control-next {
    width: 70px;
    height: 70px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.3);
    border-radius: 50%;
    margin: 0 20px;
    opacity: 0;
    transition: all 0.3s;
}
.carousel-control-prev:hover,
.carousel-control-next:hover {
    background: rgba(0,0,0,0.5);
    opacity: 1;
}
.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 30px;
    height: 30px;
}
@media (max-width: 768px) {
    .hero-section .carousel-item {
        min-height: 400px;
    }
    .hero-content h1 {
        font-size: 2rem;
    }
    .hero-content p {
        font-size: 1rem;
    }
    .hero-content .btn {
        padding: 14px 30px;
        font-size: 1rem;
    }
}
</style>

<div class="container py-5">
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="trust-badge">
                <i class="fas fa-truck"></i>
                <h6>Free Shipping</h6>
                <p>On orders over $50</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="trust-badge">
                <i class="fas fa-shield-alt"></i>
                <h6>Secure Payment</h6>
                <p>100% secure checkout</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="trust-badge">
                <i class="fas fa-undo"></i>
                <h6>Easy Returns</h6>
                <p>30-day return policy</p>
            </div>
        </div>
    </div>
    
    <h2 class="mb-4"><i class="fas fa-th-large text-primary me-2"></i> Shop by Category</h2>
    
    <div class="cat-outer">
        <button class="cat-arrow-btn left" onclick="scrollCat(-1)"><i class="fas fa-chevron-left"></i></button>
        <button class="cat-arrow-btn right" onclick="scrollCat(1)"><i class="fas fa-chevron-right"></i></button>
        
        <div class="cat-scroll-area" id="catScroll">
            <div class="cat-grid">
                <?php foreach ($categories as $cat): ?>
                <a href="products.php?category=<?php echo urlencode($cat['name']); ?>" class="cat-btn">
                    <i class="fas <?php echo $cat['icon']; ?>"></i>
                    <h6><?php echo $cat['name']; ?></h6>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <?php foreach ($categories as $cat): ?>
    <?php 
    $catName = $cat['name'];
    $products = $productsByCategory[$catName] ?? [];
    if (count($products) > 0):
    ?>
    <div class="cat-section">
        <div class="cat-header">
            <h5 class="cat-title"><i class="fas <?php echo $cat['icon']; ?>"></i><?php echo $catName; ?></h5>
            <a href="products.php?category=<?php echo urlencode($catName); ?>" class="cat-link">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
            <div class="product-card-mini">
                <?php if ($product['image']): ?>
                <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                <div style="height:180px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#94a3b8;">
                    <i class="fas fa-image fa-2x"></i>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <h6 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h6>
                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                    <div class="d-flex gap-2">
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eye"></i> View Detail
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-cart-plus me-1"></i> Add
                        </a>
                        <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary btn-sm">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
    
    <div class="banner-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3><i class="fas fa-fire me-2"></i> Summer Sale!</h3>
                <p>Get up to 50% off on selected items. Limited time offer!</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="products.php" class="btn btn-light btn-lg">
                    Shop Sale <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
    
    <?php if (count($productsByCategory) === 0): ?>
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h4>No Products Available</h4>
        <p>Products will appear here once added by the admin.</p>
        <a href="admin/login.php" class="btn btn-primary mt-3">Go to Admin Panel</a>
    </div>
    <?php endif; ?>
</div>

<script>
function scrollCat(dir) {
    document.getElementById('catScroll').scrollBy({ left: dir * 350, behavior: 'smooth' });
}
</script>

<?php include 'includes/footer.php'; ?>