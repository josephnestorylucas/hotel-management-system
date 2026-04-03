<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use App\Models\Reservation;
use App\Models\Booking;
use App\Observers\ReservationObserver;
use App\Observers\BookingObserver;
use App\Helpers\CurrencyHelper;

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
        Schema::defaultStringLength(191);
        Reservation::observe(ReservationObserver::class);
        Booking::observe(BookingObserver::class);

        // Register Blade directives for currency formatting
        Blade::directive('currency', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::formatCurrency($expression); ?>";
        });

        Blade::directive('currencySymbol', function ($expression) {
            $expression = $expression ?: 'null';
            return "<?php echo \App\Helpers\CurrencyHelper::getCurrencySymbol($expression); ?>";
        });

        // Share currency data with all views
        view()->composer('*', function ($view) {
            $view->with('systemCurrency', CurrencyHelper::getDefaultCurrency());
            $view->with('currencySymbol', CurrencyHelper::getCurrencySymbol());
            $view->with('exchangeRate', CurrencyHelper::getExchangeRate());
        });
    }
}
