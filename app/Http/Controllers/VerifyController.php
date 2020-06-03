<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Verification;
use App\User;

class VerifyController extends Controller
{
    private function genetateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    }
    public function getUnverifiedUsers()
    {
        $users = Verification::where("status", "unverified")->get();
        return response()->json(["status" => "good", "data" => $users], 200);
    }
    public function create(Request $request){
        if ($request->get('selfi') && $request->get('address') && $request->get('idCard')) {
            $selfi = $request->get('selfi');
            $selfiPath = time() . 'selfi.' . explode('/', explode(':', substr($selfi, 0, strpos($selfi, ';')))[1])[1];
            \Image::make($request->get('selfi'))->save(public_path('images\\') . $selfiPath);

            $idCard = $request->get('idCard');
            $idCardPath = time() . 'id_card.' . explode('/', explode(':', substr($idCard, 0, strpos($idCard, ';')))[1])[1];
            \Image::make($request->get('idCard'))->save(public_path('images\\') . $idCardPath);

            $address = $request->get('address');
            $addressPath = time() . 'address.' . explode('/', explode(':', substr($address, 0, strpos($address, ';')))[1])[1];
            \Image::make($request->get('address'))->save(public_path('images\\') . $addressPath);

            $verificationPhotos = [
                "selfi" => $selfiPath,
                "idCard" => $idCardPath,
                "address" => $addressPath
            ];
            $photos = json_encode($verificationPhotos);

            $user =  User::whereId($request->get('id'))->firstOrFail();
            $info = new Verification(array(
                "images" => $photos,
                "status" => "unverified",
                "user_id" => $request->get("id")
            ));
            // return response()->json($verificationPhotos, 200);
            return $user->verifiedUsers()->save($info) ?  response()->json(["status" => "success"], 200) :  response()->json("not ok", 402);
        }



        // $fileupload = new Verification(array(
        //             "images" => $name,
        //             "status" => "unverified", 
        //             "user_id" => $request->get("id")
        //         ));
        //  $fileupload->save();
        //  return response()->json('Successfully added');
        // // if ($request->hasFile('image')){
        //     $file = $request['image'];
        //     // $fileName = "myname".'LOGO'. time() . '.' . $file->getClientOriginalExtension();
        //     // $path = $file->storeAs('photos', $fileName);

        //     return response()->json($file, 200);
        // // }
        //     $user = User::whereId($request->get("id"))->firstOrFail();
        //     $info = new Verification(array(
        //         "images" => $request->image,
        //         "status" => "unverified", 
        //         "user_id" => $request->get("id")
        //     ));
        //    return  $user->verifiedUsers()->save($info) ?  response()->json("okey ", 200) :  response()->json("not ok", 402) ;
    }

    public function verifyUsers(Request $request){
        $user = User::whereId($request->user_id)->firstOrFail();
        $user->status = "verified";
        if($user->save()){
            $verifyContent = Verification::whereId($request->verifyId)->firstOrFail();
            $verifyContent->status = "verified";
             if ($verifyContent->save()) {
                $users = Verification::where("status", "unverified")->get();
                return response()->json($this->genetateResponse("success",["update user", $users ]), 200);
             } else {
                return response()->json($this->genetateResponse("failed","could not update verified"), 402);
             }
             
        }else{
            return response()->json($this->genetateResponse("failed","could not update user"), 402);
        }
    }
}
