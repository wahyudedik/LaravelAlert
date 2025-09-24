<?php

namespace Wahyudedik\LaravelAlert\Managers;

class AnimationManager
{
    protected array $animations;
    protected array $hooks;
    protected array $config;

    public function __construct()
    {
        $this->config = config('laravel-alert', []);
        $this->animations = [];
        $this->hooks = [];
        $this->registerDefaultAnimations();
        $this->registerDefaultHooks();
    }

    /**
     * Register default animations
     */
    protected function registerDefaultAnimations(): void
    {
        $this->animations = [
            'fade' => [
                'enter' => 'fadeIn',
                'exit' => 'fadeOut',
                'duration' => 300,
                'easing' => 'ease-in-out'
            ],
            'slide' => [
                'enter' => 'slideInRight',
                'exit' => 'slideOutRight',
                'duration' => 300,
                'easing' => 'ease-out'
            ],
            'slide-left' => [
                'enter' => 'slideInLeft',
                'exit' => 'slideOutLeft',
                'duration' => 300,
                'easing' => 'ease-out'
            ],
            'slide-up' => [
                'enter' => 'slideInUp',
                'exit' => 'slideOutUp',
                'duration' => 300,
                'easing' => 'ease-out'
            ],
            'slide-down' => [
                'enter' => 'slideInDown',
                'exit' => 'slideOutDown',
                'duration' => 300,
                'easing' => 'ease-out'
            ],
            'scale' => [
                'enter' => 'scaleIn',
                'exit' => 'scaleOut',
                'duration' => 300,
                'easing' => 'ease-out'
            ],
            'bounce' => [
                'enter' => 'bounceIn',
                'exit' => 'bounceOut',
                'duration' => 600,
                'easing' => 'cubic-bezier(0.68, -0.55, 0.265, 1.55)'
            ],
            'zoom' => [
                'enter' => 'zoomIn',
                'exit' => 'zoomOut',
                'duration' => 300,
                'easing' => 'ease-out'
            ],
            'flip' => [
                'enter' => 'flipInX',
                'exit' => 'flipOutX',
                'duration' => 600,
                'easing' => 'ease-out'
            ],
            'flip-y' => [
                'enter' => 'flipInY',
                'exit' => 'flipOutY',
                'duration' => 600,
                'easing' => 'ease-out'
            ],
            'rotate' => [
                'enter' => 'rotateIn',
                'exit' => 'rotateOut',
                'duration' => 600,
                'easing' => 'ease-out'
            ],
            'pulse' => [
                'enter' => 'pulse',
                'exit' => 'fadeOut',
                'duration' => 2000,
                'easing' => 'ease-in-out'
            ],
            'shake' => [
                'enter' => 'shake',
                'exit' => 'fadeOut',
                'duration' => 500,
                'easing' => 'ease-in-out'
            ],
            'wobble' => [
                'enter' => 'wobble',
                'exit' => 'fadeOut',
                'duration' => 1000,
                'easing' => 'ease-in-out'
            ]
        ];
    }

    /**
     * Register default hooks
     */
    protected function registerDefaultHooks(): void
    {
        $this->hooks = [
            'beforeEnter' => function ($element, $animation) {
                return "element.classList.add('alert-animation-hook');";
            },
            'afterEnter' => function ($element, $animation) {
                return "element.classList.remove('alert-animation-hook');";
            },
            'beforeExit' => function ($element, $animation) {
                return "element.classList.add('alert-animation-hook');";
            },
            'afterExit' => function ($element, $animation) {
                return "element.classList.remove('alert-animation-hook');";
            }
        ];
    }

    /**
     * Get animation configuration
     */
    public function getAnimation(string $name): ?array
    {
        return $this->animations[$name] ?? null;
    }

    /**
     * Get all animations
     */
    public function getAnimations(): array
    {
        return $this->animations;
    }

    /**
     * Register a custom animation
     */
    public function registerAnimation(string $name, array $animation): self
    {
        $this->animations[$name] = $animation;
        return $this;
    }

    /**
     * Register a custom hook
     */
    public function registerHook(string $name, callable $hook): self
    {
        $this->hooks[$name] = $hook;
        return $this;
    }

    /**
     * Get hook
     */
    public function getHook(string $name): ?callable
    {
        return $this->hooks[$name] ?? null;
    }

    /**
     * Get all hooks
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    /**
     * Generate CSS for animations
     */
    public function generateCSS(): string
    {
        $css = '';

        foreach ($this->animations as $name => $animation) {
            $css .= $this->generateAnimationCSS($name, $animation);
        }

        return $css;
    }

