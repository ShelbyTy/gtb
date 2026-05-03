# GTB - Gestion Technique de Bâtiment

## Présentation

GTB est un projet de supervision technique de bâtiment réalisé dans un cadre pédagogique.
Il permet de suivre plusieurs salles d'un bâtiment à partir de capteurs, de caméras IP, d'une base de données MySQL et d'une interface web en PHP.

L'objectif est de centraliser les informations techniques du bâtiment pour consulter l'état des salles, les mesures environnementales, les caméras et les alertes depuis une interface sécurisée.

---

## Fonctionnalités actuelles

- authentification utilisateur avec connexion, inscription et déconnexion
- mots de passe stockés avec `password_hash`
- vérification des mots de passe avec `password_verify`
- protection CSRF sur les formulaires sensibles
- messages flash affichés sous forme de notifications Bootstrap
- tableau de bord avec accès rapide aux principales pages
- barre de navigation commune aux pages connectées
- recherche simple dans la barre de navigation
- page `Salles` qui liste les salles enregistrées en base
- page de détail d'une salle avec capteurs, statistiques de mesures et caméras rattachées
- actualisation automatique de la page de détail des salles
- page `Caméras` préparée pour regrouper les flux vidéo
- page `Alertes` préparée pour afficher les alertes du système
- page `Mot de passe oublié` indiquant la marche à suivre
- configuration de la base via variables d'environnement
- styles séparés pour l'interface globale, le tableau de bord et les pages d'authentification

---

## Pages principales

| Fichier | Rôle |
| --- | --- |
| `login.php` | Connexion utilisateur avec protection CSRF |
| `register.php` | Création de compte avec validation des champs |
| `forgot-password.php` | Page d'aide pour mot de passe oublié |
| `logout.php` | Déconnexion sécurisée en POST avec token CSRF |
| `dashboard.php` | Tableau de bord après connexion |
| `salles.php` | Liste des salles présentes en base |
| `salle-detail.php` | Détail d'une salle, capteurs, mesures et caméras |
| `cameras.php` | Page prévue pour les caméras du projet |
| `alertes.php` | Page prévue pour les alertes du système |

---

## Structure du projet

```text
.
+-- assets/
|   +-- css/
|   |   +-- dashboard.css
|   |   +-- global.css
|   |   +-- login.css
|   +-- js/
|       +-- dashboard.js
+-- config/
|   +-- database.php
+-- includes/
|   +-- auth_check.php
|   +-- footer.php
|   +-- header.php
|   +-- navbar.php
|   +-- security.php
+-- alertes.php
+-- cameras.php
+-- dashboard.php
+-- forgot-password.php
+-- login.php
+-- logout.php
+-- register.php
+-- salle-detail.php
+-- salles.php
```

---

## Sécurité

Le projet intègre plusieurs protections côté application :

- démarrage centralisé des sessions avec `ensure_session_started`
- génération et validation de tokens CSRF
- déconnexion uniquement en requête POST validée par token
- régénération de l'identifiant de session après connexion
- hash des mots de passe avant enregistrement
- échappement HTML avec `htmlspecialchars` lors de l'affichage
- requêtes préparées PDO pour les données utilisateur
- accès protégé aux pages internes avec `includes/auth_check.php`

Ce projet est publié à des fins pédagogiques. Les identifiants, mots de passe, adresses IP privées, clés API ou données personnelles ne doivent pas être versionnés dans ce dépôt.

---

## Base de données

La connexion MySQL se fait avec PDO dans `config/database.php`.

Par défaut, l'application utilise :

| Variable | Valeur par défaut |
| --- | --- |
| `GTB_DB_HOST` | `localhost` |
| `GTB_DB_NAME` | `gtb` |
| `GTB_DB_USER` | `root` |
| `GTB_DB_PASS` | `root` |

Ces valeurs peuvent être remplacées par des variables d'environnement.

Tables utilisées ou attendues par l'application :

- `users` pour les comptes utilisateurs
- `salles` pour la liste des salles
- `capteurs` pour les capteurs rattachés aux salles
- `mesures` pour les valeurs relevées par les capteurs
- `cameras` pour les caméras rattachées aux salles

La page `salle-detail.php` vérifie l'existence de certaines tables et colonnes avant d'afficher les données. Cela permet d'avoir une page plus tolérante pendant le développement de la base.

---

## Paramètres surveillés

Les principaux paramètres prévus sont :

- température
- humidité
- CO2
- luminosité

La page de détail d'une salle peut afficher, si les données existent :

- dernière valeur
- moyenne
- minimum
- maximum
- nombre de mesures
- dernière date de mesure

---

## Matériel utilisé

- caméra IP Tapo C500 V2
- capteur température, humidité et CO2 Grove - SCD30
- capteur de luminosité Grove - Sunlight Sensor
- microcontrôleur Arduino Uno R4 WiFi

---

## Communication

Le projet prévoit une communication par WiFi entre les éléments du système.
Le protocole HTTPS est prévu pour sécuriser les échanges entre les composants.

---

## Technologies utilisées

- PHP
- MySQL
- PDO
- HTML
- CSS
- JavaScript
- Bootstrap 5
- Arduino Uno R4 WiFi
- capteurs environnementaux
- caméra IP
- Git / GitHub

---

## Statut du projet

Projet en cours de développement.

Les pages `Caméras` et `Alertes` sont déjà intégrées dans l'interface, mais restent des pages de préparation tant que les flux caméras et les alertes dynamiques ne sont pas encore branchés.

---

## Remarques

Ce dépôt est public.
Aucune information sensible, personnelle ou confidentielle ne doit y être publiée.
