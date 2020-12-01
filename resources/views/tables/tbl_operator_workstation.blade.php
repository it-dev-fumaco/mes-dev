@if($status == 'Pending')
<div class="row">
   <div class="table-responsive" style="height: 400px;position: relative;">
      <table class="table table-striped">
         <thead class="text-primary" style="font-size: 7pt !important;">
            <th class="text-center font-weight-bold">Prod. Order</th>
            <th class="text-center font-weight-bold">Customer</th>
            <th class="text-center font-weight-bold">Process</th>
            <th class="text-center font-weight-bold">Code</th>
            <th class="text-center font-weight-bold">Pending Qty</th>
            <th class="text-center font-weight-bold">Status</th>
         </thead>
         <tbody style="font-size: 9pt;">
            @forelse($tasks_list as $row)
            <tr>
               <td class="text-center"><a href="#" class="view-prod-details-btn"><b>{{ $row['production_order'] }}</b></a></td>
               <td class="text-center"><b>{{ $row['customer'] }}</b></td>
               <td class="text-center"><b>{{ $row['process'] }}</b></td>
               <td class="text-center"><b>{{ $row['item_code'] }}</b></td>
               <td class="text-center">{{ number_format($row['qty']) }}</td>
               <td class="text-center">
                  <span class="badge badge-danger" style="font-size: 9pt; color: #fff;">{{ $row['status'] }}</span>
               </td>
            </tr>
            @empty
            <tr>
               <td colspan="9" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse
         </tbody>
      </table>
   </div>
</div>
@endif

@if($status == 'Completed')
<div class="row">
   <div class="table-responsive" style="height: 400px;position: relative;">
      <table class="table table-striped">
         <thead class="text-primary" style="font-size: 7pt !important;">
            <th class="text-center font-weight-bold">Prod. Order</th>
            <th class="text-center font-weight-bold">Process</th>
            <th class="text-center font-weight-bold">Code</th>
            <th class="text-center font-weight-bold">Qty</th>
            <th class="text-center font-weight-bold">Status</th>
            <th class="text-center font-weight-bold">Machine</th>
            <th class="text-center font-weight-bold">Start Time</th>
            <th class="text-center font-weight-bold">End Time</th>
            <th class="text-center font-weight-bold">Duration</th>
            <th class="text-center font-weight-bold">QC Remarks</th>
         </thead>
         <tbody style="font-size: 9pt;">
            @forelse($tasks_list as $row)
            <tr>
               <td class="text-center"><a href="#" class="view-prod-details-btn"><b>{{ $row['production_order'] }}</b></a></td>
               <td class="text-center"><b>{{ $row['process'] }}</b></td>
               <td class="text-center"><b>{{ $row['item_code'] }}</b></td>
               <td class="text-center">{{ number_format($row['completed_qty']) }}</td>
               <td class="text-center">
                  <span class="badge badge-success" style="font-size: 9pt; color: #fff;">{{ $row['status'] }}</span><br>
                  <span style="font-size: 8pt;">{{ $row['operator_name'] }}</span>
               </td>
               <td class="text-center">{{ $row['machine'] }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['In Progress', 'Completed'])) ? $row['from_time'] : '-' }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['Completed'])) ? $row['to_time'] : '-' }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['Completed'])) ? $row['duration'] : '-' }}</td>
               <td class="text-center">{{ $row['qa_inspection_status'] }}</td>
            </tr>
            @empty
            <tr>
               <td colspan="9" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse
         </tbody>
      </table>
   </div>
</div>
@endif

@if($status == 'In Progress')
<div class="row">
   <div class="table-responsive" style="height: 400px;position: relative;">
      <table class="table table-striped">
         <thead class="text-primary" style="font-size: 7pt !important;">
            <th class="text-center font-weight-bold">Prod. Order</th>
            <th class="text-center font-weight-bold">Process</th>
            <th class="text-center font-weight-bold">Code</th>
            <th class="text-center font-weight-bold">Qty</th>
            <th class="text-center font-weight-bold">Status</th>
            <th class="text-center font-weight-bold">Machine</th>
            <th class="text-center font-weight-bold">Start Time</th>
            <th class="text-center font-weight-bold">End Time</th>
            <th class="text-center font-weight-bold">Duration</th>
            <th class="text-center font-weight-bold">QC Remarks</th>
         </thead>
         <tbody style="font-size: 9pt;">
            @forelse($tasks_list as $row)
            <tr>
               <td class="text-center"><a href="#" class="view-prod-details-btn"><b>{{ $row['production_order'] }}</b></a></td>
               <td class="text-center"><b>{{ $row['process'] }}</b></td>
               <td class="text-center"><b>{{ $row['item_code'] }}</b></td>
               <td class="text-center">{{ number_format($row['qty']) }}</td>
               <td class="text-center">
                  <span class="badge badge-warning" style="font-size: 9pt; color: #fff;">{{ $row['status'] }}</span><br>
                  <span style="font-size: 8pt;">{{ $row['operator_name'] }}</span>
               </td>
               <td class="text-center">{{ $row['machine'] }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['In Progress', 'Completed'])) ? $row['from_time'] : '-' }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['Completed'])) ? $row['to_time'] : '-' }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['Completed'])) ? $row['duration'] : '-' }}</td>
               <td class="text-center">{{ $row['qa_inspection_status'] }}</td>
            </tr>
            @empty
            <tr>
               <td colspan="9" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse
         </tbody>
      </table>
   </div>
</div>
@endif

@if($status == 'Rejects')
<div class="row">
   <div class="table-responsive" style="height: 400px;position: relative;">
      <table class="table table-striped">
         <thead class="text-primary" style="font-size: 7pt !important;">
            <th class="text-center font-weight-bold">Prod. Order</th>
            <th class="text-center font-weight-bold">Process</th>
            <th class="text-center font-weight-bold">Code</th>
            <th class="text-center font-weight-bold">Reject Qty</th>
            <th class="text-center font-weight-bold">Status</th>
            <th class="text-center font-weight-bold">Machine</th>
            <th class="text-center font-weight-bold">Start Time</th>
            <th class="text-center font-weight-bold">End Time</th>
            <th class="text-center font-weight-bold">Duration</th>
            <th class="text-center font-weight-bold">QC Remarks</th>
         </thead>
         <tbody style="font-size: 9pt;">
            @forelse($tasks_list as $row)
            <tr>
               <td class="text-center"><a href="#" class="view-prod-details-btn"><b>{{ $row['production_order'] }}</b></a></td>
               <td class="text-center"><b>{{ $row['process'] }}</b></td>
               <td class="text-center"><b>{{ $row['item_code'] }}</b></td>
               <td class="text-center">{{ number_format($row['reject_qty']) }}</td>
               <td class="text-center">
                  <span class="badge badge-success" style="font-size: 9pt; color: #fff;">{{ $row['status'] }}</span><br>
                  <span style="font-size: 8pt;">{{ $row['operator_name'] }}</span>
               </td>
               <td class="text-center">{{ $row['machine'] }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['In Progress', 'Completed'])) ? $row['from_time'] : '-' }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['Completed'])) ? $row['to_time'] : '-' }}</td>
               <td class="text-center">{{ (in_array($row['status'], ['Completed'])) ? $row['duration'] : '-' }}</td>
               <td class="text-center">{{ $row['qa_inspection_status'] }}</td>
            </tr>
            @empty
            <tr>
               <td colspan="9" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse
         </tbody>
      </table>
   </div>
</div>
@endif