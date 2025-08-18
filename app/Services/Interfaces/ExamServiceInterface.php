<?php
namespace App\Services\Interfaces;

interface ExamServiceInterface
{
    public function getAllExams() ;
    public function getExamById($id);
    public function createExam(array $data);
    public function updateExam($id, array $data);
    public function delete($id);
}