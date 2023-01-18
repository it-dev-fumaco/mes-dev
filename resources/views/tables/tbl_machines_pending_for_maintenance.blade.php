<div id="maintenenance-timelogs-tbl" class="container-fluid">
    @forelse ($machine_breakdown as $i => $machine)
        @php
            switch ($machine->status) {
                case 'Pending':
                    $badge = 'primary';
                    break;
                case 'In Process':
                    $badge = 'warning';
                    break;
                case 'On Hold':
                case 'Cancelled':
                case 'Unavailable':
                    $badge = 'secondary';
                    break;
                default:
                    $badge = null;
                    break;
            }
        @endphp
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-2 p-2 d-flex flex-row justify-content-start align-items-center">
                        <img src="{{ asset($machine->image) }}" class="w-100" alt="">
                    </div>
                    <div class="col-7" style="font-size: 10pt;">
                        <span class="badge badge-{{ $badge }}">{{ $machine->status }}</span> <span>{{ $machine->machine_code }} - {{ $machine->machine_name }}</span><br>
                        <span>{{ $machine->type }} - {{ $machine->type == 'Breakdown' ? $machine->breakdown_reason : $machine->corrective_reason }}</span> <br>
                        @if ($machine->findings)
                            {{ $machine->findings }}
                            <br>
                        @endif
                        @isset($workstation[$machine->machine_code])
                            @php
                                $workstations = collect($workstation[$machine->machine_code])->pluck('workstation');
                                $imploded = collect($workstations)->implode(', ');
                            @endphp
                            [<span class="font-weight-bold">{{ $imploded }}</span>]
                        @endisset
                        @if (in_array($machine->breakdown_status, ['In Process', 'In Progress']) && isset($in_process_timelogs[$machine->machine_breakdown_id]))
                            <span>Maintenance Personnel: {{ $in_process_timelogs[$machine->machine_breakdown_id][0]->operator_name }}</span>
                        @endif
                    </div>
                    <div class="col-3 d-flex flex-row justify-content-start align-items-center">
                        @if (!in_array($machine->breakdown_status, ['In Process', 'In Progress'])) 
                            <button type="button" class="btn btn-block btn-danger btn-lg text-uppercase pl-0 pr-0 maintnenance-access-id-modal-trigger" style="font-size: 11pt;"
                            data-machine-breakdown-id="{{ $machine->machine_breakdown_id }}"
                            data-maintenance-status="{{ $machine->status }}"
                            data-machine-id="{{ $machine->machine_code }}"
                            >
                                <i class="now-ui-icons media-1_button-play" style="font-size: 12pt;"></i> Start Now
                            </button>
                        @else
                            <button type="button" class="btn btn-block btn-warning maintnenance-access-id-modal-trigger sub-btn m-0" style="height: 90px;"
                            data-machine-breakdown-id="{{ $machine->machine_breakdown_id }}"
                            data-maintenance-status="{{ $machine->status }}"
                            data-machine-id="{{ $machine->machine_code }}"
                            >
                                <div class="waves-effect waves z-depth-4">
                                    <div class="spinner-grow">
                                        <span class="sr-only">Loading...</span>
                                    </div> <br>
                                    <span class="text-center font-weight-bold" style="color: #273746; font-size: 10pt;">In Progress</span>
                                </div>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div> 
    @empty
        <center>
            <h5>No record(s) found.</h5>
        </center>
    @endforelse
</div>
<span id="operation-placeholder" class="d-none">{{ $operation }}</span>

<!-- Modal -->
<div class="modal fade" id="maintenance-access-id-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #112C4E; color: #fff;">
                <h5 class="modal-title" id="exampleModalLabel">Maintenance</h5>
                <button type="button" class="close" onclick="$('#maintenance-access-id-modal').modal('hide');" style="color: #fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="enter-production-order">
                    <div class="col-md-10 offset-md-1">
                       <h6 class="text-center" style="text-transform: uppercase;">enter your biometric id</h6>
                       <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" class="form-control w-100" id="access-id" style="font-size: 15pt;" required>
                                </div>
                            </div>
                       </div>
                      
                        <div id="access-id-numpad">
                            <div class="text-center">
                                <div class="row1">
                                    <span class="numpad num">1</span>
                                    <span class="numpad num">2</span>
                                    <span class="numpad num">3</span>
                                </div>
                                <div class="row1">
                                    <span class="numpad num">4</span>
                                    <span class="numpad num">5</span>
                                    <span class="numpad num">6</span>
                                </div>
                                <div class="row1">
                                    <span class="numpad num">7</span>
                                    <span class="numpad num">8</span>
                                    <span class="numpad num">9</span>
                                </div>
                                <div class="row1">
                                    <span class="numpad" onclick="document.getElementById('access-id').value=document.getElementById('access-id').value.slice(0, -1);"><</span>
                                    <span class="numpad num">0</span>
                                    <span class="numpad" onclick="document.getElementById('access-id').value='';">Clear</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10 offset-md-1">
                                    <button type="button" class="btn btn-block btn-primary btn-lg" id="submit-access-id">SUBMIT</button>
                                </div>
                            </div>
                            <div class="d-none">
                                <input id="machine-breakdown-id" name="machine-breakdown-id">
                                <input id="machine-id" name="machine-id">
                                <input type="checkbox" name="is_completed" id="is-completed">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .modal{
        background-color: rgba(0,0,0, 0.4);
    }
</style>