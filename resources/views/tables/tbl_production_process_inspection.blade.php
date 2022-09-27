<div class="row" style="margin: 0 8px;">
    <div class="col-md-12">
        <table style="width: 100%; border-color: #D5D8DC;">
            <col style="width: 18%;">
            <col style="width: 24%;">
            <col style="width: 23%;">
            <col style="width: 20%;">
            <col style="width: 15%;">
            <tr style="font-size: 9pt;">
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REFERENCE NO.</b></td>
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>CUSTOMER</b></td>
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROJECT</b></td>
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>DELIVERY DATE</b></td>
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QTY</b></td>
            </tr>
            <tr style="font-size: 10pt;">
              <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $existing_production_order->sales_order }}{{ $existing_production_order->material_request }}</td>
              <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $existing_production_order->customer }}</td>
              <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $existing_production_order->project }}</td>
              <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $existing_production_order->delivery_date }}</td>
              <td class="text-center" style="border: 1px solid #ABB2B9; font-size: 15pt;">{{ $existing_production_order->qty_to_manufacture }}</td>
            </tr>
            <tr style="font-size: 10pt;">
              <td style="border: 1px solid #ABB2B9; font-size: 9pt;" class="text-center"><b>ITEM DETAIL(S):</b></td>
              <td style="border: 1px solid #ABB2B9;" colspan="4"><span class="font-weight-bold">{{ $existing_production_order->item_code }}</span> - {{ $existing_production_order->description }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-12">
        <ul class="nav nav-tabs" role="tablist" style="margin-top: 8px;">
            <li class="nav-item font-weight-bold">
               <span class="nav-link active" id="first-tab" data-toggle="tab" href="#tab{{ $existing_production_order->production_order }}-1" role="tab" aria-controls="tab1" aria-selected="true">Select for Random Inspection <span class="badge badge-primary" style="font-size: 10pt;">{{ count($task_random_inspection_arr) }}</span></span>
            </li>
            <li class="nav-item font-weight-bold">
               <span class="nav-link" id="second-tab" data-toggle="tab" href="#tab{{ $existing_production_order->production_order }}-2" role="tab" aria-controls="tab2" aria-selected="false">Select for Reject Confirmation <span class="badge badge-primary" style="font-size: 10pt;">{{ count($task_reject_confirmation) }}<span></span>
            </li>
            <li class="nav-item font-weight-bold">
                <span class="nav-link" id="third-tab" data-toggle="tab" href="#tab{{ $existing_production_order->production_order }}-3" role="tab" aria-controls="tab3" aria-selected="false">QA Inspection Log(s) <span class="badge badge-primary" style="font-size: 10pt;">{{ count($qa_inspection_logs) }}<span></span>
             </li>
        </ul>
        <div class="tab-content" style="min-height: 300px;">
            <div class="tab-pane active" id="tab{{ $existing_production_order->production_order }}-1" role="tabpanel" aria-labelledby="first-tab">
                <div class="row" style="min-height: 200px;">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped text-center">
                                <thead class="text-primary" style="font-size: 5pt;">
                                    <th class="text-center p-1"><b>PROCESS</b></th>
                                    <th class="text-center p-1"><b>BATCH DATE</b></th>
                                    <th class="text-center p-1"><b>OPERATOR</b></th>
                                    <th class="text-center p-1"><b>COMPLETED QTY</b></th>
                                    <th class="text-center p-1"><b>INSPECTED QTY</b></th>
                                    <th class="text-center p-1"><b>STATUS</b></th>
                                    <th class="text-center p-1"><b>ACTIONS</b></th>
                                </thead>
                                <tbody style="font-size: 9pt;">
                                    @forelse($task_random_inspection_arr as $row)
                                    <tr>
                                        <td class="p-1"><b>{{ $row['process'] }}</b></td>
                                        <td class="p-1"><b>{{ $row['batch_date'] }}</b></td>
                                        <td class="p-1"><b>{{ $row['operator_name'] }}</b></td>
                                        <td class="p-1"><b>{{ $row['completed_qty'] }}</b></td>
                                        <td class="p-1">
                                            <span class="font-weight-bold" style="font-size: 15pt !important;">
                                                {{ $row['inspected_qty'] }}
                                            </span>
                                        </td>
                                        <td class="p-1"><b>{{ $row['status'] }}</b></td>
                                        <td class="text-center p-1">
                                            <button type='button' class='btn pb-2 pr-3 pt-2 pl-3 btn-primary btn-lg quality-inspection-btn' data-timelog-id="{{ $row['time_log_id'] }}" data-inspection-type="Random Inspection" data-workstation="{{ $row['workstation'] }}" data-processid="{{ $row['process_id'] }}" data-production-order="{{ $existing_production_order->production_order }}">
                                                <i class='now-ui-icons gestures_tap-01'></i> Select
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7">No Task for Random Inspection</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab{{ $existing_production_order->production_order }}-2" role="tabpanel" aria-labelledby="second-tab">
                <div class="row" style="min-height: 200px;">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped text-center">
                                <thead class="text-primary" style="font-size: 5pt;">
                                    <th class="text-center p-1"><b>PROCESS</b></th>
                                    <th class="text-center p-1"><b>BATCH DATE</b></th>
                                    <th class="text-center p-1"><b>REJECT QTY</b></th>
                                    <th class="text-center p-1"><b>OPERATOR</b></th>
                                    <th class="text-center p-1"><b>STATUS</b></th>
                                    <th class="text-center p-1"><b>ACTIONS</b></th>
                                </thead>
                                <tbody style="font-size: 10pt;">
                                    @forelse($task_reject_confirmation as $row)
                                    <tr>
                                        <td class="p-1"><b>{{ $row->process }}</b></td>
                                        <td class="p-1"><b>{{ date('M-d-Y h:i A', strtotime($row->from_time)) }}</b></td>
                                        <td class="p-1"><b>{{ $row->reject }}</b></td>
                                        <td class="p-1"><b>{{ $row->operator_name }}</b></td>
                                        <td class="p-1"><b>{{ $row->status }}- {{$row->process}}</b></td>
                                        <td class="text-center p-1">
                                            <button type='button' class='btn pb-2 pr-3 pt-2 pl-3 btn-primary btn-lg reject-confirmation-btn' data-inspection-type="Reject Confirmation" data-workstation="{{ $row->workstation }}" data-production-order="{{ $row->production_order }}" data-process-id="{{ $row->process_id }}" data-qaid="{{ $row->qa_id }}">
                                                <i class='now-ui-icons gestures_tap-01'></i> Select
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6">No Task for Reject Confirmation</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab{{ $existing_production_order->production_order }}-3" role="tabpanel" aria-labelledby="third-tab">
                <div class="row" style="min-height: 200px;">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped text-center">
                                <thead class="text-primary" style="font-size: 5pt;">
                                    <th class="text-center p-1"><b>PROCESS</b></th>
                                    <th class="text-center p-1"><b>INSPECTION DATE</b></th>
                                    <th class="text-center p-1"><b>INSPECTION TYPE</b></th>
                                    <th class="text-center p-1"><b>QTY</b></th>
                                    <th class="text-center p-1"><b>INSPECTED BY</b></th>
                                    <th class="text-center p-1"><b>STATUS</b></th>
                                </thead>
                                <tbody style="font-size: 10pt;">
                                    @forelse($qa_inspection_logs as $row)
                                    <tr>
                                        <td class="p-1"><b>{{ $row['process'] }}</b></td>
                                        <td class="p-1"><b>{{ $row['qa_inspection_date'] }}</b></td>
                                        <td class="p-1"><b>{{ $row['qa_inspection_type'] }}</b></td>
                                        <td class="p-1"><b>{{ $row['actual_qty_checked'] }}</b></td>
                                        <td class="p-1"><b>{{ $row['qa_staff'] }}</b></td>
                                        <td class="font-weight-bold p-1">
                                            @if ($row['status'] == 'QC Passed')
                                            <span class="badge badge-success" style="font-size: 10pt;"> {{ $row['status'] }}</span>
                                            @else
                                            <span class="badge badge-danger" style="font-size: 10pt;"> {{ $row['status'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6">No QA Inspection Log(s)</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
