<?php
/**
 * Template Name: Account Page
 * 
 * A custom template for the user account page on WatchModMarket
 *
 * @package WatchModMarket
 */

get_header();

// Check if user is logged in, if not redirect to login page
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

// Get current user data
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get active tab from URL parameter or default to dashboard
$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';
$allowed_tabs = array('dashboard', 'orders', 'builds', 'watchlist', 'addresses', 'profile');

// Validate tab parameter
if (!in_array($active_tab, $allowed_tabs)) {
    $active_tab = 'dashboard';
}

// Set up avatar
$avatar_url = get_avatar_url($user_id, array('size' => 150));

// Get sample orders (Replace with actual WooCommerce order fetching)
function wmm_get_user_orders($user_id) {
    $orders = array();
    
    if (function_exists('wc_get_orders')) {
        $customer_orders = wc_get_orders(array(
            'customer_id' => $user_id,
            'limit' => -1,
        ));
        
        if (!empty($customer_orders)) {
            foreach ($customer_orders as $order) {
                $orders[] = array(
                    'id' => $order->get_order_number(),
                    'date' => $order->get_date_created()->date_i18n(get_option('date_format')),
                    'status' => wc_get_order_status_name($order->get_status()),
                    'total' => $order->get_formatted_order_total(),
                    'order_obj' => $order,
                );
            }
        }
    } else {
        // Sample data if WooCommerce is not active
        $orders = array(
            array('id' => 'WMM-1234', 'date' => date_i18n(get_option('date_format'), strtotime('-5 days')), 'status' => 'Completed', 'total' => '$549.99'),
            array('id' => 'WMM-1198', 'date' => date_i18n(get_option('date_format'), strtotime('-2 weeks')), 'status' => 'Shipped', 'total' => '$329.50'),
            array('id' => 'WMM-1056', 'date' => date_i18n(get_option('date_format'), strtotime('-1 month')), 'status' => 'Completed', 'total' => '$782.00')
        );
    }
    
    return $orders;
}

// Get user's builds (Replace with actual custom build post type fetching)
function wmm_get_user_builds($user_id) {
    $builds = array();
    
    // Check if the build custom post type exists
    if (post_type_exists('watch_build')) {
        $args = array(
            'post_type' => 'watch_build',
            'author' => $user_id,
            'posts_per_page' => -1,
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $builds[] = array(
                    'id' => get_the_ID(),
                    'name' => get_the_title(),
                    'date' => get_the_date(),
                    'status' => get_post_meta(get_the_ID(), 'build_visibility', true) ? 'Public' : 'Private',
                    'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: get_template_directory_uri() . '/assets/images/placeholder.jpg',
                );
            }
            wp_reset_postdata();
        }
    } else {
        // Sample data if custom post type is not active
        $builds = array(
            array('id' => 'B-4567', 'name' => 'Diver Special', 'date' => date_i18n(get_option('date_format'), strtotime('-5 days')), 'status' => 'Public', 'image' => get_template_directory_uri() . '/assets/images/placeholder.jpg'),
            array('id' => 'B-4321', 'name' => 'Vintage Explorer', 'date' => date_i18n(get_option('date_format'), strtotime('-2 weeks')), 'status' => 'Private', 'image' => get_template_directory_uri() . '/assets/images/placeholder.jpg')
        );
    }
    
    return $builds;
}

// Get user's watchlist (Replace with actual wishlist fetching)
function wmm_get_user_watchlist($user_id) {
    $watchlist = array();
    
    // Check if YITH WooCommerce Wishlist is active
    if (function_exists('YITH_WCWL')) {
        $wishlist_items = YITH_WCWL()->get_products(array(
            'wishlist_id' => 'user_' . $user_id,
        ));
        
        if (!empty($wishlist_items)) {
            foreach ($wishlist_items as $item) {
                $product = wc_get_product($item['prod_id']);
                if ($product) {
                    $watchlist[] = array(
                        'id' => $product->get_id(),
                        'name' => $product->get_name(),
                        'price' => $product->get_price_html(),
                        'stock' => $product->is_in_stock() ? ($product->get_stock_quantity() <= 5 && $product->get_stock_quantity() > 0 ? 'Low Stock' : 'In Stock') : 'Out of Stock',
                        'image' => wp_get_attachment_url($product->get_image_id()) ?: get_template_directory_uri() . '/assets/images/placeholder.jpg',
                    );
                }
            }
        }
    } else {
        // Sample data if YITH WooCommerce Wishlist is not active
        $watchlist = array(
            array('id' => 'P-789', 'name' => 'Seiko Movement NH35', 'price' => '$89.99', 'stock' => 'In Stock', 'image' => get_template_directory_uri() . '/assets/images/placeholder.jpg'),
            array('id' => 'P-456', 'name' => 'Sapphire Crystal 38mm', 'price' => '$45.50', 'stock' => 'In Stock', 'image' => get_template_directory_uri() . '/assets/images/placeholder.jpg'),
            array('id' => 'P-234', 'name' => 'Marine Dive Case 42mm', 'price' => '$129.99', 'stock' => 'Low Stock', 'image' => get_template_directory_uri() . '/assets/images/placeholder.jpg')
        );
    }
    
    return $watchlist;
}

