<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}
include '../includes/header.php';
?>
<h2>Tableau de bord Administrateur</h2>
<p>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</p>
<ul>
    <li><a href="products.php">Gérer les produits</a></li>
    <li><a href="users.php">Gérer les clients</a></li>
    <li><a href="orders.php">Gérer les commandes</a></li>
    <li><a href="../logout.php">Déconnexion</a></li>
</ul>
<?php include '../includes/footer.php'; ?> 