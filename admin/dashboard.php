<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}
include '../includes/admin_header.php';
?>
<div class="admin-dashboard">
    <h1>Tableau de bord Administrateur</h1>
    <p class="admin-subtitle">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> !</p>
    <div class="admin-cards">
        <a href="products.php" class="admin-card">
            <span class="admin-card-title">Gérer les produits</span>
            <span class="admin-card-desc">Ajouter, modifier ou supprimer des produits</span>
        </a>
        <a href="users.php" class="admin-card">
            <span class="admin-card-title">Gérer les clients</span>
            <span class="admin-card-desc">Voir et gérer les comptes clients</span>
        </a>
        <a href="orders.php" class="admin-card">
            <span class="admin-card-title">Gérer les commandes</span>
            <span class="admin-card-desc">Suivre et traiter les commandes</span>
        </a>
    </div>
</div>
</body>
</html> 