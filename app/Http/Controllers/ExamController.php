<?php

namespace App\Http\Controllers;

use App\Helpers\BaseResponse;
use App\Http\Requests\Exam\ExamRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use App\Services\Interfaces\ExamServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    public function __construct(
        protected ExamServiceInterface $examService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $data = $this->examService->list();

            if ($data instanceof LengthAwarePaginator) {
                return BaseResponse::success($data, 'Daftar ujian berhasil diambil', 200, true);
            }

            return BaseResponse::success($data, 'Daftar ujian berhasil diambil', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return BaseResponse::error('Gagal mengambil daftar ujian', 500);
        }
    }

    public function store(ExamRequest $request): JsonResponse
    {
        try {
            $exam = $this->examService->create($request->validated());
            return BaseResponse::success($exam, 'Ujian berhasil dibuat', 201);
        } catch (\Throwable $th) {
            Log::error($th);
            return BaseResponse::error('Gagal membuat ujian', 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $exam = $this->examService->find($id);

            if (!$exam) {
                return BaseResponse::error('Ujian tidak ditemukan', 404);
            }

            return BaseResponse::success($exam, 'Detail ujian berhasil diambil', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return BaseResponse::error('Gagal mengambil detail ujian', 500);
        }
    }

    public function update(UpdateExamRequest $request, $id): JsonResponse
    {
        try {
            $exam = $this->examService->update($id, $request->validated());
            return BaseResponse::success($exam, 'Ujian berhasil diupdate', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return BaseResponse::error('Gagal mengupdate ujian', 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->examService->delete($id);
            return BaseResponse::success(null, 'Ujian berhasil dihapus', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return BaseResponse::error('Gagal menghapus ujian', 500);
        }
    }
}
