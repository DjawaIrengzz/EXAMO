<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class AvatarHelper
{
    /**
     * Summary of generateDefaultAvatar
     * @param string $name
     * @param string $role
     * @return string
     */
    public static function generateDefaultAvatar(string $name, string $role)
    {
        $background = match ($role) {
            'guru' => '007bff',   // biru
            'siswa' => '28a745',  // hijau
            default => '6c757d',  // abu-abu
        };

        return "https://ui-avatars.com/api/?name=" . urlencode($name)
            . "&background={$background}&color=ffffff&bold=true";
    }
    /**
     * Summary of getAvatarUrl
     * @param mixed $user
     * @param mixed $type
     * @return string
     */
    public static function getAvatarUrl($user, $type = 'guru')
    {
        return $user->avatar
            ? asset('storage/' . $user->avatar)
            : self::generateDefaultAvatar($user->name, $type);
    }
    /**
     * Summary of deleteAvatarIfExists
     * @param mixed $path
     * @return void
     */
    public static function deleteAvatarIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
    /**
     * Summary of storeAvatar
     * @param mixed $file
     * @return string
     */
    public static function storeAvatar($file): string
    {
        return $file->store('avatars', 'public');
    }
}
