CREATE TABLE IF NOT EXISTS `orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `order_id` VARCHAR(100) NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `days` INT NOT NULL,
    `service` VARCHAR(50) NOT NULL DEFAULT 'verified_status',
    `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
    `payment_data` JSON NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `orders_order_id_unique` (`order_id`),
    KEY `orders_user_id_index` (`user_id`),
    KEY `orders_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
