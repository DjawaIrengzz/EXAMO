<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\BaseResponse;
use Illuminate\Support\Facades\Log;
use App\Services\Profile\AvatarService;
use App\Services\Profile\BiodataService;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateBiodataRequest;
use App\Services\Profile\Interface\AvatarServiceInterface;
use App\Services\Auth\Interface\AuthenticatedUserInterface;
use App\Services\Profile\Interface\BiodataServiceInterface;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Summary of biodataService
     * @var BiodataServiceInterface
     */
    private BiodataServiceInterface $biodataService;
    /**
     * Summary of avatarService
     * @var AvatarServiceInterface
     */
    private AvatarServiceInterface $avatarService;
    /**
     * Summary of authService
     * @var AuthenticatedUserInterface
     */
    private AuthenticatedUserInterface $authService;

    /**
     * Summary of __construct
     * @param \App\Services\Profile\Interface\BiodataServiceInterface $biodataService
     * @param \App\Services\Profile\Interface\AvatarServiceInterface $avatarService
     * @param \App\Services\Auth\Interface\AuthenticatedUserInterface $authService
     */
    public function __construct(
        BiodataServiceInterface $biodataService,
        AvatarServiceInterface $avatarService,
        AuthenticatedUserInterface $authService
    ) {
        $this->biodataService = $biodataService;
        $this->avatarService  = $avatarService;
        $this->authService = $authService;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = $this->authService->user();
            $profileData = $this->biodataService->getProfile($user, $this->authService->role());

            return BaseResponse::OK($profileData, 'Data retrive successfully');
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil profil: ' . $e->getMessage(), [
                'user_id' => isset($user) ? $user->id : null,
                'trace'   => $e->getTraceAsString(),
            ]);

            return BaseResponse::ServerError('Gagal mengambil profil.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            return $this->biodataService->checkRole($id);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil detail profil: ' . $e->getMessage(), [
                'id'      => $id,
                'trace'   => $e->getTraceAsString(),
            ]);

            return BaseResponse::ServerError('Gagal mengambil detail profil.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateBiodata(UpdateBiodataRequest $request): JsonResponse
    {
        try {
            return $this->biodataService->updateBiodata($request->validated());
        } catch (\Throwable $e) {
            Log::error('Gagal update biodata: ' . $e->getMessage(), [
                'user_id' => $this->authService->user()->id ?? null,
                'trace'   => $e->getTraceAsString(),
            ]);
            return BaseResponse::ServerError('Gagal memperbarui biodata.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        try {
            return $this->avatarService->updateAvatar($request->validated());
        } catch (\Throwable $e) {
            Log::error('Gagal update avatar: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return BaseResponse::ServerError('Gagal memperbarui avatar.');
        }
    }

    public function destroyAvatar(): JsonResponse
    {
        try {
            return $this->avatarService->destroyAvatar();
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus avatar: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return BaseResponse::ServerError('Gagal menghapus avatar.');
        }
    }
}
