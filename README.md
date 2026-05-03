# GTB - Gestion Technique de Batiment

## Presentation

GTB est un projet de supervision technique de batiment realise dans un cadre pedagogique.
Il permet de suivre plusieurs salles d'un batiment a partir de capteurs, de cameras IP, d'une base de donnees MySQL et d'une interface web en PHP.

L'objectif est de centraliser les informations techniques du batiment pour consulter l'etat des salles, les mesures environnementales, les cameras et les alertes depuis une interface securisee.

---

## Fonctionnalites actuelles

- authentification utilisateur avec connexion, inscription et deconnexion
- mots de passe stockes avec `password_hash`
- verification des mots de passe avec `password_verify`
- protection CSRF sur les formulaires sensibles
- messages flash affiches sous forme de notifications Bootstrap
- tableau de bord avec acces rapide aux principales pages
- barre de navigation commune aux pages connectees
- recherche simple dans la barre de navigation
- page `Salles` qui liste les salles enregistrees en base
- page de detail d'une salle avec capteurs, statistiques de mesures et cameras rattachees
- actualisation automatique de la page de detail des salles
- page `Cameras` preparee pour regrouper les flux video
- page `Alertes` preparee pour afficher les alertes du systeme
- page `Mot de passe oublie` indiquant la marche a suivre
- configuration de la base via variables d'environnement
- styles separes pour l'interface globale, le tableau de bord et les pages d'authentification

---

## Pages principales

| Fichier | Role |
| --- | --- |
| `login.php` | Connexion utilisateur avec protection CSRF |
| `register.php` | Creation de compte avec validation des champs |
| `forgot-password.php` | Page d'aide pour mot de passe oublie |
| `logout.php` | Deconnexion securisee en POST avec token CSRF |
| `dashboard.php` | Tableau de bord apres connexion |
| `salles.php` | Liste des salles presentes en base |
| `salle-detail.php` | Detail d'une salle, capteurs, mesures et cameras |
| `cameras.php` | Page prevue pour les cameras du projet |
| `alertes.php` | Page prevue pour les alertes du systeme |

---

## Structure du projet

```text
.
├── assets/
│   ├── css/
│   │   ├── dashboard.css
│   │   ├── global.css
│   │   └── login.css
│   └── js/
│       └── dashboard.js
├── config/
│   └── database.php
├── includes/
│   ├── auth_check.php
│   ├── footer.php
│   ├── header.php
│   ├── navbar.php
│   └── security.php
├── alertes.php
├── cameras.php
├── dashboard.php
├── forgot-password.php
├── login.php
├── logout.php
├── register.php
├── salle-detail.php
└── salles.php
```

---

## Securite

Le projet integre plusieurs protections cote application :

- demarrage centralise des sessions avec `ensure_session_started`
- generation et validation de tokens CSRF
- deconnexion uniquement en requete POST validee par token
- regeneration de l'identifiant de session apres connexion
- hash des mots de passe avant enregistrement
- echappement HTML avec `htmlspecialchars` lors de l'affichage
- requetes preparees PDO pour les donnees utilisateur
- acces protege aux pages internes avec `includes/auth_check.php`

Ce projet est publie a des fins pedagogiques. Les identifiants, mots de passe, adresses IP privees, cles API ou donnees personnelles ne doivent pas etre versionnes dans ce depot.

---

## Base de donnees

La connexion MySQL se fait avec PDO dans `config/database.php`.

Par defaut, l'application utilise :

| Variable | Valeur par defaut |
| --- | --- |
| `GTB_DB_HOST` | `localhost` |
| `GTB_DB_NAME` | `gtb` |
| `GTB_DB_USER` | `root` |
| `GTB_DB_PASS` | `root` |

Ces valeurs peuvent etre remplacees par des variables d'environnement.

Tables utilisees ou attendues par l'application :

- `users` pour les comptes utilisateurs
- `salles` pour la liste des salles
- `capteurs` pour les capteurs rattaches aux salles
- `mesures` pour les valeurs relevees par les capteurs
- `cameras` pour les cameras rattachees aux salles

La page `salle-detail.php` verifie l'existence de certaines tables et colonnes avant d'afficher les donnees. Cela permet d'avoir une page plus tolerante pendant le developpement de la base.

---

## Parametres surveilles

Les principaux parametres prevus sont :

- temperature
- humidite
- CO2
- luminosite

La page de detail d'une salle peut afficher, si les donnees existent :

- derniere valeur
- moyenne
- minimum
- maximum
- nombre de mesures
- derniere date de mesure

---

## Materiel utilise

- camera IP Tapo C500 V2
- capteur temperature, humidite et CO2 Grove - SCD30
- capteur de luminosite Grove - Sunlight Sensor
- microcontroleur Arduino Uno R4 WiFi

---

## Communication

Le projet prevoit une communication par WiFi entre les elements du systeme.
Le protocole HTTPS est prevu pour securiser les echanges entre les composants.

---

## Technologies utilisees

- PHP
- MySQL
- PDO
- HTML
- CSS
- JavaScript
- Bootstrap 5
- Arduino Uno R4 WiFi
- capteurs environnementaux
- camera IP
- Git / GitHub

---

## Statut du projet

Projet en cours de developpement.

Les pages `Cameras` et `Alertes` sont deja integrees dans l'interface, mais restent des pages de preparation tant que les flux cameras et les alertes dynamiques ne sont pas encore branches.

---

## Remarques

Ce depot est public.
Aucune information sensible, personnelle ou confidentielle ne doit y etre publiee.
