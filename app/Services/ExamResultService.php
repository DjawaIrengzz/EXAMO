<?php

namespace App\Services;


use App\Models\UserExam;
use App\Services\Interfaces\ExamResultServiceInterface;
use App\Repositories\Interfaces\ExamResultRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExamResultService implements ExamResultServiceInterface
{
    protected $examResultRepository;

    public function __construct(ExamResultRepositoryInterface $examResultRepository)
    {
        $this->examResultRepository = $examResultRepository;
    }


    public function getAllResults(array $filters = []):LengthAwarePaginator{

        return $this->examResultRepository->getAll();
    }


    public function getResultsByUserId(int $userId, $filters = []):LengthAwarePaginator

    {
        return $this->examResultRepository->getResultsByUserId($userId);
    }


    public function getResultsByExamId(int $examId, $filters = []):LengthAwarePaginator
   {
        return $this->examResultRepository->getResultsByExamId($examId);
    }


    public function storeResult(array $data):UserExam

    {
        return $this->examResultRepository->store($data);
    }


    public function updateResult($id, array $data):UserExam

    {
        return $this->examResultRepository->update($id, $data);
    }


    public function deleteResult($id):bool

    {
        return $this->examResultRepository->delete($id);
    }
}
