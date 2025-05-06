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
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    redirect('products.php');
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $brand = trim($_POST['brand']);
    $color = trim($_POST['color']);
    $age_recommended = trim($_POST['age_recommended']);
    $image = trim($_POST['image']);

    if ($name && $price) {
        $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, price=?, category=?, brand=?, color=?, age_recommended=?, image=? WHERE id=?');
        if ($stmt->execute([$name, $description, $price, $category, $brand, $color, $age_recommended, $image, $id])) {
            $message = "Produit modifié avec succès.";
            // Recharger les données
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
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
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br>
    <label>Description :</label><br>
    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br>
    <label>Prix :</label><br>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br>
    <label>Catégorie :</label><br>
    <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>"><br>
    <label>Marque :</label><br>
    <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>"><br>
    <label>Couleur :</label><br>
    <input type="text" name="color" value="<?= htmlspecialchars($product['color']) ?>"><br>
    <label>Âge recommandé :</label><br>
    <input type="text" name="age_recommended" value="<?= htmlspecialchars($product['age_recommended']) ?>"><br>
    <label>Image (nom du fichier) :</label><br>
    <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>"><br>
    <button type="submit">Enregistrer</button>
    <a href="products.php">Retour à la liste</a>
</form>
<?php include '../includes/footer.php'; ?> 