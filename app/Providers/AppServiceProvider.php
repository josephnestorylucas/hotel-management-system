<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use App\Models\Reservation;
use App\Models\Booking;
use App\Policies\BookingPolicy;
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
        Gate::policy(Booking::class, BookingPolicy::class);

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

        if (method_exists($this->app['translator'], 'handleMissingKeysUsing')) {
            $this->app['translator']->handleMissingKeysUsing(function (string $key, array $replace = [], ?string $locale = null) {
                $activeLocale = $locale ?: App::currentLocale();
                $fallbackLocale = config('app.fallback_locale', 'en');

                if ($activeLocale === $fallbackLocale) {
                    return $key;
                }

                if ($activeLocale === 'sw') {
                    Log::warning('Missing Swahili translation key', [
                        'key' => $key,
                        'locale' => $activeLocale,
                        'fallback_locale' => $fallbackLocale,
                    ]);
                }

                return $this->app['translator']->get($key, $replace, $fallbackLocale);
            });
        }
    }
}
