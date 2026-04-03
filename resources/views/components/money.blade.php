{{-- resources/views/components/money.blade.php --}}
{{-- 
    Usage: <x-money :amount="$value" />
    Or:    <x-money :amount="$value" currency="TZS" />
    Or:    <x-money :amount="$value" :show-symbol="false" />
--}}
@props(['amount', 'currency' => null, 'showSymbol' => true])

@php
    use App\Helpers\CurrencyHelper;
    $formattedAmount = CurrencyHelper::formatCurrency($amount, $currency, $showSymbol);
@endphp

<span {{ $attributes }}>{{ $formattedAmount }}</span>
