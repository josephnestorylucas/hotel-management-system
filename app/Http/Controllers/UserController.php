<?php
namespace App\Http\Controllers;

use App\Support\PhoneNumber;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller {
    public function index() {
        $users = User::with('role')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create() {
        $roles = Role::whereNotIn('name', [Role::ADMIN])->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(10)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role_id' => [
                'required',
                'uuid',
                \Illuminate\Validation\Rule::exists('roles', 'id')->whereNotIn('name', [Role::ADMIN]),
            ],
            'is_active' => 'boolean',
        ]);

        $validated['phone'] = PhoneNumber::normalize($validated['phone']);

        if (!PhoneNumber::isValid($validated['phone'])) {
            return back()->withErrors(['phone' => __('auth.reset.invalid_phone')])->withInput();
        }

        $validated['password'] = Hash::make($validated['password']);
        
        $user = new User();
        $user->fill($validated);
        $user->role_id = $validated['role_id'];  // Explicitly set (not mass-assignable)
        $user->is_active = $validated['is_active'] ?? true;  // Explicitly set
        $user->save();

        Log::info('Admin created user with phone number.', [
            'actor_user_id' => auth()->id(),
            'created_user_id' => $user->id,
            'phone_hash' => hash('sha256', (string) $user->phone),
        ]);
        
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user) {
        $roles = Role::whereNotIn('name', [Role::ADMIN])->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id.'|max:255',
            'phone' => 'required|string|max:30|unique:users,phone,'.$user->id,
            'password' => [
                'nullable',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(10)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role_id' => [
                'required',
                'uuid',
                \Illuminate\Validation\Rule::exists('roles', 'id')->whereNotIn('name', [Role::ADMIN]),
            ],
            'is_active' => 'boolean',
        ]);

        $validated['phone'] = PhoneNumber::normalize($validated['phone']);

        if (!PhoneNumber::isValid($validated['phone'])) {
            return back()->withErrors(['phone' => __('auth.reset.invalid_phone')])->withInput();
        }

        $previousPhone = $user->phone;

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Separate guarded fields from mass-assignable fields
        $roleId = $validated['role_id'];
        $isActive = $validated['is_active'] ?? $user->is_active;
        unset($validated['role_id'], $validated['is_active']);

        $user->fill($validated);
        $user->role_id = $roleId;  // Explicitly set
        $user->is_active = $isActive;  // Explicitly set
        $user->save();

        if ($previousPhone !== $user->phone) {
            Log::info('Admin updated user phone number.', [
                'actor_user_id' => auth()->id(),
                'target_user_id' => $user->id,
                'previous_phone_hash' => $previousPhone ? hash('sha256', $previousPhone) : null,
                'new_phone_hash' => $user->phone ? hash('sha256', $user->phone) : null,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user) {
        $this->softDelete($user);
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function archived()
    {
        $users = User::onlyDeleted()->with('role')->latest('deleted_at')->paginate(20);

        return view('users.archived', compact('users'));
    }

    public function restore(User $user)
    {
        $this->restoreModel($user);

        return redirect()->route('users.index')->with('success', 'User restored successfully.');
    }
}
