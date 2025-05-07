<?php
/**
 * Enhanced Navigation for WatchModMarket Theme
 * 
 * This file extends the existing navigation functionality
 */

// Skip functions that are already defined in functions.php
// Only add new functions that don't exist yet

/**
 * Add custom classes to menu items
 */
function watchmodmarket_nav_menu_classes($classes, $item, $args) {
    // Add class to current menu item
    if (in_array('current-menu-item', $classes)) {
        $classes[] = 'active';
    }
    
    // Add class to menu items with children
    if (in_array('menu-item-has-children', $classes)) {
        $classes[] = 'has-dropdown';
    }
    
    return $classes;
}
add_filter('nav_menu_css_class', 'watchmodmarket_nav_menu_classes', 10, 3);

/**
 * Add icon to menu items with dropdowns
 */
function watchmodmarket_nav_dropdown_icon($title, $item, $args, $depth) {
    if (in_array('menu-item-has-children', $item->classes)) {
        $title .= '<span class="dropdown-icon" aria-hidden="true">▼</span>';
    }
    return $title;
}
add_filter('nav_menu_item_title', 'watchmodmarket_nav_dropdown_icon', 10, 4);

/**
 * Modify submenu classes
 */
function watchmodmarket_nav_submenu_classes($classes) {
    $classes[] = 'dropdown-menu';
    return $classes;
}
add_filter('nav_menu_submenu_css_class', 'watchmodmarket_nav_submenu_classes', 10, 1);

/**
 * Add toggle for sub-menus on mobile
 */
function watchmodmarket_nav_submenu_toggles($item_output, $item, $depth, $args) {
    if (in_array('menu-item-has-children', $item->classes) && isset($args->theme_location) && $args->theme_location === 'primary') {
        $item_output .= '<button class="submenu-toggle" aria-expanded="false"><span class="screen-reader-text">' . esc_html__('Toggle submenu', 'watchmodmarket') . '</span></button>';
    }
    return $item_output;
}
add_filter('walker_nav_menu_start_el', 'watchmodmarket_nav_submenu_toggles', 10, 4);

/**
 * Add JavaScript for navigation functionality
 */
function watchmodmarket_nav_scripts() {
    ?>
    <script>
    (function() {
        // Mobile menu toggle
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const primaryMenu = document.getElementById('site-navigation');
        
        if (menuToggle && primaryMenu) {
            menuToggle.addEventListener('click', function() {
                const expanded = this.getAttribute('aria-expanded') === 'true' || false;
                this.setAttribute('aria-expanded', !expanded);
                primaryMenu.classList.toggle('is-active');
                document.body.classList.toggle('menu-is-active');
            });
        }
        
        // Sub-menu toggles
        const subMenuToggles = document.querySelectorAll('.submenu-toggle');
        
        subMenuToggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const expanded = this.getAttribute('aria-expanded') === 'true' || false;
                this.setAttribute('aria-expanded', !expanded);
                
                // Get the sub-menu
                const parentMenuItem = this.closest('.menu-item-has-children');
                const subMenu = parentMenuItem.querySelector('.sub-menu');
                
                if (subMenu) {
                    if (expanded) {
                        subMenu.style.maxHeight = null;
                        subMenu.setAttribute('aria-hidden', 'true');
                    } else {
                        subMenu.style.maxHeight = subMenu.scrollHeight + 'px';
                        subMenu.setAttribute('aria-hidden', 'false');
                    }
                }
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (primaryMenu && primaryMenu.classList.contains('is-active')) {
                if (!primaryMenu.contains(e.target) && e.target !== menuToggle) {
                    primaryMenu.classList.remove('is-active');
                    menuToggle.setAttribute('aria-expanded', 'false');
                    document.body.classList.remove('menu-is-active');
                }
            }
        });
        
        // Close menu on ESC key
        document.addEventListener('keyup', function(e) {
            if (e.key === 'Escape' && primaryMenu && primaryMenu.classList.contains('is-active')) {
                primaryMenu.classList.remove('is-active');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('menu-is-active');
            }
        });
    })();
    </script>
    <?php
}
add_action('wp_footer', 'watchmodmarket_nav_scripts');