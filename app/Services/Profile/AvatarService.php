<?php

namespace App\Services\Profile;

use App\Helpers\AvatarHelper;
use App\Helpers\BaseResponse;
use App\Services\Auth\AuthenticatedUserService;
use App\Services\Profile\Interface\AvatarServiceInterface;
use App\Repositories\Profile\Interface\AvatarRepositoryInterface;

class AvatarService implements AvatarServiceInterface
{
    protected AuthenticatedUserService $authService;
    protected AvatarRepositoryInterface $avatarRepo;

    public function __construct(
        AuthenticatedUserService $authService,
        AvatarRepositoryInterface $avatarRepo
    ) {
        $this->authService = $authService;
        $this->avatarRepo = $avatarRepo;
    }

    /**
     * Update avatar user login saat ini
     *
     * @param array $validated
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar($validated)
    {
        $user = $this->authService->ensure();
        $role = $this->authService->role();

        if (!isset($validated['avatar'])) {
            return BaseResponse::BadRequest('Tidak ada file avatar yang dikirim');
        }

        AvatarHelper::deleteAvatarIfExists($user->avatar);

        $avatarPath = AvatarHelper::storeAvatar($validated['avatar']);
        $this->avatarRepo->updateAvatar($user, $avatarPath);

        return BaseResponse::OK([
            'avatar_url' => AvatarHelper::getAvatarUrl($user, $role),
            'avatar_uploaded' => (bool) $user->avatar
        ], 'Avatar updated successfully');
    }

    /**
     * Hapus avatar user login saat ini
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyAvatar()
    {
        $user = $this->authService->ensure();

        if (!$user->avatar) {
            return BaseResponse::BadRequest('No avatar to delete');
        }

        AvatarHelper::deleteAvatarIfExists($user->avatar);
        $this->avatarRepo->removeAvatar($user);

        return BaseResponse::OK(null, 'Avatar deleted successfully');
    }
}
