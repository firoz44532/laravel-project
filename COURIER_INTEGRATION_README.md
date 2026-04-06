# Courier Integration System

A comprehensive courier integration system for Steadfast and Pathao courier services in Laravel. This system allows one-click automatic order entry to courier services with real-time tracking.

## Features

### 🚀 One-Click Integration
- **Single Click Order Entry**: Automatically send orders to Steadfast or Pathao with one click
- **Bulk Integration**: Process multiple orders simultaneously
- **Auto Status Updates**: Order status automatically updates to "Processing" when integrated

### 📦 Supported Couriers
- **Steadfast Courier**: Full API integration with order creation, tracking, and cancellation
- **Pathao Courier**: Complete integration with authentication, city/zone management, and tracking

### 📊 Real-Time Tracking
- **Live Status Updates**: Track orders in real-time from courier APIs
- **Automatic Sync**: Status automatically synced from courier systems
- **Error Handling**: Comprehensive error handling with retry functionality

### 🎯 Smart Features
- **Weight Calculation**: Automatic package weight calculation based on product weights
- **COD Support**: Automatic Cash on Delivery amount calculation
- **Package Description**: Smart package description generation from order items
- **Duplicate Prevention**: Prevents duplicate integrations

## Installation

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Steadfast Courier Configuration
STEADFAST_BASE_URL=https://portal.steadfast.com.bd
STEADFAST_API_KEY=your_steadfast_api_key
STEADFAST_SECRET_KEY=your_steadfast_secret_key
STEADFAST_PICKUP_ADDRESS=Your Shop Address, Dhaka

# Pathao Courier Configuration
PATHAO_BASE_URL=https://api-hermes.pathao.com
PATHAO_CLIENT_EMAIL=your_pathao_client_email
PATHAO_CLIENT_PASSWORD=your_pathao_client_password
PATHAO_CLIENT_SECRET=your_pathao_client_secret
```

### 2. Database Migration

Run the migration to create the courier integrations table:

```bash
php artisan migrate
```

### 3. Service Configuration

The courier services are automatically configured through the `config/services.php` file.

## Usage

### Individual Order Integration

1. **From Tracking Page**: 
   - Go to Admin → Orders → Order Tracking
   - Search for orders
   - Click the "Courier" button for any order
   - Select courier service (Steadfast/Pathao)
   - Click "Integrate with Courier"

2. **From Order Details**:
   - Navigate to Admin → Orders → All Orders
   - Click on any order
   - Click "Create Courier Integration"

### Bulk Integration

1. **From Courier Dashboard**:
   - Go to Admin → Orders → Courier Integrations
   - Click "Bulk Integrate"
   - Select courier service
   - Enter order IDs (comma-separated)
   - Click "Integrate Orders"

### Tracking & Management

1. **View Integrations**: 
   - Access from Admin → Orders → Courier Integrations
   - View all integration history with status
   - Filter by courier type and status

2. **Live Tracking**:
   - Click on any integration to see details
   - Real-time tracking information from courier API
   - Order details and package information

3. **Error Handling**:
   - Failed integrations show error messages
   - Retry failed integrations with one click
   - Cancel active integrations when needed

## API Endpoints

### Steadfast Courier API
- **Create Order**: `POST /api/v1/create_order`
- **Track Order**: `GET /api/v1/track_order/{tracking_code}`
- **Cancel Order**: `POST /api/v1/cancel_order`

### Pathao Courier API
- **Authentication**: `POST /v1/issue-token`
- **Get Stores**: `GET /v1/stores`
- **Get Cities**: `GET /v1/cities`
- **Get Zones**: `GET /v1/cities/{city_id}/zone-list`
- **Create Order**: `POST /v1/orders`
- **Track Order**: `GET /v1/orders/track/{tracking_code}`
- **Cancel Order**: `POST /v1/orders/cancel`

## Database Schema

### Courier Integrations Table

```sql
CREATE TABLE courier_integrations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT NOT NULL,
    courier_type ENUM('steadfast', 'pathao') NOT NULL,
    tracking_number VARCHAR(255) NULL,
    consignment_id VARCHAR(255) NULL,
    status ENUM('pending', 'synced', 'failed', 'cancelled', 'delivered') DEFAULT 'pending',
    pickup_address TEXT NULL,
    delivery_address TEXT NULL,
    customer_name VARCHAR(255) NULL,
    customer_phone VARCHAR(255) NULL,
    package_weight DECIMAL(8,2) DEFAULT 0.50,
    package_description TEXT NULL,
    cod_amount DECIMAL(10,2) DEFAULT 0,
    delivery_charge DECIMAL(10,2) DEFAULT 0,
    api_response JSON NULL,
    error_message TEXT NULL,
    synced_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_courier_status (courier_type, status),
    INDEX idx_tracking_number (tracking_number),
    INDEX idx_consignment_id (consignment_id)
);
```

## Models & Relationships

### Order Model
```php
public function courierIntegrations()
{
    return $this->hasMany(CourierIntegration::class);
}
```

### CourierIntegration Model
```php
public function order()
{
    return $this->belongsTo(Order::class);
}

