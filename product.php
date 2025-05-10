<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='error-message'>Produit non trouvé.</p>";
    include 'includes/footer.php';
    exit;
}

$productId = intval($_GET['id']);

// Fetch product details
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    echo "<p class='error-message'>Produit non trouvé.</p>";
    include 'includes/footer.php';
    exit;
}

// Fetch product images
$stmt_imgs = $pdo->prepare('SELECT image FROM images_produit WHERE produit_id = ?');
$stmt_imgs->execute([$productId]);
$images = $stmt_imgs->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="product-detail-page">
    <div class="product-detail-container">
        <div class="product-gallery">
            <?php if ($images && count($images) > 0): ?>
                <!-- Previous Arrow -->
                <span onclick="prevImg(<?= $productId ?>)" class="gallery-arrow gallery-arrow-left">
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <polyline points="15 18 9 12 15 6" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                
                <!-- Main Image -->
                <img src="assets/images/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product['nom']) ?>" id="main-img-<?= $productId ?>" class="product-image-detail">

                <!-- Next Arrow -->
                <span onclick="nextImg(<?= $productId ?>)" class="gallery-arrow gallery-arrow-right">
                    <svg width="24" height="24" viewBox="0 0 24 24">
                        <polyline points="9 6 15 12 9 18" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>

                <!-- JavaScript Logic for Image Navigation -->
                <?php if (count($images) > 1): ?>
                    <script>
                    var imgs<?= $productId ?> = <?= json_encode($images) ?>;
                    var idx<?= $productId ?> = 0;

                    function nextImg(pid) {
                        if (window['idx' + pid] < window['imgs' + pid].length - 1) {
                            window['idx' + pid]++;
                        } else {
                            window['idx' + pid] = 0;
                        }
                        document.getElementById('main-img-' + pid).src = 'assets/images/' + window['imgs' + pid][window['idx' + pid]];
                    }

                    function prevImg(pid) {
                        if (window['idx' + pid] > 0) {
                            window['idx' + pid]--;
                        } else {
                            window['idx' + pid] = window['imgs' + pid].length - 1;
                        }
                        document.getElementById('main-img-' + pid).src = 'assets/images/' + window['imgs' + pid][window['idx' + pid]];
                    }

                    window['imgs' + <?= $productId ?>] = imgs<?= $productId ?>;
                    window['idx' + <?= $productId ?>] = 0;
                    </script>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <h2 class="product-title"><?= htmlspecialchars($product['nom']) ?></h2>
            <div class="product-price"><?= number_format($product['prix'], 2) ?> €</div>
            <div class="product-category">Catégorie : <?= htmlspecialchars($product['categorie']) ?></div>
            <div class="product-description"><?= htmlspecialchars($product['description']) ?></div>

            <form action="cart.php" method="GET" class="product-actions">
                <input type="hidden" name="add" value="<?= $product['id'] ?>">
                <div class="quantity-selector">
                    <label for="quantity">Quantité :</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="99" class="quantity-input">
                </div>
                <button type="submit" class="add-to-cart-btn">Ajouter au panier</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>


