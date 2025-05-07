<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
include '../includes/admin_header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $nom = trim($_POST['name']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['price']);
    $categorie = trim($_POST['category']);
    $image_list = trim($_POST['images']);
    if ($nom && $prix) {
        $stmt = $pdo->prepare('INSERT INTO produits (nom, description, prix, categorie) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$nom, $description, $prix, $categorie])) {
            $produit_id = $pdo->lastInsertId();
            if (!empty($image_list)) {
                $images = array_map('trim', explode(',', $image_list));
                foreach ($images as $img) {
                    if ($img !== '') {
                        $stmt_img = $pdo->prepare('INSERT INTO images_produit (produit_id, image) VALUES (?, ?)');
                        $stmt_img->execute([$produit_id, $img]);
                    }
                }
            }
            $message = "Produit ajouté avec succès.";
        } else {
            $message = "Erreur lors de l'ajout du produit.";
        }
    } else {
        $message = "Le nom et le prix sont obligatoires.";
    }
}
?>
<div class="admin-container">
    <h2>Ajouter un produit</h2>
    <?php if (!empty($message)) : ?>
        <div class="admin-message" style="margin-bottom: 24px; <?= strpos($message, 'succès') !== false ? 'background:#e8f8f5;color:#148f77;' : 'background:#fdecea;color:#c0392b;' ?> padding: 14px 20px; border-radius: 6px; font-size:1.1em;">
            <?= $message ?>
        </div>
    <?php endif; ?>
    <form method="post" action="add_product.php" class="admin-form-block admin-form">
        <input type="hidden" name="add_product" value="1">
        <label>Nom :</label>
        <input type="text" name="name" required>
        <label>Description :</label>
        <textarea name="description" rows="2"></textarea>
        <label>Prix :</label>
        <input type="number" step="0.01" name="price" required>
        <label>Catégorie :</label>
        <input type="text" name="category">
        <label>Images (fichiers séparés par des virgules) :</label>
        <input type="text" name="images" placeholder="ex: img1.jpg, img2.png">
        <button type="submit" class="button-admin">Ajouter</button>
        <a href="products.php" class="button-admin button-admin-small" style="margin-left:12px;">Retour à la liste</a>
    </form>
</div>
</body>
</html> 