<?php

namespace App\Http\Controllers;

use App\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    private function genetateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    }

    public function index()
    {

        $history = History::where("status", "pending")
            ->with(['user' => function ($query) {
                // selecting fields from user table
                $query->select(array('id','email', 'phone_no', 'first_name'));
            }])
            ->get();
        // $history = History::where("status", "pending")->with("user:coin_address")->get();
        if ($history) {
            return response()->json($this->genetateResponse("success", $history), 200);
        } else {
            return response()->json($this->genetateResponse("failed", "could not fetch deposits"), 402);
        }
    }
    public function userSellCoin(Request $request)
    {
        $slug = uniqid() . uniqid();
        $history = new History(array(
            "user_id" => $request->user_id || "guest",
            "slug" => uniqid(),
            "amount" => $request->amount,
            "status" => "pending",
            "mode_of_payment" => $request->modeOfPayment,
            "type" => $request->type,
            "action" => $request->action,
            "reference_id" => $slug,
        ));

        if ($history->save()) {
            // $user = User::whereId($request->get("id"))->firstOrFail();
            $details = [
                'name' => $user->last_name,
                'title' => 'Selling coin',
                "header" => " Registration Successful",
                'body' =>   [
                    "This is to confirm your registration. Please kindly login with the same
                    credentials used in registration to access your dashboard and lots of other features. Thanks and welcome",
                    "To start Earning, you need to make a deposit",
                    "Choose an investment plan, invest and Earn"
                ],
                "companyName" => env('COMPANY_NAME', '')
            ];

            // \Mail::to("nickchibuikem@gmail.com")->send(new \App\Mail\GenMailer($details));
            $response = $this->genetateResponse("success", $slug);
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not place order");
            return response()->json($response, 402);
        }
    }
    public function userBuyCoin(Request $request)
    {
        $slug = uniqid() . uniqid();
        $history = new History(array(
            "user_id" => $request->user_id || "guest" ,
            "slug" => uniqid(),
            "amount" => $request->amount,
            "status" => "pending",
            "type" => $request->type,
            "mode_of_payment" => $request->modeOfPayment,
            "action" => $request->action,
            "address" => $request->address,
            "reference_id" => $slug,
        ));

        if ($history->save()) {
            $response = $this->genetateResponse("success", $slug);
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not place order");
            return response()->json($response, 402);
        }
    }
    public function userSellCard(Request $request)
    {
        $slug = uniqid() . uniqid();

        if ($request->get('cardImage')) {
            $cardImage = $request->get('cardImage');
            $cardImagePath = time() . 'cardImage.' . explode('/', explode(':', substr($cardImage, 0, strpos($cardImage, ';')))[1])[1];
            \Image::make($request->get('cardImage'))->save(public_path('images\\') . $cardImagePath);
        } else {
            $response = $this->genetateResponse("failed", "could not save image");
            return response()->json($response, 402);
        }
        $history = new History(array(
            "user_id" => $request->user_id || "guest",
            "slug" => uniqid(),
            "amount" => $request->amount,
            "status" => "pending",
            "image" => $cardImagePath,
            "type" => $request->type,
            "email" => $request->email,
            "action" => $request->action,
            "address" => $request->card_id,
            "reference_id" => $slug,
        ));

        if ($history->save()) {
            $response = $this->genetateResponse("success", $slug);
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not place order");
            return response()->json($response, 402);
        }
    }

    public function confirmTransaction(Request $request)
    {
        $history = History::whereId($request->id)->firstOrFail();
        $history->status = "accepted";

        if ($history->save()) {
            $histories = History::where("status", "pending")
            ->with(['user' => function ($query) {
                // selecting fields from user table
                $query->select(array('id','email', 'phone_no', 'first_name'));
            }])
            ->get();
            $response = $this->genetateResponse("success",["Updated orders", $histories]);
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not update order");
            return response()->json($response, 402);
        }
    }
    
    public function destroyTransaction(Request $request)
    {
        if (History::whereId($request->id)->delete()) {
            $History = History::where("status", "pending")->get();
            return response()->json($this->genetateResponse("success",["Deleted Order", $History ]), 200);
         } else {
            return response()->json($this->genetateResponse("failed","could not delete Order"), 402);
         }
    }
}
