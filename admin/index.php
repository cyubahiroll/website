<?php
$pageTitle = 'Dashboard';
include 'config.php';

$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status != 'cancelled'"))['total'];

$recentOrders = mysqli_query($conn, "SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
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
                <h2 class="mb-4">Dashboard</h2>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="admin-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Total Products</p>
                                    <h3 class="mb-0"><?php echo number_format($totalProducts); ?></h3>
                                </div>
                                <div class="icon blue"><i class="fas fa-box"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Total Orders</p>
                                    <h3 class="mb-0"><?php echo number_format($totalOrders); ?></h3>
                                </div>
                                <div class="icon green"><i class="fas fa-shopping-cart"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Total Users</p>
                                    <h3 class="mb-0"><?php echo number_format($totalUsers); ?></h3>
                                </div>
                                <div class="icon orange"><i class="fas fa-users"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="admin-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Revenue</p>
                                    <h3 class="mb-0">$<?php echo number_format($totalRevenue, 2); ?></h3>
                                </div>
                                <div class="icon purple"><i class="fas fa-dollar-sign"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Recent Orders</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($recentOrders) == 0): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                            <p class="text-muted mb-0">No orders yet</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php while ($order = mysqli_fetch_assoc($recentOrders)): ?>
                                    <tr>
                                        <td><span class="order-id">#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></span></td>
                                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                        <td><span class="amount">$<?php echo number_format($order['total_amount'], 2); ?></span></td>
                                        <td>
                                            <?php 
                                            $statusClass = match($order['status']) {
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'shipped' => 'primary',
                                                'delivered' => 'success',
                                                'cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                            ?>
                                            <span class="badge bg-<?php echo $statusClass; ?>"><?php echo ucfirst($order['status']); ?></span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>