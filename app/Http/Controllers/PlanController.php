<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;
use App\Plans_users;
use Illuminate\Support\Facades\Validator;
use App\User;
// use Illuminate\Support\Arr;


class PlanController extends Controller
{
    private function generateResponse($status, $data)
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

        return $plans ? response()->json($this->generateResponse("success", $plans), 201) :
            response()->json($this->generateResponse("failed", "could not fetch plans"), 401);
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
            
            $response = $this->generateResponse("success", $allPlans);
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not add plan");
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
            $response = $this->generateResponse("success",  [$allPlan, "Updated Successfully"] );
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not Update rate");
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
            $response = $this->generateResponse("failed","Validation failed");
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
                    'name' => $user->last_name,
                    'title' => 'Plan subscription successful',
                    "subject" => "Plan Subscription Successful",
                    "header" => "Your subscription to the ". $plan->plan." plan was successful",
                    'body' =>   [
                        'Your subscription for '. $request->plan . " plan was successful",
                        "your interest will be added according to the period specified on the plan details.",
                        "Please see plan details for more. Thank you"
                    ],
                    "links" => [
                        "registerLink" => "",
                    ],
                    "companyName" => env('COMPANY_NAME', '')
                ];

                $admin = [
                    'name' => "Admin",
                    'title' => 'A new Plan was subscribed successfully',
                    "subject" => "New Plan Subscription",
                    "header" => "A new subscription to the". $plan->plan."was successful",
                    'body' =>   [
                        'Plan: '. $request->plan,
                        'amount: '. $request->amount,
                        'User email: '.  $user->email,
                        'Name: '.  $user->last_name,
                    ],
                    "links" => [
                        "registerLink" => "",
                    ],
                    "companyName" => env('COMPANY_NAME', '')
                ];

            if($user->ref_code){ 
                $bonus = 0.05 * $request->amount;
                $referrer  = User::where("slug", $user->ref_code )->firstOrFail();
                $referrer ->wallet_balc += $bonus;
                $referrer ->save();
                
                $user->ref_code = null;
                $user->save();

                $details1 = [
                    'name' => $referrer->last_name,
                    'title' => 'Referrer Bonus ',
                    "subject" => "Subscription Bonus",
                    "header" => "Your referrer link was used",
                    'body' =>   [
                        'Your referrer link was used, hence you will get a bonus of 5% off the subscribers first plan',
                        "Please see your dashboard for more. Thank you"
                    ],
                    "links" => [
                        "registerLink" => "",
                    ],
                    "companyName" => env('COMPANY_NAME', '')
                ];
            
                \Mail::to($referrer->email)->send(new \App\Mail\GenMailer($details1));
                // send email
            }

            if($user->current_plan !== "active"){
                $user->wallet_balc -= $request->amount;
                $user->current_plan = "active";
                $user->save();
            }else{
                $response = $this->generateResponse("failed", "Already on a  plan");
                return response()->json($response, 200);
            }

            $response = $this->generateResponse("success", "Plan Successfully Subscribed");

            \Mail::to($user->email)->send(new \App\Mail\GenMailer($details));
            
            \Mail::to(env('MAIL_USERNAME', ''))->send(new \App\Mail\GenMailer($admin));
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed","Already on a plan");
            return response()->json($response, 402);
        }

    }
}
