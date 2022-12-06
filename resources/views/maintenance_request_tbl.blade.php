<input type="hidden" id="{{ $operation }}-total" value="{{ $list->total() }}">
<table class="table table-striped table-bordered table-hover">
    <thead class="text-primary text-uppercase" style="font-size: 7pt;">
        <th class="text-center p-2">Series</th>
        <th class="text-center p-2">Machine</th>
        <th class="text-center p-2">Category</th>
        <th class="text-center p-2">Reason</th>
        <th class="text-center p-2">Reported By</th>
        <th class="text-center p-2">Date Reported</th>
        <th class="text-center p-2">Maintenance Staff</th>
        <th class="text-center p-2">Status</th>
        <th class="text-center p-2">Action</th>
    </thead>
    <tbody style="font-size: 9pt;">
        @forelse ($list as $row)
        <tr>
            <td class="text-center p-2">{{ $row->machine_breakdown_id }}</td>
            <td class="p-2">{!! '<b>'.$row->machine_id.'</b> - '.$row->machine_name !!}</td>
            <td class="text-center p-2">{{ $row->category }}</td>
            <td class="text-center p-2">{{ $row->breakdown_reason ? $row->breakdown_reason : $row->corrective_reason }}</td>
            <td class="text-center p-2">{{ $row->reported_by }}</td>
            <td class="text-center p-2">{{ date('M-d-Y h:i A', strtotime($row->date_reported)) }}</td>
            <td class="text-center p-2">{{ $row->assigned_maintenance_staff ? $row->assigned_maintenance_staff : 'Unassigned' }}</td>
            <td class="text-center p-2">
                @php
                    switch ($row->status) {
                        case 'Pending':
                            $status = 'danger';
                            break;
                        case 'In Process':
                            $status = 'success';
                            break;
                        case 'On Hold':
                            $status = 'secondary';
                            break;
                        default:
                            $status = 'primary';
                            break;
                    }
                @endphp
                <span class="badge badge-{{ $status }}" style="font-size: 8pt;">{{ $row->status == '' ? 'Done' : $row->status }}</span>
            </td>
            <td class="text-center p-2">
                <a href="#" data-toggle="modal" data-target="#{{ $row->machine_breakdown_id }}-Modal" class="machine-details btn btn-secondary pr-3 pl-3 pt-2 pb-2" data-breakdown="{{ $row->machine_breakdown_id }}">
                    <i class="now-ui-icons design-2_ruler-pencil"></i>
                </a>

                <div class="modal fade" id="{{ $row->machine_breakdown_id }}-Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content text-left">
                            <div class="modal-header">
                                <div class="row w-100">
                                    <div class="col-6">
                                        <h5 class="modal-title">{{ $row->machine_breakdown_id }}</h5>
                                    </div>
                                    <div class="col-6 text-right">
                                        <h6 class="modal-title">Date Reported: {{ date('M-d-Y h:i A', strtotime($row->date_reported)) }}</h5>
                                    </div>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="/update_maintenance_request/{{ $row->machine_breakdown_id }}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-2">
                                            <center>
                                                <img src="{{ asset($row->image) }}" alt="" class="w-100 mx-auto">
                                            </center>
                                        </div>
                                        <div class="col-10">
                                            <div class="row">
                                                <div class="col-3 offset-6">
                                                    <label class="pl-2">Assigned Maintenance Staff</label>
                                                    <select class="form-control" name="maintenance_staff" id="{{ $row->machine_breakdown_id }}-maintenance-staff">
                                                        <option value="" {{ $row->assigned_maintenance_staff ? null : 'selected' }} disabled>Select Maintenance Staff</option>
                                                        @foreach ($maintenance_staff as $staff)
                                                            <option value="{{ $staff->employee_name }}" {{ $row->assigned_maintenance_staff == $staff->employee_name ? 'selected' : null }}>{{ $staff->employee_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-3">
                                                    @php
                                                        $statuses = array('Pending', 'On Hold', 'In Process', 'Done');
                                                        $current_status = $row->status == '' ? 'Done' : $row->status;
        
                                                        $start = $row->work_started ? Carbon\Carbon::parse($row->work_started) : null;
                                                        $end = $row->date_resolved ? Carbon\Carbon::parse($row->date_resolved) : null;
                                                        $duration = null;
        
                                                        if($start and $end){
                                                            $days = $start->diffInDays($end);
                                                            $hours = $start->copy()->addDays($days)->diffInHours($end);
                                                            $minutes = $start->copy()->addDays($days)->addHours($hours)->diffInMinutes($end);
                                                            $dur_days = ($days > 0) ? $days .'d' : null;
                                                            $dur_hours = ($hours > 0) ? $hours .'h' : null;
                                                            $dur_minutes = ($minutes > 0) ? $minutes .'m' : null;
        
                                                            $duration = $dur_days .' '. $dur_hours . ' '. $dur_minutes;
                                                        }
                                                    @endphp
                                                    <label class="pl-2">Status</label>
                                                    <select class="form-control" name="status_update" id="{{ $row->machine_breakdown_id }}-status" onchange="status_check('{{ $row->machine_breakdown_id }}')" required>
                                                        <option value="" disabled>Select a Status</option>
                                                        @foreach ($statuses as $status)
                                                            <option value="{{ $status }}" {{ $current_status == $status ? 'selected' : null }}>{{ $status }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            @if($row->work_started or $row->date_resolved)
                                                <div class="row p-2 h-100">
                                                    <div class="col">
                                                        <b>Work Started:</b> {{ $row->work_started ? Carbon\Carbon::parse($row->work_started)->format('M d, Y h:i A') : null }}
                                                    </div>
                                                    <div class="col">   
                                                        <b>Date Resolved:</b> {{ $row->date_resolved ? Carbon\Carbon::parse($row->date_resolved)->format('M d, Y h:i A') : null }}
                                                    </div>
                                                    <div class="col">
                                                        <b>Work Duration:</b> {{ $duration }}
                                                    </div>
                                                </div>
                                                <br>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4">
                                            <p><b>Series: </b>{{ $row->machine_breakdown_id }}</p>
                                            <p><b>Machine ID: </b>{{ $row->machine_id }}</p>
                                            <p><b>Machine Name: </b>{{ $row->machine_name }}</p>
                                        </div>
                                        <div class="col-4">
                                            <p><b>Category:</b> {{ $row->category }}</p>
                                            <p><b>Corrective Reason:</b> {{ $row->corrective_reason }}</p>
                                            <p><b>Breakdown Reason:</b> {{ $row->breakdown_reason }}</p>
                                        </div>
                                        <div class="col-4">
                                            <p><b>Reported By: </b>{{ $row->reported_by }}</p>
                                            <p><b>Date Reported: </b>{{ date('M-d-Y h:i A', strtotime($row->date_reported)) }}</p>
                                        </div>
                                    </div>
                                    <div class="row"> 
                                        <div class="container">
                                            <div class="hid" id="{{ $row->machine_breakdown_id }}-findings-container">
                                                <label>Findings</label>
                                                <textarea class="w-100 char-count" data-machine-id="{{ $row->machine_breakdown_id }}" id='{{ $row->machine_breakdown_id }}-findings' name="findings" cols="30" rows="3">{{ $row->findings }}</textarea>
                                            </div>
                                            <div class="hid" id="{{ $row->machine_breakdown_id }}-hold-container">
                                                <label>Reason for Hold</label>
                                                <textarea class="w-100 char-count" data-machine-id="{{ $row->machine_breakdown_id }}" id="{{ $row->machine_breakdown_id }}-hold-reason" name="hold_reason" cols="30" rows="3">{{ $row->hold_reason }}</textarea>
                                            </div>
                                            <div class="hid" id="{{ $row->machine_breakdown_id }}-work-done-container">
                                                <label>Work Done</label>
                                                <textarea class="w-100 char-count" data-machine-id="{{ $row->machine_breakdown_id }}" id='{{ $row->machine_breakdown_id }}-work-done' name="work_done" cols="30" rows="3">{{ $row->work_done }}</textarea>
                                            </div>
                                            <small class="d-none" id="{{ $row->machine_breakdown_id }}-info" style="color: red">Only 255 characters are allowed.</small>
                                            <br>
                                            <button type="submit" class="btn btn-primary float-right" id="{{ $row->machine_breakdown_id }}-submit-btn">Submit</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-12">
                                        <br/>
                                        <div class="float-right font-italic">
                                            <small>Last modified by: {{ $row->last_modified_by }} - {{ $row->last_modified_at }}</small><br>
                                            <small>Created by: {{ $row->created_by }} - {{ $row->created_at }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan=9 class="text-center">No Result(s) Found</td>
        </tr>
        @endforelse
    </tbody>
</table>
<div class="float-right mt-4 custom-{{ $operation }}-pagination">
    {{ $list->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>
<script>
    $('.hid').slideUp();

    $('.char-count').keyup(function(){
        enable_submit($(this).data('machine-id'));
        if(parseInt($(this).val().length) > 255){
            $(this).css('border', '1px solid red');
        }else{
            $(this).css('border', '1px solid #8F8F9D');
        }
    });
</script>