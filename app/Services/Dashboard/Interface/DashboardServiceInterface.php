<?php

namespace App\Services\Dashboard\Interface;

interface DashboardServiceInterface
{
    public function getSiswaDashboard();
    public function getGuruDashboard();
    public function getAdminDashboard();
    public function examSiswa(string $id);
    public function examGuru(string $id);
}
