<?php
$pageTitle = 'Products - eShop';
include 'config/config.php';
include 'includes/header.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'newest';

$where = [];
$params = [];
$types = '';

if ($search) {
    $where[] = "(name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($category) {
    $where[] = "category = ?";
    $params[] = $category;
    $types .= 's';
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) . ' AND ' : 'WHERE ';
$whereClause .= "status = 'active'";

$query = "SELECT * FROM products $whereClause ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();

$cats = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE status = 'active'");
?>

<link rel="stylesheet" href="assets/css/home.css">

<style>
.products-page {
    padding: 40px 0;
}
.products-header {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    color: white;
}
.products-header h2 {
    margin: 0;
    font-weight: 700;
}
.products-header p {
    margin: 10px 0 0;
    opacity: 0.8;
}
.filter-sidebar {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}
.filter-sidebar .filter-header {
    background: #f8fafc;
    padding: 20px 25px;
    border-bottom: 1px solid #e2e8f0;
}
.filter-sidebar .filter-header h5 {
    margin: 0;
    font-weight: 700;
    color: #1e293b;
}
.filter-sidebar .filter-body {
    padding: 25px;
}
.filter-sidebar .form-control,
.filter-sidebar .form-select {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 15px;
    transition: all 0.3s;
}
.filter-sidebar .form-control:focus,
.filter-sidebar .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.filter-group {
    margin-bottom: 25px;
}
.filter-group label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 10px;
    display: block;
}
.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.category-list li a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    color: #64748b;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
}
.category-list li a:hover,
.category-list li a.active {
    background: #2563eb;
    color: white;
}
.category-list li a .count {
    background: rgba(0,0,0,0.1);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.75rem;
}
.category-list li a:hover .count,
.category-list li a.active .count {
    background: rgba(255,255,255,0.2);
}
.filter-btn {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    font-weight: 600;
}
.products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}
.product-card-item {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: all 0.3s;
    border: 1px solid #f1f5f9;
}
.product-card-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    border-color: #2563eb;
}
.product-card-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: #f8fafc;
}
.product-card-item .card-body {
    padding: 20px;
}
.product-card-item .product-category {
    color: #94a3b8;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.product-card-item .product-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 8px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.product-card-item .product-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2563eb;
    margin-bottom: 15px;
}
.product-card-item .product-actions {
    display: flex;
    gap: 10px;
}
.product-card-item .product-actions .btn {
    flex: 1;
    border-radius: 8px;
    font-weight: 500;
    padding: 10px;
}
.empty-products {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 16px;
}
.empty-products i {
    font-size: 60px;
    color: #d1d5db;
    margin-bottom: 20px;
}
.empty-products h4 {
    color: #6b7280;
    margin-bottom: 10px;
}
.results-count {
    color: #64748b;
    margin-bottom: 20px;
}
@media (max-width: 992px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .col-lg-4 { margin-bottom: 20px; }
}
@media (max-width: 576px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container products-page">
    <div class="products-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2><i class="fas fa-box-open me-2"></i> All Products</h2>
                <p>Browse our complete collection of products</p>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="results-count"><?php echo $products->num_rows; ?> products found</span>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="filter-sidebar">
                <div class="filter-header">
                    <h5><i class="fas fa-filter me-2"></i> Filters</h5>
                </div>
                <div class="filter-body">
                    <form method="GET">
                        <div class="filter-group">
                            <label>Search</label>
                            <input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search products...">
                        </div>
                        <div class="filter-group">
                            <label>Category</label>
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                <?php mysqli_data_seek($cats, 0); ?>
                                <?php while ($c = mysqli_fetch_assoc($cats)): ?>
                                <option value="<?php echo $c['category']; ?>" <?php echo $category === $c['category'] ? 'selected' : ''; ?>><?php echo $c['category']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary filter-btn">
                            <i class="fas fa-search me-2"></i> Apply Filters
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <?php if ($products->num_rows === 0): ?>
            <div class="empty-products">
                <i class="fas fa-search"></i>
                <h4>No Products Found</h4>
                <p>Try adjusting your search or filter criteria</p>
                <a href="products.php" class="btn btn-primary mt-3">Clear Filters</a>
            </div>
            <?php else: ?>
            <div class="products-grid">
                <?php mysqli_data_seek($products, 0); ?>
                <?php while ($product = mysqli_fetch_assoc($products)): ?>
                <div class="product-card-item">
                    <?php if ($product['image']): ?>
                    <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                    <div style="height:200px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;color:#cbd5e1;">
                        <i class="fas fa-image fa-3x"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="product-category"><?php echo htmlspecialchars($product['category']); ?></span>
                        <h5 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <div class="product-actions">
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="cart.php?add=<?php echo $product['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-cart-plus me-1"></i> Add
                            </a>
                            <?php else: ?>
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>