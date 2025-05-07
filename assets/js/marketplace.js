/**
 * Watch Marketplace JavaScript
 * Enhances the functionality of the marketplace page
 */

jQuery(document).ready(function ($) {
    // Variables
    const filterForm = $('.filter-form');
    const listingCards = $('.listing-card');
    const messageButtons = $('.listing-actions .btn-secondary');
    const offerButtons = $('.listing-actions .btn-primary');

    /**
     * Initialize marketplace functionality
     */
    function initMarketplace() {
        setupFilters();
        setupListingCards();
        setupMessageButtons();
        setupOfferButtons();
    }

    /**
     * Set up filter functionality
     */
    function setupFilters() {
        // Auto-submit filter form when selects change
        $('#brand-filter, #condition-filter').on('change', function () {
            if ($(this).val() !== '') {
                // Only auto-submit for dropdown changes, not price inputs
                filterForm.submit();
            }
        });

        // Price range validation
        $('#price-min, #price-max').on('input', function () {
            const minPrice = parseInt($('#price-min').val()) || 0;
            const maxPrice = parseInt($('#price-max').val()) || 0;

            // If max price is set and less than min price, adjust max price
            if (maxPrice > 0 && maxPrice < minPrice) {
                $('#price-max').val(minPrice);
            }
        });
    }

    /**
     * Set up listing card interactions
     */
    function setupListingCards() {
        // Add hover effects and click handling
        listingCards.each(function () {
            $(this).on('click', function (e) {
                // Only navigate to listing page if not clicking on a button or link
                if (!$(e.target).closest('a, button').length) {
                    const listingUrl = $(this).find('.listing-title a').attr('href');
                    if (listingUrl) {
                        window.location.href = listingUrl;
                    }
                }
            });
        });
    }

    /**
     * Set up message seller buttons
     */
    function setupMessageButtons() {
        messageButtons.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const productId = $(this).closest('.listing-card').data('product-id');
            const sellerName = $(this).closest('.listing-card').find('.seller-name').text().replace('Seller: ', '');

            // Open message modal
            openMessageModal(productId, sellerName);
        });
    }

    /**
     * Set up make offer buttons
     */
    function setupOfferButtons() {
        offerButtons.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const productId = $(this).closest('.listing-card').data('product-id');
            const productTitle = $(this).closest('.listing-card').find('.listing-title').text().trim();
            const askingPrice = $(this).closest('.listing-card').find('.listing-price').text().trim().replace(',', '');

            // Open offer modal
            openOfferModal(productId, productTitle, askingPrice);
        });
    }

    /**
     * Open message modal
     * @param {number} productId - The product ID
     * @param {string} sellerName - The seller's name
     */
    function openMessageModal(productId, sellerName) {
        // Create modal HTML
        const modalHtml = `
            <div class="marketplace-modal" id="message-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Message to ${sellerName}</h3>
                        <button type="button" class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="message-form">
                            <input type="hidden" name="product_id" value="${productId}">
                            <input type="hidden" name="recipient" value="${sellerName}">
                            
                            <div class="form-group">
                                <label for="message-subject">Subject</label>
                                <input type="text" id="message-subject" name="subject" placeholder="Question about your listing" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message-body">Your Message</label>
                                <textarea id="message-body" name="message" rows="5" placeholder="Type your message here..." required></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary cancel-modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        // Append modal to body
        $('body').append(modalHtml);

        // Setup close functionality
        $('.close-modal, .cancel-modal').on('click', function () {
            $('#message-modal').remove();
        });

        // Handle form submission
        $('#message-form').on('submit', function (e) {
            e.preventDefault();

            // Here you would typically send the data via AJAX
            // For demo purposes, we'll just show a success message

            $('#message-modal .modal-body').html(`
                <div class="success-message">
                    <div class="success-icon">✓</div>
                    <h4>Message Sent!</h4>
                    <p>Your message has been sent to ${sellerName}. They will be notified and can respond via your messages inbox.</p>
                    <button class="btn btn-primary close-success">Close</button>
                </div>
            `);

            $('.close-success').on('click', function () {
                $('#message-modal').remove();
            });
        });
    }

    /**
     * Open offer modal
     * @param {number} productId - The product ID
     * @param {string} productTitle - The product title
     * @param {string} askingPrice - The asking price
     */
    function openOfferModal(productId, productTitle, askingPrice) {
        // Parse asking price as a number
        const price = parseFloat(askingPrice.replace(/,/g, ''));
        const suggestedOffer = Math.round(price * 0.9); // 90% of asking price as a suggestion

        // Create modal HTML
        const modalHtml = `
            <div class="marketplace-modal" id="offer-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Make an Offer</h3>
                        <button type="button" class="close-modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="listing-summary">
                            <h4>${productTitle}</h4>
                            <div class="price-info">
                                <span class="label">Asking Price:</span>
                                <span class="value">${askingPrice}</span>
                            </div>
                        </div>
                        
                        <form id="offer-form">
                            <input type="hidden" name="product_id" value="${productId}">
                            
                            <div class="form-group">
                                <label for="offer-amount">Your Offer</label>
                                <div class="offer-input">
                                    <span class="currency-symbol">$</span>
                                    <input type="number" id="offer-amount" name="offer_amount" 
                                           value="${suggestedOffer}" step="1" min="1" required>
                                </div>
                                <div class="offer-hint">Suggested: ${suggestedOffer} (90% of asking price)</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="offer-message">Message (Optional)</label>
                                <textarea id="offer-message" name="message" rows="3" 
                                          placeholder="Include any additional information with your offer..."></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary cancel-modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Submit Offer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        // Append modal to body
        $('body').append(modalHtml);

        // Setup close functionality
        $('.close-modal, .cancel-modal').on('click', function () {
            $('#offer-modal').remove();
        });

        // Handle form submission
        $('#offer-form').on('submit', function (e) {
            e.preventDefault();

            const offerAmount = $('#offer-amount').val();

            // Here you would typically send the data via AJAX
            // For demo purposes, we'll just show a success message

            $('#offer-modal .modal-body').html(`
                <div class="success-message">
                    <div class="success-icon">✓</div>
                    <h4>Offer Submitted!</h4>
                    <p>Your offer of ${offerAmount} has been submitted to the seller. You'll be notified when they respond.</p>
                    <button class="btn btn-primary close-success">Close</button>
                </div>
            `);

            $('.close-success').on('click', function () {
                $('#offer-modal').remove();
            });
        });
    }

    // Initialize the marketplace
    initMarketplace();

    // Add modal styles dynamically
    const modalStyles = `
        .marketplace-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-content {
            background-color: white;
            width: 90%;
            max-width: 500px;
            border: 3px solid var(--color-dark);
            box-shadow: 8px 8px 0 rgba(0, 0, 0, 0.2);
            animation: modalAppear 0.3s ease-out;
        }
        
        @keyframes modalAppear {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .modal-header {
            padding: 15px 20px;
            border-bottom: 2px solid var(--color-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .listing-summary {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .listing-summary h4 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        
        .price-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .price-info .label {
            font-weight: 600;
        }
        
        .price-info .value {
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--color-dark);
            font-family: var(--font-body);
        }
        
        .offer-input {
            display: flex;
            align-items: center;
        }
        
        .currency-symbol {
            padding: 10px;
            background-color: var(--color-light);
            border: 2px solid var(--color-dark);
            border-right: none;
            font-weight: 700;
        }
        
        .offer-input input {
            border-left: none;
        }
        
        .offer-hint {
            margin-top: 5px;
            font-size: 0.9rem;
            color: var(--color-light-text);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }
        
        .success-message {
            text-align: center;
            padding: 20px 0;
        }
        
        .success-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background-color: var(--color-success);
            color: white;
            border-radius: 50%;
            font-size: 30px;
            margin-bottom: 15px;
        }
        
        .success-message h4 {
            margin-top: 0;
            font-size: 1.4rem;
            margin-bottom: 10px;
        }
        
        .success-message p {
            margin-bottom: 20px;
        }
    `;

    // Add styles to head
    $('head').append(`<style>${modalStyles}</style>`);
});