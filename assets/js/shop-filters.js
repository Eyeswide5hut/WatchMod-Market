// shop-filters.js
document.addEventListener('DOMContentLoaded', function () {
    // Filter form submission
    const filterForm = document.getElementById('shop-filter-form');
    const applyFiltersBtn = document.getElementById('apply-filters');
    const clearFiltersBtn = document.getElementById('clear-filters');

    if (filterForm) {
        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            applyFilters();
        });
    }

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function (e) {
            e.preventDefault();
            applyFilters();
        });
    }

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function (e) {
            e.preventDefault();
            clearFilters();
        });
    }

    // Filter toggle for mobile
    const filterToggleBtn = document.querySelector('.filter-toggle-btn');
    const filterBlock = document.getElementById('filter-block');

    if (filterToggleBtn && filterBlock) {
        filterToggleBtn.addEventListener('click', function () {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            filterBlock.classList.toggle('active');
        });
    }

    // Apply filters
    function applyFilters() {
        const form = document.getElementById('shop-filter-form');
        const formData = new FormData(form);
        const params = new URLSearchParams();

        // Add selected categories
        const categories = [];
        form.querySelectorAll('input[name="category[]"]:checked').forEach(input => {
            categories.push(input.value);
        });
        if (categories.length) {
            params.set('product_cat', categories.join(','));
        }

        // Add price range
        const minPrice = form.querySelector('input[name="min_price"]').value;
        const maxPrice = form.querySelector('input[name="max_price"]').value;
        if (minPrice) params.set('min_price', minPrice);
        if (maxPrice) params.set('max_price', maxPrice);

        // Add rating
        const rating = form.querySelector('input[name="rating"]:checked');
        if (rating) params.set('rating', rating.value);

        // Add stock status
        const stock = form.querySelector('input[name="stock"]:checked');
        if (stock) params.set('stock', stock.value);

        // Redirect with query parameters
        const baseUrl = window.location.pathname;
        window.location.href = baseUrl + '?' + params.toString();
    }

    // Clear filters
    function clearFilters() {
        window.location.href = window.location.pathname;
    }

    // Handle sorting
    const sortSelect = document.getElementById('orderby');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('orderby', this.value);
            window.location.href = url.toString();
        });

        // Set current sort value
        const urlParams = new URLSearchParams(window.location.search);
        const currentSort = urlParams.get('orderby');
        if (currentSort) {
            sortSelect.value = currentSort;
        }
    }

    // Handle view toggle
    const gridViewBtn = document.querySelector('.view-grid');
    const listViewBtn = document.querySelector('.view-list');
    const productGrid = document.querySelector('.shop-products');

    if (gridViewBtn && listViewBtn && productGrid) {
        gridViewBtn.addEventListener('click', function () {
            productGrid.classList.remove('list-view');
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
        });

        listViewBtn.addEventListener('click', function () {
            productGrid.classList.add('list-view');
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
        });
    }
});