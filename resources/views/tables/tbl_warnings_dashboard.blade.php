<ol class="activity-feed fa-ul">
    @foreach($warnings as $n)
       @php
          if($n['type'] == 'Machine Breakdown'){
             $blink = 'blink';
          }elseif($n['type'] == 'Change Code'){
             $blink = "blink_changecode";
          }else{
             $blink ="";
          }
 
          $log_date = \Carbon\Carbon::parse($n['created']);
          $now = \Carbon\Carbon::now();
          $interval_hours = $log_date->diffInHours($now);
          $interval_min = $log_date->diffInMinutes($now);
          $interval_sec = $log_date->diffInSeconds($now);
          $created = null;
          $case = 'uppercase';
          if(date('M d, Y', strtotime($now)) == date('M d, Y', strtotime($n['created']))){
             $case = 'lowercase';
             if($interval_hours < 1 and $interval_min <= 1){
                $created = 'a few seconds ago';
             }else if($interval_hours < 1){
                $created = 'a few minutes ago';
             }else if($interval_hours > 1 and $interval_hours < 2){
                $created = 'an hour ago';
             }else{
                $created = $interval_hours.' hours ago';
             }
          }else{
             $created = date('M d, Y', strtotime($n['created']));
          }
       @endphp
        <li class="warning-feed-item {{ $blink }}" style="padding-top: 5px !important;"><i class="fa-solid fa-triangle-exclamation" style="margin-left: -40px; font-size: 20px;"></i>
            <time class="date" style="text-transform: {{ $case }}; margin-top: -16px">{{ $created }}</time>
            <span><b>{{ $n['type'] }}</b></span><br>
            <span class="text" style="font-style: italic; font-size: 10pt">{!! $n['message'] !!}</span>
        </li>
    @endforeach
 </ol>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> 

 <style>
    @import url(https://fonts.googleapis.com/css?family=Open+Sans);
          /* apply a natural box layout model to all elements, but allowing components to change */
    html {
       box-sizing: border-box;
    }
    *, *:before, *:after {
       box-sizing: inherit;
    }
 
 
    body {
       font-family: 'Open Sans', sans-serif;
       color: #4e555f;
       font-size: 14px;
    }
 
    a {
       color: #f37167;
    }
 
    .activity-feed {
       padding: 15px;
       list-style: none;
    }
    .warning-feed-item {
     position: relative;
     padding-bottom: 20px;
     padding-left: 30px;
     border-left: 2px solid #e4e8eb;
   }
 
   .warning-feed-item:last-child {
       border-color: transparent;
     }
 
     .warning-feed-item::after {
       display: none;
       position: absolute;
       top: 0;
       left: -6px;
       width: 10px;
       height: 10px;
       border-radius: 6px;
       background: #fff;
     }
 
     .warning-feed-item .date {
       display: block;
       position: relative;
       top: -5px;
       color: #8c96a3;
       text-transform: uppercase;
       font-size: 13px;
     }
     .warning-feed-item .text {
       position: relative;
       top: -3px;
     }
</style>
 