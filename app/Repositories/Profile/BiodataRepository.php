<?php

namespace App\Repositories\Profile;

use App\Models\User;
use App\Repositories\Profile\Interface\BiodataRepositoryInterface;

class BiodataRepository implements BiodataRepositoryInterface
{

    public function findById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }
}
