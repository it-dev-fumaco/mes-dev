<div class="col-12">
  <table class="table table-bordered">
    <col style="width: 5%;">
    <col style="width: 37%;">
    <col style="width: 19%;">
    <col style="width: 19%;">
    <col style="width: 20%;">
    <thead class="bg-secondary text-white text-uppercase" style="font-size: 10px;">
      <th class="p-2 text-center">ID</th>
      <th class="p-2 text-center">Process Name</th>
      <th class="p-2 text-center">Last Modified By</th>
      <th class="p-2 text-center">Last Modified At</th>
      <th class="p-2 text-center">Action</th>
    </thead>
    @forelse ($assigned_processes as $process => $details)
      @php
        $last_modified_by = collect($details)->pluck('last_modified_by')->first();
        $last_modified_at = collect($details)->pluck('last_modified_at')->first();
        $process_id = collect($details)->pluck('process_id')->first()
      @endphp
       <tbody>
        <tr>
          <td class="text-center">{{ $process_id }}</td>
          <td class="text-center">{{ $process }}</td>
          <td class="text-center">{{ $last_modified_by }}</td>
          <td class="text-center">{{ Carbon\Carbon::parse($last_modified_at)->format('F d, Y h:i A') }}</td>
          <td>
            <div class="row p-1">
              <div class="col-9 p-0 d-flex justify-content-end align-items-end">
                <button class="btn btn-info btn-sm" type="button" data-toggle="collapse" data-target="#collapse{{ $process_id }}" aria-expanded="false" aria-controls="collapse{{ $process_id }}">
                  View Assigned Machines
                </button>
              </div>
              <div class="col-2 p-0 pl-1">
                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#remove-process-{{ $process_id }}">&times;</button>

                <div class="modal fade" id="remove-process-{{ $process_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Remove {{ $process }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to remove <b>{{ $process }}</b>?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary remove-assigned-process" data-id="{{ $process_id }}">Confirm</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td colspan="5" style="padding: 0 !important;background-color: #F7F9F9 !important;">
            <div class="collapse m-2" id="collapse{{ $process_id }}">
              <form action="/process_assignment/{{ $id }}/add" class="add-process-form" method="post">
                @csrf
                <input type="hidden" name="process" value="{{ $process_id }}">
                <div class="p-0">
                  <table id="add-machine-tbl-{{ $process_id }}" class="table table-hover" style="background-color: #F7F9F9 !important;">
                    <col style="width: 90%">
                    <col style="width: 10%">
                    <thead style="font-size: 11px;">
                      <th class="font-weight-bolder">Assigned Machine(s) for {{ $process }}</th>
                      <th>
                        <button class="btn btn-secondary add-row" data-tbl="#add-machine-tbl-{{ $process_id }}" style="font-size: 9pt;"><i class="now-ui-icons ui-1_simple-add"></i> Add Row</button>
                      </th>
                    </thead>
                    @foreach ($details as $item)
                      @if ($loop->first)
                        <tr>
                          <td colspan="2">
                            <select name="process_machines[]" class="form-control rounded" style="width: 100% !important;" required>
                              @foreach ($machines as $machine)
                                  <option value="{{ $machine->machine_id }}" {{ $item->machine_id == $machine->machine_id ? 'selected' : null }}>{{ $machine->machine_code.' - '.$machine->machine_name }}</option>
                              @endforeach
                            </select>
                          </td>
                        </tr>
                      @else
                        <tr>
                          <td>
                            <select name="process_machines[]" class="form-control rounded" style="width: 100% !important;" required>
                              @foreach ($machines as $machine)
                                  <option value="{{ $machine->machine_id }}" {{ $item->machine_id == $machine->machine_id ? 'selected' : null }}>{{ $machine->machine_code.' - '.$machine->machine_name }}</option>
                              @endforeach
                            </select>
                          </td>
                          <td class="text-center">
                            <button class="btn btn-sm btn-danger remove-row">&times;</button>
                          </td>
                        </tr>
                      @endif
                    @endforeach
                  </table>
                </div>
                <div class="d-flex p-0 flex-row-reverse">
                  <button type="submit" class="btn btn-primary m-2" id="add-process-btn">Save changes</button>
                </div>
              </form>
            </div>
          </td>
        </tr>
      </tbody>
    @empty
      <tbody>
        <tr>
          <td colspan="5">
            <h5 class="text-uppercase text-muted text-center">No Process Found.</h5>
          </td>
        </tr>
      </tbody>
    @endforelse
  </table>
</div>