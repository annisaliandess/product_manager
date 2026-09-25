CREATE DATABASE IF NOT EXISTS product_manager
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE product_manager;

DROP TABLE IF EXISTS products;

CREATE TABLE products (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100) NOT NULL,
  category    VARCHAR(50)  NOT NULL,
  price       DECIMAL(12,2) NOT NULL,
  stock       INT UNSIGNED NOT NULL DEFAULT 0,
  image       VARCHAR(255) DEFAULT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_name (name)
) ENGINE=InnoDB;

INSERT INTO products (name, category, price, stock) VALUES
  ('Semen Tiga Roda 50kg',       'Semen & Beton',     62000,  150),
  ('Besi Beton 10mm 12m',        'Besi & Baja',       85000,  200),
  ('Triplex 3mm 122x244',        'Kayu & Papan',      45000,   75),
  ('Cat Dulux Weathershield 2.5L','Cat & Finishing',  195000,   40),
  ('Pipa PVC AW 3/4 inch 4m',    'Pipa & Sanitasi',   28000,  300),
  ('Kabel NYM 2x1.5mm 50m',      'Listrik & Kabel',  320000,   25),
  ('Genteng Metal Pasir',         'Atap & Genteng',    42000,  500),
  ('Palu Kambing 500gr',          'Peralatan Tangan',  55000,   60),
  ('Pasir Cor Truk Kecil',        'Pasir & Batu',     450000,   10),
  ('Engsel Pintu 4 inch',         'Pintu & Jendela',   18000,  120);
