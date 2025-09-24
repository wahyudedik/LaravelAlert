/**
 * Laravel Alert Animation Manager
 * Provides JavaScript hooks and custom animations for alerts
 */

class LaravelAlertAnimations {
    constructor() {
        this.animations = new Map();
        this.hooks = new Map();
        this.init();
    }

    /**
     * Initialize the animation system
     */
    init() {
        this.registerDefaultAnimations();
        this.registerDefaultHooks();
        this.setupEventListeners();
    }

    /**
     * Register default animations
     */
    registerDefaultAnimations() {
        // Fade animations
        this.registerAnimation('fade', {
            enter: (element, callback) => {
                element.style.opacity = '0';
                element.style.transition = 'opacity 0.3s ease';
                requestAnimationFrame(() => {
                    element.style.opacity = '1';
                    if (callback) callback();
                });
            },
            exit: (element, callback) => {
                element.style.transition = 'opacity 0.3s ease';
                element.style.opacity = '0';
                setTimeout(() => {
                    if (callback) callback();
                }, 300);
            }
        });

        // Slide animations
        this.registerAnimation('slide', {
            enter: (element, callback) => {
                element.style.transform = 'translateX(100%)';
                element.style.transition = 'transform 0.3s ease';
                requestAnimationFrame(() => {
                    element.style.transform = 'translateX(0)';
                    if (callback) callback();
                });
            },
            exit: (element, callback) => {
                element.style.transition = 'transform 0.3s ease';
                element.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (callback) callback();
                }, 300);
            }
        });

        // Scale animations
        this.registerAnimation('scale', {
            enter: (element, callback) => {
                element.style.transform = 'scale(0)';
                element.style.transition = 'transform 0.3s ease';
                requestAnimationFrame(() => {
                    element.style.transform = 'scale(1)';
                    if (callback) callback();
                });
            },
            exit: (element, callback) => {
                element.style.transition = 'transform 0.3s ease';
                element.style.transform = 'scale(0)';
                setTimeout(() => {
                    if (callback) callback();
                }, 300);
            }
        });

        // Bounce animations
        this.registerAnimation('bounce', {
            enter: (element, callback) => {
                element.style.transform = 'scale(0.3)';
                element.style.transition = 'transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                requestAnimationFrame(() => {
                    element.style.transform = 'scale(1)';
                    if (callback) callback();
                });
            },
            exit: (element, callback) => {
                element.style.transition = 'transform 0.6s ease';
                element.style.transform = 'scale(0.3)';
                setTimeout(() => {
                    if (callback) callback();
                }, 600);
            }
        });

        // Zoom animations
        this.registerAnimation('zoom', {
            enter: (element, callback) => {
                element.style.transform = 'scale(0)';
                element.style.transition = 'transform 0.3s ease';
                requestAnimationFrame(() => {
                    element.style.transform = 'scale(1)';
                    if (callback) callback();
                });
            },
            exit: (element, callback) => {
                element.style.transition = 'transform 0.3s ease';
                element.style.transform = 'scale(0)';
                setTimeout(() => {
                    if (callback) callback();
                }, 300);
            }
        });

        // Flip animations
        this.registerAnimation('flip', {
            enter: (element, callback) => {
                element.style.transform = 'perspective(400px) rotateX(90deg)';
                element.style.transition = 'transform 0.6s ease';
                requestAnimationFrame(() => {
                    element.style.transform = 'perspective(400px) rotateX(0deg)';
                    if (callback) callback();
                });
            },
            exit: (element, callback) => {
                element.style.transition = 'transform 0.6s ease';
                element.style.transform = 'perspective(400px) rotateX(90deg)';
                setTimeout(() => {
                    if (callback) callback();
                }, 600);
            }
        });

        // Rotate animations
        this.registerAnimation('rotate', {
            enter: (element, callback) => {
                element.style.transform = 'rotate(-200deg)';
                element.style.transition = 'transform 0.6s ease';
                requestAnimationFrame(() => {
                    element.style.transform = 'rotate(0deg)';
                    if (callback) callback();
                });
            },
            exit: (element, callback) => {
                element.style.transition = 'transform 0.6s ease';
                element.style.transform = 'rotate(200deg)';
                setTimeout(() => {
                    if (callback) callback();
                }, 600);
            }
        });
    }

    /**
     * Register default hooks
     */
    registerDefaultHooks() {
        // Before enter hook
        this.registerHook('beforeEnter', (element, animation) => {
            element.classList.add('alert-animation-hook');
        });

        // After enter hook
        this.registerHook('afterEnter', (element, animation) => {
            element.classList.remove('alert-animation-hook');
        });

        // Before exit hook
        this.registerHook('beforeExit', (element, animation) => {
            element.classList.add('alert-animation-hook');
        });

        // After exit hook
        this.registerHook('afterExit', (element, animation) => {
            element.classList.remove('alert-animation-hook');
        });
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Listen for custom animation events
        document.addEventListener('laravel-alert-animate', (event) => {
            const { element, animation, type, callback } = event.detail;
            this.animate(element, animation, type, callback);
        });

        // Listen for alert creation
        document.addEventListener('laravel-alert-created', (event) => {
            const { element } = event.detail;
            this.handleAlertCreated(element);
        });

        // Listen for alert removal
        document.addEventListener('laravel-alert-removed', (event) => {
            const { element } = event.detail;
            this.handleAlertRemoved(element);
        });
    }

    /**
     * Register a custom animation
     */
    registerAnimation(name, animation) {
        this.animations.set(name, animation);
    }

    /**
     * Register a custom hook
     */
    registerHook(name, hook) {
        this.hooks.set(name, hook);
    }

    /**
     * Animate an element
     */
    animate(element, animationName, type, callback) {
        const animation = this.animations.get(animationName);
        if (!animation) {
            console.warn(`Animation '${animationName}' not found`);
            if (callback) callback();
            return;
        }

        // Execute hooks
        this.executeHooks('before' + type.charAt(0).toUpperCase() + type.slice(1), element, animationName);

        // Execute animation
        if (type === 'enter') {
            animation.enter(element, () => {
                this.executeHooks('afterEnter', element, animationName);
                if (callback) callback();
            });
        } else if (type === 'exit') {
            animation.exit(element, () => {
                this.executeHooks('afterExit', element, animationName);
                if (callback) callback();
            });
        }
    }

    /**
     * Execute hooks
     */
    executeHooks(hookName, element, animation) {
        const hook = this.hooks.get(hookName);
        if (hook) {
            hook(element, animation);
        }
    }

    /**
     * Handle alert creation
     */
    handleAlertCreated(element) {
        const animation = element.dataset.animation || 'fade';
        this.animate(element, animation, 'enter');
    }

    /**
     * Handle alert removal
     */
    handleAlertRemoved(element) {
        const animation = element.dataset.animation || 'fade';
        this.animate(element, animation, 'exit', () => {
            if (element.parentNode) {
                element.remove();
            }
        });
    }

    /**
     * Create a custom animation
     */
    createCustomAnimation(name, enterFunction, exitFunction) {
        this.registerAnimation(name, {
            enter: enterFunction,
            exit: exitFunction
        });
    }

    /**
     * Get available animations
     */
    getAvailableAnimations() {
        return Array.from(this.animations.keys());
    }

    /**
     * Get available hooks
     */
    getAvailableHooks() {
        return Array.from(this.hooks.keys());
    }
}

// Initialize the animation system
const LaravelAlertAnimations = new LaravelAlertAnimations();

// Export for global use
window.LaravelAlertAnimations = LaravelAlertAnimations;

// Custom animation examples
LaravelAlertAnimations.createCustomAnimation('elastic',
    (element, callback) => {
        element.style.transform = 'scale(0)';
        element.style.transition = 'transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        requestAnimationFrame(() => {
            element.style.transform = 'scale(1)';
            if (callback) callback();
        });
    },
    (element, callback) => {
        element.style.transition = 'transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        element.style.transform = 'scale(0)';
        setTimeout(() => {
            if (callback) callback();
        }, 800);
    }
);

LaravelAlertAnimations.createCustomAnimation('swing',
    (element, callback) => {
        element.style.transform = 'rotate(-15deg)';
        element.style.transition = 'transform 0.6s ease';
        requestAnimationFrame(() => {
            element.style.transform = 'rotate(0deg)';
            if (callback) callback();
        });
    },
    (element, callback) => {
        element.style.transition = 'transform 0.6s ease';
        element.style.transform = 'rotate(15deg)';
        setTimeout(() => {
            if (callback) callback();
        }, 600);
    }
);

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LaravelAlertAnimations;
}
