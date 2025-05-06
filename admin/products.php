<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}

// Suppression d'un produit
$message = '';
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    if ($stmt->execute([$id])) {
        $message = "Produit supprimé avec succès.";
    } else {
        $message = "Erreur lors de la suppression du produit.";
    }
}

// Traitement de l'ajout de produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $brand = trim($_POST['brand']);
    $color = trim($_POST['color']);
    $age_recommended = trim($_POST['age_recommended']);
    $image = trim($_POST['image']); // Pour simplifier, on met le nom du fichier image

    if ($name && $price) {
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price, category, brand, color, age_recommended, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        if ($stmt->execute([$name, $description, $price, $category, $brand, $color, $age_recommended, $image])) {
            $message = "Produit ajouté avec succès.";
        } else {
            $message = "Erreur lors de l'ajout du produit.";
        }
    } else {
        $message = "Le nom et le prix sont obligatoires.";
    }
}

// Récupérer la liste des produits
$products = $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();

include '../includes/header.php';
?>
<h2>Gestion des Produits</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>

<h3>Ajouter un produit</h3>
<form method="post" action="products.php">
    <input type="hidden" name="add_product" value="1">
    <label>Nom :</label><br>
    <input type="text" name="name" required><br>
    <label>Description :</label><br>
    <textarea name="description"></textarea><br>
    <label>Prix :</label><br>
    <input type="number" step="0.01" name="price" required><br>
    <label>Catégorie :</label><br>
    <input type="text" name="category"><br>
    <label>Marque :</label><br>
    <input type="text" name="brand"><br>
    <label>Couleur :</label><br>
    <input type="text" name="color"><br>
    <label>Âge recommandé :</label><br>
    <input type="text" name="age_recommended"><br>
    <label>Image (nom du fichier) :</label><br>
    <input type="text" name="image"><br>
    <button type="submit">Ajouter</button>
</form>

<h3>Liste des produits</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prix</th>
        <th>Catégorie</th>
        <th>Marque</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($products as $prod) : ?>
    <tr>
        <td><?= $prod['id'] ?></td>
        <td><?= htmlspecialchars($prod['name']) ?></td>
        <td><?= number_format($prod['price'], 2) ?> €</td>
        <td><?= htmlspecialchars($prod['category']) ?></td>
        <td><?= htmlspecialchars($prod['brand']) ?></td>
        <td>
            <a href="edit_product.php?id=<?= $prod['id'] ?>">Modifier</a> |
            <a href="products.php?delete=<?= $prod['id'] ?>" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include '../includes/footer.php'; ?> 