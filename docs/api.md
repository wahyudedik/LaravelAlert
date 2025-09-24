# API Documentation

The Laravel Alert library provides a comprehensive REST API for managing alerts programmatically.

## 🔗 Base URL

```
https://your-domain.com/api/v1/alerts
```

## 🔐 Authentication

The API supports multiple authentication methods:

### Bearer Token
```bash
Authorization: Bearer your-token-here
```

### API Key
```bash
X-API-Key: your-api-key-here
```

### Custom Token
```bash
X-API-Token: your-custom-token-here
```

## 📋 Endpoints Overview

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/alerts` | List all alerts |
| POST | `/api/v1/alerts` | Create new alert |
| GET | `/api/v1/alerts/{id}` | Get specific alert |
| PUT | `/api/v1/alerts/{id}` | Update alert |
| DELETE | `/api/v1/alerts/{id}` | Delete alert |
| POST | `/api/v1/alerts/{id}/dismiss` | Dismiss alert |
| POST | `/api/v1/alerts/dismiss-all` | Dismiss all alerts |
| GET | `/api/v1/alerts/type/{type}` | Get alerts by type |
| GET | `/api/v1/alerts/stats/overview` | Get alert statistics |
| GET | `/api/v1/alerts/history/audit` | Get alert history |
| POST | `/api/v1/alerts/bulk/create` | Create multiple alerts |
| PATCH | `/api/v1/alerts/bulk/update` | Update multiple alerts |

## 📊 Response Format

### Success Response
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "alerts": [...],
    "pagination": {...}
  },
  "meta": {
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "status": 200
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": [...],
  "meta": {
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "status": 400
  }
}
```

## 🚀 Basic Operations

### List All Alerts

```bash
GET /api/v1/alerts
```

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15)
- `type` - Filter by alert type
- `theme` - Filter by theme
- `status` - Filter by status
- `priority` - Filter by priority
- `date_from` - Filter from date
- `date_to` - Filter to date

**Example:**
```bash
curl -X GET "https://your-domain.com/api/v1/alerts?page=1&per_page=10&type=success" \
  -H "Authorization: Bearer your-token"
```

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "alerts": [
      {
        "id": "alert_1234567890_abcdef",
        "type": "success",
        "message": "Operation completed successfully!",
        "title": "Success",
        "alert_type": "alert",
        "theme": "bootstrap",
        "position": "top-right",
        "animation": "fade",
        "dismissible": true,
        "auto_dismiss": true,
        "auto_dismiss_delay": 5000,
        "expires_at": null,
        "priority": 0,
        "context": null,
        "field": null,
        "form": null,
        "icon": null,
        "class": null,
        "style": null,
        "html_content": null,
        "data_attributes": null,
        "options": null,
        "created_at": 1704067200,
        "updated_at": 1704067200,
        "is_active": true,
        "dismissed_at": null,
        "read_at": null
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 1,
      "last_page": 1,
      "from": 1,
      "to": 1,
      "has_more_pages": false
    }
  },
  "meta": {
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "status": 200
  }
}
```

### Create New Alert

```bash
POST /api/v1/alerts
```

**Request Body:**
```json
{
  "type": "success",
  "message": "Operation completed successfully!",
  "title": "Success",
  "alert_type": "alert",
  "theme": "bootstrap",
  "position": "top-right",
  "animation": "fade",
  "dismissible": true,
  "auto_dismiss": true,
  "auto_dismiss_delay": 5000,
  "expires_at": "2024-01-02T00:00:00Z",
  "priority": 0,
  "context": "user_registration",
  "field": "email",
  "form": "registration",
  "icon": "check-circle",
  "class": "custom-alert",
  "style": "background: #d4edda;",
  "html_content": "<strong>Success!</strong>",
  "data_attributes": {
    "data-custom": "value"
  },
  "options": {
    "sound": true,
    "vibration": true
  }
}
```

**Example:**
```bash
curl -X POST "https://your-domain.com/api/v1/alerts" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "success",
    "message": "User created successfully!",
    "title": "Success"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "Alert created successfully",
  "data": {
    "alert": {
      "id": "alert_1234567890_abcdef",
      "type": "success",
      "message": "User created successfully!",
      "title": "Success",
      "alert_type": "alert",
      "theme": "bootstrap",
      "position": "top-right",
      "animation": "fade",
      "dismissible": true,
      "auto_dismiss": true,
      "auto_dismiss_delay": 5000,
      "expires_at": null,
      "priority": 0,
      "context": null,
      "field": null,
      "form": null,
      "icon": null,
      "class": null,
      "style": null,
      "html_content": null,
      "data_attributes": null,
      "options": null,
      "created_at": 1704067200,
      "updated_at": 1704067200,
      "is_active": true,
      "dismissed_at": null,
      "read_at": null
    }
  },
  "meta": {
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "status": 201
  }
}
```

### Get Specific Alert

```bash
GET /api/v1/alerts/{id}
```

**Example:**
```bash
curl -X GET "https://your-domain.com/api/v1/alerts/alert_1234567890_abcdef" \
  -H "Authorization: Bearer your-token"
