CREATE DATABASE IF NOT EXISTS ems_core_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ems_core_php;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    department VARCHAR(120) NULL,
    salary INT UNSIGNED NULL,

    perm_line1 VARCHAR(150) NOT NULL,
    perm_line2 VARCHAR(150) NULL,
    perm_city VARCHAR(100) NOT NULL,
    perm_state VARCHAR(80) NOT NULL,

    curr_line1 VARCHAR(150) NOT NULL,
    curr_line2 VARCHAR(150) NULL,
    curr_city VARCHAR(100) NOT NULL,
    curr_state VARCHAR(80) NOT NULL,

    profile_picture VARCHAR(255) NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_qualifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    qualification VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_qualification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_experiences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    experience VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_experience_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS leave_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason VARCHAR(500) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    decided_by INT UNSIGNED NULL,
    decided_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_leave_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_leave_decided_by FOREIGN KEY (decided_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_leave_user (user_id),
    INDEX idx_leave_status (status),
    INDEX idx_leave_start (start_date),
    INDEX idx_leave_end (end_date)
);

CREATE TABLE IF NOT EXISTS password_reset_otps (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    otp_hash VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_otp_email (email),
    INDEX idx_otp_expires (expires_at)
);

-- Default Admin
-- Email: admin@ems.local
-- Password: Admin@123
INSERT INTO users (
    full_name, email, password_hash, age,
    perm_line1, perm_line2, perm_city, perm_state,
    curr_line1, curr_line2, curr_city, curr_state,
    profile_picture, is_admin
) VALUES (
    'System Admin',
    'admin@ems.local',
    '$2y$10$eWm8iuIc0cMAPxYGF8vb4eXAtbHEV7fY7VXD3n91ehFEstAiodiBq',
    30,
    'Admin Street 1', '', 'Ghaziabad', 'Uttar Pradesh',
    'Admin Street 1', '', 'Jaipur', 'Rajasthan',
    NULL,
    1
)
ON DUPLICATE KEY UPDATE email = email;
