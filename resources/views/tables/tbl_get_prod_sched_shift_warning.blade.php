<div class="table-responsive">
    <table class="table table-striped text-center" style="font-size: 8.5pt;">
    <col style="width: 25%;">
    <col style="width: 60%;">
    <col style="width: 15%;">
      <thead class="text-primary">
        <th class="text-center"><b>Scheduled Date</b></th>
         <th class="text-center"><b>Transaction Details</b> 
         <th class="text-center"><b>Actions</b>
      </thead>
      <tbody>
        @forelse($data as $index => $row)
          <tr>
            <td><a href="#" class="prod_order_link_to_search" data-prod="" style="color:black;font-weight:bold;">{{ $row['date'] }}</a><br> Shift time out:{{ $row['shift_out'] }} </td>
            <td>
                <span style="">Last transaction ->>{{$row['data']->workstation}}[{{$row['data']->process_name}}]</span><br>
                <span style="">Operator : {{$row['data']->operator_name}} || Operator time out : {{ $row['operator_out'] }}</span><br>
                <span><b>{{$row['data']->production_order}}</b></span> <span class="badge badge-danger">{{ $row['status'] }}</span>
            </td>
            <td>
            <div class="btn-group">
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
              </button>
              <div class="dropdown-menu">
                <a href="#" class="dropdown-item btnshift " style="padding-left: 15px;" data-date="{{ $row['date'] }}" data-reloadtbl="reloadtbl_warning" >Shift Schedule</a>
              </div>
            </div>
            </td>

          </tr>
        @empty
        <tr>
           <td colspan="8" class="text-center">No Record(s) Found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>



