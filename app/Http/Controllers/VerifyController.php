<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Verification;
use App\User;

class VerifyController extends Controller
{
    public function getUnverifiedUsers(){
        $users = Verification::all();
        return response()->json(["status" => "good", "data" => $users], 200);
    }
    public function create(Request $request){
        if($request->get('image'))
        {
           $image = $request->get('image');
           $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
           \Image::make($request->get('image'))->save(public_path('images\\').$name);

           $user = User::whereId($request->get("id"))->firstOrFail();
           $info = new Verification(array(
                    "images" => $name,
                    "status" => "unverified", 
                    "user_id" => $request->get("id")
            ));
            return $user->verifiedUsers()->save($info) ?  response()->json("okey ", 200) :  response()->json("not ok", 402);
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
}
