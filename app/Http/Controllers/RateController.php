<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rate;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller
{
    private function generateResponse($status, $data){
        return  ["status" => $status, "data" => $data];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rates = Rate::all();

        return $rates? response()->json($this->generateResponse("success", $rates), 201) :
        response()->json($this->generateResponse("failed", "could not fetch Rates"), 401);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request){
        $rate = new Rate(array(
            'name' => $request->get('name'), 
            'type' => $request->get('type'), 
            'class' => $request->get('classInput'), 
            'from' => $request->get('from'), 
            'to' => $request->get('to'), 
            'buying' => $request->get('buying'), 
            'selling' => $request->get('selling'), 
            'quantity' => $request->get('quantity'), 
        ));

        if ($rate->save()) {
            $allRates = Rate::all();
            $response = $this->generateResponse("success", $allRates );
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not add rate");
            return response()->json($response, 402);
        }
        
    }
    public function createGiftcard(Request $request){
        $validator = Validator::make($request->all(), [
            'giftcardName' => 'required|max:125|',
            'type' => 'required|max:125|',
            'options' => 'min:4',
            
        ]);
        $rate = new Rate(array(
            'name' => $request->get('giftcardName'), 
            'type' => $request->get('type'), 
            'attributes' => $request->get('options'), 
            'quantity' => $request->get('quantity'), 
        ));

        if ($rate->save()) {
            $allRates = Rate::all();
            $response = $this->generateResponse("success", $allRates );
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not add card");
            return response()->json($response, 402);
        }
        
    }
    public function editGiftcard(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:125|',
            // 'type' => 'required|max:125|',
        ]);

        $rate = Rate::whereId($request->id)->firstOrFail();
        
        $rate->name = $request->name;
        $rate->type = $request->type;
        $rate->attributes = $request->get('attributes');
        $rate->quantity = $request->quantity;

        if ($rate->save()) {
            $allRates = Rate::all();
            $response = $this->generateResponse("success", $allRates );
            return response()->json($response, 200);
        } else {
            $response = $this->generateResponse("failed", "could not add card");
            return response()->json($response, 402);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $rate = Rate::whereId($request->get("rateId"))->firstOrFail();

        // $rate->current_rate = $request->get("currentRate");
        $rate->buying = $request->get("buying");
        $rate->selling = $request->get("selling");
        $rate->quantity = $request->get("quantity");
        $rate->class = $request->get("class");
        $rate->from = $request->get("from");
        $rate->to = $request->get("to");

        if ($rate->save()) {
            $allRates = Rate::all();
            $response = $this->generateResponse("success", $allRates );
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
    public function destroy($request)
    {
        if (Rate::whereId($request->id)->delete()) {
            $Rate = Rate::all();
            return response()->json($this->generateResponse("success",["Deleted rate", $Rate ]), 200);
         } else {
            return response()->json($this->generateResponse("failed","could not delete rate"), 402);
         }
    }
}
