const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const port = process.env.PORT || 3001;

app.use(cors());
app.use(bodyParser.json());

// Import routes
const productsRoutes = require('./routes/products');
const categoriesRoutes = require('./routes/categories');

app.use('/api/products', productsRoutes);
app.use('/api/categories', categoriesRoutes);

app.get('/', (req, res) => {
  res.send('Amina Shop DZ Backend API');
});

app.listen(port, () => {
  console.log(`Server running on port ${port}`);
});
