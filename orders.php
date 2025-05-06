<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Récupérer les commandes du client
$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

include 'includes/header.php';
?>
<h2>Mes Commandes</h2>
<?php if (count($orders) === 0) : ?>
    <p>Vous n'avez pas encore passé de commande.</p>
<?php else : ?>
    <?php foreach ($orders as $order) : ?>
        <div style="border:1px solid #ccc; margin-bottom:20px; padding:10px;">
            <strong>Commande n°<?= $order['id'] ?></strong> du <?= $order['created_at'] ?><br>
            Statut : <?= htmlspecialchars($order['status']) ?> | Paiement : <?= htmlspecialchars($order['payment_method']) ?><br>
            Total : <strong><?= number_format($order['total'], 2) ?> €</strong>
            <br>
            <u>Produits commandés :</u>
            <ul>
            <?php
            $stmt_items = $pdo->prepare('SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = ?');
            $stmt_items->execute([$order['id']]);
            $items = $stmt_items->fetchAll();
            foreach ($items as $item) : ?>
                <li><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?> (<?= number_format($item['price'], 2) ?> €)</li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<a href="index.php">Retour à l'accueil</a>
<?php include 'includes/footer.php'; ?> 