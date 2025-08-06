<?php
namespace App\Services;

use App\Services\Interfaces\ExamServiceInterface;
use App\Repositories\Interfaces\ExamRepositoryInterface;

class ExamService implements ExamServiceInterface
{
    protected $examRepository;

    public function __construct(ExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function getAllExams()
    {
        return $this->examRepository->getAll();
    }

    public function getExamById($id)
    {
        return $this->examRepository->findById($id);
    }

    public function createExam(array $data)
    {
        return $this->examRepository->create($data);
    }

    public function updateExam($id, array $data)
    {
        return $this->examRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->examRepository->delete($id);
    }
}
