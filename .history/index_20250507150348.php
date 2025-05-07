<!-- <?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Récupérer les 8 derniers produits
$products = $pdo->query('SELECT * FROM produits ORDER BY id DESC LIMIT 8')->fetchAll();

// Fetch categories for quick filters
$categories = $pdo->query("SELECT DISTINCT categorie FROM produits WHERE categorie IS NOT NULL AND categorie != ''")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="hero-section">
    <img src="assets/images/landingpage.webp" alt="Boutique en Ligne" class="hero-bg-img">
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

<?php if (!empty($categories)) : ?>
<div class="category-filters">
    <?php foreach ($categories as $cat) : ?>
        <a href="search.php?category=<?= urlencode($cat) ?>" class="category-card"><?= htmlspecialchars($cat) ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

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
                    <span onclick="prevImg(<?= $prod['id'] ?>)" class="gallery-arrow gallery-arrow-left">
                        <svg width="24" height="24" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <img src="assets/images/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" id="main-img-<?= $prod['id'] ?>">
                    <span onclick="nextImg(<?= $prod['id'] ?>)" class="gallery-arrow gallery-arrow-right">
                        <svg width="24" height="24" viewBox="0 0 24 24"><polyline points="9 6 15 12 9 18" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <?php if (count($images) > 1): ?>
                        <script>
                        var imgs<?= $prod['id'] ?> = <?= json_encode($images) ?>;
                        var idx<?= $prod['id'] ?> = 0;
                        function nextImg(pid) {
                            if (window['idx'+pid] < window['imgs'+pid].length-1) window['idx'+pid]++;
                            else window['idx'+pid]=0;
                            document.getElementById('main-img-'+pid).src = 'assets/images/' + window['imgs'+pid][window['idx'+pid]];
                        }
                        function prevImg(pid) {
                            if (window['idx'+pid] > 0) window['idx'+pid]--;
                            else window['idx'+pid]=window['imgs'+pid].length-1;
                            document.getElementById('main-img-'+pid).src = 'assets/images/' + window['imgs'+pid][window['idx'+pid]];
                        }
                        window['imgs'+<?= $prod['id'] ?>] = imgs<?= $prod['id'] ?>;
                        window['idx'+<?= $prod['id'] ?>] = 0;
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <strong><?= htmlspecialchars($prod['nom']) ?></strong><br>
            <div class="product-rating">★★★★☆</div>
            <span><?= number_format($prod['prix'], 2) ?> €</span><br>
            <span><?= htmlspecialchars($prod['categorie']) ?></span><br>
            <div class="product-desc">
                <?= htmlspecialchars(mb_strimwidth($prod['description'], 0, 80, '...')) ?>
            </div>
            <a href="cart.php?add=<?= $prod['id'] ?>" class="add-to-cart-btn">Ajouter au panier</a>
            <a href="product.php?id=<?= $prod['id'] ?>" class="product-details-btn">Voir le produit</a>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?> -->