public function getStatusBadgeAttribute()
{
    // Returns HTML badge for status
}

public function getCourierNameAttribute()
{
    // Returns human-readable courier name
}
```

## Controllers & Routes

### Main Controller: `CourierIntegrationController`
- `index()` - List all integrations
- `create($orderId)` - Show integration form
- `store()` - Process integration
- `show($id)` - Show integration details
- `cancel($id)` - Cancel integration
- `retry($id)` - Retry failed integration
- `bulkIntegrate()` - Process multiple orders
- `stats()` - Get integration statistics

### Routes
```php
Route::get('/courier-integrations', [CourierIntegrationController::class, 'index']);
Route::get('/courier-integrations/create/{orderId}', [CourierIntegrationController::class, 'create']);
Route::post('/courier-integrations', [CourierIntegrationController::class, 'store']);
Route::get('/courier-integrations/{integration}', [CourierIntegrationController::class, 'show']);
Route::post('/courier-integrations/{integration}/cancel', [CourierIntegrationController::class, 'cancel']);
Route::post('/courier-integrations/{integration}/retry', [CourierIntegrationController::class, 'retry']);
Route::post('/courier-integrations/bulk-integrate', [CourierIntegrationController::class, 'bulkIntegrate']);
Route::get('/courier-integrations/stats', [CourierIntegrationController::class, 'stats']);
```

## Services

### SteadfastCourierService
- Handles all Steadfast API operations
- Automatic payload preparation
- Error handling and logging
- Order creation, tracking, and cancellation

### PathaoCourierService
- Handles Pathao authentication and API operations
- City and zone management
- Store information retrieval
- Order creation, tracking, and cancellation

## Frontend Features

### Dashboard
- **Statistics Cards**: Total integrations, by courier, by status
- **Integration History**: Complete list with filtering and pagination
- **Quick Actions**: Bulk integration, retry failed orders

### Integration Form
- **Courier Selection**: Visual courier service selection
- **Order Details**: Pre-filled order and customer information
- **Package Info**: Automatic weight and description calculation
- **Validation**: Form validation and error handling

### Tracking Page
- **Live Status**: Real-time tracking from courier APIs
- **Order Details**: Complete order and package information
- **API Response**: Debug information for developers
- **Actions**: Cancel, retry, and refresh options

## Error Handling

### Types of Errors
1. **API Authentication Errors**: Invalid credentials
2. **Validation Errors**: Missing or invalid data
3. **Network Errors**: Connection issues
4. **Courier API Errors**: Service-specific errors

### Error Recovery
- **Automatic Retry**: Failed integrations can be retried
- **Error Logging**: All errors logged with details
- **User Notifications**: Clear error messages to users
- **Rollback**: Database transactions rolled back on failure

## Security Features

- **CSRF Protection**: All forms protected with CSRF tokens
- **Input Validation**: Comprehensive input validation and sanitization
- **API Key Security**: Courier API keys stored securely in environment
- **Rate Limiting**: Built-in rate limiting for API calls
- **Audit Trail**: Complete audit trail of all integrations

## Performance Optimization

- **Database Indexing**: Optimized indexes for fast queries
- **API Caching**: Responses cached where appropriate
- **Bulk Operations**: Efficient bulk processing
- **Lazy Loading**: Relationships loaded only when needed
- **Background Jobs**: Can be extended to use queue system

## Monitoring & Logging

- **Comprehensive Logging**: All API calls and responses logged
- **Error Tracking**: Detailed error tracking and reporting
- **Performance Metrics**: Integration success rates and timing
- **Audit Logs**: Complete audit trail of all actions

## Future Enhancements

### Planned Features
1. **SMS Notifications**: SMS alerts for status changes
2. **Email Templates**: Customizable email notifications
3. **Webhook Support**: Webhook integration for real-time updates
4. **Mobile App**: Mobile app for on-the-go tracking
5. **Analytics Dashboard**: Advanced analytics and reporting
6. **Multi-language Support**: Support for multiple languages

### Additional Couriers
- **eCourier**: Integration with eCourier Bangladesh
- **RedX**: Integration with RedX courier service
- **Paperfly**: Integration with Paperfly courier
- **Custom API**: Support for custom courier APIs

## Support

For issues and support:
1. Check the error logs in `storage/logs/laravel.log`
2. Verify API credentials in `.env` file
3. Ensure courier services are accessible from your server
4. Check database migrations are properly run

## Contributing

When contributing to this courier integration system:
1. Follow Laravel coding standards
2. Add proper error handling
3. Include comprehensive tests
4. Update documentation
5. Test with both courier services

---

**Note**: This system requires valid API credentials from Steadfast and Pathao courier services. Contact the respective courier services to obtain API access.
