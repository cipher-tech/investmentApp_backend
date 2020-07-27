<?php

use App\Deposit;
use App\User;
use Illuminate\Support\Facades\Route;
// use function PHPSTORM_META\type;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    function getTimeInterval($date)
    {

        // Set date as DateTime object
        $date = new DateTime($date);

        // Set now as DateTime object
        $now = date('Y-m-d H:i:s', time()); // If you want to compare two dates you both provide just delete this line and add a $now to the function 
        //parameter (ie. function getTimeInterval($date, $now))
        $now = new DateTime($now);

        // Check if date is in the past or future and calculate timedifference and set tense accordingly
        if ($now >= $date) {
            $timeDifference = date_diff($date, $now);
            $tense = " ago";
        } else {
            $timeDifference = date_diff($now, $date);
            $tense = " until";
        }

        // Set posible periods (lowest first as to end result with the highest value that isn't 0)
        $period = array(" second", " minute", " hour", " day", " month", " year");

        // Set values of the periods using the DateTime formats (matching the periods above)
        $periodValue = array($timeDifference->format('%s'), $timeDifference->format('%i'), $timeDifference->format('%h'), $timeDifference->format('%d'), $timeDifference->format('%m'), $timeDifference->format('%y'));

        // Loop through the periods (ie. seconds to years)
        for ($i = 0; $i < count($periodValue); $i++) {
            // If current value is different from 1 add 's' to the end of current period (ie. seconds)
            if ($periodValue[$i] != 1) {
                $period[$i] .= "s";
            }

            // If current value is larger than 0 set new interval overwriting the lower value that came before ensuring the result shows only the highest value that isn't 0
            if ($periodValue[$i] > 0) {
                $interval = $periodValue[$i] . $period[$i] . $tense; // ie.: 3 months ago
            }
        }

        // If any values were larger than 0 (ie. timedifference is not 0 years, 0 months, 0 days, 0 hours, 0 minutes, 0 seconds ago) return interval
        if (isset($interval)) {
            return $interval;
            // Else if no values were larger than 0 (ie. timedifference is 0 years, 0 months, 0 days, 0 hours, 0 minites, 0 seconds ago) return 0 seconds ago
        } else {
            return "0 seconds" . $tense;
        }
    }

    // Ex. (time now = November 23 2017)
    // getTimeInterval("2016-05-04 12:00:00"); // Returns: 1 year ago
    // echo getTimeInterval("2020-06-8 12:00:00"); // Returns: 1 month until
    // echo  intval(date_diff(new DateTime(date('Y-m-d H:i:s', time())), new DateTime("2020-06-08 15:10:29"))->format('%h')); // . "days";

    // $user = User::whereId(14)->firstOrFail();
    // echo $user->ref_code ? "yes " : "no";
    // echo  date_diff(new DateTime("2016-05-04 12:00:00"), new DateTime("2016-05-05 12:00:00"));

    //texting with() function 

    // echo intval(date_diff( new \DateTime("2016-05-07 23:55:00"), new \DateTime("2016-05-07 23:59:00"))->format('%i'));
    function genetateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    }
    // $deposits = Deposit::where("status", "pending")
    //     // ->select('deposits.*', 'users.dob')
    //     ->with(['user' => function($query){
    //         $query->select(['user.id', 'user.dob']);
    //       }]) //("user")
    //         // ->whereHas("user",function ($qurey){
    //         //     $qurey->select('email')->get();
    //         // })
    //     ->get(array('deposits.*', 'users.dob'));

    $deposits = Deposit::where("status", "accepted")
    ->where("user_id", "4")
        ->with(['user' => function ($query) {
            // selecting fields from user table
            $query->select(['id', 'state', "coin_address"]);
        }])
        ->get();
    if ($deposits) {
        return response()->json(genetateResponse("success", $deposits), 200);
    } else {
        return response()->json(genetateResponse("failed", "could not fetch deposits"), 402);
    }
});

Route::get('send-mail', function () {

    $details = [
        'title' => 'New Deposit Request',
        'body' => 'A new withdrawl request has been placed. Check your dashboard'
    ];

    \Mail::to(env('MAIL_USERNAME'))->send(new \App\Mail\DepositMail($details));

    dd("Email is Sent.");
    return "sent";
});
