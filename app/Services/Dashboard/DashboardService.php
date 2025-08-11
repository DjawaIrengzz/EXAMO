<?php

namespace App\Services\Dashboard;

use App\Helpers\BaseResponse;
use App\Repositories\Dashboard\Interface\DashboardRepositoryInterface;
use App\Services\Dashboard\Interface\DashboardServiceInterface;

class DashboardService implements DashboardServiceInterface
{
    protected DashboardRepositoryInterface $repo;

    public function __construct(DashboardRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getSiswaDashboard()
    {
        $data = $this->repo->getSiswaDashboard();
        return BaseResponse::OK($data, 'Dashboard Siswa retrieved successfully');
    }

    public function getGuruDashboard()
    {
        $data = $this->repo->getGuruDashboard();
        return BaseResponse::OK($data, 'Dashboard Guru retrieved successfully');
    }

    public function getAdminDashboard()
    {
        $data = $this->repo->getAdminDashboard();
        return BaseResponse::OK($data, 'Dashboard Admin retrieved successfully');
    }

    public function examSiswa(string $id)
    {
        $data = $this->repo->findExamSiswa($id);
        if (!$data) return BaseResponse::NotFound('Exam not found');
        return BaseResponse::OK($data, 'Exam Siswa retrieved successfully');
    }

    public function examGuru(string $id)
    {
        $data = $this->repo->findExamGuru($id);
        if (!$data) return BaseResponse::NotFound('Exam not found');
        return BaseResponse::OK($data, 'Exam Guru retrieved successfully');
    }
}
