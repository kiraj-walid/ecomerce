<?php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique en Ligne</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-store"></i>
            MyShop
        </a>
        
        <div class="nav-links">
            <a href="index.php" class="nav-link">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="shop.php" class="nav-link">
                <i class="fas fa-shopping-bag"></i>
                <span>Shop</span>
            </a>
            <a href="cart.php" class="nav-link cart-link">
                <i class="fas fa-shopping-cart"></i>
                <span>Cart</span>
            </a>
        </div>

        <div class="nav-actions">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-link">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
            <?php endif; ?>
        </div>

    </div>
</nav>
<style>
/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f0f2f5;
}

.navbar {
    background: #344454;
    padding: 10px 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.logo {
    color: #4CAF50;
    font-size: 1.5rem;
    text-decoration: none;
}

.nav-links {
    flex: 1;
    display: flex;
    justify-content: center;
    gap: 1.5rem;
}

.nav-actions {
    display: flex;
    justify-content: flex-end;
}

.nav-link {
    color: #fff;
    text-decoration: none;
    padding: 0.5rem 1rem;
    transition: background 0.3s, color 0.3s;
}

.nav-link:hover {
    background: #223040;
    color: #4CAF50;
}

</style>

</body>
</html>
