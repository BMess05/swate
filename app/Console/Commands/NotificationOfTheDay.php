<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DeviceToken;
use App\Models\Inventories;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationOfTheDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'word:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to user about their expiring items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        $time = date('H:i');
        $td = new \DateTime();
        $today = $td->format('Y-m-d');
        $date_after = date('Y-m-d', strtotime('3 days', strtotime($today)));
        $time_one_min_before = date('H:i', strtotime('-1 minutes', strtotime($time)));
        $time_twenty_nine_min_after = date('H:i', strtotime('29 minutes', strtotime($time)));

        $users=User::whereRaw("find_in_set($dayOfTheWeek , cooking_days)")
              ->where(function($q)  use ($time_one_min_before,$time_twenty_nine_min_after) {
                      $q->whereBetween('breakfast_time',[$time_one_min_before,$time_twenty_nine_min_after])
                        ->orWhereBetween('lunch_time', [$time_one_min_before,$time_twenty_nine_min_after])
                        ->orWhereBetween('dinner_time', [$time_one_min_before,$time_twenty_nine_min_after]);
                })->get()->toArray();
        if(count($users)>0)
        {
            $device_token=[];
            foreach ($users as  $user) {
                $user_id[]=$user['id'];
                foreach ($user['device_token'] as $key => $token) {
                    $device_token[]=$token['device_token'];
                }
            }

            $items=Inventories::whereBetween('expiry_date',[$today,$date_after])->whereIn('user_id',$user_id)->get()->toArray();
             if(count($items)>0){
                $allitems = array_column($items, 'item');
                $item_names = array_column($allitems, 'item_name');
                 if(count($item_names)==3){
                     $two_items=array_slice($item_names,0,2,true);
                     $message="Items in your inventory ". implode(',', $two_items)." and ".(count($item_names)-2)." more are expiring soon! Use them immediately.";

                    }else{
                     $message="Items in your inventory ". implode(',',$item_names)." are expiring soon! Use them immediately.";
                    }
               if(count($device_token)>0){
                  $res=$this->sendPushNotification($device_token,'Expiring Soon!',$message,$user['id']);
                  $res = 'Notification sent';
                }else{
                     $res = 'No device token found';
                }
            }else{
                 $res = 'No expiry items found';
            }

        }else{
            $res = "No data found";
        }

        $this->info($res);
       
    }

    function sendPushNotification($fcm_token, $title, $message, $id) { 
        $your_project_id_as_key= env('FCM_TOKEN');
        $url = "https://fcm.googleapis.com/fcm/send";            
        $header = [
        'authorization: key=' . $your_project_id_as_key,
            'content-type: application/json'
        ];   
        $finalPostArray = array('registration_ids' => $fcm_token,
                            'notification' => array('body' => $message,
                                                    'title' => $title,
                                                    'sound' => "default",
                                                    'badge' => 1,
                                                   ),
                            "data"=> array('id' => $id,
                                            'title' => $title, 
                                            'body' => $message)); 
        $postdata = json_encode($finalPostArray);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($ch);    
        curl_close($ch);
        return $result;
    }

}
