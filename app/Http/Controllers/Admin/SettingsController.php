<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display the admin settings page.
     */
    public function index()
    {
        $settings = [
            'default_currency' => SystemSetting::getValue('default_currency', 'USD'),
            'tzs_exchange_rate' => SystemSetting::getValue('tzs_exchange_rate', 2500),
        ];

        $currencies = CurrencyHelper::getCurrencyOptions();

        return view('admin.settings.index', compact('settings', 'currencies'));
    }

    /**
     * Update system settings (currency, etc.).
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'default_currency' => ['required', 'string', 'in:USD,TZS'],
            'tzs_exchange_rate' => ['required', 'numeric', 'min:1'],
        ]);

        $userId = Auth::id();

        SystemSetting::setValue(
            'default_currency',
            $validated['default_currency'],
            'System-wide default currency (USD or TZS)',
            $userId
        );

        SystemSetting::setValue(
            'tzs_exchange_rate',
            $validated['tzs_exchange_rate'],
            'Current TZS per 1 USD — update daily',
            $userId
        );

        // Clear currency cache so changes reflect immediately
        CurrencyHelper::clearCache();

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.confirmed' => 'The new password confirmation does not match.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('password_success', 'Password changed successfully.');
    }
}
