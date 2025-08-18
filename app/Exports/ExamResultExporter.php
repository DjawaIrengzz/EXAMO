<?php

namespace   App\Exports;

use App\Exports\Contracts\ExamResultExporterInterface;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;

class ExamResultExporter implements ExamResultExporterInterface, FromArray
{
    private $examResults;
    public function export ($examResults){
        $this-> examResults=$examResults;
        return Excel::download($this, 'hasil-ujian.xlsx');
    }
    public function array (): array{
        $data[] = ['Nama Siswa', 'Nama Ujian', 'Nilai' , 'Tanggal'];
        foreach ($this->examResults as $result){
            $data[] = [
                    $result -> user -> name,
                    $result -> exam -> name,
                    $result -> score,
                    $result -> created_at->format('d-m-Y H:i'),
            ];
            return $data;
        }
    }
}