// Get user's addresses (Replace with actual WooCommerce address fetching)
function wmm_get_user_addresses($user_id) {
    $addresses = array();
    
    if (function_exists('wc_get_customer_default_location')) {
        $shipping = array(
            'type' => 'Shipping',
            'name' => get_user_meta($user_id, 'shipping_first_name', true) . ' ' . get_user_meta($user_id, 'shipping_last_name', true),
            'address' => get_user_meta($user_id, 'shipping_address_1', true),
            'city' => get_user_meta($user_id, 'shipping_city', true),
            'state' => get_user_meta($user_id, 'shipping_state', true),
            'zip' => get_user_meta($user_id, 'shipping_postcode', true),
            'country' => get_user_meta($user_id, 'shipping_country', true),
            'isDefault' => true
        );
        
        $billing = array(
            'type' => 'Billing',
            'name' => get_user_meta($user_id, 'billing_first_name', true) . ' ' . get_user_meta($user_id, 'billing_last_name', true),
            'address' => get_user_meta($user_id, 'billing_address_1', true),
            'city' => get_user_meta($user_id, 'billing_city', true),
            'state' => get_user_meta($user_id, 'billing_state', true),
            'zip' => get_user_meta($user_id, 'billing_postcode', true),
            'country' => get_user_meta($user_id, 'billing_country', true),
            'isDefault' => true
        );
        
        // Only add addresses if they exist
        if (!empty($shipping['name']) && !empty($shipping['address'])) {
            $addresses[] = $shipping;
        }
        
        if (!empty($billing['name']) && !empty($billing['address'])) {
            $addresses[] = $billing;
        }
    }
    
    // If no addresses found, provide sample data
    if (empty($addresses)) {
        $addresses = array(
            array(
                'type' => 'Shipping',
                'name' => $current_user->display_name,
                'address' => '123 Timepiece Lane',
                'city' => 'Clockville',
                'state' => 'CA',
                'zip' => '90210',
                'country' => 'United States',
                'isDefault' => true
            ),
            array(
                'type' => 'Billing',
                'name' => $current_user->display_name,
                'address' => '456 Gear Avenue',
                'city' => 'Springtown',
                'state' => 'NY',
                'zip' => '10001',
                'country' => 'United States',
                'isDefault' => true
            )
        );
    }
    
    return $addresses;
}

// Get data for the dashboard
$orders = wmm_get_user_orders($user_id);
$builds = wmm_get_user_builds($user_id);
$watchlist = wmm_get_user_watchlist($user_id);
$addresses = wmm_get_user_addresses($user_id);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Handle profile update
        $display_name = sanitize_text_field($_POST['display_name']);
        
        // Update user data
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $display_name,
        ));
        
        if (!empty($_POST['user_email']) && is_email($_POST['user_email'])) {
            wp_update_user(array(
                'ID' => $user_id,
                'user_email' => sanitize_email($_POST['user_email']),
            ));
        }
        
        if (!empty($_POST['phone'])) {
            update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
        }
        
        $profile_updated = true;
    } elseif (isset($_POST['update_password'])) {
        // Handle password update
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $user = get_user_by('id', $user_id);
        
        if ($user && wp_check_password($current_password, $user->data->user_pass, $user->ID)) {
            if ($new_password === $confirm_password) {
                wp_set_password($new_password, $user_id);
                $password_updated = true;
            } else {
                $password_error = "New passwords don't match.";
            }
        } else {
            $password_error = "Current password is incorrect.";
        }
    }
}

// Now we'll start the HTML output
?>

