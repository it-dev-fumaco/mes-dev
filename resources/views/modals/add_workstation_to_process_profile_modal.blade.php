<!-- Modal -->
<div class="modal fade" id="add-workstation-to-process-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_process_workstation" method="POST" id="add-process-worktation-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Add Workstation<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <input type="hidden" name="process_id" value="{{ $process->id }}">

                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Workstation:</label>
                        <select class="form-control" name="workstation_id" id="workstation_id">
                           @foreach($workstation as $row)
                              <option value="{{ $row->id }}">{{ $row->workstation_name }}</option>
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