```

### Update Alert

```bash
PUT /api/v1/alerts/{id}
```

**Request Body:**
```json
{
  "type": "warning",
  "message": "Updated message",
  "title": "Updated Title"
}
```

**Example:**
```bash
curl -X PUT "https://your-domain.com/api/v1/alerts/alert_1234567890_abcdef" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "warning",
    "message": "Updated message"
  }'
```

### Delete Alert

```bash
DELETE /api/v1/alerts/{id}
```

**Example:**
```bash
curl -X DELETE "https://your-domain.com/api/v1/alerts/alert_1234567890_abcdef" \
  -H "Authorization: Bearer your-token"
```

## 🎯 Alert Actions

### Dismiss Alert

```bash
POST /api/v1/alerts/{id}/dismiss
```

**Example:**
```bash
curl -X POST "https://your-domain.com/api/v1/alerts/alert_1234567890_abcdef/dismiss" \
  -H "Authorization: Bearer your-token"
```

### Dismiss All Alerts

```bash
POST /api/v1/alerts/dismiss-all
```

**Example:**
```bash
curl -X POST "https://your-domain.com/api/v1/alerts/dismiss-all" \
  -H "Authorization: Bearer your-token"
```

## 🔍 Filtering & Search

### Get Alerts by Type

```bash
GET /api/v1/alerts/type/{type}
```

**Types:** `success`, `error`, `warning`, `info`

**Example:**
```bash
curl -X GET "https://your-domain.com/api/v1/alerts/type/success" \
  -H "Authorization: Bearer your-token"
```

### Get Alert Statistics

```bash
GET /api/v1/alerts/stats/overview
```

**Example:**
```bash
curl -X GET "https://your-domain.com/api/v1/alerts/stats/overview" \
  -H "Authorization: Bearer your-token"
```

**Response:**
```json
{
  "success": true,
  "message": "Success",
  "data": {
    "statistics": {
      "total_alerts": 150,
      "by_type": {
        "success": 75,
        "error": 25,
        "warning": 30,
        "info": 20
      },
      "by_priority": 10,
      "auto_dismiss": 50,
      "expired": 5
    },
    "storage_driver": "database",
    "timestamp": "2024-01-01T00:00:00.000000Z"
  },
  "meta": {
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "status": 200
  }
}
```

### Get Alert History

```bash
GET /api/v1/alerts/history/audit
```

**Query Parameters:**
- `limit` - Number of history items (default: 50)

**Example:**
```bash
curl -X GET "https://your-domain.com/api/v1/alerts/history/audit?limit=100" \
  -H "Authorization: Bearer your-token"
```

## 📦 Bulk Operations

### Create Multiple Alerts

```bash
POST /api/v1/alerts/bulk/create
```

**Request Body:**
```json
{
  "alerts": [
    {
      "type": "success",
      "message": "First alert",
      "title": "Success"
    },
    {
      "type": "info",
      "message": "Second alert",
      "title": "Info"
    }
  ]
}
```

**Example:**
```bash
curl -X POST "https://your-domain.com/api/v1/alerts/bulk/create" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "alerts": [
      {
        "type": "success",
        "message": "User created successfully!",
        "title": "Success"
      },
      {
        "type": "info",
        "message": "Welcome to our platform!",
        "title": "Welcome"
      }
    ]
  }'
