# API Reference

## AlertManager

### Methods

#### `success(string $message, string $title = null, array $options = [])`
Creates a success alert.

**Parameters:**
- `$message` (string) - Alert message
- `$title` (string|null) - Alert title
- `$options` (array) - Alert options

**Returns:** `AlertManager`

**Example:**
```php
Alert::success('Operation completed successfully!');
Alert::success('User created', 'Success', ['dismissible' => true]);
```

#### `error(string $message, string $title = null, array $options = [])`
Creates an error alert.

**Parameters:**
- `$message` (string) - Alert message
- `$title` (string|null) - Alert title
- `$options` (array) - Alert options

**Returns:** `AlertManager`

**Example:**
```php
Alert::error('Failed to save data!');
Alert::error('Validation failed', 'Error', ['auto_dismiss' => false]);
```

#### `warning(string $message, string $title = null, array $options = [])`
Creates a warning alert.

**Parameters:**
- `$message` (string) - Alert message
- `$title` (string|null) - Alert title
- `$options` (array) - Alert options

**Returns:** `AlertManager`

**Example:**
```php
Alert::warning('Please check your input!');
Alert::warning('Low disk space', 'Warning', ['priority' => 3]);
```

#### `info(string $message, string $title = null, array $options = [])`
Creates an info alert.

**Parameters:**
- `$message` (string) - Alert message
- `$title` (string|null) - Alert title
- `$options` (array) - Alert options

**Returns:** `AlertManager`

**Example:**
```php
Alert::info('New features available!');
Alert::info('System maintenance', 'Info', ['expires_at' => now()->addHour()]);
```

### Fluent API Methods

#### `withTitle(string $title)`
Sets the alert title.

**Parameters:**
- `$title` (string) - Alert title

**Returns:** `AlertManager`

#### `withIcon(string $icon)`
Sets the alert icon.

**Parameters:**
- `$icon` (string) - Icon class

**Returns:** `AlertManager`

#### `withClass(string $class)`
Sets custom CSS class.

**Parameters:**
- `$class` (string) - CSS class

**Returns:** `AlertManager`

#### `withStyle(string $style)`
Sets custom inline style.

**Parameters:**
- `$style` (string) - Inline CSS

**Returns:** `AlertManager`

#### `dismissible(bool $dismissible = true)`
Sets dismissible option.

**Parameters:**
- `$dismissible` (bool) - Whether alert is dismissible

**Returns:** `AlertManager`

#### `autoDismiss(bool $autoDismiss = true)`
Sets auto-dismiss option.

**Parameters:**
- `$autoDismiss` (bool) - Whether alert auto-dismisses

**Returns:** `AlertManager`

#### `autoDismissDelay(int $delay)`
Sets auto-dismiss delay.

**Parameters:**
- `$delay` (int) - Delay in milliseconds

**Returns:** `AlertManager`

### Utility Methods

#### `getAlerts()`
Gets all alerts.

**Returns:** `array`

#### `count()`
Gets alert count.

**Returns:** `int`

#### `hasAlerts()`
Checks if alerts exist.

**Returns:** `bool`

#### `clear()`
Clears all alerts.

**Returns:** `void`

#### `clearByType(string $type)`
Clears alerts by type.

**Parameters:**
- `$type` (string) - Alert type

**Returns:** `void`

## REST API

### Endpoints

#### `GET /api/v1/alerts`
Get all alerts.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "alert_123",
      "type": "success",
      "message": "Operation completed",
      "title": "Success",
      "dismissible": true,
      "auto_dismiss": false,
      "created_at": "2024-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "total": 1,
    "per_page": 15,
    "current_page": 1
  }
}
```

#### `POST /api/v1/alerts`
Create new alert.

**Request:**
```json
{
  "type": "success",
  "message": "Operation completed",
  "title": "Success",
  "dismissible": true,
  "auto_dismiss": false
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "alert_123",
    "type": "success",
    "message": "Operation completed",
    "title": "Success",
    "dismissible": true,
    "auto_dismiss": false,
    "created_at": "2024-01-01T00:00:00Z"
  }
}
```

#### `GET /api/v1/alerts/{id}`
Get specific alert.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "alert_123",
    "type": "success",
    "message": "Operation completed",
    "title": "Success",
    "dismissible": true,
    "auto_dismiss": false,
    "created_at": "2024-01-01T00:00:00Z"
  }
}
```

#### `PUT /api/v1/alerts/{id}`
Update alert.

**Request:**
```json
{
  "message": "Updated message",
  "title": "Updated title"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "alert_123",
    "type": "success",
    "message": "Updated message",
    "title": "Updated title",
    "dismissible": true,
    "auto_dismiss": false,
    "updated_at": "2024-01-01T00:00:00Z"
  }
}
```

