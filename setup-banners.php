<?php
include 'config/config.php';

$conn = mysqli_connect('localhost', 'root', '', 'ecommerce');

$result = mysqli_query($conn, "SHOW TABLES LIKE 'banners'");
if (mysqli_num_rows($result) == 0) {
    mysqli_query($conn, "CREATE TABLE banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        subtitle VARCHAR(255),
        image VARCHAR(500),
        link VARCHAR(255) DEFAULT '#',
        button_text VARCHAR(100) DEFAULT 'Shop Now',
        status ENUM('active', 'inactive') DEFAULT 'active',
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    $banners = [
        ['Welcome to eShop', 'Discover amazing products at unbeatable prices!', 'https://tse1.mm.bing.net/th/id/OIP.Bi6H2bGIVTjoKBmc6_AVFwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3', 'products.php', 'Shop Now', 1],
        ['Electronics Sale!', 'Up to 50% off on latest gadgets', 'https://m.media-amazon.com/images/I/51cb55L4jbL._SL500_.jpg', 'products.php?category=Electronics', 'Shop Now', 2],
        ['New Arrivals!', 'Check out the latest products', 'https://th.bing.com/th/id/OIP.EoPYBVZHbnt_-fZCr2L4UwHaFj?o=7rm=3.rs=1&pid=ImgDetMain&o=7&rm=3', 'products.php', 'Explore', 3],
        ['Fashion Week!', 'Trending styles at great prices', 'https://tse1.mm.bing.net/th/id/OIP.UgX1Us6bYxHKwSm6_YdQzAHaGL?rs=1&pid=ImgDetMain&o=7&rm=3', 'products.php?category=Fashion', 'Shop Fashion', 4],
        ['Home & Living', 'Upgrade your living space', 'https://thumbs.dreamstime.com/z/digital-marketing-concept-business-development-lead-generation-social-network-media-communication-strategy-banner-110916326.jpg', 'products.php?category=Home Appliances', 'Shop Now', 5],
        ['Internet Marketing', 'Grow your business online', 'https://thumbs.dreamstime.com/z/internet-marketing-online-concept-illustration-text-icons-45660793.jpg', 'products.php', 'Learn More', 6],
        ['Digital Marketing', 'SEO & promotion strategies', 'https://image.shutterstock.com/z/stock-vector-digital-marketing-concept-social-network-and-media-communication-seo-sem-and-promotion-529566106.jpg', 'products.php?category=Electronics', 'Shop Now', 7],
        ['Online Shopping', 'Best deals at your fingertips', 'https://pin.it/oJElsh6Yx', 'products.php', 'Start Shopping', 8]
    ];
    
    foreach ($banners as $banner) {
        mysqli_query($conn, "INSERT INTO banners (title, subtitle, image, link, button_text, sort_order) VALUES ('$banner[0]', '$banner[1]', '$banner[2]', '$banner[3]', '$banner[4]', $banner[5])");
    }
    
    echo "Banners table created and populated!";
} else {
    mysqli_query($conn, "TRUNCATE TABLE banners");
    
    $banners = [
        ['Welcome to eShop', 'Discover amazing products at unbeatable prices!', 'https://tse1.mm.bing.net/th/id/OIP.Bi6H2bGIVTjoKBmc6_AVFwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3', 'products.php', 'Shop Now', 1],
        ['Electronics Sale!', 'Up to 50% off on latest gadgets', 'https://m.media-amazon.com/images/I/51cb55L4jbL._SL500_.jpg', 'products.php?category=Electronics', 'Shop Now', 2],
        ['New Arrivals!', 'Check out the latest products', 'https://th.bing.com/th/id/OIP.EoPYBVZHbnt_-fZCr2L4UwHaFj?o=7rm=3.rs=1&pid=ImgDetMain&o=7&rm=3', 'products.php', 'Explore', 3],
        ['Fashion Week!', 'Trending styles at great prices', 'https://tse1.mm.bing.net/th/id/OIP.UgX1Us6bYxHKwSm6_YdQzAHaGL?rs=1&pid=ImgDetMain&o=7&rm=3', 'products.php?category=Fashion', 'Shop Fashion', 4],
        ['Home & Living', 'Upgrade your living space', 'https://thumbs.dreamstime.com/z/digital-marketing-concept-business-development-lead-generation-social-network-media-communication-strategy-banner-110916326.jpg', 'products.php?category=Home Appliances', 'Shop Now', 5],
        ['Internet Marketing', 'Grow your business online', 'https://thumbs.dreamstime.com/z/internet-marketing-online-concept-illustration-text-icons-45660793.jpg', 'products.php', 'Learn More', 6],
        ['Digital Marketing', 'SEO & promotion strategies', 'https://image.shutterstock.com/z/stock-vector-digital-marketing-concept-social-network-and-media-communication-seo-sem-and-promotion-529566106.jpg', 'products.php?category=Electronics', 'Shop Now', 7],
        ['Online Shopping', 'Best deals at your fingertips', 'https://pin.it/oJElsh6Yx', 'products.php', 'Start Shopping', 8]
    ];
    
    foreach ($banners as $banner) {
        mysqli_query($conn, "INSERT INTO banners (title, subtitle, image, link, button_text, sort_order) VALUES ('$banner[0]', '$banner[1]', '$banner[2]', '$banner[3]', '$banner[4]', $banner[5])");
    }
    
    echo "Banners updated with new images!";
}
?>