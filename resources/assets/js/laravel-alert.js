/**
 * Laravel Alert JavaScript API
 * Client-side alert management with AJAX support
 */

class LaravelAlert {
    constructor() {
        this.config = {
            apiUrl: '/laravel-alert',
            csrfToken: this.getCsrfToken(),
            defaultTheme: 'bootstrap',
            defaultAnimation: 'fade',
            defaultPosition: 'top-right',
            autoDismiss: true,
            autoDismissDelay: 5000,
            maxAlerts: 5,
            stack: true
        };

        this.alerts = new Map();
        this.containers = new Map();
        this.eventListeners = new Map();

        this.init();
    }

    /**
     * Initialize the alert system
     */
    init() {
        this.setupEventListeners();
        this.loadExistingAlerts();
        this.setupAjaxHandlers();
        this.setupRealTimeUpdates();
    }

    /**
     * Get CSRF token from meta tag
     */
    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Listen for custom events
        document.addEventListener('laravel-alert-create', (event) => {
            this.createAlert(event.detail);
        });

        document.addEventListener('laravel-alert-dismiss', (event) => {
            this.dismissAlert(event.detail.id);
        });

        document.addEventListener('laravel-alert-clear', (event) => {
            this.clearAlerts();
        });

        // Listen for AJAX responses
        document.addEventListener('ajax:success', (event) => {
            this.handleAjaxResponse(event.detail);
        });

