<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

ensure_session_started();

$csrf_token = get_csrf_token();
$erreur     = '';
$token      = trim($_GET['token'] ?? '');

// Valider le token dès l'arrivée sur la page
// On vérifie qu'il existe, n'est pas expiré et n'a pas déjà été utilisé
$tokenData = null;
if ($token !== '') {
    $stmt = $conn->prepare(
        "SELECT email, expires_at FROM password_reset_tokens
         WHERE token = :token AND used = 0 AND expires_at > NOW()"
    );
    $stmt->execute([':token' => $token]);
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$tokenData) {
    set_flash_message('danger', 'Ce lien de réinitialisation est invalide ou a expiré. Faites une nouvelle demande.');
    header('Location: forgot-password.php');
    exit();
}

// Traitement du nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $erreur = "La session a expiré ou la requête est invalide.";
    } else {
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if ($password === '') {
            $erreur = "Le mot de passe ne peut pas être vide.";
        } elseif ($password !== $confirm) {
            $erreur = "Les mots de passe ne correspondent pas.";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            $erreur = "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.";
        }

        if ($erreur === '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Mettre à jour le mot de passe de l'utilisateur
            $conn->prepare("UPDATE users SET passwrd = :hash WHERE email = :email")
                 ->execute([':hash' => $hash, ':email' => $tokenData['email']]);

            // Invalider le token pour qu'il ne puisse plus être réutilisé
            $conn->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token = :token")
                 ->execute([':token' => $token]);

            set_flash_message('success', 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.');
            header('Location: login.php');
            exit();
        }
    }
}

$pageTitle = 'Nouveau mot de passe';
$bodyClass = 'auth-page d-flex align-items-center justify-content-center p-3';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <p class="text-secondary fw-medium mb-2">Sécurité</p>
                    <h1 class="h3 fw-bold mb-3">Nouveau mot de passe</h1>
                    <p class="text-secondary mb-4">
                        Choisissez un mot de passe d'au moins 8 caractères,
                        avec une majuscule et un chiffre.
                    </p>

                    <?php if ($erreur !== ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($erreur) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Le token est passé en paramètre GET et retransmis en POST via champ caché -->
                    <form action="reset-password.php?token=<?= urlencode($token) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control password-input"
                                       id="password" name="password"
                                       autocomplete="new-password" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button">Afficher</button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control password-input"
                                       id="confirm_password" name="confirm_password"
                                       autocomplete="new-password" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button">Afficher</button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            Enregistrer le nouveau mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll('.password-toggle').forEach(function(button) {
        button.addEventListener('click', function() {
            const input = button.closest('.input-group').querySelector('.password-input');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.textContent = isHidden ? 'Masquer' : 'Afficher';
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
