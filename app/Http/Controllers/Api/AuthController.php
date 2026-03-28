<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // ১. ইনপুট ভ্যালিডেশন
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'password_confirmation' ফিল্ডের সাথে মেলাবে
        ]);

        // ২. নতুন ইউজার তৈরি
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // পাসওয়ার্ড হ্যাশ করা হচ্ছে
        ]);

        // ৩. ইউজারের জন্য একটি টোকেন তৈরি
        $token = $user->createToken('auth_token')->plainTextToken;

        // ৪. সফল রেসপন্স পাঠানো
        return response()->json([
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201); // 201 Created স্ট্যাটাস কোড
    }

    /**
     * Handle user login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // ১. ইনপুট ভ্যালিডেশন
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // ২. ইউজারকে অথেন্টিকেট করার চেষ্টা
        if (!Auth::attempt($request->only('email', 'password'))) {
            // যদি ব্যর্থ হয়, তাহলে এরর রেসপন্স
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        // ৩. সফল হলে, ইউজার অবজেক্ট খুঁজে বের করা
        $user = User::where('email', $request['email'])->firstOrFail();

        // ৪. পুরোনো টোকেন মুছে ফেলে নতুন টোকেন তৈরি (ঐচ্ছিক কিন্তু ভালো অভ্যাস)
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        // ৫. সফল রেসপন্স পাঠানো
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Handle user logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // বর্তমান অথেন্টিকেটেড ইউজারের টোকেনটি মুছে ফেলা
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Send password reset link to the given email.
     */
    public function forgot(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], 200);
        }

        return response()->json(['message' => __($status)], 400);
    }

    /**
     * Reset the user's password using the provided token.
     * Expects: token, email, password, password_confirmation
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                // Revoke all existing tokens for security (optional but recommended)
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Optionally create a new token to auto-login after reset
            $user = User::where('email', $request->email)->first();
            $token = null;
            if ($user) {
                $token = $user->createToken('auth_token')->plainTextToken;
            }

            return response()->json([
                'message' => __($status),
                'access_token' => $token,
                'token_type' => $token ? 'Bearer' : null,
                'user' => $user,
            ], 200);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
