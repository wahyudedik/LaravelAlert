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
