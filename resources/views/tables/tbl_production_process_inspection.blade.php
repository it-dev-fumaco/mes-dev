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
               <span class="nav-link active" id="first-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Random Inspection <span class="badge badge-primary" style="font-size: 10pt;">{{ count($task_random_inspection) }}</span></span>
            </li>
            <li class="nav-item font-weight-bold">
               <span class="nav-link" id="second-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Reject Confirmation <span class="badge badge-primary" style="font-size: 10pt;">{{ count($task_reject_confirmation) }}<span></span>
            </li>
        </ul>
        <div class="tab-content" style="min-height: 300px;">
            <div class="tab-pane active" id="tab1" role="tabpanel" aria-labelledby="first-tab">
                <div class="row" style="min-height: 200px;">
                    <div class="col-md-12" style="margin: 8px;">
                        <h5 class="title text-center" style="margin: 0;">Select Task for Random Inspection</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped text-center">
                                <thead class="text-primary" style="font-size: 7pt;">
                                    <th class="text-center"><b>PROCESS</b></th>
                                    <th class="text-center"><b>BATCH DATE</b></th>
                                    <th class="text-center"><b>OPERATOR</b></th>
                                    <th class="text-center"><b>COMPLETED QTY</b></th>
                                    <th class="text-center"><b>INSPECTED QTY</b></th>
                                    <th class="text-center"><b>STATUS</b></th>
                                    <th class="text-center"><b>ACTIONS</b></th>
                                </thead>
                                <tbody style="font-size: 10pt;">
                                    @forelse($task_random_inspection as $row)
                                    <tr>
                                        <td><b>{{ $row->process }}</b></td>
                                        <td><b>{{ date('M-d-Y h:i A', strtotime($row->from_time)) }}</b></td>
                                        <td><b>{{ $row->operator_name }}</b></td>
                                        <td><b>{{ $row->completed_qty }}</b></td>
                                        <td>
                                            <span class="font-weight-bold" style="font-size: 18pt !important;">
                                                {{ isset($quantity_inspected[$row->time_log_id]) ? $quantity_inspected[$row->time_log_id][0]->qty : 0 }}
                                            </span>
                                        </td>
                                        <td><b>{{ $row->status }}</b></td>
                                        <td class="text-center">
                                            <button type='button' class='btn btn-primary btn-lg quality-inspection-btn' data-timelog-id="{{ $row->time_log_id }}" data-inspection-type="Random Inspection" data-workstation="{{ $row->workstation }}" data-processid="{{ $row->process_id }}" data-production-order="{{ $existing_production_order->production_order }}">
                                                <i class='now-ui-icons gestures_tap-01'></i> Select
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6">No Task for Random Inspection</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="second-tab">
                <div class="row" style="min-height: 200px;">
                    <div class="col-md-12" style="margin: 8px;">
                        <h5 class="title text-center" style="margin: 0;">Select Task for Reject Confirmation</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped text-center">
                                <thead class="text-primary" style="font-size: 7pt;">
                                    <th class="text-center"><b>PROCESS</b></th>
                                    <th class="text-center"><b>BATCH DATE</b></th>
                                    <th class="text-center"><b>REJECT QTY</b></th>
                                    <th class="text-center"><b>OPERATOR</b></th>
                                    <th class="text-center"><b>STATUS</b></th>
                                    <th class="text-center"><b>ACTIONS</b></th>
                                </thead>
                                <tbody style="font-size: 10pt;">
                                    @forelse($task_reject_confirmation as $row)
                                    <tr>
                                        <td><b>{{ $row->process }}</b></td>
                                        <td><b>{{ date('M-d-Y h:i A', strtotime($row->from_time)) }}</b></td>
                                        <td><b>{{ $row->reject }}</b></td>
                                        <td><b>{{ $row->operator_name }}</b></td>
                                        <td><b>{{ $row->status }}- {{$row->process}}</b></td>
                                        <td class="text-center">
                                            <button type='button' class='btn btn-primary btn-lg reject-confirmation-btn' data-inspection-type="Reject Confirmation" data-workstation="{{ $row->workstation }}" data-production-order="{{ $row->production_order }}" data-process-id="{{ $row->process_id }}" data-qaid="{{ $row->qa_id }}">
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
        </div>
    </div>
 </div>
