<div class="row">
   @foreach($dashboard as $w)
   <div class="col-md-3">
      <div class="card">
         <div class="card-header">
            <div class="row">
               <div class="col-md-6">
                  <h4 class="card-title">{{ $w['workstation'] }} <small class="view-workstation-tasks text-primary" data-workstation="{{ $w['workstation'] }}" style="font-size: 10pt; cursor: pointer;"><i class="now-ui-icons ui-1_zoom-bold"></i> View</small></h4>
               </div>
               <div class="col-md-2 text-center">
                  <span style="font-size: 11pt;">On Queue <br><b>{{ $w['ua'] }}</b></span> 
               </div>
               <div class="col-md-2 text-center">
                  <span style="font-size: 11pt;">WIP <br><b>{{ $w['wip'] }}</b></span> 
               </div>
               <div class="col-md-2 text-center">
                  <span style="font-size: 11pt;">CTD <br><b>{{ $w['ctd'] }}</b></span> 
               </div>
            </div>
         </div>
         <div class="card-body">
            <div class="progress-container">
               <div class="progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{ round($w['percentage'], 2) }}%;">
                     <span class="progress-value">{{ round($w['percentage'], 2) }}%</span>
                  </div>
               </div>
            </div>
            <div class="table-responsive" style="height: 400px;position: relative;">
               <table class="table table-striped">
                  <thead class="text-primary" style="font-size: 7pt !important;">
                     <th class="text-center"><b>JT No.</b></th>
                     <th class="text-center"><b>Code</b></th>
                     <th class="text-center"><b>Qty</b></th>
                     <th class="text-center"><b>Status</b></th>
                  </thead>
                  <tbody style="font-size: 9pt;">
                     @forelse($w['tasks'] as $row)
                     @php
                     if ($row->status == 'Pending') {
                        $badge_color = 'primary';
                     }elseif ($row->status == 'In Progress') {
                        $badge_color = 'warning';
                     }elseif ($row->status == 'Completed') {
                        $badge_color = 'success';
                     }else{
                        $badge_color = 'default';
                     }

                     $qty = ($row->status == 'Unassigned') ? $row->bal : $row->qty_to_manufacture;
                     @endphp
                     <tr>
                        <td class="text-center">{{ $row->production_order }}</td>
                        <td class="text-center"><b>{{ $row->item_code }}</b><br>{{ $row->process }}</td>
                        <td class="text-center">{{ number_format($qty) }}</td>
                        <td class="text-center">
                           <span class="badge badge-{{$badge_color}}" style="font-size: 9pt; color: #fff;">{{ $row->status }}</span>
                           @if($row->operator_name)<br><span style="font-size: 8pt;">{{ $row->operator_name }}<br>{{ $row->machine_code }}</span>@endif
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="5" class="text-center">No unassigned task(s) found for this process.</td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   @endforeach
</div>