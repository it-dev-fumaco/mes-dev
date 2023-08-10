<div class="table-responsive" style="font-size: 9pt;">
  <table class="table table-striped">
    <thead class="text-primary">
      <th class="text-center"><b>No.</b></th>
      <th class="text-left"><b>Process</b></th>
      <th class="text-center"><b>Last Modified By</b></th>
      <th class="text-center"><b>Actions</b></th>
    </thead>
    <tbody>
      @foreach($assigned_processes as $process => $details)
      @php
        $last_modified_by = collect($details)->pluck('last_modified_by')->first();
        $last_modified_at = collect($details)->pluck('last_modified_at')->first();
        $process_id = collect($details)->pluck('process_id')->first()
      @endphp
      <tr>
        <td class="text-center">{{ $process_id }}</td>
        <td class="text-left">{{ $process }}</td>
        <td class="text-center">{{ $last_modified_by.' - '.Carbon\Carbon::parse($last_modified_at)->format('F d, y h:i A') }}</td>
        <td class="text-center">
          <button class="btn btn-primary btn-sm load-machines" data-toggle="modal" data-target="#machine-list-{{ $process_id }}"><i class="now-ui-icons design_bullet-list-67"></i></button>
          <button class="btn btn-sm" data-toggle="modal" data-target="#remove-process-{{ $process_id }}">&times;</button>

          <div class="modal fade" id="remove-process-{{ $process_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title" id="exampleModalLabel">{{ $process }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p style="font-size: 10pt;">Are you sure you want to remove {{ $process }}?</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-danger remove-assigned-process" data-id="{{ $process_id }}">Remove</button>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="machine-list-{{ $process_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title" id="exampleModalLabel">{{ $process }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form action="/process_assignment/{{ $id }}/add" class="add-process-form" method="post">
                  @csrf
                  <input type="hidden" name="process" value="{{ $process_id }}">
                  <div class="modal-body">
                    <table id="add-machine-tbl-{{ $process_id }}" class="table table-striped w-100">
                      <col style="width: 80%">
                      <col style="width: 20%">
                      <tr>
                        <th>Machine</th>
                        <th>
                          <button class="btn btn-sm btn-primary w-100 add-row" data-tbl="#add-machine-tbl-{{ $process_id }}" style="font-size: 9pt;"><i class="now-ui-icons ui-1_simple-add"></i> Add</button>
                        </th>
                      </tr>
                      @foreach ($details as $item)
                        @if ($loop->first)
                          <tr>
                            <td colspan=2>
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
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="add-process-btn">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<center>
  <div id="processWorkstation_pagination" class="col-md-12 text-center" style="text-align: center;">
    {{-- {{ $assigned_processes->links() }} --}}
  </div>
</center>
<style>
  .modal{
    background-color: rgba(0,0,0, .4)
  }
</style>