<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class ExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function prepareForValidation():void{
        if($this->has('shuffle_question')){
            $this->merge(['shuffle_question' => (bool) $this->input('shuffle_questions')]);
        }
        if($this->has('shuffle_options')){
            $this->merge(['shuffle_options'=>(bool) $this->input('shuffle_options')]);
        }
        $this->request->remove('token');
        $this->request->remove('created_by');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titles' => 'required|string|max:55',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:0',
            'kkm_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,aktif,nonaktif,berlangsung,selesai',
            'show_result' => 'nullable|boolean',
            'shuffle_question' => 'nullable|boolean',
            'shuffle_option' => 'nullable|boolean',
            'instructions' => 'nullable|string',
        ];
    }
    public function messages():array
    {
            return [
        'titles.required' => 'Judul ujian wajib diisi.',
        'titles.string' => 'Judul ujian harus berupa teks.',
        'titles.max' => 'Judul ujian maksimal 55 karakter.',

        'description.string' => 'Deskripsi ujian harus berupa teks.',

        'category_id.exists' => 'Kategori yang dipilih tidak ditemukan.',

        'start_time.required' => 'Waktu mulai ujian wajib diisi.',
        'start_time.date' => 'Format waktu mulai ujian tidak valid.',

        'end_time.required' => 'Waktu selesai ujian wajib diisi.',
        'end_time.date' => 'Format waktu selesai ujian tidak valid.',
        'end_time.after' => 'Waktu selesai ujian harus setelah waktu mulai.',

        'duration_minutes.required' => 'Durasi ujian wajib diisi.',
        'duration_minutes.integer' => 'Durasi ujian harus berupa angka.',
        'duration_minutes.min' => 'Durasi ujian minimal 1 menit.',

        'total_questions.required' => 'Jumlah soal wajib diisi.',
        'total_questions.integer' => 'Jumlah soal harus berupa angka.',
        'total_questions.min' => 'Jumlah soal minimal 0.',

        'kkm_score.required' => 'Nilai KKM wajib diisi.',
        'kkm_score.integer' => 'Nilai KKM harus berupa angka.',
        'kkm_score.max' => 'Nilai KKM maksimal 100.',
        'kkm_score.min' => 'Nilai KKM Minimal 1.',

        'max_attempts.integer' => 'Jumlah percobaan harus berupa angka.',
        'max_attempts.min' => 'Jumlah percobaan minimal 1.',

        'status.in' => 'Status ujian harus salah satu dari: draft, aktif, nonaktif, berlangsung, atau selesai.',

        'show_result.boolean' => 'Opsi tampilkan hasil harus bernilai true atau false.',
        'shuffle_question.boolean' => 'Opsi acak soal harus bernilai true atau false.',
        'shuffle_option.boolean' => 'Opsi acak pilihan harus bernilai true atau false.',

        'instructions.string' => 'Instruksi ujian harus berupa teks.',
            ];
    }
}
