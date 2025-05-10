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
        <div class="product-images">
            <?php if ($images && count($images) > 0): ?>
                <div class="product-image-gallery" style="display: flex; align-items: center; justify-content: center; gap: 12px; position: relative;">
                    <span onclick="prevImg(<?= $productId ?>)" class="gallery-arrow gallery-arrow-left">
                        <svg width="32" height="32" viewBox="0 0 24 24">
                            <polyline points="15 18 9 12 15 6" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <img src="assets/images/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product['nom']) ?>" id="main-img-<?= $productId ?>" class="product-image-detail">
                    <span onclick="nextImg(<?= $productId ?>)" class="gallery-arrow gallery-arrow-right">
                        <svg width="32" height="32" viewBox="0 0 24 24">
                            <polyline points="9 6 15 12 9 18" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="product-thumbnails" style="display: flex; gap: 8px; margin-top: 10px; justify-content: center;">
                        <?php foreach ($images as $idx => $img): ?>
                            <img src="assets/images/<?= htmlspecialchars($img) ?>" alt="thumb" class="product-thumb" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 2px solid #eee; cursor: pointer; transition: border 0.2s;" onclick="document.getElementById('main-img-<?= $productId ?>').src='assets/images/<?= htmlspecialchars($img) ?>'; window['idx<?= $productId ?>']=<?= $idx ?>;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="product-info">
            <h2 class="product-title"><?= htmlspecialchars($product['nom']) ?></h2>
            <div class="product-meta">
                <span class="product-price">Prix : <?= number_format($product['prix'], 2) ?> €</span>
                <span class="product-category">Catégorie : <?= htmlspecialchars($product['categorie']) ?></span>
            </div>
            <div class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
            <form action="cart.php" method="GET" class="add-to-cart-form">
                <input type="hidden" name="add" value="<?= $product['id'] ?>">
                <label for="quantity">Quantité :</label>
                <input type="number" name="quantity" value="1" min="1" max="99" class="quantity-input">
                <button type="submit" class="add-to-cart-btn">Ajouter au panier</button>
            </form>
        </div>
    </div>
</div>

<?php if ($images && count($images) > 1): ?>
<script>
    var imgs<?= $productId ?> = <?= json_encode($images) ?>;
    var idx<?= $productId ?> = 0;
    function nextImg(pid) {
        if (window['idx'+pid] < window['imgs'+pid].length - 1) {
            window['idx'+pid]++;
        } else {
            window['idx'+pid] = 0;
        }
        document.getElementById('main-img-'+pid).src = 'assets/images/' + window['imgs'+pid][window['idx'+pid]];
    }
    function prevImg(pid) {
        if (window['idx'+pid] > 0) {
            window['idx'+pid]--;
        } else {
            window['idx'+pid] = window['imgs'+pid].length - 1;
        }
        document.getElementById('main-img-'+pid).src = 'assets/images/' + window['imgs'+pid][window['idx'+pid]];
    }
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>


