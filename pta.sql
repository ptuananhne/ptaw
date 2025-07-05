-- CSDL: pta
-- Bảng mã: UTF8MB4 COLLATE utf8mb4_unicode_ci
-- Tác giả: Gemini
-- Ngày tạo: 2024-07-20

-- Xóa cơ sở dữ liệu nếu đã tồn tại để tránh lỗi và tạo mới
DROP DATABASE IF EXISTS `pta`;

-- Tạo cơ sở dữ liệu `pta` với bảng mã utf8mb4 để hỗ trợ tiếng Việt tốt nhất
CREATE DATABASE `pta` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng cơ sở dữ liệu `pta` vừa tạo
USE `pta`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho `admins` (Quản trị viên)
-- Bảng này lưu thông tin đăng nhập vào trang quản trị.
--
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Lưu mật khẩu đã được băm',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đổ dữ liệu mẫu cho bảng `admins`
-- Mật khẩu mặc định là "admin123"
--
INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$IflpBvY8sF.T3jT8EwO.A.Vqg9zP7kX5C.L6mJ2.N.U8eW4oG3o.q'); -- Mật khẩu là admin123

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho `categories` (Danh mục sản phẩm)
-- Lưu trữ các danh mục chính như Điện thoại, Laptop, Xe máy.
--
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đổ dữ liệu mẫu cho bảng `categories`
--
INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Điện thoại', 'dien-thoai'),
(2, 'Laptop', 'laptop'),
(3, 'Xe máy', 'xe-may'),
(4, 'Tivi', 'tivi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho `brands` (Thương hiệu)
-- Lưu trữ các thương hiệu như Apple, Samsung, Toyota.
--
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đường dẫn tới logo thương hiệu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đổ dữ liệu mẫu cho bảng `brands`
--
INSERT INTO `brands` (`id`, `name`, `slug`, `logo_url`) VALUES
(1, 'Apple', 'apple', 'uploads/brands/apple-logo.png'),
(2, 'Samsung', 'samsung', 'uploads/brands/samsung-logo.png'),
(3, 'Dell', 'dell', 'uploads/brands/dell-logo.png'),
(4, 'Honda', 'honda', 'uploads/brands/honda-logo.png'),
(5, 'Toyota', 'toyota', 'uploads/brands/toyota-logo.png'),
(6, 'Sony', 'sony', 'uploads/brands/sony-logo.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho `category_brand` (Bảng nối Nhiều-Nhiều)
-- Quản lý thương hiệu nào thuộc danh mục nào.
--
CREATE TABLE `category_brand` (
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`,`brand_id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `category_brand_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `category_brand_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đổ dữ liệu mẫu cho bảng `category_brand`
--
INSERT INTO `category_brand` (`category_id`, `brand_id`) VALUES
(1, 1), -- Điện thoại - Apple
(1, 2), -- Điện thoại - Samsung
(2, 1), -- Laptop - Apple
(2, 3), -- Laptop - Dell
(3, 4), -- Xe máy - Honda
(3, 5), -- Xe máy - Toyota
(4, 2), -- Tivi - Samsung
(4, 6); -- Tivi - Sony

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho `products` (Sản phẩm)
-- Bảng quan trọng nhất, lưu trữ thông tin chi tiết của từng sản phẩm.
--
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `specifications` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thông số kỹ thuật (lưu dạng JSON)',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Đường dẫn ảnh đại diện sản phẩm',
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đổ dữ liệu mẫu cho bảng `products`
--
INSERT INTO `products` (`id`, `name`, `slug`, `description`, `specifications`, `image_url`, `category_id`, `brand_id`) VALUES
(1, 'iPhone 15 Pro Max 256GB', 'iphone-15-pro-max-256gb', 'Mô tả chi tiết cho iPhone 15 Pro Max...', '{\"Màn hình\": \"OLED 6.7 inch Super Retina XDR\", \"Chip\": \"Apple A17 Pro\", \"RAM\": \"8 GB\", \"Dung lượng\": \"256 GB\"}', 'uploads/products/iphone-15.jpg', 1, 1),
(2, 'MacBook Pro 14 inch M3', 'macbook-pro-14-inch-m3', 'Mô tả chi tiết cho MacBook Pro 14 inch M3...', '{\"CPU\": \"Apple M3\", \"RAM\": \"8 GB\", \"Ổ cứng\": \"512 GB SSD\", \"Màn hình\": \"14.2 inch Liquid Retina XDR\"}', 'uploads/products/macbook-pro-m3.jpg', 2, 1),
(3, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Mô tả chi tiết cho Samsung Galaxy S24 Ultra...', '{\"Màn hình\": \"Dynamic AMOLED 2X 6.8 inch\", \"Chip\": \"Snapdragon 8 Gen 3 for Galaxy\", \"RAM\": \"12 GB\", \"Dung lượng\": \"256 GB\"}', 'uploads/products/s24-ultra.jpg', 1, 2),
(4, 'Honda Vision 2024', 'honda-vision-2024', 'Mô tả chi tiết cho xe Honda Vision 2024...', '{\"Động cơ\": \"eSP+ 4 van\", \"Dung tích xy lanh\": \"110cc\", \"Hộp số\": \"Tự động, vô cấp\"}', 'uploads/products/honda-vision.jpg', 3, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho `product_gallery` (Thư viện ảnh sản phẩm)
-- Bảng này cho phép mỗi sản phẩm có nhiều ảnh (thư viện ảnh).
--
CREATE TABLE `product_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Văn bản thay thế cho ảnh (SEO)',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự sắp xếp ảnh',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_gallery_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đổ dữ liệu mẫu cho bảng `product_gallery`
-- Thêm một vài ảnh cho sản phẩm iPhone 15 (product_id = 1)
--
INSERT INTO `product_gallery` (`product_id`, `image_url`, `alt_text`, `sort_order`) VALUES
(1, 'uploads/products/iphone-15-gallery-1.jpg', 'iPhone 15 Pro Max - Mặt trước', 1),
(1, 'uploads/products/iphone-15-gallery-2.jpg', 'iPhone 15 Pro Max - Cụm camera', 2),
(1, 'uploads/products/iphone-15-gallery-3.jpg', 'iPhone 15 Pro Max - Cạnh viền Titan', 3);

