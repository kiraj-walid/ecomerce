<?php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique en Ligne</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav style="background:#333; color:#fff; padding:10px;">
    <a href="index.php" style="color:#fff;">Accueil</a> |
    <a href="search.php" style="color:#fff;">Recherche</a> |
    <a href="cart.php" style="color:#fff;">Panier</a>
    <?php if (is_logged_in()): ?>
        <?php if (is_admin()): ?>
            | <a href="admin/dashboard.php" style="color:#fff;">Admin</a>
        <?php else: ?>
            | <a href="profile.php" style="color:#fff;">Mon Profil</a>
        <?php endif; ?>
        | <a href="logout.php" style="color:#fff; font-weight:bold;">Déconnexion</a>
    <?php else: ?>
        | <a href="login.php" style="color:#fff;">Connexion</a>
        | <a href="register.php" style="color:#fff;">Inscription</a>
    <?php endif; ?>
</nav>
<hr> 