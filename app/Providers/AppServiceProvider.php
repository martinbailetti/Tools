<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\FontHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Registrar directiva Blade para procesar fuentes
        Blade::directive('processFonts', function ($expression) {
            return "<?php echo App\Helpers\FontHelper::addFontFamilyStyles($expression); ?>";
        });

        // Directiva específica para PDFs
        Blade::directive('processFontsForPdf', function ($expression) {
            return "<?php echo App\Helpers\FontHelper::addFontFamilyStylesForPdf($expression); ?>";
        });
    }
}
