/**
 * WatchModMarket AJAX Handler - Improved
 * 
 * This module provides consistent error handling and request management
 * for all AJAX operations in the WatchModMarket theme.
 */

const AjaxHandler = (function () {
    'use strict';

    // Store configuration
    const config = {
        // Default settings for AJAX requests
        defaults: {
            ajax_url: typeof watchmodmarket_ajax !== 'undefined' ? watchmodmarket_ajax.ajax_url : '/wp-admin/admin-ajax.php',
            nonce: typeof watchmodmarket_ajax !== 'undefined' ? watchmodmarket_ajax.nonce : '',
            timeout: 30000, // 30 seconds timeout
            retries: 1      // Number of retries for failed requests
        },
        // Messages for different operations
        messages: {
            default: {
                loading: 'Loading...',
                success: 'Success!',
                error: 'An error occurred. Please try again.'
            },
            addToCart: {
                loading: 'Adding to cart...',
                success: 'Added to cart!',
                error: 'Error adding to cart. Please try again.'
            },
            quickView: {
                loading: 'Loading product...',
                success: '',
                error: 'Error loading product data. Please try again.'
            },
            newsletter: {
                loading: 'Subscribing...',
                success: 'Thank you for subscribing!',
                error: 'Error submitting form. Please try again.'
            }
        },
        // Callbacks for different operations
        callbacks: {}
    };

    /**
     * Initialize the AJAX handler
     * @param {Object} options - Optional configuration to override defaults
     */
    function init(options = {}) {
        // Merge options with config
        if (options.defaults) {
            config.defaults = { ...config.defaults, ...options.defaults };
        }

        if (options.messages) {
            // Deep merge messages
            Object.keys(options.messages).forEach(key => {
                if (config.messages[key]) {
                    config.messages[key] = { ...config.messages[key], ...options.messages[key] };
                } else {
                    config.messages[key] = options.messages[key];
                }
            });
        }

        if (options.callbacks) {
            config.callbacks = { ...config.callbacks, ...options.callbacks };
        }

        console.log('AJAX Handler initialized');
    }

    /**
     * Make an AJAX request
     * @param {Object} options - Request options
     * @returns {Promise} - Promise that resolves with the response
     */
    function request(options) {
        // Merge with default options
        const settings = {
            action: '',
            method: 'POST',
            data: {},
            contentType: 'application/x-www-form-urlencoded',
            operation: 'default',
            showLoading: true,
            element: null,
            timeout: config.defaults.timeout,
            retries: config.defaults.retries,
            ...options
        };

        // Reference to the element
        const element = settings.element;

        // Get messages for this operation
        const messages = config.messages[settings.operation] || config.messages.default;

        // Show loading state if requested
        if (settings.showLoading && element) {
            showLoading(element, messages.loading);
        }

        // Prepare request data
        let body;
        if (settings.contentType === 'application/x-www-form-urlencoded') {
            const formData = new URLSearchParams();

            // Add action
            if (settings.action) {
                formData.append('action', settings.action);
            }

            // Add nonce for security
            formData.append('security', config.defaults.nonce);

            // Add all other data
            Object.keys(settings.data).forEach(key => {
                formData.append(key, settings.data[key]);
            });

            body = formData;
        } else if (settings.contentType === 'application/json') {
            body = JSON.stringify({
                action: settings.action,
                security: config.defaults.nonce,
                ...settings.data
            });
        } else if (settings.contentType === 'multipart/form-data') {
            body = new FormData();

            // Add action
            if (settings.action) {
                body.append('action', settings.action);
            }

            // Add nonce for security
            body.append('security', config.defaults.nonce);

            // Add all other data
            Object.keys(settings.data).forEach(key => {
                if (Array.isArray(settings.data[key])) {
                    settings.data[key].forEach(item => {
                        body.append(`${key}[]`, item);
                    });
                } else {
                    body.append(key, settings.data[key]);
                }
            });
        }

        // Set up headers
        const headers = {};
        if (settings.contentType !== 'multipart/form-data') {
            headers['Content-Type'] = settings.contentType;
        }

        // Add X-Requested-With header for WordPress compatibility
        headers['X-Requested-With'] = 'XMLHttpRequest';

        // Create abort controller for timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), settings.timeout);

        // Return a promise
        return new Promise((resolve, reject) => {
            const attemptFetch = (attemptsLeft) => {
                fetch(config.defaults.ajax_url, {
                    method: settings.method,
                    headers: headers,
                    body: body,
                    signal: controller.signal
                })
                    .then(response => {
                        // Clear timeout
                        clearTimeout(timeoutId);

                        // Check if response is OK
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }

                        // Parse response
                        return response.json();
                    })
                    .then(data => {
                        // Handle success
                        if (settings.showLoading && element) {
                            showSuccess(element, messages.success);
                        }

                        // Call success callback if it exists
                        if (config.callbacks[settings.operation]?.success) {
                            config.callbacks[settings.operation].success(data, element);
                        }

                        resolve(data);
                    })
                    .catch(error => {
                        // Clear timeout
                        clearTimeout(timeoutId);

                        // Check if we should retry
                        if (attemptsLeft > 0 && error.name !== 'AbortError') {
                            console.warn(`Request failed, retrying (${attemptsLeft} attempts left)`, error);
                            attemptFetch(attemptsLeft - 1);
                        } else {
                            // Handle error
                            console.error('AJAX request error:', error);

                            if (settings.showLoading && element) {
                                showError(element, messages.error);
                            }

                            // Call error callback if it exists
                            if (config.callbacks[settings.operation]?.error) {
                                config.callbacks[settings.operation].error(error, element);
                            }

                            reject(error);
                        }
                    });
            };

            // Start first attempt
            attemptFetch(settings.retries);
        });
    }

    /**
     * Show loading state on an element
     * @param {HTMLElement} element - The element to update
     * @param {string} message - Loading message to display
     */
    function showLoading(element, message) {
        if (!element) return;

        // Store original content and state
        element.dataset.originalContent = element.innerHTML;
        element.dataset.originalDisabled = element.disabled;

        // Update element
        element.disabled = true;
        element.classList.add('loading');

        // For buttons, update text
        if (element.tagName === 'BUTTON' || element.type === 'button' || element.type === 'submit') {
            element.innerHTML = message;
        } else {
            // For other elements, append loading spinner
            const loadingSpinner = document.createElement('div');
            loadingSpinner.className = 'loading-spinner';
            loadingSpinner.textContent = message;
            element.appendChild(loadingSpinner);
        }
    }

    /**
     * Show success state on an element
     * @param {HTMLElement} element - The element to update
     * @param {string} message - Success message to display
     */
    function showSuccess(element, message) {
        if (!element) return;

        // Remove loading state
        element.classList.remove('loading');
        element.classList.add('success');

        // If message is provided, update content
        if (message) {
            if (element.tagName === 'BUTTON' || element.type === 'button' || element.type === 'submit') {
                element.innerHTML = message;
            }
        }

        // Reset element after delay
        setTimeout(() => {
            resetElement(element);
        }, 2000);
    }

    /**
     * Show error state on an element
     * @param {HTMLElement} element - The element to update
     * @param {string} message - Error message to display
     */
    function showError(element, message) {
        if (!element) return;

        // Remove loading state, add error state
        element.classList.remove('loading');
        element.classList.add('error');

        // For buttons, update text
        if (element.tagName === 'BUTTON' || element.type === 'button' || element.type === 'submit') {
            element.innerHTML = message;
        } else {
            // For other elements, show error message
            const errorMessage = document.createElement('div');
            errorMessage.className = 'error-message';
            errorMessage.textContent = message;

            // Remove any existing error message
            const existingError = element.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }

            element.appendChild(errorMessage);
        }

        // Reset element after delay
        setTimeout(() => {
            resetElement(element);
        }, 3000);
    }

    /**
     * Reset an element to its original state
     * @param {HTMLElement} element - The element to reset
     */
    function resetElement(element) {
        if (!element) return;

        // Remove all state classes
        element.classList.remove('loading', 'success', 'error');

        // Restore original content if it exists
        if (element.dataset.originalContent) {
            element.innerHTML = element.dataset.originalContent;
            delete element.dataset.originalContent;
        }

        // Restore original disabled state
        if (element.dataset.originalDisabled !== undefined) {
            element.disabled = element.dataset.originalDisabled === 'true';
            delete element.dataset.originalDisabled;
        }
    }

    /**
     * Add to cart helper function
     * @param {number} productId - The ID of the product to add
     * @param {number} quantity - The quantity to add
     * @param {HTMLElement} button - The button that triggered the add
     * @returns {Promise} - Promise that resolves with the response
     */
    function addToCart(productId, quantity = 1, button = null) {
        return request({
            action: 'watchmodmarket_add_to_cart',
            data: {
                product_id: productId,
                quantity: quantity
            },
            element: button,
            operation: 'addToCart'
        });
    }

    /**
     * Quick view helper function
     * @param {number} productId - The ID of the product to view
     * @param {HTMLElement} element - Element to show loading state
     * @returns {Promise} - Promise that resolves with the response
     */
    function quickView(productId, element = null) {
        return request({
            action: 'watchmodmarket_quick_view',
            data: {
                product_id: productId
            },
            element: element,
            operation: 'quickView'
        });
    }

    /**
     * Subscribe to newsletter helper function
     * @param {string} email - The email to subscribe
     * @param {HTMLElement} button - The submit button
     * @returns {Promise} - Promise that resolves with the response
     */
    function subscribeNewsletter(email, button = null) {
        return request({
            action: 'watchmodmarket_newsletter_subscribe',
            data: {
                email: email
            },
            element: button,
            operation: 'newsletter'
        });
    }

    /**
     * Generalized AJAX form submission
     * @param {HTMLFormElement} form - The form to submit
     * @param {Object} options - Additional options
     * @returns {Promise} - Promise that resolves with the response
     */
    function submitForm(form, options = {}) {
        if (!form) {
            return Promise.reject(new Error('Form not provided'));
        }

        // Get form data
        const formData = new FormData(form);

        // Add any additional data
        if (options.additionalData) {
            Object.keys(options.additionalData).forEach(key => {
                formData.append(key, options.additionalData[key]);
            });
        }

        // Find submit button if not specified
        const submitButton = options.submitButton || form.querySelector('button[type="submit"], input[type="submit"]');

        return request({
            action: options.action || form.getAttribute('data-action') || '',
            method: options.method || form.method || 'POST',
            data: Object.fromEntries(formData),
            contentType: options.contentType || 'application/x-www-form-urlencoded',
            element: submitButton,
            operation: options.operation || 'default',
            showLoading: options.showLoading !== false
        });
    }

    // Public API
    return {
        init: init,
        request: request,
        addToCart: addToCart,
        quickView: quickView,
        subscribeNewsletter: subscribeNewsletter,
        submitForm: submitForm
    };
})();

