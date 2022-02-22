<ol class="activity-feed border border-secondary">
   @foreach($notifications as $n)
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

         // Icon
         $icon = null;
         if($n['type'] == 'Cancelled Process'){
            $icon = 'cancelled';
         }else if($n['type'] == 'Feedbacked'){
            $icon = 'feedbacked';
         }else if(in_array($n['type'], ['Rescheduled Delivery Date', 'Production Schedule'])){
            $icon = 'reschedule';
         }else if($n['type'] == 'Ended Process'){
            $icon = 'end-process';
         }else if(in_array($n['type'], ['Start Process', 'Started Process', 'Started Production Order'])){
            $icon = 'start-process';
         }else if(in_array($n['type'], ['Restart Process', 'Restarted Process'])){
            $icon = 'restart-process';
         }else if($n['type'] == 'BOM Update'){
            $icon = 'bom-update';
         }else if($n['type'] == 'Machine Breakdown'){
            $icon = 'machine-breakdown';
         }
      @endphp
      <li class="feed-item {{ $blink.' '.$icon }}" style="padding-top: 5px !important;">
         <time class="date" style="text-transform: {{ $case }}">{{ $created }}</time>
         <span><b>{{ $n['type'] }}</b></span><br>
         @if ($n['type'] == 'BOM Update')
             @foreach ($n['message'] as $item)
               @php
                   $testing = collect($item);
                   $user = $loop->last ? $testing['user'] : null;
                   $production_order = $loop->last ? $testing['production_order'] : null;
               @endphp
               @foreach ($testing as $actions => $process)
                  @if ($actions == 'update')
                     @php
                        $from_process = collect($process)['old_process'] ? ' from '.collect($process_collect)->where('process_id', collect($process)['old_process'])->pluck('process_name')->first() : null;
                        $to_process = collect($process)['new_process'] ? ' to '.collect($process_collect)->where('process_id', collect($process)['new_process'])->pluck('process_name')->first() : null;
                     @endphp
                        <p class="p-1 m-0">{{ 'Updated process of '.collect($process)['workstation'].$from_process.$to_process }}</p>
                  @else
                     @if(isset(collect($process)['process']))
                        @php
                           $action = substr($actions, -1) == 'e' ? $actions.'d ' : $actions.'ed ';
                           $proc = collect($process_collect)->where('process_id', collect($process)['process'])->pluck('process_name')->first();
                        @endphp
                        <p class="p-1 m-0">{{ $action.$proc }}</p>
                     @endif
                  @endif

                  @if ($actions == 'user')
                     <table class="table" style="font-size: 10pt !important">
                        <tr>
                           <td class="p-1"><b>User</b></td>
                           <td class="p-1">{{ $user }}</td>
                        </tr>
                        <tr>
                           <td class="p-1"><b>Production Order</b></td>
                           <td class="p-1">{{ $production_order }}</td>
                        </tr>
                     </table>
                  @endif
               @endforeach
             @endforeach 
         @else
            <span class="text" style="font-style: italic; font-size: 10pt">{!! $n['message'] !!}</span>
         @endif
      </li>
   @endforeach
</ol>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
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
   .feed-item {
    position: relative;
    padding-bottom: 20px;
    padding-left: 30px;
    border-left: 2px solid #e4e8eb;
  }

  .feed-item:last-child {
      border-color: transparent;
    }
    /* Icons */
    .cancelled::after{
      content: "\f05e";
    }

    .feedbacked::after{
       content: '\f00c';
    }

    .reschedule::after{
       content: '\f274';
    }

    .end-process::after{
      content: '\f04d';
    }

    .start-process::after{
       content: '\f04b';
    }

    .restart-process::after{
       content: '\f01e';
    }

    .bom-update::after{
       content: '\f044';
    }

    .machine-breakdown::after{
       content: '\f071';
    }

    .feed-item::after {
      font-family: FontAwesome;
      display: block;
      position: absolute;
      top: 0;
      left: -6px;
      width: 10px;
      height: 10px;
      border-radius: 6px;
      background: #fff;
    }

    .feed-item .date {
      display: block;
      position: relative;
      top: -5px;
      color: #8c96a3;
      text-transform: uppercase;
      font-size: 13px;
    }
    .feed-item .text {
      position: relative;
      top: -3px;
    }
   </style>
