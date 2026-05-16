<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

ensure_session_started();

$csrf_token = get_csrf_token();
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
        $erreur = "La session a expiré ou la requête est invalide.";
    } else {
        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreur = "L'adresse email n'est pas valide.";
        } else {
            // On cherche si l'email existe, mais on affiche toujours le même message
            // pour éviter l'énumération de comptes (savoir si un email est inscrit ou non)
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                // Supprimer les anciens tokens non utilisés pour cet email
                $conn->prepare("DELETE FROM password_reset_tokens WHERE email = :email AND used = 0")
                     ->execute([':email' => $email]);

                // Générer un token aléatoire sécurisé (64 caractères hex)
                $token      = bin2hex(random_bytes(32));
                $expiresAt  = date('Y-m-d H:i:s', time() + 30 * 60); // expire dans 30 minutes

                $stmt = $conn->prepare(
                    "INSERT INTO password_reset_tokens (email, token, expires_at)
                     VALUES (:email, :token, :expires_at)"
                );
                $stmt->execute([
                    ':email'      => $email,
                    ':token'      => $token,
                    ':expires_at' => $expiresAt,
                ]);

                // Construire le lien de réinitialisation
                $protocol  = isset($_SERVER['HTTPS']) ? 'https' : 'http';
                $host      = $_SERVER['HTTP_HOST'];
                $resetLink = "{$protocol}://{$host}/gtb/reset-password.php?token=" . urlencode($token);

                // Envoi de l'email avec mail() — nécessite un serveur SMTP configuré
                // Sur WAMP : installer "fake sendmail" ou configurer SMTP dans php.ini
                $subject = "Réinitialisation de votre mot de passe GTB";
                $message = "Bonjour,\r\n\r\n"
                    . "Vous avez demandé une réinitialisation de mot de passe.\r\n\r\n"
                    . "Cliquez sur le lien ci-dessous pour choisir un nouveau mot de passe :\r\n"
                    . $resetLink . "\r\n\r\n"
                    . "Ce lien est valable 30 minutes.\r\n\r\n"
                    . "Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.\r\n\r\n"
                    . "— L'équipe GTB";
                $headers = "From: noreply@gtb.local\r\nContent-Type: text/plain; charset=UTF-8";

                mail($email, $subject, $message, $headers);
            }

            // Même message qu'il y ait un compte ou non → évite l'énumération d'emails
            set_flash_message('success', 'Si un compte correspond à cette adresse, un lien de réinitialisation vient d\'être envoyé.');
            header('Location: forgot-password.php');
            exit();
        }
    }
}

$pageTitle = 'Mot de passe oublié';
$bodyClass = 'auth-page d-flex align-items-center justify-content-center p-3';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <p class="text-secondary fw-medium mb-2">Assistance</p>
                    <h1 class="h3 fw-bold mb-3">Mot de passe oublié</h1>
                    <p class="text-secondary mb-4">
                        Saisissez votre adresse email. Si un compte lui correspond,
                        vous recevrez un lien valable <strong>30 minutes</strong>.
                    </p>

                    <?php if ($erreur !== ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($erreur) ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <div class="mb-4">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   autocomplete="email" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            Envoyer le lien de réinitialisation
                        </button>
                    </form>

                    <p class="text-center text-secondary mt-4 mb-0">
                        <a href="login.php" class="fw-semibold">Retour à la connexion</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
