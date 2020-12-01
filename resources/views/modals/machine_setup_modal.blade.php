<!-- Modal -->
<div class="modal fade" id="machine-setup-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 50%;">
      <form id="partial-task-frm" action="/end_task" method="post" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Partial Set-up</h5>
            </div>
            <input type="hidden" name="id" id="tsdname-id-modal">
            <input type="hidden" name="qty_done" id="qty-good-modal">
            <input type="hidden" name="good" id="good-modal">
            <input type="hidden" name="rework" id="rework-modal">
            <input type="hidden" name="reject" id="reject-modal">
            <input type="hidden" name="operator_name" id="operator_name-modal">
            <div class="modal-body">
               <div class="form-group" style="font-size: 15pt;">
                <label for="machine_option">Select Set-up</label>
                <select class="form-control" id="machine_option" name="machine_option" style="font-size: 15pt; font-weight: bold;">
                     <option value="Quality Check">Quality Check</option>
                     <option value="Partial">Partial</option>
                </select>
              </div>
            </div>
              
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary btn-lg">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
