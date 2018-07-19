<?php

namespace App\Repository;

use App\Repository\Contracts\UserRepository;
use App\User;

class UserRepo implements UserRepository
{
    public function getById(int $id) : ?User
    {
        return User::find($userId)->first();
    }

    public function add(User $user): User
    {
        $user->save();
        return $user;
    }
}