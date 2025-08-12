<?php
use App\Models\UserExam;
use App\Models\ExamResult;
use App\Repositories\Interfaces\ExamResultRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
class ExamResultRepository{
public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->applyFilters(UserExam::query(), $filters)
                    ->latest()
                    ->paginate($filters['per_page'] ?? 10);
    }
    public function getByExamId($examId)
    {
        return ExamResult::with(['user', 'exam'])
        ->where('exam_id', $examId)
        ->get();
    }

    public function getByUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $filters['user_id'] = $userId;
        return $this->getAll($filters);
    }

    public function getByExam(int $examId, array $filters = []): LengthAwarePaginator
    {
        $filters['exam_id'] = $examId;
        return $this->getAll($filters);
    }

    public function create(array $data): UserExam
    {
        return UserExam::create($data);
    }

    public function update(int $id, array $data): UserExam
    {
        $record = UserExam::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id): bool
    {
        return (bool) UserExam::destroy($id);
    }

    protected function applyFilters($query, array $filters)
    {
        return $query
            ->when(!empty($filters['user_id']), fn($q) => $q->where('user_id', $filters['user_id']))
            ->when(!empty($filters['exam_id']), fn($q) => $q->where('exam_id', $filters['exam_id']));
    }

}