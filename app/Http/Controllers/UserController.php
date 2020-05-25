<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use JWTAuthException;

class UserController extends Controller
{
    private function genetateResponse($status, $data){
        return  ["status" => $status, "data" => $data];
    }
    private function getToken($email, $password)
    {
        $token = null;
        //$credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt( ['email'=>$email, 'password'=>$password])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid',
                    'token'=>$token
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }
        return $token;
    }

    public function login(Request $request)
    {
        $user = \App\User::where('email', $request->email)->get()->first();
        if ($user && \Hash::check($request->password, $user->password)) // The passwords match...
        {
            $token = self::getToken($request->email, $request->password);
            $user->auth_token = $token;
            $user->save();
            $response = ['status'=>true, 'data'=>["user"=> $user]];           
        }
        else 
          $response = ['status'=>false, 'data'=>'Record doesnt exists'];
      

        return response()->json($response, 201);
    }

    public function register(Request $request)
    { 
        $payload = [
            'password'=>\Hash::make($request->password),
            'email'=>$request->email,
            'first_name'=>$request->first_name,
            'slug'=> \uniqid(),
            'phone_no'=> $request->phone,
            'auth_token'=> ''
        ];
                  
        $user = new \App\User($payload);
        if ($user->save())
        {
            
            $token = self::getToken($request->email, $request->password); // generate user token
            
            if (!is_string($token))  return response()->json(['status'=>false,'data'=>'Token generation failed'], 201);
            
            $user = \App\User::where('email', $request->email)->get()->first();
            
            $user->auth_token = $token; // update user token
            
            $user->save();
            
            $response = ['status'=>true, 'data'=>['name'=>$user->name,'id'=>$user->id,'email'=>$request->email,'auth_token'=>$token]];        
        }
        else
            $response = ['status'=>false, 'data'=>'Couldnt register user'];
        
        
        return response()->json($response, 201);
    }

    public function fetchAllUsers(){
       if( $users = User::all()){
            $response = $this->genetateResponse(true, $users);
            $status = 201;
       }else {
            $response = $this->genetateResponse(false, 'Couldnt get users');
            $status = 402;
       }
        return response()->json($response, $status);
    }
}
