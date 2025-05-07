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
                    <div class="gallery-wrapper">
                        <!-- Navigation Arrows -->
                        <div class="gallery-nav">
                            <a href="javascript:void(0);" class="prev">&#10094;</a>
                            <a href="javascript:void(0);" class="next">&#10095;</a>
                        </div>

                        <?php foreach ($images as $index => $img): ?>
                            <input type="radio" name="gallery" id="img-<?= $index ?>" <?= $index === 0 ? 'checked' : '' ?> class="gallery-radio">
                            <div class="gallery-slide">
                                <label for="img-<?= $index ?>" class="gallery-thumbnail">
                                    <img src="assets/images/<?= htmlspecialchars($img) ?>" alt="Image <?= $index + 1 ?>" class="gallery-thumb-img">
                                </label>
                                <div class="gallery-main-image">
                                    <img src="assets/images/<?= htmlspecialchars($img) ?>" alt="Image <?= $index + 1 ?>" class="product-image-detail">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="product-image-wrapper">
                        <img src="assets/images/default.png" alt="Image par défaut" class="product-image-detail">
                    </div>
                <?php endif; ?>
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


<?php include 'includes/footer.php'; ?>
document.addEventListener('DOMContentLoaded', function () {
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
    const radioButtons = document.querySelectorAll('.gallery-radio');
    let currentIndex = 0;

    // Function to update the checked radio button based on index
    function updateImageGallery(index) {
        radioButtons[index].checked = true;
    }

    // Next button functionality
    nextButton.addEventListener('click', function () {
        currentIndex = (currentIndex + 1) % radioButtons.length;  // Cycle to next image
        updateImageGallery(currentIndex);
    });

    // Prev button functionality
    prevButton.addEventListener('click', function () {
        currentIndex = (currentIndex - 1 + radioButtons.length) % radioButtons.length;  // Cycle to previous image
        updateImageGallery(currentIndex);
    });
});
