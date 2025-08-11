<?php

namespace App\Repositories\Dashboard;

use App\Models\Exam;
use App\Models\User;
use App\Models\Questions;
use App\Models\ExamResult;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Repositories\Dashboard\Interface\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getSiswaDashboard()
    {
        $exams = Exam::withCount('questions')
            ->where('status', 'active')
            ->latest()
            ->paginate(3)
            ->through(fn($exam) => [
                'id' => $exam->id,
                'name' => $exam->name,
                'question_count' => $exam->questions_count,
            ]);

        $completed = ExamResult::where('user_id', auth()->id())->count();

        return compact('exams', 'completed');
    }

    public function getGuruDashboard()
    {
        $exams = Exam::withCount('questions')
            ->where('status', 'active')
            ->latest()
            ->paginate(3)
            ->through(fn($exam) => [
                'id' => $exam->id,
                'name' => $exam->name,
                'question_count' => $exam->questions_count,
                'status' => $exam->status,
            ]);

        $ujianIds = Exam::where('created_by', auth()->id());
        $jumlah_exam = $ujianIds->count();
        $jumlah = ExamResult::whereIn('exam_id', $ujianIds)->distinct('exam_id')->count();
        $subscription = Subscription::where('user_id', auth()->id())->first();

        $subscription_end = null;
        if ($subscription && $subscription->status === 'active') {
            $subscription_end = Carbon::parse($subscription->end_date)->format('d F Y');
        }

        return [
            'jumlah_exam' => $jumlah_exam,
            'total_siswa' => $jumlah,
            'subscription_end' => $subscription_end,
            'exam' => $exams
        ];
    }

    public function getAdminDashboard()
    {
        $siswa = User::where('role', 'siswa')->count();
        $guru = User::where('role', 'guru')->count();
        $subscription = Subscription::whereIn('plan_type', ['premium', 'enterprise'])
            ->where('status', 'active')
            ->count();
        $pengguna_active = User::where('status', true)->count();
        $pengguna_inactive = User::where('status', false)->count();
        $data = DB::table('subscriptions')
            ->select('plan_type', DB::raw('count(*) as total'))
            ->groupBy('plan_type')
            ->get();

        return compact('siswa', 'guru', 'subscription', 'pengguna_active', 'pengguna_inactive', 'data');
    }

    public function findExamSiswa(string $id)
    {
        $exam = Exam::find($id);
        if (!$exam) return null;

        $started_at = Carbon::parse($exam->start_time);
        $end_at = Carbon::parse($exam->end_time);

        return [
            'started_at' => $started_at->format('H:i'),
            'created_at' => $exam->created_at->format('d F Y'),
            'waktu_ujian' => $started_at->diffInMinutes($end_at) . ' menit',
            'total_soal' => $exam->questions()->count(),
            'kkm_score' => $exam->kkm_score,
            'deskripsi' => $exam->description
        ];
    }

    public function findExamGuru(string $id)
    {
        $exam = Exam::find($id);
        if (!$exam) return null;

        $started_at = Carbon::parse($exam->start_time);
        $end_at = Carbon::parse($exam->end_time);

        $questions = Questions::where('exam_id', $exam->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->paginate(5)
            ->through(fn($question) => [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'image_urls' => $question->image
                    ? collect(json_decode($question->image))->map(fn($img) => asset('storage/' . $img))
                    : [],
                'options' => $question->type === 'multiple' ? json_decode($question->options, true) : null,
                'correct_answer' => in_array($question->type, ['essay', 'true_false']) ? $question->correct_answer : null,
                'explanation' => $question->explanation,
            ]);

        return [
            'exam_info' => [
                'started_at' => $started_at->format('H:i'),
                'created_at' => $exam->created_at->format('d F Y'),
                'waktu_ujian' => $started_at->diffInMinutes($end_at) . ' menit',
                'total_soal' => $exam->questions()->count(),
                'kkm_score' => $exam->kkm_score,
                'deskripsi' => $exam->description,
            ],
            'questions' => $questions
        ];
    }
}
