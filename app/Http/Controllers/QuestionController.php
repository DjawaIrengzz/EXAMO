<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuestionsRequest;
use App\Models\Questions;
use App\Services\Interface\QuestionServiceInteface;
use Illuminate\Http\Request;
use Symfony\Component\Console\Question\Question;
use App\Services\BankSoalService;
use App\Http\Requests\BankRequest;

class QuestionController extends Controller
{
    protected $questionService;
    protected BankSoalService $bank;
    public function __construct(BankSoalService $bank){
        $this->bank = $bank;

    }
    
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Questions::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', '%' . $search . '%')
                    ->orWhereHas('exam', function ($q2) use ($search) {
                        $q2->where('titles', 'like', '%' . $search . '%');
                    });
            });
        }

        return response()->json([
            'data' => $query->paginate(10),
            'message' => 'Questions retrieved successfully',
            'status' => 200
        ]);
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
    public function bank(BankRequest $request){
        $data = $request ->validated();

        $filters = ['exam_id' => $data['exam_id'] ?? null, 'category_id' => $data['category_id'] ?? null];
        $search = $data ['search'] ?? null;
        $shuffle = isset($data['shuffle']) && $data['shuffle'] == '1' ;
        $perPage = $data['per_page'] ?? 15;
        $paginator = $this->bank->list($filters,$search,$shuffle,$perPage);
        return response()->json($paginator);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionsRequest $request)
    {   
         $validated = $request->validated();

    
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')
                                  ->store('questions', 'public');
    }


    $question = Questions::create($validated);

    return response()->json([
        'data'    => $question,
        'message' => 'Question created successfully',
        'status'  => 201
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
