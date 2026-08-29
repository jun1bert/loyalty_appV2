# Services API Sync

Use API sync when the hosting plan allows only one database or cannot provide a read-only database user for the external services database.

## Loyalty App `.env`

```env
SERVICES_SYNC_URL=https://external-app.example.com/api/loyalty/services
SERVICES_SYNC_TOKEN=replace-with-a-long-random-secret
SERVICES_SYNC_TIMEOUT=20
SERVICES_AUTO_SYNC_ENABLED=true
```

When `SERVICES_SYNC_URL` is set, `php artisan services:sync-external` reads from the API instead of `SERVICES_DB_*`.

## External Laravel App

Add this route to the external services app:

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Service;

Route::get('/loyalty/services', function (Request $request) {
    abort_unless(
        hash_equals(
            (string) config('services.loyalty_sync.token'),
            (string) $request->bearerToken()
        ),
        403
    );

    return Service::query()
        ->select([
            'name',
            'price',
            'is_active',
            'session_count',
        ])
        ->orderBy('name')
        ->get();
});
```

Add this to `config/services.php` in the external app:

```php
'loyalty_sync' => [
    'token' => env('LOYALTY_SYNC_TOKEN'),
],
```

Add this to the external app `.env`:

```env
LOYALTY_SYNC_TOKEN=replace-with-the-same-long-random-secret
```

## Test

From the loyalty app:

```bash
php artisan optimize:clear
php artisan services:sync-external --dry-run
php artisan services:sync-external
```
