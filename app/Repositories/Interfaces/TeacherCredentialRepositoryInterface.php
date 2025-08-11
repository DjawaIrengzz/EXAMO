<?php

namespace App\Interfaces;
use App\Models\User;

interface TeacherCredentialRepositoryInterface
{
    public function getCredentialByTeacher($teacher);
}
