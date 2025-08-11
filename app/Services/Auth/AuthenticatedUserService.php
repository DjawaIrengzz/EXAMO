<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Helpers\BaseResponse;
use App\Services\Auth\Interface\AuthenticatedUserInterface;
use Illuminate\Support\Facades\Auth;

class AuthenticatedUserService implements AuthenticatedUserInterface
{
    /**
     * Summary of user
     * @return User
     */
    public function user(): ?User
    {
        return Auth::user();
    }
    /**
     * Summary of id
     * @return int|string|null
     */
    public function id(): ?int
    {
        return Auth::id();
    }
    /**
     * Summary of role
     */
    public function role(): ?string
    {
        return Auth::user()?->role;
    }
    /**
     * Summary of isGuest
     * @return bool
     */
    public function isGuest(): bool
    {
        return Auth::guest();
    }
    /**
     * Summary of ensure
     * @return User
     */
    public function ensure(): User
    {
        $user = Auth::user();

        if (!$user) {
            BaseResponse::Unauthorized();
        }

        return $user;
    }
}
