<?php

/**
 * Laravel Alert Type Hints
 * 
 * This file provides comprehensive type hints and documentation
 * for IDE autocompletion and IntelliSense support.
 * 
 * @package Wahyudedik\LaravelAlert
 * @version 1.0.0
 * @author Wahyudedik
 * @license MIT
 * @link https://github.com/wahyudedik/LaravelAlert
 */

namespace Wahyudedik\LaravelAlert;

/**
 * Alert Manager Interface
 * 
 * @method static Alert success(string $message, string $title = null, array $options = [])
 * @method static Alert error(string $message, string $title = null, array $options = [])
 * @method static Alert warning(string $message, string $title = null, array $options = [])
 * @method static Alert info(string $message, string $title = null, array $options = [])
 * @method static Alert custom(string $type, string $message, string $title = null, array $options = [])
 * @method static Alert ajax(string $type, string $message, array $options = [])
 * @method static Alert websocket(string $type, string $message, array $options = [])
 * @method static Alert pusher(string $type, string $message, array $options = [])
 * @method static Alert email(string $type, string $message, array $options = [])
 * @method static Alert bulk(array $alerts)
 * @method static Alert clear(string $type = null)
 * @method static Alert clearAll()
 * @method static int count(string $type = null)
 * @method static bool has(string $type)
 * @method static array get(string $type)
 * @method static array all()
 * @method static string render(string $type = null)
 * @method static string renderAll()
 * @method static Alert title(string $title)
 * @method static Alert dismissible(bool $dismissible = true)
 * @method static Alert autoDismiss(int $delay = 5000)
 * @method static Alert position(string $position)
 * @method static Alert theme(string $theme)
 * @method static Alert animation(string $animation)
 * @method static Alert icon(string $icon)
 * @method static Alert context(string $context)
 * @method static Alert field(string $field)
 * @method static Alert priority(string $priority)
 * @method static Alert expiresAt(\DateTime $expiresAt)
 * @method static Alert withData(array $data)
 * @method static Alert withActions(array $actions)
 * @method static Alert withOptions(array $options)
 */
interface AlertManagerInterface
{
    // Interface methods are documented above
}

/**
 * Toast Alert Manager Interface
 * 
 * @method static Toast success(string $message, string $title = null, array $options = [])
 * @method static Toast error(string $message, string $title = null, array $options = [])
 * @method static Toast warning(string $message, string $title = null, array $options = [])
 * @method static Toast info(string $message, string $title = null, array $options = [])
 * @method static Toast custom(string $type, string $message, string $title = null, array $options = [])
 * @method static Toast position(string $position)
 * @method static Toast duration(int $duration)
 * @method static Toast dismissible(bool $dismissible = true)
 * @method static Toast autoDismiss(int $delay = 5000)
 * @method static Toast theme(string $theme)
 * @method static Toast animation(string $animation)
 * @method static Toast icon(string $icon)
 * @method static Toast context(string $context)
 * @method static Toast field(string $field)
 * @method static Toast priority(string $priority)
 * @method static Toast expiresAt(\DateTime $expiresAt)
 * @method static Toast withData(array $data)
 * @method static Toast withActions(array $actions)
 * @method static Toast withOptions(array $options)
 */
interface ToastAlertManagerInterface
{
    // Interface methods are documented above
}

/**
 * Modal Alert Manager Interface
 * 
 * @method static Modal success(string $message, string $title = null, array $options = [])
 * @method static Modal error(string $message, string $title = null, array $options = [])
 * @method static Modal warning(string $message, string $title = null, array $options = [])
 * @method static Modal info(string $message, string $title = null, array $options = [])
 * @method static Modal custom(string $type, string $message, string $title = null, array $options = [])
 * @method static Modal size(string $size)
 * @method static Modal backdrop(bool $backdrop = true)
 * @method static Modal keyboard(bool $keyboard = true)
 * @method static Modal dismissible(bool $dismissible = true)
 * @method static Modal autoDismiss(int $delay = 5000)
 * @method static Modal theme(string $theme)
 * @method static Modal animation(string $animation)
 * @method static Modal icon(string $icon)
 * @method static Modal context(string $context)
 * @method static Modal field(string $field)
 * @method static Modal priority(string $priority)
 * @method static Modal expiresAt(\DateTime $expiresAt)
 * @method static Modal withData(array $data)
 * @method static Modal withActions(array $actions)
 * @method static Modal withOptions(array $options)
 */
interface ModalAlertManagerInterface
{
    // Interface methods are documented above
}

/**
 * Inline Alert Manager Interface
 * 
 * @method static Inline success(string $message, string $title = null, array $options = [])
 * @method static Inline error(string $message, string $title = null, array $options = [])
 * @method static Inline warning(string $message, string $title = null, array $options = [])
 * @method static Inline info(string $message, string $title = null, array $options = [])
 * @method static Inline custom(string $type, string $message, string $title = null, array $options = [])
 * @method static Inline context(string $context)
 * @method static Inline field(string $field)
 * @method static Inline dismissible(bool $dismissible = true)
 * @method static Inline autoDismiss(int $delay = 5000)
 * @method static Inline theme(string $theme)
 * @method static Inline animation(string $animation)
 * @method static Inline icon(string $icon)
 * @method static Inline priority(string $priority)
 * @method static Inline expiresAt(\DateTime $expiresAt)
 * @method static Inline withData(array $data)
 * @method static Inline withActions(array $actions)
 * @method static Inline withOptions(array $options)
 */
