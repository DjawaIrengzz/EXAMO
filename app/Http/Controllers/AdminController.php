<?php

namespace App\Http\Controllers;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Helpers\BaseResponse;

use Illuminate\Support\Carbon;
class AdminController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $gurus = User::where('role', 'guru')
        ->select('id', 'name', 'email', 'is_active', 'created_at')
        ->orderBy('name') 
        ->paginate($request->query('per_page', 10));

        return response()->json($gurus);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function history(Request $request)
    {
        $perPage = $request->query('per_page',10);
        $status = $request -> query('status');

        $query = Subscription::with('user')
        ->orderBy('created_at', 'desc');
        if ($status) {
            $query->where('status' .$status);
        }
        $histories = $query ->paginate($perPage);
        return response()->json($histories);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $guru = User::where('role', 'guru')
        ->with('currentAccessToken')
        ->findOrFail($id);

        $tokenModel = $guru->currentAccessToken();
        $teacherKey = $tokenModel->plainTextToken;

        return response()->json
        ([
            'id' => $guru->id,
            'name' =>$guru ->name,
            'email' => $guru->email,
            'phone_number' => $guru->phone_number,
            'role' => $guru->role,
            'is_active' => $guru->is_active,
            'created_at' => $guru->created_at,
            'teacher_id' => 'Guru'. $guru->id,
            'teacher_key' =>$teacherKey,

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function keuangan(Request $request)
    {
        $year = $request -> query('year', Carbon::now()->year);
        $totalIncome = Subscription::where('status', 'paid')
        ->whereYear('created_at', $year)
        ->sum('amount');

        $totalExpenses = Expense::whereYear('date', $year)
        ->sum('amount');

        $lababersih =$totalIncome - $totalExpenses;
         
        $bulanan = [];
        for ($b = 1; $m <= 12; $m++){
            $income = Subscription::where('status', 'paid')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $m)
            ->sum('amount');

            $expense = Expense::whereYear('date', $year)
            ->whereMonth('date', $m)
            ->sum('amount');

            $bulanan[]= [
                'month' => Carbon::create($year, $m)->format('M'),
                'income' => $income,
                'expense' =>$expense,
            ];
        }
        return response()->json([
            'total_income' => $totalIncome,
            'total_expense' => $totalExpenses,
            'net_profit' => $lababersih,
            'chart_date' => $bulanan,
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
