const express = require('express');
const router = express.Router();

// Simple in-memory cart store per session (for demonstration)
const session = require('express-session');

// Middleware to initialize cart in session
router.use((req, res, next) => {
  if (!req.session.cart) {
    req.session.cart = [];
  }
  next();
});

// Helper function to find cart item index
function findCartItemIndex(cart, id, size = null, color = null) {
  return cart.findIndex(item => {
    if (item.id !== id) return false;
    if (size !== null && item.size !== size) return false;
    if (color !== null && item.color !== color) return false;
    return true;
  });
}

// GET /api/cart - get cart items
router.get('/', (req, res) => {
  res.json(req.session.cart);
});

// POST /api/cart - add or update cart item
router.post('/', (req, res) => {
  const { id, quantity, size, color } = req.body;
  if (!id || !quantity) {
    return res.status(400).json({ error: 'ID et quantité sont requis' });
  }
  const qty = Math.max(1, parseInt(quantity));
  const cart = req.session.cart;
  const index = findCartItemIndex(cart, id, size, color);
  if (index >= 0) {
    cart[index].quantity = qty;
    if (size !== undefined) cart[index].size = size;
    if (color !== undefined) cart[index].color = color;
  } else {
    const newItem = { id, quantity: qty };
    if (size !== undefined) newItem.size = size;
    if (color !== undefined) newItem.color = color;
    cart.push(newItem);
  }
  req.session.cart = cart;
  res.json({ success: true });
});

// DELETE /api/cart - remove cart item
router.delete('/', (req, res) => {
  const { id, size, color } = req.body;
  if (!id) {
    return res.status(400).json({ error: 'ID est requis' });
  }
  const cart = req.session.cart;
  const index = findCartItemIndex(cart, id, size, color);
  if (index >= 0) {
    cart.splice(index, 1);
    req.session.cart = cart;
    res.json({ success: true });
  } else {
    res.status(404).json({ error: 'Article non trouvé dans le panier' });
  }
});

module.exports = router;
