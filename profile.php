<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'My Account';
include 'config/config.php';

$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_image'])) {
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = 'assets/images/users/' . basename($image);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            mysqli_query($conn, "UPDATE users SET image = '$image' WHERE id = $user_id");
            $user['image'] = $image;
            $_SESSION['success'] = 'Image updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to upload image.';
        }
    }
    header('Location: profile.php');
    exit;
}

$orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE user_id = $user_id"));
$cart = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(quantity),0) as count FROM cart WHERE user_id = $user_id"));

include 'includes/header.php';
?>

<style>
.profile-img-container {
    position: relative;
    width: 120px;
    margin: 0 auto;
}

.profile-img-container img {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
}

.profile-img-container .fa-camera {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #667eea;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 3px solid white;
}

.profile-img-container input[type="file"] {
    display: none;
}
</style>

<div class="container py-5">
    <h2 class="mb-4">My Account - <?php echo htmlspecialchars($user['name']); ?></h2>
    
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <form method="POST" enctype="multipart/form-data" id="imageForm">
                        <div class="profile-img-container">
                            <?php if (!empty($user['image'])): ?>
                            <img src="assets/images/users/<?php echo htmlspecialchars($user['image']); ?>" id="previewImg">
                            <?php else: ?>
                            <i class="fas fa-user-circle fa-5x text-secondary mb-3" id="previewImg" style="font-size: 120px;"></i>
                            <?php endif; ?>
                            <label for="imageInput">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" name="image" id="imageInput" accept="image/*">
                        </div>
                        <button type="submit" name="update_image" class="btn btn-primary btn-sm mt-3" id="uploadBtn" style="display: none;">Upload Photo</button>
                    </form>
                    
                    <h6 class="mt-3"><?php echo htmlspecialchars($user['name']); ?></h6>
                    <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                    <a href="security.php" class="btn btn-sm btn-outline-primary">Change Password</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title mb-3">Account Information</h6>
                    <p class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                    <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?></p>
                    <p class="mb-0"><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?? 'Not set'); ?></p>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-4">
                    <div class="card bg-light">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-1"><?php echo $orders['count']; ?></h4>
                            <small class="text-muted">Orders</small>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card bg-light">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-1"><?php echo $cart['count']; ?></h4>
                            <small class="text-muted">Cart</small>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card bg-light">
                        <div class="card-body text-center py-3">
                            <h4 class="mb-1"><?php echo round((time() - strtotime($user['created_at'])) / 86400); ?></h4>
                            <small class="text-muted">Days</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('previewImg');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                var img = document.createElement('img');
                img.id = 'previewImg';
                img.src = e.target.result;
                img.style.width = '120px';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '50%';
                preview.parentNode.replaceChild(img, preview);
            }
        }
        reader.readAsDataURL(this.files[0]);
        document.getElementById('uploadBtn').style.display = 'inline-block';
    }
});
</script>

<?php include 'includes/footer.php'; ?>