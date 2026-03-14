/**
 * =============================================================================
 * GLOBAL ERROR HANDLER
 * File: public/assets/js/error-handler.js
 * =============================================================================
 * Handles global JavaScript errors and unhandled promise rejections
 */

(function () {
    'use strict';

    /**
     * Global error event handler
     * Catches all unhandled JavaScript errors
     */
    window.addEventListener('error', function (event) {
        console.error('Global error:', {
            message: event.message,
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            error: event.error
        });

        // Show user-friendly message
        if (typeof showToast === 'function') {
            showToast('Đã xảy ra lỗi. Vui lòng tải lại trang.', 'error');
        } else {
            console.error('showToast not available');
        }

        // Prevent default browser error handling
        // event.preventDefault();
    });

    /**
     * Unhandled promise rejection handler
     * Catches all unhandled promise rejections
     */
    window.addEventListener('unhandledrejection', function (event) {
        console.error('Unhandled promise rejection:', {
            reason: event.reason,
            promise: event.promise
        });

        // Show user-friendly message
        if (typeof showToast === 'function') {
            showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
        } else {
            console.error('showToast not available');
        }

        // Prevent default browser error handling
        // event.preventDefault();
    });

    /**
     * Wrap async functions with error handling
     * @param {Function} fn - Async function to wrap
     * @returns {Function} Wrapped function with error handling
     */
    window.safeAsync = function (fn) {
        return async function (...args) {
            try {
                return await fn.apply(this, args);
            } catch (error) {
                console.error('Async error in', fn.name || 'anonymous function', ':', error);

                if (typeof showToast === 'function') {
                    showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
                }

                throw error; // Re-throw for debugging
            }
        };
    };

    /**
     * Wrap sync functions with error handling
     * @param {Function} fn - Function to wrap
     * @returns {Function} Wrapped function with error handling
     */
    window.safeFn = function (fn) {
        return function (...args) {
            try {
                return fn.apply(this, args);
            } catch (error) {
                console.error('Error in', fn.name || 'anonymous function', ':', error);

                if (typeof showToast === 'function') {
                    showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
                }

                throw error; // Re-throw for debugging
            }
        };
    };

    /**
     * Log error to server (optional - implement if needed)
     * @param {Error} error - Error object
     * @param {Object} context - Additional context
     */
    window.logErrorToServer = function (error, context = {}) {
        // Implement server-side error logging here
        // Example:
        // fetch('/api/log-error', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({
        //         message: error.message,
        //         stack: error.stack,
        //         context: context,
        //         userAgent: navigator.userAgent,
        //         url: window.location.href,
        //         timestamp: new Date().toISOString()
        //     })
        // });
    };

    console.log('✅ Error handler initialized');
})();
