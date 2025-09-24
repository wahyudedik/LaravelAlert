# Examples

This page contains practical examples of using the Laravel Alert library in various scenarios.

## 🚀 Basic Examples

### Simple Alerts

```php
use Wahyudedik\LaravelAlert\Facades\Alert;

// Success alert
Alert::success('Operation completed successfully!');

// Error alert
Alert::error('Something went wrong!');

// Warning alert
Alert::warning('Please check your input!');

// Info alert
Alert::info('Here is some information!');
```

### Alerts with Titles

```php
Alert::success('User created successfully!', 'Success');
Alert::error('Failed to create user!', 'Error');
Alert::warning('Please fill all required fields!', 'Warning');
Alert::info('Welcome to our platform!', 'Welcome');
```

### Alerts with Options

```php
Alert::success('User created!', 'Success', [
    'dismissible' => true,
    'auto_dismiss' => true,
    'auto_dismiss_delay' => 5000,
    'theme' => 'bootstrap',
    'position' => 'top-right',
    'animation' => 'fade'
]);
```

## 🎨 Blade Examples

### Basic Blade Usage

```blade
{{-- Single Alert --}}
<x-alert type="success" message="Operation completed!" />

{{-- Alert with Title --}}
<x-alert 
    type="error" 
    message="Something went wrong!" 
    title="Error" 
/>

{{-- Alert with Options --}}
<x-alert 
    type="warning" 
    message="Please check your input!" 
    title="Warning"
    dismissible="true"
    auto-dismiss="true"
    auto-dismiss-delay="5000"
/>
```

### Multiple Alerts Container

```blade
{{-- Display all alerts --}}
<x-alerts />

{{-- Alerts with custom options --}}
<x-alerts 
    theme="bootstrap"
    position="top-right"
    animation="fade"
    auto-clear="true"
    max-alerts="5"
/>
```

### Blade Directives

```blade
{{-- Alert directive --}}
@alert('success', 'Operation completed!')

{{-- Alerts directive --}}
@alerts

{{-- Conditional alert --}}
@alertIf($user->isNew(), 'info', 'Welcome to our platform!')
```

## 🔧 Form Validation Examples

### Laravel Validation

```php
use Illuminate\Http\Request;
use Wahyudedik\LaravelAlert\Facades\Alert;

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8'
    ]);

    try {
        $user = User::create($validated);
        Alert::success('User created successfully!', 'Success');
        return redirect()->route('users.index');
    } catch (\Exception $e) {
        Alert::error('Failed to create user: ' . $e->getMessage(), 'Error');
        return back()->withInput();
    }
}
```

### Custom Validation Messages

```php
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|unique:users'
    ], [
        'email.required' => 'Email is required',
        'email.email' => 'Please enter a valid email',
        'email.unique' => 'This email is already registered'
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            Alert::error($error);
        }
        return back()->withInput();
    }

    // Process form...
}
```

### Field-specific Alerts

```php
public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:8'
    ]);

    if ($request->filled('password')) {
        $user->update([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);
        Alert::success('Profile updated successfully!', 'Success');
    } else {
        $user->update(['email' => $validated['email']]);
        Alert::info('Email updated successfully!', 'Info');
    }

    return redirect()->route('profile');
}
```

## 🌐 AJAX Examples

### JavaScript Integration

```javascript
// Create alert via AJAX
function createAlert(type, message, title = null) {
    fetch('/api/v1/alerts', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + getAuthToken(),
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            type: type,
            message: message,
            title: title
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Alert created successfully
            console.log('Alert created:', data.data.alert);
        } else {
            console.error('Failed to create alert:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Usage
createAlert('success', 'User created successfully!', 'Success');
createAlert('error', 'Failed to create user!', 'Error');
```

### Fetch Alerts

```javascript
// Fetch all alerts
function fetchAlerts() {
    fetch('/api/v1/alerts', {
        headers: {
            'Authorization': 'Bearer ' + getAuthToken()
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayAlerts(data.data.alerts);
        }
    });
}

// Display alerts in UI
function displayAlerts(alerts) {
    const container = document.getElementById('alerts-container');
    container.innerHTML = '';

    alerts.forEach(alert => {
        const alertElement = document.createElement('div');
        alertElement.className = `alert alert-${alert.type}`;
        alertElement.innerHTML = `
            <strong>${alert.title || ''}</strong>
            ${alert.message}
            <button type="button" class="btn-close" onclick="dismissAlert('${alert.id}')"></button>
        `;
        container.appendChild(alertElement);
    });
}
```

