<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if ($email && $password) {
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['nom'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] === 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}

include 'includes/header.php';
?>
<div class="auth-container">
    <div class="auth-title">Connexion</div>
    <?php if (!empty($message)) : ?>
        <div class="auth-message <?= strpos($message, 'incorrect') === false ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>
    <form method="post" action="login.php" class="auth-form">
        <label>Email :</label>
        <input type="email" name="email" required>
        <label>Mot de passe :</label>
        <input type="password" name="password" required>
        <button type="submit">Se connecter</button>
    </form>
    <a href="register.php" class="auth-link">Pas encore inscrit ? Créer un compte</a>
</div>
<?php include 'includes/footer.php'; ?> 