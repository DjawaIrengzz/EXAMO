<?php

namespace App\Repositories\Interfaces;

use App\Models\ExamResult;
use Illuminate\Pagination\LengthAwarePaginator;
interface ExamResultRepositoryInterface
{
     public function getAll(array $filters = []): LengthAwarePaginator;
    public function getByUser(int $userId, array $filters = []): LengthAwarePaginator;
    public function getByExam(int $examId, array $filters = []): LengthAwarePaginator;
    public function create(array $data): UserExam;
    public function update(int $id, array $data): UserExam;
    public function delete(int $id): bool;
}