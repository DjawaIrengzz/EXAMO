<?php

use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\Console\Question\Question;

class BackSoalService{
    /**
     * Ambil daftar soal sesuai filter & pencarian, dengan pagination.
     *
     * @param  array  
     * @param  string|null  
     * @param  bool  
     * @param  int  
     * @return LengthAwarePaginator
     */
    public function list(array $filters =[], ?string $search=null, bool $shuffle = false, int $perPage=15): LengthAwarePaginator{
        $query = Question::query()->where('is_active', true);

        if(!empty($filters['exam_id'])){
            $query->where('exam_id', $filters['exam_id']);
        }
        if(!empty($filters['category_id'])){
            $query->whereHas('exam',fn($q) =>$q->where('category_id', $filters['category_id']));
        }
        if($search){
            $query->where('question', 'like', "%{$search}%" );
        }
        if($shuffle){
            $query->inRandomOrder();
        } else {
            $query->orderBy('created_at, desc');
        }

    }
}