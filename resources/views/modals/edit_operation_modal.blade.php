<!-- Modal -->
<div class="modal fade" id="edit-operation-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/edit_operation" method="POST" id="edit-operation-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title "> Edit Operation<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="form-row">
                   <div class="form-group col-md-12">
                     <label for="operation_name">Operation</label>
                     <input type="text" class="form-control operation_name" autocomplete="off" name="operation_name" id="operation_name_edit" placeholder="Operation" required>
                   </div>
                   <div class="form-group col-md-12">
                     <label for="operation_desc">Description</label>
                     <input type="text" class="form-control operation_desc" autocomplete="off" name="operation_desc" id="operation_desc_edit" placeholder="Description" required>
                   </div>
                   <input type="hidden" name="operation_id" class="operation_id">
                   <input type="hidden" name="old_operation" class="old_operation">
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
