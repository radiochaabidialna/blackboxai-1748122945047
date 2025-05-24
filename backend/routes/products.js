const express = require('express');
const router = express.Router();
const db = require('../db/connection');

// Get all products
router.get('/', async (req, res) => {
  try {
    const [rows] = await db.query('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id');
    // Parse JSON fields
    const products = rows.map(product => ({
      ...product,
      images: product.images ? JSON.parse(product.images) : [],
      sizes: product.sizes ? JSON.parse(product.sizes) : [],
      colors: product.colors ? JSON.parse(product.colors) : []
    }));
    res.json(products);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Database error' });
  }
});

// Get product by id
router.get('/:id', async (req, res) => {
  const id = req.params.id;
  try {
    const [rows] = await db.query('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?', [id]);
    if (rows.length === 0) {
      return res.status(404).json({ error: 'Product not found' });
    }
    const product = rows[0];
    product.images = product.images ? JSON.parse(product.images) : [];
    product.sizes = product.sizes ? JSON.parse(product.sizes) : [];
    product.colors = product.colors ? JSON.parse(product.colors) : [];
    res.json(product);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Database error' });
  }
});

module.exports = router;
