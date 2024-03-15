<?php

namespace App\Repositories\Api\Auth;

use App\Models\User;
use App\Repositories\Api\Auth\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function findByEmail(array $data) : ?object
    {
        $user = $this->user::where('email', $data['email'])->first();
        return $user ? $user : null;
    }

    public function create(array $data) : ?object
    {
        return $this->user::create($data);
    }
}
