<?php
// Fichiers utilises pour la securite et la connexion a la base
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

// Demarage de la session si besoin
ensure_session_started();

$erreur = '';
$email = '';
// Token de protection du formulaire
$csrf_token = get_csrf_token();

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verification du token CSRF
    if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $erreur = "La session a expiré ou la requête est invalide.";
    }

    // Recuperation des donnee envoyees
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Controles simple avant de consulter la base
    if ($erreur === '' && ($email === '' || $password === '')) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif ($erreur === '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    }

    if ($erreur === '') {
        try {
            // Recherche de l'utilisateur avec son email
            $query = $conn->prepare("SELECT id, email, passwrd FROM users WHERE email = :email");
            $query->execute([':email' => $email]);
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['passwrd'])) {
                // Si le mot de passe est bon, on connecte l'utilisateur
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                set_flash_message('success', 'Connexion réussie.');

                header('Location: dashboard.php');
                exit();
            }

            $erreur = "Identifiants incorrects.";
        } catch (PDOException $e) {
            $erreur = "Une erreur est survenue, veuillez réessayer.";
        }
    }
}

$pageTitle = 'Se connecter';
$bodyClass = 'auth-page d-flex align-items-center justify-content-center p-3';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container">
    <!-- Carte au centre pour que la connexion soit bien lisible -->
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <p class="text-secondary fw-medium mb-2">Bienvenue</p>
                    <h1 class="h3 fw-bold mb-3">Connexion</h1>
                    <p class="text-secondary mb-4">Veuillez saisir vos identifiants pour accéder au tableau de bord.</p>

                    <form action="" method="post">
                        <!-- Token cacher pour eviter une requete pas normal -->
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <div class="mb-3">
                            <!-- Champ pour rentrer l'adresse mail de l'utilisateur -->
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                        </div>

                        <div class="mb-3">
                            <!-- Champ mot de passe avec un petit bouton pour voir ce qu'on tape -->
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control password-input" id="password" name="password" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button">Afficher</button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-3 mb-4 flex-wrap">
                            <!-- Option garder en affichage, elle pourra servir plus tard -->
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="rememberme" name="rememberme">
                                <label class="form-check-label" for="rememberme">Se souvenir de moi</label>
                            </div>

                            <a href="forgot-password.php" class="link-primary">Mot de passe oublié ?</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Se connecter</button>
                    </form>

                    <p class="text-center text-secondary mt-4 mb-0">
                        Pas encore de compte ?
                        <a href="register.php" class="fw-semibold">Créer un compte</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Petit script pour afficher ou cacher le mot de passe
    document.querySelectorAll('.password-toggle').forEach(function(button) {
        button.addEventListener('click', function() {
            // On cherche l'input qui est dans le meme groupe que le bouton
            const input = button.closest('.input-group').querySelector('.password-input');
            const isHidden = input.type === 'password';

            input.type = isHidden ? 'text' : 'password';
            button.textContent = isHidden ? 'Masquer' : 'Afficher';
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
