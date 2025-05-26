/**
 * Enhanced Shop Filters JavaScript
 * Save as: /assets/js/shop-filters-enhanced.js
 */

(function ($) {
    'use strict';

    const ShopFilters = {
        init: function () {
            this.bindEvents();
            this.initializeFilters();
            this.updateActiveFilters();
        },

        bindEvents: function () {
            // Filter form submission
            $(document).on('submit', '.filter-form', this.handleFilterSubmit);

            // Apply filters button
            $(document).on('click', '.filter-apply-btn', this.applyFilters.bind(this));

            // Clear filters button
            $(document).on('click', '.filter-reset-btn, .clear-all-filters', this.clearFilters.bind(this));

            // Individual filter removal
            $(document).on('click', '.remove-filter', this.removeFilter.bind(this));

            // Search form
            $(document).on('submit', '.search-form', this.handleSearch.bind(this));

            // Sorting change
            $(document).on('change', '.orderby', this.handleSorting.bind(this));

            // Price inputs
            $(document).on('change', '#min-price, #max-price', this.validatePriceInputs);

            // Filter toggle for mobile
            $(document).on('click', '.toggle-filters', this.toggleFilters);

            // Real-time search (with debounce)
            let searchTimeout;
            $(document).on('input', '.search-field', function () {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val();

                searchTimeout = setTimeout(() => {
                    if (searchTerm.length >= 3 || searchTerm.length === 0) {
                        ShopFilters.performSearch(searchTerm);
                    }
                }, 500);
            });

            // Pagination clicks
            $(document).on('click', '.shop-pagination a', this.handlePagination.bind(this));

            // Filter group collapsing
            $(document).on('click', '.filter-group h4', this.toggleFilterGroup);
        },

        initializeFilters: function () {
            // Set initial values from URL
            const urlParams = new URLSearchParams(window.location.search);

            // Set category checkboxes
            const categories = urlParams.get('category');
            if (categories) {
                categories.split(',').forEach(category => {
                    $(`input[name="category[]"][value="${category}"]`).prop('checked', true);
                });
            }

            // Set price inputs
            const minPrice = urlParams.get('min_price');
            const maxPrice = urlParams.get('max_price');

            if (minPrice) $('#min-price').val(minPrice);
            if (maxPrice) $('#max-price').val(maxPrice);

            // Set other filters
            const partType = urlParams.get('filter_part_type');
            if (partType) {
                $(`input[name="filter_part_type"][value="${partType}"]`).prop('checked', true);
            }

            const compatibility = urlParams.get('filter_compatible_with');
            if (compatibility) {
                $(`input[name="filter_compatible_with"][value="${compatibility}"]`).prop('checked', true);
            }

            const stockStatus = urlParams.get('stock_status');
            if (stockStatus) {
                $(`input[name="stock_status"][value="${stockStatus}"]`).prop('checked', true);
            }

            const orderby = urlParams.get('orderby');
            if (orderby) {
                $('.orderby').val(orderby);
            }
        },

        handleFilterSubmit: function (e) {
            e.preventDefault();
            ShopFilters.applyFilters();
        },

        applyFilters: function () {
            const filters = this.getActiveFilters();
            this.updateURL(filters);
            this.performAjaxFilter(filters);
        },

        getActiveFilters: function () {
            const filters = {};

            // Categories
            const categories = [];
            $('input[name="category[]"]:checked').each(function () {
                categories.push($(this).val());
            });
            if (categories.length > 0) {
                filters.categories = categories;
            }

            // Price range
            const minPrice = $('#min-price').val();
            const maxPrice = $('#max-price').val();

            if (minPrice) filters.min_price = minPrice;
            if (maxPrice) filters.max_price = maxPrice;

            // Part type
            const partType = $('input[name="filter_part_type"]:checked').val();
            if (partType) filters.part_type = partType;

            // Compatible with
            const compatibility = $('input[name="filter_compatible_with"]:checked').val();
            if (compatibility) filters.compatible_with = compatibility;

            // Stock status
            const stockStatus = $('input[name="stock_status"]:checked').val();
            if (stockStatus) filters.stock_status = stockStatus;

            // Rating
            const rating = $('input[name="rating"]:checked').val();
            if (rating) filters.rating = rating;

            // Sorting
            const orderby = $('.orderby').val();
            if (orderby) filters.orderby = orderby;

            return filters;
        },

        performAjaxFilter: function (filters, page = 1) {
            const $productsContainer = $('.shop-products');
            const $pagination = $('.shop-pagination');

            // Show loading state
            $productsContainer.addClass('loading').html('<div class="products-loading"><div class="spinner"></div><p>' + shopFilters.i18n.filtering + '</p></div>');

            const ajaxData = {
                action: 'filter_products',
                nonce: shopFilters.filter_nonce,
                page: page,
                ...filters
            };

            $.ajax({
                url: shopFilters.ajax_url,
                type: 'POST',
                data: ajaxData,
                success: function (response) {
                    if (response.success) {
                        $productsContainer.removeClass('loading').html(response.data.products);

                        // Update pagination
                        if (response.data.pagination) {
                            $pagination.html(response.data.pagination);
                        } else {
                            $pagination.empty();
                        }

                        // Update results count
                        $('.shop-results-text').html(
                            `Showing 1-${Math.min(12, response.data.found_posts)} of ${response.data.found_posts} products`
                        );

                        // Update active filters display
                        ShopFilters.updateActiveFilters();

                        // Scroll to top of products
                        $('html, body').animate({
                            scrollTop: $productsContainer.offset().top - 100
                        }, 500);

                    } else {
                        $productsContainer.removeClass('loading').html('<div class="no-products-found"><p>' + shopFilters.i18n.no_results + '</p></div>');
                    }
                },
                error: function () {
                    $productsContainer.removeClass('loading').html('<div class="filter-error"><p>Error loading products. Please try again.</p></div>');
                }
            });
        },

        clearFilters: function (e) {
            e.preventDefault();

            // Reset all form inputs
            $('.filter-form')[0].reset();

            // Clear URL parameters
            const url = new URL(window.location);
            const params = ['category', 'min_price', 'max_price', 'filter_part_type', 'filter_compatible_with', 'stock_status', 'rating', 'orderby'];

            params.forEach(param => {
                url.searchParams.delete(param);
            });

            // Update URL without reload
            window.history.pushState({}, '', url);

            // Apply empty filters
            this.performAjaxFilter({});
        },

        removeFilter: function (e) {
            e.preventDefault();

            const $pill = $(e.currentTarget).closest('.filter-pill');
            const filterType = $pill.data('filter-type');
            const filterValue = $pill.data('filter-value');

            // Remove specific filter
            switch (filterType) {
                case 'category':
                    $(`input[name="category[]"][value="${filterValue}"]`).prop('checked', false);
                    break;
                case 'price':
                    $('#min-price, #max-price').val('');
                    break;
                case 'part_type':
                    $('input[name="filter_part_type"]').prop('checked', false);
                    break;
                case 'compatible_with':
                    $('input[name="filter_compatible_with"]').prop('checked', false);
                    break;
                case 'stock_status':
                    $('input[name="stock_status"]').prop('checked', false);
                    break;
                case 'rating':
                    $('input[name="rating"]').prop('checked', false);
                    break;
                case 'orderby':
                    $('.orderby').val('');
                    break;
            }

            // Apply filters after removal
            this.applyFilters();
        },

        handleSearch: function (e) {
            e.preventDefault();

            const searchTerm = $('.search-field').val();
            this.performSearch(searchTerm);
        },

        performSearch: function (searchTerm) {
            if (!searchTerm) {
                return;
            }

            const $productsContainer = $('.shop-products');

            // Show loading state
            $productsContainer.addClass('loading').html('<div class="products-loading"><div class="spinner"></div><p>Searching...</p></div>');

            $.ajax({
                url: shopFilters.ajax_url,
                type: 'POST',
                data: {
                    action: 'product_search',
                    nonce: shopFilters.search_nonce,
                    search_term: searchTerm
                },
                success: function (response) {
                    if (response.success) {
                        $productsContainer.removeClass('loading').html(response.data.products);

                        // Update results count
                        $('.shop-results-text').html(
                            `Found ${response.data.found_posts} products for "${response.data.search_term}"`
                        );

                        // Update URL with search parameter
                        const url = new URL(window.location);
                        url.searchParams.set('s', searchTerm);
                        window.history.pushState({}, '', url);

                    } else {
                        $productsContainer.removeClass('loading').html('<div class="no-products-found"><p>' + shopFilters.i18n.no_results + '</p></div>');
                    }
                },
                error: function () {
                    $productsContainer.removeClass('loading').html('<div class="search-error"><p>Error searching products. Please try again.</p></div>');
                }
            });
        },

        handleSorting: function (e) {
            const sortValue = $(e.target).val();
            const filters = this.getActiveFilters();

            if (sortValue) {
                filters.orderby = sortValue;
            }

            this.updateURL(filters);
            this.performAjaxFilter(filters);
        },

        handlePagination: function (e) {
            e.preventDefault();

            const $link = $(e.currentTarget);
            const page = this.getPageFromUrl($link.attr('href'));
            const filters = this.getActiveFilters();

            this.performAjaxFilter(filters, page);
        },

        getPageFromUrl: function (url) {
            const matches = url.match(/page\/(\d+)/);
            return matches ? parseInt(matches[1]) : 1;
        },

        updateURL: function (filters) {
            const url = new URL(window.location);

            // Clear existing filter parameters
            const filterParams = ['category', 'min_price', 'max_price', 'filter_part_type', 'filter_compatible_with', 'stock_status', 'rating', 'orderby'];
            filterParams.forEach(param => {
                url.searchParams.delete(param);
            });

            // Add active filters to URL
            Object.keys(filters).forEach(key => {
                if (Array.isArray(filters[key])) {
                    url.searchParams.set(key, filters[key].join(','));
                } else {
                    url.searchParams.set(key, filters[key]);
                }
            });

            // Update URL without page reload
            window.history.pushState({}, '', url);
        },

        updateActiveFilters: function () {
            const $activeFiltersContainer = $('.active-filter-pills');
            const filters = this.getActiveFilters();

            $activeFiltersContainer.empty();

            // Add filter pills
            Object.keys(filters).forEach(filterType => {
                const filterValue = filters[filterType];

                if (Array.isArray(filterValue)) {
                    filterValue.forEach(value => {
                        this.addFilterPill(filterType, value, $activeFiltersContainer);
                    });
                } else {
                    this.addFilterPill(filterType, filterValue, $activeFiltersContainer);
                }
            });

            // Add clear all button if there are active filters
            if (Object.keys(filters).length > 1) {
                const $clearAll = $('<a href="#" class="clear-all-filters">' + shopFilters.i18n.clear_filters + '</a>');
                $activeFiltersContainer.append($clearAll);
            }

            // Show/hide active filters section
            if (Object.keys(filters).length > 0) {
                $('.active-filters-section').show();
            } else {
                $('.active-filters-section').hide();
            }
        },

        addFilterPill: function (filterType, filterValue, $container) {
            const label = this.getFilterLabel(filterType, filterValue);

            const $pill = $(`
                <div class="filter-pill" data-filter-type="${filterType}" data-filter-value="${filterValue}">
                    ${label}
                    <span class="remove-filter">×</span>
                </div>
            `);

            $container.append($pill);
        },

        getFilterLabel: function (filterType, filterValue) {
            switch (filterType) {
                case 'categories':
                    return `Category: ${this.getCategoryName(filterValue)}`;
                case 'min_price':
                    return `Min Price: ${filterValue}`;
                case 'max_price':
                    return `Max Price: ${filterValue}`;
                case 'part_type':
                    return `Part Type: ${this.formatValue(filterValue)}`;
                case 'compatible_with':
                    return `Compatible: ${this.formatValue(filterValue)}`;
                case 'stock_status':
                    return `Stock: ${this.formatStockStatus(filterValue)}`;
                case 'rating':
                    return `Rating: ${filterValue}+ stars`;
                case 'orderby':
                    return `Sort: ${this.formatSortOption(filterValue)}`;
                default:
                    return `${filterType}: ${filterValue}`;
            }
        },

        getCategoryName: function (slug) {
            const $option = $(`input[name="category[]"][value="${slug}"]`).closest('label');
            return $option.text().trim() || slug;
        },

        formatValue: function (value) {
            return value.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },

        formatStockStatus: function (status) {
            const statusMap = {
                'instock': 'In Stock',
                'outofstock': 'Out of Stock',
                'onbackorder': 'On Backorder'
            };
            return statusMap[status] || status;
        },

        formatSortOption: function (option) {
            const sortMap = {
                'popularity': 'Popularity',
                'rating': 'Rating',
                'date': 'Latest',
                'price': 'Price: Low to High',
                'price-desc': 'Price: High to Low'
            };
            return sortMap[option] || option;
        },

        validatePriceInputs: function () {
            const $minPrice = $('#min-price');
            const $maxPrice = $('#max-price');

            const minVal = parseFloat($minPrice.val()) || 0;
            const maxVal = parseFloat($maxPrice.val()) || 0;

            // Ensure min is not greater than max
            if (minVal > 0 && maxVal > 0 && minVal > maxVal) {
                $maxPrice.val($minPrice.val());
            }
        },

        toggleFilters: function () {
            const $button = $(this);
            const $filterOptions = $('#filter-options');
            const isExpanded = $button.attr('aria-expanded') === 'true';

            $button.attr('aria-expanded', !isExpanded);

            if (isExpanded) {
                $filterOptions.slideUp();
            } else {
                $filterOptions.slideDown();
            }
        },

        toggleFilterGroup: function () {
            const $header = $(this);
            const $content = $header.next('.filter-collapsible');
            const isCollapsed = $header.hasClass('collapsed');

            if (isCollapsed) {
                $content.slideDown();
                $header.removeClass('collapsed');
            } else {
                $content.slideUp();
                $header.addClass('collapsed');
            }
        },

        // Utility method to debounce function calls
        debounce: function (func, wait) {
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
    };

    // Initialize when document is ready
    $(document).ready(function () {
        ShopFilters.init();
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function () {
        location.reload();
    });

})(jQuery);

// Add CSS for loading states and animations
const shopFiltersCSS = `
    .products-loading {
        text-align: center;
        padding: 4rem 0;
        color: #666;
    }

    .products-loading .spinner {
        width: 40px;
        height: 40px;
        margin: 0 auto 1rem;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #ff5c00;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .shop-products.loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        color: #495057;
        margin: 0.25rem 0.25rem 0.25rem 0;
        transition: all 0.15s ease;
    }

    .filter-pill:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }

    .remove-filter {
        cursor: pointer;
        font-weight: bold;
        color: #6c757d;
        padding: 0.125rem 0.25rem;
        border-radius: 50%;
        line-height: 1;
    }

    .remove-filter:hover {
        background: #dc3545;
        color: white;
    }

    .active-filters-section {
        margin: 1rem 0;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
    }

    .active-filters-section h4 {
        margin: 0 0 0.75rem 0;
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
    }

    .clear-all-filters {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        background: #dc3545;
        color: white;
        text-decoration: none;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        margin-left: 0.5rem;
        transition: background 0.15s ease;
    }

    .clear-all-filters:hover {
        background: #c82333;
        color: white;
        text-decoration: none;
    }

    .no-products-found,
    .filter-error,
    .search-error {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .filter-group h4.collapsed::after {
        content: '+';
        float: right;
        font-weight: normal;
    }

    .filter-group h4:not(.collapsed)::after {
        content: '−';
        float: right;
        font-weight: normal;
    }

    .filter-group h4 {
        cursor: pointer;
        user-select: none;
        padding: 0.5rem 0;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0.75rem;
    }

    .filter-group h4:hover {
        color: #ff5c00;
    }

    @media (max-width: 768px) {
        .filter-pill {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .active-filters-section {
            margin: 0.5rem 0;
            padding: 0.75rem;
        }
    }
`;

// Inject CSS into the page
if (typeof document !== 'undefined') {
    const styleElement = document.createElement('style');
    styleElement.textContent = shopFiltersCSS;
    document.head.appendChild(styleElement);
}