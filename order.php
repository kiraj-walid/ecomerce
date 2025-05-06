<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';

// Récupérer le panier
$stmt = $pdo->prepare('SELECT paniers.id as panier_id, produits.*, paniers.quantite FROM paniers JOIN produits ON paniers.produit_id = produits.id WHERE paniers.utilisateur_id = ?');
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['prix'] * $item['quantite'];
}

if (count($cart_items) === 0) {
    redirect('cart.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $mode_paiement = $_POST['payment_method'] ?? '';
    if ($mode_paiement === 'livraison' || $mode_paiement === 'carte') {
        // Créer la commande
        $stmt = $pdo->prepare('INSERT INTO commandes (utilisateur_id, statut, mode_paiement) VALUES (?, ?, ?)');
        if ($stmt->execute([$user_id, 'en_attente', $mode_paiement])) {
            $commande_id = $pdo->lastInsertId();
            // Insérer les lignes de commande
            $stmt_item = $pdo->prepare('INSERT INTO lignes_commande (commande_id, produit_id, quantite) VALUES (?, ?, ?)');
            foreach ($cart_items as $item) {
                $stmt_item->execute([$commande_id, $item['id'], $item['quantite']]);
            }
            // Vider le panier
            $pdo->prepare('DELETE FROM paniers WHERE utilisateur_id = ?')->execute([$user_id]);
            $message = "Commande passée avec succès !";
            $cart_items = [];
        } else {
            $message = "Erreur lors de la validation de la commande.";
        }
    } else {
        $message = "Veuillez choisir un mode de paiement.";
    }
}

include 'includes/header.php';
?>
<h2>Finaliser la commande</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<?php if (count($cart_items) > 0) : ?>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Produit</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Total</th>
    </tr>
    <?php foreach ($cart_items as $item) : ?>
    <tr>
        <td><?= htmlspecialchars($item['nom']) ?></td>
        <td><?= number_format($item['prix'], 2) ?> €</td>
        <td><?= $item['quantite'] ?></td>
        <td><?= number_format($item['prix'] * $item['quantite'], 2) ?> €</td>
    </tr>
    <?php endforeach; ?>
</table>
<p><strong>Total à payer : <?= number_format($total, 2) ?> €</strong></p>
<form method="post" action="order.php">
    <label>Mode de paiement :</label><br>
    <select name="payment_method" required>
        <option value="">--Choisir--</option>
        <option value="livraison">Paiement à la livraison</option>
        <option value="carte">Carte bancaire</option>
    </select><br><br>
    <button type="submit" name="place_order">Valider la commande</button>
</form>
<?php else : ?>
<p>Votre commande a été enregistrée. <a href="orders.php">Voir mes commandes</a></p>
<?php endif; ?>
<a href="cart.php">Retour au panier</a>
<?php include 'includes/footer.php'; ?> 