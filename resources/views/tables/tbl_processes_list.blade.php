<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
    <col style="width: 5%;">
    <col style="width: 46%;">
    <col style="width: 30%;">
    <col style="width: 19%;">
    <thead class="text-primary text-uppercase font-weight-bold">
      <th class="p-2 text-center"><b>No</b></th>
      <th class="p-2 text-center"><b>Process</b></th>
      <th class="p-2 text-center"><b>Last Modified By</b></th>
      <th class="p-2 text-center"><b>Action(s)</b></th>
    </thead>
    <tbody style="font-size: 13px;">
      @forelse($processes as $index => $process)
      <tr>
        <td class="p-2 text-center">{{ $index + 1 }}</td>
        <td class="p-2 text-left">
          {{ $process->process_name }} <br>
          {{-- <small class="text-muted">Last modified by: {{ $row->last_modified_by }}</small> --}}
        </td>
        <td class="p-2 text-center">
          {{ $process->last_modified_by }}
        </td>
        <td class="p-2 text-center">
          <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default btn-edit-process-modal hover-icon' data-toggle="modal" data-target="#edit-process-{{ $process->process_id }}-modal"><i class='now-ui-icons design-2_ruler-pencil'></i></button>
          <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default btn-delete-process hover-icon' data-toggle="modal" data-target="#delete-process-{{ $process->process_id }}-modal"><i class='now-ui-icons ui-1_simple-remove'></i></button>

          <div class="modal fade" id="edit-process-{{ $process->process_id }}-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <form action="/process/edit/{{ $process->process_id }}" class="edit-process-form" method="post">
                  <div class="modal-header" style="background-color: #0277BD; color: #fff;">
                    <h5 class="modal-title" id="exampleModalLabel">Edit {{ $process->process_name }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                      @csrf
                      <input type="text" class="form-control" name="process_name" value="{{ $process->process_name }}" placeholder="Process Name" required>
                      <br>
                      <textarea name="remarks" rows="3" class="form-control" placeholder="Remarks..."></textarea>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary ml-1">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="modal fade" id="delete-process-{{ $process->process_id }}-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header" style="background-color: #0277BD; color: #fff;">
                  <h5 class="modal-title" id="exampleModalLabel">Delete {{ $process->process_name }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                    Remove {{ $process->process_name }}?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-danger ml-1 delete-process" data-id="{{ $process->process_id }}">Delete</button>
                </div>
              </div>
            </div>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="4" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  <div id="process_list_pagination" class="col-md-12 text-center">{{ $processes->links() }}</div>