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
}
