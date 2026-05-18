-- ============================================================
-- ZIMNAT LIFE ASSURANCE - Policy Renewal Reminder System
-- Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS zimnat_prs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zimnat_prs;

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(150)  NOT NULL,
    email       VARCHAR(255)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    role        ENUM('admin','policy_officer','viewer') NOT NULL DEFAULT 'viewer',
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Table: policies
-- ============================================================
CREATE TABLE IF NOT EXISTS policies (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    policy_number   VARCHAR(50)     NOT NULL UNIQUE,
    client_name     VARCHAR(150)    NOT NULL,
    insurance_type  VARCHAR(100)    NOT NULL,
    premium_amount  DECIMAL(15,2)   NOT NULL,
    start_date      DATE            NOT NULL,
    renewal_date    DATE            NOT NULL,
    status          ENUM('Active','Expired','Pending Renewal') NOT NULL DEFAULT 'Active',
    created_by      INT UNSIGNED    NOT NULL,
    updated_by      INT UNSIGNED    NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_policies_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON UPDATE CASCADE,
    CONSTRAINT fk_policies_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Table: documents
-- ============================================================
CREATE TABLE IF NOT EXISTS documents (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    policy_id       INT UNSIGNED    NOT NULL,
    original_name   VARCHAR(255)    NOT NULL,
    stored_name     VARCHAR(255)    NOT NULL,
    file_path       VARCHAR(500)    NOT NULL,
    mime_type       VARCHAR(100)    NOT NULL,
    file_size       INT UNSIGNED    NOT NULL,
    uploaded_by     INT UNSIGNED    NOT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_documents_policy   FOREIGN KEY (policy_id)   REFERENCES policies(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_documents_uploader FOREIGN KEY (uploaded_by) REFERENCES users(id)   ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Indexes for performance
-- ============================================================
CREATE INDEX idx_policies_renewal_date ON policies(renewal_date);
CREATE INDEX idx_policies_status       ON policies(status);
CREATE INDEX idx_documents_policy_id   ON documents(policy_id);

-- ============================================================
-- Seed: Default Admin User
-- Password: Admin@1234 (bcrypt)
-- ============================================================
INSERT INTO users (full_name, email, password, role, is_active) VALUES
(
    'System Administrator',
    'admin@zimnat.co.zw',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1
);

-- Note: Default password is "Admin@1234"
-- Change immediately after first login.
