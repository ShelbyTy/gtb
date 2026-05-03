<?php
// Cette page sert juste a informer l'utilisateur de la marche a suivre
require_once __DIR__ . '/includes/security.php';

// Demarage de la session si besoin
ensure_session_started();

$pageTitle = 'Mot de passe oublié';
$bodyClass = 'auth-page d-flex align-items-center justify-content-center p-3';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container">
    <!-- Petite page d'aide quand l'utilisateur a perdu son mot de passe -->
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <!-- Texte simple pour expliquer quoi faire sans vrai systeme d'email -->
                    <p class="text-secondary fw-medium mb-2">Assistance</p>
                    <h1 class="h3 fw-bold mb-3">Mot de passe oublié</h1>

                    <p class="text-secondary mb-4">
                        Pour redéfinir votre mot de passe, merci de contacter le support.
                    </p>

                    <div class="alert alert-primary mb-4" role="alert">
                        Le support pourra vérifier votre compte puis vous indiquer la suite à faire.
                    </div>

                    <!-- Bouton pour revenir a la connexion -->
                    <a href="login.php" class="btn btn-primary w-100 fw-bold py-2">
                        Retour à la connexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
