<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Security';
include 'config/config.php';

$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));

if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    if (!password_verify($current, $user['password'])) {
        $_SESSION['error'] = 'Current password is incorrect';
    } elseif ($new !== $confirm) {
        $_SESSION['error'] = 'New passwords do not match';
    } elseif (strlen($new) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hash' WHERE id = $user_id");
        $_SESSION['success'] = 'Password changed successfully!';
        header('Location: profile.php');
        exit;
    }
}

include 'includes/header.php';
?>

<style>
.security-page {
    min-height: 60vh;
    padding: 40px 0;
}

.security-page h2 {
    color: #333;
    font-size: 1.8rem;
    margin-bottom: 30px;
}

.security-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
    max-width: 500px;
    margin: 0 auto;
}

.security-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    border: none;
}

.security-card .card-header h5 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.security-card .card-body {
    padding: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    color: #333;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-group input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e5e5e5;
    border-radius: 10px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
}

.form-group small {
    display: block;
    color: #777;
    margin-top: 6px;
    font-size: 0.85rem;
}

.btn-change {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
}

.btn-change:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-cancel {
    background: #f1f5f9;
    color: #64748b;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    margin-top: 10px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-cancel:hover {
    background: #e2e8f0;
}

.btn-group {
    display: flex;
    gap: 10px;
}

.btn-group .btn-change,
.btn-group .btn-cancel {
    flex: 1;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.alert-danger {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.alert-success {
    background: #d1fae5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
</style>

<div class="security-page">
    <div class="container">
        <div class="text-center">
            <h2><i class="fas fa-lock me-2"></i>Change Password</h2>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>
        
        <div class="security-card">
            <div class="card-header">
                <h5><i class="fas fa-key me-2"></i>Update Your Password</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" placeholder="Enter current password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" placeholder="Enter new password" required>
                        <small>At least 6 characters</small>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                    </div>
                    <div class="btn-group">
                        <a href="profile.php" class="btn-cancel">Cancel</a>
                        <button type="submit" name="change_password" class="btn-change">
                            <i class="fas fa-save me-2"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>