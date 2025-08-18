<?php
namespace App\Exports\Contracts;
interface ExamResultExporterInterface
{
    public function export($examResults);
}