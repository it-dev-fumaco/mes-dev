<!-- Modal -->
<div class="modal fade" id="add-process-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_process" method="POST" id="add-process-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Add Process<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     <div class="form-group">
                        <label>Process:</label>
                        <input type="text" class="form-control form-control-lg" name="process_name" required>
                     </div>
                     <div class="form-group">
                        <label>Color Legend:</label>
                        <input type="color" class="form-control form-control-lg" name="color_legend" style="height: 40px;">
                     </div>
                     <div class="form-group">
                        <label>Remarks:</label>
                        <input type="text" class="form-control form-control-lg" name="remarks">
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
