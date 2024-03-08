<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

            // Aquí puedes decidir qué hacer con el usuario y el token. Por ejemplo, podrías
            // crear una sesión, devolver una respuesta con el token, etc.
            
            // Devolvemos una respuesta de éxito (modifica esto según tus necesidades)
            return response()->json(['message' => 'Login successful', 'token' => $idToken]);
        } catch (\Exception $e) {
            // Manejo de errores si la autenticación falla
            error_log("La autenticación falló: " . $e->getMessage());
            return response()->json(['error' => 'Login failed'], 401);
        }
    }
}
