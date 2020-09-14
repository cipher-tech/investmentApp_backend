<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deposit;
use App\User;
use Illuminate\Support\Facades\Validator;

class DepositController extends Controller
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
        $deposits = Deposit::where("status", "pending")
            ->with(['user' => function ($query) {
                // selecting fields from user table
                $query->select(['id', 'state', "coin_address"]);
            }])
            ->get();
        if ($deposits) {
            return response()->json($this->generateResponse("success", $deposits), 200);
        } else {
            return response()->json($this->generateResponse("failed", "could not fetch deposits"), 402);
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
        $validator = Validator::make($request->all(), [
            'id' => 'required|min:1|max:40',
            'amount' => 'required|min:2|max:40',
            'email' => 'required|min:2|max:125|email',
        ]);
        if ($validator->fails()) {
            return response()->json($this->generateResponse("failed", "Validation failed"), 402);
        }

        $slug = uniqid();
        $deposit = new Deposit(array(
            "user_id" => $request->get("id"),
            "status" => "pending",
            "slug" => $slug,
            "email" => $request->email,
            "coin_address" => $request->coinAddress || null,
            "trans_type" => "deposit",
            "amount" => $request->get("amount"),
        ));

        if ($deposit->save()) {
            
            $userEmail = [
                'name' => 'Admin',
                'title' => 'New deposit Request',
                "header" => "New deposit request placed",
                "subject" => "New Deposit Request",
                'body' => [
                    'A new deposit request has been placed. Check your dashboard.',
                    'email: '. $request->email,
                    "amount: " . $request->get("amount"),
                    'transaction id: ' . $slug,
                ],
<<<<<<< HEAD
                "links" => "",
=======
                "links" => [
                    "registerLink" => "",
                ],
>>>>>>> 27f281f5e9c8313fef6f69520060f29be765febf
                "companyName" => env('COMPANY_NAME', '')
            ];

            \Mail::to(env('MAIL_USERNAME', ''))->send(new \App\Mail\GenMailer($userEmail));

            return response()->json($this->generateResponse("success", ["Deposit request placed Successfully", $slug]), 200);
        } else {
            return response()->json($this->generateResponse("failed", "could not place request"), 402);
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
            return response()->json($this->generateResponse("success", ["update user", $deposits]), 200);
        } else {
            return response()->json($this->generateResponse("failed", "could not update verified"), 402);
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
            return response()->json($this->generateResponse("success", ["Deleted user", $Deposit]), 200);
        } else {
            return response()->json($this->generateResponse("failed", "could not delete user"), 402);
        }
    }
}
