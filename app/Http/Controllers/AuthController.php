<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AuthLoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handles user login, authentication, and Laravel Passport token generation.
     *
     * @param AuthLoginRequest $request
     * @return JsonResponse
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        // 1. Attempt to authenticate using email and password
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials provided. Check your email or password.',
            ], 401); // 401 Unauthorized
        }

        // 2. Retrieve the authenticated user
        $user = Auth::user();

        // 3. Status Check (Assuming active status is 1, as per guidelines)
       
        // 4. Token Generation (Laravel Passport)
        // Token is named based on the user type for clarity
        $tokenName = ($user->user_type === 2) ? 'faculty_api_token' : 'student_api_token';

            if ($user->user_type === 2) {
        // Only grant the faculty scope if they are a faculty member
        $scopes = ['faculty-access']; 
    } else {
        // Grant the student scope otherwise
        $scopes = ['student-only']; 
    }
        
        // Passport's createToken generates a token object
        // We retrieve the accessToken property which holds the actual JWT string.
            $tokenResult = $user->createToken($tokenName, $scopes); 
            $Token = $tokenResult->accessToken;

        

        // 5. Successful API Response
        return response()->json([
            'message' => 'Login successful.',
            'access_token' => $Token,
            'token_type' => 'Bearer',
            'user' => [
                'user_id' => $user->user_id,
                'user_type' => $user->user_type,
                'email' => $user->email,
            ]
        ], 200);
    }

    public function logout(Request $request): JsonResponse
{
    // Revokes the current token used for the request.
    // Auth::user() or $request->user() returns the authenticated User instance.
    // token() returns the Passport token object.
    $request->user()->token()->revoke();
    
    return response()->json([
        'message' => 'Successfully logged out and token revoked.'
    ], 200);
}
}