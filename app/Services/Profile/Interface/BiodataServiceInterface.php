<?php

namespace App\Services\Profile\Interface;

use App\Models\User;

interface BiodataServiceInterface
{
    /**
     * Ambil profil lengkap (biodata + kredensial jika ada) untuk user terautentikasi.
     */
    public function getProfile($user, $role);

    /**
     * Ambil profil berdasarkan role dan ID, throw ModelNotFoundException jika tidak ada
     */
    public function checkRole( int $id);

    /**
     * Update biodata (name, email, gender, dll) untuk user tertentu.
     */
    public function updateBiodata($validated);
}
