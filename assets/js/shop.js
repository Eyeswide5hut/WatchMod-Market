// Enhanced Shop JavaScript for WatchModMarket with Gallery Features

document.addEventListener('DOMContentLoaded', function () {
    // Filter functionality
    const filterToggleBtn = document.querySelector('.filter-toggle-btn');
    const filterBlock = document.querySelector('.filter-block');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const orderbySelect = document.getElementById('orderby');
    const productGrid = document.querySelector('.product-grid');
    const activeFiltersContainer = document.getElementById('active-filters');
    const noResultsDiv = document.getElementById('no-results');

    // Toggle filter panel on mobile
    if (filterToggleBtn) {
        filterToggleBtn.addEventListener('click', function () {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            filterBlock.classList.toggle('show');
        });
    }

    // Apply filters
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function () {
            const selectedCategories = getSelectedCheckboxes('category');
            const selectedBrands = getSelectedCheckboxes('brand');
            const selectedPartTypes = getSelectedCheckboxes('part-type');
            const selectedCaseSizes = getSelectedCheckboxes('case-size');
            const selectedCompatibility = getSelectedCheckboxes('compatible-with');
            const minPrice = document.getElementById('min-price').value;
            const maxPrice = document.getElementById('max-price').value;

            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            // Clear existing filter params
            params.delete('category');
            params.delete('brand');
            params.delete('part-type');
            params.delete('case-size');
            params.delete('compatible-with');
            params.delete('min_price');
            params.delete('max_price');

            // Add selected filters
            if (selectedCategories.length) params.set('category', selectedCategories.join(','));
            if (selectedBrands.length) params.set('brand', selectedBrands.join(','));
            if (selectedPartTypes.length) params.set('part-type', selectedPartTypes.join(','));
            if (selectedCaseSizes.length) params.set('case-size', selectedCaseSizes.join(','));
            if (selectedCompatibility.length) params.set('compatible-with', selectedCompatibility.join(','));
            if (minPrice) params.set('min_price', minPrice);
            if (maxPrice) params.set('max_price', maxPrice);

            window.location.href = url.origin + url.pathname + '?' + params.toString();
        });
    }

    // Clear filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function () {
            const checkboxes = document.querySelectorAll('.filter-list input[type="checkbox"]');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            document.getElementById('min-price').value = 0;
            document.getElementById('max-price').value = 1000;

            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            // Clear all filter params
            params.delete('category');
            params.delete('brand');
            params.delete('part-type');
            params.delete('case-size');
            params.delete('compatible-with');
            params.delete('min_price');
            params.delete('max_price');

            window.location.href = url.origin + url.pathname + '?' + params.toString();
        });
    }

    // Quick view functionality
    document.addEventListener('click', function (e) {
        if (e.target.closest('.quick-view')) {
            const productCard = e.target.closest('.product-card');
            if (productCard) {
                openQuickView(productCard);
            }
        }
    });

    // Close quick view
    const closeQuickViewBtn = document.getElementById('close-quick-view');
    const quickViewModal = document.getElementById('quick-view-modal');

    if (closeQuickViewBtn) {
        closeQuickViewBtn.addEventListener('click', function () {
            quickViewModal.style.display = 'none';
            quickViewModal.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    // Close quick view on ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && quickViewModal.classList.contains('active')) {
            quickViewModal.style.display = 'none';
            quickViewModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Close quick view when clicking outside
    if (quickViewModal) {
        quickViewModal.addEventListener('click', function (e) {
            if (e.target === this) {
                this.style.display = 'none';
                this.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Sorting change handler
    if (orderbySelect) {
        orderbySelect.addEventListener('change', function () {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);
            params.set('orderby', this.value);
            window.location.href = url.origin + url.pathname + '?' + params.toString();
        });
    }

    // Display active filters
    showActiveFilters();

    // Initialize any currently displayed products
    initializeProductCards();

    // Initialize gallery functionality
    initializeGallery();
});

// Helper function to get selected checkboxes
function getSelectedCheckboxes(name) {
    const checkboxes = document.querySelectorAll(`input[name="${name}"]:checked`);
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}

// Open quick view modal
function openQuickView(productCard) {
    const modal = document.getElementById('quick-view-modal');
    const productId = productCard.dataset.productId || productCard.querySelector('a').href.split('/').pop();

    // Fetch product data via AJAX
    if (typeof watchmodmarket_shop !== 'undefined') {
        const formData = new FormData();
        formData.append('action', 'watchmodmarket_quick_view');
        formData.append('product_id', productId);
        formData.append('nonce', watchmodmarket_shop.nonce);

        fetch(watchmodmarket_shop.ajax_url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    // Populate modal with real data
                    document.getElementById('quick-view-product-image').src = data.image;
                    document.getElementById('quick-view-product-name').textContent = data.name;
                    document.getElementById('quick-view-product-price').innerHTML = data.price;
                    document.getElementById('quick-view-product-description').textContent = data.description;

                    // Load specifications from attributes
                    const specsList = document.getElementById('quick-view-specs-list');
                    let specsHTML = '';

                    if (data.attributes) {
                        Object.entries(data.attributes).forEach(([name, value]) => {
                            specsHTML += `
                            <div class="spec-item">
                                <span class="spec-label">${name}:</span>
                                <span>${value}</span>
                            </div>
                        `;
                        });
                    }

                    specsList.innerHTML = specsHTML;

                    // Update Add to Cart button URL
                    const addToCartBtn = modal.querySelector('.add-to-cart');
                    if (addToCartBtn && data.add_to_cart_url) {
                        addToCartBtn.setAttribute('data-url', data.add_to_cart_url);
                        addToCartBtn.setAttribute('data-product-id', productId);
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching product data:', error);
                // Fallback to basic product data from card
                const productImage = productCard.querySelector('.product-thumbnail').src;
                const productName = productCard.querySelector('.product-info h3').textContent;
                const productPrice = productCard.querySelector('.product-price').textContent;

                document.getElementById('quick-view-product-image').src = productImage;
                document.getElementById('quick-view-product-name').textContent = productName;
                document.getElementById('quick-view-product-price').textContent = productPrice;
            });
    }

    // Show modal
    modal.style.display = 'flex';
    requestAnimationFrame(() => {
        modal.classList.add('active');
    });
    document.body.style.overflow = 'hidden';
}

// Display active filters
function showActiveFilters() {
    const container = document.getElementById('active-filters');
    if (!container) return;

    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);

    container.innerHTML = '';

    // Add active filters
    const filters = {
        category: 'Category',
        brand: 'Brand',
        'part-type': 'Part Type',
        'case-size': 'Case Size',
        'compatible-with': 'Compatible with'
    };

    Object.entries(filters).forEach(([param, label]) => {
        const value = params.get(param);
        if (value) {
            value.split(',').forEach(val => {
                const filterElement = document.createElement('div');
                filterElement.className = 'active-filter';
                filterElement.innerHTML = `
                    ${label}: ${val}
                    <button class="remove-filter" data-param="${param}" data-value="${val}">×</button>
                `;
                container.appendChild(filterElement);
            });
        }
    });

    // Add price range filter
    const minPrice = params.get('min_price');
    const maxPrice = params.get('max_price');
    if (minPrice || maxPrice) {
        const filterElement = document.createElement('div');
        filterElement.className = 'active-filter';
        filterElement.innerHTML = `
            Price: $${minPrice || '0'} - $${maxPrice || '1000'}
            <button class="remove-filter" data-param="price">×</button>
        `;
        container.appendChild(filterElement);
    }

    // Add remove filter event listeners
    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-filter')) {
            const param = e.target.dataset.param;
            const value = e.target.dataset.value;
            removeFilter(param, value);
        }
    });
}

