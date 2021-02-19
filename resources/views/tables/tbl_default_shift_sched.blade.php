 @forelse($shift_sched as $i => $sched)
    <div class="text-center" style="font-size:12pt; margin: -20px 0 5px 0;line-height:20px;text-align:center;display:inline;margin-bottom:10px;">
        <span class="noshift_blink" style="display: {{($sched['shift_type'] == 'No Shift') ? '' : 'none'}}; color:red;font-weight:bold;">-&nbsp; {{ $sched['time_in'] }}&nbsp;- &nbsp;{{ $sched['time_out'] }}</span>
        <span class="text-center"  style="display: {{($sched['shift_type'] == 'Regular Shift') ? 'inline' : 'none'}}"> <b>Shift</b> - &nbsp; {{ $sched['time_in'] }}&nbsp;- &nbsp;{{ $sched['time_out'] }}</span>
        <span class="text-center"  style="display: {{($sched['shift_type'] == 'Special Shift') ? 'inline' : 'none'}}"> <b>Shift</b> - &nbsp; {{ $sched['time_in'] }}&nbsp;- &nbsp;{{ $sched['time_out'] }}</span>
        <span class="text-center"  style="display: {{($sched['shift_type'] == 'Overtime Shift') ? 'inline' : 'none'}}">| &nbsp;<span style="background-color:white  ;color:black;font-weight:bold;"> <b>OT</b> - &nbsp; {{ $sched['time_in'] }}&nbsp;- &nbsp;{{ $sched['time_out'] }}</span></span>
                              
    </div>
    @empty
        <span class="text-center" style="font-size:12pt;display:block; margin-top: -8px;"></span>
@endforelse