<?php
/**
 * Template Name: Watch Builder
 * 
 * The template for displaying the interactive watch builder page
 */

get_header();
?>

<div class="builder-interface">
    <div class="container">
        <h1><?php the_title(); ?></h1>
        <p class="builder-intro"><?php echo esc_html__('Create your perfect timepiece by selecting and combining different watch components. Our interactive tool lets you visualize your design in real-time.', 'watchmodmarket'); ?></p>

        <div class="builder-container">
            <!-- Parts Selection Panel -->
            <div class="parts-panel">
                <div class="parts-panel-header">
                    <h3><?php echo esc_html__('Watch Parts', 'watchmodmarket'); ?></h3>
                </div>
                <div class="parts-tabs" role="tablist">
                    <button class="parts-tab active" id="tab-cases" role="tab" aria-selected="true"
                        aria-controls="section-cases"><?php echo esc_html__('Cases', 'watchmodmarket'); ?></button>
                    <button class="parts-tab" id="tab-dials" role="tab" aria-selected="false"
                        aria-controls="section-dials"><?php echo esc_html__('Dials', 'watchmodmarket'); ?></button>
                    <button class="parts-tab" id="tab-hands" role="tab" aria-selected="false"
                        aria-controls="section-hands"><?php echo esc_html__('Hands', 'watchmodmarket'); ?></button>
                    <button class="parts-tab" id="tab-straps" role="tab" aria-selected="false"
                        aria-controls="section-straps"><?php echo esc_html__('Straps', 'watchmodmarket'); ?></button>
                    <button class="parts-tab" id="tab-movements" role="tab" aria-selected="false"
                        aria-controls="section-movements"><?php echo esc_html__('Movements', 'watchmodmarket'); ?></button>
                </div>
                <div class="parts-content">
                    <!-- Parts Sections -->
                    
                    <!-- Cases Section -->
                    <div class="part-section active" id="section-cases" role="tabpanel" aria-labelledby="tab-cases">
                        <?php
                        // Get case products
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => 'cases',
                                ),
                            ),
                        );
                        
                        $cases_query = new WP_Query($args);
                        
                        if ($cases_query->have_posts()) {
                            $first = true;
                            while ($cases_query->have_posts()) {
                                $cases_query->the_post();
                                global $product;
                                // Get case diameter from product meta
                                $case_diameter = get_post_meta($product->get_id(), '_case_diameter', true);
                                // Get compatibility with other movements
                                $compatible_movements = get_post_meta($product->get_id(), '_compatible_movements', true);
                                ?>
                                <div class="part-item <?php echo $first ? 'selected' : ''; ?>" 
                                     data-part-id="case-<?php echo esc_attr($product->get_id()); ?>"
                                     data-diameter="<?php echo esc_attr($case_diameter); ?>"
                                     data-compatibility="<?php echo esc_attr($compatible_movements); ?>"
                                     data-price="<?php echo esc_attr($product->get_price()); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('thumbnail', array('class' => 'part-thumbnail', 'loading' => 'lazy', 'alt' => get_the_title()));
                                    }
                                    ?>
                                    <div class="part-details">
                                        <div class="part-name"><?php the_title(); ?></div>
                                        <div class="part-specs">
                                            <?php 
                                            if ($case_diameter) {
                                                echo esc_html__('Diameter: ', 'watchmodmarket') . esc_html($case_diameter) . 'mm';
                                            } else {
                                                echo esc_html($product->get_short_description());
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="part-price"><?php echo $product->get_price_html(); ?></div>
                                </div>
                                <?php
                                $first = false;
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p>' . esc_html__('No case products found.', 'watchmodmarket') . '</p>';
                        }
                        ?>
                    </div>

                    <!-- Dials Section -->
                    <div class="part-section" id="section-dials" role="tabpanel" aria-labelledby="tab-dials">
                        <?php
                        // Get dial products
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => 'dials',
                                ),
                            ),
                        );
                        
                        $dials_query = new WP_Query($args);
                        
                        if ($dials_query->have_posts()) {
                            $first = true;
                            while ($dials_query->have_posts()) {
                                $dials_query->the_post();
                                global $product;
                                // Get dial diameter
                                $dial_diameter = get_post_meta($product->get_id(), '_dial_diameter', true);
                                ?>
                                <div class="part-item <?php echo $first ? 'selected' : ''; ?>" 
                                     data-part-id="dial-<?php echo esc_attr($product->get_id()); ?>"
                                     data-diameter="<?php echo esc_attr($dial_diameter); ?>"
                                     data-price="<?php echo esc_attr($product->get_price()); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('thumbnail', array('class' => 'part-thumbnail', 'loading' => 'lazy', 'alt' => get_the_title()));
                                    }
                                    ?>
                                    <div class="part-details">
                                        <div class="part-name"><?php the_title(); ?></div>
                                        <div class="part-specs">
                                            <?php 
                                            if ($dial_diameter) {
                                                echo esc_html__('Diameter: ', 'watchmodmarket') . esc_html($dial_diameter) . 'mm';
                                            } else {
                                                echo esc_html($product->get_short_description());
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="part-price"><?php echo $product->get_price_html(); ?></div>
                                </div>
                                <?php
                                $first = false;
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p>' . esc_html__('No dial products found.', 'watchmodmarket') . '</p>';
                        }
                        ?>
                    </div>

                    <!-- Hands Section -->
                    <div class="part-section" id="section-hands" role="tabpanel" aria-labelledby="tab-hands">
                        <?php
                        // Get hands products
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => 'hands',
                                ),
                            ),
                        );
                        
                        $hands_query = new WP_Query($args);
                        
                        if ($hands_query->have_posts()) {
                            $first = true;
                            while ($hands_query->have_posts()) {
                                $hands_query->the_post();
                                global $product;
                                // Get hands compatibility info
                                $compatible_movements = get_post_meta($product->get_id(), '_compatible_movements', true);
                                ?>
                                <div class="part-item <?php echo $first ? 'selected' : ''; ?>" 
                                     data-part-id="hands-<?php echo esc_attr($product->get_id()); ?>"
                                     data-compatibility="<?php echo esc_attr($compatible_movements); ?>"
                                     data-price="<?php echo esc_attr($product->get_price()); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('thumbnail', array('class' => 'part-thumbnail', 'loading' => 'lazy', 'alt' => get_the_title()));
                                    }
                                    ?>
                                    <div class="part-details">
                                        <div class="part-name"><?php the_title(); ?></div>
                                        <div class="part-specs"><?php echo esc_html($product->get_short_description()); ?></div>
                                    </div>
                                    <div class="part-price"><?php echo $product->get_price_html(); ?></div>
                                </div>
                                <?php
                                $first = false;
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p>' . esc_html__('No hands products found.', 'watchmodmarket') . '</p>';
                        }
                        ?>
                    </div>

                    <!-- Straps Section -->
                    <div class="part-section" id="section-straps" role="tabpanel" aria-labelledby="tab-straps">
                        <?php
                        // Get strap products
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => 'straps',
                                ),
                            ),
                        );
                        
                        $straps_query = new WP_Query($args);
                        
                        if ($straps_query->have_posts()) {
                            $first = true;
                            while ($straps_query->have_posts()) {
                                $straps_query->the_post();
                                global $product;
                                // Get strap width
                                $strap_width = get_post_meta($product->get_id(), '_strap_width', true);
                                ?>
                                <div class="part-item <?php echo $first ? 'selected' : ''; ?>" 
                                     data-part-id="strap-<?php echo esc_attr($product->get_id()); ?>"
                                     data-width="<?php echo esc_attr($strap_width); ?>"
                                     data-price="<?php echo esc_attr($product->get_price()); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('thumbnail', array('class' => 'part-thumbnail', 'loading' => 'lazy', 'alt' => get_the_title()));
                                    }
                                    ?>
                                    <div class="part-details">
                                        <div class="part-name"><?php the_title(); ?></div>
                                        <div class="part-specs">
                                            <?php 
                                            if ($strap_width) {
                                                echo esc_html__('Width: ', 'watchmodmarket') . esc_html($strap_width) . 'mm';
                                            } else {
                                                echo esc_html($product->get_short_description());
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="part-price"><?php echo $product->get_price_html(); ?></div>
                                </div>
                                <?php
                                $first = false;
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p>' . esc_html__('No strap products found.', 'watchmodmarket') . '</p>';
                        }
                        ?>
                    </div>

                    <!-- Movements Section -->
                    <div class="part-section" id="section-movements" role="tabpanel" aria-labelledby="tab-movements">
                        <?php
                        // Get movement products
                        $args = array(
                            'post_type' => 'product',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'slug',
                                    'terms' => 'movements',
                                ),
                            ),
                        );
                        
                        $movements_query = new WP_Query($args);
                        
                        if ($movements_query->have_posts()) {
                            $first = true;
                            while ($movements_query->have_posts()) {
                                $movements_query->the_post();
                                global $product;
                                // Get movement specs
                                $movement_type = get_post_meta($product->get_id(), '_movement_type', true);
                                ?>
                                <div class="part-item <?php echo $first ? 'selected' : ''; ?>" 
                                     data-part-id="movement-<?php echo esc_attr($product->get_id()); ?>"
                                     data-movement-type="<?php echo esc_attr($movement_type); ?>"
                                     data-price="<?php echo esc_attr($product->get_price()); ?>">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('thumbnail', array('class' => 'part-thumbnail', 'loading' => 'lazy', 'alt' => get_the_title()));
                                    }
                                    ?>
                                    <div class="part-details">
                                        <div class="part-name"><?php the_title(); ?></div>
                                        <div class="part-specs"><?php echo esc_html($product->get_short_description()); ?></div>
                                    </div>
                                    <div class="part-price"><?php echo $product->get_price_html(); ?></div>
                                </div>
                                <?php
                                $first = false;
                            }
                            wp_reset_postdata();
                        } else {
                            echo '<p>' . esc_html__('No movement products found.', 'watchmodmarket') . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Watch Preview Area -->
            <div class="watch-preview">
                <div class="preview-controls">
                    <button class="view-control active" data-view="front" aria-label="<?php echo esc_attr__('View watch from front', 'watchmodmarket'); ?>"><?php echo esc_html__('Front View', 'watchmodmarket'); ?></button>
                    <button class="view-control" data-view="side" aria-label="<?php echo esc_attr__('View watch from side', 'watchmodmarket'); ?>"><?php echo esc_html__('Side View', 'watchmodmarket'); ?></button>
                    <button class="view-control" data-view="back" aria-label="<?php echo esc_attr__('View watch from back', 'watchmodmarket'); ?>"><?php echo esc_html__('Back View', 'watchmodmarket'); ?></button>
                    <button class="view-control" data-view="3d" aria-label="<?php echo esc_attr__('View watch in 3D', 'watchmodmarket'); ?>"><?php echo esc_html__('3D View', 'watchmodmarket'); ?></button>
                </div>

                <div class="preview-container">
                    <div class="watch-canvas" id="watch-preview-canvas">
                        <canvas id="watch-3d-render" width="400" height="400" aria-label="<?php echo esc_attr__('3D watch preview', 'watchmodmarket'); ?>"></canvas>
                        <div id="loading-indicator" class="loading-indicator"><?php echo esc_html__('Loading Preview...', 'watchmodmarket'); ?></div>
                        <noscript>
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/watch-placeholder.jpg" alt="<?php echo esc_attr__('Watch preview placeholder', 'watchmodmarket'); ?>" width="400" height="400">
                            <p><?php echo esc_html__('JavaScript is required for the interactive watch preview. Enable JavaScript or view our', 'watchmodmarket'); ?> <a href="<?php echo esc_url(get_permalink(get_page_by_path('catalog'))); ?>"><?php echo esc_html__('parts catalog', 'watchmodmarket'); ?></a>.</p>
                        </noscript>
                    </div>
                </div>

                <div class="compatibility-alert" id="compatibility-alert">
                    <h4><?php echo esc_html__('Compatibility Warning', 'watchmodmarket'); ?></h4>
                    <p><?php echo esc_html__('Some selected parts may not be fully compatible. Please check the specifications or contact customer support for assistance.', 'watchmodmarket'); ?></p>
                </div>

                <!-- Build Details -->
                <div class="preview-details">
                    <h3><?php echo esc_html__('Build Specifications', 'watchmodmarket'); ?></h3>
                    <table class="spec-table">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__('Component', 'watchmodmarket'); ?></th>
                                <th><?php echo esc_html__('Selected Part', 'watchmodmarket'); ?></th>
                                <th><?php echo esc_html__('Specifications', 'watchmodmarket'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                    
                    <div class="price-summary">
                        <div class="label"><?php echo esc_html__('Total Price:', 'watchmodmarket'); ?></div>
                        <div class="total" id="total-price">$0.00</div>
                    </div>
                    
                    <div class="action-buttons">
                        <button id="save-build" class="btn btn-secondary"><?php echo esc_html__('Save Build', 'watchmodmarket'); ?></button>
                        
                        <form id="add-build-to-cart" method="post" action="<?php echo esc_url(wc_get_cart_url()); ?>">
                            <input type="hidden" id="build-total-price" name="build_total_price" value="0">
                            <!-- Hidden fields for selected parts will be added by JavaScript -->
                            <button type="submit" class="btn btn-primary"><?php echo esc_html__('Add to Cart', 'watchmodmarket'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Build Modal -->
<div id="save-build-modal" class="builder-modal" role="dialog" aria-labelledby="save-build-title" aria-hidden="true" style="display: none;">
    <div class="builder-modal-content">
        <span class="builder-modal-close" aria-label="<?php echo esc_attr__('Close', 'watchmodmarket'); ?>">&times;</span>
        <h3 id="save-build-title"><?php echo esc_html__('Save Your Build', 'watchmodmarket'); ?></h3>
        <form id="save-build-form">
            <div class="form-group">
                <label for="build-name"><?php echo esc_html__('Build Name', 'watchmodmarket'); ?></label>
                <input type="text" id="build-name" name="build_name" required>
            </div>
            <div class="form-group">
                <label for="build-description"><?php echo esc_html__('Description (Optional)', 'watchmodmarket'); ?></label>
                <textarea id="build-description" name="build_description"></textarea>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="build_public" value="1">
                    <?php echo esc_html__('Make this build public in the community', 'watchmodmarket'); ?>
                </label>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo esc_html__('Save Build', 'watchmodmarket'); ?></button>
        </form>
    </div>
</div>

</main>

<?php get_footer(); ?>

<!-- Builder JavaScript -->
<script>
(function($) {
    'use strict';

    // Watch Builder Class
    class WatchBuilder {
        constructor() {
            // State
            this.state = {
                currentView: 'front',
                selectedParts: {
                    case: '',
                    dial: '',
                    hands: '',
                    strap: '',
                    movement: ''
                },
                compatibilityWarnings: [],
                isLoading: true
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

            // DOM Elements
            this.elements = {
                loading: $('#loading-indicator'),
                tabs: $('.parts-tab'),
                partItems: $('.part-item'),
                viewControls: $('.view-control'),
                compatAlert: $('#compatibility-alert'),
                totalPrice: $('#total-price'),
                saveButton: $('#save-build'),
                buildForm: $('#add-build-to-cart')
            };

            // Settings
            this.settings = {
                ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
                nonce: '<?php echo wp_create_nonce('watch_builder_nonce'); ?>',
                isLoggedIn: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>,
                loginUrl: '<?php echo esc_url(wp_login_url(get_permalink())); ?>',
                currencySymbol: '<?php echo get_woocommerce_currency_symbol(); ?>',
                i18n: {
                    incompatibleMovement: '<?php echo esc_js(__('The selected movement is not compatible with this case.', 'watchmodmarket')); ?>',
                    dialTooBig: '<?php echo esc_js(__('The selected dial is too large for this case.', 'watchmodmarket')); ?>',
                    incompatibleHands: '<?php echo esc_js(__('The selected hands are not compatible with this movement.', 'watchmodmarket')); ?>',
                    loginRequired: '<?php echo esc_js(__('You must be logged in to save builds. Please log in or create an account.', 'watchmodmarket')); ?>',
                    selectSomeParts: '<?php echo esc_js(__('Please select at least one part to save a build.', 'watchmodmarket')); ?>',
                    saveBuild: '<?php echo esc_js(__('Save Your Build', 'watchmodmarket')); ?>',
                    buildName: '<?php echo esc_js(__('Build Name', 'watchmodmarket')); ?>',
                    buildDescription: '<?php echo esc_js(__('Description (Optional)', 'watchmodmarket')); ?>',
                    makePublic: '<?php echo esc_js(__('Make this build public in the community', 'watchmodmarket')); ?>',
                    saveButton: '<?php echo esc_js(__('Save Build', 'watchmodmarket')); ?>',
                    saving: '<?php echo esc_js(__('Saving...', 'watchmodmarket')); ?>',
                    buildSaved: '<?php echo esc_js(__('Your build has been saved successfully!', 'watchmodmarket')); ?>',
                    saveFailed: '<?php echo esc_js(__('Failed to save your build. Please try again.', 'watchmodmarket')); ?>'
                }
            };

            // Initialize
            this.init();
        }

        /**
         * Initialize the builder
         */
        init() {
            // Setup event listeners
            this.setupTabs();
            this.setupPartSelection();
            this.setupViewControls();
            this.setupFormSubmission();
            this.setupSaveButton();

            // Initialize rendering
            if (window.THREE) {
                this.initThreeJS();
            } else {
                this.initCanvas();
                this.createWatchModel2D();
            }

            // Initial update and check
            this.updatePricing();
            this.checkSelectedPartsOnLoad();

            // Hide loading after initialization
            setTimeout(() => {
                this.state.isLoading = false;
                this.elements.loading.fadeOut();
            }, 1000);
        }

        /**
         * Check for pre-selected parts on page load
         */
        checkSelectedPartsOnLoad() {
            // Get initially selected parts
            $('.part-item.selected').each((_, item) => {
                const partId = $(item).data('part-id');
                if (partId) {
                    const partType = partId.split('-')[0];
                    this.state.selectedParts[partType] = partId;
                }
            });

            // Run compatibility check
            this.checkCompatibility();

            // Update the watch model
            if (window.THREE) {
                this.updateWatchModel();
            } else {
                this.createWatchModel2D();
            }
        }

        /**
         * Setup tabs in parts panel
         */
        setupTabs() {
            this.elements.tabs.on('click', (e) => {
                const $tab = $(e.currentTarget);
                const tabId = $tab.attr('id');
                const targetSection = $('#' + tabId.replace('tab-', 'section-'));

                // Update tab state
                this.elements.tabs.removeClass('active').attr('aria-selected', 'false');
                $tab.addClass('active').attr('aria-selected', 'true');

                // Update section visibility
                $('.part-section').removeClass('active').attr('hidden', 'true');
                targetSection.addClass('active').removeAttr('hidden');
            });
        }

        /**
         * Setup part selection
         */
        setupPartSelection() {
            this.elements.partItems.on('click', (e) => {
                const $item = $(e.currentTarget);
                const partId = $item.data('part-id');

                if (!partId) return;

                const partType = partId.split('-')[0]; // e.g., "case", "dial", etc.

                // Update selection in UI
                $item.siblings('.part-item').removeClass('selected');
                $item.addClass('selected');

                // Store selected part
                this.state.selectedParts[partType] = partId;

                // Check compatibility
                this.checkCompatibility();

                // Update 3D model
                if (window.THREE) {
                    this.updateWatchModel();
                } else {
                    this.createWatchModel2D();
                }

                // Update pricing
                this.updatePricing();
                
                // Update spec table
                this.updateSpecTable();
            });
        }

        /**
         * Setup view controls
         */
        setupViewControls() {
            this.elements.viewControls.on('click', (e) => {
                const $control = $(e.currentTarget);
                const view = $control.data('view');

                // Update control state
                this.elements.viewControls.removeClass('active');
                $control.addClass('active');

                // Update current view
                this.state.currentView = view;

                // Update view angle
                this.updateViewAngle();

                // If 2D rendering, update the canvas
                if (!window.THREE) {
                    this.createWatchModel2D();
                }
            });
        }

        /**
         * Initialize canvas for 2D fallback
         */
        initCanvas() {
            this.canvas = document.getElementById('watch-3d-render');
            if (!this.canvas) {
                console.error('Canvas element not found');
                return;
            }

            this.ctx = this.canvas.getContext('2d');

            // Ensure the canvas is visible
            $(this.canvas).css('display', 'block');
        }

        /**
         * Initialize Three.js for 3D rendering
         */
        initThreeJS() {
            try {
                // Create scene
                this.scene = new THREE.Scene();
                this.scene.background = new THREE.Color(0xEEEEEE);

                // Get canvas
                this.canvas = document.getElementById('watch-3d-render');
                if (!this.canvas) {
                    console.error('Canvas element not found');
                    return;
                }

                // Create camera
                this.camera = new THREE.PerspectiveCamera(45, this.canvas.width / this.canvas.height, 0.1, 1000);
                this.camera.position.set(0, 0, 10);

                // Create renderer
                this.renderer = new THREE.WebGLRenderer({
                    canvas: this.canvas,
                    antialias: true,
                    alpha: true
                });
                this.renderer.setSize(this.canvas.width, this.canvas.height);
                this.renderer.shadowMap.enabled = true;

                // Add lighting
                const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
                this.scene.add(ambientLight);

                const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
                directionalLight.position.set(1, 1, 1);
                directionalLight.castShadow = true;
                this.scene.add(directionalLight);

                // Add orbit controls if available
                if (THREE.OrbitControls) {
                    this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
                    this.controls.enableDamping = true;
                    this.controls.dampingFactor = 0.05;
                    this.controls.rotateSpeed = 0.7;
                    this.controls.enableZoom = true;
                    this.controls.enablePan = false;
                    this.controls.autoRotate = (this.state.currentView === '3d');
                    this.controls.autoRotateSpeed = 1.0;
                }

                // Start animation loop
                this.animate();

                // Create initial model
                this.updateWatchModel();
            } catch (error) {
                console.error('Error initializing Three.js:', error);
                // Fall back to 2D canvas
                this.initCanvas();
                this.createWatchModel2D();
            }
        }

        /**
         * Animation loop for Three.js
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
         * Update the watch model based on selected parts
         */
        updateWatchModel() {
            if (this.state.isLoading) return;

            // Show loading indicator
            this.elements.loading.fadeIn();

            // Clear previous model
            if (this.scene && this.currentModel) {
                this.scene.remove(this.currentModel);
            }

            // Create new watch model based on selected parts
            if (window.THREE) {
                this.createWatchModel3D();
            } else {
                this.createWatchModel2D();
            }

            // Update view angle
            this.updateViewAngle();

            // Hide loading indicator
            setTimeout(() => {
                this.elements.loading.fadeOut();
            }, 500);
        }

        /**
         * Create a 3D watch model using Three.js
         * Note: This is a simplified version that creates basic shapes
         */
        createWatchModel3D() {
            // Create new group for the watch model
            this.currentModel = new THREE.Group();

            // Create basic shapes for different parts
            // This is a simplified example - in a real application you would load actual 3D models

            // Case - Simple cylinder
            const caseGeometry = new THREE.CylinderGeometry(4, 4, 1, 32);
            const caseMaterial = new THREE.MeshStandardMaterial({ color: 0x888888 });
            const caseMesh = new THREE.Mesh(caseGeometry, caseMaterial);
            caseMesh.rotation.x = Math.PI * 0.5;
            this.currentModel.add(caseMesh);

            // Dial - Simple circle
            const dialGeometry = new THREE.CircleGeometry(3.5, 32);
            const dialMaterial = new THREE.MeshStandardMaterial({ color: 0xFFFFFF });
            const dialMesh = new THREE.Mesh(dialGeometry, dialMaterial);
            dialMesh.position.z = 0.1;
            this.currentModel.add(dialMesh);

            // Hands - Simple boxes
            const hourHandGeo = new THREE.BoxGeometry(0.2, 2, 0.1);
            const minuteHandGeo = new THREE.BoxGeometry(0.2, 3, 0.1);
            const handMaterial = new THREE.MeshStandardMaterial({ color: 0x000000 });

            const hourHand = new THREE.Mesh(hourHandGeo, handMaterial);
            hourHand.position.y = 1;

            const minuteHand = new THREE.Mesh(minuteHandGeo, handMaterial);
            minuteHand.position.y = 1.5;
            minuteHand.rotation.z = Math.PI * 0.25;

            const handsGroup = new THREE.Group();
            handsGroup.add(hourHand);
            handsGroup.add(minuteHand);
            handsGroup.position.z = 0.2;

            this.currentModel.add(handsGroup);

            // Strap - Simple box
            const strapGeometry = new THREE.BoxGeometry(2, 8, 0.5);
            const strapMaterial = new THREE.MeshStandardMaterial({ color: 0x000000 });
            const strapMesh = new THREE.Mesh(strapGeometry, strapMaterial);
            strapMesh.position.y = -2;

            this.currentModel.add(strapMesh);

            // Add the combined model to the scene
            this.scene.add(this.currentModel);
        }

        /**
         * Create a 2D watch model using canvas fallback
         */
        createWatchModel2D() {
            if (!this.ctx || !this.canvas) return;

            // Clear canvas
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

            // Set center point
            const centerX = this.canvas.width / 2;
            const centerY = this.canvas.height / 2;

            // Draw based on current view
            switch (this.state.currentView) {
                case 'front':
                    this.drawWatchFront(centerX, centerY);
                    break;
                case 'side':
                    this.drawWatchSide(centerX, centerY);
                    break;
                case 'back':
                    this.drawWatchBack(centerX, centerY);
                    break;
                case '3d':
                    // In 2D mode, just show front view for "3D" option
                    this.drawWatchFront(centerX, centerY);
                    break;
            }
        }

        /**
         * Draw front view of watch in 2D
         */
        drawWatchFront(centerX, centerY) {
            // Basic drawing code for front view
            // Draw case
            this.ctx.strokeStyle = '#000000';
            this.ctx.lineWidth = 3;
            this.ctx.beginPath();
            this.ctx.arc(centerX, centerY, 150, 0, Math.PI * 2);
            this.ctx.stroke();
            this.ctx.fillStyle = '#FFFFFF';
            this.ctx.fill();

            // Draw watch face markers
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
            // Hour hand
            this.ctx.strokeStyle = '#000000';
            this.ctx.lineWidth = 4;
            this.ctx.beginPath();
            this.ctx.moveTo(centerX, centerY);
            this.ctx.lineTo(centerX + 60, centerY - 40);
            this.ctx.stroke();
            
            // Minute hand
            this.ctx.strokeStyle = '#000000';
            this.ctx.lineWidth = 2;
            this.ctx.beginPath();
            this.ctx.moveTo(centerX, centerY);
            this.ctx.lineTo(centerX, centerY - 100);
            this.ctx.stroke();
            
            // Second hand
            this.ctx.strokeStyle = '#FF0000';
            this.ctx.lineWidth = 1;
            this.ctx.beginPath();
            this.ctx.moveTo(centerX, centerY);
            this.ctx.lineTo(centerX + 80, centerY + 20);
            this.ctx.stroke();

            // Draw center
            this.ctx.fillStyle = '#000000';
            this.ctx.beginPath();
            this.ctx.arc(centerX, centerY, 5, 0, Math.PI * 2);
            this.ctx.fill();
        }

        /**
         * Draw side view of watch in 2D 
         */
        drawWatchSide(centerX, centerY) {
            // Basic drawing code for side view
            this.ctx.lineWidth = 3;
            this.ctx.strokeStyle = '#000000';
            this.ctx.fillStyle = '#CCCCCC';

            // Case profile
            this.ctx.beginPath();
            this.ctx.rect(centerX - 150, centerY - 20, 300, 40);
            this.ctx.fill();
            this.ctx.stroke();

            // Crown
            this.ctx.beginPath();
            this.ctx.rect(centerX + 150, centerY - 5, 20, 10);
            this.ctx.fill();
            this.ctx.stroke();
            
            // Strap top
            this.ctx.fillStyle = '#222222';
            this.ctx.beginPath();
            this.ctx.rect(centerX - 100, centerY - 80, 200, 60);
            this.ctx.fill();
            this.ctx.stroke();
            
            // Strap bottom
            this.ctx.beginPath();
            this.ctx.rect(centerX - 100, centerY + 20, 200, 60);
            this.ctx.fill();
            this.ctx.stroke();
        }

        /**
         * Draw back view of watch in 2D
         */
        drawWatchBack(centerX, centerY) {
            // Basic drawing code for back view
            this.ctx.strokeStyle = '#000000';
            this.ctx.lineWidth = 3;
            this.ctx.beginPath();
            this.ctx.arc(centerX, centerY, 150, 0, Math.PI * 2);
            this.ctx.stroke();
            this.ctx.fillStyle = '#CCCCCC';
            this.ctx.fill();

            // Draw back details
            this.ctx.beginPath();
            this.ctx.arc(centerX, centerY, 120, 0, Math.PI * 2);
            this.ctx.stroke();
            
            // Draw screws
            this.ctx.fillStyle = '#666666';
            for (let i = 0; i < 8; i++) {
                const angle = i * Math.PI / 4;
                const x = centerX + Math.cos(angle) * 135;
                const y = centerY + Math.sin(angle) * 135;
                
                this.ctx.beginPath();
                this.ctx.arc(x, y, 5, 0, Math.PI * 2);
                this.ctx.fill();
                this.ctx.stroke();
            }
            
            // Draw text
            this.ctx.fillStyle = '#000000';
            this.ctx.font = '12px Arial';
            this.ctx.textAlign = 'center';
            this.ctx.fillText('WatchModMarket', centerX, centerY - 20);
            this.ctx.fillText('Custom Build', centerX, centerY);
            this.ctx.fillText('Water Resistant 50M', centerX, centerY + 20);
        }

        /**
         * Update the camera angle based on selected view
         */
        updateViewAngle() {
            if (!this.camera) return;

            // Update auto-rotation based on view
            if (this.controls) {
                this.controls.autoRotate = (this.state.currentView === '3d');
            }

            // Set camera position based on view
            switch (this.state.currentView) {
                case 'front':
                    this.camera.position.set(0, 0, 10);
                    break;
                case 'side':
                    this.camera.position.set(10, 0, 0);
                    break;
                case 'back':
                    this.camera.position.set(0, 0, -10);
                    break;
                case '3d':
                    this.camera.position.set(7, 7, 7);
                    break;
            }

            // Look at the center
            this.camera.lookAt(0, 0, 0);

            // Reset controls if available
            if (this.controls) {
                this.controls.update();
            }
        }

        /**
         * Check compatibility between selected parts
         */
        checkCompatibility() {
            this.state.compatibilityWarnings = [];

            // Examples of compatibility checks:

            // Check case and movement compatibility
            if (this.state.selectedParts.case && this.state.selectedParts.movement) {
                const caseId = this.state.selectedParts.case.split('-')[1];
                const movementId = this.state.selectedParts.movement.split('-')[1];

                // Get compatibility data from data attributes
                const $caseElement = $(`[data-part-id="case-${caseId}"]`);
                const compatibleMovements = $caseElement.data('compatibility');

                if (compatibleMovements && !compatibleMovements.includes(`movement-${movementId}`)) {
                    this.state.compatibilityWarnings.push({
                        parts: ['case', 'movement'],
                        message: this.settings.i18n.incompatibleMovement
                    });