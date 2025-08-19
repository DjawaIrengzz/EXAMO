<?php
namespace App\Services;

use App\Services\Interfaces\ExamServiceInterface;
use App\Repositories\Interfaces\ExamRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ExamService implements ExamServiceInterface
{
    protected $examRepository;
    protected int $maxTokenAttempt=5;
    public function __construct(ExamRepositoryInterface $examRepository)
    {
        $this->examRepository = $examRepository;
    }

    public function getAllExams()
    {
        return $this->examRepository->getAll();
    }

    public function getExamById($id)
    {
        return $this->examRepository->findById($id);
    }

    public function createExam(array $data)
    {
        unset($data['token'], $data['created_by'], $data['id']);

        if(array_key_exists('shuffle_questions', $data)){
            $data['shuffle_question'] = (bool) $data['shuffle_question'];
        }
        if(array_key_exists('shuffle_options', $data)){
            $data['shuffle_option'] = (bool) $data['shuffle_options'];
        }
        $userId=Auth::id();
        if(!$userId){
            throw new \RuntimeException('User harus terautentikasi untuk membuat exam');
        }
        $data['created_by'] = $userId;
        $attempt = 0;
       do {
            $attempt++;
            $data['token'] = strtoupper(Str::random(10)); 

            try {
                return $this->examRepository->create($data);
            } catch (QueryException $e) {
                $driverErrorCode = $e->errorInfo[1] ?? null;

                if ($driverErrorCode === 1062 && $attempt < $this->maxTokenAttempt) {
                    usleep(50000);
                    continue;
                }

                throw $e;
            }
        } while ($attempt < $this->maxTokenAttempt);
            throw new \RuntimeException('Tidak dapat membuat token untuk exam setelah percobaan');
        }
    

    public function updateExam($id, array $data)
    {
        return $this->examRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->examRepository->delete($id);
    }
}
