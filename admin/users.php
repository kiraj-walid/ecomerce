<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}

// Suppression d'un client
$message = '';
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ? AND role = "client"');
    if ($stmt->execute([$id])) {
        $message = "Client supprimé avec succès.";
    } else {
        $message = "Erreur lors de la suppression du client.";
    }
}

// Récupérer la liste des clients
$clients = $pdo->query("SELECT * FROM utilisateurs WHERE role = 'client' ORDER BY id DESC")->fetchAll();

include '../includes/admin_header.php';

// Préparation du filtre de recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filtered_clients = array_filter($clients, function($client) use ($search) {
    return $search === '' || stripos($client['nom'], $search) !== false || stripos($client['email'], $search) !== false;
});
?>
<div class="admin-container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <h2 style="margin-bottom:0;">Liste des clients</h2>
    </div>
    <form method="get" action="users.php" class="admin-search-form">
        <input type="text" name="search" placeholder="Nom ou email..." value="<?= htmlspecialchars($search) ?>" class="admin-search-input">
        <button type="submit" class="button-admin button-admin-small">Rechercher</button>
    </form>
    <div class="admin-table-responsive">
        <table class="admin-table">
            <tr>
                <!-- <th>ID</th> -->
                <th>Nom d'utilisateur</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($filtered_clients as $client) : ?>
            <tr>
                <!-- <td><?= $client['id'] ?></td> -->
                <td><?= htmlspecialchars($client['nom']) ?></td>
                <td><?= htmlspecialchars($client['email']) ?></td>
                <td>
                    <div class="admin-actions">
                        <a href="edit_user.php?id=<?= $client['id'] ?>" class="button-admin button-admin-small">Modifier</a>
                        <a href="users.php?delete=<?= $client['id'] ?>" class="button-admin button-admin-small button-admin-danger" onclick="return confirm('Supprimer ce client ?');">Supprimer</a>
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