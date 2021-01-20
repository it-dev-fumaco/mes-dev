<div class="row">
   <div class="col-md-12 m-0 p-0">
      @if (count($task_list) <= 0)
      <h6 class="text-center mt-3 mb-3">No Production Order(s) scheduled for today</h6>
      @else
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" role="tablist">
         @php
            $i = 0;
         @endphp
         @foreach($task_list as $process => $production_orders)
         <li class="nav-item font-weight-bold">
            <a class="nav-link {{ ($loop->first) ? 'active' : '' }} text-dark" data-toggle="tab" href="#tab{{ $i }}" role="tab" aria-controls="tab{{ $i }}">{{ $process }}</a>
         </li>
         @php
            $i++;
         @endphp
         @endforeach
      </ul>
      <!-- Tab panes -->
      <div class="tab-content" style="overflow-y: auto; height: 500px;">
         @php
            $b = 0;
         @endphp
         @foreach($task_list as $process => $production_orders)
         <div class="tab-pane {{ ($loop->first) ? 'active' : '' }}" id="tab{{ $b }}" role="tabpanel" aria-labelledby="tab1">
            @if($status == 'Pending')
            <table class="table table-striped">
               <col style="width: 20%;">
               <col style="width: 30%;">
               <col style="width: 15%;">
               <col style="width: 15%;">
               <col style="width: 10%;">
               <thead class="text-primary" style="font-size: 7pt !important;">
                  <th class="text-center font-weight-bold">Prod. Order</th>
                  <th class="text-center font-weight-bold">Customer</th>
                  <th class="text-center font-weight-bold">Item Code</th>
                  <th class="text-center font-weight-bold">Pending Qty</th>
                  <th class="text-center font-weight-bold">Status</th>
               </thead>
               <tbody style="font-size: 9pt;">
                  @forelse($production_orders as $row)
                  <tr>
                     <td class="text-center font-weight-bold"><a href="#" class="view-prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
                     <td class="text-center font-weight-bold">{{ $row['customer'] }}</td>
                     <td class="text-center font-weight-bold">{{ $row['item_code'] }}</td>
                     <td class="text-center font-weight-bold">{{ number_format($row['qty']) }}</td>
                     <td class="text-center">
                        <span class="badge badge-danger text-white" style="font-size: 9pt;">{{ $row['status'] }}</span>
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="9" class="text-center">No Record(s) Found.</td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
            @endif
            @if($status == 'In Progress')
            <table class="table table-striped">
               <thead class="text-primary" style="font-size: 7pt !important;">
                  <th class="text-center font-weight-bold">Prod. Order</th>
                  <th class="text-center font-weight-bold">Item Code</th>
                  <th class="text-center font-weight-bold">Qty</th>
                  <th class="text-center font-weight-bold">Status</th>
                  <th class="text-center font-weight-bold">Machine</th>
                  <th class="text-center font-weight-bold">Start Time</th>
                  <th class="text-center font-weight-bold">QC Remarks</th>
               </thead>
               <tbody style="font-size: 9pt;">
                  @forelse($production_orders as $row)
                  <tr>
                     <td class="text-center font-weight-bold"><a href="#" class="view-prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
                     <td class="text-center font-weight-bold">{{ $row['item_code'] }}</td>
                     <td class="text-center font-weight-bold">{{ number_format($row['qty']) }}</td>
                     <td class="text-center">
                        <span class="badge badge-warning text-white" style="font-size: 9pt;">{{ $row['status'] }}</span>
                        <span class="d-block" style="font-size: 8pt;">{{ $row['operator_name'] }}</span>
                     </td>
                     <td class="text-center font-weight-bold">{{ $row['machine'] }}</td>
                     <td class="text-center font-weight-bold">{{ (in_array($row['status'], ['In Progress', 'Completed'])) ? $row['from_time'] : '-' }}</td>
                     <td class="text-center font-weight-bold">{{ $row['qa_inspection_status'] }}</td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="9" class="text-center font-weight-bold">No Record(s) Found.</td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
            @endif

            @if($status == 'Completed')
            <table class="table table-striped">
               <col style="width: 12%;">
               <col style="width: 10%;">
               <col style="width: 10%;">
               <col style="width: 12%;">
               <col style="width: 10%;">
               <col style="width: 12%;">
               <col style="width: 12%;">
               <col style="width: 10%;">
               <col style="width: 12%;">
               <thead class="text-primary" style="font-size: 6pt !important;">
                  <th class="text-center font-weight-bold p-0">Prod. Order</th>
                  <th class="text-center font-weight-bold p-0">Item Code</th>
                  <th class="text-center font-weight-bold">Qty</th>
                  <th class="text-center font-weight-bold">Status</th>
                  <th class="text-center font-weight-bold">Machine</th>
                  <th class="text-center font-weight-bold p-0">Start Time</th>
                  <th class="text-center font-weight-bold p-0">End Time</th>
                  <th class="text-center font-weight-bold">Duration</th>
                  <th class="text-center font-weight-bold p-0">QC Remarks</th>
               </thead>
               <tbody style="font-size: 9pt;">
                  @forelse($production_orders as $row)
                  <tr>
                     <td class="text-center font-weight-bold p-0"><a href="#" class="view-prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
                     <td class="text-center font-weight-bold">{{ $row['item_code'] }}</td>
                     <td class="text-center font-weight-bold">{{ number_format($row['completed_qty']) }}</td>
                     <td class="text-center p-0">
                        <span class="badge badge-success text-white" style="font-size: 9pt;">{{ $row['status'] }}</span>
                        <span class="d-block" style="font-size: 8pt;">{{ $row['operator_name'] }}</span>
                     </td>
                     <td class="text-center font-weight-bold">{{ $row['machine'] }}</td>
                     <td class="text-center font-weight-bold">{{ (in_array($row['status'], ['In Progress', 'Completed'])) ? $row['from_time'] : '-' }}</td>
                     <td class="text-center font-weight-bold">{{ (in_array($row['status'], ['Completed'])) ? $row['to_time'] : '-' }}</td>
                     <td class="text-center font-weight-bold">{{ (in_array($row['status'], ['Completed'])) ? $row['duration'] : '-' }}</td>
                     <td class="text-center font-weight-bold">{{ $row['qa_inspection_status'] }}</td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="9" class="text-center font-weight-bold">No Record(s) Found.</td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
            @endif
            
            @if($status == 'Rejects')
            <table class="table table-striped">
               <col style="width: 12%;">
               <col style="width: 10%;">
               <col style="width: 10%;">
               <col style="width: 12%;">
               <col style="width: 10%;">
               <col style="width: 12%;">
               <col style="width: 12%;">
               <col style="width: 10%;">
               <col style="width: 12%;">
               <thead class="text-primary" style="font-size: 6pt !important;">
                  <th class="text-center font-weight-bold">Prod. Order</th>
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
                  @forelse($production_orders as $row)
                  <tr>
                     <td class="text-center font-weight-bold p-0"><a href="#" class="view-prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
                     <td class="text-center font-weight-bold">{{ $row['item_code'] }}</td>
                     <td class="text-center font-weight-bold">{{ number_format($row['reject_qty']) }}</td>
                     <td class="text-center">
                        <span class="badge badge-success text-white" style="font-size: 9pt;">{{ $row['status'] }}</span>
                        <span class="d-block" style="font-size: 8pt;">{{ $row['operator_name'] }}</span>
                     </td>
                     <td class="text-center font-weight-bold">{{ $row['machine'] }}</td>
                     <td class="text-center font-weight-bold">{{ (in_array($row['status'], ['In Progress', 'Completed'])) ? $row['from_time'] : '-' }}</td>
                     <td class="text-center font-weight-bold">{{ (in_array($row['status'], ['Completed'])) ? $row['to_time'] : '-' }}</td>
                     <td class="text-center font-weight-bold">{{ (in_array($row['status'], ['Completed'])) ? $row['duration'] : '-' }}</td>
                     <td class="text-center font-weight-bold">{{ $row['qa_inspection_status'] }}</td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="9" class="text-center">No Record(s) Found.</td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
            @endif
         </div>
         @php
            $b++;
         @endphp
         @endforeach
      </div>
      @endif
   </div>
</div>