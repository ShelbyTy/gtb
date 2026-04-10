# GTB — Gestion Technique de Bâtiment

## Présentation du projet

Le projet **GTB (Gestion Technique de Bâtiment)** est un projet de fin d’année réalisé dans le cadre du **BTS CIEL Option A**.  
Il a pour objectif de mettre en place une solution de **supervision en temps réel** de deux salles d’un bâtiment :

- un **open space**
- un **local technique**

Le système permet de mesurer, transmettre, stocker et afficher plusieurs paramètres environnementaux afin de surveiller l’état des salles en continu.

---

## Objectifs

L’objectif principal du projet est de concevoir une plateforme capable de :

- relever des données environnementales en temps réel
- transmettre ces données vers un poste de traitement
- enregistrer les mesures dans une base de données SQL
- afficher les informations sur une interface web
- permettre la visualisation de deux flux de caméras IP
- proposer un accès sécurisé via authentification

---

## Paramètres surveillés

Les paramètres mesurés dans les salles sont les suivants :

- **température**
- **humidité**
- **CO2**
- **luminosité**

Ces données sont collectées à l’aide de cartes **Arduino Uno R4 WiFi** et de capteurs adaptés.

---

## Architecture générale

Le projet repose sur plusieurs blocs fonctionnels :

### 1. Acquisition des données
Des capteurs installés dans les deux salles relèvent les différentes mesures environnementales.

### 2. Traitement embarqué
Les cartes **Arduino Uno R4 WiFi** récupèrent les données des capteurs et préparent les trames à envoyer.

### 3. Transmission
Les mesures sont envoyées vers un **PC de traitement** pour être exploitées.

### 4. Stockage
Les données reçues sont insérées dans une **base de données SQL** afin de conserver un historique des mesures.

### 5. Supervision web
Un site web permet d’afficher :
- les valeurs mesurées
- l’état des deux salles
- les flux de **deux caméras IP**
- une interface de connexion avec **login / register**

---

## Technologies utilisées

Selon l’avancement actuel du projet, les technologies utilisées ou prévues sont :

- **Arduino Uno R4 WiFi**
- **Capteurs environnementaux**
- **PHP**
- **HTML / CSS**
- **SQL / MySQL**
- **PDO** pour la connexion à la base de données
- **phpMyAdmin** pour l’administration de la base
- **Git / GitHub** pour le versionnement

---

## Fonctionnalités principales

- acquisition de données environnementales
- supervision en temps réel
- stockage des mesures en base SQL
- affichage des données sur une interface web
- authentification utilisateur
- affichage de deux flux vidéo IP
- suivi de deux salles distinctes

---

## Organisation du projet

Le projet est réalisé par une équipe de 4 étudiants :

- **Jean LECAILLIER**
- **Baptiste STAUMONT**
- **Diaminatou BARY**
- **Médéric QUERON**

---

## Enjeux techniques

Ce projet mobilise plusieurs compétences du BTS CIEL :

- systèmes embarqués
- réseaux
- développement web
- bases de données
- communication entre équipements
- supervision de systèmes techniques

Il permet également de travailler sur une problématique concrète de **gestion intelligente d’un bâtiment**.

---

## Perspectives d’amélioration

À terme, le projet pourrait être enrichi avec :

- des alertes automatiques en cas de dépassement de seuil
- des graphiques d’évolution des mesures
- une meilleure gestion des utilisateurs
- l’export des données
- une interface d’administration plus complète
- des statistiques sur l’historique des salles

---

## Statut du projet

Projet en cours de développement dans le cadre du **BTS CIEL Option A**.

---

## Auteur

Projet réalisé dans le cadre d’un travail d’équipe étudiant.  
Participation au développement : **Jean LECAILLIER** et son équipe.
