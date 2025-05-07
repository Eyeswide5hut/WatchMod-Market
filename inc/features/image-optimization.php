<?php
/**
 * Image Optimization Functionality for WatchModMarket Theme
 *
 * @package WatchModMarket
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add WebP support for uploads
 */
function watchmodmarket_enable_webp_uploads() {
    add_filter('mime_types', function($mimes) {
        $mimes['webp'] = 'image/webp';
        return $mimes;
    });
    
    add_filter('file_is_displayable_image', function($result, $path) {
        if (pathinfo($path, PATHINFO_EXTENSION) === 'webp') {
            return true;
        }
        return $result;
    }, 10, 2);
}
add_action('init', 'watchmodmarket_enable_webp_uploads');

/**
 * Generate WebP versions of uploaded images
 */
function watchmodmarket_generate_webp_versions($metadata, $attachment_id) {
    // Check if imagewebp function exists (PHP 5.5.0+)
    if (!function_exists('imagewebp')) {
        return $metadata;
    }
    
    // Check mime type
    $mime_type = get_post_mime_type($attachment_id);
    if (strpos($mime_type, 'image/') !== 0 || $mime_type === 'image/webp') {
        return $metadata;
    }
    
    // Get quality setting from theme customizer (default 85%)
    $quality = get_theme_mod('image_quality', 85);
    
    // Get upload directory
    $upload_dir = wp_upload_dir();
    $filepath = $upload_dir['basedir'] . '/' . $metadata['file'];
    
    if (file_exists($filepath)) {
        $webp_filepath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $filepath);
        
        // Create image resource based on original file
        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filepath);
                
                // Handle PNG transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            default:
                return $metadata;
        }
        
        // Convert and save WebP version
        if ($image) {
            imagewebp($image, $webp_filepath, $quality);
            imagedestroy($image);
            
            // Generate WebP versions for all sizes
            if (!empty($metadata['sizes'])) {
                foreach ($metadata['sizes'] as $size => $size_data) {
                    $size_filepath = dirname($filepath) . '/' . $size_data['file'];
                    $size_webp_filepath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $size_filepath);
                    
                    if (file_exists($size_filepath)) {
                        switch ($mime_type) {
                            case 'image/jpeg':
                                $size_image = imagecreatefromjpeg($size_filepath);
                                break;
                            case 'image/png':
                                $size_image = imagecreatefrompng($size_filepath);
                                
                                // Handle PNG transparency
                                imagepalettetotruecolor($size_image);
                                imagealphablending($size_image, true);
                                imagesavealpha($size_image, true);
                                break;
                            case 'image/webp':
                                $size_image = imagecreatefromwebp($size_filepath);
                                break;
                        }
                        
                        if (isset($size_image)) {
                            imageavif($size_image, $size_avif_filepath, $quality);
                            imagedestroy($size_image);
                        }
                    }
                }
            }
        }
    }
    
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'watchmodmarket_generate_avif_versions', 20, 2); // Priority after WebP generation

/**
 * Add WebP/AVIF support to image srcset
 */
function watchmodmarket_add_nextgen_to_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
    // Skip if no sources
    if (empty($sources)) {
        return $sources;
    }
    
    // Get browser support based on Accept header
    $supports_webp = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
    $supports_avif = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/avif') !== false;
    
    // Only proceed if browser supports modern formats
    if (!$supports_webp && !$supports_avif) {
        return $sources;
    }
    
    // Process each source
    foreach ($sources as $width => $source) {
        // Check file paths
        $file_url = $source['url'];
        $file_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $file_url);
        
        $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file_url);
        $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file_path);
        
        $avif_url = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $file_url);
        $avif_path = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $file_path);
        
        // Prioritize AVIF if supported and file exists
        if ($supports_avif && file_exists($avif_path)) {
            $sources[$width]['url'] = $avif_url;
            $sources[$width]['mime_type'] = 'image/avif';
        } 
        // Fall back to WebP if supported and file exists
        elseif ($supports_webp && file_exists($webp_path)) {
            $sources[$width]['url'] = $webp_url;
            $sources[$width]['mime_type'] = 'image/webp';
        }
    }
    
    return $sources;
}
add_filter('wp_calculate_image_srcset', 'watchmodmarket_add_nextgen_to_srcset', 10, 5);

/**
 * Replace featured images with WebP/AVIF versions when appropriate
 */
