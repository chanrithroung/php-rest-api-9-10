// API configuration
const API_URL = 'http://localhost/myphp/api.php';

// Products array (will be populated from API)
let products = [];
let editingProductId = null;

// Pagination variables
let currentPage = 1;
const itemsPerPage = 10;

// Function to format price as currency
function formatPrice(price) {
    return '$' + parseFloat(price).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Function to format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });
}

// Function to create SVG icons
function createIcon(iconName, size = 16) {
    const icons = {
        edit: `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="m18.5 2.5 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
        </svg>`,
        delete: `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3,6 5,6 21,6"></polyline>
            <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>`,
        save: `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20,6 9,17 4,12"></polyline>
        </svg>`,
        cancel: `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>`,
        chevronLeft: `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15,18 9,12 15,6"></polyline>
        </svg>`,
        chevronRight: `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9,18 15,12 9,6"></polyline>
        </svg>`
    };
    return icons[iconName] || '';
}

// Function to calculate pagination info
function getPaginationInfo() {
    const totalItems = products.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
    
    return {
        totalItems,
        totalPages,
        startIndex,
        endIndex,
        currentPage,
        itemsPerPage
    };
}

// Function to get products for current page
function getCurrentPageProducts() {
    const { startIndex, endIndex } = getPaginationInfo();
    return products.slice(startIndex, endIndex);
}

// Function to show loading state
function showLoading() {
    const tableBody = document.getElementById('rows');
    tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #666;">Loading...</td></tr>';
}

// Function to show error state
function showError(message) {
    const tableBody = document.getElementById('rows');
    tableBody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 40px; color: #e53e3e;">Error: ${message}</td></tr>`;
}

// Function to show empty state
function showEmpty() {
    const tableBody = document.getElementById('rows');
    tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px; color: #666;">No products found</td></tr>';
}

