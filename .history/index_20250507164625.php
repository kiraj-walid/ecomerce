<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Fetch the 8 latest products
$products = $pdo->query('SELECT * FROM produits ORDER BY id DESC LIMIT 8')->fetchAll();

// Fetch categories for quick filters
$categories = $pdo->query("SELECT DISTINCT categorie FROM produits WHERE categorie IS NOT NULL AND categorie != ''")->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Hero Section -->
<div class="hero-section" style="background-image: url('assets/images/landingpage.webp');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-text">
            <h1>Bienvenue sur la Boutique en Ligne</h1>
            <p>Découvrez les dernières tendances, trouvez vos produits préférés et profitez d'une expérience d'achat moderne et sécurisée.</p>
        </div>
        <div class="hero-search">
            <form action="search.php" method="GET">
                <input type="text" name="q" placeholder="Rechercher un produit..." class="search-input">
                <button type="submit" class="hero-btn">Rechercher</button>
            </form>
        </div>
    </div>
</div>

<!-- Category Filters -->
<?php if (!empty($categories)) : ?>
    <div class="category-filters">
        <h2>Parcourez nos catégories</h2>
        <?php foreach ($categories as $cat) : ?>
            <a href="search.php?category=<?= urlencode($cat) ?>" class="category-card"><?= htmlspecialchars($cat) ?></a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Featured Products Section -->
<h2 class="recent-products-title">Produits récents</h2>
<?php if (count($products) === 0) : ?>
    <p class="no-products">Aucun produit disponible.</p>
<?php else : ?>
    <div class="product-listing">
        <?php foreach ($products as $prod) : ?>
            <div class="product-card">
                <?php
                $stmt_imgs = $pdo->prepare('SELECT image FROM images_produit WHERE produit_id = ?');
                $stmt_imgs->execute([$prod['id']]);
                $images = $stmt_imgs->fetchAll(PDO::FETCH_COLUMN);
                ?>
                <div class="product-gallery">
                    <?php if ($images && count($images) > 0): ?>
                        <img src="assets/images/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" class="product-image">
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <strong><?= htmlspecialchars($prod['nom']) ?></strong><br>
                    <div class="product-rating">★★★★☆</div>
                    <span><?= number_format($prod['prix'], 2) ?> MAD</span><br>
                    <span class="product-category"><?= htmlspecialchars($prod['categorie']) ?></span><br>
                    <div class="product-desc">
                        <?= htmlspecialchars(mb_strimwidth($prod['description'], 0, 80, '...')) ?>
                    </div>
                    <div class="product-actions">
                        <a href="cart.php?add=<?= $prod['id'] ?>" class="add-to-cart-btn">Ajouter au panier</a>
                        <a href="product.php?id=<?= $prod['id'] ?>" class="product-details-btn">Voir le produit</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Promotions Section -->
<section id="promotions">
    <h2>Offres Spéciales</h2>
    <p>Soldes jusqu'à 50% sur les produits sélectionnés</p>
    <a href="sales-page.php" class="promo-btn">Voir les offres</a>
</section>

<!-- Footer Section -->
<?php include 'includes/footer.php'; ?>
