-- Drop existing tables if they exist
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

-- Create categories table
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_fr VARCHAR(255) NOT NULL,
  name_ar VARCHAR(255) NOT NULL
);

-- Create products table
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_fr VARCHAR(255) NOT NULL,
  name_ar VARCHAR(255) NOT NULL,
  description_fr TEXT,
  description_ar TEXT,
  price DECIMAL(10,2) NOT NULL,
  category_id INT NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  promotion BOOLEAN NOT NULL DEFAULT FALSE,
  new BOOLEAN NOT NULL DEFAULT FALSE,
  images TEXT, -- JSON encoded array of image URLs
  sizes TEXT, -- JSON encoded array of sizes
  colors TEXT, -- JSON encoded array of colors
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Create orders table
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cart TEXT NOT NULL, -- JSON encoded array of cart items
  customer TEXT NOT NULL, -- JSON encoded customer info
  date DATETIME NOT NULL,
  status ENUM('pending', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'
);

-- Insert initial categories
INSERT INTO categories (name_fr, name_ar) VALUES
('Vêtements', 'ملابس'),
('Chaussures', 'أحذية'),
('Accessoires', 'إكسسوارات');

-- Insert initial products
INSERT INTO products (name_fr, name_ar, description_fr, description_ar, price, category_id, stock, promotion, new, images, sizes, colors) VALUES
('T-shirt Homme', 'تي شيرت رجالي', 'T-shirt confortable en coton', 'تي شيرت مريح من القطن', 1500.00, 1, 100, FALSE, TRUE, '["https://example.com/images/tshirt1.jpg"]', '["S","M","L","XL"]', '["rouge","bleu","noir"]'),
('Chaussures de sport', 'أحذية رياضية', 'Chaussures légères pour le sport', 'أحذية خفيفة للرياضة', 3500.00, 2, 50, TRUE, FALSE, '["https://example.com/images/shoes1.jpg"]', '["40","41","42","43"]', '["blanc","noir"]'),
('Casquette', 'قبعة', 'Casquette stylée pour l\'été', 'قبعة أنيقة للصيف', 800.00, 3, 200, FALSE, FALSE, '["https://example.com/images/cap1.jpg"]', '[]', '["noir","bleu"]');
