<?php
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserAnswerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaceAuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\DashboardController;


// | Semua route di sini memakai:
// | - auth:sanctum
// | - throttle:api  (60 req/min per user/IP)
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
});

Route::middleware(['throttle:api'])->group(function () {
    // Public endpoints (no auth)
    Route::post('/login',            [AuthController::class, 'login']);
    Route::post('/register',         [AuthController::class, 'register']);
    Route::post('/forgot-password',  [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',   [AuthController::class, 'resetPassword']);

    // Protected: harus login
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::put('/update-password',  [AuthController::class, 'update']);

        // Admin-only
        Route::middleware('role:admin')->group(function () {
            Route::post('/register/admin', [AuthController::class, 'registerAdmin']);

            // User management
            Route::get('/users',                      [AuthController::class, 'getAllUsers']);
            Route::get('/users/{role}',               [AuthController::class, 'getUsersByRole']);
            Route::put('/users/{userId}/role',        [AuthController::class, 'updateUserRole']);
            Route::put('/users/{userId}/toggle-status',[AuthController::class, 'toggleUserStatus']);

            // Guru registration approval
            Route::get('/guru/pending',               [AuthController::class, 'getPendingGuruRegistrations']);
            Route::put('/guru/{userId}/approve',      [AuthController::class, 'approveGuruRegistration']);
            Route::delete('/guru/{userId}/reject',    [AuthController::class, 'rejectGuruRegistration']);
        });

        // Guru-only
        Route::middleware('role:guru')->prefix('guru')->group(function () {
            Route::apiResource('exams',           ExamController::class);
            Route::apiResource('exams.questions', QuestionController::class);
            Route::apiResource('categories', CategoryController::class)
                 ->shallow();
        });

        // User-only (siswa)
        Route::middleware('role:user')->group(function () {
            Route::post('categories',    [CategoryController::class, 'indexActive']);
            Route::post('categories/{category:slug}',    [CategoryController::class, 'showBySlug']);
            Route::post('categories/{categories}',    [CategoryController::class, 'show']);
            Route::post('exams/{exam}/answers',    [UserAnswerController::class, 'store']);
            Route::get('exams',                    [ExamController::class, 'available']);
            Route::get('exam/{exam}',              [ExamController::class, 'show']);
            Route::get('exam',                     [ExamController::class, 'index']);
            Route::get('questions',                [QuestionController::class, 'index']);
            Route::get('questions/{question}',     [QuestionController::class, 'show']);
            Route::apiResource('options',         SystemSettingController::class);
        });
    });
});
