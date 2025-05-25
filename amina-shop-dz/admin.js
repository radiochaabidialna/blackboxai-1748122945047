document.addEventListener('DOMContentLoaded', () => {
  const sectionProducts = document.getElementById('section-products');
  const sectionCategories = document.getElementById('section-categories');
  const sectionOrders = document.getElementById('section-orders');

  const navProducts = document.getElementById('nav-products');
  const navCategories = document.getElementById('nav-categories');
  const navOrders = document.getElementById('nav-orders');

  function hideAllSections() {
    sectionProducts.classList.add('hidden');
    sectionCategories.classList.add('hidden');
    sectionOrders.classList.add('hidden');
  }

  function activateNavButton(button) {
    navProducts.classList.remove('bg-gray-700');
    navCategories.classList.remove('bg-gray-700');
    navOrders.classList.remove('bg-gray-700');
    button.classList.add('bg-gray-700');
  }

  navProducts.addEventListener('click', () => {
    hideAllSections();
    sectionProducts.classList.remove('hidden');
    activateNavButton(navProducts);
    fetchAndRenderProducts();
    fetchAndRenderCategories();
  });

  navCategories.addEventListener('click', () => {
    hideAllSections();
    sectionCategories.classList.remove('hidden');
    activateNavButton(navCategories);
    fetchAndRenderCategories();
  });

  navOrders.addEventListener('click', () => {
    hideAllSections();
    sectionOrders.classList.remove('hidden');
    activateNavButton(navOrders);
    fetchAndRenderOrders();
  });

  // Initialize with products section active
  navProducts.click();

  // The rest of the code for products, categories, orders management remains the same as before
  // Fetch and render products
  async function fetchProducts() {
    try {
      const res = await fetch('backend/api/products.php');
      const products = await res.json();
      return products;
    } catch (error) {
      console.error('Failed to fetch products:', error);
      return [];
    }
  }

  async function fetchAndRenderProducts() {
    const products = await fetchProducts();
    const productsList = document.getElementById('products-list');
    productsList.innerHTML = '';
    products.forEach(product => {
      const productName = product.name_fr;
      const productDescription = product.description_fr;
      const productElem = document.createElement('div');
      productElem.className = 'bg-white rounded-lg shadow-md p-4 flex justify-between items-center';

      productElem.innerHTML = `
        <div>
          <h3 class="font-semibold text-lg">${productName}</h3>
          <p>${productDescription}</p>
          <p class="text-blue-700 font-bold">${product.price} DZD</p>
        </div>
        <div class="space-x-2">
          <button data-id="${product.id}" class="edit-btn bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition">Modifier</button>
          <button data-id="${product.id}" class="delete-btn bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Supprimer</button>
        </div>
      `;
      productsList.appendChild(productElem);
    });
    attachProductEventListeners();
  }

  function attachProductEventListeners() {
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        const products = await fetchProducts();
        const product = products.find(p => p.id == id);
        if (product) {
          document.getElementById('product-id').value = product.id;
          document.getElementById('name_fr').value = product.name_fr;
          document.getElementById('name_ar').value = product.name_ar;
          document.getElementById('description_fr').value = product.description_fr;
          document.getElementById('description_ar').value = product.description_ar;
          document.getElementById('price').value = product.price;
          document.getElementById('category_id').value = product.category_id;
          document.getElementById('stock').value = product.stock;
          document.getElementById('promotion').checked = product.promotion == 1;
          document.getElementById('new').checked = product.new == 1;
          document.getElementById('images').value = product.images ? product.images.join(',') : '';
          document.getElementById('sizes').value = product.sizes ? product.sizes.join(',') : '';
          document.getElementById('colors').value = product.colors ? product.colors.join(',') : '';
        }
      });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        if (confirm('Voulez-vous vraiment supprimer ce produit ?')) {
          try {
            const res = await fetch(`backend/api/products.php?id=${id}`, {
              method: 'DELETE'
            });
            const data = await res.json();
            if (data.success) {
              fetchAndRenderProducts();
            } else {
              alert('Erreur lors de la suppression');
            }
          } catch (error) {
            alert('Erreur lors de la suppression');
            console.error(error);
          }
        }
      });
    });
  }

  document.getElementById('product-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('product-id').value;
    const name_fr = document.getElementById('name_fr').value;
    const name_ar = document.getElementById('name_ar').value;
    const description_fr = document.getElementById('description_fr').value;
    const description_ar = document.getElementById('description_ar').value;
    const price = parseFloat(document.getElementById('price').value);
    const category_id = parseInt(document.getElementById('category_id').value);
    const stock = parseInt(document.getElementById('stock').value);
    const promotion = document.getElementById('promotion').checked;
    const newProduct = document.getElementById('new').checked;
    const images = document.getElementById('images').value.split(',').map(s => s.trim()).filter(s => s);
    const sizes = document.getElementById('sizes').value.split(',').map(s => s.trim()).filter(s => s);
    const colors = document.getElementById('colors').value.split(',').map(s => s.trim()).filter(s => s);

    const productData = {
      name_fr,
      name_ar,
      description_fr,
      description_ar,
      price,
      category_id,
      stock,
      promotion,
      new: newProduct,
      images,
      sizes,
      colors
    };

    try {
      let res;
      if (id) {
        productData.id = parseInt(id);
        res = await fetch('backend/api/products.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(productData)
        });
      } else {
        res = await fetch('backend/api/products.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(productData)
        });
      }
      const data = await res.json();
      if (data.success) {
        alert('Produit enregistré avec succès');
        document.getElementById('product-form').reset();
        document.getElementById('product-id').value = '';
        fetchAndRenderProducts();
      } else {
        alert('Erreur lors de l\'enregistrement');
      }
    } catch (error) {
      alert('Erreur lors de l\'enregistrement');
      console.error(error);
    }
  });

  document.getElementById('reset-btn').addEventListener('click', () => {
    document.getElementById('product-form').reset();
    document.getElementById('product-id').value = '';
  });

  // Categories management
  async function fetchCategories() {
    try {
      const res = await fetch('backend/api/categories.php');
      const categories = await res.json();
      return categories;
    } catch (error) {
      console.error('Failed to fetch categories:', error);
      return [];
    }
  }

  async function fetchAndRenderCategories() {
    const categories = await fetchCategories();
    const categorySelect = document.getElementById('category_id');
    categorySelect.innerHTML = '';
    categories.forEach(cat => {
      const option = document.createElement('option');
      option.value = cat.id;
      option.textContent = cat.name;
      categorySelect.appendChild(option);
    });

    const categoriesList = document.getElementById('categories-list');
    categoriesList.innerHTML = '';
    categories.forEach(cat => {
      const div = document.createElement('div');
      div.className = 'flex justify-between items-center p-2 border rounded';
      div.innerHTML = `
        <span>${cat.name}</span>
        <div class="space-x-2">
          <button data-id="${cat.id}" class="edit-category bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600">Modifier</button>
          <button data-id="${cat.id}" class="delete-category bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Supprimer</button>
        </div>
      `;
      categoriesList.appendChild(div);
    });
    attachCategoryEventListeners();
  }

  function attachCategoryEventListeners() {
    document.querySelectorAll('.edit-category').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        const categories = await fetchCategories();
        const category = categories.find(c => c.id == id);
        if (category) {
          document.getElementById('category-id').value = category.id;
          document.getElementById('category-name').value = category.name;
          navCategories.click();
        }
      });
    });

    document.querySelectorAll('.delete-category').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        if (confirm('Voulez-vous vraiment supprimer cette catégorie ?')) {
          try {
            const res = await fetch(`backend/api/categories.php?id=${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.success) {
              fetchAndRenderCategories();
            } else {
              alert('Erreur lors de la suppression');
            }
          } catch (error) {
            alert('Erreur lors de la suppression');
            console.error(error);
          }
        }
      });
    });
  }

  document.getElementById('category-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('category-id').value;
    const name = document.getElementById('category-name').value.trim();
    if (!name) {
      alert('Le nom de la catégorie est requis');
      return;
    }
    try {
      let res;
      if (id) {
        res = await fetch('backend/api/categories.php', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: parseInt(id), name })
        });
      } else {
        res = await fetch('backend/api/categories.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name })
        });
      }
      const data = await res.json();
      if (data.success) {
        alert('Catégorie enregistrée avec succès');
        document.getElementById('category-form').reset();
        fetchAndRenderCategories();
      } else {
        alert('Erreur lors de l\'enregistrement');
      }
    } catch (error) {
      alert('Erreur lors de l\'enregistrement');
      console.error(error);
    }
  });

  document.getElementById('reset-category-btn').addEventListener('click', () => {
    document.getElementById('category-form').reset();
  });

  // Orders management
  async function fetchOrders() {
    try {
      const res = await fetch('backend/api/orders.php');
      const orders = await res.json();
      return orders;
    } catch (error) {
      console.error('Failed to fetch orders:', error);
      return [];
    }
  }

  async function fetchAndRenderOrders() {
    const orders = await fetchOrders();
    const ordersList = document.getElementById('orders-list');
    ordersList.innerHTML = '';
    orders.forEach(order => {
      const div = document.createElement('div');
      div.className = 'border rounded p-4';
      div.innerHTML = `
        <p><strong>ID:</strong> ${order.id}</p>
        <p><strong>Date:</strong> ${order.date}</p>
        <p><strong>Status:</strong> ${order.status}</p>
        <p><strong>Client:</strong> ${order.customer ? order.customer.name : ''}</p>
        <p><strong>Produits:</strong></p>
        <ul class="list-disc list-inside">
          ${order.cart ? order.cart.map(item => `<li>${item.quantity} x ${item.size} ${item.color} (ID produit: ${item.id})</li>`).join('') : ''}
        </ul>
        <div class="mt-2 space-x-2">
          <button data-id="${order.id}" class="update-order-status bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Changer statut</button>
          <button data-id="${order.id}" class="delete-order bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Supprimer</button>
        </div>
      `;
      ordersList.appendChild(div);
    });
    attachOrderEventListeners();
  }

  function attachOrderEventListeners() {
    document.querySelectorAll('.update-order-status').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        const newStatus = prompt('Entrez le nouveau statut (pending, shipped, delivered, cancelled):');
        if (newStatus) {
          try {
            const res = await fetch('backend/api/orders.php', {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ id: parseInt(id), status: newStatus })
            });
            const data = await res.json();
            if (data.success) {
              alert('Statut mis à jour');
              fetchAndRenderOrders();
            } else {
              alert('Erreur lors de la mise à jour');
            }
          } catch (error) {
            alert('Erreur lors de la mise à jour');
            console.error(error);
          }
        }
      });
    });

    document.querySelectorAll('.delete-order').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const id = e.target.getAttribute('data-id');
        if (confirm('Voulez-vous vraiment supprimer cette commande ?')) {
          try {
            const res = await fetch(`backend/api/orders.php?id=${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.success) {
              fetchAndRenderOrders();
            } else {
              alert('Erreur lors de la suppression');
            }
          } catch (error) {
            alert('Erreur lors de la suppression');
            console.error(error);
          }
        }
      });
    });
  }
});
