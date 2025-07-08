/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `pta` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `pta`;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Lưu mật khẩu đã được băm',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `admins` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
	(1, 'admin', 'admin@example.com', '$2y$10$GRzZEM8Wi8LboDlfhrAicOTeBOBppz6XRpAzDhkWQM9an6MQZcTgC', '2025-07-07 03:44:49');

CREATE TABLE IF NOT EXISTS `banners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'Thứ tự sắp xếp banner',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `banners` (`id`, `image_url`, `link_url`, `title`, `is_active`, `sort_order`, `created_at`) VALUES
	(2, 'uploads/banners/686cfdd74d86d-685037f7e73bf-banner.jpg', '', 'main', 1, 0, '2025-07-08 11:15:35'),
	(3, 'uploads/banners/686cfde492440-z6686047565387_cf79c9b4549925744985366983615610.jpg', '', 'ngon à', 1, 1, '2025-07-08 11:15:48'),
	(4, 'uploads/banners/686cfe00f11c5-z6686047555190_0855fc5d2b160050f2259b9808c0f11b.jpg', '', 'end', 1, 2, '2025-07-08 11:16:16');

CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đường dẫn tới logo thương hiệu',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `brands` (`id`, `name`, `slug`, `logo_url`, `created_at`, `updated_at`) VALUES
	(1, 'Apple', 'apple', 'uploads/brands/apple-logo.png', '2025-07-05 04:52:08', '2025-07-05 04:52:08'),
	(2, 'Samsung', 'samsung', 'uploads/brands/samsung-logo.png', '2025-07-05 04:52:08', '2025-07-05 04:52:08'),
	(3, 'Dell', 'dell', 'uploads/brands/dell-logo.png', '2025-07-05 04:52:08', '2025-07-05 04:52:08'),
	(4, 'Honda', 'honda', 'uploads/brands/honda-logo.png', '2025-07-05 04:52:08', '2025-07-05 04:52:08'),
	(5, 'Toyota', 'toyota', 'uploads/brands/toyota-logo.png', '2025-07-05 04:52:08', '2025-07-05 04:52:08');

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'Thứ tự sắp xếp danh mục',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `categories` (`id`, `name`, `slug`, `sort_order`, `created_at`, `updated_at`) VALUES
	(1, 'Điện thoại', 'dien-thoai', 0, '2025-07-05 04:52:08', '2025-07-08 12:14:15'),
	(2, 'Laptop', 'laptop', 1, '2025-07-05 04:52:08', '2025-07-08 12:14:15'),
	(3, 'Xe máy', 'xe-may', 2, '2025-07-05 04:52:08', '2025-07-08 11:52:59');

CREATE TABLE IF NOT EXISTS `category_brand` (
  `category_id` int NOT NULL,
  `brand_id` int NOT NULL,
  PRIMARY KEY (`category_id`,`brand_id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `category_brand_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `category_brand_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `category_brand` (`category_id`, `brand_id`) VALUES
	(1, 1),
	(2, 1),
	(1, 2),
	(2, 3),
	(3, 4),
	(3, 5);

CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả chi tiết',
  `specifications` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Thông số kỹ thuật (lưu dạng JSON)',
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đường dẫn ảnh đại diện sản phẩm',
  `category_id` int NOT NULL,
  `brand_id` int NOT NULL,
  `view_count` int NOT NULL DEFAULT '0',
  `price` decimal(12,0) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `products` (`id`, `name`, `slug`, `description`, `specifications`, `image_url`, `category_id`, `brand_id`, `view_count`, `price`, `created_at`, `updated_at`) VALUES
	(1, 'iPhone 15 Pro Max 256GB', 'iphone-15-pro-max-256gb', 'Mô tả chi tiết cho iPhone 15 Pro Max...', '{"Màn hình":"OLED 6.7 inch","Chip":"Apple A17 Pro","ngon":"rẻ vl"}', 'uploads/products/686cf89e7c927-z - Copy (2).png', 1, 1, 276, 32000000, '2025-07-06 06:14:28', '2025-07-08 10:53:44'),
	(2, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Mô tả chi tiết cho Samsung Galaxy S24 Ultra...', '{"Màn hình": "Dynamic AMOLED 2X 6.8 inch", "Chip": "Snapdragon 8 Gen 3"}', 'uploads/products/s24-ultra.jpg', 1, 2, 242, 29500000, '2025-07-06 06:14:28', '2025-07-07 09:10:04'),
	(3, 'iPhone 15 Plus 128GB', 'iphone-15-plus-128gb', 'Phiên bản Plus với màn hình lớn và thời lượng pin ấn tượng.', '{"Màn hình": "OLED 6.7 inch", "Chip": "Apple A16 Bionic"}', 'uploads/products/iphone-15-plus.jpg', 1, 1, 180, 24500000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(4, 'Samsung Galaxy Z Fold 5', 'samsung-galaxy-z-fold-5', 'Điện thoại gập cao cấp nhất từ Samsung.', '{"Màn hình chính": "7.6 inch Foldable Dynamic AMOLED 2X", "Chip": "Snapdragon 8 Gen 2"}', 'uploads/products/z-fold-5.jpg', 1, 2, 151, 40990000, '2025-07-06 06:14:28', '2025-07-08 09:54:39'),
	(5, 'iPhone 14 Pro 128GB', 'iphone-14-pro-128gb', 'Hiệu năng mạnh mẽ với chip A16 Bionic.', '{"Màn hình": "OLED 6.1 inch", "Chip": "Apple A16 Bionic"}', 'uploads/products/iphone-14-pro.jpg', 1, 1, 195, 23000000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(6, 'Samsung Galaxy S23 FE', 'samsung-galaxy-s23-fe', 'Phiên bản Fan Edition với nhiều tính năng cao cấp.', '{"Màn hình": "Dynamic AMOLED 2X 6.4 inch", "Chip": "Exynos 2200"}', 'uploads/products/s23-fe.jpg', 1, 2, 140, 13890000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(7, 'iPhone 13 128GB', 'iphone-13-128gb', 'Lựa chọn tuyệt vời với mức giá hợp lý.', '{"Màn hình": "OLED 6.1 inch", "Chip": "Apple A15 Bionic"}', 'uploads/products/iphone-13.jpg', 1, 1, 210, 15790000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(8, 'Samsung Galaxy A55', 'samsung-galaxy-a55', 'Điện thoại tầm trung đáng mua nhất.', '{"Màn hình": "Super AMOLED 6.6 inch", "Chip": "Exynos 1480"}', 'uploads/products/galaxy-a55.jpg', 1, 2, 166, 9690000, '2025-07-06 06:14:28', '2025-07-06 07:15:33'),
	(9, 'iPhone SE (2022)', 'iphone-se-2022', 'Nhỏ gọn, mạnh mẽ với chip A15 Bionic.', '{"Màn hình": "Retina IPS LCD 4.7 inch", "Chip": "Apple A15 Bionic"}', 'uploads/products/iphone-se.jpg', 1, 1, 130, 10990000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(10, 'MacBook Pro 14 inch M3', 'macbook-pro-14-inch-m3', 'Mô tả chi tiết cho MacBook Pro 14 inch M3...', '{"CPU": "Apple M3", "RAM": "8 GB", "Ổ cứng": "512 GB SSD"}', 'uploads/products/macbook-pro-m3.jpg', 2, 1, 190, 39990000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(11, 'Dell XPS 15 9530', 'dell-xps-15-9530', 'Laptop cao cấp cho công việc sáng tạo.', '{"CPU": "Intel Core i7-13700H", "RAM": "16 GB", "Ổ cứng": "512 GB SSD"}', 'uploads/products/dell-xps-15.jpg', 2, 3, 175, 45000000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(12, 'MacBook Air 13 inch M2', 'macbook-air-13-m2', 'Mỏng nhẹ, mạnh mẽ, thời lượng pin cả ngày.', '{"CPU": "Apple M2", "RAM": "8 GB", "Ổ cứng": "256 GB SSD"}', 'uploads/products/macbook-air-m2.jpg', 2, 1, 224, 26500000, '2025-07-06 06:14:28', '2025-07-08 10:10:22'),
	(13, 'Dell Inspiron 15 3520', 'dell-inspiron-15-3520', 'Laptop văn phòng phổ thông.', '{"CPU": "Intel Core i5-1235U", "RAM": "8 GB", "Ổ cứng": "256 GB SSD"}', 'uploads/products/dell-inspiron-15.jpg', 2, 3, 150, 14500000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(14, 'MacBook Pro 16 inch M3 Pro', 'macbook-pro-16-m3-pro', 'Sức mạnh đỉnh cao cho chuyên gia.', '{"CPU": "Apple M3 Pro", "RAM": "18 GB", "Ổ cứng": "512 GB SSD"}', 'uploads/products/macbook-pro-16.jpg', 2, 1, 160, 59990000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(15, 'Dell Vostro 3400', 'dell-vostro-3400', 'Bền bỉ, đáng tin cậy cho doanh nghiệp.', '{"CPU": "Intel Core i5-1135G7", "RAM": "8 GB", "Ổ cứng": "512 GB SSD"}', 'uploads/products/dell-vostro-3400.jpg', 2, 3, 135, 16200000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(16, 'MacBook Air 15 inch M2', 'macbook-air-15-m2', 'Màn hình lớn hơn, trải nghiệm tuyệt vời hơn.', '{"CPU": "Apple M2", "RAM": "8 GB", "Ổ cứng": "256 GB SSD"}', 'uploads/products/macbook-air-15.jpg', 2, 1, 185, 31000000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(17, 'Dell Latitude 7440', 'dell-latitude-7440', 'Laptop doanh nhân cao cấp, bảo mật.', '{"CPU": "Intel Core i7-1365U", "RAM": "16 GB", "Ổ cứng": "512 GB SSD"}', 'uploads/products/dell-latitude-7440.jpg', 2, 3, 120, 35000000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(18, 'iMac 24 inch M3', 'imac-24-m3', 'Máy tính All-in-One mạnh mẽ và đầy màu sắc.', '{"CPU": "Apple M3", "RAM": "8 GB", "Ổ cứng": "256 GB SSD"}', 'uploads/products/imac-24.jpg', 2, 1, 111, 33490000, '2025-07-06 06:14:28', '2025-07-06 14:29:44'),
	(19, 'Honda Vision 2024', 'honda-vision-2024', 'Mô tả chi tiết cho xe Honda Vision 2024...', '{"Động cơ":"eSP+ 4 van"}', 'uploads/products/686cf7821d2c1-z - Copy (2).png', 3, 4, 387, 32000000, '2025-07-06 06:14:28', '2025-07-08 12:14:00'),
	(20, 'Honda Air Blade 160', 'honda-air-blade-160', 'Thiết kế thể thao, động cơ mạnh mẽ.', '{"Động cơ": "eSP+ 4 van", "Dung tích": "160cc"}', 'uploads/products/air-blade-160.jpg', 3, 4, 323, 58000000, '2025-07-06 06:14:28', '2025-07-06 14:04:19'),
	(21, 'Toyota Vios G', 'toyota-vios-g', 'Mẫu sedan quốc dân, bền bỉ và tiết kiệm.', '{"Động cơ": "1.5L Dual VVT-i", "Hộp số": "CVT"}', 'uploads/products/toyota-vios.jpg', 3, 5, 280, 592000000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(22, 'Honda SH 160i', 'honda-sh-160i', 'Vua tay ga, đẳng cấp và sang trọng.', '{"Động cơ": "eSP+ 4 van", "Dung tích": "160cc"}', 'uploads/products/honda-sh.jpg', 3, 4, 300, 92000000, '2025-07-06 06:14:28', '2025-07-06 06:14:28'),
	(23, 'Toyota Corolla Cross 1.8V', 'toyota-corolla-cross-1-8v', 'SUV đô thị bán chạy nhất.', '{"Động cơ": "1.8L Dual VVT-i", "Hộp số": "CVT"}', 'uploads/products/corolla-cross.jpg', 3, 5, 263, 860000000, '2025-07-06 06:14:28', '2025-07-06 14:16:35'),
	(24, 'Honda Winner X', 'honda-winner-x', 'Xe côn tay thể thao, hiệu suất cao.', '{"Động cơ": "DOHC 150cc", "Hộp số": "6 cấp"}', 'uploads/products/winner-x.jpg', 3, 4, 291, 46000000, '2025-07-06 06:14:28', '2025-07-06 10:50:31'),
	(26, 'Honda Vario 160', 'honda-vario-160', 'Thiết kế góc cạnh, đậm chất thể thao.', '{"Động cơ": "eSP+ 4 van", "Dung tích": "160cc"}', 'uploads/products/vario-160.jpg', 3, 4, 275, 52000000, '2025-07-06 06:14:28', '2025-07-06 06:14:28');

CREATE TABLE IF NOT EXISTS `product_gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Văn bản thay thế cho ảnh (SEO)',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'Thứ tự sắp xếp ảnh',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_gallery_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

REPLACE INTO `product_gallery` (`id`, `product_id`, `image_url`, `alt_text`, `sort_order`, `created_at`) VALUES
	(8, 1, 'uploads/products/686cf89e7c927-z - Copy (2).png', NULL, 0, '2025-07-08 10:53:18'),
	(9, 1, 'uploads/products/686cf89e7cee8-z - Copy.png', NULL, 0, '2025-07-08 10:53:18'),
	(10, 1, 'uploads/products/686cf89e7d49c-z.png', NULL, 0, '2025-07-08 10:53:18');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
