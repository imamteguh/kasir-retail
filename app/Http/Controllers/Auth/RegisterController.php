<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8|confirmed',
            'store_name' => 'required|string|max:150',
            'terms'      => 'required'
        ]);

        // Buat user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'owner',
        ]);

        // Buat toko milik user
        $store = Store::create([
            'user_id' => $user->id,
            'name'    => $request->store_name,
            'address' => $request->address,
            'phone'   => $request->phone,
        ]);

        // Buat langganan trial (misal 7 hari)
        $trialDays = 7;
        Subscription::create([
            'store_id'   => $store->id,
            'plan_id'    => null,
            'start_date' => now(),
            'end_date'   => Carbon::now()->addDays($trialDays),
            'status'     => 'active',
        ]);

        // Auto-login
        Auth::login($user);
        $user->sendEmailVerificationNotification();
        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat mencoba versi trial selama 7 hari.');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->route('dashboard')->with('success', 'Email successfully verified!!');
    }
}
