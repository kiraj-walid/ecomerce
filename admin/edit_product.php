<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}

if (!isset($_GET['id'])) {
    redirect('products.php');
}

$id = intval($_GET['id']);
$message = '';

// Récupérer les infos du produit
$stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    redirect('products.php');
}

// Récupérer les images du produit
$stmt_imgs = $pdo->prepare('SELECT image FROM images_produit WHERE produit_id = ?');
$stmt_imgs->execute([$id]);
$images = $stmt_imgs->fetchAll(PDO::FETCH_COLUMN);
$image_list = implode(', ', $images);

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $nom = trim($_POST['name']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['price']);
    $categorie = trim($_POST['category']);
    $image_list = trim($_POST['images']);
    $stock = intval($_POST['stock']);

    if ($nom && $prix) {
        $stmt = $pdo->prepare('UPDATE produits SET nom=?, description=?, prix=?, categorie=?, stock=? WHERE id=?');
        if ($stmt->execute([$nom, $description, $prix, $categorie, $stock, $id])) {
            // Mettre à jour les images
            $pdo->prepare('DELETE FROM images_produit WHERE produit_id = ?')->execute([$id]);
            if (!empty($image_list)) {
                $images = array_map('trim', explode(',', $image_list));
                foreach ($images as $img) {
                    if ($img !== '') {
                        $stmt_img = $pdo->prepare('INSERT INTO images_produit (produit_id, image) VALUES (?, ?)');
                        $stmt_img->execute([$id, $img]);
                    }
                }
            }
            $message = "Produit modifié avec succès.";
            // Recharger les données
            $stmt = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
            $stmt->execute([$id]);
            $product = $stmt->fetch();
        } else {
            $message = "Erreur lors de la modification.";
        }
    } else {
        $message = "Le nom et le prix sont obligatoires.";
    }
}

include '../includes/admin_header.php';
?>
<div class="admin-container">
    <h2>Modifier un produit</h2>
    <?php if (!empty($message)) : ?>
        <div class="admin-message <?= strpos($message, 'succès') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    <form method="post" action="edit_product.php?id=<?= $id ?>" class="admin-form-block admin-form">
        <input type="hidden" name="update_product" value="1">
        <label>Nom :</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['nom']) ?>" required>
        <label>Description :</label>
        <textarea name="description" rows="2"><?= htmlspecialchars($product['description']) ?></textarea>
        <label>Prix :</label>
        <input type="number" step="0.01" name="price" value="<?= $product['prix'] ?>" required>
        <label>Stock :</label>
        <input type="number" name="stock" value="<?= $product['stock'] ?>" min="0" required>
        <label>Catégorie :</label>
        <input type="text" name="category" value="<?= htmlspecialchars($product['categorie']) ?>">
        <label>Images (fichiers séparés par des virgules) :</label>
        <input type="text" name="images" value="<?= htmlspecialchars($image_list) ?>" placeholder="ex: img1.jpg, img2.png">
        <button type="submit" class="button-admin">Enregistrer</button>
        <a href="products.php" class="button-admin button-admin-small button-admin-back">Retour à la liste</a>
    </form>
</div>
</body>
</html> 