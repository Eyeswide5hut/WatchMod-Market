/**
 * Enhanced Watch Builder functionality for WatchModMarket
 * Improvements: Better error handling, performance optimization, modern JS features
 */
(function ($) {
    "use strict";

    // Watch Builder Class with improvements
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

            // 3D Elements
            this.canvas = null;
            this.ctx = null;
            this.scene = null;
            this.camera = null;
            this.renderer = null;
            this.controls = null;
            this.currentModel = null;
            this.animationFrameId = null;

            // Performance tracking
            this.lastUpdateTime = 0;
            this.updateThrottle = 100; // ms

            // Cache DOM elements for better performance
            this.elements = this.cacheElements();

            // Settings with better defaults
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
                canvas: $('#watch-3d-render')[0]
            };
        }

        /**
         * Get settings with better validation
         */
        getSettings() {
            const wpData = window.wpData || {};
            return {
                ajaxUrl: wpData.ajaxUrl || '/wp-admin/admin-ajax.php',
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
                confirmIncompatible: 'There are compatibility warnings. Continue anyway?'
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
                .on('submit', '#add-build-to-cart', this.handleFormSubmission.bind(this))
                .on('click', '#save-build', this.handleSaveBuild.bind(this));

            // Window events
            $(window).on('beforeunload', this.cleanup.bind(this));
            $(window).on('resize', this.debounce(this.handleResize.bind(this), 250));
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
         * Handle part selection with throttling
         */
        handlePartSelection(e) {
            e.preventDefault();

            // Throttle updates for performance
            const now = Date.now();
            if (now - this.lastUpdateTime < this.updateThrottle) {
                return;
            }
            this.lastUpdateTime = now;

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

            // Batch updates for better performance
            this.batchUpdate();
        }

        /**
         * Handle view changes
         */
        handleViewChange(e) {
            e.preventDefault();
            const $control = $(e.currentTarget);
            const view = $control.data('view');

            if (!view || view === this.state.currentView) return;

            // Update control state
            this.elements.viewControls.removeClass('active');
            $control.addClass('active');

            // Update current view
            this.state.currentView = view;

            // Update view angle
            this.updateViewAngle();

            // Update 2D canvas if needed
            if (!window.THREE) {
                this.createWatchModel2D();
            }
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
         * Initialize renderer with better error handling
         */
        async initializeRenderer() {
            if (!this.elements.canvas) {
                throw new Error('Canvas element not found');
            }

            try {
                if (window.THREE && this.isWebGLSupported()) {
                    await this.initThreeJS();
                } else {
                    this.initCanvas();
                    this.createWatchModel2D();
                }
            } catch (error) {
                console.warn('3D rendering failed, falling back to 2D:', error);
                this.initCanvas();
                this.createWatchModel2D();
            }
        }

        /**
         * Check WebGL support
         */
        isWebGLSupported() {
            try {
                const canvas = document.createElement('canvas');
                return !!(window.WebGLRenderingContext &&
                    (canvas.getContext('webgl') || canvas.getContext('experimental-webgl')));
            } catch (e) {
                return false;
            }
        }

        /**
         * Enhanced Three.js initialization
         */
        async initThreeJS() {
            try {
                // Create scene with better settings
                this.scene = new THREE.Scene();
                this.scene.background = new THREE.Color(0xF5F5F5);
                this.scene.fog = new THREE.Fog(0xF5F5F5, 50, 100);

                // Create camera with responsive aspect ratio
                const aspect = this.elements.canvas.clientWidth / this.elements.canvas.clientHeight;
                this.camera = new THREE.PerspectiveCamera(45, aspect, 0.1, 1000);
                this.camera.position.set(0, 0, 10);

                // Create renderer with better settings
                this.renderer = new THREE.WebGLRenderer({
                    canvas: this.elements.canvas,
                    antialias: true,
                    alpha: true,
                    powerPreference: 'high-performance'
                });

                this.renderer.setSize(
                    this.elements.canvas.clientWidth,
                    this.elements.canvas.clientHeight
                );
                this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                this.renderer.shadowMap.enabled = true;
                this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;

                // Enhanced lighting setup
                this.setupLighting();

                // Add controls if available
                if (window.THREE.OrbitControls) {
                    this.setupControls();
                }

                // Start animation loop
                this.animate();

                // Create initial model
                this.updateWatchModel();

            } catch (error) {
                console.error('Three.js initialization failed:', error);
                throw error;
            }
        }

        /**
         * Setup enhanced lighting
         */
        setupLighting() {
            // Ambient light
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
            this.scene.add(ambientLight);

            // Main directional light
            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(5, 5, 5);
            directionalLight.castShadow = true;
            directionalLight.shadow.mapSize.width = 2048;
            directionalLight.shadow.mapSize.height = 2048;
            directionalLight.shadow.camera.near = 0.5;
            directionalLight.shadow.camera.far = 50;
            this.scene.add(directionalLight);

            // Fill light
            const fillLight = new THREE.DirectionalLight(0xffffff, 0.3);
            fillLight.position.set(-5, -5, -5);
            this.scene.add(fillLight);
        }

        /**
         * Setup enhanced controls
         */
        setupControls() {
            this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
            this.controls.enableDamping = true;
            this.controls.dampingFactor = 0.05;
            this.controls.rotateSpeed = 0.7;
            this.controls.enableZoom = true;
            this.controls.enablePan = false;
            this.controls.autoRotate = (this.state.currentView === '3d');
            this.controls.autoRotateSpeed = 2.0;
            this.controls.minDistance = 5;
            this.controls.maxDistance = 20;
        }

        /**
         * Enhanced animation loop with performance monitoring
         */
        animate() {
            this.animationFrameId = requestAnimationFrame(this.animate.bind(this));

            if (this.controls) {
                this.controls.update();
            }

            if (this.renderer && this.scene && this.camera) {
                this.renderer.render(this.scene, this.camera);
            }
        }

        /**
         * Enhanced compatibility checking with better logic
         */
        checkCompatibility() {
            this.state.compatibilityWarnings = [];

            const parts = this.state.selectedParts;

            // Check case and movement compatibility
            this.checkCaseMovementCompatibility(parts);

            // Check case and dial size compatibility
            this.checkCaseDialCompatibility(parts);

            // Check hands and movement compatibility
            this.checkHandsMovementCompatibility(parts);

            // Update compatibility display
            this.updateCompatibilityWarnings();
        }

        /**
         * Check case and movement compatibility
         */
        checkCaseMovementCompatibility(parts) {
            const caseId = parts.get('case');
            const movementId = parts.get('movement');

            if (!caseId || !movementId) return;

            const $caseElement = $(`[data-part-id="${caseId}"]`);
            const compatibility = $caseElement.data('compatibility');

            if (compatibility && typeof compatibility === 'string' &&
                !compatibility.includes(movementId.split('-')[1])) {
                this.state.compatibilityWarnings.push({
                    parts: ['case', 'movement'],
                    message: this.settings.i18n.incompatibleMovement,
                    severity: 'error'
                });
            }
        }

        /**
         * Check case and dial size compatibility
         */
        checkCaseDialCompatibility(parts) {
            const caseId = parts.get('case');
            const dialId = parts.get('dial');

            if (!caseId || !dialId) return;

            const $caseElement = $(`[data-part-id="${caseId}"]`);
            const $dialElement = $(`[data-part-id="${dialId}"]`);

            const caseDiameter = parseFloat($caseElement.data('diameter'));
            const dialDiameter = parseFloat($dialElement.data('diameter'));

            if (caseDiameter && dialDiameter && caseDiameter < dialDiameter) {
                this.state.compatibilityWarnings.push({
                    parts: ['case', 'dial'],
                    message: this.settings.i18n.dialTooBig,
                    severity: 'error'
                });
            }
        }

        /**
         * Enhanced pricing update with validation
         */
        updatePricing() {
            let totalPrice = 0;

            this.state.selectedParts.forEach((partId, partType) => {
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
         * Enhanced form submission with better validation
         */
        handleFormSubmission(e) {
            e.preventDefault();

            // Validate required parts
            const requiredParts = ['case', 'dial', 'hands', 'strap', 'movement'];
            const missingParts = requiredParts.filter(part => !this.state.selectedParts.get(part));

            if (missingParts.length > 0) {
                this.showError(`${this.settings.i18n.selectAllParts}: ${missingParts.join(', ')}`);
                return false;
            }

            // Check compatibility warnings
            if (this.state.compatibilityWarnings.length > 0) {
                if (!confirm(this.settings.i18n.confirmIncompatible)) {
                    return false;
                }
            }

            // Add selected parts to form
            this.addPartsToForm(e.target);

            return true;
        }

        /**
         * Add selected parts to form as hidden inputs
         */
        addPartsToForm(form) {
            this.state.selectedParts.forEach((partId, partType) => {
                if (partId) {
                    const hiddenInput = $(`<input type="hidden" name="selected_${partType}" value="${partId.split('-')[1]}">`);
                    $(form).append(hiddenInput);
                }
            });
        }

        /**
         * Enhanced save build functionality
         */
        async handleSaveBuild(e) {
            e.preventDefault();

            if (!this.settings.isLoggedIn) {
                this.showError(this.settings.i18n.loginRequired);
                setTimeout(() => {
                    window.location.href = this.settings.loginUrl;
                }, 2000);
                return;
            }

            // Check if any parts are selected
            const hasSelectedParts = Array.from(this.state.selectedParts.values()).some(part => part !== null);

            if (!hasSelectedParts) {
                this.showError(this.settings.i18n.selectSomeParts);
                return;
            }

            try {
                await this.openSaveDialog();
            } catch (error) {
                console.error('Save build error:', error);
                this.showError(this.settings.i18n.saveFailed);
            }
        }

        /**
         * Handle window resize
         */
        handleResize() {
            if (this.camera && this.renderer && this.elements.canvas) {
                const width = this.elements.canvas.clientWidth;
                const height = this.elements.canvas.clientHeight;

                this.camera.aspect = width / height;
                this.camera.updateProjectionMatrix();
                this.renderer.setSize(width, height);
            }
        }

        /**
         * Show error message to user
         */
        showError(message) {
            // Create or update error display
            let errorDiv = $('.builder-error');
            if (!errorDiv.length) {
                errorDiv = $('<div class="builder-error"></div>');
                $('.builder-container').prepend(errorDiv);
            }

            errorDiv.text(message).addClass('show');

            // Auto-hide after 5 seconds
            setTimeout(() => {
                errorDiv.removeClass('show');
            }, 5000);
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
         * Enhanced cleanup with memory management
         */
        cleanup() {
            // Cancel animation frame
            if (this.animationFrameId) {
                cancelAnimationFrame(this.animationFrameId);
                this.animationFrameId = null;
            }

            // Dispose Three.js resources
            if (this.scene) {
                this.disposeScene();
            }

            if (this.renderer) {
                this.renderer.dispose();
                this.renderer = null;
            }

            // Clear references
            this.camera = null;
            this.controls = null;
            this.currentModel = null;
        }

        /**
         * Dispose Three.js scene objects
         */
        disposeScene() {
            if (!this.scene) return;

            this.scene.traverse((object) => {
                if (object.geometry) {
                    object.geometry.dispose();
                }
                if (object.material) {
                    if (Array.isArray(object.material)) {
                        object.material.forEach(material => material.dispose());
                    } else {
                        object.material.dispose();
                    }
                }
            });

            this.scene.clear();
            this.scene = null;
        }

        // Placeholder methods for missing functionality
        checkSelectedPartsOnLoad() {
            // Implementation for checking pre-selected parts
        }

        updateWatchModel() {
            // Implementation for updating 3D/2D model
        }

        updateViewAngle() {
            // Implementation for updating camera angle
        }

        createWatchModel2D() {
            // Implementation for 2D canvas rendering
        }

        initCanvas() {
            // Implementation for canvas initialization
        }

        updateCompatibilityWarnings() {
            // Implementation for displaying compatibility warnings
        }

        async openSaveDialog() {
            // Implementation for save dialog
        }
    }

    // Initialize when DOM is ready
    $(document).ready(() => {
        // Only initialize if we're on the builder page
        if ($('.builder-interface').length) {
            try {
                new WatchBuilder();
            } catch (error) {
                console.error('Failed to initialize Watch Builder:', error);
            }
        }
    });

})(jQuery);