```

### Update Multiple Alerts

```bash
PATCH /api/v1/alerts/bulk/update
```

**Request Body:**
```json
{
  "ids": ["alert_1234567890_abcdef", "alert_0987654321_fedcba"],
  "action": "dismiss",
  "data": {
    "reason": "User dismissed"
  }
}
```

**Actions:** `activate`, `deactivate`, `dismiss`, `mark_read`, `mark_unread`, `delete`

**Example:**
```bash
curl -X PATCH "https://your-domain.com/api/v1/alerts/bulk/update" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "ids": ["alert_1234567890_abcdef"],
    "action": "dismiss"
  }'
```

## 🔒 Authentication & Authorization

### API Key Authentication

```bash
curl -X GET "https://your-domain.com/api/v1/alerts" \
  -H "X-API-Key: your-api-key"
```

### Bearer Token Authentication

```bash
curl -X GET "https://your-domain.com/api/v1/alerts" \
  -H "Authorization: Bearer your-token"
```

### Custom Token Authentication

```bash
curl -X GET "https://your-domain.com/api/v1/alerts" \
  -H "X-API-Token: your-custom-token"
```

## 🚦 Rate Limiting

The API implements rate limiting to prevent abuse:

- **Default Limit**: 60 requests per minute
- **Headers**: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- **Response**: 429 Too Many Requests when limit exceeded

## 🌐 CORS Support

The API supports Cross-Origin Resource Sharing (CORS):

```bash
curl -X OPTIONS "https://your-domain.com/api/v1/alerts" \
  -H "Origin: https://your-frontend.com" \
  -H "Access-Control-Request-Method: GET"
```

## 📊 Health Check

### API Health

```bash
GET /api/v1/alerts/health
```

**Example:**
```bash
curl -X GET "https://your-domain.com/api/v1/alerts/health"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "healthy",
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "version": "1.0.0",
    "uptime": 3600
  }
}
```

## 📚 API Documentation

### Get API Documentation

```bash
GET /api/v1/alerts/docs
```

**Example:**
```bash
curl -X GET "https://your-domain.com/api/v1/alerts/docs"
```

## 🔧 Configuration

### API Configuration

```php
// config/laravel-alert.php
'api' => [
    'enabled' => true,
    'auth_method' => 'token', // token, api_key, oauth, jwt, session
    'tokens' => ['your-secret-token'],
    'api_keys' => ['your-api-key'],
    'rate_limiting' => [
        'enabled' => true,
        'max_attempts' => 60,
        'decay_minutes' => 1
    ],
    'cors' => [
        'enabled' => true,
        'allowed_origins' => ['*']
    ]
]
```

## 🧪 Testing

### Test API Endpoints

```bash
# Test health endpoint
curl -X GET "https://your-domain.com/api/v1/alerts/health"

# Test authentication
curl -X GET "https://your-domain.com/api/v1/alerts" \
  -H "Authorization: Bearer your-token"

# Test alert creation
curl -X POST "https://your-domain.com/api/v1/alerts" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{"type": "success", "message": "Test alert"}'
```

## 🚨 Error Handling

### Common Error Codes

- **400** - Bad Request
- **401** - Unauthorized
- **403** - Forbidden
- **404** - Not Found
- **422** - Validation Error
- **429** - Too Many Requests
- **500** - Internal Server Error

### Error Response Format

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": [
    {
      "field": "type",
      "message": "The type field is required.",
      "code": "validation"
    }
  ],
  "meta": {
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "status": 422
  }
}
```

## 📖 Examples

### JavaScript/Fetch

```javascript
// Create alert
const response = await fetch('/api/v1/alerts', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer your-token',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    type: 'success',
    message: 'Operation completed!',
    title: 'Success'
  })
});

const data = await response.json();
console.log(data);
```

### PHP/cURL

```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://your-domain.com/api/v1/alerts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer your-token',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'type' => 'success',
    'message' => 'Operation completed!',
    'title' => 'Success'
]));

$response = curl_exec($ch);
curl_close($ch);
```

---

**Next**: [Authentication Guide](authentication.md)
