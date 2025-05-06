<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';

// Ajouter un produit au panier
if (isset($_GET['add'])) {
    $produit_id = intval($_GET['add']);
    $stmt = $pdo->prepare('SELECT id FROM paniers WHERE utilisateur_id = ? AND produit_id = ?');
    $stmt->execute([$user_id, $produit_id]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare('UPDATE paniers SET quantite = quantite + 1 WHERE utilisateur_id = ? AND produit_id = ?');
        $stmt->execute([$user_id, $produit_id]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO paniers (utilisateur_id, produit_id, quantite) VALUES (?, ?, 1)');
        $stmt->execute([$user_id, $produit_id]);
    }
    $message = "Produit ajouté au panier.";
}

// Modifier la quantité
if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $panier_id => $qty) {
        $qty = max(1, intval($qty));
        $stmt = $pdo->prepare('UPDATE paniers SET quantite = ? WHERE id = ? AND utilisateur_id = ?');
        $stmt->execute([$qty, $panier_id, $user_id]);
    }
    $message = "Quantités mises à jour.";
}

// Supprimer un produit du panier
if (isset($_GET['delete'])) {
    $panier_id = intval($_GET['delete']);
    $stmt = $pdo->prepare('DELETE FROM paniers WHERE id = ? AND utilisateur_id = ?');
    $stmt->execute([$panier_id, $user_id]);
    $message = "Produit supprimé du panier.";
}

// Récupérer le panier
$stmt = $pdo->prepare('SELECT paniers.id as panier_id, produits.*, paniers.quantite FROM paniers JOIN produits ON paniers.produit_id = produits.id WHERE paniers.utilisateur_id = ?');
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['prix'] * $item['quantite'];
}

include 'includes/header.php';
?>
<h2>Mon Panier</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<?php if (count($cart_items) === 0) : ?>
    <p>Votre panier est vide.</p>
<?php else : ?>
<form method="post" action="cart.php">
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Produit</th>
        <th>Prix</th>
        <th>Quantité</th>
        <th>Total</th>
        <th>Action</th>
    </tr>
    <?php foreach ($cart_items as $item) : ?>
    <tr>
        <td><?= htmlspecialchars($item['nom']) ?></td>
        <td><?= number_format($item['prix'], 2) ?> €</td>
        <td><input type="number" name="qty[<?= $item['panier_id'] ?>]" value="<?= $item['quantite'] ?>" min="1"></td>
        <td><?= number_format($item['prix'] * $item['quantite'], 2) ?> €</td>
        <td><a href="cart.php?delete=<?= $item['panier_id'] ?>" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<button type="submit" name="update_qty">Mettre à jour les quantités</button>
</form>
<p><strong>Total : <?= number_format($total, 2) ?> €</strong></p>
<a href="order.php">Finaliser la commande</a>
<?php endif; ?>
<?php include 'includes/footer.php'; ?> 