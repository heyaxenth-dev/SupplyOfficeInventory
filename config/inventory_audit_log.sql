-- Inventory Audit Log Table
-- Tracks who made what changes to inventory items

CREATE TABLE IF NOT EXISTS `inventory_audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  `action` enum('ADD','EDIT','DELETE') NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `stock_number` varchar(100) DEFAULT NULL,
  `changes_summary` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_action` (`action`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
