<?php
namespace App\Http\Controllers;

use App\Support\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller {
    public function edit(Request $request) {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone,'.Auth::id()],
        ]);

        $validated['phone'] = PhoneNumber::normalize($validated['phone']);

        if (!PhoneNumber::isValid($validated['phone'])) {
            return back()->withErrors(['phone' => __('auth.reset.invalid_phone')])->withInput();
        }

        $user = $request->user();
        $previousPhone = $user->phone;
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($previousPhone !== $user->phone) {
            Log::info('User updated profile phone number.', [
                'actor_user_id' => $user->id,
                'previous_phone_hash' => $previousPhone ? hash('sha256', $previousPhone) : null,
                'new_phone_hash' => $user->phone ? hash('sha256', $user->phone) : null,
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request) {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.confirmed' => 'The new password confirmation does not match.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('profile.edit')->with('password_success', 'Password changed successfully.');
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'The provided password is incorrect.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'userDeletion');
        }

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
