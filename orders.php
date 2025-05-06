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
<h2>Mes Commandes</h2>
<?php if (count($orders) === 0) : ?>
    <p>Vous n'avez pas encore passé de commande.</p>
<?php else : ?>
    <?php foreach ($orders as $order) : ?>
        <div style="border:1px solid #ccc; margin-bottom:20px; padding:10px;">
            <strong>Commande n°<?= $order['id'] ?></strong> du <?= $order['date_commande'] ?><br>
            Statut : <?= htmlspecialchars($order['statut']) ?> | Paiement : <?= htmlspecialchars($order['mode_paiement']) ?><br>
            <u>Produits commandés :</u>
            <ul>
            <?php
            $stmt_items = $pdo->prepare('SELECT lignes_commande.*, produits.nom, produits.prix FROM lignes_commande JOIN produits ON lignes_commande.produit_id = produits.id WHERE lignes_commande.commande_id = ?');
            $stmt_items->execute([$order['id']]);
            $items = $stmt_items->fetchAll();
            $total = 0;
            foreach ($items as $item) :
                $total += $item['prix'] * $item['quantite'];
            ?>
                <li><?= htmlspecialchars($item['nom']) ?> x <?= $item['quantite'] ?> (<?= number_format($item['prix'], 2) ?> €)</li>
            <?php endforeach; ?>
            </ul>
            Total : <strong><?= number_format($total, 2) ?> €</strong>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<a href="index.php">Retour à l'accueil</a>
<?php include 'includes/footer.php'; ?> 