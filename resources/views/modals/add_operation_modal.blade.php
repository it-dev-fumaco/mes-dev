<!-- Modal -->
<div class="modal fade" id="add-operation-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/add_operation" method="POST" id="add-operation-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title "> Add Operation<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="form-row">
                   <div class="form-group col-md-12">
                     <label for="operation_name">Operation</label>
                     <input type="text" class="form-control" autocomplete="off" name="operation_name" id="operation_name" placeholder="Operation" required>
                   </div>
                   <div class="form-group col-md-12">
                     <label for="operation_desc">Description</label>
                     <input type="text" class="form-control" autocomplete="off" name="operation_desc" id="operation_desc" placeholder="Description" required>
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
