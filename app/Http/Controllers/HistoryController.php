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

    public function userBuyCoin(Request $request){
        $slug = uniqid() . uniqid();
        $history = new History(array(
            "user_id" => $request->user_id,
            "slug" => uniqid(),
            "amount" => $request->amount,
            "status" => "pending",
            "type" => $request->type,
            "reference_id" => $slug,
        ));

        if ($history->save()) {
            $response = $this->genetateResponse("success", $slug );
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not Update rate");
            return response()->json($response, 402);
        }
    }
}
