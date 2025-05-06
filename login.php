<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if ($email && $password) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
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
<h2>Connexion</h2>
<?php if (!empty($message)) : ?>
    <div style="color: red; margin-bottom: 10px;"> <?= $message ?> </div>
<?php endif; ?>
<form method="post" action="login.php">
    <label>Email :</label><br>
    <input type="email" name="email" required><br>
    <label>Mot de passe :</label><br>
    <input type="password" name="password" required><br>
    <button type="submit">Se connecter</button>
</form>
<p>Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
<?php include 'includes/footer.php'; ?> 