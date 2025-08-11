<?php

namespace App\Repositories\Dashboard\Interface;

interface DashboardRepositoryInterface
{
    public function getSiswaDashboard();
    public function getGuruDashboard();
    public function getAdminDashboard();
    public function findExamSiswa(string $id);
    public function findExamGuru(string $id);
}
