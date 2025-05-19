<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    // Si l'utilisateur n'existe pas (supprimé ?), déconnecter
    session_destroy();
    redirect('login.php');
}

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'] ?? '';
    $update_fields = [];
    $update_params = [];
    if ($username && $email) {
        $update_fields[] = 'nom = ?';
        $update_params[] = $username;
        $update_fields[] = 'email = ?';
        $update_params[] = $email;
        if (!empty($new_password)) {
            $update_fields[] = 'mot_de_passe = ?';
            $update_params[] = password_hash($new_password, PASSWORD_DEFAULT);
        }
        $update_params[] = $user_id;
        $sql = 'UPDATE utilisateurs SET ' . implode(', ', $update_fields) . ' WHERE id = ?';
        if ($pdo->prepare($sql)->execute($update_params)) {
            $message = "Profil mis à jour.";
            $_SESSION['username'] = $username;
            // Recharger les données
            $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
        } else {
            $message = "Erreur lors de la mise à jour.";
        }
    } else {
        $message = "Nom d'utilisateur et email obligatoires.";
    }
}

include 'includes/header.php';
?>
<div class="profile-container">
    <h2 class="profile-title">Mon Profil</h2>
    <?php if (!empty($message)) : ?>
        <div class="profile-message"><?= $message ?></div>
    <?php endif; ?>
    <form method="post" action="profile.php" class="profile-form">
        <label>Nom d'utilisateur :</label>
        <input type="text" name="username" value="<?= isset($user['nom']) ? htmlspecialchars($user['nom']) : '' ?>" required>
        <label>Email :</label>
        <input type="email" name="email" value="<?= isset($user['email']) ? htmlspecialchars($user['email']) : '' ?>" required>
        <label>Nouveau mot de passe :</label>
        <input type="password" name="new_password" placeholder="Laisser vide pour ne pas changer">
        <button type="submit">Mettre à jour</button>
    </form>
    <a href="orders.php" class="profile-link">Voir mes commandes</a>
</div>
<?php include 'includes/footer.php'; ?> 