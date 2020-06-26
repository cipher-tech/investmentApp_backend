<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deposit;
use App\User;
class DepositController extends Controller
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
        $deposits = Deposit::where("status", "pending")
        ->with(['user' => function ($query) {
            // selecting fields from user table
            $query->select(['id', 'state', "coin_address"]);
        }])
        ->get();
        if ($deposits) {
            return response()->json($this->genetateResponse("success",$deposits), 200);
         } else {
            return response()->json($this->genetateResponse("failed","could not fetch deposits"), 402);
         }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $slug = uniqid();
        $deposit = new Deposit(array(
            "user_id" => $request->get("id"),
            "status" => "pending",
            "slug" => $slug,
            "email" => $request->email,
            "trans_type" => "deposit",
            "amount" => $request->get("amount"),
        ));

        if ($deposit->save()) {
            
            $uesrEmail = [
                'title' => 'New deposit Request',
                'body' => 'A new deposit request has been placed. Check your dashboard. <br/> transction id'. $slug 
            ];
        
            // \Mail::to($userMail->email)->send(new \App\Mail\DepositMail($uesrEmail));
            
            return response()->json($this->genetateResponse("success","Deposit request placed Successfully"), 200);
            
         } else {
            return response()->json($this->genetateResponse("failed","could not place request"), 402);
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
    public function update(Request $request)
    {
        $deposit = Deposit::whereSlug($request->get("slug"))->firstOrFail();
        $deposit->status = "accepted";

        $user = User::whereId($request->get("user_id"))->firstOrFail();
        $user->wallet_balc +=  $request->get("amount");

        if ($deposit->save() && $user->save()) {
            $deposits = Deposit::where("status", "pending")->get();
            return response()->json($this->genetateResponse("success",["update user", $deposits ]), 200);
        } else {
            return response()->json($this->genetateResponse("failed","could not update verified"), 402);
        }
         
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (Deposit::whereId($request->id)->delete()) {
            $Deposit = Deposit::where("status", "pending")->get();
            return response()->json($this->genetateResponse("success",["Deleted user", $Deposit ]), 200);
         } else {
            return response()->json($this->genetateResponse("failed","could not delete user"), 402);
         }
    }
}
