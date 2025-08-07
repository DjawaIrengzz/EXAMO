<?php
namespace App\Services\Interfaces;

interface ExamResultServiceInterface
{public function getAllResults(array $filters = []): LengthAwarePaginator;
    public function getResultsByUserId(int $userId, array $filters = []): LengthAwarePaginator;
    public function getResultsByExamId(int $examId, array $filters = []): LengthAwarePaginator;
    public function storeResult(array $data): UserExam;
    public function updateResult(int $id, array $data): UserExam;
    public function deleteResult(int $id): bool;
}