<?php

namespace App\Http\Middleware;

use App\Helpers\BaseResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return BaseResponse::error('User tidak terautentikasi', 401);
        }

        // mendukung beberapa role dipisah koma: "admin,editor"
        $allowed = array_map('trim', explode(',', $roles));
        $userRole = strtolower($user->role ?? '');

        $matched = collect($allowed)
            ->map(fn($r) => strtolower($r))
            ->contains($userRole);

        if (!$matched) {
            Log::warning('Unauthorized role access', [
                'user_id' => $user->id ?? null,
                'user_role' => $user->role ?? null,
                'required_roles' => $allowed
            ]);

            return BaseResponse::error('Akses ditolak', 403);
        }

        return $next($request);
    }
}
