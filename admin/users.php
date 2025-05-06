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
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ? AND role = "client"');
    if ($stmt->execute([$id])) {
        $message = "Client supprimé avec succès.";
    } else {
        $message = "Erreur lors de la suppression du client.";
    }
}

// Récupérer la liste des clients
$clients = $pdo->query("SELECT * FROM users WHERE role = 'client' ORDER BY id DESC")->fetchAll();

include '../includes/header.php';
?>
<h2>Gestion des Clients</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nom d'utilisateur</th>
        <th>Email</th>
        <th>Date d'inscription</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($clients as $client) : ?>
    <tr>
        <td><?= $client['id'] ?></td>
        <td><?= htmlspecialchars($client['username']) ?></td>
        <td><?= htmlspecialchars($client['email']) ?></td>
        <td><?= $client['created_at'] ?></td>
        <td>
            <a href="edit_user.php?id=<?= $client['id'] ?>">Modifier</a> |
            <a href="users.php?delete=<?= $client['id'] ?>" onclick="return confirm('Supprimer ce client ?');">Supprimer</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include '../includes/footer.php'; ?> 