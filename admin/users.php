<?php
$pageTitle = 'Users';
include 'config.php';

$search = $_GET['search'] ?? '';
$where = $search ? "WHERE role = 'user' AND (name LIKE '%$search%' OR email LIKE '%$search%')" : "WHERE role = 'user'";
$users = mysqli_query($conn, "SELECT * FROM users $where ORDER BY created_at DESC");
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
            <h1><i class="fas fa-users me-2"></i>Users</h1>
        </div>
        
        <div class="content-card">
            <div class="card-header">
                <form method="GET" class="d-flex gap-3 w-100">
                    <div class="search-box" style="flex:1;max-width:400px;">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?php echo $search; ?>">
                    </div>
                    <span class="badge" style="background:#f1f5f9;color:#64748b;padding:12px 18px;">
                        <i class="fas fa-users me-2"></i><?php echo mysqli_num_rows($users); ?> Users
                    </span>
                </form>
            </div>
            
            <div class="card-body p-0">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($users) == 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-users d-block mb-3"></i>
                                    <h4>No users found</h4>
                                    <p>Registered users will appear here</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php while ($user = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><span class="order-id">#<?php echo str_pad($user['id'], 3, '0', STR_PAD_LEFT); ?></span></td>
                            <td><strong><?php echo htmlspecialchars($user['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($user['address'] ?? 'N/A'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>