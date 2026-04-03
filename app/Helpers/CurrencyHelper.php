<?php

namespace App\Helpers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class CurrencyHelper
{
    /**
     * Supported currencies with their configurations
     */
    public const CURRENCIES = [
        'USD' => [
            'symbol' => '$',
            'name' => 'US Dollar',
            'decimals' => 2,
            'position' => 'before', // symbol position: before or after amount
        ],
        'TZS' => [
            'symbol' => 'TZS',
            'name' => 'Tanzanian Shilling',
            'decimals' => 0,
            'position' => 'after',
        ],
    ];

    /**
     * Get the system's default currency code.
     */
    public static function getDefaultCurrency(): string
    {
        return Cache::remember('system_currency', 60, function () {
            return SystemSetting::getValue('default_currency', 'USD');
        });
    }

    /**
     * Get the currency symbol for a given currency code.
     */
    public static function getCurrencySymbol(?string $currency = null): string
    {
        $currency = $currency ?? self::getDefaultCurrency();
        return self::CURRENCIES[$currency]['symbol'] ?? $currency;
    }

    /**
     * Get the number of decimal places for a currency.
     */
    public static function getDecimals(?string $currency = null): int
    {
        $currency = $currency ?? self::getDefaultCurrency();
        return self::CURRENCIES[$currency]['decimals'] ?? 2;
    }

    /**
     * Format a monetary amount with the appropriate currency symbol.
     * 
     * @param float|int $amount The amount to format
     * @param string|null $currency The currency code (defaults to system currency)
     * @param bool $showSymbol Whether to show the currency symbol
     * @return string Formatted currency string
     */
    public static function formatCurrency($amount, ?string $currency = null, bool $showSymbol = true): string
    {
        $currency = $currency ?? self::getDefaultCurrency();
        $config = self::CURRENCIES[$currency] ?? self::CURRENCIES['USD'];
        
        $formatted = number_format((float) $amount, $config['decimals']);
        
        if (!$showSymbol) {
            return $formatted;
        }

        if ($config['position'] === 'before') {
            return $config['symbol'] . $formatted;
        }
        
        return $formatted . ' ' . $config['symbol'];
    }

    /**
     * Format amount with explicit USD currency.
     */
    public static function formatUSD($amount, bool $showSymbol = true): string
    {
        return self::formatCurrency($amount, 'USD', $showSymbol);
    }

    /**
     * Format amount with explicit TZS currency.
     */
    public static function formatTZS($amount, bool $showSymbol = true): string
    {
        return self::formatCurrency($amount, 'TZS', $showSymbol);
    }

    /**
     * Get exchange rate from system settings (TZS per 1 USD).
     */
    public static function getExchangeRate(): float
    {
        return (float) Cache::remember('tzs_exchange_rate', 60, function () {
            return SystemSetting::getValue('tzs_exchange_rate', 2500);
        });
    }

    /**
     * Convert amount between currencies.
     * 
     * @param float $amount The amount to convert
     * @param string $from Source currency code
     * @param string $to Target currency code
     * @return float Converted amount
     */
    public static function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $rate = self::getExchangeRate();

        if ($from === 'USD' && $to === 'TZS') {
            return $amount * $rate;
        }

        if ($from === 'TZS' && $to === 'USD') {
            return $amount / $rate;
        }

        return $amount;
    }

    /**
     * Clear the currency cache (call after updating settings).
     */
    public static function clearCache(): void
    {
        Cache::forget('system_currency');
        Cache::forget('tzs_exchange_rate');
    }

    /**
     * Get all available currencies for dropdown selects.
     */
    public static function getCurrencyOptions(): array
    {
        return collect(self::CURRENCIES)->map(function ($config, $code) {
            return [
                'code' => $code,
                'name' => $config['name'],
                'symbol' => $config['symbol'],
                'label' => "{$config['name']} ({$config['symbol']})",
            ];
        })->values()->toArray();
    }
}
