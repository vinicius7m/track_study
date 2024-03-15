<?php

namespace App\Repositories\Api\Auth;

interface AuthRepositoryInterface
{
    public function findByEmail(array $data): ?object;

    public function create(array $data): ?object;
}
