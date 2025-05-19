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
<form method="get" action="search.php" class="search-form">
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
<div class="product-grid">
    <?php if (count($products) === 0) : ?>
        <p>Aucun produit trouvé.</p>
    <?php else : ?>
        <?php foreach ($products as $prod) : ?>
            <div class="product-card">
                <div class="product-image">
                    <?php
                    $stmt_imgs = $pdo->prepare('SELECT image FROM images_produit WHERE produit_id = ?');
                    $stmt_imgs->execute([$prod['id']]);
                    $images = $stmt_imgs->fetchAll(PDO::FETCH_COLUMN);
                    ?>
                    <div class="product-gallery" style="text-align:center; position:relative;">
                        <?php if ($images && count($images) > 0): ?>
                            <span onclick="prevImgSearch(<?= $prod['id'] ?>)" style="cursor:pointer; position:absolute; left:0; top:50%; transform:translateY(-50%); font-size:18px; color:#333; padding:2px 6px; z-index:2;">
                                <svg width="20" height="20" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <img src="assets/images/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" style="max-width:80px; max-height:60px; margin-bottom:2px;" id="main-img-<?= $prod['id'] ?>-search">
                            <span onclick="nextImgSearch(<?= $prod['id'] ?>)" style="cursor:pointer; position:absolute; right:0; top:50%; transform:translateY(-50%); font-size:18px; color:#333; padding:2px 6px; z-index:2;">
                                <svg width="20" height="20" viewBox="0 0 24 24"><polyline points="9 6 15 12 9 18" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <h3><?= htmlspecialchars($prod['nom']) ?></h3>
                <p class="product-price"><?= number_format($prod['prix'], 2) ?> MAD</p>
                <p class="product-category"><?= htmlspecialchars($prod['categorie']) ?></p>
                <a href="cart.php?add=<?= $prod['id'] ?>" class="add-to-cart-btn">Ajouter au panier</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- <?php if (count($products) === 0) : ?>
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
            <td>
                <?php
                $stmt_imgs = $pdo->prepare('SELECT image FROM images_produit WHERE produit_id = ?');
                $stmt_imgs->execute([$prod['id']]);
                $images = $stmt_imgs->fetchAll(PDO::FETCH_COLUMN);
                ?>
                <div class="product-gallery" style="text-align:center; position:relative;">
                    <?php if ($images && count($images) > 0): ?>
                        <span onclick="prevImgSearch(<?= $prod['id'] ?>)" style="cursor:pointer; position:absolute; left:0; top:50%; transform:translateY(-50%); font-size:18px; color:#333; padding:2px 6px; z-index:2;">
                            <svg width="20" height="20" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <img src="assets/images/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" style="max-width:80px; max-height:60px; margin-bottom:2px;" id="main-img-<?= $prod['id'] ?>-search">
                        <span onclick="nextImgSearch(<?= $prod['id'] ?>)" style="cursor:pointer; position:absolute; right:0; top:50%; transform:translateY(-50%); font-size:18px; color:#333; padding:2px 6px; z-index:2;">
                            <svg width="20" height="20" viewBox="0 0 24 24"><polyline points="9 6 15 12 9 18" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <?php if (count($images) > 1): ?>
                            <script>
                            var imgsS<?= $prod['id'] ?> = <?= json_encode($images) ?>;
                            var idxS<?= $prod['id'] ?> = 0;
                            function nextImgSearch(pid) {
                                if (window['idxS'+pid] < window['imgsS'+pid].length-1) window['idxS'+pid]++;
                                else window['idxS'+pid]=0;
                                document.getElementById('main-img-'+pid+'-search').src = 'assets/images/' + window['imgsS'+pid][window['idxS'+pid]];
                            }
                            function prevImgSearch(pid) {
                                if (window['idxS'+pid] > 0) window['idxS'+pid]--;
                                else window['idxS'+pid]=window['imgsS'+pid].length-1;
                                document.getElementById('main-img-'+pid+'-search').src = 'assets/images/' + window['imgsS'+pid][window['idxS'+pid]];
                            }
                            window['imgsS'+<?= $prod['id'] ?>] = imgsS<?= $prod['id'] ?>;
                            window['idxS'+<?= $prod['id'] ?>] = 0;
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <br><?= htmlspecialchars($prod['nom']) ?>
            </td>
            <td><?= number_format($prod['prix'], 2) ?> €</td>
            <td><?= htmlspecialchars($prod['categorie']) ?></td>
            <td><a href="cart.php?add=<?= $prod['id'] ?>">Ajouter au panier</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?> -->
<?php include 'includes/footer.php'; ?> 