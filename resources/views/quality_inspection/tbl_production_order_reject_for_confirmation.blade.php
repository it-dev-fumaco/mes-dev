<table style="width: 100%; border-color: #D5D8DC;">
    <col style="width: 15%;">
    <col style="width: 27%;">
    <col style="width: 23%;">
    <col style="width: 20%;">
    <col style="width: 15%;">
    <tbody>
        <tr style="font-size: 9pt;">
            <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REFERENCE NO.</b></td>
            <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>CUSTOMER</b></td>
            <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROJECT</b></td>
            <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>DELIVERY DATE</b></td>
            <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>QTY</b></td>
        </tr>
        <tr style="font-size: 10pt;">
            <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $production_order_details->sales_order . $production_order_details->material_request }}</td>
            <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $production_order_details->customer }}</td>
            <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $production_order_details->project }}</td>
            <td class="text-center" style="border: 1px solid #ABB2B9;">{{ \Carbon\Carbon::parse($production_order_details->delivery_date)->format('M. d, Y') }}</td>
            <td class="text-center" style="border: 1px solid #ABB2B9;">
                <span class="d-block" style="font-size: 13pt;">{{ $production_order_details->qty_to_manufacture }}</span>
                <small class="d-block">{{ $production_order_details->stock_uom }}</small>
            </td>
        </tr>
        <tr style="font-size: 10pt;">
            <td style="border: 1px solid #ABB2B9; font-size: 9pt;" class="text-center"><b>ITEM DETAIL(S):</b></td>
            <td style="border: 1px solid #ABB2B9;" colspan="4"><span class="font-weight-bold">{{ $production_order_details->item_code }}</span> - <span>{!! $production_order_details->description !!}</span></td>
        </tr>
    </tbody>
</table>

<form action="/submitRejectConfirmation/{{ $production_order_details->production_order }}" method="POST" id="reject-confirmation-form">
    @csrf
    <table class="table table-bordered mt-3">
        <thead class="bg-secondary text-white text-uppercase" style="font-size: 7pt;">
            <th class="p-1 text-center" style="width: 15%;">Workstation</th>
            <th class="p-1 text-center" style="width: 15%;">Date Reported</th>
            <th class="p-1 text-center" style="width: 18%;">Operator Name</th>
            <th class="p-1 text-center" style="width: 10%;">Declared Reject</th>
            <th class="p-1 text-center" style="width: 15%;">Reject Type</th>
            <th class="p-1 text-center" style="width: 12%;">Confirmed Reject Qty</th>
            <th class="p-1 text-center" style="width: 10%;">Disposition</th>
            <th class="p-1 text-center" style="width: 5%;">-</th>
        </thead>
        <tbody style="font-size: 9pt;">
            @forelse ($timelogs as $timelog)
            @php
                $rejection_arr = collect($reject_confirmation_list)->groupBy('time_log_id');
                $select_arr = $r = [];
                if(isset($rejection_arr[$timelog])){
                    $r = $rejection_arr[$timelog][0];
                    $select_arr = $rejection_arr[$timelog];
                }
                
                if ($production_order_details->operation_id == 3) {
                    $workstation_checklist = $checklist;
                } else {
                    $workstation_checklist = array_key_exists($r->workstation, $checklist) ? $checklist[$r->workstation] : [];
                }
            @endphp
            <tr>
                <td class="text-center p-1">{{ $r->workstation }}
                    <input type="hidden" value="{{ $production_order_details->operation_id }}" name="operation_id">
                    <input type="hidden" value="{{ $r->workstation }}" name="workstation[{{ $r->time_log_id }}]">
                    <input type="hidden" value="{{ $r->qa_id }}" name="qa_id[{{ $r->time_log_id }}]">
                    <input type="hidden" value="{{ $r->rejected_qty }}" name="old_reject_qty[{{ $r->time_log_id }}]">
                </td>
                <td class="text-center p-1">{{ \Carbon\Carbon::parse($r->created_at)->format('M. d, Y h:i A') }}</td>
                <td class="text-center p-1">{{ $r->operator_name }}</td>
                <td class="text-center p-1">{{ $r->rejected_qty }}</td>
                <td class="text-center p-1">
                    @foreach ($select_arr as $item)
                        <div class="p-1">
                            <select class="form-control rounded" name="reject_type[{{ $timelog }}][{{ $item->reject_reason_id }}]" required>
                                <option value="" {{ !in_array($item->reject_list_id, collect($workstation_checklist)->pluck('reject_list_id')->toArray()) ? 'selected' : null }} disabled>Select Reject Type</option>
                                @forelse($workstation_checklist as $i)
                                    <option value="{{ $i->reject_list_id }}" {{ $item->reject_list_id == $i->reject_list_id ? 'selected' : '' }}>{{ $i->reject_reason }}</option>
                                @empty
                                    <option value="">No Reject Type(s)</option>
                                @endforelse
                            </select>
                        </div>
                    @endforeach
                </td>
                <td class="text-center p-1">
                    <input type="text" class="form-control rounded" name="confirmed_reject[{{ $r->time_log_id }}]" required>
                </td>
                <td class="text-center p-1">
                    <select class="form-control rounded" name="disposition[{{ $r->time_log_id }}]" required>
                        <option value="">Select Disposition</option>
                        <option value="Use As Is">"Use As Is"</option>
                        <option value="Rework">For Rework</option>
                        <option value="Scrap">Scrap / Defective</option>
                    </select>
                </td>
                <td class="text-center p-1">
                    <button class="btn btn-danger btn-sm remove-workstation-row">&times;</button>
                </td>
            </tr>
            @empty
                <tr>
                    <td class="text-center p-1" colspan="8">No record(s) found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pull-right">
        <button class="btn btn-primary" type="submit" {{ count($timelogs) <= 0 ? 'disabled' : null }}>Submit</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</form>