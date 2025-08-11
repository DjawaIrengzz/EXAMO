<?php

namespace App\Repositories\Profile;

use App\Models\User;
use App\Repositories\Profile\Interface\AvatarRepositoryInterface;

class AvatarRepository implements AvatarRepositoryInterface
{
    public function updateAvatar(User $user, string $path): User
    {
        $user->avatar = $path;
        $user->save();
        return $user;
    }

    public function removeAvatar(User $user): User
    {
        $user->avatar = null;
        $user->save();
        return $user;
    }
}
