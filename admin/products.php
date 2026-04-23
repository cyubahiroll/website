<?php
$pageTitle = 'Products';
include 'config.php';

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];
    $sizes = $_POST['sizes'];
    
    $image = '';
    if ($_FILES['image']['name']) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/products/' . $image);
    }
    
    $stmt = $conn->prepare("INSERT INTO products (name, price, image, description, category, stock, status, sizes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsssss", $name, $price, $image, $description, $category, $stock, $status, $sizes);
    $stmt->execute();
    $_SESSION['success'] = 'Product added successfully';
    header('Location: products.php');
    exit;
}

if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];
    $sizes = $_POST['sizes'];
    
    if ($_FILES['image']['name']) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/products/' . $image);
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ?, description = ?, category = ?, stock = ?, status = ?, sizes = ? WHERE id = ?");
        $stmt->bind_param("sdssssssi", $name, $price, $image, $description, $category, $stock, $status, $sizes, $id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, category = ?, stock = ?, status = ?, sizes = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $name, $price, $description, $category, $stock, $status, $sizes, $id);
    }
    $stmt->execute();
    $_SESSION['success'] = 'Product updated successfully';
    header('Location: products.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    $_SESSION['success'] = 'Product deleted successfully';
    header('Location: products.php');
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
$editProduct = null;
$showAddForm = isset($_GET['add']) || isset($_GET['edit']);
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id = $id"));
    $showAddForm = true;
}

$categories = [
    'Electronics', 'Mobile Phones', 'Laptops', 'Tablets', 'Phone Accessories',
    'Computer Accessories', 'Gaming Consoles', 'Video Games', 'Cameras',
    'Headphones & Earbuds', 'Home Appliances', 'Kitchen Appliances', 'Televisions',
    'Smart Home Devices', 'Fashion (Men)', 'Fashion (Women)', 'Shoes (Men)',
    'Shoes (Women)', 'Bags & Handbags', 'Watches', 'Jewelry',
    'Beauty Products', 'Fitness Equipment', 'Books'
];

$search = $_GET['search'] ?? '';
$catFilter = $_GET['category'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$where = [];
if ($search) $where[] = "name LIKE '%$search%'";
if ($catFilter) $where[] = "category = '$catFilter'";
if ($statusFilter) $where[] = "status = '$statusFilter'";
$whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
$products = mysqli_query($conn, "SELECT * FROM products $whereClause ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-md-10 p-4">
                <?php if ($showAddForm): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-plus me-2"></i><?php echo $editProduct ? 'Edit Product' : 'Add Product'; ?></h2>
                    <a href="products.php" class="btn btn-secondary">Back</a>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <?php if ($editProduct): ?>
                            <input type="hidden" name="id" value="<?php echo $editProduct['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Product Name</label>
                                            <input type="text" name="name" class="form-control" value="<?php echo $editProduct['name'] ?? ''; ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Price ($)</label>
                                            <input type="number" name="price" step="0.01" class="form-control" value="<?php echo $editProduct['price'] ?? ''; ?>" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Stock</label>
                                            <input type="number" name="stock" class="form-control" value="<?php echo $editProduct['stock'] ?? 0; ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" <?php echo ($editProduct['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="inactive" <?php echo ($editProduct['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Category</label>
                                            <select name="category" class="form-select">
                                                <?php foreach ($categories as $cat): ?>
                                                <option value="<?php echo $cat; ?>" <?php echo ($editProduct['category'] ?? '') === $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Sizes</label>
                                            <input type="text" name="sizes" class="form-control" value="<?php echo $editProduct['sizes'] ?? 'S,M,L,XL'; ?>">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="4"><?php echo $editProduct['description'] ?? ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Product Image</label>
                                    <div class="upload-box">
                                        <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                                        <?php if ($editProduct['image']): ?>
                                        <img src="../assets/images/products/<?php echo $editProduct['image']; ?>" class="image-preview" id="imgPreview">
                                        <p class="mt-2 mb-0">Click to change</p>
                                        <?php else: ?>
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Click to upload</p>
                                        <small>800x800px</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" name="<?php echo $editProduct ? 'update_product' : 'add_product'; ?>" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i><?php echo $editProduct ? 'Update' : 'Save'; ?>
                                </button>
                                <a href="products.php" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php else: ?>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-box me-2"></i>Products</h2>
                    <a href="products.php?add=1" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Product</a>
                </div>
                
                <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="d-flex gap-3">
                            <div class="input-group" style="max-width: 300px;">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo $search; ?>">
                            </div>
                            <select name="category" class="form-select" style="width: auto;" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $catFilter === $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="status" class="form-select" style="width: auto;" onchange="this.form.submit()">
                                <option value="">All Status</option>
                                <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">All Products</h4>
                        <span class="badge bg-primary"><?php echo mysqli_num_rows($products); ?> Products</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($products) == 0): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-box-open fa-2x text-muted mb-2 d-block"></i>
                                            <p class="text-muted mb-0">No products found</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php while ($product = mysqli_fetch_assoc($products)): ?>
                                    <tr>
                                        <td>#<?php echo str_pad($product['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                        <td>
                                            <?php if ($product['image']): ?>
                                            <img src="../assets/images/products/<?php echo $product['image']; ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                            <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                                        <td><span class="amount">$<?php echo number_format($product['price'], 2); ?></span></td>
                                        <td><?php echo $product['stock']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($product['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="products.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <a href="products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imgPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>