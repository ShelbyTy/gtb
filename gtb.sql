-- ============================================================
-- Base de données : gtb
-- Générée depuis l'analyse du code source
-- ============================================================

CREATE DATABASE IF NOT EXISTS `gtb`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE `gtb`;

-- ------------------------------------------------------------
-- Table : users
-- Comptes utilisateurs de l'application
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`       INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(100)     NOT NULL,
    `email`    VARCHAR(255)     NOT NULL,
    `passwrd`  VARCHAR(255)     NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : salles
-- Salles du bâtiment surveillées par le système GTB
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `salles` (
    `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `nom`          VARCHAR(150)  NOT NULL,
    `type`         VARCHAR(100)  NOT NULL,
    `open_for_all` TINYINT(1)    NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : capteurs
-- Capteurs physiques rattachés à une salle
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `capteurs` (
    `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `id_salle`     INT UNSIGNED  NOT NULL,
    `type`         VARCHAR(100)  NOT NULL,
    `unite`        VARCHAR(20)   NOT NULL DEFAULT '',
    `id_arduino`   VARCHAR(50)   NULL     DEFAULT NULL,
    `is_connected` TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `fk_capteurs_salle` (`id_salle`),
    CONSTRAINT `fk_capteurs_salle`
        FOREIGN KEY (`id_salle`) REFERENCES `salles` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : mesures
-- Valeurs relevées par les capteurs au fil du temps
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mesures` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `id_capteur`  INT UNSIGNED     NOT NULL,
    `type_mesure` VARCHAR(100)     NOT NULL,
    `valeur`      DECIMAL(10, 4)   NOT NULL,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fk_mesures_capteur` (`id_capteur`),
    CONSTRAINT `fk_mesures_capteur`
        FOREIGN KEY (`id_capteur`) REFERENCES `capteurs` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : alertes
-- Alertes générées automatiquement quand un seuil est dépassé
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `alertes` (
    `id`                INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    `id_capteur`        INT UNSIGNED        NOT NULL,
    `type_alerte`       VARCHAR(200)        NOT NULL,
    `message`           TEXT                NOT NULL,
    `valeur_declencheur` DECIMAL(10, 4)     NULL DEFAULT NULL,
    `seuil`             DECIMAL(10, 4)      NULL DEFAULT NULL,
    `niveau`            ENUM('info','warning','critical') NOT NULL DEFAULT 'warning',
    `is_resolved`       TINYINT(1)          NOT NULL DEFAULT 0,
    `created_at`        DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `resolved_at`       DATETIME            NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_alertes_capteur` (`id_capteur`),
    KEY `idx_alertes_resolved` (`is_resolved`),
    CONSTRAINT `fk_alertes_capteur`
        FOREIGN KEY (`id_capteur`) REFERENCES `capteurs` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table : cameras
-- Caméras installées dans les salles
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cameras` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `id_salle`      INT UNSIGNED  NOT NULL,
    `nom`           VARCHAR(150)  NOT NULL,
    `url_flux`      VARCHAR(500)  NULL DEFAULT NULL,
    `camera_status` TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `fk_cameras_salle` (`id_salle`),
    CONSTRAINT `fk_cameras_salle`
        FOREIGN KEY (`id_salle`) REFERENCES `salles` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
