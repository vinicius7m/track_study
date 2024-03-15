<?php

namespace App\Services\Api\Auth;

use App\Models\User;
use App\Repositories\Api\Auth\AuthRepository;
use App\Services\Api\Auth\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(array $data) : JsonResponse
    {
        $user = $this->authRepository->findByEmail($data);

        if(!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                    'success' => false,
                    'message' => "As credenciais enviadas estão incorretas"
                ]);
        }

        // Logout other devices
        // if($data->has('logout_others_devices'))
        $user->tokens()->delete();

        $token = $user->createToken($data['device_name'])->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function register(array $data) : JsonResponse
    {


        if($data['password'] !== $data['password_confirmation']) {
            return response()->json(['success' => false, 'message' => "Senhas estão diferentes"], 409);
        }

        $user = $this->authRepository->findByEmail($data);

        if($user) {
            return response()->json(['success' => false, 'message' => "Email já cadastrado em outro usuário"], 409);
        }

        $dataUser = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ];

        $user = $this->authRepository->create($dataUser);

        $token = $user->createToken($data['device_name'])->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function logout(object $user) : JsonResponse
    {
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sucesso'
        ]);
    }

    public function profile(object $user) : JsonResponse
    {
        return response()->json([
            'user' => $user
        ]);
    }
}
