<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::findOrFail($request->route('id'));

        // Validasi hash email
        if (! hash_equals(
            sha1($user->getEmailForVerification()),
            $request->route('hash')
        )) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
