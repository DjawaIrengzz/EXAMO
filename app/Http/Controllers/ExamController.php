<?php

namespace App\Http\Controllers;
use App\Http\Requests\Exam\ExamRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use App\Services\Interfaces\ExamServiceInterface;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\UserExam;
class ExamController extends Controller
{
      public function __construct(
        protected ExamServiceInterface $examService
    ) {}

    public function index()
    {
        return $this->examService->list();
    }

    public function store(ExamRequest $request)
    {
        return $this->examService->create($request->validated());
    }

    public function show($id)
    {
        return $this->examService->find($id);
    }

    public function update(ExamRequest $request, $id)
    {
        return $this->examService->update($id, $request->validated());
    }

    public function destroy($id)
    {
        return $this->examService->delete($id);
    }
}
