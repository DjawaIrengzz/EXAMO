<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\BaseResponse;
use App\Http\Resources\TeacherCredentialResource;
use App\Services\TeacherCredentialService;

class TeacherCredentialController extends Controller
{
    protected $teacherCredentialService;

    public function __construct(TeacherCredentialService $service)
    {
        $this->teacherCredentialService = $service;
    }

    public function showApiKey()
    {
        $teacher = auth()->user();
        $credential =  $this->teacherCredentialService->getApiKey($teacher);
        return new TeacherCredentialResource($credential);
    }
}
