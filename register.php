<?php

try {
    $conn = new PDO('mysql:host=localhost;dbname=gtb_website;charset=utf8mb4', 'root', 'root');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username         = trim($_POST['username']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif ($password !== $confirm_password) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 8) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    }

    if (empty($erreur)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $query = $conn->prepare("INSERT INTO users (username, email, passwrd) VALUES (:username, :email, :passwrd)");
            $query->execute([
                ':username' => $username,
                ':email'    => $email,
                ':passwrd'  => $hash
            ]);

            header('Location: login.php?inscription=ok');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $erreur = "Cette adresse email est déjà utilisée.";
            } else {
                $erreur = "Une erreur est survenue, veuillez réessayer.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Créer un compte</title>
</head>

<body>

    <div class="login-card">

        <p>Remplissez les informations ci-dessous</p>
        <h1>Créer un compte</h1>

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form action="" method="post" class="form-login">

            <div class="email-part">
                <input type="text" name="username" placeholder="Nom d'utilisateur"
                    value="<?= htmlspecialchars($username ?? '') ?>" required>
            </div>

            <div class="email-part">
                <input type="email" name="email" placeholder="Email"
                    value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>

            <div class="password-part">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>

            <div class="password-part">
                <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
            </div>

            <div class="submit-part">
                <input type="submit" value="Créer le compte">
            </div>

        </form>

        <p class="register-link">Déjà un compte ? <a href="login.php">Se connecter</a></p>

    </div>

</body>

</html>