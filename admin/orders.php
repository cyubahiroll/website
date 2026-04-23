<?php
$pageTitle = 'Orders';
include 'config.php';

if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE orders SET status = '$status' WHERE id = $id");
    $_SESSION['success'] = 'Order status updated';
    header('Location: orders.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$where = $statusFilter ? "WHERE o.status = '$statusFilter'" : '';
$orders = mysqli_query($conn, "SELECT o.*, u.name as user_name, u.email as user_email 
    FROM orders o LEFT JOIN users u ON o.user_id = u.id $where ORDER BY o.created_at DESC");

$viewOrder = null;
$orderItems = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $viewOrder = mysqli_fetch_assoc(mysqli_query($conn, "SELECT o.*, u.name as user_name, u.email as user_email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = $id"));
    $orderItems = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $id");
}

$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
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
            <h1><i class="fas fa-shopping-cart me-2"></i>Orders</h1>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($viewOrder): ?>
        <div class="content-card mb-4">
            <div class="card-header">
                <h3><i class="fas fa-receipt me-2"></i>Order #<?php echo str_pad($viewOrder['id'], 4, '0', STR_PAD_LEFT); ?></h3>
                <a href="orders.php" class="btn-secondary"><i class="fas fa-times me-2"></i>Close</a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-user me-2"></i>Customer Details</h5>
                        <div class="detail-row">
                            <span class="detail-label">Name</span>
                            <span class="detail-value"><?php echo htmlspecialchars($viewOrder['user_name']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value"><?php echo htmlspecialchars($viewOrder['user_email']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value"><?php echo htmlspecialchars($viewOrder['phone']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Address</span>
                            <span class="detail-value"><?php echo nl2br(htmlspecialchars($viewOrder['address'])); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i>Order Details</h5>
                        <div class="detail-row">
                            <span class="detail-label">Order ID</span>
                            <span class="detail-value">#<?php echo str_pad($viewOrder['id'], 4, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date</span>
                            <span class="detail-value"><?php echo date('M d, Y g:i A', strtotime($viewOrder['created_at'])); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Total</span>
                            <span class="detail-value amount">$<?php echo number_format($viewOrder['total_amount'], 2); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status</span>
                            <span class="detail-value">
                                <?php 
                                $statusClass = match($viewOrder['status']) {
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'primary',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($viewOrder['status']); ?></span>
                            </span>
                        </div>
                        <div class="detail-row">
                            <form method="POST" class="d-flex align-items-center gap-2 mt-2">
                                <input type="hidden" name="id" value="<?php echo $viewOrder['id']; ?>">
                                <select name="status" class="form-select" style="width:auto;">
                                    <option value="pending" <?php echo $viewOrder['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $viewOrder['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $viewOrder['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $viewOrder['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $viewOrder['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn-primary">
                                    <i class="fas fa-save me-2"></i>Update
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <h5 class="mt-4 mb-3"><i class="fas fa-box me-2"></i>Order Items</h5>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = mysqli_fetch_assoc($orderItems)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td class="amount">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        
        <div class="content-card">
            <div class="card-header">
                <form method="GET" class="d-flex gap-3">
                    <select name="status" class="form-select" style="width:auto;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="processing" <?php echo $statusFilter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $statusFilter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $statusFilter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </form>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($orders) == 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-shopping-cart d-block mb-3"></i>
                                    <h4>No orders found</h4>
                                    <p>Orders will appear here</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                        <?php 
                        $orderStatusClass = match($order['status']) {
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
                            <td>
                                <strong><?php echo htmlspecialchars($order['user_name']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($order['user_email']); ?></small>
                            </td>
                            <td><span class="amount">$<?php echo number_format($order['total_amount'], 2); ?></span></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="form-select" style="width:auto;padding:6px 12px;font-size:0.8rem;" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn-action"><i class="fas fa-eye"></i></a>
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
</body>
</html>