<?php

namespace App\Services\Profile\Interface;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface AvatarServiceInterface
{
    /**
     * Simpan avatar baru, return URL avatar.
     */
    public function updateAvatar($validated);

    /**
     * Hapus avatar user, return void atau flag.
     */
    public function destroyAvatar();
}
