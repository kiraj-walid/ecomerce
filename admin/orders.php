<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}

// Changement de statut
$message = '';
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    if (in_array($status, ['en_attente', 'payee', 'livree', 'annulee'])) {
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        if ($stmt->execute([$status, $order_id])) {
            $message = "Statut mis à jour.";
        } else {
            $message = "Erreur lors de la mise à jour du statut.";
        }
    }
}

// Récupérer toutes les commandes
$orders = $pdo->query('SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY created_at DESC')->fetchAll();

include '../includes/header.php';
?>
<h2>Gestion des Commandes</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<?php if (count($orders) === 0) : ?>
    <p>Aucune commande trouvée.</p>
<?php else : ?>
    <?php foreach ($orders as $order) : ?>
        <div style="border:1px solid #ccc; margin-bottom:20px; padding:10px;">
            <strong>Commande n°<?= $order['id'] ?></strong> du <?= $order['created_at'] ?><br>
            Client : <?= htmlspecialchars($order['username']) ?><br>
            Statut : <strong><?= htmlspecialchars($order['status']) ?></strong> |
            Paiement : <?= htmlspecialchars($order['payment_method']) ?><br>
            Total : <strong><?= number_format($order['total'], 2) ?> €</strong>
            <form method="post" action="orders.php" style="margin-top:5px;">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="status">
                    <option value="en_attente" <?= $order['status']=='en_attente'?'selected':'' ?>>En attente</option>
                    <option value="payee" <?= $order['status']=='payee'?'selected':'' ?>>Payée</option>
                    <option value="livree" <?= $order['status']=='livree'?'selected':'' ?>>Livrée</option>
                    <option value="annulee" <?= $order['status']=='annulee'?'selected':'' ?>>Annulée</option>
                </select>
                <button type="submit" name="update_status">Changer le statut</button>
            </form>
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
<a href="dashboard.php">Retour au tableau de bord</a>
<?php include '../includes/footer.php'; ?> 