// Initialize when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize with default options
    AjaxHandler.init();

    // Example usage:

    // Add to cart buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            if (!productId) return;

            AjaxHandler.addToCart(productId, 1, this)
                .then(response => {
                    // Handle success - Cart count and notification are handled by default callbacks
                    console.log('Product added to cart:', response);

                    // Show notification
                    if (response.cart_count) {
                        // Update mini cart if it exists
                        const miniCartCount = document.querySelector('.cart-count');
                        if (miniCartCount) {
                            miniCartCount.textContent = response.cart_count;
                        }
                    }
                })
                .catch(error => {
                    // Error handling is done by the handler
                    console.error('Error adding to cart:', error);
                });
        });
    });

    // Quick view buttons
    document.querySelectorAll('.quick-view').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const productId = this.dataset.productId;
            if (!productId) return;

            AjaxHandler.quickView(productId, this)
                .then(response => {
                    // Open modal with product data
                    const modal = document.getElementById('quick-view-modal');
                    if (modal && response.html) {
                        const content = modal.querySelector('.modal-product-content');
                        if (content) {
                            content.innerHTML = response.html;
                        }

                        // Show modal
                        modal.classList.add('show');
                        modal.removeAttribute('aria-hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading quick view:', error);
                });
        });
    });

    // Newsletter forms
    document.querySelectorAll('.newsletter-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const emailInput = this.querySelector('input[type="email"]');
            if (!emailInput || !emailInput.value) return;

            const submitButton = this.querySelector('button[type="submit"]');

            AjaxHandler.submitForm(this, {
                operation: 'newsletter',
                submitButton: submitButton
            })
                .then(response => {
                    // Replace form with success message
                    this.innerHTML = '<p class="success-message">Thank you for subscribing!</p>';
                })
                .catch(error => {
                    console.error('Newsletter subscription error:', error);
                });
        });
    });
});