<?php
namespace App\Repositories\Api\Auth;

use Illuminate\Http\JsonResponse;

interface AuthRepositoryInterface
{

    public function findByEmail(array $data) : ?object;

}
