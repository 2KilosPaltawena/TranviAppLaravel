<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
// Importamos la fachada de Firebase
use Kreait\Laravel\Firebase\Facades\Firebase;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validación de los campos requeridos
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        // Obtenemos la instancia de autenticación de Firebase
        $firebaseAuth = Firebase::auth();
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
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        return response(['errors' => $validator->errors()->all()], 422);
    }

    $request['password'] = Hash::make($request['password']);
    $user = User::create($request->toArray());

    $token = $user->createToken('authToken')->accessToken;

    return response()->json(['token' => $token], 200);
}

}
