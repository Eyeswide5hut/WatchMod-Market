/**
 * Fixed Watch Builder functionality for WatchModMarket
 * Main fixes: Proper AJAX setup, form handling, and event binding
 */
(function ($) {
    "use strict";

    // Watch Builder Class
    class WatchBuilder {
        constructor() {
            // State management
            this.state = {
                currentView: 'front',
                selectedParts: new Map([
                    ['case', null],
                    ['dial', null],
                    ['hands', null],
                    ['strap', null],
                    ['movement', null]
                ]),
                compatibilityWarnings: [],
                isLoading: true,
                totalPrice: 0
            };

            // Cache DOM elements for better performance
            this.elements = this.cacheElements();

            // Settings with better validation
            this.settings = this.getSettings();

            // Initialize with error handling
            this.init().catch(error => {
                console.error('Failed to initialize Watch Builder:', error);
                this.showError('Failed to initialize builder. Please refresh the page.');
            });
        }

        /**
         * Cache DOM elements for better performance
         */
        cacheElements() {
            return {
                loading: $('#loading-indicator'),
                tabs: $('.parts-tab'),
                partItems: $('.part-item'),
                viewControls: $('.view-control'),
                compatAlert: $('#compatibility-alert'),
                totalPrice: $('#total-price'),
                saveButton: $('#save-build'),
                buildForm: $('#add-build-to-cart'),
                specTable: $('.spec-table tbody'),
                canvas: $('#watch-3d-render')[0],
                saveModal: $('#save-build-modal'),
                saveForm: $('#save-build-form')
            };
        }

        /**
         * Get settings with proper fallbacks
         */
        getSettings() {
            // Try to get settings from localized script first
            const wpData = window.wpData || window.watchmodmarket_ajax || {};

            return {
                ajaxUrl: wpData.ajax_url || wpData.ajaxUrl || '/wp-admin/admin-ajax.php',
                nonce: wpData.nonce || '',
                isLoggedIn: Boolean(wpData.isLoggedIn),
                loginUrl: wpData.loginUrl || '/wp-login.php',
                currencySymbol: wpData.currencySymbol || '$',
                themeUrl: wpData.themeUrl || '',
                i18n: wpData.i18n || this.getDefaultI18n()
            };
        }

        /**
         * Default internationalization strings
         */
        getDefaultI18n() {
            return {
                incompatibleMovement: 'The selected movement is not compatible with this case.',
                dialTooBig: 'The selected dial is too large for this case.',
                incompatibleHands: 'The selected hands are not compatible with this movement.',
                loginRequired: 'You must be logged in to save builds.',
                selectSomeParts: 'Please select at least one part to save a build.',
                buildSaved: 'Your build has been saved successfully!',
                saveFailed: 'Failed to save your build. Please try again.',
                selectAllParts: 'Please select all required parts',
                confirmIncompatible: 'There are compatibility warnings. Continue anyway?',
                addingToCart: 'Adding to cart...',
                addedToCart: 'Added to cart!'
            };
        }

        /**
         * Initialize the builder with async support
         */
        async init() {
            try {
                // Setup event listeners
                this.setupEventListeners();

                // Initialize rendering
                await this.initializeRenderer();

                // Initial state setup
                this.checkSelectedPartsOnLoad();
                this.updatePricing();

                // Hide loading after successful initialization
                setTimeout(() => {
                    this.state.isLoading = false;
                    this.elements.loading.fadeOut();
                }, 1000);

                console.log('Watch Builder initialized successfully');

            } catch (error) {
                console.error('Initialization error:', error);
                throw error;
            }
        }

        /**
         * Setup all event listeners with better delegation
         */
        setupEventListeners() {
            // Use event delegation for better performance
            $(document)
                .on('click', '.parts-tab', this.handleTabClick.bind(this))
                .on('click', '.part-item', this.handlePartSelection.bind(this))
                .on('click', '.view-control', this.handleViewChange.bind(this))
                .on('click', '#save-build', this.handleSaveBuild.bind(this))
                .on('submit', '#add-build-to-cart', this.handleAddToCart.bind(this))
                .on('submit', '#save-build-form', this.handleSaveForm.bind(this))
                .on('click', '.builder-modal-close', this.closeSaveModal.bind(this))
                .on('click', '#save-build-modal', (e) => {
                    if (e.target === e.currentTarget) {
                        this.closeSaveModal();
                    }
                });

            // Window events
            $(window).on('beforeunload', this.cleanup.bind(this));
            $(window).on('resize', this.debounce(this.handleResize.bind(this), 250));

            console.log('Event listeners set up');
        }

        /**
         * Handle part selection with throttling
         */
        handlePartSelection(e) {
            e.preventDefault();

            const $item = $(e.currentTarget);
            const partId = $item.data('part-id');

            if (!partId) return;

            const partType = partId.split('-')[0];

            // Validate part type
            if (!this.state.selectedParts.has(partType)) {
                console.warn('Invalid part type:', partType);
                return;
            }

            // Update UI
            $item.siblings('.part-item').removeClass('selected');
            $item.addClass('selected');

            // Store selected part
            this.state.selectedParts.set(partType, partId);

            console.log('Selected part:', partType, partId);

            // Batch updates for better performance
            this.batchUpdate();
        }

        /**
         * Batch multiple updates for better performance
         */
        batchUpdate() {
            // Use requestAnimationFrame for smooth updates
            if (this.updatePending) return;

            this.updatePending = true;
            requestAnimationFrame(() => {
                this.checkCompatibility();
                this.updateWatchModel();
                this.updatePricing();
                this.updateSpecTable();
                this.updatePending = false;
            });
        }

        /**
         * Handle Add to Cart form submission
         */
        handleAddToCart(e) {
            e.preventDefault();

            console.log('Add to Cart clicked');

            // Validate required parts
            const selectedParts = Array.from(this.state.selectedParts.entries())
                .filter(([key, value]) => value !== null);

            if (selectedParts.length === 0) {
                this.showError(this.settings.i18n.selectAllParts);
                return false;
            }

            // Check compatibility warnings
            if (this.state.compatibilityWarnings.length > 0) {
                if (!confirm(this.settings.i18n.confirmIncompatible)) {
                    return false;
                }
            }

            // Show loading state
            const $submitButton = this.elements.buildForm.find('button[type="submit"]');
            const originalText = $submitButton.text();
            $submitButton.text(this.settings.i18n.addingToCart).prop('disabled', true);

            // Prepare data for submission
            const formData = this.prepareCartData();

            // Submit via AJAX
            $.ajax({
                url: this.settings.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'add_build_to_cart',
                    nonce: this.settings.nonce,
                    ...formData
                },
                success: (response) => {
                    console.log('Cart response:', response);
                    if (response.success) {
                        $submitButton.text(this.settings.i18n.addedToCart);
                        // Redirect to cart after a short delay
                        setTimeout(() => {
                            window.location.href = response.data.cart_url || '/cart/';
                        }, 1000);
                    } else {
                        this.showError(response.data.message || 'Failed to add to cart');
                        $submitButton.text(originalText).prop('disabled', false);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('AJAX error:', error);
                    this.showError('Failed to add to cart. Please try again.');
                    $submitButton.text(originalText).prop('disabled', false);
                }
            });
        }

        /**
         * Prepare data for cart submission
         */
        prepareCartData() {
            const cartData = {
                selected_parts: {},
                total_price: this.state.totalPrice,
                build_data: {
                    name: 'Custom Watch Build',
                    parts: {}
                }
            };

            // Add selected parts
            this.state.selectedParts.forEach((partId, partType) => {
                if (partId) {
                    const productId = partId.split('-')[1];
                    cartData.selected_parts[partType] = productId;
                    cartData.build_data.parts[partType] = {
                        id: productId,
                        name: this.getPartName(partId),
                        price: this.getPartPrice(partId)
                    };
                }
            });

            return cartData;
        }

        /**
         * Get part name from DOM
         */
        getPartName(partId) {
            const $part = $(`[data-part-id="${partId}"]`);
            return $part.find('.part-name').text() || 'Unknown Part';
        }

        /**
         * Get part price from DOM
         */
        getPartPrice(partId) {
            const $part = $(`[data-part-id="${partId}"]`);
            return parseFloat($part.data('price')) || 0;
        }

        /**
         * Handle Save Build button click
         */
        handleSaveBuild(e) {
            e.preventDefault();

            console.log('Save Build clicked');

            if (!this.settings.isLoggedIn) {
                this.showError(this.settings.i18n.loginRequired);
                setTimeout(() => {
                    window.location.href = this.settings.loginUrl;
                }, 2000);
                return;
            }

            // Check if any parts are selected
            const hasSelectedParts = Array.from(this.state.selectedParts.values())
                .some(part => part !== null);

            if (!hasSelectedParts) {
                this.showError(this.settings.i18n.selectSomeParts);
                return;
            }

            // Show save modal
            this.openSaveModal();
        }

        /**
         * Open save modal
         */
        openSaveModal() {
            this.elements.saveModal.show().attr('aria-hidden', 'false');
            this.elements.saveForm.find('#build-name').focus();
        }

        /**
         * Close save modal
         */
        closeSaveModal() {
            this.elements.saveModal.hide().attr('aria-hidden', 'true');
            this.elements.saveForm[0].reset();
        }

        /**
         * Handle save form submission
         */
        handleSaveForm(e) {
            e.preventDefault();

            console.log('Save form submitted');

            const formData = {
                action: 'save_watch_build',
                security: this.settings.nonce, // Use 'security' as expected by the PHP handler
                build_name: $('#build-name').val(),
                build_description: $('#build-description').val(),
                build_public: $('input[name="build_public"]').is(':checked') ? 1 : 0,
                selected_parts: this.prepareSelectedPartsForSave(),
                total_price: this.state.totalPrice
            };

            const $submitButton = this.elements.saveForm.find('button[type="submit"]');
            const originalText = $submitButton.text();

            $.ajax({
                url: this.settings.ajaxUrl,
                method: 'POST',
                data: formData,
                beforeSend: () => {
                    $submitButton.text('Saving...').prop('disabled', true);
                },
                success: (response) => {
                    console.log('Save response:', response);
                    if (response.success) {
                        this.showSuccess(this.settings.i18n.buildSaved);
                        this.closeSaveModal();
                    } else {
                        this.showError(response.data.message || this.settings.i18n.saveFailed);
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Save error:', error);
                    this.showError(this.settings.i18n.saveFailed);
                },
                complete: () => {
                    $submitButton.text(originalText).prop('disabled', false);
                }
            });
        }

        /**
         * Prepare selected parts for save operation
         */
        prepareSelectedPartsForSave() {
            const parts = {};
            this.state.selectedParts.forEach((partId, partType) => {
                if (partId) {
                    parts[partType] = {
                        id: partId.split('-')[1],
                        full_id: partId,
                        name: this.getPartName(partId),
                        price: this.getPartPrice(partId)
                    };
                }
            });
            return parts;
        }

        /**
         * Enhanced pricing update with validation
         */
        updatePricing() {
            let totalPrice = 0;

            this.state.selectedParts.forEach((partId) => {
                if (partId) {
                    const $part = $(`[data-part-id="${partId}"]`);
                    const partPrice = parseFloat($part.data('price')) || 0;
                    totalPrice += partPrice;
                }
            });

            this.state.totalPrice = totalPrice;

            // Update price display with proper formatting
            if (this.elements.totalPrice.length) {
                const formattedPrice = this.formatPrice(totalPrice);
                this.elements.totalPrice.text(formattedPrice);
            }

            // Update hidden form field
            $('#build-total-price').val(totalPrice.toFixed(2));
        }

        /**
         * Format price with proper currency symbol
         */
        formatPrice(price) {
            return this.settings.currencySymbol + price.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        /**
         * Enhanced spec table update
         */
        updateSpecTable() {
            if (!this.elements.specTable.length) return;

            this.elements.specTable.empty();

            this.state.selectedParts.forEach((partId, partType) => {
                if (partId) {
                    const $part = $(`[data-part-id="${partId}"]`);
                    const partName = $part.find('.part-name').text() || 'Unknown';
                    const partSpecs = $part.find('.part-specs').text() || 'No specifications';

                    const row = $(`
                        <tr>
                            <td>${this.capitalizeFirstLetter(partType)}</td>
                            <td>${partName}</td>
                            <td>${partSpecs}</td>
                        </tr>
                    `);

                    this.elements.specTable.append(row);
                }
            });
        }

        /**
         * Show error message to user
         */
        showError(message) {
            this.showNotification(message, 'error');
        }

        /**
         * Show success message to user
         */
        showSuccess(message) {
            this.showNotification(message, 'success');
        }

        /**
         * Show notification to user
         */
        showNotification(message, type = 'info') {
            // Remove existing notifications
            $('.builder-notification').remove();

            // Create notification
            const notification = $(`
                <div class="builder-notification builder-notification-${type}">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `);

            // Add to page
            $('.builder-container').prepend(notification);

            // Handle close button
            notification.find('.notification-close').on('click', () => {
                notification.fadeOut(() => notification.remove());
            });

            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 5000);
        }

        /**
         * Check for pre-selected parts on page load
         */
        checkSelectedPartsOnLoad() {
            $('.part-item.selected').each((_, item) => {
                const partId = $(item).data('part-id');
                if (partId) {
                    const partType = partId.split('-')[0];
                    if (this.state.selectedParts.has(partType)) {
                        this.state.selectedParts.set(partType, partId);
                    }
                }
            });

            this.checkCompatibility();
            this.updateWatchModel();
        }

        /**
         * Basic compatibility checking
         */
        checkCompatibility() {
            this.state.compatibilityWarnings = [];

            // Hide alert by default
            this.elements.compatAlert.hide();

            // Add compatibility logic here as needed
            if (this.state.compatibilityWarnings.length > 0) {
                this.elements.compatAlert.show();
            }
        }

        /**
         * Initialize renderer
         */
        async initializeRenderer() {
            try {
                this.initCanvas();
                this.createWatchModel2D();
            } catch (error) {
                console.warn('Rendering initialization failed:', error);
            }
        }

        /**
         * Initialize canvas for 2D rendering
         */
        initCanvas() {
            if (!this.elements.canvas) {
                console.warn('Canvas element not found');
                return;
            }

            this.ctx = this.elements.canvas.getContext('2d');
            $(this.elements.canvas).css('display', 'block');
        }

        /**
         * Create 2D watch model
         */
        createWatchModel2D() {
            if (!this.ctx || !this.elements.canvas) return;

            this.ctx.clearRect(0, 0, this.elements.canvas.width, this.elements.canvas.height);

            const centerX = this.elements.canvas.width / 2;
            const centerY = this.elements.canvas.height / 2;

            this.drawWatchFront(centerX, centerY);
        }

        /**
         * Draw basic watch representation
         */
        drawWatchFront(centerX, centerY) {
            // Draw case
            this.ctx.strokeStyle = '#000000';
            this.ctx.lineWidth = 3;
            this.ctx.beginPath();
            this.ctx.arc(centerX, centerY, 150, 0, Math.PI * 2);
            this.ctx.stroke();
            this.ctx.fillStyle = '#FFFFFF';
            this.ctx.fill();

            // Draw basic watch elements
            this.ctx.strokeStyle = '#000000';
            this.ctx.lineWidth = 2;

            // Hour markers
            for (let i = 0; i < 12; i++) {
                const angle = (i * Math.PI / 6) - Math.PI / 2;
                const startX = centerX + Math.cos(angle) * 130;
                const startY = centerY + Math.sin(angle) * 130;
                const endX = centerX + Math.cos(angle) * 140;
                const endY = centerY + Math.sin(angle) * 140;

                this.ctx.beginPath();
                this.ctx.moveTo(startX, startY);
                this.ctx.lineTo(endX, endY);
                this.ctx.stroke();
            }

            // Draw hands
            this.ctx.strokeStyle = '#000000';
            this.ctx.lineWidth = 4;
            this.ctx.beginPath();
            this.ctx.moveTo(centerX, centerY);
            this.ctx.lineTo(centerX + 60, centerY - 40);
            this.ctx.stroke();

            this.ctx.lineWidth = 2;
            this.ctx.beginPath();
            this.ctx.moveTo(centerX, centerY);
            this.ctx.lineTo(centerX, centerY - 100);
            this.ctx.stroke();

            // Center dot
            this.ctx.fillStyle = '#000000';
            this.ctx.beginPath();
            this.ctx.arc(centerX, centerY, 5, 0, Math.PI * 2);
            this.ctx.fill();
        }

        /**
         * Update watch model (placeholder)
         */
        updateWatchModel() {
            this.createWatchModel2D();
        }

        /**
         * Handle tab clicks
         */
        handleTabClick(e) {
            e.preventDefault();
            const $tab = $(e.currentTarget);
            const tabId = $tab.attr('id');

            if (!tabId) return;

            const targetSection = $('#' + tabId.replace('tab-', 'section-'));

            if (!targetSection.length) return;

            // Update tab state
            this.elements.tabs.removeClass('active').attr('aria-selected', 'false');
            $tab.addClass('active').attr('aria-selected', 'true');

            // Update section visibility
            $('.part-section').removeClass('active').attr('hidden', 'true');
            targetSection.addClass('active').removeAttr('hidden');
        }

        /**
         * Handle view changes
         */
        handleViewChange(e) {
            e.preventDefault();
            const $control = $(e.currentTarget);
            const view = $control.data('view');

            if (!view || view === this.state.currentView) return;

            this.elements.viewControls.removeClass('active');
            $control.addClass('active');

            this.state.currentView = view;
            this.createWatchModel2D();
        }

        /**
         * Handle window resize
         */
        handleResize() {
            // Handle any resize logic here
        }

        /**
         * Utility function to capitalize first letter
         */
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        /**
         * Debounce utility function
         */
        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        /**
         * Cleanup function
         */
        cleanup() {
            // Cleanup logic here
        }
    }

    // Initialize when DOM is ready
    $(document).ready(() => {
        // Only initialize if we're on the builder page
        if ($('.builder-interface').length) {
            try {
                window.watchBuilder = new WatchBuilder();
                console.log('Watch Builder initialized');
            } catch (error) {
                console.error('Failed to initialize Watch Builder:', error);
            }
        }
    });

})(jQuery);