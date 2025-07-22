<?php

namespace App\Http\Controllers;
use App\Http\Requests\ExamRequest;
use App\Http\Requests\UpdateExamRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Exam;
class ExamController extends Controller
{
    public function index(){
        $exams = Exam::all('category', 'creator')->latest()->get();
        return response()->json($exams);
    }
    public function store(ExamRequest $request){
        $validated = $request->validate();
            
        $validated['token'] = Str::upper(Str::random(6));
        $validated['created_by'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'draft';
        $validated['shuffle_question'] = $request->has('shuffle_question');
        $validated['shuffle_option'] = $request->has('shuffle_option');
        $validated['show_result'] = $request->has('show_result');
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
        $validated = $request->validate();
        $exam->update($validated);
        return response()->json(['Message' => 'Ujian telah diperbarui', 'exam' =>$exam]);
    }
}
