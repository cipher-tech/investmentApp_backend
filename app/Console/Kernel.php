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
                    $date2 = Carbon::createFromTimeString($plan->created_at);
                    // echo ;
                    if ($date->diff($date2)->format("%H") == 23) {
                        $earnings =  ($plan->rate / 100 ) * $plan->amount;
                        $plan->earnings +=  $earnings;
                        $plan->count += 1;
                        $plan->save();
                        $user = User::whereId($plan->user_id)->firstOrFail();
                        $user->earnings = $plan->earnings;
                        $user->save();
                    }          
                }else{
                    $plan->status = "inactive";
                    $plan->save();

                    $user = User::whereId($plan->user_id)->firstOrFail();
                    $user->current_plan= "none";
                    $user->earnings = $plan->earnings;
                    $user->save();

                    $history = new History(array(
                        "user_id"   =>      $plan->user_id,
                        "plan"      =>      $plan->plan_id,
                        "amount"    =>      $plan->amount,
                        "earnings"  =>      $plan->earnings,
                        "duration"  =>      $plan->duration,
                        "rate"      =>      $plan->rate,
                    ));

                    $history->save();
                }
            });
        })->everyMinute(); 
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
