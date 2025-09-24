<?php

namespace Wahyudedik\LaravelAlert\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Wahyudedik\LaravelAlert\Http\Responses\AlertApiResponse;

class ApiAuthentication
{
    protected array $config;
    protected array $rateLimitConfig;

    public function __construct()
    {
        $this->config = config('laravel-alert.api', []);
        $this->rateLimitConfig = config('laravel-alert.rate_limiting', []);
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $guard = null): JsonResponse
    {
        // Check if API is enabled
        if (!$this->config['enabled'] ?? true) {
            return AlertApiResponse::error('API is disabled', 503);
        }

        // Check rate limiting
        if ($this->isRateLimited($request)) {
            return $this->handleRateLimit($request);
        }

        // Check authentication
        if (!$this->isAuthenticated($request, $guard)) {
            return $this->handleUnauthenticated($request);
        }

        // Check permissions
        if (!$this->hasPermission($request)) {
            return $this->handleUnauthorized($request);
        }

        // Add user context to request
        $this->addUserContext($request);

        return $next($request);
    }

    /**
     * Check if request is rate limited.
     */
    protected function isRateLimited(Request $request): bool
    {
        if (!$this->rateLimitConfig['enabled'] ?? false) {
            return false;
        }

        $key = $this->getRateLimitKey($request);
        $maxAttempts = $this->rateLimitConfig['max_attempts'] ?? 60;
        $decayMinutes = $this->rateLimitConfig['decay_minutes'] ?? 1;

        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    /**
     * Handle rate limit exceeded.
     */
    protected function handleRateLimit(Request $request): JsonResponse
    {
        $key = $this->getRateLimitKey($request);
        $retryAfter = RateLimiter::availableIn($key);

        return AlertApiResponse::error(
            'Too many requests. Please try again later.',
            429,
            [
                'retry_after' => $retryAfter,
                'limit' => $this->rateLimitConfig['max_attempts'] ?? 60
            ]
        );
    }

    /**
     * Check if user is authenticated.
     */
    protected function isAuthenticated(Request $request, string $guard = null): bool
    {
        $authMethod = $this->config['auth_method'] ?? 'token';

        switch ($authMethod) {
            case 'token':
                return $this->checkTokenAuth($request);
            case 'api_key':
                return $this->checkApiKeyAuth($request);
            case 'oauth':
                return $this->checkOAuthAuth($request);
            case 'jwt':
                return $this->checkJwtAuth($request);
            case 'session':
                return Auth::guard($guard)->check();
            default:
                return true; // No authentication required
        }
    }

    /**
     * Check token authentication.
     */
    protected function checkTokenAuth(Request $request): bool
    {
        $token = $request->bearerToken() ?? $request->header('X-API-Token');

        if (!$token) {
            return false;
        }

        // Check against configured tokens
        $validTokens = $this->config['tokens'] ?? [];
        return in_array($token, $validTokens);
    }

    /**
     * Check API key authentication.
     */
    protected function checkApiKeyAuth(Request $request): bool
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return false;
        }

