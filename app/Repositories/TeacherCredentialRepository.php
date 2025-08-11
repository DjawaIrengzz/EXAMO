<?php

namespace App\Repositories;

use App\Models\TeacherCredential;
use App\Interfaces\TeacherCredentialRepositoryInterface;
use App\Models\User;

class TeacherCredentialRepository implements TeacherCredentialRepositoryInterface
{
    public function getCredentialByUser(User $user)
    {
        return TeacherCredential::where('user_id', $user->id)->first();
    }

    public function findByTeacherId($teacherId)
    {
        return TeacherCredential::where('teacher_id', $teacherId)->first();
    }

    public function create(array $data)
    {
        return TeacherCredential::create($data);
    }

    public function updateByTeacherId($teacherId, array $data)
    {
        $credential = $this->findByTeacherId($teacherId);
        if ($credential) {
            $credential->update($data);
            return $credential;
        }
        return null;
    }

    public function deleteByTeacherId($teacherId)
    {
        return TeacherCredential::where('teacher_id', $teacherId)->delete();
    }

    public function  getCredentialByTeacher($teacher)
    {
        return $teacher->credential()->first();
    }
}
