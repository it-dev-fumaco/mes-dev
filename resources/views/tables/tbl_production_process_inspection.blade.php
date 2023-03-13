<div class="row p-0 m-0">
    <div class="col-md-12 p-0">
        <table style="width: 100%; border-color: #D5D8DC;">
            <col style="width: 18%;">
            <col style="width: 24%;">
            <col style="width: 23%;">
            <col style="width: 20%;">
            <col style="width: 15%;">
            <tr style="font-size: 8pt;">
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REFERENCE NO.</b></td>
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>CUSTOMER</b></td>
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROJECT</b></td>
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>DELIVERY DATE</b></td>
              <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QTY</b></td>
            </tr>
            <tr style="font-size: 8pt;">
              <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $existing_production_order->sales_order }}{{ $existing_production_order->material_request }}</td>
              <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $existing_production_order->customer }}</td>
              <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $existing_production_order->project }}</td>
              <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $existing_production_order->delivery_date }}</td>
              <td class="text-center" style="border: 1px solid #ABB2B9; font-size: 14pt;">{{ $existing_production_order->qty_to_manufacture }}</td>
            </tr>
            <tr style="font-size: 9pt;">
              <td style="border: 1px solid #ABB2B9; font-size: 9pt;" class="text-center"><b>ITEM DETAIL(S):</b></td>
              <td style="border: 1px solid #ABB2B9;" colspan="4" class="text-justify"><span class="font-weight-bold">{{ $existing_production_order->item_code }}</span> - {{ $existing_production_order->description }}</td>
            </tr>
        </table>
    </div>
    @if (count($reject_category_per_workstation) > 0)    
    <div class="col-12 p-0 mb-5">
        <ul class="d-block nav nav-tabs custom-tabs-rcfa mt-2 border-bottom-0" role="tablist">
            @foreach ($reject_category_per_workstation as $i => $r)
            <li class="nav-item font-weight-bold d-inline-block">
                <span class="nav-link border m-1 rounded {{ $loop->first ? 'active' : '' }} p-3" id="rcfa-tab-{{ $i }}" data-toggle="tab" href="#rcfa{{ $existing_production_order->production_order }}-{{ $i }}" role="tab" aria-controls="tab1" aria-selected="true">{{ $r->reject_category_name }}</span>
            </li>
            @endforeach
            <li class="nav-item font-weight-bold pull-right d-none">
                <span class="nav-link border mb-1 ml-1 mr-1 mt-3 rounded" id="fourth-tab" data-toggle="tab" href="#tab{{ $existing_production_order->production_order }}-4"  data-production-order="{{ $existing_production_order->production_order }}" data-operation="{{ $operation }}" role="tab" aria-controls="tab4" aria-selected="false">Pending for Confirmation <span class="badge" id="reject-confirmation-count" style="font-size: 10pt; background-color: #F96332; color: #fff">0<span></span>
            </li>
            @if (count($qa_inspection_logs) > 0)
            <li class="nav-item font-weight-bold pull-right">
                <span class="nav-link border mb-1 ml-1 mr-1 mt-3 rounded" id="third-tab" data-toggle="tab" href="#tab{{ $existing_production_order->production_order }}-3" role="tab" aria-controls="tab3" aria-selected="false">QA Inspection Log(s) <span class="badge badge-primary" style="font-size:">{{ count($qa_inspection_logs) }}<span></span>
            </li>
            @endif
        </ul>
        <style>
            .custom-tabs-rcfa .active {
                background-color: #a6acaf !important;
                color: #ffffff !important;
            }
        </style>
        <div class="tab-content" style="min-height: 300px;"> 
            @foreach ($reject_category_per_workstation as $i => $r)
            <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="rcfa{{ $existing_production_order->production_order }}-{{ $i }}" role="tabpanel" aria-labelledby="first-tab">
                <div class="row mt-2" style="min-height: 200px;">
                    <div class="col-md-12">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-striped">
                                <col style="width: 15%;"><!-- BATCH DATE -->
                                <col style="width: 20%;"><!-- MACHINE -->
                                <col style="width: 16%;"><!-- PROCESS -->
                                <col style="width: 15%;"><!-- OPERATOR -->
                                <col style="width: 8%;"><!-- GOOD QTY -->
                                <col style="width: 8%;"><!-- INSPECTED QTY -->
                                <col style="width: 8%;"><!-- REJECTED QTY -->
                                <col style="width: 10%;"><!-- ACTION -->
                                <thead class="text-primary text-uppercase" style="font-size: 5pt;">
                                    <tr>
                                        <th class="text-center p-1 font-weight-bold">BATCH DATE</th>
                                        <th class="text-center p-1 font-weight-bold">MACHINE</th>
                                        <th class="text-center p-1 font-weight-bold">PROCESS</th>
                                        <th class="text-center p-1 font-weight-bold">OPERATOR</th>
                                        <th class="text-center p-1 font-weight-bold">GOOD QTY</th>
                                        <th class="text-center p-1 font-weight-bold">INSPECTED QTY</th>
                                        <th class="text-center p-1 font-weight-bold">REJECTED QTY</th>
                                        <th class="text-center p-1 font-weight-bold">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 9pt;">
                                    @php
                                        $task_random_inspection_list = array_key_exists($r->reject_category_id, $task_random_inspection_arr) ? $task_random_inspection_arr[$r->reject_category_id] : [];
                                    @endphp
                                    @forelse($task_random_inspection_list as $row)
                                    <tr>
                                        <td class="text-center p-1 font-weight-bold">{{ $row['batch_date'] }}</td>
                                        <td class="text-center p-1 font-weight-bold">{{ $row['machine'] }}</td>
                                        <td class="text-center p-1 font-weight-bold">{{ $row['process'] }}</td>
                                        <td class="text-center p-1 font-weight-bold">{{ $row['operator_name'] }}</td>
                                        <td class="text-center p-1 font-weight-bold" style="font-size: 13pt !important;">{{ $row['good'] }}</td>
                                        <td class="text-center p-1 font-weight-bold" style="font-size: 13pt !important;">{{ $row['inspected_qty'] }}</td>
                                        <td class="text-center p-1 font-weight-bold" style="font-size: 13pt !important;">{{ $row['rejected_qty'] }}</td>
                                        <td class="text-center p-1">
                                            <button type='button' class='btn pb-2 pr-3 pt-2 pl-3 btn-primary btn-lg quality-inspection-btn' data-timelog-id="{{ $row['time_log_id'] }}" data-inspection-type="Random Inspection" data-workstation="{{ $row['workstation'] }}" data-processid="{{ $row['process_id'] }}" data-production-order="{{ $existing_production_order->production_order }}" data-reject-cat="{{ $r->reject_category_id }}">Inspect</button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-uppercase text-muted">No Task for Random Inspection</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="tab-pane" id="tab{{ $existing_production_order->production_order }}-3" role="tabpanel" aria-labelledby="third-tab">
                <div class="row" style="min-height: 200px;">
                    <div class="col-md-12">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-striped text-center">
                                <thead class="text-primary" style="font-size: 6pt;">
                                    <th class="text-center p-2"><b>PROCESS</b></th>
                                    <th class="text-center p-2"><b>INSPECTION DATE</b></th>
                                    <th class="text-center p-2"><b>INSPECTION TYPE</b></th>
                                    <th class="text-center p-2"><b>QTY</b></th>
                                    <th class="text-center p-2"><b>INSPECTED BY</b></th>
                                    <th class="text-center p-2"><b>STATUS</b></th>
                                </thead>
                                <tbody style="font-size: 10pt;">
                                    @forelse($qa_inspection_logs as $row)
                                    <tr>
                                        <td class="text-center p-2"><b>{{ $row['process'] }}</b></td>
                                        <td class="text-center p-2"><b>{{ $row['qa_inspection_date'] }}</b></td>
                                        <td class="text-center p-2"><b>{{ $row['qa_inspection_type'] }}</b></td>
                                        <td class="text-center p-2"><b>{{ $row['actual_qty_checked'] }}</b></td>
                                        <td class="text-center p-2"><b>{{ $row['qa_staff'] }}</b></td>
                                        <td class="text-center p-2">
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

            <div class="tab-pane overflow-auto" id="tab{{ $existing_production_order->production_order }}-4" role="tabpanel" aria-labelledby="fourth-tab" style="max-height: 350px;"></div>
        </div>
    </div>
    @else
        <div class="col-12 p-0 mt-5">
            <h5 class="text-center text-muted">No Inspection Criteria configured for this workstation.<br/>Please contact QA Department.</h5>
        </div>
    @endif
</div>

<script>
    $(document).ready(function (){
        get_reject_for_confirmation_count();
        function get_reject_for_confirmation_count(){
            $.ajax({
                url:"/getProductionOrderRejectForConfirmation/{{ $existing_production_order->production_order }}",
                type:"GET",
                data: {
                    operation: '{{ $operation }}',
                    get_qty: 1
                },
                success:function(response){
                    if (response.message <= 0) {
                        $('#reject-confirmation-count').parent().removeClass('d-none');
                    } else {
                        $('#reject-confirmation-count').html(response.message);
                        $('#reject-confirmation-count').parent().addClass('d-none');
                    }
                }
            });
        }
    });
</script>