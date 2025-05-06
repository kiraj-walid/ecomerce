<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Récupérer les filtres
$keyword = $_GET['keyword'] ?? '';
$categorie = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';

// Construction de la requête
$sql = "SELECT * FROM produits WHERE 1";
$params = [];
if ($keyword) {
    $sql .= " AND (nom LIKE ? OR description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
if ($categorie) {
    $sql .= " AND categorie = ?";
    $params[] = $categorie;
}
if ($sort === 'price_asc') {
    $sql .= " ORDER BY prix ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY prix DESC";
} else {
    $sql .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Récupérer les valeurs distinctes pour les filtres
$categories = $pdo->query('SELECT DISTINCT categorie FROM produits WHERE categorie IS NOT NULL AND categorie != ""')->fetchAll(PDO::FETCH_COLUMN);

include 'includes/header.php';
?>
<h2>Recherche de produits</h2>
<form method="get" action="search.php">
    <input type="text" name="keyword" placeholder="Mot-clé..." value="<?= htmlspecialchars($keyword) ?>">
    <select name="category">
        <option value="">Catégorie</option>
        <?php foreach ($categories as $cat) : ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $cat == $categorie ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="sort">
        <option value="">Trier par</option>
        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Prix croissant</option>
        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Prix décroissant</option>
    </select>
    <button type="submit">Rechercher</button>
</form>

<?php if (count($products) === 0) : ?>
    <p>Aucun produit trouvé.</p>
<?php else : ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Nom</th>
            <th>Prix</th>
            <th>Catégorie</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $prod) : ?>
        <tr>
            <td><?= htmlspecialchars($prod['nom']) ?></td>
            <td><?= number_format($prod['prix'], 2) ?> €</td>
            <td><?= htmlspecialchars($prod['categorie']) ?></td>
            <td><a href="cart.php?add=<?= $prod['id'] ?>">Ajouter au panier</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<?php include 'includes/footer.php'; ?> 