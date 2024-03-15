<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Api\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        return $this->authService->login($request->validated());
    }

    public function register(RegisterRequest $request) : JsonResponse
    {
        return $this->authService->register($request->validated());
    }

    public function logout(Request $request) : JsonResponse
    {
        return $this->authService->logout($request->user());
    }

    public function profile(Request $request) : JsonResponse
    {
        return $this->authService->profile($request->user());
    }

}
