<?php
namespace App\Repositories;

use App\Models\Exam;
use App\Repositories\Interfaces\ExamRepositoryInterface;

class ExamRepository implements ExamRepositoryInterface
{
    public function getAll()
    {
        return Exam::all();
    }

    public function findById($id)
    {
        return Exam::findOrFail($id);
    }

    public function create(array $data)
    {
        return Exam::create($data);
    }

    public function update($id, array $data)
    {
        $exam = Exam::findOrFail($id);
        $exam->update($data);
        return $exam;
    }

    public function delete($id)
    {
        Exam::destroy($id);
    }
}
