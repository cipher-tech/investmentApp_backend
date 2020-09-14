<?php

namespace App\Console;
use App\Console\DateTime;
use App\History;
use App\Plans_users;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\User;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Plans_users::where("status", "active")->get()->filter(function ($plan) {
                if($plan->count !== $plan->duration){
                    $date = Carbon::now("West Central Africa");
                    // $date2 = Carbon::createFromTimeString($plan->created_at);
                    $date3 = new Carbon($plan->created_at,"West Central Africa");
                    // echo ;
                    if ($date->diff($date3)->format("%H") == "00") {
                        $earnings =  round(
                            (($plan->rate / 100 ) * $plan->amount) / 7, 1
                        );
                        $plan->earnings +=  $earnings;
                        $plan->count += 1;
                        $plan->save();
                        $user = User::whereId($plan->user_id)->firstOrFail();
                        $user->earnings = $plan->earnings;
                        $user->save();
                    }          
                }else{
                    $totalEarnings = $plan->earnings;
                    $plan->status = "inactive";
                    // $plan->earnings = ;
                    $user = User::whereId($plan->user_id)->firstOrFail();
                    $user->current_plan= "none";
                    $user->wallet_balc += $totalEarnings + $plan->amount;
                    $user->earnings = 0;
                    
                    $plan->earnings = 0;

                    $user->save();
                    $plan->save();

                    $history = new History(array(
                        "user_id"   =>      $plan->user_id,
                        "plan"      =>      $plan->plan_id,
                        "amount"    =>      $plan->amount,
                        "earnings"  =>      $totalEarnings,
                        "duration"  =>      $plan->duration,
                        "rate"      =>      $plan->rate,
                    ));

                    $history->save();
                }
            });
        })->hourly(); //everyMinute(); //hourly(); ; 
    }
//intval(date_diff(new \DateTime(date('Y-m-d H:i:s', time())), new \DateTime($plan->created_at))->format('%i')) >= 1
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
