<?php
session_start();

// Message de succès si on revient d'une redirection réussie
$success = (isset($_GET['connexion']) && $_GET['connexion'] === 'ok' && $_SERVER['REQUEST_METHOD'] !== 'POST')
    ? 'Connexion réussie ! Ravie de vous revoir !'
    : '';

try {
    $conn = new PDO('mysql:host=localhost;dbname=gtb_website;charset=utf8mb4', 'root', 'root');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    }

    if (empty($erreur)) {
        try {
            // On cherche l'utilisateur par email
            $query = $conn->prepare("SELECT id, email, passwrd FROM users WHERE email = :email");
            $query->execute([':email' => $email]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            // Vérification sécurisée du mot de passe
            if ($user && password_verify($password, $user['passwrd'])) {
                // Stockage en session si besoin
                $_SESSION['user_id'] = $user['id'];

                // Redirection vers la même page pour afficher le message de succès (ou vers dashboard.php)
                header('Location: login.php?connexion=ok');
                exit();
            } else {
                $erreur = "Identifiants incorrects.";
            }
        } catch (PDOException $e) {
            $erreur = "Une erreur est survenue, veuillez réessayer.";
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
    <title>Se connecter</title>
</head>

<body>

    <div class="login-card">

        <?php if ($success): ?>
            <div class="alert alert-success">
                <span class="icon">✅</span> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($erreur): ?>
            <div class="alert alert-error">
                <span class="icon">❌</span> <?php echo $erreur; ?>
            </div>
        <?php endif; ?>

        <p>Bienvenue</p>
        <h1>Veuillez saisir vos identifiants</h1>

        <form action="" method="post" class="form-login">
            <div class="email-part">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="password-part">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>

            <div class="remember-row">
                <div class="remember-left">
                    <input type="checkbox" id="rememberme" name="rememberme">
                    <label for="rememberme">Se souvenir de moi</label>
                </div>
                <a href="#" class="forgot-link">Mot de passe oublié ?</a>
            </div>

            <div class="submit-part">
                <input type="submit" value="Se connecter">
            </div>
        </form>

        <p class="register-link">Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
    </div>

</body>

</html>