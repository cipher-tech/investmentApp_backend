<?php

namespace App\Http\Controllers;

use App\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    private function generateResponse($status, $data)
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
            return response()->json($this->generateResponse("success", $history), 200);
        } else {
            return response()->json($this->generateResponse("failed", "could not fetch deposits"), 402);
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
                'name' => "Admin",
                'title' => 'User sell coin order',
                "header" => "Sell Coin Order",
                "subject" => "New Withdrawal Request",
                'body' =>   [
                    "A user just placed a sell coin order,",
                    "See details below: ",
                    "id: ". $slug,
                    "Amount: ". $request->amount,
                    "mode_of_payment: ". $request->modeOfPayment,
                    "status". "pending",
                ],
                "companyName" => env('COMPANY_NAME', '')
            ];

            $response = $this->generateResponse("success", $slug);

            // \Mail::to(env('MAIL_USERNAME', ''))->send(new \App\Mail\GenMailer($details));

            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not place order");
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
            $details = [
                'name' => "Admin",
                'title' => 'User buy coin order',
                "header" => "Buy Coin Order",
                "subject" => "Registration successful",
                'body' =>   [
                    "A user just placed a buy coin order,",
                    "See details below: ",
                    "id: ". $slug,
                    "Amount: ". $request->amount,
                    "mode_of_payment: ". $request->modeOfPayment,
                    "status". "pending",
                ],
                "companyName" => env('COMPANY_NAME', '')
            ];

            $response = $this->generateResponse("success", $slug);

            // \Mail::to(env('MAIL_USERNAME', ''))->send(new \App\Mail\GenMailer($details));
            // $response = $this->generateResponse("success", $slug);
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not place order");
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
            $response = $this->generateResponse("failed", "could not save image");
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
            $details = [
                'name' => "Admin",
                'title' => 'User sell card order',
                "header" => "Sell Card Order",
                "subject" => "Registration successful",
                'body' =>   [
                    "A user just placed a sell card order,",
                    "See details below: ",
                    "id: ". $slug,
                    "email: ". $request->email,
                    "card_id: ". $request->card_id,
                    "Amount: ". $request->amount,
                    "mode_of_payment: ". $request->modeOfPayment,
                    "status". "pending",
                ],
                "companyName" => env('COMPANY_NAME', '')
            ];

            $response = $this->generateResponse("success", $slug);

            // \Mail::to(env('MAIL_USERNAME', ''))->send(new \App\Mail\GenMailer($details));
            // $response = $this->generateResponse("success", $slug);
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not place order");
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
            $response = $this->generateResponse("success",["Updated orders", $histories]);
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not update order");
            return response()->json($response, 402);
        }
    }
    
    public function destroyTransaction(Request $request)
    {
        if (History::whereId($request->id)->delete()) {
            $History = History::where("status", "pending")->get();
            return response()->json($this->generateResponse("success",["Deleted Order", $History ]), 200);
         } else {
            return response()->json($this->generateResponse("failed","could not delete Order"), 402);
         }
    }
}