// Remove filter
function removeFilter(param, value) {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);

    if (param === 'price') {
        params.delete('min_price');
        params.delete('max_price');
    } else {
        const currentValues = params.get(param);
        if (currentValues) {
            const valueArray = currentValues.split(',');
            const filteredValues = valueArray.filter(v => v !== value);
            if (filteredValues.length) {
                params.set(param, filteredValues.join(','));
            } else {
                params.delete(param);
            }
        }
    }

    window.location.href = url.origin + url.pathname + '?' + params.toString();
}

// Initialize product cards
function initializeProductCards() {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        // Add hover effects
        card.addEventListener('mouseenter', function () {
            this.classList.add('is-hovered');
        });

        card.addEventListener('mouseleave', function () {
            this.classList.remove('is-hovered');
        });

        // Add to cart functionality
        const addToCartBtn = card.querySelector('.add-to-cart');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function (e) {
                e.preventDefault();
                handleAddToCart(this);
            });
        }

        // Wishlist functionality
        const wishlistBtn = card.querySelector('.add-to-wishlist');
        if (wishlistBtn) {
            wishlistBtn.addEventListener('click', function (e) {
                e.preventDefault();
                handleWishlist(this);
            });
        }
    });
}

// Initialize gallery functionality
function initializeGallery() {
    const grid = document.querySelector('.product-grid');
    if (!grid) return;

    // Create gallery overlay
    const overlay = document.createElement('div');
    overlay.className = 'gallery-overlay';
    document.body.appendChild(overlay);

    const galleryModal = document.createElement('div');
    galleryModal.className = 'gallery-modal';
    galleryModal.innerHTML = `
        <div class="gallery-container">
            <button class="gallery-close" aria-label="Close gallery">×</button>
            <div class="gallery-image-container">
                <img class="gallery-image" src="" alt="">
                <div class="gallery-navigation">
                    <button class="gallery-prev" aria-label="Previous image">&larr;</button>
                    <button class="gallery-next" aria-label="Next image">&rarr;</button>
                </div>
            </div>
            <div class="gallery-info">
                <h3 class="gallery-product-name"></h3>
                <div class="gallery-product-price"></div>
                <div class="gallery-actions">
                    <button class="btn btn-primary gallery-add-to-cart">Add to Cart</button>
                    <button class="btn btn-secondary gallery-quick-view">Quick View</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(galleryModal);

    let currentProductIndex = 0;
    let productCards = [];

    // Update product cards array
    function updateProductCards() {
        productCards = Array.from(document.querySelectorAll('.product-card'));
    }

    // Open gallery
    function openGallery(index) {
        currentProductIndex = index;
        updateGallery();
        document.body.style.overflow = 'hidden';
        galleryModal.classList.add('active');
    }

    // Close gallery
    function closeGallery() {
        document.body.style.overflow = '';
        galleryModal.classList.remove('active');
    }

    // Update gallery content
    function updateGallery() {
        const card = productCards[currentProductIndex];
        if (!card) return;

        const image = card.querySelector('.product-thumbnail');
        const name = card.querySelector('.product-info h3');
        const price = card.querySelector('.product-price');

        if (image) galleryModal.querySelector('.gallery-image').src = image.src;
        if (name) galleryModal.querySelector('.gallery-product-name').textContent = name.textContent;
        if (price) galleryModal.querySelector('.gallery-product-price').innerHTML = price.innerHTML;

        // Update navigation button states
        const prevBtn = galleryModal.querySelector('.gallery-prev');
        const nextBtn = galleryModal.querySelector('.gallery-next');

        prevBtn.disabled = currentProductIndex === 0;
        nextBtn.disabled = currentProductIndex === productCards.length - 1;
    }

    // Navigation
    function showPreviousProduct() {
        if (currentProductIndex > 0) {
            currentProductIndex--;
            updateGallery();
        }
    }

    function showNextProduct() {
        if (currentProductIndex < productCards.length - 1) {
            currentProductIndex++;
            updateGallery();
        }
    }

    // Event Listeners

    // Click on product image to open gallery
    document.addEventListener('click', function (e) {
        const productImage = e.target.closest('.product-image');
        if (productImage) {
            const card = productImage.closest('.product-card');
            updateProductCards();
            const index = productCards.indexOf(card);
            if (index > -1) {
                e.preventDefault();
                openGallery(index);
            }
        }
    });

    // Gallery controls
    galleryModal.querySelector('.gallery-close').addEventListener('click', closeGallery);
    overlay.addEventListener('click', closeGallery);
    galleryModal.querySelector('.gallery-prev').addEventListener('click', showPreviousProduct);
    galleryModal.querySelector('.gallery-next').addEventListener('click', showNextProduct);

    // Keyboard navigation
    document.addEventListener('keydown', function (e) {
        if (!galleryModal.classList.contains('active')) return;

        switch (e.key) {
            case 'Escape':
                closeGallery();
                break;
            case 'ArrowLeft':
                showPreviousProduct();
                break;
            case 'ArrowRight':
                showNextProduct();
                break;
        }
    });

    // Gallery actions
    galleryModal.querySelector('.gallery-add-to-cart').addEventListener('click', function () {
        const currentCard = productCards[currentProductIndex];
        const addToCartBtn = currentCard.querySelector('.add-to-cart');
        if (addToCartBtn) {
            handleAddToCart(addToCartBtn);
        }
    });

    galleryModal.querySelector('.gallery-quick-view').addEventListener('click', function () {
        const currentCard = productCards[currentProductIndex];
        openQuickView(currentCard);
        closeGallery();
    });
}

// Handle add to cart
function handleAddToCart(button) {
    const productId = button.dataset.productId || button.closest('.product-card').dataset.productId;

    // Show loading state
    button.classList.add('loading');
    const originalText = button.textContent;
    button.textContent = watchmodmarket_shop?.adding_to_cart || 'Adding...';
    button.disabled = true;

    // Simulate AJAX add to cart (implement real AJAX in production)
    setTimeout(() => {
        button.classList.remove('loading');
        button.classList.add('added');
        button.textContent = watchmodmarket_shop?.added_to_cart || 'Added!';

        // Show cart notification
        showCartNotification(productId);

        // Reset button after delay
        setTimeout(() => {
            button.classList.remove('added');
            button.textContent = originalText;
            button.disabled = false;
        }, 2000);
    }, 800);
}

// Handle wishlist
function handleWishlist(button) {
    const productId = button.dataset.productId || button.closest('.product-card').dataset.productId;

    button.classList.toggle('active');
    const icon = button.querySelector('span');
    if (icon) {
        icon.textContent = button.classList.contains('active') ? '❤️' : '🤍';
    }

    // Add animation
    const iconSvg = button.querySelector('svg');
    if (iconSvg) {
        iconSvg.style.transform = 'scale(1.3)';
        setTimeout(() => {
            iconSvg.style.transform = '';
        }, 300);
    }
}

// Show cart notification
function showCartNotification(productId) {
    let notification = document.getElementById('cart-notification');

    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'cart-notification';
        notification.className = 'notification';
        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">✓</div>
                <div class="notification-message">${watchmodmarket_shop?.product_added || 'Product added to cart'}</div>
                <div class="notification-actions">
                    <a href="${window.location.origin}/cart" class="btn btn-primary btn-sm">View Cart</a>
                    <button class="notification-close btn btn-secondary btn-sm">Continue Shopping</button>
                </div>
            </div>
        `;
        document.body.appendChild(notification);

        // Add close functionality
        notification.querySelector('.notification-close').addEventListener('click', function () {
            notification.classList.remove('active');
        });
    }

    // Reset any existing timeout
    if (notification.timeoutId) {
        clearTimeout(notification.timeoutId);
    }

    // Show notification
    notification.classList.add('active');

    // Auto-hide after 5 seconds
    notification.timeoutId = setTimeout(() => {
        notification.classList.remove('active');
    }, 5000);
}