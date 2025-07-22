<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionsRequest;
use App\Models\Questions;
use App\Services\Interface\QuestionServiceInteface;
use Illuminate\Http\Request;
use Symfony\Component\Console\Question\Question;

class QuestionController extends Controller
{
    protected $questionService;

    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Questions::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', '%' . $search . '%')
                    ->orWhereHas('exam', function ($q2) use ($search) {
                        $q2->where('title', 'like', '%' . $search . '%');
                    });
            });
        }

        return response()->json([
            'data' => $query->paginate(10),
            'message' => 'Questions retrieved successfully',
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionsRequest $request)
    {   
        $validated = $request->validated();

        if(isset($validated['options'])){
            $validated['options'] = json_encode($validated['options']);
        }
        
        Questions::create($validated);

        return response()
            ->json([
                'message' => 'Question created successfully',
                'question' => $request->validated()
            ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Questions::findOrFail($id);

        return response()->json([
            'data' => $data,
            'message' => 'Question retrieved successfully',
            'status' => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuestionsRequest $request, string $id)
    {
        $question = Questions::findOrFail($id);
        $validated = $request->validated();

        if(isset($validated['options'])){
            $validated['options'] = json_encode($validated['options']);
        }

        $question->update($validated);

        return response()->json([
            'message' => 'Question updated successfully',
            'question' => $question
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Questions::findOrFail($id);
        $question->delete();

        return response()->json(null, 204);
    }
}
