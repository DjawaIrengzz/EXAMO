<?php
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserExamController;
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
use App\Http\Controllers\GuruController;
use App\Http\Controllers\AdminController;
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
        Route::middleware('role:admin')->prefix('admin')->group(function () {
             // 1. Manajemen Guru
            Route::get  ('gurus'       , [AdminController::class, 'index']);
            Route::get  ('gurus/{id}'  , [AdminController::class, 'show']);
            // (opsional: regenerate key, toggle active, dll)

            // 2. Subscription (Premium/Free)
            Route::get  ('subscriptions'          , [SubscriptionController::class, 'index']);
            Route::put  ('subscriptions/{id}'     , [SubscriptionController::class, 'update']);
            // (opsional: detail, delete…)

            // 3. System Settings
            Route::get  ('settings'               , [SystemSettingController::class, 'index']);
            Route::put  ('settings'               , [SystemSettingController::class, 'update']);

            // 4. Dashboard (statistik global)
            Route::get  ('dashboard'              , [DashboardController::class, 'index']);

            // 5. Audit Logs
            Route::get  ('audit-logs'             , [AuditLogController::class, 'index']);

            // Riwayat Transaksi
            Route::get('history', [AdminController::class, 'history']);

            // Ringkasan Keuangan & Grafik
            Route::get('finance', [AdminController::class, 'finance']);
        });

        });

        // 6. Guru-only
        Route::middleware('role:guru')->prefix('guru')->group(function () {
            Route::get('bank-soal', [QuestionController::class, 'bank']);

        // 7. Profile
             Route::get   ('/profile'           , [GuruController::class, 'index']);
            Route::get   ('/profile/{id}'      , [GuruController::class, 'show']);
            Route::put   ('/profile'      , [GuruController::class, 'update']);
            Route::post  ('/profile/avatar'    , [GuruController::class, 'updateAvatar']);
        
        // 8. Exam
            Route::apiResource('exams',           ExamController::class);
            Route::apiResource('exams.questions', QuestionController::class);

            Route::apiResource('categories', CategoryController::class)
                 ->shallow();
        });

        // 9. User-only (siswa)
        Route::middleware('role:user')->prefix('user')->group(function () {

            // 10. Profile
             Route::get   ('/profile'           , [SiswaController::class, 'index']);
            Route::get   ('/profile/{id}'      , [SiswaController::class, 'show']);
            Route::put   ('/profile'      , [SiswaController::class, 'update']);
            Route::post  ('/profile/avatar'    , [SiswaController::class, 'updateAvatar']);

            // 11. Category
            Route::get('categories',    [CategoryController::class, 'indexActive']);
            Route::get('categories/{category:slug}',    [CategoryController::class, 'showBySlug']);
            Route::get('categories/{categories}',    [CategoryController::class, 'show']);

            // 12. Jawaban User
            Route::post('exams/{exam}/answers',    [UserAnswerController::class, 'store']);

            // 13. Exam Behavior
             Route::post('{exam}/start', [UserExamController::class, 'start']);
             Route::get('{exam}/status', [UserExamController::class, 'status']);
            Route::post('{exam}/finish', [UserExamController::class, 'finish']);

            // 14. Exam viewing
            Route::get('exams',                    [ExamController::class, 'available']);
            Route::get('exam/{exam}',              [ExamController::class, 'show']);
            Route::get('exam',                     [ExamController::class, 'index']);

            // 15. Question viewing
            Route::get('questions',                [QuestionController::class, 'index']);
            Route::get('questions/{question}',     [QuestionController::class, 'show']);
            Route::apiResource('options',         SystemSettingController::class);

            Route::get('bank-soal', [QuestionController::class, 'bank']);
        });
    });

