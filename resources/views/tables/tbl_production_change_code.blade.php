
<table class="table" id="table_id">
    <col style="width: 10%;">
    <col style="width: 15%;">
    <col style="width: 60%;">
    <tbody>
       @foreach($notifications as $n)
       <tr class="{{ $n['type'] == 'Machine Breakdown' ? 'blink' : '' }}">
          <td>@if($n['type'] == 'Machine Breakdown')
             <img src="{{ asset('img/warning.png')}}" width="40">
             @else

             <img src="{{ asset('img/info.png')}}" width="40">

             @endif</td>
          <td class="text-center" style="font-size: 12pt;font-weight: bold;">
          
             {{ $n['type'] }}</td>
          <td class="" style="font-size: 11pt;">{!! $n['message']!!}</td>
       </tr>
       @endforeach
    </tbody>
</table>
 <style>
 .trhide{
     display:none;
 }</style>


