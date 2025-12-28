-- SQL migration for WorkmateApp backend
-- Create database and tables for app tracking + dashboard

CREATE DATABASE IF NOT EXISTS monitor DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE monitor;

CREATE TABLE IF NOT EXISTS app_usage (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id VARCHAR(255) NOT NULL,
  app_name VARCHAR(255) NOT NULL,
  screenshot_path VARCHAR(512) DEFAULT NULL,
  duration INT NOT NULL,
  online BOOLEAN NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Managers table for dashboard authentication
CREATE TABLE IF NOT EXISTS managers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create a default manager account (username: admin, password: admin123)
-- To change, run: UPDATE managers SET password_hash = PASSWORD('admin123') WHERE username='admin';
INSERT IGNORE INTO managers (username, password_hash) VALUES (
  'admin',
  '$2y$10$0qxWfqGYBb5j5KmY9v9zVuVpFjFJb4R7I7GBiF4jL5vxKqD0v5Vze'
);



