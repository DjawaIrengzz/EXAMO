<?php

namespace App\Services;

use App\Helpers\BaseResponse;
use App\Interfaces\TeacherCredentialRepositoryInterface;

class TeacherCredentialService
{
    protected $teacherCredentialRepository;

    public function __construct(TeacherCredentialRepositoryInterface $repository)
    {
        $this->teacherCredentialRepository = $repository;
    }

    public function getApiKey($teacher)
    {
        return  $this->teacherCredentialRepository->getCredentialByTeacher($teacher);

    }
}
