CREATE DATABASE IF NOT EXISTS kantinhub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE kantinhub;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS kantin;

CREATE TABLE kantin (
  id   INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  icon VARCHAR(20)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menu (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  kantin_id INT NOT NULL,
  nama      VARCHAR(100) NOT NULL,
  harga     INT NOT NULL,
  img       TEXT NOT NULL,
  FOREIGN KEY (kantin_id) REFERENCES kantin(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  kantin_id      INT NOT NULL,
  username       VARCHAR(50) NOT NULL,
  items          TEXT NOT NULL,
  total_price    INT NOT NULL,
  payment_method VARCHAR(20),
  note           TEXT,
  is_completed   TINYINT(1) NOT NULL DEFAULT 0,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kantin_id) REFERENCES kantin(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  username    VARCHAR(50) NOT NULL,
  review_text TEXT NOT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO kantin (id, nama, icon) VALUES
  (1, 'USMAN 1', '🫓'),
  (2, 'USMAN 2', '🍡'),
  (3, 'USMAN 3', '🍢'),
  (4, 'USMAN 4', '🥤'),
  (5, 'USMAN 7', '🍚'),
  (6, 'USMAN 8', '🍗');

INSERT INTO menu (kantin_id, nama, harga, img) VALUES
  (1, 'Pentol', 5000, 'https://images.unsplash.com/photo-1596649281278-6bd3914a1c6a?w=400'),
  (1, 'Bakpao', 2500, 'https://images.unsplash.com/photo-1627916531398-ff2f1cf1d2ce?w=400'),
  (1, 'Gorengan', 1000, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=400'),
  (2, 'Pentol', 5000, 'https://images.unsplash.com/photo-1596649281278-6bd3914a1c6a?w=400'),
  (2, 'Bakpao', 2500, 'https://images.unsplash.com/photo-1627916531398-ff2f1cf1d2ce?w=400'),
  (2, 'Gorengan', 1000, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=400'),
  (3, 'Tempura', 1000, 'https://images.unsplash.com/photo-1581184953963-d159747d1286?w=400'),
  (3, 'Gorengan', 1000, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=400'),
  (4, 'Es Sachet', 3000, 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400'),
  (4, 'Le Minerale', 3000, 'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=400'),
  (4, 'Nabati', 2000, 'https://images.unsplash.com/photo-1621939514649-280e2ee25f60?w=400'),
  (4, 'Pop Mie', 6000, 'https://images.unsplash.com/photo-1612929633738-8fe44f7ec841?w=400'),
  (4, 'Mie Gelas', 2500, 'https://images.unsplash.com/photo-1552611052-33e04de081de?w=400'),
  (5, 'Tempura', 1000, 'https://images.unsplash.com/photo-1581184953963-d159747d1286?w=400'),
  (5, 'Nasi Goreng', 1000, 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400'),
  (6, 'Nasi Geprek', 7000, 'https://images.unsplash.com/photo-1606080206121-aee43df79768?w=400'),
  (6, 'Donat', 3000, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400'),
  (6, 'Risol', 3000, 'https://images.unsplash.com/photo-1625938146369-adc83368bda7?w=400'),
  (6, 'Es Sirup', 3000, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=400');
