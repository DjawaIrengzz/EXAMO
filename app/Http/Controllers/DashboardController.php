<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\Exam;
use App\Models\User;
use App\Models\Questions;
use App\Models\ExamResult;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Helpers\BaseResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }
        switch ($user->role) {
            case 'admin':
                return $this->dashboardAdmin();

            case 'guru':
                return $this->dashboardGuru();

            case 'user':
                return $this->dashboardSiswa();

            default:
                abort(403);
        }
    }
    public function dashboardSiswa()
    {
        $exams = Exam::withCount('questions')
            ->where('status', 'active')
            ->latest()
            ->paginate(3)
            ->through(function ($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'question_count' => $exam->questions_count,
                ];
            });


        $completed = ExamResult::where('user_id', auth()->id())->count();

        return BaseResponse::OK([
            'exam' => $exams,
            'completed' => $completed,
        ], 'Dashboard Siswa retrieved successfully');
    }

    public function dashboardGuru()
    {
        $exams = Exam::withCount('questions')
            ->where('status', 'active')
            ->latest()
            ->paginate(3)
            ->through(function ($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'question_count' => $exam->questions_count,
                    'status' => $exam->status,
                ];
            });

        $ujianIds = Exam::where('created_by', auth()->id());
        $jumlah_exam = $ujianIds->count();
        $jumlah = ExamResult::whereIn('exam_id', $ujianIds)
            ->distinct('exam_id')
            ->count();
        $subscription = Subscription::where('user_id', auth()->id());

        $subcription_end = null;
        if ($subscription->status === 'active') {
            $subscription_end = Carbon::parse($subscription->end_date)->format('d F Y');
        }

        return BaseResponse::OK([
            'jumlah_exam' => $jumlah_exam > 0 ? $jumlah_exam : 0,
            'total_siswa' => $jumlah > 0 ? $jumlah : 0,
            'subscription_end' => $subcription_end,
            'exam' => $exams
        ], 'Dashboard Guru retrieved successfully');
    }

    public function dashboardAdmin()
    {
        $siswa = User::where('role', 'siswa');
        $guru = User::where('role', 'guru');
        $subcripton = Subscription::whereIn('plan_type', ['premium', 'enterprise'])
            ->where('status', 'active')
            ->count();

        $pengguna_active = User::where('status', true)
            ->count();
        $pengguna_inactive = User::where('status', false)
            ->count();
        $data = DB::table('subscriptions')
            ->select('plan_type', DB::raw('count(*) as total'))
            ->groupBy('plan_type')
            ->get();

        return BaseResponse::OK([
            'jumlah_siswa' => $siswa->count(),
            'jumlah_guru' => $guru->count(),
            'jumlah_subscription' => $subcripton,
            'pengguna_active' => $pengguna_active,
            'pengguna_inactive' => $pengguna_inactive,
            'subscription_data' => $data
        ], 'Dashboard Admin retrieved successfully');
    }

    public function show(string $id)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        switch ($user->role) {

            case 'guru':
                return $this->showExamGuru(id: $id);

            case 'siswa':
                return $this->showExamSiswa($id);

            default:
                abort(403);
        }
    }
    /**
     * Display the specified resource.
     */
    public function showExamSiswa(string $id)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return BaseResponse::NotFound('Exam not found');
        }

        $started_at = Carbon::parse($exam->start_time);
        $end_at = Carbon::parse($exam->end_time);

        return BaseResponse::OK([
            'started_at' => Carbon::parse($exam->start_time)->format('H:i'),
            'created_at' => $exam->created_at->format('d F Y'),
            'waktu_ujian' => $started_at->diffInMinutes($end_at) . ' menit',
            'total_soal' => $exam->questions()->count(),
            'kkm_score' => $exam->kkm_score,
            'deskripsi' => $exam->description
        ], 'Exam Siswa retrieved successfully');
    }

    public function showExamGuru(string $id)
    {
        $exam = Exam::find($id);

        if (!$exam) {
            return BaseResponse::NotFound('Exam not found');
        }

        $started_at = Carbon::parse($exam->start_time);
        $end_at = Carbon::parse($exam->end_time);

        // Ambil pertanyaan terkait dan paginasi 5
        $questions = Questions::where('exam_id', $exam->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->paginate(5);

        // Format data soal
        $formattedQuestions = $questions->through(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->question,
                'type' => $question->type,
                'image_urls' => $question->image
                    ? collect(json_decode($question->image))->map(function ($img) {
                        return asset('storage/' . $img);
                    })
                    : [],
                'options' => $question->type === 'multiple' ? json_decode($question->options, true) : null,
                'correct_answer' => in_array($question->type, ['essay', 'true_false']) ? $question->correct_answer : null,
                'explanation' => $question->explanation,
            ];
        });

        return BaseResponse::OK([
            'exam_info' => [
                'started_at' => $started_at->format('H:i'),
                'created_at' => $exam->created_at->format('d F Y'),
                'waktu_ujian' => $started_at->diffInMinutes($end_at) . ' menit',
                'total_soal' => $exam->questions()->count(),
                'kkm_score' => $exam->kkm_score,
                'deskripsi' => $exam->description,
            ],
            'questions' => $formattedQuestions,
        ], 'Exam Guru retrieved successfully');
    }
}
