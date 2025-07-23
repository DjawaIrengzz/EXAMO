<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        if(!$token) {
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
                'avatar' => $guru->avatar ? asset('storage/' . $guru->avatar) : null,
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()
            ->json([
                'message' => 'This endpoint is not implemented yet.'
            ], 501);
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

    public function updateAvatar(UpdateAvatarRequest $reqeust)
    {
        $guru = auth()->user();
        $validated = $reqeust->validated();

        if ($reqeust->hasFile('avatar')) {
            $path = $reqeust->file('avatar')->store('avatars', 'public');
            $guru->avatar = $path;
        }

        $guru->save();

        return response()
            ->json([
                'message' => 'Avatar updated successfully',
                'guru' => $guru
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response()
            ->json([
                'message' => 'This endpoint is not implemented yet.'
            ], 501);
    }
}
