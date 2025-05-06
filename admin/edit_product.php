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

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $nom = trim($_POST['name']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['price']);
    $categorie = trim($_POST['category']);
    $image = trim($_POST['image']);

    if ($nom && $prix) {
        $stmt = $pdo->prepare('UPDATE produits SET nom=?, description=?, prix=?, categorie=?, image=? WHERE id=?');
        if ($stmt->execute([$nom, $description, $prix, $categorie, $image, $id])) {
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

include '../includes/header.php';
?>
<h2>Modifier un produit</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<form method="post" action="edit_product.php?id=<?= $id ?>">
    <input type="hidden" name="update_product" value="1">
    <label>Nom :</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($product['nom']) ?>" required><br>
    <label>Description :</label><br>
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br>
    <label>Prix :</label><br>
    <input type="number" step="0.01" name="price" value="<?= $product['prix'] ?>" required><br>
    <label>Catégorie :</label><br>
    <input type="text" name="category" value="<?= htmlspecialchars($product['categorie']) ?>"><br>
    <label>Image (nom du fichier) :</label><br>
    <input type="text" name="image" value="<?= isset($product['image']) ? htmlspecialchars($product['image']) : '' ?>"><br>
    <button type="submit">Enregistrer</button>
    <a href="products.php">Retour à la liste</a>
</form>
<?php include '../includes/footer.php'; ?> 