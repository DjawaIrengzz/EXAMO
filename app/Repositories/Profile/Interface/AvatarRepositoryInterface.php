<?php

namespace App\Repositories\Profile\Interface;

use App\Models\User;

interface AvatarRepositoryInterface
{
    public function updateAvatar(User $user, string $path): User;

    public function removeAvatar(User $user): User;
}
