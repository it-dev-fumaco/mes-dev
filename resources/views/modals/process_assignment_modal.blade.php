<!-- Modal -->
<div class="modal fade" id="process-assignment-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/process_assignment" method="POST" id="add-assign-machine-workstation-to-process-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i>Process Assignment<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <input type="hidden" name="assign_process_id" id="assign_process_id" class="assign_process_id">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Workstation:</label>
                        <select class="form-control" name="workstation_id_assign" id="workstation_id_assign" onchange="get_machine_assign()">
                           @foreach($workstation_list as $row)
                              <option value="{{ $row->workstation_id }}">{{ $row->workstation_name }}</option>
                           @endforeach
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Machine:</label>
                        <select class="form-control" name="machine_assignment" id="machine_assignment">
                        <option value=""></option>
                        
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
