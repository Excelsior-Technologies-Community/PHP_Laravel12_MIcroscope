# PHP_Laravel12_MIcroscope

---

## 1. Create New Laravel Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Microscope
cd PHP_Laravel12_Microscope
```

### .env

```env
MICROSCOPE_ENABLED=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

## 2. Install Laravel Microscope

```bash
composer require imanghafoori/laravel-microscope --dev
```

### Publish config

```bash
php artisan vendor:publish --provider="Imanghafoori\LaravelMicroscope\LaravelMicroscopeServiceProvider"
```

Now file created:

```
config/microscope.php
```

---

## 3. Basic Clean Route Setup

Open:

```
routes/web.php
```

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Microscope Working';
});
```

Run first scan:

```bash
php artisan check:all
```

---

## 4. Create Demo Controller (For Testing)

```bash
php artisan make:controller DemoController
```

Open:

```
app/Http/Controllers/DemoController.php
```

```php
<?php

namespace App\Http\Controllers;

class DemoController extends Controller
{
    public function index()
    {
        $name = "Harry";

        if ($name) {
            return "Hello";
        }
            return "World";
        

        return $undefinedVariable;
    }
}
```

---

## 5. Connect Controller to Route

Open:

```
routes/web.php
```

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;

Route::get('/', [DemoController::class, 'index']);
```

---

## 6. Check Unused Imports

Run:

```bash
php artisan check:imports
```

It will show:

* Extra import: User
* Unused import: Product

Fix manually:

Remove:

```php
use App\Models\User;
use App\Models\Product;
```

Run again:

```bash
php artisan check:imports
```
<img width="547" height="461" alt="Screenshot 2026-02-17 121319" src="https://github.com/user-attachments/assets/cbbcd7a2-542b-4219-814d-215ca82330a1" />

---

## 7. Refactor Nested Logic (Early Returns)

Run:

```bash
php artisan check:early_returns
```
<img width="596" height="438" alt="Screenshot 2026-02-17 121439" src="https://github.com/user-attachments/assets/08f212cd-853e-4573-b503-89edb16aa67d" />


Type:

```
yes
```

It converts:

```php
if ($name) {
    return "Hello";
} else {
    return "World";
}
```

Into:

```php
if (! $name) {
    return "World";
}

return "Hello";
```

---

## 8. Detect Dead Controller Methods

Add this inside controller:

```php
public function unusedMethod() 
{ 
    return "Not used"; 
}
```

Run:

```bash
php artisan check:dead_controllers
```
<img width="762" height="179" alt="Screenshot 2026-02-17 121608" src="https://github.com/user-attachments/assets/d649752e-58d3-45ab-b6a3-bd3d6c8bbdf0" />


It detects unused public methods.

---

## 9. Route Validation Demo

Break route intentionally:

```php
Route::get('/', [DemoController::class, 'wrongMethod']);
```

Run:

```bash
php artisan check:routes
```
<img width="624" height="314" alt="Screenshot 2026-02-17 125439" src="https://github.com/user-attachments/assets/897d06c8-8949-4823-9bf6-6df5b75eba59" />


It reports:

* Method does not exist

Fix back to:

```php
Route::get('/', [DemoController::class, 'index']);
```

Run again:

```bash
php artisan check:routes
```
<img width="567" height="319" alt="Screenshot 2026-02-17 122051" src="https://github.com/user-attachments/assets/44392390-a90d-4481-b666-eb1332ef1ed3" />

---

## 10. Detect Bad Practices

Add inside controller:

```php
public function index()
{
    $value = env('APP_NAME');   

    return "Hello";
}
```

Run:

```bash
php artisan check:bad_practices
```
<img width="595" height="346" alt="Screenshot 2026-02-17 130049" src="https://github.com/user-attachments/assets/87a12ca6-0b74-4d9a-a10a-1109746c9e4e" />


It reports:

```
env() used outside config file.
```

Fix properly:

```php
public function index()
{
    $value = config('app.name');   

    return "Hello";
}
```

Run again:

```bash
php artisan check:bad_practices
```
<img width="580" height="293" alt="Screenshot 2026-02-17 130218" src="https://github.com/user-attachments/assets/38583f9b-87c9-45aa-b367-a764d3064dc6" />

---

## 11. Remove Debug Statements Automatically

Add inside controller:

```php
public function index()
{
    dd("Testing Microscope"); 

    return "Hello";
}
```

Create `search_replace.php`

Run:

```bash
php artisan search_replace
```

Open file:

```
search_replace.php
```

```php
<?php

return [

    'remove_dd' => [
        'search' => "'<global_func_call:dd,dump>'('<in_between>');",
        'replace' => '',
    ],

];
```

Run:

```bash
php artisan search_replace --name=remove_dd
```
<img width="678" height="400" alt="Screenshot 2026-02-17 122016" src="https://github.com/user-attachments/assets/a3e4b36a-25cf-416d-8ce8-9ed0e8c2dedd" />


It will detect:

Replacing:

```php
dd("Testing Microscope");
```

Type:

```
yes
```

Debug line removed automatically.

---

## 12. Enforce Imports

If you write:

```php
public function index()
{
    $user = new \App\Models\User();   

    return "Hello";
}
```

Run:

```bash
php artisan enforce:imports
```
<img width="665" height="248" alt="Screenshot 2026-02-17 131934" src="https://github.com/user-attachments/assets/d8ee4ee2-705a-4fe1-9403-0628bf4abb95" />


It converts to:

```php
use App\Models\User;

public function index()
{
    $user = new User();  

    return "Hello";
}
```

---

## 13. Final Full Clean Scan

Run:

```bash
php artisan check:all
```
<img width="526" height="804" alt="Screenshot 2026-02-17 122613" src="https://github.com/user-attachments/assets/d58674d4-56b9-46a9-8cb3-daddd497768e" />

<img width="535" height="697" alt="Screenshot 2026-02-17 122648" src="https://github.com/user-attachments/assets/8b849eca-d1a8-4eb0-b95e-b6fd90ef3fbc" />

<img width="749" height="467" alt="Screenshot 2026-02-17 122702" src="https://github.com/user-attachments/assets/37987d46-b18b-45ab-8685-ffb092a266dc" />


You should see:

```
0 wrong imports found
All view() calls are correct
No dead controller action found
No env() misuse found
```

---

## 14. Recommended Daily Workflow

### During Development

```bash
php artisan check:imports
php artisan check:routes
php artisan check:views
```
<img width="542" height="289" alt="Screenshot 2026-02-17 132649" src="https://github.com/user-attachments/assets/d2950138-40a4-4c3e-b60e-958918c3ec7e" />


### Before Commit

```bash
php artisan check:all
```
