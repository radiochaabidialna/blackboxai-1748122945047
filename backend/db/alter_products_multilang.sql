ALTER TABLE products
  ADD COLUMN name_fr VARCHAR(255) NOT NULL AFTER id,
  ADD COLUMN name_ar VARCHAR(255) NOT NULL AFTER name_fr,
  ADD COLUMN description_fr TEXT AFTER description,
  ADD COLUMN description_ar TEXT AFTER description_fr,
  ADD COLUMN new BOOLEAN DEFAULT FALSE AFTER promotion;
