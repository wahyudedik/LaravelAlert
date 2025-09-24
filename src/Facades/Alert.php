<?php

namespace Wahyudedik\LaravelAlert\Facades;

use Illuminate\Support\Facades\Facade;
use Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface;

/**
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface success(string $message, ?string $title = null, array $options = [])
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface error(string $message, ?string $title = null, array $options = [])
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface warning(string $message, ?string $title = null, array $options = [])
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface info(string $message, ?string $title = null, array $options = [])
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface add(string $type, string $message, ?string $title = null, array $options = [])
 * @method static array getAlerts()
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface clear()
 * @method static string render(string $type, string $message, ?string $title = null, array $options = [])
 * @method static string renderAll()
 * @method static int count()
 * @method static bool hasAlerts()
 * @method static array getAlertsByType(string $type)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface clearByType(string $type)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface addMultiple(array $alerts)
 * @method static \Wahyudedik\LaravelAlert\Models\Alert|null first()
 * @method static \Wahyudedik\LaravelAlert\Models\Alert|null last()
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface removeById(string $id)
 * @method static array flush()
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface addWithExpiration(string $type, string $message, ?string $title = null, int $expiresInSeconds = 3600, array $options = [])
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface addWithAutoDismiss(string $type, string $message, ?string $title = null, int $autoDismissDelay = 5000, array $options = [])
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface temporary(string $type, string $message, ?string $title = null, int $expiresInSeconds = 300, array $options = [])
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface flash(string $type, string $message, ?string $title = null, int $autoDismissDelay = 3000, array $options = [])
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface cleanupExpired()
 * @method static array getExpiredAlerts()
 * @method static array getAutoDismissAlerts()
 * 
 * Fluent API Methods:
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface withIcon(string $icon)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface withClass(string $class)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface withStyle(string $style)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface withAnimation(string $animation)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface withTheme(string $theme)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface withPosition(string $position)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface withDataAttribute(string $key, string $value)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface withHtmlContent(string $content)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface dismissible(bool $dismissible = true)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface expiresIn(int $seconds)
 * @method static \Wahyudedik\LaravelAlert\Contracts\AlertManagerInterface autoDismissIn(int $milliseconds)
 */
class Alert extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'alert.manager';
    }
}
