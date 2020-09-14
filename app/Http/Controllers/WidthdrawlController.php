<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Widthdrawal;
use App\User;
use Illuminate\Support\Facades\Validator;


class WidthdrawlController extends Controller
{

    // private $emailAddress = env('MAIL_USERNAME');
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
    
        $widthdrawl = Widthdrawal::where("status", "pending")
        ->with(['user' => function ($query) {
            // selecting fields from user table
            $query->select(['id', 'state', "coin_address"]);
        }])
        ->get();
        // $widthdrawl = Widthdrawal::where("status", "pending")->with("user:coin_address")->get();
        if ($widthdrawl) {
            return response()->json($this->generateResponse("success",$widthdrawl), 200);
         } else {
            return response()->json($this->generateResponse("failed","could not fetch deposits"), 402);
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
        $validator = Validator::make($request->all(), [
            'id' => 'required|min:1|max:40',
            'amount' => 'required|min:2|max:40',
            'email' => 'required|min:2|max:125|email',
        ]);
        if ($validator->fails()) {
            return response()->json($this->generateResponse("failed","Validation failed"), 402);
        }

        $slug = uniqid();
        $widthdrawal = new Widthdrawal (array(
            "user_id" => $request->get("id"),
            "status" => "pending",
            "slug" => $slug,
            "email" => $request->email,
            "coin_address" => $request->coinAddress || null,
            "trans_type" => "withdrawal",
            "amount" => $request->get("amount"),
        ));

        
        if ($widthdrawal->save()) {

            $userEmail = [
                'name' => 'Admin',
                'title' => 'New withdrawal Request',
                "header" => "New withdrawal request placed",
                'body' => [
                    'A new withdrawl request has been placed. Check your dashboard.',
                    'email: '. $request->email,
                    "amount: ". $request->get("amount"),
                    'transaction id: ' . $slug,
                ],
                "links" => [
                    "registerLink" => "",
                ],
                "companyName" => env('COMPANY_NAME', '')
            ];
        
            \Mail::to(env('MAIL_USERNAME', ''))->send(new \App\Mail\GenMailer($userEmail));

            return response()->json($this->generateResponse("success",["Withdrawal request placed Successfully",$slug] ), 200);
        } else {
            return response()->json($this->generateResponse("failed","could not place request"), 402);
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
            return response()->json($this->generateResponse("success",["update user", $widthdrawal ]), 200);
        } else {
            return response()->json($this->generateResponse("failed","could not update verified"), 402);
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
        if (Widthdrawal::whereId($request->id)->delete()) {
            $widthdrawal = Widthdrawal::where("status", "pending")->get();
            return response()->json($this->generateResponse("success",["Deleted user", $widthdrawal ]), 200);
         } else {
            return response()->json($this->generateResponse("failed","could not delete user"), 402);
         }
    }
}
