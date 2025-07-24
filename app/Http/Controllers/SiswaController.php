<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\AvatarHelper;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateBiodataRequest;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswa = auth()->user();

        return response()->json([
            'name' => $siswa->name,
            'email' => $siswa->email,
            'phone' => $siswa->phone_number,
            'gender' => $siswa->gender,
            'avatar_url' => AvatarHelper::getAvatarUrl($siswa, 'siswa'),
            'avatar_uploaded' => $siswa->avatar ? true : false,
            'role' => $siswa->role,
            'created_at' => $siswa->created_at,
            'status' => $siswa->status,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);

        return response()->json([
            'message' => 'Siswa found',
            'data' => $siswa
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBiodataRequest $request, string $id)
    {
        $siswa = auth()->user();
        $validated = $request->validated();

        $siswa->update($validated);

        return response()->json([
            'message' => 'Biodata updated successfully',
            'data' => $siswa
        ]);
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $siswa = auth()->user();
        $request->validated();

        if ($request->hasFile('avatar')) {
            if ($siswa->avatar && Storage::disk('public')->exists($siswa->avatar)) {
                Storage::disk('public')->delete($siswa->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $siswa->avatar = $path;
        }

        $siswa->save();

        return response()
            ->json([
                'message' => 'Avatar updated successfully',
                'siswa' => $siswa
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyAvatar(string $id)
    {
        $siswa = auth()->user();

        if (!$siswa->avatar) {
            return response()->json([
                "message" => "Avatar not found",
            ], 404);
        }

        if (Storage::disk('public')->exists($siswa->avatar)) {
            Storage::disk('public')->delete($siswa->avatar);
        }

        $siswa->avatar = null;
        $siswa->save();

        return response()->json([
            "message" => "Avatar deleted successfully",
        ], 200);
    }
}
