<?php

namespace App\Http\Controllers;


use App\Exports\ExamResultExporter;
use App\Http\Requests\StoreExamResultRequest;
use App\Models\ExamResult;
use App\Services\ExamResultExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

class ExamResultController extends Controller
{
    private $exportService;
    public function __contruct(ExamResultExportService $exportService){
        $this->exportService = $exportService;
    }
    public function index(Request $req){
       $results = $this -> exportService -> getAllResults($req->all());
       return response() -> json($results);
    }
    public function byExam(){
        $results = $this -> exportService -> updateResult()->byExam();
    }
    public function store(StoreExamResultRequest $request){
       $record = $this->exportService->storeResult($request->validated());
        return response()->json($record,201);
    }

    public function update(int $id, Request $req){
       $record = $this ->exportService -> updateResult($id, $req->all());
       return response() -> json($record);
}

}

