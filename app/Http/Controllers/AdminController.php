<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (Gate::denies('viewAny', User::class)){
            return response()->json(['message' => 'Forbidden'],403);

        }

        $gurus = User::where('role', 'guru')
        ->select('id', 'name', 'email', 'is_active', 'created_at')
        ->orderBy('name') 
        ->paginate($request->query('per_page', 10));

        return response()->json($gurus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if(Gate::denies('view', User::class)){
            return response()->json(['message'=> 'Forbidden'],403);
        }
        $guru = User::where('role', 'guru')
        ->with('currentAccessToken')
        ->findOrFail($id);

        $tokenModel = $guru->currentAccessToken();
        $teacherKey = $tokenModel->plainTextToken;

        return response()->json
        ([
            'id' => $guru->id,
            'name' =>$guru ->name,
            'email' => $guru->email,
            'phone_number' => $guru->phone_number,
            'role' => $guru->role,
            'is_active' => $guru->is_active,
            'created_at' => $guru->created_at,
            'teacher_id' => 'Guru'. $guru->id,
            'teacher_key' =>$teacherKey,

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
