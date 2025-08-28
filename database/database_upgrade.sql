-- Modern Cafe Billing System - Database Enhancements 
-- Run this in your phpMyAdmin or MySQL console 
 
-- Add new columns for enhanced functionality 
ALTER TABLE `orders`  
ADD COLUMN `payment_method` VARCHAR(50) DEFAULT 'cash', 
ADD COLUMN `notes` TEXT, 
ADD COLUMN `discount_amount` DECIMAL(10,2) DEFAULT 0.00, 
ADD COLUMN `tax_amount` DECIMAL(10,2) DEFAULT 0.00; 
 
-- Add image support for products 
ALTER TABLE `products`  
ADD COLUMN `image_url` VARCHAR(255), 
ADD COLUMN `barcode` VARCHAR(100), 
ADD COLUMN `stock_quantity` INT DEFAULT 0; 
 
-- Enhanced system settings 
ALTER TABLE `system_settings`  
ADD COLUMN `theme_color` VARCHAR(7) DEFAULT '#2c3e50', 
ADD COLUMN `currency_symbol` VARCHAR(5) DEFAULT '$', 
ADD COLUMN `tax_rate` DECIMAL(5,2) DEFAULT 0.00; 
