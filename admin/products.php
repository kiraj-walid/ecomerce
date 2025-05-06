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
    $stmt = $pdo->prepare('DELETE FROM produits WHERE id = ?');
    if ($stmt->execute([$id])) {
        $message = "Produit supprimé avec succès.";
    } else {
        $message = "Erreur lors de la suppression du produit.";
    }
}

// Traitement de l'ajout de produit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $nom = trim($_POST['name']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['price']);
    $categorie = trim($_POST['category']);
    $image_list = trim($_POST['images']); // champ images (fichiers séparés par des virgules)

    if ($nom && $prix) {
        $stmt = $pdo->prepare('INSERT INTO produits (nom, description, prix, categorie) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$nom, $description, $prix, $categorie])) {
            $produit_id = $pdo->lastInsertId();
            // Insertion des images
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

// Récupérer la liste des produits
$products = $pdo->query('SELECT * FROM produits ORDER BY id DESC')->fetchAll();

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
    <label>Images (fichiers séparés par des virgules) :</label><br>
    <input type="text" name="images" placeholder="ex: img1.jpg, img2.png"><br>
    <button type="submit">Ajouter</button>
</form>

<h3>Liste des produits</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prix</th>
        <th>Catégorie</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($products as $prod) : ?>
    <tr>
        <td><?= $prod['id'] ?></td>
        <td><?= htmlspecialchars($prod['nom']) ?></td>
        <td><?= number_format($prod['prix'], 2) ?> €</td>
        <td><?= htmlspecialchars($prod['categorie']) ?></td>
        <td>
            <a href="edit_product.php?id=<?= $prod['id'] ?>">Modifier</a> |
            <a href="products.php?delete=<?= $prod['id'] ?>" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include '../includes/footer.php'; ?> 