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

include '../includes/admin_header.php';

// Préparation des filtres de recherche
$search_name = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$search_cat = isset($_GET['search_cat']) ? trim($_GET['search_cat']) : '';

// Récupérer toutes les catégories distinctes
$categories = $pdo->query('SELECT DISTINCT categorie FROM produits WHERE categorie IS NOT NULL AND categorie != ""')->fetchAll(PDO::FETCH_COLUMN);

// Filtrage des produits
$filtered_products = array_filter($products, function($prod) use ($search_name, $search_cat) {
    $match_name = $search_name === '' || stripos($prod['nom'], $search_name) !== false;
    $match_cat = $search_cat === '' || $prod['categorie'] === $search_cat;
    return $match_name && $match_cat;
});
?>
<div class="admin-container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <h2 style="margin-bottom:0;">Liste des produits</h2>
        <a href="add_product.php" class="button-admin">Ajouter un produit</a>
    </div>
    <form method="get" action="products.php" style="margin:18px 0 32px 0;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
        <input type="text" name="search_name" placeholder="Nom du produit..." value="<?= htmlspecialchars($search_name) ?>" style="padding:8px 10px;border-radius:4px;border:1px solid #e1e4ea;">
        <select name="search_cat" style="padding:8px 10px;border-radius:4px;border:1px solid #e1e4ea;">
            <option value="">Toutes catégories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $search_cat === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="button-admin button-admin-small">Rechercher</button>
    </form>
    <div class="admin-table-responsive">
        <table class="admin-table">
            <tr>
                <!-- <th>ID</th> -->
                <th>Image</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Catégorie</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($filtered_products as $prod) :
                // Récupérer la première image du produit
                $stmt_img = $pdo->prepare('SELECT image FROM images_produit WHERE produit_id = ? LIMIT 1');
                $stmt_img->execute([$prod['id']]);
                $img = $stmt_img->fetchColumn();
                $img_url = $img ? '../assets/images/' . htmlspecialchars($img) : 'https://via.placeholder.com/56x56?text=Image';
            ?>
            <tr>
                <!-- <td><?= $prod['id'] ?></td> -->
                <td><img src="<?= $img_url ?>" alt="Image produit"></td>
                <td><?= htmlspecialchars($prod['nom']) ?></td>
                <td><?= number_format($prod['prix'], 2) ?> €</td>
                <td><?= $prod['stock'] ?></td>
                <td><?= htmlspecialchars($prod['categorie']) ?></td>
                <td>
                    <div class="admin-actions">
                        <a href="edit_product.php?id=<?= $prod['id'] ?>" class="button-admin button-admin-small">Modifier</a>
                        <a href="products.php?delete=<?= $prod['id'] ?>" class="button-admin button-admin-small button-admin-danger" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <a href="dashboard.php" class="button-admin" style="margin-top:32px;">Retour au tableau de bord</a>
</div>
</body>
</html> 