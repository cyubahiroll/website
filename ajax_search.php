<?php
include 'config/config.php';

$search = trim($_GET['search'] ?? '');

if (strlen($search) < 2) {
    exit;
}

$searchTerm = '%' . $search . '%';
$stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR category LIKE ? OR description LIKE ? LIMIT 10");
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo '<div class="search-no-result">No products found</div>';
} else {
    while ($product = $result->fetch_assoc()) {
        echo '<a href="product.php?id=' . $product['id'] . '" class="search-result-item">';
        if ($product['image']) {
            echo '<img src="assets/images/products/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">';
        } else {
            echo '<div style="width:50px;height:50px;background:#f0f0f0;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:12px;"><i class="fas fa-image text-muted"></i></div>';
        }
        echo '<div class="info">';
        echo '<div class="name">' . htmlspecialchars($product['name']) . '</div>';
        echo '<div class="category">' . htmlspecialchars($product['category'] ?? 'Uncategorized') . '</div>';
        echo '</div>';
        echo '<div class="price">$' . number_format($product['price'], 2) . '</div>';
        echo '</a>';
    }
}