function watchmodmarket_optimize_featured_image($html, $post_id, $post_thumbnail_id, $size, $attr) {
    // Only modify if WebP is supported or enabled in customizer
    if (!get_theme_mod('enable_webp_conversion', true)) {
        return $html;
    }
    
    // Get browser support based on Accept header
    $supports_webp = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
    $supports_avif = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/avif') !== false;
    
    // Only proceed if browser supports modern formats
    if (!$supports_webp && !$supports_avif) {
        return $html;
    }
    
    // Get image URL
    $image_src = wp_get_attachment_image_src($post_thumbnail_id, $size);
    if (!$image_src) {
        return $html;
    }
    
    $image_url = $image_src[0];
    $image_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $image_url);
    
    $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $image_url);
    $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $image_path);
    
    $avif_url = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $image_url);
    $avif_path = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $image_path);
    
    // Check if we need to modify the HTML
    $modified = false;
    $optimized_url = '';
    
    // Prioritize AVIF if supported and file exists
    if ($supports_avif && file_exists($avif_path)) {
        $optimized_url = $avif_url;
        $modified = true;
    } 
    // Fall back to WebP if supported and file exists
    elseif ($supports_webp && file_exists($webp_path)) {
        $optimized_url = $webp_url;
        $modified = true;
    }
    
    // If we have an optimized version, create picture element
    if ($modified) {
        // Create picture element
        $picture = '<picture>';
        
        // Add source elements for modern formats
        if ($supports_avif && file_exists($avif_path)) {
            $picture .= '<source srcset="' . esc_url($avif_url) . '" type="image/avif">';
        }
        
        if ($supports_webp && file_exists($webp_path)) {
            $picture .= '<source srcset="' . esc_url($webp_url) . '" type="image/webp">';
        }
        
        // Add original img as fallback
        $picture .= $html;
        $picture .= '</picture>';
        
        return $picture;
    }
    
    return $html;
}
add_filter('post_thumbnail_html', 'watchmodmarket_optimize_featured_image', 10, 5);

/**
 * Add image optimization settings to customizer
 */
