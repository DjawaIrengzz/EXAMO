<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/password/reset/{token}', function (Request $request, $token) {
    return response()->json([
        'message' => 'Reset route OK',
        'token' => $token,
        'email' => $request->email,
    ]);
})->name('password.reset');


