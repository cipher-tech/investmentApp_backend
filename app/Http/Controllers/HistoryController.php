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

    public function userSellCoin(Request $request){
        $slug = uniqid() . uniqid();
        $history = new History(array(
            "user_id" => $request->user_id,
            "slug" => uniqid(),
            "amount" => $request->amount,
            "status" => "pending",
            "type" => $request->type,
            "action" => $request->action,
            "reference_id" => $slug,
        ));

        if ($history->save()) {
            $response = $this->genetateResponse("success", $slug );
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not place order");
            return response()->json($response, 402);
        }
    }
    public function userBuyCoin(Request $request){
        $slug = uniqid() . uniqid();
        $history = new History(array(
            "user_id" => $request->user_id,
            "slug" => uniqid(),
            "amount" => $request->amount,
            "status" => "pending",
            "type" => $request->type,
            "action" => $request->action,
            "address" => $request->address,
            "reference_id" => $slug,
        ));

        if ($history->save()) {
            $response = $this->genetateResponse("success", $slug );
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not place order");
            return response()->json($response, 402);
        }
    }
    public function userSellCard(Request $request){
        $slug = uniqid() . uniqid();

        if ($request->get('cardImage')) {
            $cardImage = $request->get('cardImage');
            $cardImagePath = time() . 'cardImage.' . explode('/', explode(':', substr($cardImage, 0, strpos($cardImage, ';')))[1])[1];
            \Image::make($request->get('cardImage'))->save(public_path('images\\') . $cardImagePath);
        }else{
            $response = $this->genetateResponse("failed", "could not save image");
            return response()->json($response, 402);
        }
        $history = new History(array(
            "user_id" => $request->user_id,
            "slug" => uniqid(),
            "amount" => $request->amount,
            "status" => "pending",
            "image" => $cardImagePath ,
            "type" => $request->type,
            "action" => $request->action,
            "address" => $request->card_id,
            "reference_id" => $slug,
        ));

        if ($history->save()) {
            $response = $this->genetateResponse("success", $slug );
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not place order");
            return response()->json($response, 402);
        }
    }
}
