<?php

namespace App\Services\Profile;

use App\Models\User;
use App\Helpers\InputHelper;
use App\Helpers\AvatarHelper;
use App\Helpers\BaseResponse;
use App\Services\Auth\AuthenticatedUserService;
use App\Services\Profile\Interface\BiodataServiceInterface;
use App\Repositories\Profile\Interface\BiodataRepositoryInterface;

class BiodataService implements BiodataServiceInterface
{

    protected AuthenticatedUserService $authService;

    protected BiodataRepositoryInterface $biodataRepo;

    public function __construct(
        AuthenticatedUserService $authService,
        BiodataRepositoryInterface $biodataRepo
    ) {
        $this->authService = $authService;
        $this->biodataRepo = $biodataRepo;
    }

    /**
     * Summary of guruIndex
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile($user, $role)
    {
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone_number,
            'gender' => $user->gender,
            'avatar_url' => AvatarHelper::getAvatarUrl($user, '$role'),
            'avatar_uploaded' => (bool) $user->avatar,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'status' => $user->status,
        ];

        if ($user->role === 'guru') {
            // Teacher Kredensial
        }

        return $data;
    }

    public function checkRole($id)
    {
        $user = $this->authService->ensure();

        $target = $this->biodataRepo->findById($id);

        if ($user->role === 'admin') {
            // Lihas semua role
        } elseif ($user->role === 'guru' && $target->role === 'admin') {
            return BaseResponse::Forbidden('Anda tidak memiliki akses');
        } elseif ($user->role === 'siswa' && in_array($target->role, ['guru', 'admin'])) {
            return BaseResponse::Forbidden('Anda tidak memiliki akses');
        } elseif ($user->role !== 'admin' && $user->role !== $target->role) {
            return BaseResponse::Forbidden('Anda tidak memiliki akses');
        }

        $profileData = $this->getProfile($target, $target->role);

        if (
            $target->role === 'guru' &&
            $user->role !== 'admin' &&
            $user->id !== $target->id
        ) {
            unset(
                $profileData['teacher_id'],
                $profileData['teacher_key']
            );
        }

        return BaseResponse::OK($profileData, 'Retrive data successfully');
    }
    /**
     * Summary of UpdateBiodata
     * @param mixed $validated
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBiodata($validated)
    {
        $user = $this->authService->ensure();

        $clean = InputHelper::filterEmptyValues($validated);

        $updateUser = $this->biodataRepo->updateUser($user, $clean);

        return BaseResponse::OK($updateUser, 'Biodata berhasil diupdate');
    }
}
