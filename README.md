# GTB - Gestion Technique de Bâtiment

## Présentation

GTB est un projet de supervision technique de bâtiment réalisé dans un cadre pédagogique.  
Son objectif est de surveiller en temps réel différents paramètres environnementaux dans plusieurs salles d'un bâtiment à l'aide de capteurs, d'un système embarqué, d'une base de données et d'une interface web.

---

## Objectifs

Le projet permet de :

- mesurer des données environnementales en temps réel
- transmettre les données vers un poste de traitement
- stocker les mesures dans une base de données SQL
- afficher les informations sur une interface web
- visualiser des flux de caméras IP
- sécuriser l'accès par authentification

---

## Matériel utilisé

- Caméra : Tapo C500 V2
- Capteur température, humidité et CO2 : Grove - SCD30
- Luminosité : Grove - Sunlight Sensor
- Microcontrôleur : Arduino Uno R4 WiFi

---

## Communication

Le projet utilise une communication par WiFi.  
Notre choix est d'utiliser le protocole HTTPS pour sécuriser les échanges entre les éléments du système.

---

## Paramètres surveillés

Les principaux paramètres suivis sont :

- température
- humidité
- CO2
- luminosité

---

## Architecture générale

Le système repose sur plusieurs blocs :

### 1. Acquisition

Des capteurs relèvent les mesures dans les différentes salles supervisées.

### 2. Traitement embarqué

Une carte microcontrôleur récupère les données et prépare leur transmission.

### 3. Transmission

Les mesures sont envoyées vers un poste central de traitement grâce à la communication WiFi.

### 4. Stockage

Les données sont enregistrées dans une base SQL pour permettre leur consultation et leur historique.

### 5. Supervision web

Une interface web permet de :

- consulter les mesures
- visualiser l'état des salles
- accéder aux flux vidéo
- se connecter via un système d'authentification

---

## Technologies utilisées

- Arduino Uno R4 WiFi
- capteurs environnementaux
- caméra IP Tapo C500 V2
- PHP
- HTML
- CSS
- SQL / MySQL
- PDO
- HTTPS
- WiFi
- Git / GitHub

---

## Fonctionnalités principales

- acquisition de données environnementales
- supervision en temps réel
- enregistrement en base de données
- interface web de consultation
- authentification utilisateur
- visualisation de flux vidéo IP

---

## Statut du projet

Projet en cours de développement.

---

## Remarques

Ce dépôt est public.  
Aucune information sensible, personnelle ou confidentielle ne doit y être publiée.

## Sécurité

Ce projet est publié à des fins pédagogiques.  
Les informations sensibles comme les identifiants, mots de passe, adresses IP privées, clés API ou données personnelles ne doivent pas être versionnées dans ce dépôt.
