<?php

namespace App\Services;

use Illuminate\Support\Str;

class Verification
{
    public function __construct()
    {
        //
    }

    /**
     * Todo: Generate account verification code.
     * Pass user Id
     * @param int $userId
     *
     * return string
     */
    public function generateVerificationCode(int $user_id, ?string $token = null, string $token_name = 'usertoken'): string
    {
        // Token
        $token = (!is_null($token) && !empty($token)) ? $token : Str::random(60);

        // Save in database, table `verifications`
        \App\Models\UserToken::create([
            'user' => $user_id,
            'name' => strtolower(trim($token_name)),
            'token' => $token,
            'expires_at' => now()->addMinutes(60),
        ]);

        // Save user id in session for 65 minutes
        session(['token_user' => $user_id]);
        // Set the OTP to expire after 65 minutes
        session(['token_expiry' => now()->addMinutes(65)]);

        // Token
        return $token;
    }

    /**
     * Todo: Verify account verification code.
     * Pass token
     * @param string $token
     *
     */
    public function verifyVerificationCode(string $token, ?string $token_name = null, ?string $user_id = null)
    {
        // Get user id from session
        $user_id = session('token_user') ?? $user_id;

        // Expiry
        if (!is_null(session('token_expiry'))) {
            // Check if token has expired
            if (session('token_expiry') < now()) {
                return false;
            }
        }

        // Get token from database
        $query = \App\Models\UserToken::where('token', $token)
            ->where('expires_at', '>', now());

        // User
        if ($user_id) {
            $query->where('user', $user_id);
        }

        // Token
        if (!is_null($token_name) && !empty($token_name)) {
            $query->where('name', $token_name);
        }

        // Query
        $token = $query->first();

        // If token is not found
        if (!$token) {
            return false;
        }

        // Delete token from database
        $token->delete();

        // Remove user id from session
        session()->forget('token_user');
        session()->forget('token_expiry');

        return $token;
    }

    /**
     * Todo: Generate Random Numbers
     * Pass lenght
     * Pass limit (by default is 9)
     * @param integer $length
     * @param integer $maxDigit (Default 9)
     *
     */
    public function generateRandomNumbers(int $length, int $maxDigit = 9): int
    {
        // Random Number
        $randomNumber = '';
        for ($i = 0; $i < $length; $i++) {
            // Generate using Rand
            $randomNumber .= rand(0, $maxDigit);
        }

        // Return
        return (int) $randomNumber;
    }
}