<div class="account-page">
    <div class="account-header">
        <div class="container">
            <h1>My Account</h1>
        </div>
    </div>
    
    <div class="account-container container">
        <div class="account-sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="Profile">
                </div>
                <div class="user-details">
                    <h3><?php echo esc_html($current_user->display_name); ?></h3>
                    <p><?php echo esc_html($current_user->user_email); ?></p>
                </div>
            </div>
            
            <nav class="account-nav">
                <ul>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('tab', 'dashboard')); ?>" 
                           class="<?php echo $active_tab === 'dashboard' ? 'active' : ''; ?>">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('tab', 'orders')); ?>" 
                           class="<?php echo $active_tab === 'orders' ? 'active' : ''; ?>">
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('tab', 'builds')); ?>" 
                           class="<?php echo $active_tab === 'builds' ? 'active' : ''; ?>">
                            My Builds
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('tab', 'watchlist')); ?>" 
                           class="<?php echo $active_tab === 'watchlist' ? 'active' : ''; ?>">
                            Watchlist
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('tab', 'addresses')); ?>" 
                           class="<?php echo $active_tab === 'addresses' ? 'active' : ''; ?>">
                            Addresses
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('tab', 'profile')); ?>" 
                           class="<?php echo $active_tab === 'profile' ? 'active' : ''; ?>">
                            Profile Settings
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="account-actions">
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn btn-secondary logout-btn">Log Out</a>
            </div>
        </div>
        
        <div class="account-content">
            <?php if ($active_tab === 'dashboard'): ?>
                <!-- Dashboard Tab -->
                <div class="dashboard-tab">
                    <div class="account-welcome">
                        <h2>Welcome back, <?php echo esc_html($current_user->display_name); ?>!</h2>
                        <p>Member since <?php echo date_i18n(get_option('date_format'), strtotime($current_user->user_registered)); ?></p>
                    </div>
                    
                    <div class="dashboard-stats">
                        <div class="stat-box">
                            <span class="stat-number"><?php echo count($orders); ?></span>
                            <span class="stat-label">Orders</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-number"><?php echo count($builds); ?></span>
                            <span class="stat-label">Builds</span>
                        </div>
                        <div class="stat-box">
                            <span class="stat-number"><?php echo count($watchlist); ?></span>
                            <span class="stat-label">Watchlist</span>
                        </div>
                    </div>
                    
                    <div class="quick-sections">
                        <div class="quick-section">
                            <div class="section-header">
                                <h3>Recent Orders</h3>
                                <a href="<?php echo esc_url(add_query_arg('tab', 'orders')); ?>" class="view-all">View All</a>
                            </div>
                            <div class="recent-orders">
                                <?php 
                                $recent_orders = array_slice($orders, 0, 2);
                                if (!empty($recent_orders)):
                                    foreach ($recent_orders as $order): 
                                ?>
                                    <div class="order-item">
                                        <div class="order-details">
                                            <span class="order-id"><?php echo esc_html($order['id']); ?></span>
                                            <span class="order-date"><?php echo esc_html($order['date']); ?></span>
                                        </div>
                                        <div class="order-status">
                                            <span class="status-badge <?php echo esc_attr(strtolower($order['status'])); ?>"><?php echo esc_html($order['status']); ?></span>
                                            <span class="order-total"><?php echo esc_html($order['total']); ?></span>
                                        </div>
                                    </div>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <p>You haven't placed any orders yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="quick-section">
                            <div class="section-header">
                                <h3>My Builds</h3>
                                <a href="<?php echo esc_url(add_query_arg('tab', 'builds')); ?>" class="view-all">View All</a>
                            </div>
                            <div class="recent-builds">
                                <?php 
                                $recent_builds = array_slice($builds, 0, 2);
                                if (!empty($recent_builds)):
                                    foreach ($recent_builds as $build): 
                                ?>
                                    <div class="build-item">
                                        <div class="build-image">
                                            <img src="<?php echo esc_url($build['image']); ?>" alt="<?php echo esc_attr($build['name']); ?>">
                                            <span class="build-status <?php echo esc_attr(strtolower($build['status'])); ?>"><?php echo esc_html($build['status']); ?></span>
                                        </div>
                                        <div class="build-details">
                                            <h4><?php echo esc_html($build['name']); ?></h4>
                                            <span class="build-date"><?php echo esc_html($build['date']); ?></span>
                                            <a href="#" class="btn btn-small">Edit Build</a>
                                        </div>
                                    </div>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                    <p>You haven't created any builds yet.</p>
                                    <a href="<?php echo esc_url(home_url('/watch-builder/')); ?>" class="btn btn-primary">Start Building</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($active_tab === 'orders'): ?>
                <!-- Orders Tab -->
                <div class="orders-tab">
                    <h2>My Orders</h2>
                    
                    <div class="orders-filters">
                        <div class="filters-group">
                            <div class="filter-item">
                                <label>Status:</label>
                                <select id="order-status-filter">
                                    <option>All</option>
                                    <option>Completed</option>
                                    <option>Shipped</option>
                                    <option>Processing</option>
                                    <option>Cancelled</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label>Date Range:</label>
                                <select id="order-date-filter">
                                    <option>All Time</option>
                                    <option>Last 30 Days</option>
                                    <option>Last 3 Months</option>
                                    <option>Last Year</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-secondary" id="apply-order-filters">Apply Filters</button>
                    </div>
                    
                    <div class="orders-list">
                        <div class="orders-header">
                            <span>Order ID</span>
                            <span>Date</span>
                            <span>Status</span>
                            <span>Total</span>
                            <span>Actions</span>
                        </div>
                        
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <div class="order-row">
                                    <span class="order-id"><?php echo esc_html($order['id']); ?></span>
                                    <span class="order-date"><?php echo esc_html($order['date']); ?></span>
                                    <span class="status-badge <?php echo esc_attr(strtolower($order['status'])); ?>"><?php echo esc_html($order['status']); ?></span>
                                    <span class="order-total"><?php echo esc_html($order['total']); ?></span>
                                    <div class="order-actions">
                                        <a href="#" class="btn btn-small">View</a>
                                        <?php if (isset($order['order_obj']) && $order['order_obj']->has_status(array('processing', 'on-hold'))): ?>
                                            <a href="#" class="btn btn-small btn-secondary">Track</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>You haven't placed any orders yet.</p>
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-primary">Browse Shop</a>
                        <?php endif; ?>
                    </div>
                </div>
                
            <?php elseif ($active_tab === 'addresses'): ?>
                <!-- Addresses Tab -->
                <div class="addresses-tab">
                    <h2>My Addresses</h2>
                    
                    <div class="addresses-grid">
                        <?php if (!empty($addresses)): ?>
                            <?php foreach ($addresses as $address): ?>
                                <div class="address-card">
                                    <div class="address-header">
                                        <h3><?php echo esc_html($address['type']); ?> Address</h3>
                                        <?php if ($address['isDefault']): ?>
                                            <span class="default-badge">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="address-details">
                                        <p><?php echo esc_html($address['name']); ?></p>
                                        <p><?php echo esc_html($address['address']); ?></p>
                                        <p><?php echo esc_html($address['city']); ?>, <?php echo esc_html($address['state']); ?> <?php echo esc_html($address['zip']); ?></p>
                                        <p><?php echo esc_html($address['country']); ?></p>
                                    </div>
                                    <div class="address-actions">
                                        <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', $address['type'] === 'Billing' ? 'billing' : 'shipping', wc_get_page_permalink('myaccount'))); ?>" class="btn btn-small">Edit</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <div class="add-address-card">
                            <div class="add-icon">+</div>
                            <h3>Add New Address</h3>
                            <a href="<?php echo esc_url(wc_get_endpoint_url('edit-address', 'shipping', wc_get_page_permalink('myaccount'))); ?>" class="btn">Add Address</a>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($active_tab === 'profile'): ?>
                <!-- Profile Tab -->
                <div class="profile-tab">
                    <h2>My Profile</h2>
                    
                    <?php if (isset($profile_updated) && $profile_updated): ?>
                        <div class="notification success">
                            <p>Your profile has been updated successfully.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($password_updated) && $password_updated): ?>
                        <div class="notification success">
                            <p>Your password has been updated successfully.</p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($password_error)): ?>
                        <div class="notification error">
                            <p><?php echo esc_html($password_error); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="profile-grid">
                        <div class="profile-section">
                            <div class="avatar-section">
                                <div class="current-avatar">
                                    <img src="<?php echo esc_url($avatar_url); ?>" alt="Profile">
                                </div>
                                <div class="avatar-actions">
                                    <?php if (class_exists('Simple_Local_Avatars')): ?>
                                        <a href="<?php echo esc_url(admin_url('profile.php')); ?>" class="btn btn-secondary">Change Photo</a>
                                    <?php else: ?>
                                        <a href="https://gravatar.com/" target="_blank" class="btn btn-secondary">Change Gravatar</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <form class="profile-form" method="post">
                                <div class="form-group">
                                    <label for="display_name">Full Name</label>
                                    <input type="text" id="display_name" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="user_email">Email Address</label>
                                    <input type="email" id="user_email" name="user_email" value="<?php echo esc_attr($current_user->user_email); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone" placeholder="Add your phone number" value="<?php echo esc_attr(get_user_meta($user_id, 'phone', true)); ?>">
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="profile-section password-section">
                            <h3>Change Password</h3>
                            
                            <form class="password-form" method="post">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="danger-zone">
                        <h3>Danger Zone</h3>
                        <div class="danger-actions">
                            <a href="#" class="btn btn-danger" id="delete-account-btn">Delete Account</a>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($active_tab === 'builds'): ?>
                <!-- Builds Tab -->
                <div class="builds-tab">
                    <h2>My Builds</h2>
                    
                    <div class="builds-filters">
                        <div class="filters-group">
                            <div class="filter-item">
                                <label>Status:</label>
                                <select id="build-status-filter">
                                    <option>All</option>
                                    <option>Public</option>
                                    <option>Private</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label>Sort By:</label>
                                <select id="build-sort-filter">
                                    <option>Newest First</option>
                                    <option>Oldest First</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-secondary" id="apply-build-filters">Apply Filters</button>
                    </div>
                    
                    <div class="builds-grid">
                        <?php if (!empty($builds)): ?>
                            <?php foreach ($builds as $build): ?>
                                <div class="build-card">
                                    <div class="build-image">
                                        <img src="<?php echo esc_url($build['image']); ?>" alt="<?php echo esc_attr($build['name']); ?>">
                                        <span class="build-status <?php echo esc_attr(strtolower($build['status'])); ?>"><?php echo esc_html($build['status']); ?></span>
                                    </div>
                                    <div class="build-info">
                                        <h3><?php echo esc_html($build['name']); ?></h3>
                                        <p class="build-date">Created on <?php echo esc_html($build['date']); ?></p>
                                        <div class="build-actions">
                                            <a href="#" class="btn">View Build</a>
                                            <a href="#" class="btn btn-secondary">Edit</a>
                                            <a href="#" class="btn btn-secondary">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <div class="create-build-card">
                            <div class="add-icon">+</div>
                            <h3>Create New Build</h3>
                            <p>Start designing your perfect watch</p>
                            <a href="<?php echo esc_url(home_url('/watch-builder/')); ?>" class="btn">Create Build</a>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($active_tab === 'watchlist'): ?>
                <!-- Watchlist Tab -->
                <div class="watchlist-tab">
                    <h2>My Watchlist</h2>
                    
                    <div class="watchlist-items">
                        <?php if (!empty($watchlist)): ?>
                            <?php foreach ($watchlist as $item): ?>
                                <div class="watchlist-item">
                                    <div class="watchlist-image">
                                        <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
                                    </div>
                                    <div class="watchlist-details">
                                        <h3><?php echo esc_html($item['name']); ?></h3>
                                        <span class="watchlist-price"><?php echo wp_kses_post($item['price']); ?></span>
                                        <span class="watchlist-stock"><?php echo esc_html($item['stock']); ?></span>
                                    </div>
                                    <div class="watchlist-actions">
                                        <a href="<?php echo esc_url(wc_get_cart_url() . '?add-to-cart=' . esc_attr($item['id'])); ?>" class="btn btn-primary">Add to Cart</a>
                                        <?php if (function_exists('YITH_WCWL')): ?>
                                            <a href="<?php echo esc_url(add_query_arg('remove_from_wishlist', $item['id'], YITH_WCWL()->get_wishlist_url('user_' . $user_id))); ?>" class="btn btn-secondary">Remove</a>
                                        <?php else: ?>
                                            <a href="#" class="btn btn-secondary">Remove</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-watchlist">
                                <div class="empty-icon">⭐</div>
                                <h3>Your watchlist is empty</h3>
                                <p>Save items you're interested in to track pricing and availability</p>
                                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="btn btn-primary">Browse Products</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Add JavaScript for filtering and other interactive features
?>
<script>
jQuery(document).ready(function($) {
    // Order filtering
    $('#apply-order-filters').on('click', function() {
        // In a real implementation, you would update the order list via AJAX
        // For now, we'll just show an alert
        alert('Filtering would be applied here in a real implementation');
    });
    
    // Build filtering
    $('#apply-build-filters').on('click', function() {
        // In a real implementation, you would update the build list via AJAX
        alert('Filtering would be applied here in a real implementation');
    });
    
    // Delete account confirmation
    $('#delete-account-btn').on('click', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            // In a real implementation, you would handle account deletion
            alert('Account deletion would be handled here in a real implementation');
        }
    });
});
</script>



<?php get_footer(); ?>