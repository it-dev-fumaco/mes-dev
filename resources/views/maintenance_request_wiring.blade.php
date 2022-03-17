<input type="hidden" id="wiring-total" value="{{ $list->total() }}">
<table class="table table-striped">
    <thead class="text-primary">
        <th class="text-center font-weight-bold">Series</th>
        <th class="text-center font-weight-bold">Machine</th>
        <th class="text-center font-weight-bold">Category</th>
        <th class="text-center font-weight-bold">Reason</th>
        <th class="text-center font-weight-bold">Reported By</th>
        <th class="text-center font-weight-bold">Date Reported</th>
        <th class="text-center font-weight-bold">Maintenance Staff</th>
        <th class="text-center font-weight-bold">Status</th>
        <th class="text-center font-weight-bold">Actions</th>
    </thead>
    <tbody>
        @forelse ($list as $row)
        <tr>
            <td class="text-center">{{ $row->machine_breakdown_id }}</td>
            <td class="text-center">{{ $row->machine_id }}</td>
            <td class="text-center">{{ $row->category }}</td>
            <td class="text-center">{{ $row->breakdown_reason ? $row->breakdown_reason : $row->corrective_reason }}</td>
            <td class="text-center">{{ $row->reported_by }}</td>
            <td class="text-center">{{ date('M-d-Y h:i A', strtotime($row->date_reported)) }}</td>
            <td class="text-center">{{ $row->assigned_maintenance_staff ? $row->assigned_maintenance_staff : 'Unassigned' }}</td>
            <td class="text-center">
                @php
                    if($row->status == 'Pending'){
                        $status = 'danger';
                    }else if($row->status == 'In Process'){
                        $status = 'success';
                    }else if($row->status == 'On Hold'){
                        $status = 'secondary';
                    }else{
                        $status = 'primary';
                    }
                @endphp
                <span class="badge badge-{{ $status }}" style="font-size: 10pt">{{ $row->status == '' ? 'Done' : $row->status }}</span>
            </td>
            <td class="text-center">
                <a href="#" data-toggle="modal" data-target="#{{ $row->machine_breakdown_id }}-Modal" class="machine-details" data-breakdown="{{ $row->machine_breakdown_id }}">
                    <i class="fas fa-edit" style="font-size: 20px; color: #fff; background-color: #000; border-radius: 50%; padding: 5px"></i>
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
                                        <div class="col-4"></div>
                                        <div class="col-3">
                                            <label class="pl-2">Assigned Maintenance Staff</label>
                                            <select class="form-control" name="maintenance_staff">
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
                                        <div class="row" style="margin-top: -25px">
                                            <div class="col-2"></div>
                                            <div class="col">
                                                <b>Work Started:</b> {{ $row->work_started ? Carbon\Carbon::parse($row->work_started)->format('M d, Y h:i A') : null }}
                                            </div>
                                            {{-- <div class="col">   
                                                <b>Date Resolved:</b> {{ $row->date_resolved ? Carbon\Carbon::parse($row->date_resolved)->format('M d, Y h:i A') : null }}
                                            </div>
                                            <div class="col">
                                                <b>Work Duration:</b> {{ $duration }}
                                            </div> --}}
                                        </div>
                                        <br>
                                    @endif
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
                                            <div class="hid" id="{{ $row->machine_breakdown_id }}-hold-container">
                                                <label>Reason for Hold</label>
                                                <textarea class="w-100" id="{{ $row->machine_breakdown_id }}-hold-reason" name="hold_reason" cols="30" rows="3">{{ $row->hold_reason }}</textarea>
                                            </div>
                                            <div class="hid" id="{{ $row->machine_breakdown_id }}-done-container">
                                                <div class="row">
                                                    <div class="col">
                                                        <label>Findings</label>
                                                        <textarea class="w-100" id='{{ $row->machine_breakdown_id }}-findings' name="findings" cols="30" rows="3">{{ $row->findings }}</textarea>
                                                    </div>
                                                    <div class="col" id="{{ $row->machine_breakdown_id }}-work-done-input">
                                                        <label>Work Done</label>
                                                        <textarea class="w-100" id='{{ $row->machine_breakdown_id }}-work-done' name="work_done" cols="30" rows="3">{{ $row->work_done }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <button type="submit" class="btn btn-primary float-right">Submit</button>
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
<div class="float-right mt-4 custom-wiring-pagination" data-div='wiring'>
    {{ $list->appends(request()->query())->links('pagination::bootstrap-4') }}
</div>
<script>
    $('.hid').slideUp();
</script>