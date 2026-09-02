<?php

namespace App\Providers;

use Abdal\PhpianRender\PhpianRender;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Reshapes Persian text so dompdf renders joined letters correctly
        // (dompdf does not perform Arabic/Persian glyph shaping on its own).
        // Usage in blade: @fa($expense->vendor?->name)
        Blade::directive('fa', function ($expression) {
            return "<?php echo e(\\Abdal\\PhpianRender\\PhpianRender::reshapeStatic((string) ($expression))); ?>";
        });
    }
}
