<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        error_log(1);

         $validator = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
    ]);

        error_log(2);

        $passwordHash = Hash::make($request->input('password'));

        error_log($passwordHash);
     
        $user = User::create([
         'name' => $request->input('name'),
         'email' => $request->input('email'),
         'password' => $passwordHash,
         ]);

        error_log(3);

        // Asigna el rol inicial al usuario
        $user->assignRole('UserDriver');
        error_log(4);

        return response()->json(['user' => $user, 'token' => $user->createToken('authToken')->plainTextToken]);
    }

    public function login(Request $request)
    {
        error_log(1);
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        error_log(2);

        $email = $request->input('email');
        $password = $request->input('password');
        error_log(3);

        $user = User::where('email', $request->email)->first();
        error_log(4);

        $firebaseAuth = Firebase::auth();
        $signInResult = $firebaseAuth->signInWithEmailAndPassword($email, $password);
        $userFirebase = $signInResult->data();
        $idToken = $signInResult->idToken();
        error_log(5);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        error_log(6);

        return response()->json(['token' => $user->createToken('authToken')->plainTextToken,'tokenFirebase'=>$idToken]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
