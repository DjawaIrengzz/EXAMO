<?php

namespace App\Services\Auth\Interface;

use App\Models\User;

interface AuthenticatedUserInterface{

    /**
     * Summary of user
     * @return void
     */
    public function user(): ?User;

    /**
     * Summary of id
     * @return void
     */
    public function id(): ?int;

    /**
     * Summary of role
     * @return void
     */
    public function role(): ?string;

    /**
     * Summary of isGuest
     * @return void
     */
    public function isGuest(): bool;

    /**
     * Summary of ensure
     * @return void
     */
    public function ensure(): User;

}