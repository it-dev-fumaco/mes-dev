<!-- Modal -->
<div class="modal fade" id="edit-process-setup-list-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/update_process_setup_list" method="POST" id="edit-process-setup-list-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Edit Process<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <input type="hidden" name="edit_process_id" id="edit_process_id">
                     <div class="form-group">
                        <label>Process:</label>
                        <input type="text" class="form-control form-control-lg" name="edit_process_name" id="edit_process_name" required>
                     </div>
                     <div class="form-group">
                        <label>Color Legend:</label>
                        <input type="color" class="form-control form-control-lg" name="color_legend" id="edit_color_legend" style="height: 40px;">
                     </div>
                     <div class="form-group">
                        <label>Remarks:</label>
                        <input type="text" class="form-control form-control-lg" name="edit_remarks" id="edit_remarks">
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
