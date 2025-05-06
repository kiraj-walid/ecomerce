<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Récupérer les filtres
$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';
$brand = $_GET['brand'] ?? '';
$color = $_GET['color'] ?? '';
$age = $_GET['age'] ?? '';
$sort = $_GET['sort'] ?? '';

// Construction de la requête
$sql = "SELECT * FROM products WHERE 1";
$params = [];
if ($keyword) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if ($brand) {
    $sql .= " AND brand = ?";
    $params[] = $brand;
}
if ($color) {
    $sql .= " AND color = ?";
    $params[] = $color;
}
if ($age) {
    $sql .= " AND age_recommended = ?";
    $params[] = $age;
}
if ($sort === 'price_asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY price DESC";
} else {
    $sql .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Récupérer les valeurs distinctes pour les filtres
$categories = $pdo->query('SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ""')->fetchAll(PDO::FETCH_COLUMN);
$brands = $pdo->query('SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != ""')->fetchAll(PDO::FETCH_COLUMN);
$colors = $pdo->query('SELECT DISTINCT color FROM products WHERE color IS NOT NULL AND color != ""')->fetchAll(PDO::FETCH_COLUMN);
$ages = $pdo->query('SELECT DISTINCT age_recommended FROM products WHERE age_recommended IS NOT NULL AND age_recommended != ""')->fetchAll(PDO::FETCH_COLUMN);

include 'includes/header.php';
?>
<h2>Recherche de produits</h2>
<form method="get" action="search.php">
    <input type="text" name="keyword" placeholder="Mot-clé..." value="<?= htmlspecialchars($keyword) ?>">
    <select name="category">
        <option value="">Catégorie</option>
        <?php foreach ($categories as $cat) : ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $cat == $category ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="brand">
        <option value="">Marque</option>
        <?php foreach ($brands as $b) : ?>
            <option value="<?= htmlspecialchars($b) ?>" <?= $b == $brand ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="color">
        <option value="">Couleur</option>
        <?php foreach ($colors as $c) : ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= $c == $color ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="age">
        <option value="">Âge recommandé</option>
        <?php foreach ($ages as $a) : ?>
            <option value="<?= htmlspecialchars($a) ?>" <?= $a == $age ? 'selected' : '' ?>><?= htmlspecialchars($a) ?></option>
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
            <th>Marque</th>
            <th>Couleur</th>
            <th>Âge recommandé</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $prod) : ?>
        <tr>
            <td><?= htmlspecialchars($prod['name']) ?></td>
            <td><?= number_format($prod['price'], 2) ?> €</td>
            <td><?= htmlspecialchars($prod['category']) ?></td>
            <td><?= htmlspecialchars($prod['brand']) ?></td>
            <td><?= htmlspecialchars($prod['color']) ?></td>
            <td><?= htmlspecialchars($prod['age_recommended']) ?></td>
            <td><a href="cart.php?add=<?= $prod['id'] ?>">Ajouter au panier</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<?php include 'includes/footer.php'; ?> 