### Dismiss Alert

```javascript
// Dismiss specific alert
function dismissAlert(alertId) {
    fetch(`/api/v1/alerts/${alertId}/dismiss`, {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken()
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove alert from UI
            const alertElement = document.querySelector(`[data-alert-id="${alertId}"]`);
            if (alertElement) {
                alertElement.remove();
            }
        }
    });
}

// Dismiss all alerts
function dismissAllAlerts() {
    fetch('/api/v1/alerts/dismiss-all', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + getAuthToken()
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear all alerts from UI
            document.getElementById('alerts-container').innerHTML = '';
        }
    });
}
```

## 🎯 Real-time Examples

### WebSocket Integration

```javascript
// Connect to WebSocket
const socket = new WebSocket('ws://your-domain.com/laravel-alert/ws/connect');

socket.onopen = function(event) {
    console.log('Connected to WebSocket');
    
    // Subscribe to alerts
    socket.send(JSON.stringify({
        action: 'subscribe',
        channel: 'alerts'
    }));
};

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    
    if (data.type === 'alert') {
        // Display new alert
        displayAlert(data.alert);
    } else if (data.type === 'alert_dismissed') {
        // Remove dismissed alert
        removeAlert(data.alert_id);
    }
};

// Send alert via WebSocket
function sendAlert(type, message, title) {
    socket.send(JSON.stringify({
        action: 'create_alert',
        alert: {
            type: type,
            message: message,
            title: title
        }
    }));
}
```

### Pusher Integration

```javascript
// Initialize Pusher
const pusher = new Pusher('your-pusher-key', {
    cluster: 'your-cluster'
});

// Subscribe to alerts channel
const channel = pusher.subscribe('alerts');

// Listen for new alerts
channel.bind('alert.created', function(data) {
    displayAlert(data.alert);
});

// Listen for dismissed alerts
channel.bind('alert.dismissed', function(data) {
    removeAlert(data.alert_id);
});

// Listen for updated alerts
channel.bind('alert.updated', function(data) {
    updateAlert(data.alert);
});
```

## 🎨 Custom Theme Examples

### Bootstrap Theme

```blade
{{-- Bootstrap alert --}}
<x-alert 
    type="success" 
    message="Operation completed!" 
    theme="bootstrap"
    class="alert-success"
    dismissible="true"
/>
```

### Tailwind Theme

```blade
{{-- Tailwind alert --}}
<x-alert 
    type="error" 
    message="Something went wrong!" 
    theme="tailwind"
    class="bg-red-50 text-red-800 border-red-200"
    dismissible="true"
/>
```

### Bulma Theme

```blade
{{-- Bulma alert --}}
<x-alert 
    type="warning" 
    message="Please check your input!" 
    theme="bulma"
    class="notification is-warning"
    dismissible="true"
/>
```

### Custom Theme

```blade
{{-- Custom theme --}}
<x-alert 
    type="info" 
    message="Here is some information!" 
    theme="custom"
    class="custom-alert custom-alert-info"
    style="background: #e3f2fd; color: #1976d2;"
    dismissible="true"
/>
```

## 🎭 Animation Examples

### Fade Animation

```blade
<x-alert 
    type="success" 
    message="Operation completed!" 
    animation="fade"
    animation-duration="300"
/>
```

### Slide Animation

```blade
<x-alert 
    type="error" 
    message="Something went wrong!" 
    animation="slide"
    animation-duration="500"
/>
```

### Bounce Animation

```blade
<x-alert 
    type="warning" 
    message="Please check your input!" 
    animation="bounce"
    animation-duration="600"
/>
```

### Custom Animation

```blade
<x-alert 
    type="info" 
    message="Here is some information!" 
    animation="custom"
    animation-duration="400"
    animation-class="custom-bounce"
/>
```

## 🔧 Advanced Examples

### Conditional Alerts

