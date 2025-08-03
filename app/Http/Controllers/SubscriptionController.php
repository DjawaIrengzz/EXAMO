<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubcriptionRequest;
use App\Http\Requests\UpdateSubcriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Plan;
class SubscriptionController extends Controller
{
    public function plan()
{
    $plans = Plan::all(['type','duration_months','price']);
    return response()->json($plans);
}
    public function index(Request $request){
        if(Gate::denies('viewAny', Subscription::class)){
            return response()->json(['message' => 'forbidden']);
        }
        $subscriptions = Subscription::with('user')
        ->latest()
        ->paginate($request->query('per_page', 10));
        return response()->json( $subscriptions);
        
    }
    public function subscription(Request $request){
        $user = Auth::user();
        $subs = Subscription::where('user_id', $user->id)
        ->orderBy('created_at','desc')
        ->paginate($request->query('per_page', 10));
    }

    public function store(StoreSubcriptionRequest $request){
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $subscription = Subscription::create($data);
        return response()->json( ['message'=> 'Subscription Created', 'subscription' => $subscription] );
    }

    public function show(Subscription $subscription){
        if (Gate::denies('view', $subscription)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $subscription->load('user');
        return response()->json($subscription);
    }

    public function update(UpdateSubcriptionRequest $request, Subscription $subscription){
        if(Gate::denies('update', $subscription)){
            return response()->json(['message'=> 'forbidden'],403);
        }
        $subscription->update($request->validated());

        return response()->json( ['message'=> 'Updated','subscription'=> $subscription] );
    }
    public function destroy(Subscription $subscription)
{
    Gate::authorize('delete', $subscription);
    $subscription->delete();
    return response()->json(['message' => 'Subscription deleted.']);
}
}
