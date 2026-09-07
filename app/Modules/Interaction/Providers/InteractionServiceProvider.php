<?php

namespace App\Modules\Interaction\Providers;

use App\Models\User;
use App\Modules\Interaction\Models\Bookmark;
use App\Modules\Interaction\Models\Comment;
use App\Modules\Interaction\Models\Like;
use App\Modules\Interaction\Models\ShareLink;
use App\Modules\Interaction\Models\View;
use App\Modules\Interaction\Services\BookmarkService;
use App\Modules\Interaction\Services\CommentService;
use App\Modules\Interaction\Services\LikeService;
use App\Modules\Interaction\Services\ShareService;
use App\Modules\Interaction\Services\ViewService;
use App\Modules\Post\Models\Post;
use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class InteractionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CommentService::class, fn () => new CommentService);
        $this->app->singleton(LikeService::class, fn () => new LikeService);
        $this->app->singleton(ShareService::class, fn () => new ShareService);
        $this->app->singleton(ViewService::class, fn () => new ViewService);
        $this->app->singleton(BookmarkService::class, fn () => new BookmarkService);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Load Migrations
        if (is_dir(__DIR__.'/../Database/Migrations')) {
            $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        }

        // 2. Load API Routes
        if (file_exists(__DIR__.'/../Routes/api.php')) {
            Route::prefix('api')
                ->middleware('api')
                ->group(__DIR__.'/../Routes/api.php');
        }

        // 3. Load Web Share Routes
        if (file_exists(__DIR__.'/../Routes/web.php')) {
            Route::middleware('web')
                ->group(__DIR__.'/../Routes/web.php');
        }

        // 4. Load Views
        if (is_dir(__DIR__.'/../Views')) {
            $this->loadViewsFrom(__DIR__.'/../Views', 'interaction');
        }

        // 5. Morph Map Registration for polymorphic relations
        Relation::morphMap([
            'user' => User::class,
            'comment' => Comment::class,
            'like' => Like::class,
            'share_link' => ShareLink::class,
            'view' => View::class,
            'bookmark' => Bookmark::class,
            'post' => Post::class,
            'product' => Product::class,
        ]);
    }
}
