<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\plan;
use App\Plans_users;
use App\User;

class PlanController extends Controller
{
    private function genetateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::all();

        return $plans ? response()->json($this->genetateResponse("success", $plans), 201) :
            response()->json($this->genetateResponse("failed", "could not fetch plans"), 401);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rate = new Plan(array(
            'plan' => $request->get('name'),
            'rate' => $request->get('rate'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
            'slug' => uniqid(),
            'user_id' => $request->get('userId'),
            'duration' => $request->get('duration'),
        ));

        if ($rate->save()) {
            $allPlans = Plan::all();
            $response = $this->genetateResponse("success", $allPlans);
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not add plan");
            return response()->json($response, 402);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $plan = Plan::whereId($request->get("planId"))->firstOrFail();

        $plan->plan = $request->get("plan");
        $plan->rate = $request->get("rate");
        $plan->duration = $request->get("duration");

        if ($plan->save()) {
            $allPlan = Plan::all();
            $response = $this->genetateResponse("success",  [$allPlan, "UpdatedSuccessfully"] );
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not Update rate");
            return response()->json($response, 402);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function registerPlan(Request $request)
    {
        $plan = Plan::wherePlan($request->plan)->firstOrFail();
        $user = User::whereId($request->userId)->firstOrFail();

        if($user->current_plan !== "active"){
            $user->current_plan = "active";
            $user->save();
        }else{
            $response = $this->genetateResponse("failed", "Already on a  plan");
            return response()->json($response, 200);
        }

        if (
            $plan->users()->sync([
                $request->userId =>
                [
                    "amount"  => $request->amount,
                    "count"   => 0,
                    "duration" => $plan->duration,
                    "status"  => "active",
                    "rate"    => $plan->rate,
                    "earnings" => 0
                ]
            ])
        ) {
            $user->wallet_balc -= $request->amount;
            $user->save();
            $response = $this->genetateResponse("success", "Plan Successfully Subscribed");
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not register plan");
            return response()->json($response, 402);
        }
        // new Plans_users(array(
        //     "plan_id" => $plan->id,
        //     "user_id" => $request->userId
        // "plan_id" => $plan->id,
        //     "user_id" => $request->userId,,
        //     "amount"  => $request->amount,
        //     "count"   => 0,
        //     "duration"=> $plan->duration,
        //     "status"  => "active",
        //     "rate"    => $plan->rate,
        //     "earnings"=> 0
        // ));

    }
}
