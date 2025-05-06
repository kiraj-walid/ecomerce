<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';

// Récupérer les infos de l'utilisateur
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'] ?? '';
    $update_fields = [];
    $update_params = [];
    if ($username && $email) {
        $update_fields[] = 'username = ?';
        $update_params[] = $username;
        $update_fields[] = 'email = ?';
        $update_params[] = $email;
        if (!empty($new_password)) {
            $update_fields[] = 'password = ?';
            $update_params[] = password_hash($new_password, PASSWORD_DEFAULT);
        }
        $update_params[] = $user_id;
        $sql = 'UPDATE users SET ' . implode(', ', $update_fields) . ' WHERE id = ?';
        if ($pdo->prepare($sql)->execute($update_params)) {
            $message = "Profil mis à jour.";
            $_SESSION['username'] = $username;
            // Recharger les données
            $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
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
<h2>Mon Profil</h2>
<?php if (!empty($message)) : ?>
    <div style="color: green; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<form method="post" action="profile.php">
    <label>Nom d'utilisateur :</label><br>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br>
    <label>Email :</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
    <label>Nouveau mot de passe :</label><br>
    <input type="password" name="new_password" placeholder="Laisser vide pour ne pas changer"><br>
    <button type="submit">Mettre à jour</button>
</form>
<a href="orders.php">Voir mes commandes</a>
<?php include 'includes/footer.php'; ?> 