<?php
namespace App\Services;

use App\Repositories\Interfaces\ExamResultRepositoryInterface;
use App\Exports\Contracts\ExamResultExporterInterface;
use ExamResultRepository;

class ExamResultExportService{
    private $repo;
    private $exporter;
    public function __construct(ExamResultRepository $repo, ExamResultExporterInterface $exporter){
        $this -> repo = $repo;
        $this->exporter = $exporter;
    }
    public function exportByExam($examId){
        $results = $this->repo->getByExamId($examId);
        return $this->exporter->export($results);
    }
}