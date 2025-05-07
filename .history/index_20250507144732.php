<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Fetch categories for quick filters
$categoriesStmt = $pdo->query("SELECT DISTINCT categorie FROM produits WHERE categorie IS NOT NULL AND categorie != ''");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch all products and their images in a single query
$productsStmt = $pdo->query('SELECT p.id, p.nom, p.prix, p.categorie, p.description, i.image FROM produits p LEFT JOIN images_produit i ON p.id = i.produit_id ORDER BY p.id DESC');
$productsData = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

// Group images by product ID
$products = [];
foreach ($productsData as $prod) {
    $prodId = $prod['id'];
    if (!isset($products[$prodId])) {
        $products[$prodId] = [
            'id' => $prodId,
            'nom' => htmlspecialchars($prod['nom']),
            'prix' => number_format($prod['prix'], 2),
            'categorie' => htmlspecialchars($prod['categorie']),
            'description' => htmlspecialchars(mb_strimwidth($prod['description'], 0, 80, '...')),
            'images' => []
        ];
    }
    if ($prod['image']) {
        $products[$prodId]['images'][] = htmlspecialchars($prod['image']);
    }
}
?>

<div class="hero-section">
    <img src="assets/images/landingpage.webp" alt="Boutique en Ligne" class="hero-bg-img">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-text">
            <h1>Bienvenue sur la Boutique en Ligne</h1>
            <p>Découvrez les dernières tendances, trouvez vos produits préférés et profitez d'une expérience d'achat moderne et sécurisée.</p>
        </div>
    </div>
</div>

<?php if (!empty($categories)) : ?>
<div class="category-filters">
    <?php foreach ($categories as $cat) : ?>
        <a href="search.php?category=<?= urlencode($cat) ?>" class="category-card"><?= $cat ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="search-bar">
    <form action="search.php" method="GET">
        <input type="text" name="q" placeholder="Rechercher un produit..." class="search-input">
        <button type="submit" class="search-btn">Rechercher</button>
    </form>
</div>

<h2 class="recent-products-title">Voir tous les produits</h2>

<?php if (empty($products)) : ?>
    <p class="no-products">Aucun produit disponible.</p>
<?php else : ?>
    <div class="product-listing">
        <?php foreach ($products as $prod) : ?>
            <?php include 'product-card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