function watchmodmarket_add_image_settings($wp_customize) {
    $wp_customize->add_section('watchmodmarket_image_optimization', array(
        'title' => __('Image Optimization', 'watchmodmarket'),
        'priority' => 50,
    ));
    
    // Enable WebP conversion
    $wp_customize->add_setting('enable_webp_conversion', array(
        'default' => true,
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'watchmodmarket_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_webp_conversion', array(
        'label' => __('Enable WebP Conversion', 'watchmodmarket'),
        'description' => __('Automatically convert JPEG and PNG images to WebP format for better performance.', 'watchmodmarket'),
        'section' => 'watchmodmarket_image_optimization',
        'type' => 'checkbox',
    ));
    
    // Image quality setting
    $wp_customize->add_setting('image_quality', array(
        'default' => 85,
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('image_quality', array(
        'label' => __('Image Quality', 'watchmodmarket'),
        'description' => __('Set the quality for compressed images (1-100).', 'watchmodmarket'),
        'section' => 'watchmodmarket_image_optimization',
        'type' => 'range',
        'input_attrs' => array(
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ),
    ));
    
    // Enable lazy loading
    $wp_customize->add_setting('enable_lazy_loading', array(
        'default' => true,
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'watchmodmarket_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('enable_lazy_loading', array(
        'label' => __('Enable Lazy Loading', 'watchmodmarket'),
        'description' => __('Load images only when they enter the viewport for better performance.', 'watchmodmarket'),
        'section' => 'watchmodmarket_image_optimization',
        'type' => 'checkbox',
    ));
}
add_action('customize_register', 'watchmodmarket_add_image_settings');

/**
 * Sanitize checkbox values
 */
function watchmodmarket_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * Apply lazy loading to images
 */
function watchmodmarket_apply_lazy_loading($content) {
    // Only modify if lazy loading is enabled in customizer
    if (!get_theme_mod('enable_lazy_loading', true)) {
        return $content;
    }
    
    // Skip if AMP page (AMP has its own lazy loading)
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        return $content;
    }
    
    // Don't lazy load if the content is small
    if (strlen($content) < 600) {
        return $content;
    }
    
    // Add loading="lazy" attribute to img tags that don't already have it
    $content = preg_replace('/<img((?!loading=).+?)>/i', '<img loading="lazy"$1>', $content);
    
    return $content;
}
add_filter('the_content', 'watchmodmarket_apply_lazy_loading', 99);
add_filter('post_thumbnail_html', 'watchmodmarket_apply_lazy_loading', 99);
add_filter('woocommerce_product_get_image', 'watchmodmarket_apply_lazy_loading', 99);

/**
 * Add responsive image sizes
 */
function watchmodmarket_add_image_sizes() {
    // Add custom image sizes for product galleries and responsive layouts
    add_image_size('product-large', 1200, 1200, false);
    add_image_size('product-medium', 800, 800, false);
    add_image_size('product-small', 400, 400, false);
    
    // Add custom image sizes for blog featured images
    add_image_size('blog-featured', 1200, 675, true);
    add_image_size('blog-card', 600, 338, true);
}
add_action('after_setup_theme', 'watchmodmarket_add_image_sizes');

/**
 * Register responsive image sizes for srcset
 */
function watchmodmarket_register_responsive_image_sizes() {
    // Add responsive image sizes
    add_filter('wp_calculate_image_sizes', 'watchmodmarket_calculate_image_sizes', 10, 5);
    
    // Register custom image sizes with names for media library
    add_filter('image_size_names_choose', 'watchmodmarket_custom_image_sizes');
}
add_action('init', 'watchmodmarket_register_responsive_image_sizes');

/**
 * Calculate responsive image sizes based on layout
 */
function watchmodmarket_calculate_image_sizes($sizes, $size, $image_src, $image_meta, $attachment_id) {
    // Define sizes for different page layouts
    if (is_singular('post')) {
        $sizes = '(min-width: 1200px) 1200px, (min-width: 768px) 800px, 100vw';
    } elseif (is_archive() || is_home()) {
        $sizes = '(min-width: 1200px) 600px, (min-width: 768px) 400px, 100vw';
    } elseif (is_product()) {
        $sizes = '(min-width: 1200px) 600px, (min-width: 768px) 500px, 100vw';
    }
    
    return $sizes;
}

/**
 * Add custom image sizes to media library dropdown
 */
function watchmodmarket_custom_image_sizes($sizes) {
    return array_merge($sizes, array(
        'product-large' => __('Product Large', 'watchmodmarket'),
        'product-medium' => __('Product Medium', 'watchmodmarket'),
        'product-small' => __('Product Small', 'watchmodmarket'),
        'blog-featured' => __('Blog Featured', 'watchmodmarket'),
        'blog-card' => __('Blog Card', 'watchmodmarket'),
    ));
}

/**
 * Image Optimizer Utility Class
 */
class Watchmodmarket_Image_Optimizer {
    /**
     * Output an optimized image with picture element for modern formats
     */
    public static function get_optimized_image($attachment_id, $size = 'full', $attr = array()) {
        // Set default attributes
        $default_attr = array(
            'loading' => 'lazy',
            'decoding' => 'async'
        );
        
        $attr = array_merge($default_attr, $attr);
        
        // Get browser support based on Accept header
        $supports_webp = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
        $supports_avif = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/avif') !== false;
        
        // Get image sources
        $sources = array();
        $original_url = wp_get_attachment_image_url($attachment_id, $size);
        
        if (!$original_url) {
            return '';
        }
        
        $original_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $original_url);
        
        // Check for AVIF version
        if ($supports_avif) {
            $avif_url = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $original_url);
            $avif_path = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $original_path);
            
            if (file_exists($avif_path)) {
                $sources[] = array(
                    'url' => $avif_url,
                    'type' => 'image/avif'
                );
            }
        }
        
        // Check for WebP version
        if ($supports_webp) {
            $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $original_url);
            $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $original_path);
            
            if (file_exists($webp_path)) {
                $sources[] = array(
                    'url' => $webp_url,
                    'type' => 'image/webp'
                );
            }
        }
        
        // Add original as fallback
        $sources[] = array(
            'url' => $original_url,
            'type' => wp_get_attachment_mime_type($attachment_id)
        );
        
        // Generate picture element
        $picture = '<picture>';
        foreach ($sources as $source) {
            if ($source['url'] !== $original_url) {
                $picture .= sprintf('<source srcset="%s" type="%s">', esc_url($source['url']), esc_attr($source['type']));
            }
        }
        
        // Add final img tag
        $img_attrs = '';
        foreach ($attr as $key => $value) {
            $img_attrs .= sprintf(' %s="%s"', $key, esc_attr($value));
        }
        
        $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if (empty($alt_text)) {
            $alt_text = get_the_title($attachment_id);
        }
        
        $picture .= sprintf('<img src="%s" alt="%s"%s>', 
            esc_url($original_url), 
            esc_attr($alt_text), 
            $img_attrs
        );
        
        $picture .= '</picture>';
        
        return $picture;
    }
    
    /**
     * Get optimized image URL
     */
    public static function get_optimized_url($attachment_id, $size = 'full') {
        $original_url = wp_get_attachment_image_url($attachment_id, $size);
        
        if (!$original_url) {
            return '';
        }
        
        // Check for WebP/AVIF versions
        $supports_webp = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false;
        $supports_avif = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/avif') !== false;
        
        $original_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $original_url);
        
        // Check for AVIF version
        if ($supports_avif) {
            $avif_url = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $original_url);
            $avif_path = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $original_path);
            
            if (file_exists($avif_path)) {
                return $avif_url;
            }
        }
        
        // Check for WebP version
        if ($supports_webp) {
            $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $original_url);
            $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $original_path);
            
            if (file_exists($webp_path)) {
                return $webp_url;
            }
        }
        
        return $original_url;
    }
}

