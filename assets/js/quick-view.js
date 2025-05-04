/**
 * WatchModMarket Quick View Module - Modern Implementation
 * 
 * This module provides a clean, modular implementation of the product quick view
 * functionality with proper error handling and memory management.
 */

const QuickView = (function () {
    'use strict';

    // Store configuration
    const config = {
        selectors: {
            // Buttons that trigger the quick view
            trigger: '.quick-view',
            // The modal container
            modal: '#quick-view-modal',
            // The content container within the modal
            content: '.modal-product-content',
            // The loading indicator
            loading: '.modal-loading',
            // Close button
            close: '.close-modal',
            // Quantity buttons
            qtyButtons: '.qty-btn',
            // Quantity input
            qtyInput: '.qty-input',
            // Add to cart button
            addToCart: '.add-to-cart-btn'
        },
        classes: {
            show: 'show',
            loading: 'loading',
            success: 'success',
            added: 'added'
        },
        attributes: {
            ariaHidden: 'aria-hidden',
            productId: 'data-product-id'
        },
        // Template for the modal if it doesn't exist in the DOM
        modalTemplate: `
            <div id="quick-view-modal" class="product-modal" aria-hidden="true">
                <div class="product-modal-content">
                    <button class="close-modal" aria-label="Close">&times;</button>
                    <div class="modal-body">
                        <div class="modal-loading">Loading...</div>
                        <div class="modal-product-content" style="display: none;"></div>
                    </div>
                </div>
            </div>
        `
    };

    // Store elements and state
    let elements = {};
    let state = {
        initialized: false,
        modalCreated: false
    };

    /**
     * Initialize the quick view functionality
     * @param {Object} options - Optional configuration to override defaults
     */
    function init(options = {}) {
        // Merge options with config
        Object.assign(config, options);

        // Ensure the modal exists in the DOM
        ensureModalExists();

        // Cache DOM elements
        cacheElements();

        // Only proceed if we have the necessary trigger elements
        if (!elements.triggers || !elements.triggers.length) {
            console.warn('Quick view trigger elements not found');
            return;
        }

        // Initialize event listeners
        bindEvents();

        state.initialized = true;
        console.log('Quick view module initialized');
    }

    /**
     * Ensure the quick view modal exists in the DOM
     */
    function ensureModalExists() {
        if (!document.querySelector(config.selectors.modal)) {
            // Insert the modal template
            document.body.insertAdjacentHTML('beforeend', config.modalTemplate);
            state.modalCreated = true;
        }
    }

    /**
     * Cache DOM elements for better performance
     */
    function cacheElements() {
        elements = {};

        // Cache element references
        elements.triggers = document.querySelectorAll(config.selectors.trigger);
        elements.modal = document.querySelector(config.selectors.modal);
        elements.content = elements.modal?.querySelector(config.selectors.content);
        elements.loading = elements.modal?.querySelector(config.selectors.loading);
        elements.close = elements.modal?.querySelector(config.selectors.close);
    }

    /**
     * Bind event listeners
     */
    function bindEvents() {
        // Quick view trigger
        elements.triggers.forEach(trigger => {
            trigger.addEventListener('click', handleQuickViewClick);
        });

        if (elements.modal) {
            // Close button click
            if (elements.close) {
                elements.close.addEventListener('click', closeModal);
            }

            // Click outside modal
            elements.modal.addEventListener('click', handleModalBackdropClick);

            // Add event delegation for dynamic content
            elements.modal.addEventListener('click', handleModalContentClick);
        }

        // ESC key press
        document.addEventListener('keydown', handleKeyPress);
    }

    /**
     * Remove event listeners (useful for cleanup)
     */
    function unbindEvents() {
        // Quick view trigger
        elements.triggers.forEach(trigger => {
            trigger.removeEventListener('click', handleQuickViewClick);
        });

        if (elements.modal) {
            // Close button click
            if (elements.close) {
                elements.close.removeEventListener('click', closeModal);
            }

            // Click outside modal
            elements.modal.removeEventListener('click', handleModalBackdropClick);

            // Event delegation
            elements.modal.removeEventListener('click', handleModalContentClick);
        }

        // ESC key press
        document.removeEventListener('keydown', handleKeyPress);
    }

    /**
     * Handle click on quick view button
     * @param {Event} e - The click event
     */
    function handleQuickViewClick(e) {
        e.preventDefault();

        // Find the product ID
        const productCard = this.closest('.product-card');
        if (!productCard) return;

        const productId = this.dataset.productId || productCard.dataset.productId;
        if (!productId) {
            console.error('Product ID not found');
            return;
        }

        openModal(productId);
    }

    /**
     * Handle clicks on the modal backdrop (to close)
     * @param {Event} e - The click event
     */
    function handleModalBackdropClick(e) {
        if (e.target === elements.modal) {
            closeModal();
        }
    }

    /**
     * Handle clicks within the modal for event delegation
     * @param {Event} e - The click event
     */
    function handleModalContentClick(e) {
        // Quantity buttons
        if (e.target.closest(config.selectors.qtyButtons)) {
            const button = e.target.closest(config.selectors.qtyButtons);
            handleQuantityButtonClick(button);
        }

        // Add to cart button
        if (e.target.closest(config.selectors.addToCart)) {
            const button = e.target.closest(config.selectors.addToCart);
            handleAddToCartClick(button);
        }
    }

    /**
     * Handle keypress events
     * @param {KeyboardEvent} e - The keypress event
     */
    function handleKeyPress(e) {
        if (e.key === 'Escape' && elements.modal &&
            !elements.modal.hasAttribute(config.attributes.ariaHidden)) {
            closeModal();
        }
    }

    /**
     * Handle quantity button clicks
     * @param {HTMLElement} button - The clicked button
     */
    function handleQuantityButtonClick(button) {
        const input = button.closest('.quantity').querySelector(config.selectors.qtyInput);
        if (!input) return;

        const currentVal = parseInt(input.value) || 1;
        const min = parseInt(input.getAttribute('min')) || 1;
        const max = parseInt(input.getAttribute('max')) || 99;

        if (button.classList.contains('plus') && currentVal < max) {
            input.value = currentVal + 1;
        } else if (button.classList.contains('minus') && currentVal > min) {
            input.value = currentVal - 1;
        }

        // Trigger change event
        const event = new Event('change', { bubbles: true });
        input.dispatchEvent(event);
    }

    /**
     * Handle add to cart button click
     * @param {HTMLElement} button - The clicked button
     */
    function handleAddToCartClick(button) {
        const productId = button.getAttribute(config.attributes.productId);
        if (!productId) {
            console.error('Product ID not found on add to cart button');
            return;
        }

        const quantity = elements.modal.querySelector(config.selectors.qtyInput)?.value || 1;

        // Visual feedback
        button.classList.add(config.classes.loading);
        const originalText = button.textContent;
        button.textContent = window.watchmodmarket_ajax?.adding_to_cart || 'Adding...';

        // AJAX request using Fetch API
        fetch(window.watchmodmarket_ajax?.ajax_url || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'watchmodmarket_add_to_cart',
                product_id: productId,
                quantity: quantity,
                security: window.watchmodmarket_ajax?.nonce || ''
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(response => {
                if (response.success) {
                    // Update visual cues
                    button.classList.remove(config.classes.loading);
                    button.classList.add(config.classes.success);
                    button.textContent = window.watchmodmarket_ajax?.added_to_cart || 'Added to cart!';

                    // Update cart count if available
                    if (response.cart_count) {
                        document.querySelectorAll('.cart-count').forEach(el => {
                            el.textContent = response.cart_count;
                        });
                    }

                    // Close modal after delay
                    setTimeout(() => {
                        closeModal();

                        // Reset button state
                        setTimeout(() => {
                            button.classList.remove(config.classes.success);
                            button.textContent = originalText;
                        }, 500);
                    }, 1500);
                } else {
                    throw new Error(response.data?.message || 'Error adding to cart');
                }
            })
            .catch(error => {
                console.error('Add to cart error:', error);
                button.classList.remove(config.classes.loading);
                button.textContent = originalText;

                // Show error message
                const errorMsg = document.createElement('div');
                errorMsg.className = 'error-message';
                errorMsg.textContent = error.message || 'Error adding to cart. Please try again.';
                button.parentNode.appendChild(errorMsg);

                // Remove error message after delay
                setTimeout(() => {
                    if (errorMsg.parentNode) {
                        errorMsg.parentNode.removeChild(errorMsg);
                    }
                }, 3000);
            });
    }

    /**
     * Open the quick view modal and load product data
     * @param {string} productId - The product ID to load
     */
    function openModal(productId) {
        if (!elements.modal || !elements.content || !elements.loading) {
            console.error('Modal elements not found');
            return;
        }

        // Show modal with loading state
        elements.modal.classList.add(config.classes.show);
        elements.modal.removeAttribute(config.attributes.ariaHidden);
        elements.loading.style.display = 'block';
        elements.content.style.display = 'none';

        // Prevent body scrolling
        document.body.style.overflow = 'hidden';

        // Fetch product data
        fetchProductData(productId);
    }

    /**
     * Close the quick view modal
     */
    function closeModal() {
        if (!elements.modal) return;

        elements.modal.classList.remove(config.classes.show);
        elements.modal.setAttribute(config.attributes.ariaHidden, 'true');

        // Re-enable body scrolling
        document.body.style.overflow = '';

        // Reset content after animation completes
        setTimeout(() => {
            if (elements.content) {
                elements.content.innerHTML = '';
            }
        }, 300);
    }

    /**
     * Fetch product data from the server
     * @param {string} productId - The product ID to fetch
     */
    function fetchProductData(productId) {
        // Check if AJAX endpoint is available
        if (!window.watchmodmarket_ajax?.ajax_url) {
            console.error('AJAX URL not found');
            showError('Configuration error. Please reload the page and try again.');
            return;
        }

        // Make AJAX request using Fetch API
        fetch(window.watchmodmarket_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'watchmodmarket_quick_view',
                product_id: productId,
                security: window.watchmodmarket_ajax.nonce || ''
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.html) {
                    renderProductData(data.html);
                } else {
                    throw new Error(data.data?.message || 'Error loading product data');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showError('Error loading product data. Please try again.');
            });
    }

    /**
     * Render product data in the modal
     * @param {string} html - The HTML content to render
     */
    function renderProductData(html) {
        if (!elements.content || !elements.loading) return;

        elements.content.innerHTML = html;

        // Hide loading, show content
        elements.loading.style.display = 'none';
        elements.content.style.display = 'block';

        // Initialize any necessary components inside the modal
        initModalComponents();
    }

    /**
     * Show error message in the modal
     * @param {string} message - The error message to display
     */
    function showError(message) {
        if (!elements.content || !elements.loading) return;

        const errorHTML = `
            <div class="modal-error">
                <p>${message}</p>
                <button class="btn btn-secondary retry-button">Try Again</button>
            </div>
        `;

        elements.content.innerHTML = errorHTML;
        elements.loading.style.display = 'none';
        elements.content.style.display = 'block';

        // Add event listener to retry button
        const retryButton = elements.content.querySelector('.retry-button');
        if (retryButton) {
            retryButton.addEventListener('click', () => {
                closeModal();
            });
        }
    }

    /**
     * Initialize components inside the modal
     */
    function initModalComponents() {
        // Any additional initialization for components inside the modal
        // This creates proper separation of concerns
    }

    /**
     * Clean up resources and remove the modal if it was dynamically created
     */
    function destroy() {
        // Unbind all event listeners
        unbindEvents();

        // Remove the modal if we created it
        if (state.modalCreated && elements.modal) {
            elements.modal.parentNode.removeChild(elements.modal);
        }

        // Reset state
        state.initialized = false;
        state.modalCreated = false;

        console.log('Quick view module destroyed and cleaned up');
    }

    // Public API
    return {
        init: init,
        destroy: destroy,
        openModal: openModal,
        closeModal: closeModal
    };
})();

// Initialize when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Only initialize if we're on a page with products
    if (document.querySelector('.product-card')) {
        QuickView.init();
    }
});