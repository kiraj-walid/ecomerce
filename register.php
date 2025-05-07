<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($username && $email && $password) {
        // Vérifier unicité
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? OR nom = ?');
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $message = "Email ou nom d'utilisateur déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)');
            if ($stmt->execute([$username, $email, $hash])) {
                $message = "Inscription réussie. <a href='login.php'>Connectez-vous</a>.";
            } else {
                $message = "Erreur lors de l'inscription.";
            }
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}

include 'includes/header.php';
?>
<div class="auth-container">
    <div class="auth-title">Inscription</div>
    <?php if (!empty($message)) : ?>
        <div class="auth-message <?= strpos($message, 'réussie') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    <form method="post" action="register.php" class="auth-form">
        <label>Nom d'utilisateur :</label>
        <input type="text" name="username" required>
        <label>Email :</label>
        <input type="email" name="email" required>
        <label>Mot de passe :</label>
        <input type="password" name="password" required>
        <button type="submit">S'inscrire</button>
    </form>
    <a href="login.php" class="auth-link">Déjà inscrit ? Se connecter</a>
</div>
<?php include 'includes/footer.php'; ?> 