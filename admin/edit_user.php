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

include '../includes/header.php';
?>
<h2>Modifier un client</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<form method="post" action="edit_user.php?id=<?= $id ?>">
    <input type="hidden" name="update_user" value="1">
    <label>Nom d'utilisateur :</label><br>
    <input type="text" name="username" value="<?= htmlspecialchars($user['nom']) ?>" required><br>
    <label>Email :</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
    <button type="submit">Enregistrer</button>
    <a href="users.php">Retour à la liste</a>
</form>
<?php include '../includes/footer.php'; ?> 