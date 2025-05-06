<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Récupérer les 8 derniers produits
$products = $pdo->query('SELECT * FROM produits ORDER BY id DESC LIMIT 8')->fetchAll();
?>
<h1>Bienvenue sur la Boutique en Ligne</h1>
<p>
    <a href="search.php">Rechercher un produit</a> |
    <a href="cart.php">Voir mon panier</a> |
    <a href="profile.php">Mon profil</a>
</p>
<h2>Produits récents</h2>
<?php if (count($products) === 0) : ?>
    <p>Aucun produit disponible.</p>
<?php else : ?>
    <div style="display:flex; flex-wrap:wrap; gap:20px;">
    <?php foreach ($products as $prod) : ?>
        <div style="border:1px solid #ccc; padding:10px; width:220px; background:#fff;">
            <?php if (!empty($prod['image'])) : ?>
                <img src="assets/images/<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" style="max-width:200px; max-height:120px;"><br>
            <?php endif; ?>
            <strong><?= htmlspecialchars($prod['nom']) ?></strong><br>
            <span><?= number_format($prod['prix'], 2) ?> €</span><br>
            <span><?= htmlspecialchars($prod['categorie']) ?></span><br>
            <a href="cart.php?add=<?= $prod['id'] ?>">Ajouter au panier</a>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?> 