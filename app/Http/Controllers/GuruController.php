<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AvatarHelper;
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

        return response()->json([
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
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $guru = auth()->user();

        return response()
            ->json([
                'guru' => $guru
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBiodataRequest $request, string $id)
    {
        $guru = auth()->user();
        $validated = $request->validated();

        $guru->update($validated);

        return response()
            ->json([
                'message' => 'Biodata updated successfully',
                'guru' => $guru
            ], 200);
    }

    public function updateAvatar(UpdateAvatarRequest $request)

    {
        $guru = auth()->user();
        $request->validated();

        if ($request->hasFile('avatar')) {
            if ($guru->avatar && Storage::disk('public')->exists($guru->avatar)) {
                Storage::disk('public')->delete($guru->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $guru->avatar = $path;
        }

        $guru->save();

        return response()
            ->json([
                'message' => 'Avatar updated successfully',
                'guru' => $guru
            ], 200);
    }

    public function destroyAvatar()
    {
        $guru = auth()->user();

        if (!$guru->avatar) {
            return response()->json([
                "message" => "Avatar not found",
            ], 404);
        }

        if (Storage::disk('public')->exists($guru->avatar)) {
            Storage::disk('public')->delete($guru->avatar);
        }

        $guru->avatar = null;
        $guru->save();

        return response()->json([
            "message" => "Avatar deleted successfully",
        ], 200);
    }
}
