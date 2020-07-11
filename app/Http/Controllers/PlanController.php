<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\plan;
use App\Plans_users;
use Illuminate\Support\Facades\Validator;
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
            $response = $this->genetateResponse("success",  [$allPlan, "Updated Successfully"] );
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
        $validator = Validator::make($request->all(), [
            'userId' => 'required|min:1|max:20',
            'amount' => 'required|min:2|max:11',
            'plan' => 'required|min:5|max:25|',
        ]);
        if ($validator->fails()) {
            $response = $this->genetateResponse("failed","Validation failed");
            return response()->json($response, 402);
        }

        $plan = Plan::wherePlan($request->plan)->firstOrFail();
        $user = User::whereId($request->userId)->firstOrFail();

        if ($user->current_plan !== "active") {

            $plan->users()->save( 
                $user,
                [
                    "amount"  => $request->amount,
                    "count"   => 0,
                    "duration" => $plan->duration,
                    "status"  => "active",
                    "rate"    => $plan->rate,
                    "earnings" => 0
                ]
                );
                $details = [
                    'title' => 'Plan subcribtion successful',
                    'body' => 'Your subcribtion for '. $request->plan . " was successful"
                ];
            
                // \Mail::to(env($user->email))->send(new \App\Mail\DepositMail($details));

            if($user->ref_code){ 
                $bonus = 0.05 * $request->amount;
                $referrer  = User::where("slug", $user->ref_code )->firstOrFail();
                $referrer ->wallet_balc += $bonus;
                $referrer ->save();
                
                $user->ref_code = null;
                $user->save();

                $details = [
                    'title' => 'Referrer Bonus ',
                    'body' => 'Your referrer link was used, hence you will get a bonus of 5% off the subcribers first plan'
                ];
            
                // \Mail::to(env($user->email))->send(new \App\Mail\DepositMail($details));
                // send email
            }

            if($user->current_plan !== "active"){
                $user->wallet_balc -= $request->amount;
                $user->current_plan = "active";
                $user->save();
            }else{
                $response = $this->genetateResponse("failed", "Already on a  plan");
                return response()->json($response, 200);
            }

            $response = $this->genetateResponse("success", "Plan Successfully Subscribed");
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed","Already on a plan");
            return response()->json($response, 402);
        }

    }
}
