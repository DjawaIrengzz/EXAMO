<?php

namespace App\Http\Controllers;


use App\Models\Exam;
use App\Models\Questions;
use Illuminate\Http\Request;
use App\Helpers\BaseResponse;
use App\Services\BankSoalService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\BankRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\QuestionsRequest;
use Symfony\Component\Console\Question\Question;
use App\Services\Interface\QuestionServiceInteface;

class QuestionController extends Controller
{
    protected $questionService;
    protected BankSoalService $bank;
    public function __construct(BankSoalService $bank)
    {
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

        $questions = $query->paginate(10);
        return BaseResponse::OK($questions, $questions->count() > 0 ? 'Question Retrived successfully' : 'No questions found');
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
    public function detach(Exam $exam, $questionId): JsonResponse
    {

        if (! $exam->bankQuestions()->where('question_id', $questionId)->exists()) {
            return response()->json([
                'message' => "Question dengan ID {$questionId} Tidak tersambung"
            ], 404);
        }

        $exam->bankQuestions()->detach($questionId);

        return response()->json([
            'message' => "Question {$questionId} Diputuskan dengan sukses"
        ], 200);
    }
    public function bank(BankRequest $request)
    {
        $data = $request->validated();

        $filters = ['exam_id' => $data['exam_id'] ?? null, 'category_id' => $data['category_id'] ?? null];
        $search = $data['search'] ?? null;
        $shuffle = isset($data['shuffle']) && $data['shuffle'] == '1';
        $perPage = $data['per_page'] ?? 15;
        $paginator = $this->bank->list($filters, $search, $shuffle, $perPage);
        return response()->json($paginator);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuestionsRequest $request)
    {
        $isMultiple = $request->has('questions');
        $questionsInput = $isMultiple ? $request->questions : [$request->all()];
        $questionFiles = $request->file('questions') ?? [$request->file()];

        $storedQuestions = [];

        foreach ($questionsInput as $index => $questionData) {
            $question = new Questions();
            $question->question = $questionData['question'];
            $question->type = $questionData['type'];
            $question->correct_answer = $questionData['correct_answer'] ?? null;
            $question->explanation = $questionData['explanation'] ?? null;
            $question->order = $questionData['order'] ?? null;
            $question->is_active = $questionData['is_active'];

            $optionData = [];
            $optionsInput = $questionData['options'] ?? [];

            foreach ($optionsInput as $key => $value) {
                if ($value !== null && $value !== '') {
                    $optionData[] = [
                        'type' => 'text',
                        'value' => $value
                    ];
                } else {
                    $optionFile = $questionFiles[$index]['options'][$key] ?? null;

                    if ($optionFile && $optionFile->isValid()) {
                        $path = $optionFile->store('questions/options', 'public');
                        $optionData[] = [
                            'type' => 'image',
                            'value' => $path
                        ];
                    }
                }
            }

            $question->options = json_encode($optionData);

            $uploadedImages = $questionFiles[$index]['image'] ?? [];
            $imagePaths = [];

            if (!is_array($uploadedImages)) {
                $uploadedImages = [$uploadedImages];
            }

            foreach ($uploadedImages as $image) {
                if ($image && $image->isValid()) {
                    $path = $image->store('questions/images', 'public');
                    $imagePaths[] = $path;
                }
            }

            $question->image = json_encode($imagePaths);
            $question->save();
            $storedQuestions[] = $question;
        }

        return BaseResponse::Created(
            $isMultiple ? $storedQuestions : $storedQuestions[0],
            'question berhasil dibuat'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Questions::find($id);

        if (!$data) {
            return BaseResponse::NotFound('Question not found');
        }

        return BaseResponse::OK($data, 'Question retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuestionsRequest $request, string $id)
{
    $question = Questions::find($id);
    if (!$question) {
        return BaseResponse::NotFound('Question not found');
    }

    $validated = $request->validated();

    // Handle file options & images jika ada
    if ($request->hasFile('options')) {
        $optionData = [];
        foreach ($request->file('options') as $key => $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('questions/options', 'public');
                $optionData[] = ['type' => 'image', 'value' => $path];
            }
        }
        $validated['options'] = json_encode($optionData);
    } elseif (isset($validated['options']) && is_array($validated['options'])) {
        $validated['options'] = json_encode(
            collect($validated['options'])->map(fn($v) => ['type' => 'text', 'value' => $v]),
            JSON_THROW_ON_ERROR
        );
    }

    // Handle image upload jika ada
    if ($request->hasFile('image')) {
        $paths = [];
        $images = is_array($request->file('image')) ? $request->file('image') : [$request->file('image')];

        foreach ($images as $img) {
            if ($img && $img->isValid()) {
                $paths[] = $img->store('questions/images', 'public');
            }
        }

        $validated['image'] = json_encode($paths, JSON_THROW_ON_ERROR);
    }

    $question->update($validated);

    return BaseResponse::OK($question, 'Question updated successfully');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = Questions::find($id);
        if (!$question) {
            return BaseResponse::NotFound('Question not found');
        }
        $question->delete();

        return BaseResponse::NoContent();
    }
}
