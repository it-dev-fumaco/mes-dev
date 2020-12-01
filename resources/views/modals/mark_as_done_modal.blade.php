<!-- Modal -->
<div class="modal fade" id="mark-done-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/mark_as_done_task" method="POST" id="mark-done-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title">
                <span>Mark as Done</span>
                <span class="workstation-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <label for="machine_selection"><b>Machine</b></label>
                     <select style="font-size: 13pt;" name="machine_selected_id" class="form-control" id="machine_selection">
                           <option value="">
                           </option>
                     </select>
                     <br>
                    <h5 class="text-center">Override duration and cycle time?</h5>
                    <input type="hidden" name="id" required id="jt-index">
                    <input type="hidden" name="qty_accepted" required id="qty-accepted-override">
                    
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
         </div>
      </form>
   </div>
</div>