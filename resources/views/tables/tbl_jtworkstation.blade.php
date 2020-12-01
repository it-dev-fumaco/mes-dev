

<table class="table" id="jt-workstations-tbl">
              <thead class="text-primary" style="font-size: 8pt;">
                <th class="text-center" style="width: 25%;"><b>Production Order</b></th>
                <th class="text-center" style="width: 40%;" colspan="3"><b>Item Details</b></th>
                <th class="text-center" style="width: 15%;" colspan="2"><b>Qty</b></th>
                <th class="text-center" style="width: 20%;"><b>Actions</b></th>
              </thead>
              @forelse($data as $index => $row)
               @php
                $inprogress = collect($data)->where('status', 'In Progress')->count();
                $start_btn_prop = ($inprogress > 0) ? 'disabled' : '';
                $production_item = $row['production_item'] .': '.$row['description'];
                $enable = ($row['status'] != 'Accepted') ? 'disabled' : '';
                $colspan = ($row['status'] == 'In Progress') ? 'colspan="2"' : '';
                $rowspan = ($row['status'] == 'Accepted') ? '1="2"' : '';
                $is_enabled = null;
                if(!$loop->first){
                  if($data[$index - 1]['prev_good_qty'] == 0 || $data[$index - 1]['prev_good_qty']  < $data[$index - 1]['qty_accepted'] && $data[$index - 1]['prev_good_qty'] > 0){
                    if($data[0]['prev_good_qty'] == 0 || $data[0]['prev_good_qty']  < $data[0]['qty_accepted'] && $data[0]['prev_good_qty'] > 0){
                     $is_enabled = 'enable';
                    }else{
                      $is_enabled = 'disabled';
                    }
                  }else{
                    $is_enabled = 'disabled';
                  }
                }
                $stat = null;
                if ($row['status'] == 'Machine Setup'){
                  $stat = 'For QC';
                }

                if ($row['remarks'] == 'Rework' && $row['rework'] == 0) {
                  $stat = 'Rework';
                }
              @endphp
              <tbody style="font-size: 10pt;">
                <tr>
                  <td class="text-left align-top" rowspan="5">
                    <div class="text-center">{{ $row['production_order'] }}</div><br>
                    <p style="margin: 1px 5px; font-size: 8pt;">Customer: {{ $row['customer']}}</p>
                    <p style="margin: 1px 5px; font-size: 8pt;">Project: {{ $row['project'] }}</p>
                    <p style="margin: 1px 5px; font-size: 8pt;">SO No.: {{ $row['sales_order'] }}</p>
                  </td>
                  <td rowspan="4" colspan="3" class="align-top"><b>{{ $row['production_item'] }}</b> <span class="badge badge-danger" style="font-size: 9pt;">{{ $stat }}</span><br>{{ $row['description'] }}</td>
                  <td style="padding: 0;">Accepted Qty</td>
                  <td class="text-center" style="font-size: 12pt; padding: 0;"><b>{{ number_format($row['qty_accepted']) }}</b></td>
                  <td class="text-center" rowspan="4">
                    @if($row['status'] == "Accepted")
                    <button class="btn btn-block btn-danger btn-lg start-btn" data-name="{{$row['tsdname']}}" data-qty="{{ number_format($row['qty_accepted']) }}" data-item="{{ $production_item }}" data-prevqty="{{ $row['prev_good_qty'] }}" data-prevstation="{{ $row['prev_workstation'] }}" data-prevoperator="{{ $row['prev_operator'] }}" {{$start_btn_prop}} style="margin: 5px;">Start Work</button>
                    @elseif($row['status'] == "In Progress")
                    <button class="btn btn-block btn-warning btn-lg end-btn" data-name="{{$row['tsdname']}}" data-qty="{{ number_format($row['completed_qty']) }}" style="margin: 5px; padding-left: 8px; padding-right: 8px;">
                      <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> {{ $row['status'] }}
                    </button>
                    @elseif($row['status'] == 'Machine Setup')
                    <button class="btn btn-block btn-info btn-lg" style="margin: 5px; padding-left: 8px; padding-right: 8px;">
                      <i class="now-ui-icons ui-1_check"></i> {{ $row['status'] }}
                    </button>
                    @elseif($row['status'] == "Completed")
                    <button class="btn btn-block btn-success btn-lg edit-btn2" data-name="{{$row['tsdname']}}" data-qty="{{ number_format($row['completed_qty']) }}" style="margin: 5px;">
                      <i class="now-ui-icons ui-1_check"></i> Done
                    </button>
                    @if($row['reject'] > 0)
                    <button class="btn btn-block btn-lg rework-btn" data-name="{{ $row['tsdname'] }}" data-qty="{{ number_format($row['reject']) }}" style="margin: 5px;" {{-- {{($row['remarks'] == 'Reject for Checking') ? 'disabled' : null }} --}} {{($row['rework'] > 0) ? 'disabled' : null }}>
                      <i class="now-ui-icons arrows-1_refresh-69"></i> Rework
                    </button>
                    @endif
                    @endif
                  </td>
                </tr>
                <tr>
                  <td style="padding: 0;" class="align-top">Good</td>
                  <td class="text-center align-top" style="font-size: 12pt; padding: 0;"><b>{{ number_format($row['good']) }}</b></td>
                </tr>
                <tr>
                  <td style="padding: 0;" class="align-top">Reject</td>
                  <td class="text-center align-top" style="font-size: 12pt; padding: 0;"><b>{{ number_format($row['reject']) }}</b></td>
                </tr>
                <tr>
                  <td style="padding: 0;" class="align-top">Rework</td>
                  <td class="text-center align-top" style="font-size: 12pt; padding: 0;"><b>{{ number_format($row['rework']) }}</b></td>
                </tr>
                <tr>
                  @php
                    $start_time = (in_array($row['status'], ["In Progress", "Completed"])) ? Carbon\Carbon::parse($row['from_time'])->format('m-d-Y h:i:s A') : '---';
                    $end_time = ($row['status'] != "In Progress") ? Carbon\Carbon::parse($row['to_time'])->format('m-d-Y h:i:s A') : '---';
                  @endphp
                  @if($row['status'] == "Completed" )
                  <td class="text-center align-top" style=" font-size: 8pt;border: none;">
                    <p style="margin: 1px 5px;"><b>Start Time</b></p>
                    <p style="margin: 1px 5px;">{{ $start_time }}</p>
                  </td>
                  <td class="text-center align-top" style=" font-size: 8pt;border: none;">
                    <p style="margin: 1px 5px;"><b>End Time</b></p>
                    <p style="margin: 1px 5px;">{{ $end_time }}</p>
                  </td>
                  <td class="text-center align-top" style=" font-size: 8pt;border: none;">
                    <p style="margin: 1px 5px;"><b>Duration</b></p>
                    <p style="margin: 1px 5px;">{{ $row['hours'] }}</p>
                  </td>
                  <td class="text-center align-top" colspan="2" style=" font-size: 8pt;border: none;">
                  @if($row['item_feedback'] != 'In Progress')
                    <p style="margin: 1px 5px;"><b>Quality Inspection</b></p>
                    <p style="margin: 1px 5px;">{{ $row['qa_inspection_status'] }}</p>
                  @endif
                  {{-- @if($row['remarks'] == 'Reject for Checking')
                    <p style="margin: 1px 5px;"><b>QC Inspection</b></p>
                    <p style="margin: 1px 5px;">Pending</p>
                  @endif --}}
                  </td>
                  @endif
                  @if($row['status'] == "Accepted")
                  <td class="text-center" colspan="7" style=" font-size: 12pt; border:none">
                    @if($row['prev_good_qty'] == 0 || $row['prev_good_qty'] < $row['qty_accepted'] && $row['prev_good_qty'] > 0)
                    {{-- <span class="text-blink1"><b>Not Ready</b></span> --}}
                    @else
                    {{-- <span class="text-blink2"><b>Ready to Start!</b></span> --}}
                    @endif
                  </td>
                  @else
                  <td style="border: none;"></td>
                  @endif
                </tr>
              </tbody>
              @empty
              <tbody>
                <tr>
                  <td colspan="9" class="text-center" style="font-size: 15pt;">No assigned task(s) found.</td>
                </tr>
              </tbody>
              @endforelse
            </table>




<style type="text/css">
  #jt-workstations-tbl tbody:nth-child(odd) {
    background-color: #f2f2f2;
  }

</style>
