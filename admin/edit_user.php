<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_logged_in() || !is_admin()) {
    redirect('../login.php');
}

if (!isset($_GET['id'])) {
    redirect('users.php');
}

$id = intval($_GET['id']);
$message = '';

// Récupérer les infos du client
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ? AND role = "client"');
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    redirect('users.php');
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    if ($username && $email) {
        $stmt = $pdo->prepare('UPDATE utilisateurs SET nom=?, email=? WHERE id=?');
        if ($stmt->execute([$username, $email, $id])) {
            $message = "Client modifié avec succès.";
            // Recharger les données
            $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ? AND role = "client"');
            $stmt->execute([$id]);
            $user = $stmt->fetch();
        } else {
            $message = "Erreur lors de la modification.";
        }
    } else {
        $message = "Le nom d'utilisateur et l'email sont obligatoires.";
    }
}

include '../includes/admin_header.php';
?>
<div class="admin-container">
    <h2>Modifier un client</h2>
    <?php if (!empty($message)) : ?>
        <div class="admin-message <?= strpos($message, 'succès') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    <form method="post" action="edit_user.php?id=<?= $id ?>" class="admin-form-block admin-form">
        <input type="hidden" name="update_user" value="1">
        <label>Nom d'utilisateur :</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['nom']) ?>" required>
        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <br>
        <br>
        <button type="submit" class="button-admin">Enregistrer</button>
        <a href="users.php" class="button-admin button-admin-small button-admin-back">Retour à la liste</a>
    </form>
</div>
</body>
</html> 