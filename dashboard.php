<?php
require_once __DIR__ . '/includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">

    <link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/filled/boxicons-filled.min.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.8/fonts/brands/boxicons-brands.min.css" rel="stylesheet">

    <title>Tableau de bord</title>
</head>

<body>

    <aside class="side-menu">

        <section class="menu-header">
            <h2>GTB</h2>
        </section>

        <section class="menu-navigation">
            <nav>
                <ul class="nav-list">
                    <li class="nav-item"><a href="#">Tableau de bord</a></li>
                    <li class="nav-item"><a href="#">Capteurs</a></li>
                    <li class="nav-item"><a href="#">Graphiques</a></li>
                    <li class="nav-item"><a href="#">Rapports</a></li>
                    <li class="nav-item"><a href="#">Caméras</a></li>
                    <li class="nav-item"><a href="#">Utilisateur</a></li>
                    <li class="nav-item"><a href="#">Logs</a></li>
                    <li class="nav-item"><a href="#">Paramètres</a></li>
                </ul>
            </nav>
        </section>

        <section class="menu-footer">
            <a href="logout.php">Se déconnecter</a>
        </section>

    </aside>

</body>

</html>
