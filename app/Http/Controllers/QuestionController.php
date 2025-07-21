<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request){
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'question' => 'required|string',
            'type' => 'in:multiple,essay,true_false',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);
        if ($validated['type'] === 'multiple'){
            if (empty($validated['options'])|| !is_array($validated['options'])){
                return response()->json(['error' => 'Opsi diharuskan banyak'], 422);
            }
        if(!isset($validated['correct_answer'])){
            return response()->json(['error' => 'Jawaban benar harus diisi untuk pertanyaan pilihan ganda'], 422);
        }
        $validated['options'] =json_encode($validated['options']);

    }else{
        $validated['options'] = null;
    }
    $validated['is_active'] = $request->has('is_active');
    $question = Question::create($validated);
    return response()->json([
        'message' => 'Sukses dibuat',
        'question' => $question,
    ]);
    }
}