/**
 * Generate responsive image element with optimized formats
 */
function watchmodmarket_responsive_image($attachment_id, $sizes = array(), $attr = array()) {
    return Watchmodmarket_Image_Optimizer::get_optimized_image($attachment_id, $sizes, $attr);
}

/**
 * Add progressive image loading script to footer
 */
function watchmodmarket_progressive_image_loading() {
    // Only add if lazy loading is enabled
    if (!get_theme_mod('enable_lazy_loading', true)) {
        return;
    }
    
    ?>
    <script>
    // Progressive image loading
    (function() {
        // Check for IntersectionObserver support
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const dataSrc = img.dataset.src;
                        
                        if (dataSrc) {
                            img.src = dataSrc;
                            img.classList.add('loaded');
                            delete img.dataset.src;
                            imageObserver.unobserve(img);
                        }
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });
            
            // Observe all images with data-src attribute
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for browsers without IntersectionObserver
            document.querySelectorAll('img[data-src]').forEach(img => {
                img.src = img.dataset.src;
                delete img.dataset.src;
            });
        }
        
        // Add fade-in effect for images
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.fade-in-image').forEach(img => {
                if (img.complete) {
                    img.classList.add('loaded');
                } else {
                    img.addEventListener('load', () => {
                        img.classList.add('loaded');
                    });
                }
            });
        });
    })();
    </script>
    
    <style>
    /* Progressive image loading styles */
    img[data-src] {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    img.loaded {
        opacity: 1;
    }
    
    .fade-in-image {
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .fade-in-image.loaded {
        opacity: 1;
    }
    </style>
    <?php
}
add_action('wp_footer', 'watchmodmarket_progressive_image_loading');
 {
                            case 'image/jpeg':
                                $size_image = imagecreatefromjpeg($size_filepath);
                                break;
                            case 'image/png':
                                $size_image = imagecreatefrompng($size_filepath);
                                
                                // Handle PNG transparency
                                imagepalettetotruecolor($size_image);
                                imagealphablending($size_image, true);
                                imagesavealpha($size_image, true);
                                break;
                        }
                        
                        if (isset($size_image)) {
                            imagewebp($size_image, $size_webp_filepath, $quality);
                            imagedestroy($size_image);
                        }
                    }
                }
            }
        }
    }
    
    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'watchmodmarket_generate_webp_versions', 10, 2);

/**
 * Add AVIF support (if available in PHP 8.1+)
 */
function watchmodmarket_enable_avif_support() {
    // Check if AVIF is supported by PHP
    if (function_exists('imageavif')) {
        add_filter('mime_types', function($mimes) {
            $mimes['avif'] = 'image/avif';
            return $mimes;
        });
        
        add_filter('file_is_displayable_image', function($result, $path) {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'avif') {
                return true;
            }
            return $result;
        }, 10, 2);
    }
}
add_action('init', 'watchmodmarket_enable_avif_support');

/**
 * Generate AVIF versions of uploaded images if PHP 8.1+
 */
function watchmodmarket_generate_avif_versions($metadata, $attachment_id) {
    // Check if imageavif function exists (PHP 8.1+)
    if (!function_exists('imageavif')) {
        return $metadata;
    }
    
    // Check mime type
    $mime_type = get_post_mime_type($attachment_id);
    if (strpos($mime_type, 'image/') !== 0 || $mime_type === 'image/avif') {
        return $metadata;
    }
    
    // Get quality setting from theme customizer (default 85%)
    $quality = get_theme_mod('image_quality', 85);
    
    // Get upload directory
    $upload_dir = wp_upload_dir();
    $filepath = $upload_dir['basedir'] . '/' . $metadata['file'];
    
    if (file_exists($filepath)) {
        $avif_filepath = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $filepath);
        
        // Create image resource based on original file
        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filepath);
                
                // Handle PNG transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($filepath);
                break;
            default:
                return $metadata;
        }
        
        // Convert and save AVIF version
        if ($image) {
            imageavif($image, $avif_filepath, $quality);
            imagedestroy($image);
            
            // Generate AVIF versions for all sizes
            if (!empty($metadata['sizes'])) {
                foreach ($metadata['sizes'] as $size => $size_data) {
                    $size_filepath = dirname($filepath) . '/' . $size_data['file'];
                    $size_avif_filepath = preg_replace('/\.(jpe?g|png|webp)$/i', '.avif', $size_filepath);
                    
                    if (file_exists($size_filepath)) {
                        switch ($mime_type)