    /**
     * Generate JavaScript for animations
     */
    public function generateJavaScript(): string
    {
        $js = 'class LaravelAlertAnimations {';
        $js .= 'constructor() { this.animations = new Map(); this.hooks = new Map(); this.init(); }';
        $js .= 'init() { this.registerDefaultAnimations(); this.registerDefaultHooks(); }';

        // Register animations
        foreach ($this->animations as $name => $animation) {
            $js .= $this->generateAnimationJS($name, $animation);
        }

        // Register hooks
        foreach ($this->hooks as $name => $hook) {
            $js .= $this->generateHookJS($name, $hook);
        }

        $js .= '}';
        $js .= 'window.LaravelAlertAnimations = new LaravelAlertAnimations();';

        return $js;
    }

    /**
     * Generate CSS for a specific animation
     */
    protected function generateAnimationCSS(string $name, array $animation): string
    {
        $css = '';

        if (isset($animation['enter'])) {
            $css .= ".alert-{$name}-enter { animation: {$animation['enter']} {$animation['duration']}ms {$animation['easing']}; }";
        }

        if (isset($animation['exit'])) {
            $css .= ".alert-{$name}-exit { animation: {$animation['exit']} {$animation['duration']}ms {$animation['easing']}; }";
        }

        return $css;
    }

    /**
     * Generate JavaScript for a specific animation
     */
    protected function generateAnimationJS(string $name, array $animation): string
    {
        $js = '';

        if (isset($animation['enter'])) {
            $js .= "this.registerAnimation('{$name}', {";
            $js .= "enter: (element, callback) => {";
            $js .= "element.style.animation = '{$animation['enter']} {$animation['duration']}ms {$animation['easing']}';";
            $js .= "if (callback) callback();";
            $js .= "},";
        }

        if (isset($animation['exit'])) {
            $js .= "exit: (element, callback) => {";
            $js .= "element.style.animation = '{$animation['exit']} {$animation['duration']}ms {$animation['easing']}';";
            $js .= "if (callback) callback();";
            $js .= "}";
            $js .= "});";
        }

        return $js;
    }

    /**
     * Generate JavaScript for a specific hook
     */
    protected function generateHookJS(string $name, callable $hook): string
    {
        $js = "this.registerHook('{$name}', (element, animation) => {";
        $js .= call_user_func($hook, 'element', 'animation');
        $js .= "});";

        return $js;
    }

    /**
     * Get animation classes for an element
     */
    public function getAnimationClasses(string $animation, string $type = 'enter'): string
    {
        $animationConfig = $this->getAnimation($animation);
        if (!$animationConfig) {
            return '';
        }

        $class = "alert-{$animation}-{$type}";

        if (isset($animationConfig['duration'])) {
            $class .= " alert-duration-{$animationConfig['duration']}";
        }

        if (isset($animationConfig['easing'])) {
            $class .= " alert-easing-{$animationConfig['easing']}";
        }

        return $class;
    }

    /**
     * Get animation data attributes
     */
    public function getAnimationDataAttributes(string $animation): array
    {
        $animationConfig = $this->getAnimation($animation);
        if (!$animationConfig) {
            return [];
        }

        $attributes = [];

        if (isset($animationConfig['duration'])) {
            $attributes['data-animation-duration'] = $animationConfig['duration'];
        }

        if (isset($animationConfig['easing'])) {
            $attributes['data-animation-easing'] = $animationConfig['easing'];
        }

        if (isset($animationConfig['enter'])) {
            $attributes['data-animation-enter'] = $animationConfig['enter'];
        }

        if (isset($animationConfig['exit'])) {
            $attributes['data-animation-exit'] = $animationConfig['exit'];
        }

        return $attributes;
    }

    /**
     * Check if animation exists
     */
    public function hasAnimation(string $name): bool
    {
        return isset($this->animations[$name]);
    }

    /**
     * Check if hook exists
     */
    public function hasHook(string $name): bool
    {
        return isset($this->hooks[$name]);
    }

    /**
     * Get available animation names
     */
    public function getAvailableAnimations(): array
    {
        return array_keys($this->animations);
    }

    /**
     * Get available hook names
     */
    public function getAvailableHooks(): array
    {
        return array_keys($this->hooks);
    }

    /**
     * Create a custom animation
     */
    public function createCustomAnimation(
        string $name,
        string $enterAnimation,
        string $exitAnimation,
        int $duration = 300,
        string $easing = 'ease-out'
    ): self {
        return $this->registerAnimation($name, [
            'enter' => $enterAnimation,
            'exit' => $exitAnimation,
            'duration' => $duration,
            'easing' => $easing
        ]);
    }

    /**
     * Create a custom hook
     */
    public function createCustomHook(string $name, callable $hook): self
    {
        return $this->registerHook($name, $hook);
    }
}
