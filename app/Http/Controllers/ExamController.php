<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(){
        $exams = Exam::all('category', 'creator')->latest()->get();
        return response()->json($exams);
    }
    public function store(Request $request){
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'creator_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:0',
            'kkm_score' => 'required|integer|max:100',
            'status' => 'in:draft,aktif,nonaktif,berlangsung,selesai',
            'show_result' => 'boolean',
            'shuffle_question' => 'boolean',
            'shuffle_option' => 'boolean',
            'max_attempts' => 'integer|min:1',
            'instructions' => 'nullable|string',
        ]);
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
    public function update(Request $request, $id){
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:0',
            'kkm_score' => 'required|integer|max:100',
            'status' => 'in:draft,aktif,nonaktif,berlangsung,selesai',
        ]);
        $exam->update($validated);
        return response()->json(['Message' => 'Ujian telah diperbarui', 'exam' =>$exam]);
    }
}
