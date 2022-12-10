<input type="hidden" id="{{ $operation }}-total" value="{{ $list->total() }}">
<table class="table table-striped table-bordered table-hover">
    <thead class="text-primary text-uppercase" style="font-size: 7pt;">
        <th class="text-center p-2">Series</th>
        <th class="text-center p-2">Machine</th>
        <th class="text-center p-2">Category</th>
        <th class="text-center p-2">Reason</th>
        <th class="text-center p-2">Reported By</th>
        <th class="text-center p-2">Date Reported</th>
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
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content text-left">
                            <div class="modal-header" style="background-color: #0277BD; color: #fff;">
                                <div class="row w-100">
                                    <div class="col-6">
                                        <h5 class="modal-title">{{ $row->machine_breakdown_id }}</h5>
                                    </div>
                                    <div class="col-6 text-right">
                                        <h6 class="modal-title">
                                            Date Reported: {{ date('M-d-Y h:i A', strtotime($row->date_reported)) }} 
                                        </h6>
                                    </div>
                                </div>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" style="color: #fff;">&times;</span>
                                </button>
                            </div>
                            <div id="{{ $row->machine_breakdown_id }}-container" class="modal-body">
                                <form action="/update_maintenance_request/{{ $row->machine_breakdown_id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-2">
                                            <center>
                                                <img src="{{ asset($row->image) }}" alt="" class="w-100 mx-auto">
                                            </center>
                                        </div>
                                        <div class="col-10">
                                            <h5 class="mb-0"><b>{{ $row->machine_id }}</b> - {{ $row->machine_name }}</h5>
                                            <div class="row p-0">
                                                <div class="col-6">
                                                    <span><b>Building/Equipment: </b>{{ $row->building }}</span><br>
                                                    <span><b>Type: </b>{{ $row->type }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <span><b>Category: </b>{{ $row->category }}</span><br>
                                                    @if ($row->category == 'Corrective')
                                                        <span><b>Corrective Reason: </b>{{ $row->corrective_reason }}</span>
                                                    @else
                                                        <span><b>Breakdown Reason: </b>{{ $row->breakdown_reason }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2">
                                        <div class="col-4">
                                            <div class="mb-1">
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
                                                <label><b>Status</b></label>
                                                <select class="form-control rounded" name="status_update" id="{{ $row->machine_breakdown_id }}-status" data-container="#{{ $row->machine_breakdown_id }}-container" onchange="status_check('{{ $row->machine_breakdown_id }}')" required>
                                                    <option value="" disabled>Select a Status</option>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status }}" {{ $current_status == $status ? 'selected' : null }}>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <br>
                                            <div id="{{ $row->machine_breakdown_id }}-date-started" class="mb-2 {{ $current_status == 'On Hold' ? 'hid' : null }}">
                                                <label><b>Date Started</b></label>
                                                <input type="date" class="form-control rounded" name="date_started" value="{{ $row->work_started ? Carbon\Carbon::parse($row->work_started)->format('Y-m-d') : null }}">
                                            </div>
                                            <div id="{{ $row->machine_breakdown_id }}-date-resolved" class="mb-2 {{ $current_status != 'Done' ? 'hid' : null }}">
                                                <label><b>Completion Date</b></label>
                                                <input type="date" class="form-control rounded" name="date_resolved" value="{{ $row->date_resolved ? Carbon\Carbon::parse($row->date_resolved)->format('Y-m-d') : null }}">
                                            </div>
                                            <div class="row">
                                                @php
                                                    $maintenance_staffs = collect($operators)->where('department', 'Plant Services');
                                                    $staffs = isset($assigned_staffs[$row->machine_breakdown_id]) ? $assigned_staffs[$row->machine_breakdown_id] : [];
                                                @endphp
                                                <select class="d-none form-control rounded" id="{{ $row->machine_breakdown_id }}-staff-select">
                                                    <option value="">Select Maintenance Staff</option>
                                                    @foreach ($maintenance_staffs as $e)
                                                        <option value="{{ $e->operator_id }}">{{ $e->employee_name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="container-fluid mx-auto">
                                                    <br/>
                                                    <table class="table table-bordered" id="{{ $row->machine_breakdown_id }}-staff-table" style="font-size: 9pt !important;">
                                                        <thead>
                                                            <tr>
                                                                <td scope="col" class="text-center p-2 font-weight-bold">Maintenenance Staff</td>
                                                                <td class="text-center p-2 d-print-none" style="width: 10%;">
                                                                    <button type="button" class="btn btn-outline-primary btn-sm add-row-btn" id="add-staff-btn" data-table="#{{ $row->machine_breakdown_id }}-staff-table" data-select="#{{ $row->machine_breakdown_id }}-staff-select" style="font-size: 9pt !important;">Add</button>
                                                                </td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($staffs as $staff)
                                                                <tr>
                                                                    <td class="p-2">
                                                                        <select name="maintenance_staff[]" class="form-control w-100" style="width: 100%;" required>
                                                                            <option value="">Select Maintenance Staff</option>
                                                                            @foreach ($maintenance_staffs as $e)
                                                                                <option value="{{ $e->operator_id }}" {{ $staff->user_id == $e->operator_id ? 'selected' : null }}>{{ $e->employee_name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td class="text-center d-print-none">
                                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-td-row"><i class="now-ui-icons ui-1_simple-remove ont-weight-bold" style="cursor: pointer; font-size: 8pt !important;"></i></button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row d-print-none">
                                                <div class="custom-file m-3 text-left">
                                                    <input type="file" class="custom-file-input" id="customFile" name="file">
                                                    <label class="custom-file-label" for="customFile">Choose File</label>
                                                </div>
                                                @if ($row->attached_files)
                                                    <div class="col-12">
                                                        <h6>Attachments:</h6>
                                                        <ul>
                                                            @foreach (explode(',', $row->attached_files) as $file)
                                                                <li>
                                                                    <a href="{{ asset('/storage/files/maintenance/'.$row->machine_breakdown_id.'/'.$file) }}" target="_blank">{{ $file }}</a>
                                                                    &nbsp;
                                                                    <a href="#" class="text-danger" data-toggle="modal" data-target="#removeFileModal" style="text-decoration: none; text-transform: none;">
                                                                        <i class="now-ui-icons ui-1_simple-remove font-weight-bold" style="cursor: pointer"></i>
                                                                    </a>

                                                                    <!-- Modal -->
                                                                    <div class="modal fade" id="removeFileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                <h5 class="modal-title" id="exampleModalLabel">Remove File</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    Remove {{ $file }}?
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>&nbsp;
                                                                                    <a href="/remove_file?file={{ $file }}&id={{ $row->machine_breakdown_id }}" class="btn btn-primary">Confirm</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-8">
                                            <div>
                                                <label for="remarks"><b>Complaints/Problems</b></label>
                                                <textarea name="complaints" rows="4" class="form-control rounded" style="border: 1px solid #CED4DA;">{{ $row->complaints }}</textarea>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-6">
                                                    <span><b>Requested By: </b>{{ $row->reported_by }}</span><br>
                                                    <span><b>Date Reported: </b>{{ $row->date_reported ? Carbon\Carbon::parse($row->date_reported)->format('M d, Y h:i A') : null }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <span><b>Approved By: </b>{{ $row->created_by }}</span><br>
                                                    <span><b>Date: </b>{{ $row->created_at ? Carbon\Carbon::parse($row->created_at)->format('M d, Y h:i A') : null }}</span>
                                                </div>
                                            </div>
                                            <br>
                                            <center>
                                                <p><b>For Plant Services (Maintenance Dept.) Only</b></p>
                                            </center>
                                            <div class="mt-3">
                                                <label for="findings"><b>Findings</b></label>
                                                <textarea name="findings" rows="4" class="form-control rounded" style="border: 1px solid #CED4DA;">{{ $row->findings }}</textarea>
                                            </div>

                                            <div id="{{ $row->machine_breakdown_id }}-hold-container" class="hid mt-4">
                                                <label for="hold_reason"><b>Reason for Hold</b></label>
                                                <textarea name="hold_reason" rows="4" class="form-control rounded" style="border: 1px solid #CED4DA;">{{ $row->findings }}</textarea>
                                            </div>

                                            <div id="{{ $row->machine_breakdown_id }}-work-done-container" class="hid mt-4">
                                                <label for="work_done"><b>Work Done</b></label>
                                                <textarea name="work_done" rows="4" class="form-control rounded" style="border: 1px solid #CED4DA;">{{ $row->findings }}</textarea>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <label for="remarks"><b>Remarks</b></label>
                                                <textarea name="remarks" rows="4" class="form-control rounded" style="border: 1px solid #CED4DA;">{{ $row->remarks }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 d-print-none">
                                        <div class="col-12 pt-2">
                                            <div class="float-right">
                                                <button type="button" class="btn btn-secondary printBtn" data-print-area="{{ $row->machine_breakdown_id }}-container">
                                                    <i class="now-ui-icons education_paper"></i> Print
                                                </button>
                                                <button type="submit" class="btn btn-primary" id="{{ $row->machine_breakdown_id }}-submit-btn">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="row d-print-none">
                                    <div class="col-12">
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
<style>
    .modal{
        background-color: rgba(0,0,0,0.4);
    }
</style>
<script>
    $('.hid').slideUp();

    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").change(function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $('.printBtn').on('click', function (){
        var print_area = '#' + $(this).data('print-area');

        $(print_area).printThis();
    });

    $('.char-count').keyup(function(){
        enable_submit($(this).data('machine-id'));
        if(parseInt($(this).val().length) > 255){
            $(this).css('border', '1px solid red');
        }else{
            $(this).css('border', '1px solid #8F8F9D');
        }
    });
</script>