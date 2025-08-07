<?php

namespace App\Services;

use App\Services\Interfaces\ExamResultServiceInterface;
use App\Repositories\Interfaces\ExamResultRepositoryInterface;

class ExamResultService implements ExamResultServiceInterface
{
    protected $examResultRepository;

    public function __construct(ExamResultRepositoryInterface $examResultRepository)
    {
        $this->examResultRepository = $examResultRepository;
    }

    public function getAllResults()
    {
        return $this->examResultRepository->getAll();
    }

    public function getResultsByUserId($userId)
    {
        return $this->examResultRepository->getResultsByUserId($userId);
    }

    public function getResultsByExamId($examId)
    {
        return $this->examResultRepository->getResultsByExamId($examId);
    }

    public function storeResult(array $data)
    {
        return $this->examResultRepository->store($data);
    }

    public function updateResult($id, array $data)
    {
        return $this->examResultRepository->update($id, $data);
    }

    public function deleteResult($id)
    {
        return $this->examResultRepository->delete($id);
    }
}
