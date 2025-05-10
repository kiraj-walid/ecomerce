<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Récupérer les commandes du client
$stmt = $pdo->prepare('SELECT * FROM commandes WHERE utilisateur_id = ? ORDER BY date_commande DESC');
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include 'includes/header.php';
?>
<div class="orders-container">
    <h2 class="orders-title">Mes Commandes</h2>
    <?php if (count($orders) === 0) : ?>
        <p class="orders-empty">Vous n'avez pas encore passé de commande.</p>
    <?php else : ?>
        <?php foreach ($orders as $order) : ?>
            <div class="order-card">
                <div class="order-header">
                    <div class="order-number">Commande n°<?= $order['id'] ?></div>
                    <div class="order-date"><?= $order['date_commande'] ?></div>
                    <div class="order-status"><?= htmlspecialchars($order['statut']) ?></div>
                    <div class="order-payment">Paiement : <?= htmlspecialchars($order['mode_paiement']) ?></div>
                </div>
                <div class="order-content">
                    <h3 class="order-products-title">Produits commandés</h3>
                    <ul class="order-products-list">
                    <?php
                    $stmt_items = $pdo->prepare('SELECT lignes_commande.*, produits.nom, produits.prix FROM lignes_commande JOIN produits ON lignes_commande.produit_id = produits.id WHERE lignes_commande.commande_id = ?');
                    $stmt_items->execute([$order['id']]);
                    $items = $stmt_items->fetchAll();
                    $total = 0;
                    foreach ($items as $item) :
                        $total += $item['prix'] * $item['quantite'];
                    ?>
                        <li class="order-product-item">
                            <span class="order-product-name"><?= htmlspecialchars($item['nom']) ?></span>
                            <span class="order-product-quantity">x <?= $item['quantite'] ?></span>
                            <span class="order-product-price"><?= number_format($item['prix'], 2) ?> €</span>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                    <div class="order-total">
                        Total : <?= number_format($total, 2) ?> €
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <a href="index.php" class="orders-back-link">Retour à l'accueil</a>
</div>
<?php include 'includes/footer.php'; ?> 