<?php
namespace App\Services\Api\Auth;

use Illuminate\Http\JsonResponse;

interface AuthServiceInterface
{

    public function login(array $data): JsonResponse;

}
