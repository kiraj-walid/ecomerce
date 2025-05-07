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
    <h1 class="product-title"><?= htmlspecialchars($product['nom']) ?></h1>

    <div class="product-detail-container">
        <div class="product-images">
            <div class="image-gallery">
                <?php if ($images && count($images) > 0): ?>
                    <?php foreach ($images as $index => $img): ?>
                        <div class="product-image-wrapper" data-index="<?= $index ?>">
                            <img src="assets/images/<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['nom']) ?>" class="product-image-detail">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="product-image-wrapper">
                        <img src="assets/images/default.png" alt="Image par défaut" class="product-image-detail">
                    </div>
                <?php endif; ?>
            </div>
            <div class="gallery-controls">
                <button class="gallery-btn prev-btn">&#10094;</button>
                <button class="gallery-btn next-btn">&#10095;</button>
            </div>
        </div>

        <div class="product-info">
            <h2><?= htmlspecialchars($product['nom']) ?></h2>
            <p class="product-price">Prix : <?= number_format($product['prix'], 2) ?> €</p>
            <p class="product-category">Catégorie : <?= htmlspecialchars($product['categorie']) ?></p>
            <p class="product-description">Description : <?= htmlspecialchars($product['description']) ?></p>

            <form action="cart.php" method="GET" class="add-to-cart-form">
                <input type="hidden" name="add" value="<?= $product['id'] ?>">
                <label for="quantity">Quantité :</label>
                <input type="number" name="quantity" value="1" min="1" max="99" class="quantity-input">
                <button type="submit" class="add-to-cart-btn">Ajouter au panier</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const images = document.querySelectorAll(".product-image-wrapper");
        const prevBtn = document.querySelector(".prev-btn");
        const nextBtn = document.querySelector(".next-btn");
        let currentIndex = 0;

        function updateGallery() {
            images.forEach((img, index) => {
                img.style.display = index === currentIndex ? "block" : "none";
            });
        }

        prevBtn.addEventListener("click", () => {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateGallery();
        });

        nextBtn.addEventListener("click", () => {
            currentIndex = (currentIndex + 1) % images.length;
            updateGallery();
        });

        updateGallery();
    });
</script>

<?php include 'includes/footer.php'; ?>