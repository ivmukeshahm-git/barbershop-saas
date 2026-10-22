<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\CurrentShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        $shop = CurrentShop::get();

        return view('admin.auth.login', compact('shop'));
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Enter your email address or contact number.',
            'password.required' => 'Enter your password.',
        ]);

        $shop = CurrentShop::require();
        $login = $request->input('email');
        $password = $request->input('password');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'contact_no';
        $user = User::where($field, $login)->first();

        if ($user && Hash::check($password, $user->password)) {
            if ($user->active_status != 1) {
                return back()->withErrors(['email' => 'This account is deactivated. Please reach out to the support team.'])->withInput();
            }

            if (empty($user->is_platform_admin) && (int) $user->shop_id !== (int) $shop->id) {
                return back()->withErrors(['email' => 'This account does not belong to this shop.'])->withInput();
            }

            if ($shop->status === 'suspended' || $shop->status === 'cancelled') {
                return back()->withErrors(['email' => 'This shop account is not active. Please contact support.'])->withInput();
            }

            Auth::login($user, $request->boolean('remember'));

            $shop->syncExpiredSubscriptions();

            if (!$shop->hasActiveSubscription()) {
                return redirect()->route('admin.plan.expired', ['shop' => $shop->slug]);
            }

            return redirect()
                ->route($user->preferredAdminHomeRoute(), ['shop' => $shop->slug])
                ->with('success', 'You have successfully logged in.');
        }

        return back()->withErrors(['email' => 'Invalid email/contact number or password.'])->withInput();
    }

    public function RoleAssignSuperAdmin()
    {
        $user = User::findOrFail(2);
        $role = Role::find(4);
        $user->assignRole($role->name);
    }

    public function logout(Request $request)
    {
        $shop = CurrentShop::get();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($shop) {
            return redirect()
                ->route('login', ['shop' => $shop->slug])
                ->with('success', 'You have been logged out.');
        }

        return redirect()->route('saas.find-shop')->with('success', 'You have been logged out.');
    }
}
