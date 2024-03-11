<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\User;
use Kreait\Laravel\Firebase\Facades\Firebase;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        error_log(1);
        // Validación de los campos requeridos
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        error_log(2);

        $email = $request->input('email');
        $password = $request->input('password');
        
        $firebaseAuth = Firebase::auth();
        error_log(3);
        try {
            // Intentamos autenticar al usuario con Firebase
            $signInResult = $firebaseAuth->signInWithEmailAndPassword($email, $password);
            // Aquí puedes obtener diferentes datos del usuario o del resultado del inicio de sesión
            $user = $signInResult->data();
            // Por ejemplo, puedes obtener el token del usuario
            $idToken = $signInResult->idToken();

            $user = User::where('email', $email)->first();
            Auth::login($user);
            $accessToken = $user->createToken('authToken')->accessToken;
            return response(['user' => auth()->user(), 'access_token' => $accessToken, 'token' => $idToken]);
        } catch (\Exception $e) {
            error_log("La autenticación falló: " . $e->getMessage());
            return response()->json(['error' => 'Login failed'], 401);
        }
    }

public function register(Request $request)
{
    error_log(1);
    /*$validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);*/
       
    $validator = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
    ]);
    error_log(2);

    error_log(3);

   $passwordHash = Hash::make($request->input('password'));

   error_log($passwordHash);

   $user = User::create([
    'name' => $request->input('name'),
    'email' => $request->input('email'),
    'password' => $passwordHash,
    ]);

    error_log(4);

    $token = $user->createToken('authToken')->accessToken;

    error_log($token);

    return response()->json(['token' => $token,'email'=> $email], 200);
}

}
