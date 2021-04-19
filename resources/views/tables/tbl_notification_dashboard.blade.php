<table class="table" id="table_id">
   <col style="width: 10%;">
   <col style="width: 15%;">
   <col style="width: 50%;">
   <col style="width: 10%;">
   <tbody>
      @foreach($notifications as $n)
      @php
         if($n['type'] == 'Machine Breakdown'){
            $blink = 'blink';
         }elseif($n['type'] == 'Change Code'){
            $blink = "blink_changecode";
         }else{
            $blink ="";
         }
      @endphp
      <tr class="{{$blink}}">
         <td class="text-center p-1">@if($n['type'] == 'Machine Breakdown')
            <img src="{{ asset('img/warning.png')}}" width="30">
            @else
            <img src="{{ asset('img/info.png')}}" width="30">
            @endif
         </td>
         <td class="text-center font-weight-bold p-1" style="font-size: 9pt;">{{ $n['type'] }}</td>
         <td class="p-1" style="font-size: 9pt;">{!! $n['message']!!}</td>
         <td class="text-center p-1 align-middle">
            <img data-frmtable="{{ $n['table'] }}" data-timelogid="{{ $n['timelog_id'] }}" class="btn-hide" src="{{ asset('img/close.png')}}" width="13">
         </td>
      </tr>
      @endforeach
   </tbody>
</table>
   <style>
   .trhide{
         display:none;
   }</style>


