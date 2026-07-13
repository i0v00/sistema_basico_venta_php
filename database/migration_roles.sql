-- Script de Migración para Base de Datos (Sistema de Roles y Pedidos en Vivo)
-- Duke's Fast Food POS

-- 1. Modificar tabla de usuarios para añadir columnas de rol, nombre completo y estado activo
ALTER TABLE `users` 
  ADD COLUMN `role` VARCHAR(20) NOT NULL DEFAULT 'caja' AFTER `password`,
  ADD COLUMN `full_name` VARCHAR(100) NULL AFTER `role`,
  ADD COLUMN `active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `full_name`;

-- 2. Modificar tabla de ventas (sales) para asociar el cajero y el estado de preparación
ALTER TABLE `sales`
  ADD COLUMN `cashier_id` INT NULL AFTER `items_count`,
  ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'pendiente' AFTER `cashier_id`,
  ADD CONSTRAINT `fk_sales_cashier` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- 3. Actualizar el usuario administrador por defecto
UPDATE `users` 
  SET `role` = 'admin', `full_name` = 'Administrador Sistema' 
  WHERE `username` = 'admin';

-- 4. Crear índices para optimizar consultas de tiempo real de los pedidos
CREATE INDEX `idx_sales_date_status` ON `sales` (`sale_date`, `status`);