interface InlineAlertManagerInterface
{
    // Interface methods are documented above
}

/**
 * Alert Configuration Type Hints
 * 
 * @property string $theme Alert theme (bootstrap, tailwind, bulma)
 * @property string $position Alert position (top-right, top-left, bottom-right, bottom-left, top-center, bottom-center)
 * @property bool $dismissible Whether alerts are dismissible
 * @property bool $auto_dismiss Whether alerts auto-dismiss
 * @property int $auto_dismiss_delay Auto-dismiss delay in milliseconds
 * @property string $session_key Session key for storing alerts
 * @property bool $javascript_enabled Whether JavaScript is enabled
 * @property array $themes Available themes
 * @property array $positions Available positions
 * @property array $animations Available animations
 * @property array $icons Available icons
 * @property array $priorities Available priorities
 * @property array $contexts Available contexts
 * @property array $fields Available fields
 * @property array $storage Storage configuration
 * @property array $cache Cache configuration
 * @property array $redis Redis configuration
 * @property array $performance Performance configuration
 * @property array $pusher Pusher configuration
 * @property array $websocket WebSocket configuration
 * @property array $email Email configuration
 */
class AlertConfig
{
    // Configuration properties are documented above
}

/**
 * Alert Options Type Hints
 * 
 * @property string $type Alert type (success, error, warning, info, custom)
 * @property string $message Alert message
 * @property string $title Alert title
 * @property bool $dismissible Whether alert is dismissible
 * @property int $auto_dismiss_delay Auto-dismiss delay in milliseconds
 * @property string $position Alert position
 * @property string $theme Alert theme
 * @property string $animation Alert animation
 * @property string $icon Alert icon
 * @property string $context Alert context
 * @property string $field Alert field
 * @property string $priority Alert priority
 * @property \DateTime $expires_at Alert expiration date
 * @property array $data Additional data
 * @property array $actions Alert actions
 * @property array $options Additional options
 */
class AlertOptions
{
    // Alert options properties are documented above
}

/**
 * Alert Response Type Hints
 * 
 * @property bool $success Whether the operation was successful
 * @property string $message Response message
 * @property array $data Response data
 * @property array $alerts Alert data
 * @property int $count Alert count
 * @property array $errors Error messages
 * @property array $warnings Warning messages
 * @property array $info Info messages
 * @property array $success_messages Success messages
 */
class AlertResponse
{
    // Response properties are documented above
}

/**
 * Alert Statistics Type Hints
 * 
 * @property int $total Total alerts
 * @property int $success Success alerts
 * @property int $error Error alerts
 * @property int $warning Warning alerts
 * @property int $info Info alerts
 * @property int $custom Custom alerts
 * @property array $by_type Alerts by type
 * @property array $by_context Alerts by context
 * @property array $by_field Alerts by field
 * @property array $by_priority Alerts by priority
 * @property array $by_theme Alerts by theme
 * @property array $by_position Alerts by position
 * @property array $by_animation Alerts by animation
 * @property array $by_icon Alerts by icon
 * @property array $by_date Alerts by date
 * @property array $by_hour Alerts by hour
 * @property array $by_day Alerts by day
 * @property array $by_week Alerts by week
 * @property array $by_month Alerts by month
 * @property array $by_year Alerts by year
 */
class AlertStatistics
{
    // Statistics properties are documented above
}

/**
 * Alert Performance Type Hints
 * 
 * @property float $memory_usage Memory usage in bytes
 * @property float $execution_time Execution time in seconds
 * @property int $database_queries Database queries count
 * @property float $cache_hit_rate Cache hit rate percentage
 * @property int $redis_operations Redis operations count
 * @property int $session_operations Session operations count
 * @property int $ajax_requests AJAX requests count
 * @property int $websocket_connections WebSocket connections count
 * @property int $pusher_events Pusher events count
 * @property int $email_sent Emails sent count
 * @property array $performance_metrics Performance metrics
 */
class AlertPerformance
{
    // Performance properties are documented above
}

/**
 * Alert Integration Type Hints
 * 
 * @property bool $pusher_enabled Whether Pusher is enabled
 * @property bool $websocket_enabled Whether WebSocket is enabled
 * @property bool $email_enabled Whether email is enabled
 * @property bool $ajax_enabled Whether AJAX is enabled
 * @property bool $database_enabled Whether database is enabled
 * @property bool $redis_enabled Whether Redis is enabled
 * @property bool $cache_enabled Whether cache is enabled
 * @property array $integrations Available integrations
 * @property array $pusher_config Pusher configuration
 * @property array $websocket_config WebSocket configuration
 * @property array $email_config Email configuration
 * @property array $ajax_config AJAX configuration
 * @property array $database_config Database configuration
 * @property array $redis_config Redis configuration
 * @property array $cache_config Cache configuration
 */