#### `DELETE /api/v1/alerts/{id}`
Delete alert.

**Response:**
```json
{
  "success": true,
  "message": "Alert deleted successfully"
}
```

#### `POST /api/v1/alerts/{id}/dismiss`
Dismiss alert.

**Response:**
```json
{
  "success": true,
  "message": "Alert dismissed successfully"
}
```

#### `POST /api/v1/alerts/dismiss-all`
Dismiss all alerts.

**Response:**
```json
{
  "success": true,
  "message": "All alerts dismissed successfully"
}
```

#### `DELETE /api/v1/alerts/clear`
Clear all alerts.

**Response:**
```json
{
  "success": true,
  "message": "All alerts cleared successfully"
}
```

#### `GET /api/v1/alerts/type/{type}`
Get alerts by type.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "alert_123",
      "type": "success",
      "message": "Operation completed",
      "title": "Success",
      "created_at": "2024-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "total": 1,
    "type": "success"
  }
}
```

#### `GET /api/v1/alerts/stats/overview`
Get alert statistics.

**Response:**
```json
{
  "success": true,
  "data": {
    "total_alerts": 100,
    "success_count": 40,
    "error_count": 30,
    "warning_count": 20,
    "info_count": 10,
    "dismissed_count": 50,
    "active_count": 50
  }
}
```

#### `GET /api/v1/alerts/history/audit`
Get alert history.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "alert_123",
      "type": "success",
      "message": "Operation completed",
      "action": "created",
      "timestamp": "2024-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "total": 1,
    "per_page": 15,
    "current_page": 1
  }
}
```

#### `POST /api/v1/alerts/bulk/create`
Create multiple alerts.

**Request:**
```json
{
  "alerts": [
    {
      "type": "success",
      "message": "First alert"
    },
    {
      "type": "error",
      "message": "Second alert"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "created_count": 2,
    "alerts": [
      {
        "id": "alert_123",
        "type": "success",
        "message": "First alert"
      },
      {
        "id": "alert_124",
        "type": "error",
        "message": "Second alert"
      }
    ]
  }
}
```

#### `PATCH /api/v1/alerts/bulk/update`
Update multiple alerts.

**Request:**
```json
{
  "alerts": [
    {
      "id": "alert_123",
      "message": "Updated first alert"
    },
    {
      "id": "alert_124",
      "message": "Updated second alert"
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "updated_count": 2,
    "alerts": [
      {
        "id": "alert_123",
        "type": "success",
        "message": "Updated first alert"
      },
      {
        "id": "alert_124",
        "type": "error",
        "message": "Updated second alert"
      }
    ]
  }
}
```

### Authentication

All API endpoints require authentication. Supported methods:

- **Token Authentication**: `Authorization: Bearer {token}`
- **API Key**: `X-API-Key: {key}`
- **OAuth**: `Authorization: Bearer {oauth_token}`
- **JWT**: `Authorization: Bearer {jwt_token}`

### Error Responses

#### 400 Bad Request
```json
{
  "success": false,
  "error": "Bad Request",
  "message": "Invalid request data",
  "errors": {
    "message": ["The message field is required."]
  }
}
```

#### 401 Unauthorized
```json
{
  "success": false,
  "error": "Unauthorized",
  "message": "Authentication required"
}
```

#### 403 Forbidden
```json
{
  "success": false,
  "error": "Forbidden",
  "message": "Insufficient permissions"
}
```

#### 404 Not Found
```json
{
  "success": false,
  "error": "Not Found",
  "message": "Alert not found"
}
```

#### 422 Unprocessable Entity
```json
{
  "success": false,
  "error": "Unprocessable Entity",
  "message": "Validation failed",
  "errors": {
    "type": ["The type field is required."],
    "message": ["The message field is required."]
  }
}
```

#### 500 Internal Server Error
```json
{
  "success": false,
  "error": "Internal Server Error",
  "message": "An error occurred while processing your request"
}
```

### Rate Limiting

API requests are rate limited:

- **Default**: 60 requests per minute
- **Burst**: 100 requests per minute
- **Headers**: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`

### Pagination

List endpoints support pagination:

- **Page**: `?page=1`
- **Per Page**: `?per_page=15`
- **Sort**: `?sort=created_at`
- **Order**: `?order=desc`

### Filtering

List endpoints support filtering:

- **Type**: `?type=success`
- **Status**: `?status=active`
- **Date Range**: `?from=2024-01-01&to=2024-01-31`
- **Search**: `?search=keyword`

### Sorting

List endpoints support sorting:

- **Field**: `?sort=created_at`
- **Direction**: `?order=desc`
- **Multiple**: `?sort=type,created_at&order=asc,desc`
