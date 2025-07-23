<?php

namespace App\Http\Controllers;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\Auth\ChangePassword;
use App\Http\Requests\Auth\ForgotPassword;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdatePassword;
use App\Http\Requests\Auth\ResetPassword;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\HasApiTokens;
class AuthController extends Controller
{
    public function login(LoginRequest $request){
        $credential= $request->validated();
        $user = User::where('email', $credential['email'])->first();
        if (!$user || !Hash::check($credential['password'], $user->password)) {
            return response()->json([
                'message' => 'Login Gagal'
            ], 401);
        }
        

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'=>'Login berhasil',
            'token'=> $token,
            'user' => [
                'id'=> $user->id,
                'name'=> $user->name,
                'email'=> $user->email,
                'role'=> $user->role,
            ]
        ],200);
    }

    public function register(RegisterRequest $request){
        $validate = $request->validated();
        $user = User::create([
            'name' => $validate['name'],
            'email'=> $validate['email'],
            'password'=> Hash::make($validate['password']),
            'role' => $validate['role'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'=> 'Berhasil buat akun',
            'token' => $token,
            'user'=> [
                'id'=> $user->id,
                'name'=> $user->name,
                'email'=> $user->email,
                'role'=> $user->role,
            ]
        ]);
    }


    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message'=> 'Berhasil logout'
        ]);
    }
    public function forgotPassword(ForgotPassword $request){

        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => 'Reset Link Sended'])
        : response()->json(['message' => 'Unable send the link'],401);

    }
    public function resetPassword(ResetPassword $request){

        $status = Password::reset(
            $request->only(
                'email','password', 'password_confirmation', 'token'
            ),
            //closeure I
            //         V 

            function ($user,$password){
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
                $user ->tokens()->delete();
                }
                );
                if ($status == Password::PASSWORD_RESET){
                    return response() -> json([
                        'message' => 'Password Berhasil direset'
                    ]);
                }
                throw ValidationException::withMessages([
                    'email'=> [__($status)],
                ]);
    }

    public function update(UpdatePassword $request){

        $user = $request->user();
        $user->update($request->only('name', 'email'));
        return response()->json([
            'message' =>'Berhasil Update',
            'data' => $user
        ]);
    }

    public function changePassword(ChangePassword $request){

        $user = $request->user();

        if(!Hash::check($request->current_password,$user->password)){
            return response()->json(['message' =>'password salah']);
        }
        $user->update([
            'password' => Hash::make($request->new_password),
    
    ]);
    }
}