        // Listen for form submissions
        document.addEventListener('submit', (event) => {
            this.handleFormSubmission(event);
        });
    }

    /**
     * Load existing alerts from the page
     */
    loadExistingAlerts() {
        const alertElements = document.querySelectorAll('[data-alert-id]');
        alertElements.forEach(element => {
            const alertId = element.dataset.alertId;
            const alertData = this.parseAlertElement(element);
            this.alerts.set(alertId, alertData);
        });
    }

    /**
     * Parse alert element to extract data
     */
    parseAlertElement(element) {
        return {
            id: element.dataset.alertId,
            type: element.dataset.alertType,
            message: element.textContent.trim(),
            title: element.querySelector('.alert-title')?.textContent?.trim(),
            dismissible: element.classList.contains('alert-dismissible'),
            autoDismiss: element.dataset.autoDismiss === 'true',
            dismissDelay: parseInt(element.dataset.dismissDelay) || this.config.autoDismissDelay,
            animation: element.dataset.animation || this.config.defaultAnimation,
            position: element.dataset.position || this.config.defaultPosition,
            theme: element.dataset.theme || this.config.defaultTheme,
            element: element
        };
    }

    /**
     * Create a new alert
     */
    createAlert(options) {
        const alertId = this.generateId();
        const alertData = {
            id: alertId,
            type: options.type || 'info',
            message: options.message || '',
            title: options.title || null,
            dismissible: options.dismissible !== false,
            autoDismiss: options.autoDismiss !== false,
            dismissDelay: options.dismissDelay || this.config.autoDismissDelay,
            animation: options.animation || this.config.defaultAnimation,
            position: options.position || this.config.defaultPosition,
            theme: options.theme || this.config.defaultTheme,
            class: options.class || '',
            style: options.style || '',
            icon: options.icon || null,
            htmlContent: options.htmlContent || null,
            dataAttributes: options.dataAttributes || {},
            actions: options.actions || null
        };

        this.alerts.set(alertId, alertData);
        this.renderAlert(alertData);
        this.setupAlertEvents(alertId);

        return alertId;
    }

    /**
     * Render an alert to the DOM
     */
    renderAlert(alertData) {
        const container = this.getOrCreateContainer(alertData.position);
        const alertElement = this.createAlertElement(alertData);

        container.appendChild(alertElement);
        alertData.element = alertElement;

        // Apply animation
        this.applyAnimation(alertElement, alertData.animation, 'enter');

        // Auto-dismiss if enabled
        if (alertData.autoDismiss) {
            this.autoDismissAlert(alertData.id, alertData.dismissDelay);
        }

        // Limit number of alerts
        this.limitAlerts(container);

        // Dispatch event
        this.dispatchEvent('laravel-alert-created', { alert: alertData });
    }

    /**
     * Create alert element
     */
    createAlertElement(alertData) {
        const element = document.createElement('div');
        element.id = alertData.id;
        element.className = this.getAlertClasses(alertData);
        element.setAttribute('data-alert-id', alertData.id);
        element.setAttribute('data-alert-type', alertData.type);
        element.setAttribute('data-animation', alertData.animation);
        element.setAttribute('data-position', alertData.position);
        element.setAttribute('data-theme', alertData.theme);

        if (alertData.autoDismiss) {
            element.setAttribute('data-auto-dismiss', 'true');
            element.setAttribute('data-dismiss-delay', alertData.dismissDelay);
        }

        // Add data attributes
        Object.entries(alertData.dataAttributes).forEach(([key, value]) => {
            element.setAttribute(`data-${key}`, value);
        });

        // Set style
        if (alertData.style) {
            element.style.cssText = alertData.style;
        }

        // Create content
        element.innerHTML = this.generateAlertHTML(alertData);

        return element;
    }

    /**
     * Generate alert HTML based on theme
     */
    generateAlertHTML(alertData) {
        const theme = alertData.theme || this.config.defaultTheme;

        switch (theme) {
            case 'tailwind':
                return this.generateTailwindHTML(alertData);
            case 'bulma':
                return this.generateBulmaHTML(alertData);
            default:
                return this.generateBootstrapHTML(alertData);
        }
    }

    /**
     * Generate Bootstrap HTML
     */
    generateBootstrapHTML(alertData) {
        let html = '<div class="alert-header">';

        if (alertData.icon) {
            html += `<i class="${alertData.icon} me-2"></i>`;
        }

        if (alertData.title) {
            html += `<strong>${alertData.title}</strong>`;
        }

        if (alertData.dismissible) {
            html += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        }

        html += '</div>';
        html += '<div class="alert-body">';

        if (alertData.htmlContent) {
            html += alertData.htmlContent;
        } else {
            html += alertData.message;
        }

        html += '</div>';

        return html;
    }

    /**
     * Generate Tailwind HTML
     */
    generateTailwindHTML(alertData) {
        let html = '<div class="flex items-center justify-between p-4 border-b border-gray-200">';
        html += '<div class="flex items-center">';

        if (alertData.icon) {
            html += `<i class="${alertData.icon} mr-2"></i>`;
        }

        if (alertData.title) {
            html += `<strong class="font-semibold">${alertData.title}</strong>`;
        }

        html += '</div>';

        if (alertData.dismissible) {
            html += '<button type="button" class="ml-4 text-gray-400 hover:text-gray-600 focus:outline-none">';
            html += '<i class="fas fa-times"></i></button>';
        }

        html += '</div>';
        html += '<div class="p-4">';

        if (alertData.htmlContent) {
            html += alertData.htmlContent;
        } else {
            html += `<p class="text-gray-700">${alertData.message}</p>`;
        }

        html += '</div>';

        return html;
    }

    /**
     * Generate Bulma HTML
     */
    generateBulmaHTML(alertData) {
        let html = '<div class="notification-header">';
        html += '<div class="level"><div class="level-left"><div class="level-item">';

        if (alertData.icon) {
            html += `<span class="icon"><i class="${alertData.icon}"></i></span>`;
        }

        if (alertData.title) {
            html += `<strong>${alertData.title}</strong>`;
        }

        html += '</div></div>';

        if (alertData.dismissible) {
            html += '<div class="level-right"><div class="level-item">';
            html += '<button class="delete"></button></div></div>';
        }

        html += '</div></div>';
        html += '<div class="notification-content">';

        if (alertData.htmlContent) {
            html += alertData.htmlContent;
        } else {
            html += `<p>${alertData.message}</p>`;
        }

        html += '</div>';

        return html;
    }

    /**
     * Get alert classes
     */
    getAlertClasses(alertData) {
        const theme = alertData.theme || this.config.defaultTheme;
        const baseClasses = alertData.class || '';

        switch (theme) {
            case 'tailwind':
                return `toast ${baseClasses}`;
            case 'bulma':
                return `notification ${baseClasses}`;
            default:
                return `alert alert-${alertData.type === 'error' ? 'danger' : alertData.type} ${baseClasses}`;
        }
    }

    /**
     * Get or create container for position
     */
    getOrCreateContainer(position) {
        if (this.containers.has(position)) {
            return this.containers.get(position);
        }

        const container = document.createElement('div');
        container.className = 'laravel-alert-container';
        container.setAttribute('data-position', position);
        container.style.cssText = this.getPositionStyles(position);

        document.body.appendChild(container);
        this.containers.set(position, container);

        return container;
    }

    /**
     * Get position styles
     */
    getPositionStyles(position) {
        const styles = {
            'top-right': 'position: fixed; top: 20px; right: 20px; z-index: 9999;',
            'top-left': 'position: fixed; top: 20px; left: 20px; z-index: 9999;',
            'bottom-right': 'position: fixed; bottom: 20px; right: 20px; z-index: 9999;',
            'bottom-left': 'position: fixed; bottom: 20px; left: 20px; z-index: 9999;',
            'top-center': 'position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;',
            'bottom-center': 'position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;'
        };

        return styles[position] || styles['top-right'];
    }

    /**
     * Setup alert events
     */
    setupAlertEvents(alertId) {
        const alertData = this.alerts.get(alertId);
        if (!alertData || !alertData.element) return;

        const element = alertData.element;

        // Dismiss button
        const dismissButton = element.querySelector('.btn-close, .delete, [data-dismiss]');
        if (dismissButton) {
            dismissButton.addEventListener('click', () => {
                this.dismissAlert(alertId);
            });
        }

        // Click to dismiss
        if (alertData.dismissible && !alertData.autoDismiss) {
            element.addEventListener('click', (event) => {
                if (event.target === element) {
                    this.dismissAlert(alertId);
                }
            });
        }
    }

    /**
     * Auto-dismiss alert
     */
    autoDismissAlert(alertId, delay) {
        setTimeout(() => {
            this.dismissAlert(alertId);
        }, delay);
    }

    /**
     * Dismiss an alert
     */
    dismissAlert(alertId) {
        const alertData = this.alerts.get(alertId);
        if (!alertData || !alertData.element) return;

        const element = alertData.element;
        const animation = alertData.animation || this.config.defaultAnimation;

        // Apply exit animation
        this.applyAnimation(element, animation, 'exit', () => {
            element.remove();
            this.alerts.delete(alertId);
            this.dispatchEvent('laravel-alert-dismissed', { alertId });
        });
    }

    /**
     * Clear all alerts
     */
    clearAlerts() {
        this.alerts.forEach((alertData, alertId) => {
            this.dismissAlert(alertId);
        });
    }

    /**
     * Apply animation to element
     */
    applyAnimation(element, animation, type, callback) {
        if (!element || !animation) {
            if (callback) callback();
            return;
        }

        const animationClass = `alert-${animation}-${type}`;
        element.classList.add(animationClass);

        // Remove animation class after animation completes
        const duration = this.getAnimationDuration(animation);
        setTimeout(() => {
            element.classList.remove(animationClass);
            if (callback) callback();
        }, duration);
    }

    /**
     * Get animation duration
     */
    getAnimationDuration(animation) {
        const durations = {
            'fade': 300,
            'slide': 300,
            'scale': 300,
            'bounce': 600,
            'zoom': 300,
            'flip': 600,
            'rotate': 600
        };

        return durations[animation] || 300;
    }

    /**
     * Limit number of alerts
     */
    limitAlerts(container) {
        const alerts = container.querySelectorAll('[data-alert-id]');
        if (alerts.length > this.config.maxAlerts) {
            const excess = alerts.length - this.config.maxAlerts;
            for (let i = 0; i < excess; i++) {
                const alertId = alerts[i].dataset.alertId;
                this.dismissAlert(alertId);
            }
        }
    }

    /**
     * Setup AJAX handlers
     */
    setupAjaxHandlers() {
        // Intercept fetch requests
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            const response = await originalFetch(...args);
            this.handleAjaxResponse(response);
            return response;
        };

        // Intercept XMLHttpRequest
        const originalXHR = window.XMLHttpRequest;
        window.XMLHttpRequest = function () {
            const xhr = new originalXHR();
            const originalOnReadyStateChange = xhr.onreadystatechange;

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status >= 200 && xhr.status < 300) {
                    LaravelAlert.handleAjaxResponse(xhr);
                }
                if (originalOnReadyStateChange) {
                    originalOnReadyStateChange.apply(this, arguments);
                }
            };

            return xhr;
        };
    }

    /**
     * Handle AJAX response
     */
    static handleAjaxResponse(response) {
        try {
            const data = response.json ? response.json() : JSON.parse(response.responseText);
            if (data.alerts) {
                data.alerts.forEach(alert => {
                    LaravelAlert.createAlert(alert);
                });
            }
        } catch (error) {
            console.warn('Failed to parse AJAX response for alerts:', error);
        }
    }

    /**
     * Setup real-time updates
     */
    setupRealTimeUpdates() {
        // WebSocket support (if available)
        if (window.WebSocket) {
            this.setupWebSocket();
        }

        // Polling for updates
        this.setupPolling();
    }

    /**
     * Setup WebSocket connection
     */
    setupWebSocket() {
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsUrl = `${protocol}//${window.location.host}/laravel-alert/ws`;

        try {
            const ws = new WebSocket(wsUrl);

            ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                if (data.type === 'alert') {
                    this.createAlert(data.alert);
                }
            };

            ws.onerror = (error) => {
                console.warn('WebSocket connection failed:', error);
            };
        } catch (error) {
            console.warn('WebSocket not available:', error);
        }
    }

    /**
     * Setup polling for updates
     */
    setupPolling() {
        setInterval(() => {
            this.checkForUpdates();
        }, 5000); // Check every 5 seconds
    }

    /**
     * Check for updates
     */
    async checkForUpdates() {
        try {
            const response = await fetch(`${this.config.apiUrl}/alerts`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.config.csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.alerts && data.alerts.length > 0) {
                    data.alerts.forEach(alert => {
                        this.createAlert(alert);
                    });
                }
            }
        } catch (error) {
            console.warn('Failed to check for updates:', error);
        }
    }

    /**
     * Handle form submission
     */
    handleFormSubmission(event) {
        const form = event.target;
        if (form.hasAttribute('data-alert-on-submit')) {
            const alertType = form.getAttribute('data-alert-type') || 'success';
            const alertMessage = form.getAttribute('data-alert-message') || 'Form submitted successfully!';

            this.createAlert({
                type: alertType,
                message: alertMessage
            });
        }
    }

    /**
     * Dispatch custom event
     */
    dispatchEvent(eventName, detail) {
        const event = new CustomEvent(eventName, { detail });
        document.dispatchEvent(event);
    }

    /**
     * Generate unique ID
     */
    generateId() {
        return 'alert-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Public API methods
     */
    success(message, options = {}) {
        return this.createAlert({ type: 'success', message, ...options });
    }

    error(message, options = {}) {
        return this.createAlert({ type: 'error', message, ...options });
    }

    warning(message, options = {}) {
        return this.createAlert({ type: 'warning', message, ...options });
    }

    info(message, options = {}) {
        return this.createAlert({ type: 'info', message, ...options });
    }

    getAlerts() {
        return Array.from(this.alerts.values());
    }

    getAlert(id) {
        return this.alerts.get(id);
    }

    hasAlerts() {
        return this.alerts.size > 0;
    }

    getAlertsCount() {
        return this.alerts.size;
    }
}

// Initialize the alert system
const LaravelAlert = new LaravelAlert();

// Export for global use
window.LaravelAlert = LaravelAlert;

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LaravelAlert;
}
