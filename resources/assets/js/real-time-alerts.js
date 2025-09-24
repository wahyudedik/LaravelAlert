/**
 * Laravel Alert Real-time Updates
 * WebSocket and polling support for real-time alert updates
 */

class LaravelAlertRealTime {
    constructor() {
        this.config = {
            wsUrl: this.getWebSocketUrl(),
            apiUrl: '/laravel-alert',
            pollingInterval: 5000,
            reconnectInterval: 3000,
            maxReconnectAttempts: 5
        };

        this.ws = null;
        this.pollingTimer = null;
        this.reconnectAttempts = 0;
        this.isConnected = false;
        this.subscriptions = new Set();
        this.connectionId = null;

        this.init();
    }

    /**
     * Initialize real-time updates
     */
    init() {
        this.setupWebSocket();
        this.setupPolling();
        this.setupEventListeners();
    }

    /**
     * Get WebSocket URL
     */
    getWebSocketUrl() {
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const host = window.location.host;
        return `${protocol}//${host}/laravel-alert/ws`;
    }

    /**
     * Setup WebSocket connection
     */
    setupWebSocket() {
        if (!window.WebSocket) {
            console.warn('WebSocket not supported, falling back to polling');
            return;
        }

        try {
            this.ws = new WebSocket(this.config.wsUrl);

            this.ws.onopen = () => {
                console.log('WebSocket connected');
                this.isConnected = true;
                this.reconnectAttempts = 0;
                this.dispatchEvent('laravel-alert-ws-connected');
            };

            this.ws.onmessage = (event) => {
                this.handleWebSocketMessage(event);
            };

            this.ws.onclose = (event) => {
                console.log('WebSocket disconnected:', event.code, event.reason);
                this.isConnected = false;
                this.dispatchEvent('laravel-alert-ws-disconnected');
                this.attemptReconnect();
            };

            this.ws.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.dispatchEvent('laravel-alert-ws-error', { error });
            };
        } catch (error) {
            console.error('Failed to create WebSocket connection:', error);
        }
    }

    /**
     * Setup polling as fallback
     */
    setupPolling() {
        this.pollingTimer = setInterval(() => {
            if (!this.isConnected) {
                this.checkForUpdates();
            }
        }, this.config.pollingInterval);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Listen for page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') {
                this.checkForUpdates();
            }
        });

        // Listen for online/offline events
        window.addEventListener('online', () => {
            this.checkForUpdates();
        });

        window.addEventListener('offline', () => {
            this.dispatchEvent('laravel-alert-offline');
        });
    }

    /**
     * Handle WebSocket message
     */
    handleWebSocketMessage(event) {
        try {
            const data = JSON.parse(event.data);

            switch (data.type) {
                case 'alert':
                    this.handleAlertMessage(data);
                    break;
                case 'dismiss':
                    this.handleDismissMessage(data);
                    break;
                case 'clear':
                    this.handleClearMessage(data);
                    break;
                case 'ping':
                    this.handlePingMessage(data);
                    break;
                default:
                    console.warn('Unknown WebSocket message type:', data.type);
            }
        } catch (error) {
            console.error('Failed to parse WebSocket message:', error);
        }
    }

    /**
     * Handle alert message
     */
    handleAlertMessage(data) {
        const { alert } = data;

        // Check if we're subscribed to this channel
        if (data.channels && !this.isSubscribedToAny(data.channels)) {
            return;
        }

        // Create the alert using the main LaravelAlert instance
        if (window.LaravelAlert) {
            window.LaravelAlert.createAlert(alert);
        }

        this.dispatchEvent('laravel-alert-received', { alert });
    }

    /**
     * Handle dismiss message
     */
    handleDismissMessage(data) {
        const { alertId } = data;

        if (window.LaravelAlert) {
            window.LaravelAlert.dismissAlert(alertId);
        }

        this.dispatchEvent('laravel-alert-dismissed', { alertId });
    }

    /**
     * Handle clear message
     */
    handleClearMessage(data) {
        if (window.LaravelAlert) {
            window.LaravelAlert.clearAlerts();
        }

        this.dispatchEvent('laravel-alert-cleared');
    }

    /**
     * Handle ping message
     */
    handlePingMessage(data) {
        // Respond to ping with pong
        this.sendWebSocketMessage({
            type: 'pong',
            timestamp: Date.now()
        });
    }

    /**
     * Check for updates via AJAX
     */
    async checkForUpdates() {
        try {
            const response = await fetch(`${this.config.apiUrl}/alerts`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.alerts && data.alerts.length > 0) {
                    data.alerts.forEach(alert => {
                        if (window.LaravelAlert) {
                            window.LaravelAlert.createAlert(alert);
                        }
                    });
                }
            }
        } catch (error) {
            console.warn('Failed to check for updates:', error);
        }
    }

    /**
     * Send WebSocket message
     */
    sendWebSocketMessage(data) {
        if (this.ws && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(data));
        }
    }

    /**
     * Subscribe to channels
     */
    subscribe(channels) {
        if (!Array.isArray(channels)) {
            channels = [channels];
        }

        channels.forEach(channel => {
            this.subscriptions.add(channel);
        });

        // Send subscription to server
        this.sendWebSocketMessage({
            type: 'subscribe',
            channels: channels
        });

        // Also subscribe via AJAX
        this.subscribeViaAjax(channels);
    }

    /**
     * Unsubscribe from channels
     */
    unsubscribe(channels) {
        if (!Array.isArray(channels)) {
            channels = [channels];
        }

        channels.forEach(channel => {
            this.subscriptions.delete(channel);
        });

        // Send unsubscription to server
        this.sendWebSocketMessage({
            type: 'unsubscribe',
            channels: channels
        });

        // Also unsubscribe via AJAX
        this.unsubscribeViaAjax(channels);
    }

    /**
     * Subscribe via AJAX
     */
    async subscribeViaAjax(channels) {
        try {
            await fetch(`${this.config.apiUrl}/ws/subscribe`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    channels: channels,
                    connection_id: this.connectionId
                })
            });
        } catch (error) {
            console.warn('Failed to subscribe via AJAX:', error);
        }
    }

    /**
     * Unsubscribe via AJAX
     */
    async unsubscribeViaAjax(channels) {
        try {
            await fetch(`${this.config.apiUrl}/ws/unsubscribe`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    channels: channels,
                    connection_id: this.connectionId
                })
            });
        } catch (error) {
            console.warn('Failed to unsubscribe via AJAX:', error);
        }
    }

    /**
     * Check if subscribed to any of the channels
     */
    isSubscribedToAny(channels) {
        return channels.some(channel => this.subscriptions.has(channel));
    }

    /**
     * Attempt to reconnect WebSocket
     */
    attemptReconnect() {
        if (this.reconnectAttempts >= this.config.maxReconnectAttempts) {
            console.warn('Max reconnection attempts reached');
            return;
        }

        this.reconnectAttempts++;
        console.log(`Attempting to reconnect (${this.reconnectAttempts}/${this.config.maxReconnectAttempts})`);

        setTimeout(() => {
            this.setupWebSocket();
        }, this.config.reconnectInterval);
    }

    /**
     * Get connection status
     */
    getConnectionStatus() {
        return {
            isConnected: this.isConnected,
            reconnectAttempts: this.reconnectAttempts,
            subscriptions: Array.from(this.subscriptions),
            connectionId: this.connectionId
        };
    }

    /**
     * Get CSRF token
     */
    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    /**
     * Dispatch custom event
     */
    dispatchEvent(eventName, detail = {}) {
        const event = new CustomEvent(eventName, { detail });
        document.dispatchEvent(event);
    }

    /**
     * Cleanup
     */
    destroy() {
        if (this.ws) {
            this.ws.close();
        }

        if (this.pollingTimer) {
            clearInterval(this.pollingTimer);
        }

        this.subscriptions.clear();
    }
}

// Initialize real-time updates
const LaravelAlertRealTime = new LaravelAlertRealTime();

// Export for global use
window.LaravelAlertRealTime = LaravelAlertRealTime;

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LaravelAlertRealTime;
}