class AlertIntegration
{
    // Integration properties are documented above
}

/**
 * Alert Theme Type Hints
 * 
 * @property string $name Theme name
 * @property string $display_name Theme display name
 * @property string $description Theme description
 * @property string $version Theme version
 * @property string $author Theme author
 * @property string $license Theme license
 * @property string $homepage Theme homepage
 * @property array $colors Theme colors
 * @property array $fonts Theme fonts
 * @property array $sizes Theme sizes
 * @property array $spacing Theme spacing
 * @property array $borders Theme borders
 * @property array $shadows Theme shadows
 * @property array $animations Theme animations
 * @property array $icons Theme icons
 * @property array $templates Theme templates
 * @property array $styles Theme styles
 * @property array $scripts Theme scripts
 */
class AlertTheme
{
    // Theme properties are documented above
}

/**
 * Alert Animation Type Hints
 * 
 * @property string $name Animation name
 * @property string $display_name Animation display name
 * @property string $description Animation description
 * @property string $type Animation type (entrance, exit, attention)
 * @property string $direction Animation direction (up, down, left, right, center)
 * @property string $easing Animation easing
 * @property int $duration Animation duration in milliseconds
 * @property int $delay Animation delay in milliseconds
 * @property bool $infinite Whether animation is infinite
 * @property array $keyframes Animation keyframes
 * @property array $properties Animation properties
 * @property array $triggers Animation triggers
 */
class AlertAnimation
{
    // Animation properties are documented above
}

/**
 * Alert Icon Type Hints
 * 
 * @property string $name Icon name
 * @property string $display_name Icon display name
 * @property string $description Icon description
 * @property string $type Icon type (font, svg, image)
 * @property string $class Icon CSS class
 * @property string $unicode Icon Unicode
 * @property string $svg Icon SVG
 * @property string $image Icon image
 * @property array $sizes Icon sizes
 * @property array $colors Icon colors
 * @property array $variants Icon variants
 */
class AlertIcon
{
    // Icon properties are documented above
}

/**
 * Alert Position Type Hints
 * 
 * @property string $name Position name
 * @property string $display_name Position display name
 * @property string $description Position description
 * @property string $vertical Vertical position (top, bottom, center)
 * @property string $horizontal Horizontal position (left, right, center)
 * @property array $coordinates Position coordinates
 * @property array $offsets Position offsets
 * @property array $z_index Position z-index
 * @property array $responsive Responsive positions
 */
class AlertPosition
{
    // Position properties are documented above
}

/**
 * Alert Priority Type Hints
 * 
 * @property string $name Priority name
 * @property string $display_name Priority display name
 * @property string $description Priority description
 * @property int $level Priority level (1-10)
 * @property string $color Priority color
 * @property string $icon Priority icon
 * @property array $styles Priority styles
 * @property array $behaviors Priority behaviors
 */
class AlertPriority
{
    // Priority properties are documented above
}

/**
 * Alert Context Type Hints
 * 
 * @property string $name Context name
 * @property string $display_name Context display name
 * @property string $description Context description
 * @property string $type Context type (form, page, section, component)
 * @property string $scope Context scope (global, local, session)
 * @property array $fields Context fields
 * @property array $rules Context rules
 * @property array $styles Context styles
 * @property array $behaviors Context behaviors
 */
class AlertContext
{
    // Context properties are documented above
}

/**
 * Alert Field Type Hints
 * 
 * @property string $name Field name
 * @property string $display_name Field display name
 * @property string $description Field description
 * @property string $type Field type (input, select, textarea, checkbox, radio)
 * @property string $validation Field validation
 * @property array $rules Field rules
 * @property array $options Field options
 * @property array $styles Field styles
 * @property array $behaviors Field behaviors
 */
class AlertField
{
    // Field properties are documented above
}

/**
 * Alert Action Type Hints
 * 
 * @property string $name Action name
 * @property string $display_name Action display name
 * @property string $description Action description
 * @property string $type Action type (button, link, form, ajax)
 * @property string $label Action label
 * @property string $url Action URL
 * @property string $method Action method
 * @property array $data Action data
 * @property array $options Action options
 * @property array $styles Action styles
 * @property array $behaviors Action behaviors
 */
class AlertAction
{
    // Action properties are documented above
}

/**
 * Alert Data Type Hints
 * 
 * @property mixed $value Data value
 * @property string $type Data type
 * @property string $format Data format
 * @property array $metadata Data metadata
 * @property array $validation Data validation
 * @property array $transformation Data transformation
 * @property array $serialization Data serialization
 */
class AlertData
{
    // Data properties are documented above
}

/**
 * Alert Options Type Hints
 * 
 * @property array $global Global options
 * @property array $type Type-specific options
 * @property array $context Context-specific options
 * @property array $field Field-specific options
 * @property array $priority Priority-specific options
 * @property array $theme Theme-specific options
 * @property array $position Position-specific options
 * @property array $animation Animation-specific options
 * @property array $icon Icon-specific options
 * @property array $integration Integration-specific options
 */
class AlertOptionsConfig
{
    // Options configuration properties are documented above
}