// Function to fetch products from API
async function fetchProducts() {
    try {
        showLoading();
        
        const response = await fetch(API_URL, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        // Handle different response formats
        if (Array.isArray(data)) {
            products = data;
        } else if (data.success && Array.isArray(data.data)) {
            products = data.data;
        } else if (data.products && Array.isArray(data.products)) {
            products = data.products;
        } else {
            throw new Error('Invalid response format');
        }
        
        // Reset to first page when fetching new data
        currentPage = 1;
        renderProducts();
        renderPagination();
        
    } catch (error) {
        console.error('Error fetching products:', error);
        showError('Failed to load products. Please check your API connection.');
        renderPagination(); // Show empty pagination
    }
}

// Function to render products in the table
function renderProducts() {
    const tableBody = document.getElementById('rows');
    
    if (products.length === 0) {
        showEmpty();
        return;
    }
    
    const currentPageProducts = getCurrentPageProducts();
    tableBody.innerHTML = '';
    
    currentPageProducts.forEach(product => {
        const row = document.createElement('tr');
        row.setAttribute('data-product-id', product.id);
        
        // ID cell
        const idCell = document.createElement('td');
        idCell.textContent = product.id;
        row.appendChild(idCell);
        
        // Title cell
        const titleCell = document.createElement('td');
        if (editingProductId === product.id) {
            titleCell.innerHTML = `<input type="text" class="edit-input" value="${product.title}" data-field="title">`;
        } else {
            titleCell.textContent = product.title;
        }
        row.appendChild(titleCell);
        
        // Price cell
        const priceCell = document.createElement('td');
        priceCell.className = 'price';
        if (editingProductId === product.id) {
            priceCell.innerHTML = `<input type="number" class="edit-input" value="${product.price}" data-field="price" step="0.01" min="0">`;
        } else {
            priceCell.textContent = formatPrice(product.price);
        }
        row.appendChild(priceCell);
        
        // Date cell
        const dateCell = document.createElement('td');
        dateCell.className = 'date';
        dateCell.textContent = product.created_at;
        row.appendChild(dateCell);
        
        // Actions cell
        const actionsCell = document.createElement('td');
        const actionsDiv = document.createElement('div');
        actionsDiv.className = 'actions';
        
        if (editingProductId === product.id) {
            // Save and Cancel buttons
            const saveBtn = document.createElement('button');
            saveBtn.className = 'action-btn save-btn';
            saveBtn.innerHTML = createIcon('save');
            saveBtn.title = 'Save';
            saveBtn.onclick = () => saveProduct(product.id);
            
            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'action-btn cancel-btn';
            cancelBtn.innerHTML = createIcon('cancel');
            cancelBtn.title = 'Cancel';
            cancelBtn.onclick = () => cancelEdit();
            
            actionsDiv.appendChild(saveBtn);
            actionsDiv.appendChild(cancelBtn);
        } else {
            // Edit and Delete buttons
            const editBtn = document.createElement('button');
            editBtn.className = 'action-btn edit-btn';
            editBtn.innerHTML = createIcon('edit');
            editBtn.title = 'Edit';
            editBtn.onclick = () => editProduct(product.id);
            
            const deleteBtn = document.createElement('button');
            deleteBtn.className = 'action-btn delete-btn';
            deleteBtn.innerHTML = createIcon('delete');
            deleteBtn.title = 'Delete';
            deleteBtn.onclick = () => deleteProduct(product.id);
            
            actionsDiv.appendChild(editBtn);
            actionsDiv.appendChild(deleteBtn);
        }
        
        actionsCell.appendChild(actionsDiv);
        row.appendChild(actionsCell);
        
        tableBody.appendChild(row);
    });
}

// Function to render pagination controls
function renderPagination() {
    const paginationContainer = document.getElementById('pagination');
    const { totalItems, totalPages, startIndex, endIndex } = getPaginationInfo();
    
    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let paginationHTML = `
        <div class="pagination-info">
            Showing ${startIndex + 1}-${endIndex} of ${totalItems} products
        </div>
        <div class="pagination-controls">
    `;
    
    // Previous button
    paginationHTML += `
        <button class="pagination-btn ${currentPage === 1 ? 'disabled' : ''}" 
                onclick="goToPage(${currentPage - 1})" 
                ${currentPage === 1 ? 'disabled' : ''}>
            ${createIcon('chevronLeft')} Previous
        </button>
    `;
    
    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
    
    // Adjust start page if we're near the end
    if (endPage - startPage + 1 < maxVisiblePages) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }
    
    // First page and ellipsis
    if (startPage > 1) {
        paginationHTML += `<button class="pagination-btn page-btn" onclick="goToPage(1)">1</button>`;
        if (startPage > 2) {
            paginationHTML += `<span class="pagination-ellipsis">...</span>`;
        }
    }
    
    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        paginationHTML += `
            <button class="pagination-btn page-btn ${i === currentPage ? 'active' : ''}" 
                    onclick="goToPage(${i})">${i}</button>
        `;
    }
    
    // Last page and ellipsis
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHTML += `<span class="pagination-ellipsis">...</span>`;
        }
        paginationHTML += `<button class="pagination-btn page-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
    }
    
    // Next button
    paginationHTML += `
        <button class="pagination-btn ${currentPage === totalPages ? 'disabled' : ''}" 
                onclick="goToPage(${currentPage + 1})" 
                ${currentPage === totalPages ? 'disabled' : ''}>
            Next ${createIcon('chevronRight')}
        </button>
    `;
    
    paginationHTML += '</div>';
    
    paginationContainer.innerHTML = paginationHTML;
}

// Function to go to a specific page
function goToPage(page) {
    const { totalPages } = getPaginationInfo();
    
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    editingProductId = null; // Cancel any ongoing edits when changing pages
    renderProducts();
    renderPagination();
}

// Function to add a new product via API
async function addProduct(title, price) {
    try {
        // Disable form while submitting
        const submitButton = document.querySelector('.form-button');
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Adding...';
        submitButton.disabled = true;
        
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                title: title,
                price: parseFloat(price)
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        // Check if the API returned success
        if (result.success === false) {
            throw new Error(result.message || 'Failed to add product');
        }
        
        // Refresh the products list
        await fetchProducts();
        
        // Show success message (optional)
        console.log('Product added successfully');
        
    } catch (error) {
        console.error('Error adding product:', error);
        alert('Failed to add product: ' + error.message);
    } finally {
        // Re-enable form
        const submitButton = document.querySelector('.form-button');
        submitButton.textContent = 'Add Product';
        submitButton.disabled = false;
    }
}

// Function to start editing a product
function editProduct(id) {
    editingProductId = id;
    renderProducts();
}

// Function to cancel editing
function cancelEdit() {
    editingProductId = null;
    renderProducts();
}

// Function to save edited product
async function saveProduct(id) {
    try {
        const row = document.querySelector(`tr[data-product-id="${id}"]`);
        const titleInput = row.querySelector('input[data-field="title"]');
        const priceInput = row.querySelector('input[data-field="price"]');
        
        const title = titleInput.value.trim();
        const price = parseFloat(priceInput.value);
        
        // Basic validation
        if (!title) {
            alert('Please enter a title');
            return;
        }
        
        if (!price || price <= 0) {
            alert('Please enter a valid price');
            return;
        }
        
        // Add loading state to the row
        row.classList.add('loading');
        
        const response = await fetch(API_URL, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id,
                title: title,
                price: price
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success === false) {
            throw new Error(result.message || 'Failed to update product');
        }
        
        // Update local data
        const productIndex = products.findIndex(p => p.id == id);
        if (productIndex !== -1) {
            products[productIndex].title = title;
            products[productIndex].price = price;
        }
        
        editingProductId = null;
        renderProducts();
        renderPagination();
        
        console.log('Product updated successfully');
        
    } catch (error) {
        console.error('Error updating product:', error);
        alert('Failed to update product: ' + error.message);
        
        // Remove loading state
        const row = document.querySelector(`tr[data-product-id="${id}"]`);
        if (row) row.classList.remove('loading');
    }
}

// Function to delete a product
async function deleteProduct(id) {
    // Get product details for confirmation
    const product = products.find(p => p.id == id);
    if (!product) return;
    
    // Confirm deletion
    if (!confirm(`Are you sure you want to delete "${product.title}"?`)) {
        return;
    }
    
    try {
        const row = document.querySelector(`tr[data-product-id="${id}"]`);
        row.classList.add('loading');
        
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success === false) {
            throw new Error(result.message || 'Failed to delete product');
        }
        
        // Remove from local data
        products = products.filter(p => p.id != id);
        
        // Adjust current page if necessary
        const { totalPages } = getPaginationInfo();
        if (currentPage > totalPages && totalPages > 0) {
            currentPage = totalPages;
        }
        
        renderProducts();
        renderPagination();
        
        console.log('Product deleted successfully');
        
    } catch (error) {
        console.error('Error deleting product:', error);
        alert('Failed to delete product: ' + error.message);
        
        // Remove loading state
        const row = document.querySelector(`tr[data-product-id="${id}"]`);
        if (row) row.classList.remove('loading');
    }
}

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    // Load products when page loads
    fetchProducts();
    
    // Set up form submission handler
    const form = document.getElementById('productForm');
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        const title = document.getElementById('title').value.trim();
        const price = document.getElementById('price').value;
        
        // Basic validation
        if (!title) {
            alert('Please enter a title');
            return;
        }
        
        if (!price || parseFloat(price) <= 0) {
            alert('Please enter a valid price');
            return;
        }
        
        await addProduct(title, price);
        
        // Reset form only if successful
        form.reset();
    });
});

// Optional: Add refresh button functionality
function refreshProducts() {
    editingProductId = null; // Cancel any ongoing edits
    fetchProducts();
}

// Export functions for potential use in other scripts
window.productStore = {
    fetchProducts,
    addProduct,
    editProduct,
    saveProduct,
    deleteProduct,
    refreshProducts,
    goToPage
};
