-- =========================================================
-- COCO Perfume Store - Database Schema (fixed for phpMyAdmin)
-- =========================================================

SET NAMES utf8mb4;

-- If your phpMyAdmin user does NOT have permission to create databases,
-- comment out the next two lines and just select/create "coco_db"
-- manually from the phpMyAdmin left sidebar first, then run the rest.
CREATE DATABASE IF NOT EXISTS coco_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE coco_db;

-- ---------------------------------------------------------
-- Users
-- ---------------------------------------------------------
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Products
-- ---------------------------------------------------------
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand VARCHAR(60) DEFAULT 'COCO',
  name VARCHAR(120) NOT NULL,
  category VARCHAR(30) NOT NULL,
  notes VARCHAR(255) DEFAULT '',
  size VARCHAR(20) DEFAULT '100ml',
  price INT NOT NULL,
  original_price INT NOT NULL,
  discount INT DEFAULT 0,
  stock INT DEFAULT 10,
  rating DECIMAL(2,1) DEFAULT 4.5,
  image VARCHAR(255) NOT NULL,
  badge VARCHAR(40) DEFAULT '',
  delivery_estimate VARCHAR(60) DEFAULT '3-5 Business Days',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Cart
-- ---------------------------------------------------------
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(128) NOT NULL,
  user_id INT DEFAULT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Orders
-- ---------------------------------------------------------
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(30) NOT NULL UNIQUE,
  user_id INT DEFAULT NULL,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  country VARCHAR(60) DEFAULT 'India',
  state VARCHAR(60) NOT NULL,
  city VARCHAR(60) NOT NULL,
  address VARCHAR(255) NOT NULL,
  pin_code VARCHAR(10) NOT NULL,
  payment_method VARCHAR(30) NOT NULL,
  subtotal INT NOT NULL,
  discount INT DEFAULT 0,
  gst INT NOT NULL,
  shipping INT DEFAULT 0,
  grand_total INT NOT NULL,
  status VARCHAR(20) DEFAULT 'Confirmed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  name VARCHAR(120) NOT NULL,
  price INT NOT NULL,
  quantity INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Seed Products (special character in "Chanel N°5" replaced
-- with "No.5" to avoid import charset errors)
-- ---------------------------------------------------------
INSERT INTO products (brand, name, category, notes, size, price, original_price, discount, stock, rating, image, badge, delivery_estimate) VALUES
('COCO', 'COCO Noir',            'noir',    'Grapefruit, rose, oud, and amber',              '100ml', 1200, 1800, 33, 6,  5.0, 'assets/product_black.jpg', 'Best Seller', '3-5 Business Days'),
('COCO', 'COCO Gold',            'classic', 'Amber wood, vanilla, jasmine, and gold musk',    '100ml', 1400, 2000, 30, 12, 4.8, 'assets/product_gold.jpg',  'Classic',     '3-5 Business Days'),
('COCO', 'Chanel No.5',          'classic', 'Aldehydes, ylang-ylang, jasmine, and sandalwood','100ml', 1350, 1800, 25, 10, 4.9, 'assets/suggested_n5.jpg',  'Iconic',      '3-5 Business Days'),
('COCO', 'Coco Mademoiselle',    'floral',  'Orange, jasmine, rose, and patchouli',           '100ml', 1100, 1500, 27, 9,  4.7, 'assets/suggested_mademoiselle.jpg', 'Elegant', '3-5 Business Days'),
('COCO', 'COCO Vert',            'fresh',   'Bergamot, green tea, white musk, and vetiver',   '100ml', 2000, 2600, 23, 18, 4.5, 'assets/product_vert.jpg',  'New',         '3-5 Business Days'),
('COCO', 'COCO Bois',            'woody',   'Sandalwood, cedar, cardamom, and leather',       '100ml', 1200, 1700, 29, 5,  4.9, 'assets/product_bois.jpg',  'Exclusive',   '3-5 Business Days'),
('COCO', 'Midnight Oud',         'noir',    'Oud, saffron, and dark amber',                   '100ml', 5999, 8570, 30, 6,  5.0, 'assets/product_black.jpg', 'Best Seller', 'Delivery by Jul 25'),
('COCO', 'Royal Amber',          'classic', 'Amber, vanilla, and warm musk',                  '50ml',  4499, 5999, 25, 12, 4.8, 'assets/product_gold.jpg',  'Classic',     'Delivery by Jul 26'),
('COCO', 'Velvet Rose',          'floral',  'Rose petals, peony, and soft musk',              '100ml', 6999, 9999, 30, 3,  4.9, 'assets/product_bois.jpg',  'Exclusive',   'Delivery by Jul 24'),
('COCO', 'Ocean Breeze',         'fresh',   'Sea salt, citrus, and driftwood',                '30ml',  2499, 3499, 28, 18, 4.5, 'assets/product_vert.jpg',  'New',         'Delivery by Jul 27'),
('COCO', 'White Musk',           'classic', 'White musk, iris, and soft vanilla',             '50ml',  3999, 5499, 27, 8,  4.7, 'assets/product_black.jpg', 'Popular',     'Delivery by Jul 25'),
('COCO', 'Noir Intense',         'noir',    'Black pepper, leather, and dark oud',            '100ml', 8999, 11999,25, 4,  5.0, 'assets/product_bois.jpg',  'Limited',     'Delivery by Jul 23');
