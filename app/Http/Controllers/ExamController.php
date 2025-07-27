<?php

namespace App\Http\Controllers;
use App\Http\Requests\Exam\ExamRequest;
use App\Http\Requests\Exam\UpdateExamRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
class ExamController extends Controller
{
    public function index(){
        $exams = Exam::with(['category:id,name', 'creator:id,name,email'])->latest()->paginate(10);
        return response()->json($exams);
    }
    public function available(Request $request){
        $user = $request->user();
        $available = Exam::with('category:id,name')->where('status' , 'aktif') -> whereDoesntHave('userExams', function($query) use ($user){
            $query->where('user_id', $user->id);
        })-> latest()->paginate(10);

        return response()->json($available);
    }
    public function attachToExam(Request $request)
{
    $data = $request->validate([
        'exam_id' => 'required|exists:exams,id',
        'question_ids' => 'required|array',
        'question_ids.*' => 'exists:questions,id'
    ]);

    foreach ($data['question_ids'] as $qid) {
        DB::table('exam_question')->insert([
            'exam_id' => $data['exam_id'],
            'question_id' => $qid,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return response()->json(['message' => 'Questions attached to exam successfully']);
}


    
    //patch
    public function partialUpdate(Request $request, $id)
{
    $exam = Exam::findOrFail($id);
    $this->authorize('update', $exam);

    $validated = $request->validate([
        'title' => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'status' => 'sometimes|in:draft,published,archived',
        'shuffle_question' => 'sometimes|boolean',
        'shuffle_option' => 'sometimes|boolean',
        'show_result' => 'sometimes|boolean',
        'max_attempts' => 'sometimes|integer|min:1',
    ]);

    $exam->update($validated);

    return response()->json([
        'message' => 'Ujian berhasil diperbarui sebagian',
        'exam' => $exam
    ]);
}

    public function store(ExamRequest $request){
        $validated = $request->validated();
            
        $validated['token'] = Str::upper(Str::random(6));
        $validated['created_by'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['shuffle_question'] = $request->boolean('shuffle_question');
        $validated['shuffle_option'] = $request->boolean('shuffle_option');
        $validated['show_result'] = $request->boolean('show_result');
        $validated['max_attempts'] = $validated['max_attempts'] ?? 1;
        $exam = Exam::create($validated);
        return response()->json(['Message' => 'Ujian telah dibuat', 'exam' =>$exam],201);
    }
    public function show($id){
        $exam = Exam::with(['category', 'creator', 'questions'])->findOrFail($id);
        return response()->json($exam);
    }
    public function update(UpdateExamRequest $request, $id){
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);
        $validated = $request->validated();
        $exam->update($validated);
        return response()->json(['Message' => 'Ujian telah diperbarui', 'exam' =>$exam]);
    }
}
