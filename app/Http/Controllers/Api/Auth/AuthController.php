<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function auth(AuthRequest $request) : JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                ['success' => false, 'message' => "As credenciais enviadas estão incorretas"]);
        }

        // Logout other devices
        // if($request->has('logout_others_devices'))
        $user->tokens()->delete();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function register(AuthRequest $request) : string
    {
        if($request->password !== $request->password_confirmation) {
            return response()->json(['success' => false, 'message' => "Senhas estão diferentes"], 409);
        }

        $findUserByEmail = User::where('email', $request->email)->first();

        if($findUserByEmail) {
            return response()->json(['success' => false, 'message' => "Email já cadastrado em outro usuário"], 409);
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->save();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function logout(Request $request) : string
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sucesso'
        ]);
    }

    public function me(Request $request) : string
    {
        $user = $request->user();

        return response()->json([
            'me' => $user
        ]);
    }

    public function dashboard() : string
    {
        return response()->json([
            'success' => true,
            'message' => "Deu certo"
        ]);


    }

}
