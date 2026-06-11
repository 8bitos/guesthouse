document.addEventListener('DOMContentLoaded', function() {
    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -80px 0px', // Trigger slightly before entering viewport
        threshold: 0.05
    };
    
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target); // Reveal only once
            }
        });
    }, observerOptions);
    
    // Function to observe all reveal elements
    function observeElements() {
        document.querySelectorAll('.reveal').forEach(el => {
            observer.observe(el);
        });
    }

    observeElements();

    // Export globally in case dynamic content is loaded (e.g. pagination, AJAX)
    window.observeScrollReveals = observeElements;

    // --- Loading UI Initialization ---
    // Inject the top loading bar element
    const topBar = document.createElement('div');
    topBar.className = 'top-loading-bar';
    document.body.appendChild(topBar);

    // Global loading helpers
    window.startTopLoading = function() {
        topBar.classList.remove('finished');
        topBar.classList.add('animating');
    };

    window.stopTopLoading = function() {
        topBar.classList.remove('animating');
        topBar.classList.add('finished');
    };

    // Trigger loader on page unload (navigating to another page)
    window.addEventListener('beforeunload', function() {
        window.startTopLoading();
    });

    // Handle form submissions globally
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        // Skip GET forms or forms with explicit no-spinner flag
        if (form.method.toLowerCase() === 'get' || form.dataset.noSpinner === 'true') {
            return;
        }

        window.startTopLoading();

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            // Skip if the button is already disabled or already showing a spinner to avoid duplicate updates
            if (submitBtn.disabled || submitBtn.querySelector('.animate-spin')) {
                return;
            }

            // Prevent duplicate clicks
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');

            // Find or default the loading message
            const loadingText = submitBtn.dataset.loadingText || 'Processing...';

            if (loadingText === 'icon-only') {
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-4 w-4 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
            } else {
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>${loadingText}</span>
                `;
            }
        }
    });
});