        // Check against configured API keys
        $validKeys = $this->config['api_keys'] ?? [];
        return in_array($apiKey, $validKeys);
    }

    /**
     * Check OAuth authentication.
     */
    protected function checkOAuthAuth(Request $request): bool
    {
        $token = $request->bearerToken();

        if (!$token) {
            return false;
        }

        // Validate OAuth token
        try {
            $user = Auth::guard('api')->user();
            return $user !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check JWT authentication.
     */
    protected function checkJwtAuth(Request $request): bool
    {
        $token = $request->bearerToken();

        if (!$token) {
            return false;
        }

        // Validate JWT token
        try {
            $user = Auth::guard('jwt')->user();
            return $user !== null;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if user has permission.
     */
    protected function hasPermission(Request $request): bool
    {
        $requiredPermission = $this->getRequiredPermission($request);

        if (!$requiredPermission) {
            return true;
        }

        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Check if user has required permission
        if (method_exists($user, 'hasPermission')) {
            return $user->hasPermission($requiredPermission);
        }

        // Check if user has required role
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($requiredPermission);
        }

        return true;
    }

    /**
     * Get required permission for request.
     */
    protected function getRequiredPermission(Request $request): ?string
    {
        $route = $request->route();
        $action = $route->getAction();

        return $action['permission'] ?? null;
    }

    /**
     * Handle unauthenticated request.
     */
    protected function handleUnauthenticated(Request $request): JsonResponse
    {
        return AlertApiResponse::unauthorized('Authentication required');
    }

    /**
     * Handle unauthorized request.
     */
    protected function handleUnauthorized(Request $request): JsonResponse
    {
        return AlertApiResponse::forbidden('Insufficient permissions');
    }

    /**
     * Add user context to request.
     */
    protected function addUserContext(Request $request): void
    {
        $user = Auth::user();

        if ($user) {
            $request->merge([
                'user_id' => $user->id,
                'user_context' => [
                    'id' => $user->id,
                    'name' => $user->name ?? $user->email,
                    'email' => $user->email,
                    'roles' => $user->roles ?? [],
                    'permissions' => $user->permissions ?? []
                ]
            ]);
        }
    }

    /**
     * Get rate limit key for request.
     */
    protected function getRateLimitKey(Request $request): string
    {
        $user = Auth::user();
        $ip = $request->ip();

        if ($user) {
            return 'api_rate_limit:' . $user->id;
        }

        return 'api_rate_limit:' . $ip;
    }

    /**
     * Check if request should be rate limited.
     */
    protected function shouldRateLimit(Request $request): bool
    {
        $rateLimitConfig = $this->rateLimitConfig;

        // Check if rate limiting is enabled
        if (!($rateLimitConfig['enabled'] ?? false)) {
            return false;
        }

        // Check if request is from whitelisted IP
        $whitelistedIps = $rateLimitConfig['whitelist'] ?? [];
        if (in_array($request->ip(), $whitelistedIps)) {
            return false;
        }

        // Check if user is exempt from rate limiting
        $user = Auth::user();
        if ($user && $user->isExemptFromRateLimit ?? false) {
            return false;
        }

        return true;
    }

    /**
     * Get rate limit configuration for request.
     */
    protected function getRateLimitConfig(Request $request): array
    {
        $user = Auth::user();

        if ($user && $user->rateLimitConfig) {
            return $user->rateLimitConfig;
        }

        return $this->rateLimitConfig;
    }

    /**
     * Log API request.
     */
    protected function logRequest(Request $request): void
    {
        if (!($this->config['logging'] ?? false)) {
            return;
        }

        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => Auth::id(),
            'timestamp' => now()->toISOString()
        ];

        \Log::info('API Request', $logData);
    }

    /**
     * Get authentication method from request.
     */
    protected function getAuthMethod(Request $request): string
    {
        if ($request->bearerToken()) {
            return 'bearer';
        }

        if ($request->header('X-API-Key')) {
            return 'api_key';
        }

        if ($request->header('X-API-Token')) {
            return 'token';
        }

        return 'none';
    }

    /**
     * Validate API key.
     */
    protected function validateApiKey(string $apiKey): bool
    {
        $validKeys = $this->config['api_keys'] ?? [];

        if (empty($validKeys)) {
            return false;
        }

        return in_array($apiKey, $validKeys);
    }

    /**
     * Validate API token.
     */
    protected function validateApiToken(string $token): bool
    {
        $validTokens = $this->config['tokens'] ?? [];

        if (empty($validTokens)) {
            return false;
        }

        return in_array($token, $validTokens);
    }

    /**
     * Get API key from request.
     */
    protected function getApiKey(Request $request): ?string
    {
        return $request->header('X-API-Key');
    }

    /**
     * Get API token from request.
     */
    protected function getApiToken(Request $request): ?string
    {
        return $request->bearerToken() ?? $request->header('X-API-Token');
    }

    /**
     * Check if request is from allowed origin.
     */
    protected function isAllowedOrigin(Request $request): bool
    {
        $allowedOrigins = $this->config['allowed_origins'] ?? [];

        if (empty($allowedOrigins)) {
            return true;
        }

        $origin = $request->header('Origin');

        if (!$origin) {
            return true;
        }

        return in_array($origin, $allowedOrigins);
    }

    /**
     * Check if request has valid CORS headers.
     */
    protected function hasValidCors(Request $request): bool
    {
        $corsConfig = $this->config['cors'] ?? [];

        if (!($corsConfig['enabled'] ?? false)) {
            return true;
        }

        $origin = $request->header('Origin');
        $allowedOrigins = $corsConfig['allowed_origins'] ?? [];

        if (empty($allowedOrigins)) {
            return true;
        }

        return in_array($origin, $allowedOrigins);
    }

    /**
     * Get user permissions.
     */
    protected function getUserPermissions($user): array
    {
        if (!$user) {
            return [];
        }

        if (method_exists($user, 'getPermissions')) {
            return $user->getPermissions();
        }

        if (method_exists($user, 'permissions')) {
            return $user->permissions ?? [];
        }

        return [];
    }

    /**
     * Check if user has specific permission.
     */
    protected function userHasPermission($user, string $permission): bool
    {
        if (!$user) {
            return false;
        }

        $permissions = $this->getUserPermissions($user);

        return in_array($permission, $permissions);
    }

    /**
     * Get user roles.
     */
    protected function getUserRoles($user): array
    {
        if (!$user) {
            return [];
        }

        if (method_exists($user, 'getRoles')) {
            return $user->getRoles();
        }

        if (method_exists($user, 'roles')) {
            return $user->roles ?? [];
        }

        return [];
    }

    /**
     * Check if user has specific role.
     */
    protected function userHasRole($user, string $role): bool
    {
        if (!$user) {
            return false;
        }

        $roles = $this->getUserRoles($user);

        return in_array($role, $roles);
    }
}
