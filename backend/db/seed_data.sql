USE aminashopdz;

-- Insert categories
INSERT INTO categories (name) VALUES
('hommes'),
('femmes'),
('enfants');

-- Insert sample products
INSERT INTO products (name, description, price, category_id, stock, promotion, date_added, images, sizes, colors) VALUES
('T-shirt Bleu', 'T-shirt confortable en coton bleu.', 1500.00, (SELECT id FROM categories WHERE name = 'hommes'), 50, FALSE, '2024-04-01',
  '["https://images.pexels.com/photos/428340/pexels-photo-428340.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=200"]',
  '["S","M","L","XL"]',
  '["Bleu"]'),
('Robe élégante', 'Robe élégante pour femmes.', 3500.00, (SELECT id FROM categories WHERE name = 'femmes'), 30, TRUE, '2024-05-10',
  '["https://images.pexels.com/photos/2983464/pexels-photo-2983464.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=200"]',
  '["S","M","L"]',
  '["Rouge","Noir"]'),
('Jean décontracté', 'Jean décontracté pour hommes.', 2500.00, (SELECT id FROM categories WHERE name = 'hommes'), 40, FALSE, '2024-03-15',
  '["https://images.pexels.com/photos/2983463/pexels-photo-2983463.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=200"]',
  '["M","L","XL"]',
  '["Bleu"]'),
('Veste d\'hiver', 'Veste chaude pour l\'hiver.', 5000.00, (SELECT id FROM categories WHERE name = 'hommes'), 20, TRUE, '2024-05-20',
  '["https://images.pexels.com/photos/428338/pexels-photo-428338.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=200"]',
  '["L","XL"]',
  '["Noir"]'),
('Jupe fleurie', 'Jupe fleurie pour femmes.', 2000.00, (SELECT id FROM categories WHERE name = 'femmes'), 25, FALSE, '2024-04-25',
  '["https://images.pexels.com/photos/2983465/pexels-photo-2983465.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=200"]',
  '["S","M"]',
  '["Multicolore"]'),
('Pull en laine', 'Pull en laine chaud.', 3000.00, (SELECT id FROM categories WHERE name = 'hommes'), 35, FALSE, '2024-05-05',
  '["https://images.pexels.com/photos/428339/pexels-photo-428339.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=200"]',
  '["M","L"]',
  '["Gris"]'),
('T-shirt enfant', 'T-shirt confortable pour enfants.', 1200.00, (SELECT id FROM categories WHERE name = 'enfants'), 60, TRUE, '2024-05-15',
  '["https://images.pexels.com/photos/1704122/pexels-photo-1704122.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=200"]',
  '["XS","S"]',
  '["Bleu","Rouge"]'),
('Chaussures sport', 'Chaussures de sport confortables.', 4000.00, (SELECT id FROM categories WHERE name = 'hommes'), 45, FALSE, '2024-04-10',
  '["https://images.pexels.com/photos/19090/pexels-photo.jpg?auto=compress&cs=tinysrgb&dpr=2&h=200"]',
  '["40","41","42","43"]',
  '["Noir","Blanc"]');
