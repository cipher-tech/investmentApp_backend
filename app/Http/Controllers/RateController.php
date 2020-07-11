<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rate;

class RateController extends Controller
{
    private function genetateResponse($status, $data){
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

        return $rates? response()->json($this->genetateResponse("success", $rates), 201) :
        response()->json($this->genetateResponse("failed", "could not fetch Rates"), 401);
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
            'current_rate' => $request->get('currentRate'), 
            'buying' => $request->get('buying'), 
            'selling' => $request->get('selling'), 
            'quantity' => $request->get('quantity'), 
        ));

        if ($rate->save()) {
            $allRates = Rate::all();
            $response = $this->genetateResponse("success", $allRates );
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not add rate");
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

        if ($rate->save()) {
            $allRates = Rate::all();
            $response = $this->genetateResponse("success", $allRates );
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not Update rate");
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
            return response()->json($this->genetateResponse("success",["Deleted rate", $Rate ]), 200);
         } else {
            return response()->json($this->genetateResponse("failed","could not delete rate"), 402);
         }
    }
}
