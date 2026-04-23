<?php
function getCartCount($conn, $userId) {
    if (!$userId) return 0;
    $stmt = $conn->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    return $row[0] ?? 0;
}

function getCartTotal($conn, $userId) {
    if (!$userId) return 0;
    $query = "SELECT SUM(c.quantity * p.price) as total 
              FROM cart c 
              JOIN products p ON c.product_id = p.id 
              WHERE c.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

$cartCount = isset($_SESSION['user_id']) ? getCartCount($conn, $_SESSION['user_id']) : 0;
$cartTotal = isset($_SESSION['user_id']) ? getCartTotal($conn, $_SESSION['user_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'eShop - Online Store'; ?></title>
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo SITE_URL; ?>/3.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/home.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/cart.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/security.css">
</head>
<body>
    <nav class="main-navbar">
        <div class="nav-container">
            <a href="index.php" class="nav-logo">
                <img src="assets/images/users/2.png" alt="Logo" style="width:60px;height:60px;object-fit:contain;">
                <span>eShop</span>
            </a>
            
            <div class="nav-links">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home me-1"></i> Home
                </a>
                <a href="products.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                    <i class="fas fa-box me-1"></i> Products
                </a>
            </div>
            
            <form class="nav-search" method="GET" action="products.php">
                <input type="text" name="search" id="searchInput" placeholder="Search products..." value="<?php echo $_GET['search'] ?? ''; ?>" autocomplete="off">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div id="searchResults" class="search-results"></div>
            
            <div class="nav-actions">
                <a href="cart.php" class="nav-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge"><?php echo $cartCount; ?></span>
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="nav-user-links">
                    <a href="profile.php" class="nav-user-link">
                        <i class="fas fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="orders.php" class="nav-user-link">
                        <i class="fas fa-box"></i>
                        <span>My Orders</span>
                    </a>
                    <a href="logout.php" class="nav-user-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
                <?php else: ?>
                <a href="login.php" class="nav-btn login-btn">Login</a>
                <a href="register.php" class="nav-btn register-btn">Register</a>
                <?php endif; ?>
            </div>
            
            <button class="mobile-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <div class="collapse" id="mobileMenu">
        <div style="background: #1e293b; padding: 15px 20px;">
            <a href="index.php" style="display:block;padding:12px 0;color:#cbd5e1;border-bottom:1px solid rgba(255,255,255,0.1);"><i class="fas fa-home"></i> Home</a>
            <a href="products.php" style="display:block;padding:12px 0;color:#cbd5e1;border-bottom:1px solid rgba(255,255,255,0.1);"><i class="fas fa-box"></i> Products</a>
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php" style="display:block;padding:12px 0;color:#cbd5e1;"><i class="fas fa-user"></i> Profile</a>
            <a href="logout.php" style="display:block;padding:12px 0;color:#cbd5e1;"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
            <a href="login.php" style="display:block;padding:12px 0;color:#cbd5e1;"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php" style="display:block;padding:12px 0;color:#cbd5e1;"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

<style>
.main-navbar {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 15px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 20px rgba(0,0,0,0.2);
}
.nav-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 25px;
    display: flex;
    align-items: center;
    gap: 35px;
}
.nav-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.6rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
}
.nav-logo i {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.nav-links { display: flex; gap: 10px; }
.nav-link {
    padding: 12px 20px;
    color: #cbd5e1;
    text-decoration: none;
    font-weight: 500;
    border-radius: 10px;
    font-size: 1rem;
}
.nav-link:hover, .nav-link.active {
    background: rgba(255,255,255,0.15);
    color: white;
}
.nav-search {
    flex: 1;
    max-width: 420px;
    display: flex;
    position: relative;
}
.nav-search input {
    width: 100%;
    padding: 12px 55px 12px 20px;
    border: 2px solid transparent;
    border-radius: 30px;
    background: rgba(255,255,255,0.12);
    color: white;
    font-size: 0.95rem;
}
.nav-search input::placeholder { color: #94a3b8; }
.nav-search input:focus {
    outline: none;
    background: white;
    color: #333;
}
.nav-search button {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    width: 34px;
    height: 34px;
    color: white;
    cursor: pointer;
}
.nav-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-left: auto;
}
.nav-user-links {
    display: flex;
    align-items: center;
    gap: 10px;
}
.nav-user-link {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.9rem;
}
.nav-user-link:hover {
    background: rgba(255,255,255,0.15);
}
.nav-user-link i {
    font-size: 1rem;
}
.nav-cart {
    position: relative;
    width: 46px;
    height: 46px;
    background: rgba(255,255,255,0.12);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}
.nav-cart:hover { background: rgba(255,255,255,0.25); }
.cart-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
    min-width: 20px;
    text-align: center;
}
.nav-btn {
    padding: 12px 26px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
}
.login-btn {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
}
.login-btn:hover { background: #667eea; color: white; }
.register-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.register-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}
.mobile-toggle { display: none; background: none; border: none; color: white; font-size: 1.6rem; cursor: pointer; }

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    margin-top: 8px;
}

.search-results.show {
    display: block;
}

.search-result-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item:hover {
    background: #f8f9fa;
}

.search-result-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    margin-right: 12px;
}

.search-result-item .info {
    flex: 1;
}

.search-result-item .name {
    font-weight: 600;
    font-size: 0.95rem;
}

.search-result-item .price {
    color: #667eea;
    font-weight: 600;
    font-size: 0.9rem;
}

.search-result-item .category {
    font-size: 0.75rem;
    color: #777;
}

.search-no-result {
    padding: 20px;
    text-align: center;
    color: #777;
}

.nav-search {
    position: relative;
}

@media (max-width: 992px) {
    .nav-links, .nav-search, .nav-actions { display: none; }
    .mobile-toggle { display: block; }
}
</style>

<script>
document.getElementById('searchInput').addEventListener('input', function(e) {
    var query = e.target.value.trim();
    var results = document.getElementById('searchResults');
    
    if (query.length < 2) {
        results.classList.remove('show');
        return;
    }
    
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'ajax_search.php?search=' + encodeURIComponent(query), true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            results.innerHTML = xhr.responseText;
            if (xhr.responseText.trim()) {
                results.classList.add('show');
            } else {
                results.innerHTML = '<div class="search-no-result">No products found</div>';
                results.classList.add('show');
            }
        }
    };
    xhr.send();
});

document.addEventListener('click', function(e) {
    var search = document.getElementById('searchInput');
    var results = document.getElementById('searchResults');
    if (!search.contains(e.target) && !results.contains(e.target)) {
        results.classList.remove('show');
    }
});
</script>