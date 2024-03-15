<?php
namespace App\Services\Api\Auth;

use Illuminate\Http\JsonResponse;

interface AuthServiceInterface
{

    public function login(array $data) : JsonResponse;

    public function register(array $data) : JsonResponse;

    public function logout(object $user) : JsonResponse;

    public function profile(object $user) : JsonResponse;

}
