<!-- Modal -->
<div class="modal fade" id="add-process-workstation-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_process_workstation" method="POST" id="add-process-worktation-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Add Process<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <input type="hidden" name="workstation" value="{{ $list->workstation_name }}">
                  <input type="hidden" name="workstation_id" value="{{ $list->workstation_id }}">

                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Process:</label>
                        <select class="form-control" name="process_id" id="process_id">
                           @foreach($process_list as $row)
                              <option value="{{ $row->process_id }}">{{ $row->process_name }}</option>
                           @endforeach
                        </select>
                     </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
