-- SQL script for Restaurant POS Database
CREATE DATABASE IF NOT EXISTS `dukes_cakes_venta` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dukes_cakes_venta`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user: admin / admin (hashed using PASSWORD_BCRYPT)
INSERT INTO `users` (`id`, `username`, `password`) 
VALUES (1, 'admin', '$2y$10$iWbHnlydGvW07Fw2K8sW3.M0b7dKk3lXJ8Wv8q9x3dJj3v.y8b9K.')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 2. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `icon` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `icon`) VALUES
(1, 'Hamburguesas', '🍔'),
(2, 'Hot Dogs', '🌭'),
(3, 'Papas Fritas', '🍟'),
(4, 'Bebidas', '🥤'),
(5, 'Postres', '🍦')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 3. Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `active` TINYINT(1) DEFAULT 1,
  `use_recipe` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `code`, `name`, `description`, `price`, `image`, `active`, `use_recipe`) VALUES
(1, 1, 'HAM-SUP', 'Hamburguesa Suprema', 'Deliciosa hamburguesa doble de carne de res con queso, lechuga y salsa especial.', 5.50, 'burger_suprema.png', 1, 1),
(2, 1, 'HAM-BBQ', 'Hamburguesa BBQ Tocino', 'Hamburguesa simple de res con tocino crujiente, queso cheddar y salsa BBQ.', 6.20, 'burger_bbq.png', 1, 1),
(3, 2, 'HOT-CLA', 'Clásico Hot Dog', 'Salchicha Frankfurt, mostaza, salsa de tomate y papas fritas trituradas encima.', 3.00, 'hotdog_clasico.png', 1, 1),
(4, 3, 'PAP-MED', 'Papas Fritas Medianas', 'Papas fritas crujientes doradas, ligeramente sazonadas con sal marina.', 2.50, 'papas_medianas.png', 1, 0),
(5, 4, 'REF-GIG', 'Refresco Gigante', 'Coca-Cola helada de 32oz para acompañar tu menú.', 1.80, 'refresco.png', 1, 0),
(6, 5, 'POS-SUN', 'Sundae de Chocolate', 'Helado suave de vainilla cubierto con delicioso sirope de chocolate caliente.', 2.20, 'sundae_chocolate.png', 1, 0)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 4. Raw Materials Table (Materia Prima)
CREATE TABLE IF NOT EXISTS `raw_materials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `unit` VARCHAR(20) NOT NULL, -- gr, ml, un, lonchas, etc.
  `current_stock` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `min_stock` DECIMAL(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `raw_materials` (`id`, `name`, `unit`, `current_stock`, `min_stock`) VALUES
(1, 'Carne de res (150g)', 'un', 100.00, 15.00),
(2, 'Pan de hamburguesa con sésamo', 'un', 120.00, 20.00),
(3, 'Queso Cheddar lonchas', 'un', 150.00, 30.00),
(4, 'Tocino ahumado tiras', 'un', 80.00, 15.00),
(5, 'Salchicha Frankfurt', 'un', 90.00, 20.00),
(6, 'Pan de Hot Dog', 'un', 100.00, 20.00)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- 5. Recipes Table (Recetas por producto)
CREATE TABLE IF NOT EXISTS `recipes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `raw_material_id` INT NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL, -- Cantidad de materia prima requerida
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Recipe for Hamburguesa Suprema (Product 1): 1 pan, 2 carnes, 2 quesos
INSERT INTO `recipes` (`product_id`, `raw_material_id`, `quantity`) VALUES
(1, 1, 2.00), -- 2 carnes
(1, 2, 1.00), -- 1 pan
(1, 3, 2.00)  -- 2 quesos
ON DUPLICATE KEY UPDATE `quantity`=VALUES(`quantity`);

-- Recipe for Hamburguesa BBQ Tocino (Product 2): 1 pan, 1 carne, 1 queso, 2 tocinos
INSERT INTO `recipes` (`product_id`, `raw_material_id`, `quantity`) VALUES
(2, 1, 1.00),
(2, 2, 1.00),
(2, 3, 1.00),
(2, 4, 2.00)
ON DUPLICATE KEY UPDATE `quantity`=VALUES(`quantity`);

-- Recipe for Clásico Hot Dog (Product 3): 1 pan de hotdog, 1 salchicha
INSERT INTO `recipes` (`product_id`, `raw_material_id`, `quantity`) VALUES
(3, 5, 1.00),
(3, 6, 1.00)
ON DUPLICATE KEY UPDATE `quantity`=VALUES(`quantity`);

-- 6. Sales Table (Ventas)
CREATE TABLE IF NOT EXISTS `sales` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sale_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `total` DECIMAL(10,2) NOT NULL,
  `items_count` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Sale Details Table (Detalle de Ventas)
CREATE TABLE IF NOT EXISTS `sale_details` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sale_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `price` DECIMAL(10,2) NOT NULL, -- precio histórico
  `quantity` INT NOT NULL,
  FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Settings Table (Configuraciones)
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('track_raw_materials', '0') -- Desactivado por defecto
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);
