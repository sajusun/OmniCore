<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make {name : The StudlyCase name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a new plug-and-play dashboard module with standard enterprise structure';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rawName = (string) $this->argument('name');
        $module = Str::studly($rawName);
        $kebab = Str::kebab($module);
        $snake = Str::snake($module);

        $modulePath = app_path("Modules/{$module}");

        if (File::isDirectory($modulePath)) {
            $this->error("Module [{$module}] already exists at {$modulePath}!");
            return Command::FAILURE;
        }

        $this->info("Creating module: [{$module}]...");

        // Directories to create
        $dirs = [
            "{$modulePath}/Database/Migrations",
            "{$modulePath}/Events",
            "{$modulePath}/Http/Controllers/Api",
            "{$modulePath}/Http/Controllers/Web",
            "{$modulePath}/Models",
            "{$modulePath}/Providers",
            "{$modulePath}/Routes",
            "{$modulePath}/Services",
        ];

        foreach ($dirs as $dir) {
            File::makeDirectory($dir, 0755, true);
        }

        // 1. Service Provider
        $providerCode = <<<PHP
<?php

declare(strict_types=1);

namespace App\Modules\\{$module}\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class {$module}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repositories or services
    }

    public function boot(): void
    {
        // 1. Load Migrations
        if (is_dir(__DIR__ . '/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        }

        // 2. Load API Routes
        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            Route::prefix('api/v1/{$kebab}')
                ->middleware('api')
                ->group(__DIR__ . '/../Routes/api.php');
        }

        // 3. Load Admin Routes
        if (file_exists(__DIR__ . '/../Routes/admin.php')) {
            Route::prefix('admin/{$kebab}')
                ->name('admin.{$kebab}.')
                ->middleware(['web', 'auth'])
                ->group(__DIR__ . '/../Routes/admin.php');
        }
    }
}
PHP;
        File::put("{$modulePath}/Providers/{$module}ServiceProvider.php", $providerCode);

        // 2. Model
        $modelCode = <<<PHP
<?php

declare(strict_types=1);

namespace App\Modules\\{$module}\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$module} extends Model
{
    use HasFactory;

    protected \$guarded = ['id'];
}
PHP;
        File::put("{$modulePath}/Models/{$module}.php", $modelCode);

        // 3. Service
        $serviceCode = <<<PHP
<?php

declare(strict_types=1);

namespace App\Modules\\{$module}\Services;

use App\Modules\\{$module}\Models\\{$module};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class {$module}Service
{
    public function paginate(int \$perPage = 15): LengthAwarePaginator
    {
        return {$module}::query()->latest()->paginate(\$perPage);
    }

    public function create(array \$data): {$module}
    {
        return {$module}::create(\$data);
    }
}
PHP;
        File::put("{$modulePath}/Services/{$module}Service.php", $serviceCode);

        // 4. API Controller
        $controllerCode = <<<PHP
<?php

declare(strict_types=1);

namespace App\Modules\\{$module}\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\\{$module}\Services\\{$module}Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class {$module}ApiController extends Controller
{
    public function __construct(
        protected {$module}Service \$service
    ) {}

    public function index(): JsonResponse
    {
        \$data = \$this->service->paginate();

        return response()->json([
            'status' => true,
            'code'   => 200,
            'data'   => \$data,
        ]);
    }
}
PHP;
        File::put("{$modulePath}/Http/Controllers/Api/{$module}ApiController.php", $controllerCode);

        // 5. Routes
        $apiRouteCode = <<<PHP
<?php

declare(strict_types=1);

use App\Modules\\{$module}\Http\Controllers\Api\\{$module}ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [{$module}ApiController::class, 'index']);
PHP;
        File::put("{$modulePath}/Routes/api.php", $apiRouteCode);

        $adminRouteCode = <<<PHP
<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Define admin routes for {$module}
PHP;
        File::put("{$modulePath}/Routes/admin.php", $adminRouteCode);

        // 6. USAGE.md
        $usageCode = <<<MD
# {$module} Module (`{$module}`)

Plug-and-play {$module} module for OmniCore.

---

## 🎯 1. Use Cases
- Standard business operations for {$module}.

---

## 📋 2. Requirements & Prerequisites
- PHP 8.2+, Laravel 11+
- App\Models\User model.

---

## 🗄️ 3. Database Tables Created
- `{$snake}s` table.

---

## 🛣️ 4. Key Routes
- API: `GET /api/v1/{$kebab}` - Index listing.

---

## ⚡ 5. Events & Decoupled Architecture
- Dispatches: None yet.
- Listens: None.

---

## 🚀 6. Plug & Play Installation Guide
1. Copy `app/Modules/{$module}` to `app/Modules/`.
2. Register `App\Modules\\{$module}\Providers\\{$module}ServiceProvider::class` in `bootstrap/providers.php`.
3. Run `php artisan migrate`.
MD;
        File::put("{$modulePath}/USAGE.md", $usageCode);

        $this->newLine();
        $this->info("✓ Module [{$module}] scaffolded successfully!");
        $this->line("• Provider: <comment>app/Modules/{$module}/Providers/{$module}ServiceProvider.php</comment>");
        $this->line("• Service:  <comment>app/Modules/{$module}/Services/{$module}Service.php</comment>");
        $this->line("• Docs:     <comment>app/Modules/{$module}/USAGE.md</comment>");
        $this->newLine();
        $this->line("To activate, register the ServiceProvider in <info>bootstrap/providers.php</info> and run <info>php artisan module:list</info>.");

        return Command::SUCCESS;
    }
}
