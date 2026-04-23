document.addEventListener('DOMContentLoaded', function() {
    const alertAlerts = document.querySelectorAll('.alert');
    alertAlerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

function updateQuantity(cartId, change) {
    const quantityInput = document.getElementById('quantity-' + cartId);
    let newQuantity = parseInt(quantityInput.value) + change;
    if (newQuantity < 1) newQuantity = 1;
    if (newQuantity > 99) newQuantity = 99;
    quantityInput.value = newQuantity;
    window.location.href = 'cart.php?update=' + cartId + '&quantity=' + newQuantity;
}

function removeFromCart(cartId) {
    if (confirm('Are you sure you want to remove this item from cart?')) {
        window.location.href = 'cart.php?remove=' + cartId;
    }
}

function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
        window.location.href = 'cart.php?clear=1';
    }
}

document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
    let valid = true;
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();
    
    if (!name) {
        document.getElementById('nameError').textContent = 'Name is required';
        valid = false;
    } else {
        document.getElementById('nameError').textContent = '';
    }
    
    if (!phone) {
        document.getElementById('phoneError').textContent = 'Phone is required';
        valid = false;
    } else if (!/^\d{10,15}$/.test(phone)) {
        document.getElementById('phoneError').textContent = 'Enter valid phone number';
        valid = false;
    } else {
        document.getElementById('phoneError').textContent = '';
    }
    
    if (!address) {
        document.getElementById('addressError').textContent = 'Address is required';
        valid = false;
    } else {
        document.getElementById('addressError').textContent = '';
    }
    
    if (!valid) {
        e.preventDefault();
    }
});

document.getElementById('productImage')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 200px;">';
        };
        reader.readAsDataURL(file);
    }
});

function searchProducts() {
    const searchTerm = document.getElementById('searchInput').value;
    if (searchTerm.length >= 2) {
        window.location.href = 'products.php?search=' + encodeURIComponent(searchTerm);
    }
}

document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchProducts();
    }
});

function filterByCategory(category) {
    window.location.href = 'products.php?category=' + encodeURIComponent(category);
}

function sortProducts(sort) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sort);
    window.location.href = url.toString();
}