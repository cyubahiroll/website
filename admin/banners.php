<?php
$pageTitle = 'Banners';
include 'config.php';

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_POST['add_banner'])) {
    $title = trim($_POST['title']);
    $subtitle = trim($_POST['subtitle']);
    $link = $_POST['link'];
    $button_text = $_POST['button_text'];
    $sort_order = $_POST['sort_order'];
    $status = $_POST['status'];
    
    $image = '';
    if ($_FILES['image']['name']) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/banners/' . $image);
    }
    
    if ($image) {
        mysqli_query($conn, "INSERT INTO banners (title, subtitle, image, link, button_text, sort_order, status) VALUES ('$title', '$subtitle', '$image', '$link', '$button_text', '$sort_order', '$status')");
        $_SESSION['success'] = 'Banner added successfully';
    }
    header('Location: banners.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM banners WHERE id = $id");
    $_SESSION['success'] = 'Banner deleted successfully';
    header('Location: banners.php');
    exit;
}

if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $current = $_GET['status'];
    $new = $current == 'active' ? 'inactive' : 'active';
    mysqli_query($conn, "UPDATE banners SET status = '$new' WHERE id = $id");
    $_SESSION['success'] = 'Banner status updated';
    header('Location: banners.php');
    exit;
}

$banners = mysqli_query($conn, "SELECT * FROM banners ORDER BY sort_order ASC");
$showAddForm = isset($_GET['add']);
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
        <?php if ($showAddForm): ?>
        <div class="page-title">
            <h1><i class="fas fa-plus me-2"></i>Add Banner</h1>
            <a href="banners.php" class="btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
        </div>
        
        <div class="content-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Banner Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Subtitle</label>
                                    <input type="text" name="subtitle" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Link URL</label>
                                    <input type="text" name="link" class="form-control" value="products.php">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Button Text</label>
                                    <input type="text" name="button_text" class="form-control" value="Shop Now">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" class="form-control" value="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Banner Image</label>
                            <div class="upload-box">
                                <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload</p>
                                <small>Recommended: 1920x600px</small>
                                <img class="image-preview" id="imgPreview" style="display:none;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" name="add_banner" class="btn-primary">
                            <i class="fas fa-save me-2"></i>Save Banner
                        </button>
                        <a href="banners.php" class="btn-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
                    </div>
                </div>
            </form>
        </div>
        
        <?php else: ?>
        
        <div class="page-title">
            <h1><i class="fas fa-images me-2"></i>Banners</h1>
            <a href="banners.php?add=1" class="btn-primary"><i class="fas fa-plus me-2"></i>Add Banner</a>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="content-card">
            <div class="card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th width="150">Image</th>
                            <th>Title</th>
                            <th>Subtitle</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($banners) == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-images d-block mb-3"></i>
                                    <h4>No banners found</h4>
                                    <p>Add banners to display on homepage</p>
                                    <a href="banners.php?add=1" class="btn-primary mt-3"><i class="fas fa-plus me-2"></i>Add Banner</a>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php while ($banner = mysqli_fetch_assoc($banners)): ?>
                        <tr>
                            <td><span class="order-id">#<?php echo str_pad($banner['id'], 2, '0', STR_PAD_LEFT); ?></span></td>
                            <td>
                                <?php if ($banner['image']): ?>
                                <img src="../assets/images/banners/<?php echo $banner['image']; ?>" class="product-thumb">
                                <?php else: ?>
                                <div class="product-thumb d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($banner['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($banner['subtitle'] ?? '-'); ?></td>
                            <td><?php echo $banner['sort_order']; ?></td>
                            <td>
                                <a href="?toggle=<?php echo $banner['id']; ?>&status=<?php echo $banner['status']; ?>" class="status-badge <?php echo $banner['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($banner['status']); ?>
                                </a>
                            </td>
                            <td>
                                <a href="?delete=<?php echo $banner['id']; ?>" class="btn-action delete" onclick="return confirm('Delete this banner?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>
    
    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imgPreview').src = e.target.result;
                document.getElementById('imgPreview').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>