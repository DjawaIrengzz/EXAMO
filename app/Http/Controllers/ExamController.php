<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;

use App\Http\Requests\Exam\ExamRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use App\Services\ExamService;

use App\Services\Interfaces\ExamServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    public function __construct(
        protected ExamServiceInterface $examService
    ) {
        $this->service = $examService;
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->examService->getAllExams();

            if ($data instanceof LengthAwarePaginator) {
                return ResponseHelper::success($data, 'Daftar ujian berhasil diambil', 200, true);
            }

            return ResponseHelper::success($data, 'Daftar ujian berhasil diambil', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return ResponseHelper::error('Gagal mengambil daftar ujian', 500);
        }
    }

    public function store(ExamRequest $request): JsonResponse
    {
        try {
            $exam = $this->examService->createExam($request->validated());
            return ResponseHelper::success($exam, 'Ujian berhasil dibuat', 201);
        } catch (\Throwable $e) {
    return ResponseHelper::error($e->getMessage(), 500); // sementara biar tau error asli
}
    }

    public function show($id): JsonResponse
    {
        try {
            $exam = $this->examService->getExamById($id);

            if (!$exam) {
                return ResponseHelper::error('Ujian tidak ditemukan', 404);
            }

            return ResponseHelper::success($exam, 'Detail ujian berhasil diambil', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return ResponseHelper::error('Gagal mengambil detail ujian', 500);
        }
    }

    public function update(UpdateExamRequest $request, $id): JsonResponse
    {
        try {
            $exam = $this->examService->updateExam($id, $request->validated());
            return ResponseHelper::success($exam, 'Ujian berhasil diupdate', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return ResponseHelper::error('Gagal mengupdate ujian', 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->examService->delete($id);
            return ResponseHelper::success(null, 'Ujian berhasil dihapus', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return ResponseHelper::error('Gagal menghapus ujian', 500);
        }
    }
}
