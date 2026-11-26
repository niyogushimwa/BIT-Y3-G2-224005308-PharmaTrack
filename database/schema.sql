-- ============================================================
--  PharmaTrack - Online Pharmacy Stock Alert System
--  schema.sql - database structure (3NF)
--  MySQL / MariaDB (XAMPP)
-- ============================================================

CREATE DATABASE IF NOT EXISTS pharmacy_stock_alert
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE pharmacy_stock_alert;

SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)  NOT NULL,
    email         VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('admin', 'pharmacist') NOT NULL DEFAULT 'pharmacist',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- suppliers
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS suppliers (
    supplier_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(120) NOT NULL,
    phone       VARCHAR(30)  NOT NULL,
    email       VARCHAR(100) DEFAULT NULL,
    address     VARCHAR(255) DEFAULT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (supplier_id),
    KEY idx_suppliers_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- medicines
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS medicines (
    medicine_id   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(150) NOT NULL,
    category      VARCHAR(100) NOT NULL,
    stock         INT UNSIGNED NOT NULL DEFAULT 0,
    reorder_level INT UNSIGNED NOT NULL DEFAULT 10,
    expiry_date   DATE NOT NULL,
    supplier_id   INT UNSIGNED DEFAULT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (medicine_id),
    KEY idx_medicines_name (name),
    KEY idx_medicines_expiry (expiry_date),
    KEY idx_medicines_supplier (supplier_id),
    CONSTRAINT fk_medicines_supplier
        FOREIGN KEY (supplier_id) REFERENCES suppliers (supplier_id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- stock_alerts
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS stock_alerts (
    alert_id     INT UNSIGNED NOT NULL AUTO_INCREMENT,
    medicine_id  INT UNSIGNED NOT NULL,
    alert_type   ENUM('low', 'expired') NOT NULL,
    triggered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved     TINYINT(1) NOT NULL DEFAULT 0,
    resolved_at  DATETIME DEFAULT NULL,
    PRIMARY KEY (alert_id),
    KEY idx_alerts_active (resolved, alert_type),
    KEY idx_alerts_medicine (medicine_id),
    CONSTRAINT fk_alerts_medicine
        FOREIGN KEY (medicine_id) REFERENCES medicines (medicine_id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
