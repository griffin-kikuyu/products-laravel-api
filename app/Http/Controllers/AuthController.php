<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required|string',
                'email' => 'required|email|string|unique:users',
                'password' => [
                    'required',
                    'min:6',
                    'max:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,8}$/',
                ],
            ], [
                'email.unique' => 'The email already exists.',
                'password.min' => 'The password must be more than 6 characters long.',
                'password.max' => 'The password must be a maximum of 8 characters long.',
                'password.regex' => 'The password must have at least one uppercase letter, one lowercase letter, one number, and one special character.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json(['message' => 'Signup successful.'], 201);
        } 
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // public function login(Request $request): JsonResponse
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'email' => 'required|email',
    //             'password' => 'required',
    //         ], [
    //             'email.required' => 'Please enter an email address.',
    //             'email.email' => 'Please enter a valid email address.',
    //             'password.required' => 'Please enter a password.',
    //         ]);

    //         $user = User::where('email', $validatedData['email'])->first();

    //         if (!$user) {
    //             return response()->json(['error' => 'User not found.'], 404);
    //         }
    //         if (!$user->verified) {
    //             return response()->json(['error' => 'Please verify your email address first.'], 401);
    //         }
    //         if (!Hash::check($validatedData['password'], $user->password)) {
    //             return response()->json(['error' => 'Invalid credentials.'], 401);
    //         }

    //         $is_admin = $user->hasRole('admin');
    //         $token = $user->createToken('auth-token')->plainTextToken;

    //         return response()->json(['token' => $token, 'user' => $user, 'is_admin' => $is_admin], 200);

    //     } catch (ValidationException $e) {
    //         $errors = $e->validator->errors()->all();
    //         return response()->json(['errors' => $errors], 422);
    //     } catch (\Exception $e) {
    //         $errorMessage = $e->getMessage(); // Get the specific error message
    //         Log::error($errorMessage); // Log the error message for debugging purposes

    //         return response()->json(['error' => $errorMessage], 500);
    //     }
    // }
    public function login(Request $request): JsonResponse
{
    try {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter a password.',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        if (!Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json(['message' => 'Login successful.', 'token' => $token, 'user' => $user], 200);

    } catch (ValidationException $e) {
        $errors = $e->validator->errors()->all();
        return response()->json(['errors' => $errors], 422);
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage(); // Get the specific error message
        Log::error($errorMessage); // Log the error message for debugging purposes

        return response()->json(['error' => 'Something went wrong.'], 500);
    }
}
public function logout(Request $request){
    $user = $request->user();

    $user->tokens()->delete();

    return response()->json(['message' => "Logout successful"]);
}
}