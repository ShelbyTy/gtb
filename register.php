<?php

try {
    $conn = new PDO('mysql:host=localhost;dbname=gtb;charset=utf8mb4', 'root', 'root');
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

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
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
    <title>Créer un compte</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body class="auth-page">

    <div class="auth-card">

        <?php if (!empty($erreur)): ?>
            <div class="alert alert-error">
                <span class="icon">❌</span>
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <p class="auth-subtitle">Inscription</p>
        <h1 class="auth-title">Créer un compte</h1>
        <p class="auth-description">Remplissez les informations ci-dessous pour créer votre accès.</p>

        <form action="" method="post" class="form-login">

            <div class="form-field">
                <input
                    type="text"
                    name="username"
                    placeholder="Nom d'utilisateur"
                    value="<?= htmlspecialchars($username ?? '') ?>"
                    required>
            </div>

            <div class="form-field">
                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    value="<?= htmlspecialchars($email ?? '') ?>"
                    required>
            </div>

            <div class="form-field password-field">
                <input
                    type="password"
                    name="password"
                    class="password-input"
                    placeholder="Mot de passe"
                    required>

                <button
                    type="button"
                    class="password-toggle"
                    aria-label="Afficher le mot de passe"
                    title="Afficher le mot de passe">👁</button>
            </div>

            <div class="form-field password-field">
                <input
                    type="password"
                    name="confirm_password"
                    class="password-input"
                    placeholder="Confirmer le mot de passe"
                    required>

                <button
                    type="button"
                    class="password-toggle"
                    aria-label="Afficher le mot de passe"
                    title="Afficher le mot de passe">👁</button>
            </div>

            <div class="submit-part">
                <input type="submit" value="Créer le compte">
            </div>

        </form>

        <p class="auth-link">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>

    </div>

    <script>
        document.querySelectorAll('.password-field').forEach(function(field) {
            const input = field.querySelector('.password-input');
            const button = field.querySelector('.password-toggle');

            button.addEventListener('click', function() {
                const isPasswordHidden = input.type === 'password';

                input.type = isPasswordHidden ? 'text' : 'password';
                button.textContent = isPasswordHidden ? '👁' : '👁';

                button.setAttribute(
                    'aria-label',
                    isPasswordHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe'
                );

                button.setAttribute(
                    'title',
                    isPasswordHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe'
                );
            });
        });
    </script>

</body>

</html>