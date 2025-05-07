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
        $stmt = $pdo->prepare('UPDATE commandes SET statut = ? WHERE id = ?');
        if ($stmt->execute([$status, $order_id])) {
            $message = "Statut mis à jour.";
        } else {
            $message = "Erreur lors de la mise à jour du statut.";
        }
    }
}

// Récupérer toutes les commandes
$orders = $pdo->query('SELECT commandes.*, utilisateurs.nom as client_nom FROM commandes JOIN utilisateurs ON commandes.utilisateur_id = utilisateurs.id ORDER BY date_commande DESC')->fetchAll();

include '../includes/admin_header.php';
?>
<div class="admin-container">
<h2>Gestion des Commandes</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<?php if (count($orders) === 0) : ?>
    <p>Aucune commande trouvée.</p>
<?php else : ?>
    <?php foreach ($orders as $order) : ?>
        <div style="border:1px solid #e1e4ea; margin-bottom:20px; padding:10px; border-radius:6px; background:#f9fbfd;">
            <strong>Commande n°<?= $order['id'] ?></strong> du <?= $order['date_commande'] ?><br>
            Client : <?= htmlspecialchars($order['client_nom']) ?><br>
            Statut : <strong><?= htmlspecialchars($order['statut']) ?></strong> |
            Paiement : <?= htmlspecialchars($order['mode_paiement']) ?><br>
            <form method="post" action="orders.php" style="margin-top:5px;">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="status">
                    <option value="en_attente" <?= $order['statut']=='en_attente'?'selected':'' ?>>En attente</option>
                    <option value="payee" <?= $order['statut']=='payee'?'selected':'' ?>>Payée</option>
                    <option value="livree" <?= $order['statut']=='livree'?'selected':'' ?>>Livrée</option>
                    <option value="annulee" <?= $order['statut']=='annulee'?'selected':'' ?>>Annulée</option>
                </select>
                <button type="submit" name="update_status" class="button-admin">Changer le statut</button>
            </form>
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
<a href="dashboard.php" class="button-admin" style="margin-top:32px;">Retour au tableau de bord</a>
</div>
</body>
</html> 