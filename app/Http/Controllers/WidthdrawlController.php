<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Widthdrawal;
use App\User;

class WidthdrawlController extends Controller
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
        $widthdrawl = Widthdrawal::where("status", "pending")->get();
        if ($widthdrawl) {
            return response()->json($this->genetateResponse("success",$widthdrawl), 200);
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
        $widthdrawal = new Widthdrawal (array(
            "user_id" => $request->get("id"),
            "status" => "pending",
            "slug" => uniqid(),
            "trans_type" => "deposit",
            "amount" => $request->get("amount"),
        ));

        if ($widthdrawal->save()) {
            return response()->json($this->genetateResponse("success","Widthdrawl request placed Successfully"), 200);
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
        $widthdrawl = Widthdrawal::whereSlug($request->get("slug"))->firstOrFail();
        $widthdrawl->status = "accepted";

        $user = User::whereId($request->get("user_id"))->firstOrFail();
        $user->wallet_balc -=  $request->get("amount");
        if($user->wallet_balc <= 0  ){
            $user->wallet_balc =  0;
        }
        if ($widthdrawl->save() && $user->save()) {
            $widthdrawal = Widthdrawal::where("status", "pending")->get(); 
            return response()->json($this->genetateResponse("success",["update user", $widthdrawal ]), 200);
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
    public function destroy($id)
    {
        //
    }
}
