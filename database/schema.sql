CREATE DATABASE IF NOT EXISTS ems_core_php CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ems_core_php;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,

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