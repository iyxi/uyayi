-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2026 at 02:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uyayi`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT, 
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(64) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `method` enum('GCash','Card','COD') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Paid','Failed','Refunded') NOT NULL DEFAULT 'Pending',
  `txn_reference` varchar(128) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(100) NOT NULL UNIQUE,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sku` varchar(64) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `parent_id`, `sku`, `name`, `description`, `price`, `stock`, `visible`, `image`, `created_at`, `updated_at`, `deleted_at`, `images`) VALUES
(1, 1, NULL, 'UY-BW-BTL', 'Baby Wash (500ml)', 'Premium gentle formula Baby Wash (500ml) for daily baby care.', 980.00, 100, 1, 'img/baby_wash.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/baby_wash.jpg')),
(2, 1, NULL, 'UY-HL-TBE', 'Hydrating Lotion (200ml)', 'Premium gentle formula Hydrating Lotion (200ml) for daily baby care.', 650.00, 100, 1, 'img/Lotion_Hydrating.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Lotion_Hydrating.jpg')),
(3, 1, NULL, 'UY-BC-JAR', 'Baby Bum Cream (100g)', 'Premium gentle formula Baby Bum Cream (100g) for daily baby care.', 520.00, 100, 1, 'img/Face_Cream.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Face_Cream.jpg')),
(4, 3, NULL, 'UY-SN-BTL', 'Body Sunscreen SPF50', 'Premium gentle formula Body Sunscreen SPF50 for daily baby care.', 890.00, 100, 1, 'img/Body_Sunscreen.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Body_Sunscreen.jpg')),
(5, 2, NULL, 'UY-MO-BTL', 'Massage Oil (100ml)', 'Premium gentle formula Massage Oil (100ml) for daily baby care.', 480.00, 100, 1, 'img/Liquid_Soap.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Liquid_Soap.jpg')),
(6, 5, 1, 'UY-S-WASH', 'Baby Wash  (Sachet)', 'Travel-friendly 3ml sachet. Perfect for testing on sensitive skin.', 130.00, 200, 1, 'img/Sachet_Wash.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Sachet_Wash.jpg')),
(7, 5, 2, 'UY-S-LOTN', 'Hydrating Lotion  (Sachet)', 'Travel-friendly 3ml sachet. Perfect for testing on sensitive skin.', 130.00, 200, 1, 'img/Sachet_Lotion.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Sachet_Lotion.jpg')),
(8, 5, 3, 'UY-S-BUM', 'Baby Bum Cream  (Sachet)', 'Travel-friendly 3ml sachet. Perfect for testing on sensitive skin.', 130.00, 200, 1, 'img/Sachet_BumCream.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Sachet_BumCream.jpg')),
(9, 5, 4, 'UY-S-SUN', 'Body Sunscreen SPF50 (Sachet)', 'Travel-friendly 3ml sachet. Perfect for testing on sensitive skin.', 130.00, 200, 1, 'img/Sachet_Sunscreen.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Sachet_Sunscreen.jpg')),
(10, 5, 5, 'UY-S-OIL', 'Massage Oil  (Sachet)', 'Travel-friendly 3ml sachet. Perfect for testing on sensitive skin.', 130.00, 200, 1, 'img/Sachet_MassageOil.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Sachet_MassageOil.jpg')),
(11, 4, NULL, 'UY-DP-XS-12', 'Ultra-Soft Diapers - XS (12 pcs)', 'Size XS hypoallergenic diapers. Breathable and leak-proof.', 350.00, 150, 1, 'img/XS_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XS_Diaper.jpg')),
(12, 4, 11, 'UY-DP-XS-24', 'Ultra-Soft Diapers - XS (24 pcs)', 'Size XS hypoallergenic diapers. Breathable and leak-proof.', 670.00, 150, 1, 'img/XS_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XS_Diaper.jpg')),
(13, 4, 11, 'UY-DP-XS-36', 'Ultra-Soft Diapers - XS (36 pcs)', 'Size XS hypoallergenic diapers. Breathable and leak-proof.', 990.00, 150, 1, 'img/XS_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XS_Diaper.jpg')),
(14, 4, NULL, 'UY-DP-S-12', 'Ultra-Soft Diapers - S (12 pcs)', 'Size S hypoallergenic diapers. Breathable and leak-proof.', 350.00, 150, 1, 'img/Small_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Small_Diaper.jpg')),
(15, 4, 14, 'UY-DP-S-24', 'Ultra-Soft Diapers - S (24 pcs)', 'Size S hypoallergenic diapers. Breathable and leak-proof.', 670.00, 150, 1, 'img/Small_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Small_Diaper.jpg')),
(16, 4, 14, 'UY-DP-S-36', 'Ultra-Soft Diapers - S (36 pcs)', 'Size S hypoallergenic diapers. Breathable and leak-proof.', 990.00, 150, 1, 'img/Small_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Small_Diaper.jpg')),
(17, 4, NULL, 'UY-DP-M-12', 'Ultra-Soft Diapers - M (12 pcs)', 'Size M hypoallergenic diapers. Breathable and leak-proof.', 350.00, 150, 1, 'img/Medium_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Medium_Diaper.jpg')),
(18, 4, 17, 'UY-DP-M-24', 'Ultra-Soft Diapers - M (24 pcs)', 'Size M hypoallergenic diapers. Breathable and leak-proof.', 670.00, 150, 1, 'img/Medium_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Medium_Diaper.jpg')),
(19, 4, 17, 'UY-DP-M-36', 'Ultra-Soft Diapers - M (36 pcs)', 'Size M hypoallergenic diapers. Breathable and leak-proof.', 990.00, 150, 1, 'img/Medium_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Medium_Diaper.jpg')),
(20, 4, NULL, 'UY-DP-L-12', 'Ultra-Soft Diapers - L (12 pcs)', 'Size L hypoallergenic diapers. Breathable and leak-proof.', 350.00, 150, 1, 'img/Large_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Large_Diaper.jpg')),
(21, 4, 20, 'UY-DP-L-24', 'Ultra-Soft Diapers - L (24 pcs)', 'Size L hypoallergenic diapers. Breathable and leak-proof.', 670.00, 150, 1, 'img/Large_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Large_Diaper.jpg')),
(22, 4, 20, 'UY-DP-L-36', 'Ultra-Soft Diapers - L (36 pcs)', 'Size L hypoallergenic diapers. Breathable and leak-proof.', 990.00, 150, 1, 'img/Large_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Large_Diaper.jpg')),
(23, 4, NULL, 'UY-DP-XL-12', 'Ultra-Soft Diapers - XL (12 pcs)', 'Size XL hypoallergenic diapers. Breathable and leak-proof.', 350.00, 150, 1, 'img/XL_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XL_Diaper.jpg')),
(24, 4, 23, 'UY-DP-XL-24', 'Ultra-Soft Diapers - XL (24 pcs)', 'Size XL hypoallergenic diapers. Breathable and leak-proof.', 670.00, 150, 1, 'img/XL_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XL_Diaper.jpg')),
(25, 4, 23, 'UY-DP-XL-36', 'Ultra-Soft Diapers - XL (36 pcs)', 'Size XL hypoallergenic diapers. Breathable and leak-proof.', 990.00, 150, 1, 'img/XL_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XL_Diaper.jpg')),
(26, 4, NULL, 'UY-DP-XXL-12', 'Ultra-Soft Diapers - XXL (12 pcs)', 'Size XXL hypoallergenic diapers. Breathable and leak-proof.', 350.00, 150, 1, 'img/XXL_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XXL_Diaper.jpg')),
(27, 4, 26, 'UY-DP-XXL-24', 'Ultra-Soft Diapers - XXL (24 pcs)', 'Size XXL hypoallergenic diapers. Breathable and leak-proof.', 670.00, 150, 1, 'img/XXL_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XXL_Diaper.jpg')),
(28, 4, 26, 'UY-DP-XXL-36', 'Ultra-Soft Diapers - XXL (36 pcs)', 'Size XXL hypoallergenic diapers. Breathable and leak-proof.', 990.00, 150, 1, 'img/XXL_Diaper.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/XXL_Diaper.jpg')),
(29, 1, NULL, 'UY-BS-BAR', 'Bath Soap Bar', 'High-quality Bath Soap Bar for delicate skin.', 180.00, 120, 1, 'img/Bath_Soap.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Bath_Soap.jpg')),
(30, 2, NULL, 'UY-CR-JAR', 'Soothing Chest Rub', 'High-quality Soothing Chest Rub for delicate skin.', 320.00, 120, 1, 'img/Chest_Rub.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Chest_Rub.jpg')),
(31, 2, NULL, 'UY-PJ-JAR', 'Pure Petroleum Jelly', 'High-quality Pure Petroleum Jelly for delicate skin.', 150.00, 120, 1, 'img/Petroleum_Jelly.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Petroleum_Jelly.jpg')),
(32, 2, NULL, 'UY-RS-SPR', 'Nappy Rash Spray', 'High-quality Nappy Rash Spray for delicate skin.', 450.00, 120, 1, 'img/Rash_Spray.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Rash_Spray.jpg')),
(33, 2, NULL, 'UY-NB-TBE', 'Organic Nipple Balm', 'High-quality Organic Nipple Balm for delicate skin.', 580.00, 120, 1, 'img/Nipple_Balm.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Nipple_Balm.jpg')),
(34, 2, NULL, 'UY-SG-TBE', 'Aloe Soothing Gel', 'High-quality Aloe Soothing Gel for delicate skin.', 290.00, 120, 1, 'img/Soothing_Gel.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Soothing_Gel.jpg')),
(35, 6, NULL, 'UY-CB-PK', 'Cotton Buds (200s)', 'High-quality Cotton Buds (200s) for delicate skin.', 140.00, 120, 1, 'img/Cotton_Buds.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Cotton_Buds.jpg')),
(36, 6, NULL, 'UY-FC-BTL', 'Fabric Conditioner (1L)', 'High-quality Fabric Conditioner (1L) for delicate skin.', 380.00, 120, 1, 'img/Fabric_Conditioner.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Fabric_Conditioner.jpg')),
(37, 4, NULL, 'UY-WP-PK', 'Baby Wipes (80 sheets)', 'High-quality Baby Wipes (80 sheets) for delicate skin.', 190.00, 120, 1, 'img/Wipes.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Wipes.jpg')),
(38, 3, NULL, 'UY-FS-SPF', 'Face Sunscreen SPF30', 'High-quality Face Sunscreen SPF30 for delicate skin.', 620.00, 120, 1, 'img/Face_Sunscreen.jpg', NOW(), NOW(), NULL, JSON_ARRAY('img/Face_Sunscreen.jpg'));

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) PRIMARY KEY AUTO_INCREMENT,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'admin', 'System administrator'),
(2, 'manager', 'Inventory and order manager'),
(3, 'customer', 'Regular customer');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `role` varchar(20) NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `address`, `photo`, `status`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@uyayi.com', NOW(), '$2y$10$FJEFd5Knm9BQyPee03vmbODYpNalDUME5BDkEiNWimaz2J3XOxapC', NULL, NULL, NULL, 'active', 'admin', NOW(), NOW()),
(2, 'Test Customer', 'customer@test.com', NOW(), '$2y$10$cRetgGTlzkb1UnB8JeF1mu.OHT60N9QVA8CTfeSlQzdj/HSEM60gi', NULL, NULL, NULL, 'active', 'customer', NOW(), NOW()),
(3, 'John Smith', 'john@example.com', NOW(), '$2y$10$cRetgGTlzkb1UnB8JeF1mu.OHT60N9QVA8CTfeSlQzdj/HSEM60gi', NULL, NULL, NULL, 'active', 'customer', NOW(), NOW());

--
-- Indexes for dumped tables
--
-- --------------------------------------------------------
--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
  (1, 'Bath Essentials', 1, NOW(), NOW()),
  (2, 'Diapering Care', 1, NOW(), NOW()),
  (3, 'Skin Care', 1, NOW(), NOW()),
  (4, 'Health & Hygiene', 1, NOW(), NOW()),
  (5, 'Sachet Variants', 1, NOW(), NOW()),
  (6, 'Household Essentials', 1, NOW(), NOW());

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_payments_user` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_products_visible` (`visible`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_parent` (`parent_id`);
--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_parent` FOREIGN KEY (`parent_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
