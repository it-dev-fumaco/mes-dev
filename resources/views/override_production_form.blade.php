<form action="/update_override_production_form" method="POST" autocomplete="off" id="override-production-order-form">
    @csrf
    <input type="hidden" name="production_order" value="{{ $production_order_details->production_order }}">
    <div class="row m-0">
        <div class="col-md-12 p-2">
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
                    <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $production_order_details->sales_order }} {{ $production_order_details->material_request }}</td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $production_order_details->customer }}</td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $production_order_details->project }}</td>
                    <td class="text-center" style="border: 1px solid #ABB2B9;">{{ $production_order_details->delivery_date }}</td>
                    <td class="text-center" style="border: 1px solid #ABB2B9; font-size: 15pt;">{{ $production_order_details->qty_to_manufacture }}</td>
                </tr>
                <tr style="font-size: 10pt;">
                    <td style="border: 1px solid #ABB2B9; font-size: 9pt;" class="text-center"><b>ITEM DETAIL(S):</b></td>
                    <td style="border: 1px solid #ABB2B9;" colspan="4"><span class="font-weight-bold">{{ $production_order_details->item_code }}</span> - <span>{{ $production_order_details->description }}</span></td>
                </tr>
            </table>

            <table class="mt-3" style="width: 100%; border-color: #D5D8DC;">
                <col style="width: 15%;">
                <col style="width: 17%;">
                <col style="width: 8%;">
                <col style="width: 8%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 10%;">
                <col style="width: 12%;">
                <thead style="font-size: 10pt;">
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>WORKSTATION</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>PROCESS</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>GOOD</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>REJECT</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>MACHINE</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>START</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>END</b></td>
                    <td class="text-center" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;"><b>OPERATOR</b></td>
                </thead>
                <tbody style="font-size: 9pt;">
                    @foreach ($production_order_operations as $row)
                    @php
                        if ($row->workstation == 'Spotwelding') {
                            $logs = array_key_exists($row->job_ticket_id, $spotwelding_operator_logs) ? $spotwelding_operator_logs[$row->job_ticket_id] : [];
                        } else {
                            $logs = array_key_exists($row->job_ticket_id, $operator_logs) ? $operator_logs[$row->job_ticket_id] : [];
                        }

                        $rowspan = count($logs) > 0 ? 'rowspan="' . count($logs) . '"' : ''; 
                    @endphp
                    <tr>
                        <td class="text-center p-2 font-weight-bold" style="border: 1px solid #ABB2B9;" {!! $rowspan !!}>{{ $row->workstation }}</td>
                        <td class="text-center p-2 font-weight-bold" style="border: 1px solid #ABB2B9;" {!! $rowspan !!}>{{ $row->process }}</td>
                        @if (count($logs) > 0)
                            
                        @foreach ($logs as $log)
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <input type="hidden" name="job_ticket[{{ $row->job_ticket_id }}][has_time_logs]" value="1">
                            <div class="form-group m-0">
                                <input type="text" class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][{{ $log->time_log_id }}][good]" value="{{ $log->good }}" required>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <input type="text" class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][{{ $log->time_log_id }}][reject]" value="{{ $log->reject }}" required>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                @php
                                    $machines = array_key_exists($row->process_id, $machine_per_process) ? $machine_per_process[$row->process_id] : [];
                                @endphp
                                <select class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][{{ $log->time_log_id }}][machine]" required>
                                    <option value="">Select Machine</option>
                                    @foreach ($machines as $machine)
                                    <option value="{{ $machine->machine_code }}" {{ $log->machine_code == $machine->machine_code ? 'selected' : '' }}>{{ $machine->machine_code . ' - ' . $machine->machine_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <input type="datetime-local" class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][{{ $log->time_log_id }}][start_time]" value="{{ $log->from_time }}" required>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <input type="datetime-local" class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][{{ $log->time_log_id }}][end_time]" value="{{ $log->to_time }}" required>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <select class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][{{ $log->time_log_id }}][operator]" required>
                                    <option value="">Select Operator</option>
                                    @foreach ($operators as $user_id => $operator)
                                    <option value="{{ $user_id }}" {{ $log->operator_id == $user_id ? 'selected' : '' }}>{{ $operator }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                        @else
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <input type="text" class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][good]" required>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <input type="text" class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][reject]" required>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                @php
                                    $machines = array_key_exists($row->process_id, $machine_per_process) ? $machine_per_process[$row->process_id] : [];
                                @endphp
                                <select class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][machine]" required>
                                    <option value="">Select Machine</option>
                                    @foreach ($machines as $machine)
                                    <option value="{{ $machine->machine_code }}">{{ $machine->machine_code . ' - ' . $machine->machine_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <input type="datetime-local" class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][start_time]" required>
                            </div></td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <input type="datetime-local" class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][end_time]" required>
                            </div>
                        </td>
                        <td class="text-center p-2" style="border: 1px solid #ABB2B9;">
                            <div class="form-group m-0">
                                <select class="form-control rounded" name="job_ticket[{{ $row->job_ticket_id }}][operator]" required>
                                    <option value="">Select Operator</option>
                                    @foreach ($operators as $user_id => $operator)
                                    <option value="{{ $user_id }}">{{ $operator }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</form>