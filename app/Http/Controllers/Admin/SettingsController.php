<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
            'sms_provider_key' => SystemSetting::getValue('sms_provider_key', ''),
            'sms_sender_id' => SystemSetting::getValue('sms_sender_id', ''),
            'sms_base_url' => SystemSetting::getValue('sms_base_url', ''),
            'sms_is_enabled' => filter_var(SystemSetting::getValue('sms_is_enabled', 'false'), FILTER_VALIDATE_BOOLEAN),
            'sms_api_key_set' => !empty(SystemSetting::getValue('sms_api_key')),
            'mail_driver' => SystemSetting::getValue('mail_driver', 'smtp'),
            'mail_host' => SystemSetting::getValue('mail_host', ''),
            'mail_port' => SystemSetting::getValue('mail_port', '587'),
            'mail_username' => SystemSetting::getValue('mail_username', ''),
            'mail_encryption' => SystemSetting::getValue('mail_encryption', 'tls'),
            'mail_from_address' => SystemSetting::getValue('mail_from_address', ''),
            'mail_from_name' => SystemSetting::getValue('mail_from_name', ''),
            'mail_is_enabled' => filter_var(SystemSetting::getValue('mail_is_enabled', 'false'), FILTER_VALIDATE_BOOLEAN),
            'mail_password_set' => !empty(SystemSetting::getValue('mail_password')),
            'azampesa_base_url' => SystemSetting::getValue('azampesa_base_url', ''),
            'azampesa_is_enabled' => filter_var(SystemSetting::getValue('azampesa_is_enabled', 'false'), FILTER_VALIDATE_BOOLEAN),
            'azampesa_app_name_set' => !empty(SystemSetting::getValue('azampesa_app_name')),
            'azampesa_client_id_set' => !empty(SystemSetting::getValue('azampesa_client_id')),
            'azampesa_client_secret_set' => !empty(SystemSetting::getValue('azampesa_client_secret')),
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
     * Update SMS provider settings.
     */
    public function updateSmsSettings(Request $request)
    {
        $hasSmsApiKey = !empty(SystemSetting::getValue('sms_api_key', ''));
        $hasSmsProviderKey = !empty(SystemSetting::getValue('sms_provider_key', ''));

        $validated = $request->validate([
            'sms_provider_key' => [
                'nullable',
                'string',
                'max:100',
                Rule::requiredIf(fn () => $request->boolean('sms_is_enabled') && !$hasSmsProviderKey),
            ],
            'sms_sender_id' => ['nullable', 'string', 'max:100'],
            'sms_api_key' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => $request->boolean('sms_is_enabled') && !$hasSmsApiKey),
            ],
            'sms_base_url' => ['nullable', 'url'],
            'sms_is_enabled' => ['nullable', 'boolean'],
        ]);

        $userId = Auth::id();
        $isEnabled = $request->boolean('sms_is_enabled');

        SystemSetting::setValue('sms_provider_key', $validated['sms_provider_key'] ?? '', 'SMS provider key', $userId);
        SystemSetting::setValue('sms_sender_id', $validated['sms_sender_id'] ?? '', 'SMS sender ID', $userId);
        SystemSetting::setValue('sms_base_url', $validated['sms_base_url'] ?? '', 'SMS provider base URL', $userId);
        SystemSetting::setValue('sms_is_enabled', $isEnabled ? 'true' : 'false', 'SMS provider enabled flag', $userId);

        if (!empty($validated['sms_api_key'])) {
            SystemSetting::setValue('sms_api_key', $validated['sms_api_key'], 'SMS provider API key', $userId);
        }

        return back()->with('sms_success', 'SMS settings updated successfully.');
    }

    /**
     * Update email configuration settings.
     */
    public function updateEmailSettings(Request $request)
    {
        $hasMailPassword = !empty(SystemSetting::getValue('mail_password', ''));

        $validated = $request->validate([
            'mail_driver' => ['nullable', 'string', 'max:50', 'required_if:mail_is_enabled,1'],
            'mail_host' => ['nullable', 'string', 'max:191', 'required_if:mail_is_enabled,1'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535', 'required_if:mail_is_enabled,1'],
            'mail_username' => ['nullable', 'string', 'max:191'],
            'mail_password' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => $request->boolean('mail_is_enabled') && !$hasMailPassword),
            ],
            'mail_encryption' => ['nullable', 'string', 'max:10', Rule::in(['tls', 'ssl', 'none'])],
            'mail_from_address' => ['nullable', 'email', 'max:191', 'required_if:mail_is_enabled,1'],
            'mail_from_name' => ['nullable', 'string', 'max:191'],
            'mail_is_enabled' => ['nullable', 'boolean'],
        ]);

        $userId = Auth::id();
        $isEnabled = $request->boolean('mail_is_enabled');

        SystemSetting::setValue('mail_driver', $validated['mail_driver'] ?? 'smtp', 'Mail driver', $userId);
        SystemSetting::setValue('mail_host', $validated['mail_host'] ?? '', 'Mail host', $userId);
        SystemSetting::setValue('mail_port', (string) ($validated['mail_port'] ?? ''), 'Mail port', $userId);
        SystemSetting::setValue('mail_username', $validated['mail_username'] ?? '', 'Mail username', $userId);
        SystemSetting::setValue('mail_encryption', $validated['mail_encryption'] ?? 'tls', 'Mail encryption', $userId);
        SystemSetting::setValue('mail_from_address', $validated['mail_from_address'] ?? '', 'Mail from address', $userId);
        SystemSetting::setValue('mail_from_name', $validated['mail_from_name'] ?? '', 'Mail from name', $userId);
        SystemSetting::setValue('mail_is_enabled', $isEnabled ? 'true' : 'false', 'Mail enabled flag', $userId);

        if (!empty($validated['mail_password'])) {
            SystemSetting::setValue('mail_password', $validated['mail_password'], 'Mail password', $userId);
        }

        return back()->with('email_success', 'Email settings updated successfully.');
    }

    /**
     * Update AzamPesa payment settings.
     */
    public function updateAzamPesaSettings(Request $request)
    {
        $hasAppName = !empty(SystemSetting::getValue('azampesa_app_name', ''));
        $hasClientId = !empty(SystemSetting::getValue('azampesa_client_id', ''));

        $validated = $request->validate([
            'azampesa_is_enabled' => ['nullable', 'boolean'],
            'azampesa_app_name' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => $request->boolean('azampesa_is_enabled') && !$hasAppName),
            ],
            'azampesa_client_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf(fn () => $request->boolean('azampesa_is_enabled') && !$hasClientId),
            ],
            'azampesa_client_secret' => ['nullable', 'string', 'max:255'],
            'azampesa_base_url' => ['nullable', 'url'],
            'azampesa_auth_url' => ['nullable', 'url'],
        ]);

        $userId = Auth::id();
        $isEnabled = $request->boolean('azampesa_is_enabled');

        SystemSetting::setValue('azampesa_base_url', $validated['azampesa_base_url'] ?? '', 'AzamPesa base URL', $userId);
        SystemSetting::setValue('azampesa_auth_url', $validated['azampesa_auth_url'] ?? '', 'AzamPesa auth URL', $userId);
        SystemSetting::setValue('azampesa_is_enabled', $isEnabled ? 'true' : 'false', 'AzamPesa payment enabled flag', $userId);

        if (!empty($validated['azampesa_app_name'])) {
            SystemSetting::setValue('azampesa_app_name', $validated['azampesa_app_name'], 'AzamPesa app name', $userId);
        }

        if (!empty($validated['azampesa_client_id'])) {
            SystemSetting::setValue('azampesa_client_id', $validated['azampesa_client_id'], 'AzamPesa client ID', $userId);
        }

        if (!empty($validated['azampesa_client_secret'])) {
            SystemSetting::setValue('azampesa_client_secret', $validated['azampesa_client_secret'], 'AzamPesa client secret', $userId);
        }

        return back()->with('azampesa_success', 'AzamPesa payment settings updated successfully.');
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
