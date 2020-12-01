
<table class="table" id="table_id">
                           <col style="width: 10%;">
                           <col style="width: 15%;">
                           <col style="width: 50%;">
                           <col style="width: 10%;">
                           <tbody>
                              @foreach($notifications as $n)
                              <tr class="{{ $n['type'] == 'Machine Breakdown' ? 'blink' : '' }}">
                                 <td>@if($n['type'] == 'Machine Breakdown')
                                    <img src="{{ asset('img/warning.png')}}" width="40">
                                    @else

                                    <img src="{{ asset('img/info.png')}}" width="40">

                                    @endif</td>
                                 <td class="text-center" style="font-size: 10pt;font-weight: bold;">
                                 
                                    {{ $n['type'] }}</td>
                                 <td class="" style="font-size: 9pt;">{!! $n['message']!!}</td>
                                 <td class="text-center">
                                 <img style="margin-top:-50px;" data-frmtable="{{ $n['table'] }}" data-timelogid="{{ $n['timelog_id'] }}" class="btn-hide" src="{{ asset('img/close.png')}}" width="13">

                                    
                                 </td>
                                 <!-- <td><button class="btn btn-primary">Close</button></td> -->
                              </tr>
                              @endforeach
                           </tbody>
</table>
                        <style>
                        .trhide{
                            display:none;
                        }</style>


