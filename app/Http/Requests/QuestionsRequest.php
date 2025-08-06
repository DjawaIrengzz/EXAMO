<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->has('questions')) {
            return [
                'questions' => 'required|array',
                'questions.*.exam_id' => 'required|exists:exams,id',
                'questions.*.question' => 'required|string',
                'questions.*.type' => 'required|in:multiple,essay,true_false,image',
                'questions.*.options' => 'nullable|array',
                'questions.*.options.*' => 'string',
                'questions.*.correct_answer' => 'required|string',
                'questions.*.explanation' => 'nullable|string',
                'questions.*.image' => 'nullable',
                'questions.*.image.*' => 'string',
                'questions.*.order' => 'nullable|integer',
                'questions.*.is_active' => 'required|boolean',
            ];
        }

        return [
            'question' => 'required|string',
            'type' => 'required|in:multiple,essay,true_false,image',
            'options' => 'nullable|array',
            'options.*' => 'string',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'image' => 'nullable',
            'image.*' => 'string',
            'order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $inputs = $this->has('questions') ? $this->input('questions') : [$this->all()];

            foreach ($inputs as $index => $q) {
                $type = $q['type'] ?? null;
                $options = $q['options'] ?? [];
                $correct = $q['correct_answer'] ?? null;

                // Common: Validasi options harus string
                if (is_array($options)) {
                    foreach ($options as $optKey => $optVal) {
                        if (!is_string($optVal) || trim($optVal) === '') {
                            $validator->errors()->add("questions.$index.options.$optKey", 'Setiap opsi harus berupa string dan tidak boleh kosong.');
                        }
                    }
                }

                if ($type === 'multiple') {
                    if (count($options) < 3) {
                        $validator->errors()->add("questions.$index.options", 'Minimal 3 opsi untuk multiple choice.');
                    }

                    if (empty($correct)) {
                        $validator->errors()->add("questions.$index.correct_answer", 'Correct answer wajib diisi untuk multiple choice.');
                    } elseif (!array_key_exists($correct, $options)) {
                        $validator->errors()->add("questions.$index.correct_answer", 'Correct answer harus cocok dengan key dari options.');
                    }
                }

                if ($type === 'image') {
                    if (count($options) < 3) {
                        $validator->errors()->add("questions.$index.options", 'Minimal 3 opsi untuk soal gambar.');
                    }

                    if (empty($correct)) {
                        $validator->errors()->add("questions.$index.correct_answer", 'Correct answer wajib diisi untuk soal gambar.');
                    } elseif (!array_key_exists($correct, $options)) {
                        $validator->errors()->add("questions.$index.correct_answer", 'Correct answer harus cocok dengan key dari options.');
                    }
                }

                if (in_array($type, ['essay', 'true_false'])) {
                    if (empty($correct)) {
                        $validator->errors()->add("questions.$index.correct_answer", 'Correct answer wajib diisi untuk tipe ' . $type);
                    }

                    if (!empty($options)) {
                        $validator->errors()->add("questions.$index.options", 'Options tidak boleh diisi untuk tipe ' . $type);
                    }
                }

                // Validasi default jika type tidak dikenali
                if (!in_array($type, ['multiple', 'essay', 'true_false', 'image'])) {
                    $validator->errors()->add("questions.$index.type", 'Tipe soal tidak dikenali.');
                }
            }
        });
    }
}
