<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaceUser;
class FaceAuthController extends Controller


{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'encoding' => 'required|array'
        ]);

        FaceUser::create([
            'name' => $request->name,
            'face_encoding' => $request->encoding,
        ]);

        return response()->json(['message' => 'berhasil register']);
    }

    public function loginFace(Request $request)
    {
        $encoding = $request->input('encoding');
        $users = FaceUser::all();

        foreach ($users as $user) {
            $dbEncoding = $user->face_encoding;
            $distance = 0;

            for ($i = 0; $i < count($encoding); $i++) {
                $distance += pow($encoding[$i] - $dbEncoding[$i], 2);
            }

            $distance = sqrt($distance);

            if ($distance < 0.45) {
                return response()->json([
                    'message' => 'login sukses',
                    'user' => $user->name
                ]);
            }
        }

        return response()->json([
            'message' => 'Wajah tidak dikenali'
        ], 401);
    }
}