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
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ?');
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $message = "Email ou nom d'utilisateur déjà utilisé.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
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
<?php if (!empty($message)) : ?>
    <div style="color: red; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<h2>Inscription</h2>
<form method="post" action="register.php">
    <label>Nom d'utilisateur :</label><br>
    <input type="text" name="username" required><br>
    <label>Email :</label><br>
    <input type="email" name="email" required><br>
    <label>Mot de passe :</label><br>
    <input type="password" name="password" required><br>
    <button type="submit">S'inscrire</button>
</form>
<p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
<?php include 'includes/footer.php'; ?> 