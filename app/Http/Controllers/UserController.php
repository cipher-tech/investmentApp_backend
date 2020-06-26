<?php

namespace App\Http\Controllers;

use App\Deposit;
use Illuminate\Http\Request;
use App\User;
use App\Widthdrawal;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use JWTAuthException;

class UserController extends Controller
{
    private function genetateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    }
    private function getToken($email, $password)
    {
        $token = null;
        //$credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt(['email' => $email, 'password' => $password])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid',
                    'token' => $token
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
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:125',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'data' => 'invalid input'];

            return response()->json($response, 201);
    
        }
        $user = \App\User::where('email', $request->email)->get()->last();
        if ($user && \Hash::check($request->password, $user->password)) // The passwords match...
        {
            $token = self::getToken($request->email, $request->password);
            $user->auth_token = $token;
            $user->save();
            $response = ['status' => true, 'data' => ["user" => $user]];
        } else
            $response = ['status' => false, 'data' => 'Record doesnt exists'];


        return response()->json($response, 201);
    }

    public function register(Request $request)
    {
        $payload = [
            'password' => \Hash::make($request->password),
            'email' => $request->email,
            'first_name' => $request->first_name,
            "last_name" => $request->last_name,
            'slug' => \uniqid(),
            'phone_no' => $request->phone,
            'auth_token' => '',
            "ref_code"  => $request->refCode ? $request->refCode : null,
        ];

        // send email to user with ref code 
        // $details = [
        //     'title' => 'Mail from ItSolutionStuff.com',
        //     'body' => 'This is for testing email using smtp'
        // ];

        // \Mail::to("nickchibuikem@gmail.com")->send(new \App\Mail\DepositMail($details));

        $user = new \App\User($payload);
        if ($user->save()) {

            $token = self::getToken($request->email, $request->password); // generate user token
            $uesrEmail = [
                'title' => 'Registration Successful',
                'body' => 'Your registration was successful, log i  to access your dashboard.'
            ];

            // \Mail::to($userMail->email)->send(new \App\Mail\DepositMail($uesrEmail));

            if (!is_string($token))  return response()->json(['status' => false, 'data' => 'Token generation failed'], 201);

            $user = \App\User::where('email', $request->email)->get()->first();

            $user->auth_token = $token; // update user token

            $user->save();

            $response = ['status' => true, 'data' => ['name' => $user->name, 'id' => $user->id, 'email' => $request->email, 'auth_token' => $token]];
        } else
            $response = ['status' => false, 'data' => 'Couldnt register user'];


        return response()->json($response, 201);
    }

    public function fetchAllUsers()
    {
        if ($users = User::all()) {
            $response = $this->genetateResponse(true, $users);
            $status = 201;
        } else {
            $response = $this->genetateResponse(false, 'Couldnt get users');
            $status = 402;
        }
        return response()->json($response, $status);
    }

    public function update(Request $request)
    {
        $user = User::whereId($request->get("userId"))->firstOrFail();

        $user->role = $request->get("role");
        $user->wallet_balc = $request->get("wallet_bal");

        if ($user->save()) {
            $allUsers = User::all();
            $response = $this->genetateResponse("success", $allUsers);
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not Update user");
            return response()->json($response, 402);
        }
    }
    public function getUser(Request $request)
    {
        $user = User::whereId($request->get("id"))->firstOrFail();
        if ($user) {
            $response = $this->genetateResponse("success", $user);
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not get user");
            return response()->json($response, 402);
        }
    }

    public function resetPassword(Request $request)
    {
        $user = User::whereEmail($request->get("email"))->firstOrFail();
        $password = \uniqid();
        $user->password = \Hash::make($password);

        if ($user->save()) {
            $response = $this->genetateResponse("success", "Password reset successful");
            
            $uesrEmail = [
                'title' => 'Password reset Successful',
                'body' => 'Your Password reset was successful, use this password to log in '. $password
            ];

            \Mail::to("nickchibuikem@gmail.com")->send(new \App\Mail\DepositMail($uesrEmail));
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse(false, "could not reset Password");
            return response()->json($response, 402);
        }
    }

    public function updateUserInfo(Request $request)
    {
        $user = User::where('slug', $request->slug)->firstOrFail();

        $user->email = $request->get("email")? $request->get("email") : $user->email  ;
        $user->password = $request->get("password")? $request->get("password") : $user->password ;
        $user->phone_no = $request->get("phone_no")? $request->get("phone_no") : $user->phone_no ;
        $user->first_name = $request->get("first_name")? $request->get("first_name") : $user->first_name ;
        $user->last_name = $request->get("last_name")? $request->get("last_name") :  $user->last_name;
        $user->dob = $request->get("dob")? $request->get("dob") :  $user->dob;
        $user->coutry = $request->get("coutry")? $request->get("coutry") :  $user->coutry;
        $user->state = $request->get("state")? $request->get("state") : $user->state;
        $user->city = $request->get("city")? $request->get("city") : $user->city;
        $user->coin_address = $request->get("coin_address")? $request->get("coin_address") :  $user->coin_address;
        $user->zip_code = $request->get("zip_code")? $request->get("zip_code") :  $user->zip_code;
       
        if ($user->save()) {
            $user = User::whereId($user->id)->firstOrFail();
            $updatedUsers =  $user = User::whereSlug($request->get("slug"))->firstOrFail();;
            $response = $this->genetateResponse("success", $updatedUsers);
            return response()->json($response, 200);
        } else {
            $response = $this->genetateResponse("failed", "could not Update user");
            return response()->json($response, 402);
        }
    }
    public function updateUserPassword(Request $request)
    {
        $user = User::where('slug', $request->slug)->firstOrFail();

        if ($user && !\Hash::check($request->oldPassword, $user->password)) // The passwords match...
        {
            $user->password = \Hash::make($request->newPassword);
            $response =  $this->genetateResponse("success", "Password Updated");
            return $user->save() ?  response()->json($response, 200) :  response()->json("failed update", 402);

        } else{
            $response = $this->genetateResponse("failed", "Password do not match");
            return response()->json($response, 200);
        }
    }

    public function userTransactions(Request $request)
    {
        
        $deposits = collect( Deposit::where("user_id", $request->id)
        ->where("status", "accepted")
        ->with(['user' => function ($query) {
            // selecting fields from user table
            $query->select(['id', 'state', "coin_address"]);
        }])
        ->get());

        $widthdrawl = collect(Widthdrawal::where("user_id", $request->id)
        ->where("status", "accepted")
        ->with(['user' => function ($query) {
            // selecting fields from user table
            $query->select(['id', 'state', "coin_address"]);
        }])
        ->get());

        // $history = $widthdrawl->combine($deposits);
        $history = $widthdrawl->merge($deposits)->sortBy("created_at")->toArray();
        // $tags = array_merge($widthdrawl, $deposits);

        if ($widthdrawl && $deposits) {
            return response()->json($this->genetateResponse("success",$history), 200);
         } else {
            return response()->json($this->genetateResponse("failed","could not fetch history"), 402);
         }
    }
}
