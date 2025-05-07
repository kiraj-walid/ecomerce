<?php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Boutique en Ligne</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a href="dashboard.php" class="navbar-logo">Admin</a>
        <div class="navbar-links">
            <a href="dashboard.php">Tableau de bord</a>
            <a href="products.php">Produits</a>
            <a href="users.php">Clients</a>
            <!-- <a href="orders.php">Commandes</a> -->
            <a href="../logout.php" class="logout">Déconnexion</a>
        </div>
    </div>
</nav>