<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\ExamResult;
use App\Models\Questions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

            case 'siswa':
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

        return response()->json([
            'exam' => $exams,
            'completed' => $completed,
        ]);
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

        $jumlah_exam = Exam::where('created_by', auth()->id())->count();
        $ujianIds = Exam::where('user_id', auth()->id())
            ->pluck('exam_id');
        $jumlah = ExamResult::whereIn('exam_id', $ujianIds)
            ->distinct('exam_id')
            ->count();

        // Belum menampilakn bank soal

        return response()->json([
            'jumlah_exam' => $jumlah_exam > 0 ? $jumlah_exam : 0,
            'total_siswa' => $jumlah > 0 ? $jumlah : 0,
            'exam' => $exams
        ]);
    }

    public function dashboardAdmin()
    {
        return response()->json([
            'message' => 'Dashboard Admin'
        ]);
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
        $exam = Exam::findOrFail($id);
        $started_at = Carbon::parse($exam->start_time);
        $end_at = Carbon::parse($exam->end_time);

        return response()->json([
            'started_at' => Carbon::parse($exam->start_time)->format('H:i'),
            'created_at' => $exam->created_at->format('d F Y'),
            'waktu_ujian' => $started_at->diffInMinutes($end_at) . ' menit',
            'total_soal' => $exam->questions()->count(),
            'kkm_score' => $exam->kkm_score,
            'deskripsi' => $exam->description
        ]);
    }

    public function showExamGuru(string $id)
    {
        $exam = Exam::findOrFail($id);

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

        return response()->json([
            'exam_info' => [
                'started_at' => $started_at->format('H:i'),
                'created_at' => $exam->created_at->format('d F Y'),
                'waktu_ujian' => $started_at->diffInMinutes($end_at) . ' menit',
                'total_soal' => $exam->questions()->count(),
                'kkm_score' => $exam->kkm_score,
                'deskripsi' => $exam->description,
            ],
            'questions' => $formattedQuestions,
        ]);
    }
}
