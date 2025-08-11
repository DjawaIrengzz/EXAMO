<?php

namespace App\Repositories\Profile\Interface;

use App\Models\User;

interface BiodataRepositoryInterface
{

    public function findById(int $id): User;
    public function updateUser(User $user, array $data): User;
}
