<?php
$pageTitle = 'Store';
include 'config.php';

$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1"));
$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status != 'cancelled'"))['total'];
$pendingOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'"))['count'];
$deliveredOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status = 'delivered'"))['count'];
$lowStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE stock < 10"))['count'];
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
        <div class="page-title">
            <h1><i class="fas fa-store me-2"></i>Store Overview</h1>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-box"></i></div>
                <div class="stat-info">
                    <p>Total Products</p>
                    <h3><?php echo number_format($totalProducts); ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info">
                    <p>Total Orders</p>
                    <h3><?php echo number_format($totalOrders); ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <p>Total Users</p>
                    <h3><?php echo number_format($totalUsers); ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-info">
                    <p>Revenue</p>
                    <h3>$<?php echo number_format($totalRevenue, 2); ?></h3>
                </div>
            </div>
        </div>
        
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-clock me-2"></i>Order Status</h3>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Pending</span>
                        <span class="detail-value"><span class="status-badge warning"><?php echo $pendingOrders; ?></span></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Delivered</span>
                        <span class="detail-value"><span class="status-badge success"><?php echo $deliveredOrders; ?></span></span>
                    </div>
                </div>
            </div>
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-exclamation-triangle me-2"></i>Alerts</h3>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Low Stock</span>
                        <span class="detail-value"><span class="status-badge danger"><?php echo $lowStock; ?> items</span></span>
                    </div>
                </div>
            </div>
            <div class="content-card">
                <div class="card-header">
                    <h3><i class="fas fa-cog me-2"></i>Store Info</h3>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Name</span>
                        <span class="detail-value"><?php echo htmlspecialchars($settings['store_name'] ?? 'eShop'); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Currency</span>
                        <span class="detail-value"><?php echo htmlspecialchars($settings['currency'] ?? 'USD'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <h3><i class="fas fa-clock me-2"></i>Recent Orders</h3>
            </div>
            <div class="card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recentOrders = mysqli_query($conn, "SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10");
                        while ($order = mysqli_fetch_assoc($recentOrders)):
                            $statusClass = match($order['status']) {
                                'pending' => 'warning',
                                'processing' => 'info',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                default => 'secondary'
                            };
                        ?>
                        <tr>
                            <td><span class="order-id">#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></span></td>
                            <td><strong><?php echo htmlspecialchars($order['user_name']); ?></strong></td>
                            <td><span class="amount">$<?php echo number_format($order['total_amount'], 2); ?></span></td>
                            <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>