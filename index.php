<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Récupérer les 8 derniers produits
$products = $pdo->query('SELECT * FROM produits ORDER BY id DESC LIMIT 8')->fetchAll();
?>
<h1>Bienvenue sur la Boutique en Ligne</h1>
<p>
    <a href="search.php">Rechercher un produit</a> |
    <a href="cart.php">Voir mon panier</a> |
    <a href="profile.php">Mon profil</a>
</p>
<h2>Produits récents</h2>
<?php if (count($products) === 0) : ?>
    <p>Aucun produit disponible.</p>
<?php else : ?>
    <div style="display:flex; flex-wrap:wrap; gap:20px;">
    <?php foreach ($products as $prod) : ?>
        <div style="border:1px solid #ccc; padding:10px; width:220px; background:#fff;">
            <?php
            $stmt_imgs = $pdo->prepare('SELECT image FROM images_produit WHERE produit_id = ?');
            $stmt_imgs->execute([$prod['id']]);
            $images = $stmt_imgs->fetchAll(PDO::FETCH_COLUMN);
            ?>
            <div class="product-gallery" style="text-align:center; position:relative;">
                <?php if ($images && count($images) > 0): ?>
                    <span onclick="prevImg(<?= $prod['id'] ?>)" style="cursor:pointer; position:absolute; left:0; top:50%; transform:translateY(-50%); font-size:22px; color:#333; padding:2px 6px; z-index:2;">
                        <svg width="24" height="24" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <img src="assets/images/<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($prod['nom']) ?>" style="max-width:200px; max-height:120px; margin-bottom:4px;" id="main-img-<?= $prod['id'] ?>">
                    <span onclick="nextImg(<?= $prod['id'] ?>)" style="cursor:pointer; position:absolute; right:0; top:50%; transform:translateY(-50%); font-size:22px; color:#333; padding:2px 6px; z-index:2;">
                        <svg width="24" height="24" viewBox="0 0 24 24"><polyline points="9 6 15 12 9 18" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <?php if (count($images) > 1): ?>
                        <script>
                        var imgs<?= $prod['id'] ?> = <?= json_encode($images) ?>;
                        var idx<?= $prod['id'] ?> = 0;
                        function nextImg(pid) {
                            if (window['idx'+pid] < window['imgs'+pid].length-1) window['idx'+pid]++;
                            else window['idx'+pid]=0;
                            document.getElementById('main-img-'+pid).src = 'assets/images/' + window['imgs'+pid][window['idx'+pid]];
                        }
                        function prevImg(pid) {
                            if (window['idx'+pid] > 0) window['idx'+pid]--;
                            else window['idx'+pid]=window['imgs'+pid].length-1;
                            document.getElementById('main-img-'+pid).src = 'assets/images/' + window['imgs'+pid][window['idx'+pid]];
                        }
                        window['imgs'+<?= $prod['id'] ?>] = imgs<?= $prod['id'] ?>;
                        window['idx'+<?= $prod['id'] ?>] = 0;
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <strong><?= htmlspecialchars($prod['nom']) ?></strong><br>
            <span><?= number_format($prod['prix'], 2) ?> €</span><br>
            <span><?= htmlspecialchars($prod['categorie']) ?></span><br>
            <a href="cart.php?add=<?= $prod['id'] ?>">Ajouter au panier</a>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?> 