```php
// Show alert based on condition
if ($user->isNew()) {
    Alert::info('Welcome to our platform!', 'Welcome');
} elseif ($user->isReturning()) {
    Alert::success('Welcome back!', 'Welcome Back');
}

// Show alert based on user role
if ($user->hasRole('admin')) {
    Alert::info('Admin panel access granted', 'Admin');
} elseif ($user->hasRole('user')) {
    Alert::info('User dashboard access granted', 'Dashboard');
}
```

### Context-specific Alerts

```php
// Form-specific alerts
Alert::error('Email is required', 'Validation Error', [
    'context' => 'user_registration',
    'field' => 'email',
    'form' => 'registration'
]);

// Page-specific alerts
Alert::info('New features available!', 'Update', [
    'context' => 'dashboard',
    'page' => 'home'
]);
```

### Priority-based Alerts

```php
// High priority alert
Alert::error('System maintenance in 5 minutes!', 'Maintenance', [
    'priority' => 10,
    'auto_dismiss' => false,
    'dismissible' => false
]);

// Low priority alert
Alert::info('New feature available', 'Feature', [
    'priority' => 1,
    'auto_dismiss' => true,
    'auto_dismiss_delay' => 10000
]);
```

### Expiration-based Alerts

```php
// Temporary alert (expires in 1 hour)
Alert::info('Temporary message', 'Info', [
    'expires_at' => now()->addHour()
]);

// Flash alert (expires in 5 minutes)
Alert::success('Flash message', 'Success', [
    'expires_at' => now()->addMinutes(5)
]);
```

## 🧪 Testing Examples

### Unit Tests

```php
use Tests\TestCase;
use Wahyudedik\LaravelAlert\Facades\Alert;

class AlertTest extends TestCase
{
    public function test_can_create_success_alert()
    {
        Alert::success('Test message');
        
        $alerts = Alert::getAlerts();
        $this->assertCount(1, $alerts);
        $this->assertEquals('success', $alerts[0]['type']);
        $this->assertEquals('Test message', $alerts[0]['message']);
    }

    public function test_can_create_alert_with_title()
    {
        Alert::success('Test message', 'Test Title');
        
        $alerts = Alert::getAlerts();
        $this->assertEquals('Test Title', $alerts[0]['title']);
    }

    public function test_can_create_alert_with_options()
    {
        Alert::success('Test message', 'Test Title', [
            'dismissible' => true,
            'auto_dismiss' => true,
            'auto_dismiss_delay' => 5000
        ]);
        
        $alerts = Alert::getAlerts();
        $this->assertTrue($alerts[0]['dismissible']);
        $this->assertTrue($alerts[0]['auto_dismiss']);
        $this->assertEquals(5000, $alerts[0]['auto_dismiss_delay']);
    }
}
```

### Feature Tests

```php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlertFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_alert_via_api()
    {
        $response = $this->postJson('/api/v1/alerts', [
            'type' => 'success',
            'message' => 'Test message',
            'title' => 'Test Title'
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'alert' => [
                            'type' => 'success',
                            'message' => 'Test message',
                            'title' => 'Test Title'
                        ]
                    ]
                ]);
    }

    public function test_can_get_alerts_via_api()
    {
        // Create some alerts first
        $this->postJson('/api/v1/alerts', [
            'type' => 'success',
            'message' => 'Test message 1'
        ]);

        $this->postJson('/api/v1/alerts', [
            'type' => 'error',
            'message' => 'Test message 2'
        ]);

        $response = $this->getJson('/api/v1/alerts');

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'alerts' => [
                            [
                                'type' => 'success',
                                'message' => 'Test message 1'
                            ],
                            [
                                'type' => 'error',
                                'message' => 'Test message 2'
                            ]
                        ]
                    ]
                ]);
    }
}
```

## 🚀 Performance Examples

### Batch Operations

```php
// Create multiple alerts at once
$alerts = [
    ['type' => 'success', 'message' => 'User 1 created'],
    ['type' => 'success', 'message' => 'User 2 created'],
    ['type' => 'success', 'message' => 'User 3 created']
];

Alert::addMultiple($alerts);
```

### Cache Optimization

```php
// Use cache for better performance
$cacheManager = new CacheAlertManager();
$cacheManager->setCacheDriver('redis');
$cacheManager->success('Cached alert!');
```

### Database Optimization

```php
// Use database for persistence
$dbManager = new DatabaseAlertManager();
$dbManager->success('Persistent alert!');
```

---

**Next**: [API Documentation](api.md)
