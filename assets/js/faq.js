/**
 * FAQ functionality for WatchModMarket - Modernized Version
 * This uses modern JavaScript patterns and more defensive programming
 */

// Use a module pattern to encapsulate functionality
const WMMFAQ = (function () {
    'use strict';

    // Store configuration
    const config = {
        selectors: {
            faqToggle: '.faq-toggle',
            faqQuestion: '.faq-question',
            faqAnswer: '.faq-answer',
            searchInput: '#faq-search-input',
            searchButton: '#faq-search-button',
            faqItem: '.faq-item',
            faqCategory: '.faq-category',
            noResultsFound: '.no-results-found',
            faqSearch: '.faq-search',
            resetSearch: '.reset-search',
            quickLink: '.quick-link',
            faqPage: '.faq-page'
        },
        classes: {
            faqAnswerOpen: 'faq-answer-open',
            searchActive: 'search-active',
            searchHighlight: 'search-highlight'
        },
        attributes: {
            ariaExpanded: 'aria-expanded',
            hidden: 'hidden'
        },
        // Default localization fallbacks
        localization: {
            noResultsTitle: 'No Results Found',
            noResultsText: 'We couldn\'t find any FAQs matching your search. Please try a different search term or browse the categories below.',
            resetSearch: 'Reset Search'
        }
    };

    // Store DOM elements and state
    let elements = {};
    let state = {
        initialized: false
    };

    /**
     * Initialize the FAQ component
     * @param {Object} options - Optional configuration to override defaults
     */
    function init(options = {}) {
        // Merge options with defaults
        if (options.localization) {
            config.localization = { ...config.localization, ...options.localization };
        }

        // Cache DOM elements
        cacheElements();

        // Only proceed if we have the necessary elements
        if (!elements.faqToggles.length) {
            console.warn('FAQ elements not found, skipping initialization');
            return;
        }

        // Initialize components
        initFAQToggle();
        initFAQSearch();
        initSmoothScroll();

        // Add CSS for search highlights
        addSearchHighlightStyles();

        state.initialized = true;
        console.log('FAQ component initialized');
    }

    /**
     * Cache DOM elements for better performance
     */
    function cacheElements() {
        elements = {};

        // Cache all selectors
        Object.keys(config.selectors).forEach(key => {
            const selector = config.selectors[key];
            const selectedElements = document.querySelectorAll(selector);

            // Store single element or NodeList based on result count
            elements[key] = selectedElements.length === 1 ?
                selectedElements[0] :
                selectedElements;
        });
    }

    /**
     * Initialize the toggle functionality for FAQ items
     */
    function initFAQToggle() {
        if (!elements.faqToggles.length) return;

        elements.faqToggles.forEach(toggle => {
            toggle.addEventListener('click', function () {
                const faqQuestion = this.closest(config.selectors.faqQuestion);
                if (!faqQuestion) return;

                const faqAnswer = faqQuestion.nextElementSibling;
                if (!faqAnswer || !faqAnswer.matches(config.selectors.faqAnswer)) return;

                const isExpanded = this.getAttribute(config.attributes.ariaExpanded) === 'true';

                // Toggle the current FAQ
                this.setAttribute(config.attributes.ariaExpanded, !isExpanded);
                faqAnswer.toggleAttribute(config.attributes.hidden, isExpanded);

                if (!isExpanded) {
                    // Add animation class
                    faqAnswer.classList.add(config.classes.faqAnswerOpen);

                    // Smooth scroll if on mobile
                    if (window.innerWidth < 768) {
                        const questionTop = this.getBoundingClientRect().top + window.pageYOffset - 20;
                        window.scrollTo({
                            top: questionTop,
                            behavior: 'smooth'
                        });
                    }
                } else {
                    // Remove animation class
                    faqAnswer.classList.remove(config.classes.faqAnswerOpen);
                }
            });
        });
    }

    /**
     * Initialize the search functionality for FAQs
     */
    function initFAQSearch() {
        const searchInput = elements.searchInput;
        const searchButton = elements.searchButton;
        const faqItems = elements.faqItem;
        const faqCategories = elements.faqCategory;

        if (!searchInput || !searchButton || !faqItems.length) return;

        // Get or create no results message element
        let noResultsMessage = elements.noResultsFound;

        if (!noResultsMessage) {
            noResultsMessage = createNoResultsMessage();
        }

        // Add event listeners
        searchButton.addEventListener('click', function (e) {
            e.preventDefault();
            performSearch();
        });

        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        /**
         * Create the no results message element
         * @returns {HTMLElement} The created element
         */
        function createNoResultsMessage() {
            const noResultsElement = document.createElement('div');
            noResultsElement.className = 'no-results-found';
            noResultsElement.hidden = true;

            noResultsElement.innerHTML = `
                <h3>${config.localization.noResultsTitle}</h3>
                <p>${config.localization.noResultsText}</p>
                <button class="btn btn-secondary reset-search">${config.localization.resetSearch}</button>
            `;

            // Find where to append it
            const faqSearch = elements.faqSearch;
            if (faqSearch) {
                faqSearch.insertAdjacentElement('afterend', noResultsElement);
            } else {
                // Fallback - append to the first FAQ category parent
                const firstCategory = faqCategories[0];
                if (firstCategory && firstCategory.parentNode) {
                    firstCategory.parentNode.insertBefore(noResultsElement, firstCategory);
                }
            }

            // Add event listener to reset button
            const resetButton = noResultsElement.querySelector(config.selectors.resetSearch);
            if (resetButton) {
                resetButton.addEventListener('click', resetSearch);
            }

            return noResultsElement;
        }

        /**
         * Perform search based on input value
         */
        function performSearch() {
            const searchTerm = searchInput.value.toLowerCase().trim();

            if (searchTerm === '') {
                resetSearch();
                return;
            }

            let resultsFound = false;

            // Hide all FAQ items initially
            faqItems.forEach(item => {
                item.hidden = true;
            });

            // Search through questions and answers
            faqItems.forEach(item => {
                const questionText = item.querySelector(config.selectors.faqQuestion)?.textContent.toLowerCase() || '';
                const answerText = item.querySelector(config.selectors.faqAnswer)?.textContent.toLowerCase() || '';

                if (questionText.includes(searchTerm) || answerText.includes(searchTerm)) {
                    item.hidden = false;

                    // Expand the item to show the match
                    const toggle = item.querySelector(config.selectors.faqToggle);
                    const answer = item.querySelector(config.selectors.faqAnswer);

                    if (toggle && answer) {
                        toggle.setAttribute(config.attributes.ariaExpanded, true);
                        answer.removeAttribute(config.attributes.hidden);
                        answer.classList.add(config.classes.faqAnswerOpen);
                    }

                    // Highlight matching text
                    highlightMatches(item, searchTerm);

                    resultsFound = true;
                }
            });

            // Show/hide categories based on visible items
            if (faqCategories.length) {
                faqCategories.forEach(category => {
                    const visibleItems = category.querySelectorAll(`${config.selectors.faqItem}:not([hidden])`);
                    category.hidden = visibleItems.length === 0;
                });
            }

            // Show no results message if needed
            if (noResultsMessage) {
                noResultsMessage.hidden = resultsFound;
            }

            // Add active search indicator
            const faqPage = elements.faqPage;
            if (faqPage) {
                faqPage.classList.add(config.classes.searchActive);
            }

            // Scroll to first result
            if (resultsFound) {
                const firstResult = document.querySelector(`${config.selectors.faqItem}:not([hidden])`);
                if (firstResult) {
                    setTimeout(() => {
                        const scrollTop = firstResult.getBoundingClientRect().top + window.pageYOffset - 100;
                        window.scrollTo({
                            top: scrollTop,
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            }
        }

        /**
         * Reset the search state and UI
         */
        function resetSearch() {
            if (searchInput) searchInput.value = '';

            // Reset all FAQ items and categories
            faqItems.forEach(item => {
                item.hidden = false;
            });

            if (faqCategories.length) {
                faqCategories.forEach(category => {
                    category.hidden = false;
                });
            }

            // Reset highlights
            document.querySelectorAll(`${config.selectors.faqQuestion}, ${config.selectors.faqAnswer}`).forEach(el => {
                el.innerHTML = el.textContent;
            });

            // Reset expanded state
            elements.faqToggles.forEach(toggle => {
                toggle.setAttribute(config.attributes.ariaExpanded, false);
            });

            document.querySelectorAll(config.selectors.faqAnswer).forEach(answer => {
                answer.hidden = true;
                answer.classList.remove(config.classes.faqAnswerOpen);
            });

            // Hide no results message
            if (noResultsMessage) {
                noResultsMessage.hidden = true;
            }

            // Remove active search indicator
            const faqPage = elements.faqPage;
            if (faqPage) {
                faqPage.classList.remove(config.classes.searchActive);
            }
        }

        /**
         * Highlight matching text in the FAQ item
         * @param {HTMLElement} item - The FAQ item element
         * @param {string} searchTerm - The term to highlight
         */
        function highlightMatches(item, searchTerm) {
            const question = item.querySelector(config.selectors.faqToggle);
            const answer = item.querySelector(config.selectors.faqAnswer);

            if (!question || !answer) return;

            // Remove any existing spans first to avoid nested highlights
            if (question.originalHTML === undefined) {
                question.originalHTML = question.innerHTML;
            } else {
                question.innerHTML = question.originalHTML;
            }

            // Highlight matching text in question
            const questionText = question.textContent;
            const questionHighlighted = questionText.replace(
                new RegExp(escapeRegExp(searchTerm), 'gi'),
                match => `<span class="search-highlight">${match}</span>`
            );

            // Preserve toggle icon if it exists
            const toggleIcon = question.querySelector('.toggle-icon');
            const toggleIconHTML = toggleIcon ? toggleIcon.outerHTML : '';

            question.innerHTML = questionHighlighted;

            if (toggleIcon && toggleIconHTML) {
                question.insertAdjacentHTML('beforeend', toggleIconHTML);
            }

            // Highlight matching text in answer - work with text nodes to preserve HTML structure
            highlightTextNodes(answer, searchTerm);
        }

        /**
         * Recursively highlight text nodes without breaking HTML structure
         * @param {Node} element - The element to search within
         * @param {string} searchTerm - The term to highlight
         */
        function highlightTextNodes(element, searchTerm) {
            const childNodes = Array.from(element.childNodes);

            childNodes.forEach(node => {
                if (node.nodeType === Node.TEXT_NODE) {
                    // This is a text node
                    const text = node.nodeValue;
                    if (text.toLowerCase().includes(searchTerm.toLowerCase())) {
                        // Create a temporary element
                        const temp = document.createElement('span');
                        // Replace the matching text with highlighted version
                        temp.innerHTML = text.replace(
                            new RegExp(escapeRegExp(searchTerm), 'gi'),
                            match => `<span class="search-highlight">${match}</span>`
                        );

                        // Replace the text node with our highlighted version
                        const fragment = document.createDocumentFragment();
                        while (temp.firstChild) {
                            fragment.appendChild(temp.firstChild);
                        }

                        node.parentNode.replaceChild(fragment, node);
                    }
                } else if (node.nodeType === Node.ELEMENT_NODE) {
                    // This is an element node, recurse into it
                    highlightTextNodes(node, searchTerm);
                }
            });
        }
    }

    /**
     * Initialize smooth scrolling for FAQ category links
     */
    function initSmoothScroll() {
        const quickLinks = elements.quickLink;

        if (!quickLinks.length) return;

        quickLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                if (!targetId) return;

                const target = document.querySelector(targetId);

                if (target) {
                    const offsetTop = target.getBoundingClientRect().top + window.pageYOffset - 80;

                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });

                    // Set focus to the target for accessibility
                    target.setAttribute('tabindex', '-1');
                    target.focus({ preventScroll: true });
                }
            });
        });
    }

    /**
     * Add CSS for search highlighting
     */
    function addSearchHighlightStyles() {
        // Only add if not already present
        if (!document.querySelector('#faq-highlight-styles')) {
            const style = document.createElement('style');
            style.id = 'faq-highlight-styles';
            style.textContent = `.search-highlight { background-color: #FFFF00; font-weight: bold; }`;
            document.head.appendChild(style);
        }
    }

    /**
     * Helper function to escape special characters in regex
     * @param {string} string - The string to escape
     * @returns {string} Escaped string
     */
    function escapeRegExp(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    /**
     * Reset the FAQ system state - useful for single page apps or updates
     */
    function reset() {
        // Remove event listeners by re-initializing
        init();
    }

    // Public API
    return {
        init: init,
        reset: reset
    };
})();

// Initialize when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize with optional localization
    WMMFAQ.init({
        localization: window.faqLocalizations || {}
    });
});