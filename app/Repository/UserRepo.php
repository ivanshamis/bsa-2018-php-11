<?php

namespace App\Repository;

use App\Repository\Contracts\UserRepository;
use App\User;

class UserRepo implements UserRepository
{
    public function getById(int $id) : ?User
    {
        return User::find($id)->first();
    }

    public function add(User $user): User
    {
        $user->save();
        return $user;
    }

    public function getFakeUser(): User
    {
        return factory(User::class)->create();
    }
}