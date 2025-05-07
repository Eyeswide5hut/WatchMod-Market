/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 */
(function($) {
    'use strict';

    // Site title and description.
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('.site-title a').text(to);
        });
    });
    
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('.site-description').text(to);
        });
    });

    // Hero section
    wp.customize('hero_title', function(value) {
        value.bind(function(to) {
            $('.hero-content h1').text(to);
        });
    });
    
    wp.customize('hero_tagline', function(value) {
        value.bind(function(to) {
            $('.hero-content .tagline').text(to);
        });
    });
})(jQuery);