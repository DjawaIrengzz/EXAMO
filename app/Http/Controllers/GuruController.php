<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AvatarHelper;
use App\Helpers\BaseResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateBiodataRequest;

class GuruController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $guru = auth()->user();
        $token = auth()->user()->currentAccessToken();

        if (!$token) {
            return response()->json([
                'message' => 'Token not found',
            ], 404);
        }

        $createdAt = Carbon::parse($token->created_at)->translatedFormat('d F Y');
        $lastUsed = $token->last_used_at
            ? Carbon::parse($token->last_used_at)->translatedFormat('d F Y, H:i')
            : null;

        $isActive = $token->last_used_at
            ? Carbon::parse($token->last_used_at)->gt(now()->subDays(30))
            : false;

        return BaseResponse::OK([
            'informasi_umum' => [
                'name' => $guru->name,
                'email' => $guru->email,
                'phone' => $guru->phone_number,
                'gender' => $guru->gender,
                'avatar_url' => AvatarHelper::getAvatarUrl($guru  , 'guru'),
                'avatar_uploaded' => $guru->avatar ? true : false,
                'role' => $guru->role,
                'created_at' => $guru->created_at,
                'status' => $guru->status,
            ],
            'kredensial' => [
                'status' => $isActive,
                'generated_at' => $createdAt,
                'last_used' => $lastUsed ?? 'belum pernah digunakan',
                'key' => $token,
            ]
        ], 'Guru information retrieved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $guru = auth()->user();

        return BaseResponse::OK($guru, 'Guru found');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBiodataRequest $request)
    {
        $guru = auth()->user();
        $validated = $request->validated();

        $guru->update($validated);

        return BaseResponse::OK($guru, 'Biodata updated successfully');
    }

    public function updateAvatar(UpdateAvatarRequest $request)

    {
        $guru = auth()->user();
        $request->validated();

        if($request->hasFile('avatar')){
            AvatarHelper::deleteAvatarIfExists($guru->avatar);

            $guru->avatar = AvatarHelper::storeAvatar($request->file('avatar'));
        }

        $guru->avatar = AvatarHelper::storeAvatar($request->file('avatar'));

        $guru->save();

        return BaseResponse::OK([
            'avatar_url' => AvatarHelper::getAvatarUrl($guru, 'siswa'),
            'avatar_uploaded' => (bool) $guru->avatar
        ], 'Avatar updated successfully');
    }

    public function destroyAvatar()
    {
        $guru = auth()->user();

        if (!$guru->avatar) {
            BaseResponse::BadRequest('No avatar to delete');
        }

        AvatarHelper::deleteAvatarIfExists($guru->avatar);

        $guru->avatar = null;
        $guru->save();

        return BaseResponse::OK(null, 'Avatar deleted successfully');
    }
}
