<!-- Modal -->
<div class="modal fade edit-workstation-modal" id="edit-workstation-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/edit_workstation" method="POST" id="edit-workstation-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons design-2_ruler-pencil"></i> Edit Workstation<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                  <input type="hidden" name="workstation_id" id="orig_workstation_id">
                  <input type="hidden" name="workstation_orig" id="orig_workstation_name">
                  <input type="hidden" name="operation_orig" id="orig_operation">
                     <div class="form-group">
                        <label for="operation">Operation:</label>
                        <select class="form-control" name="operation" id="edit_workstation_operation" required>
                           @foreach($operation_list as $rows)
                           <option value="{{ $rows->operation_id }}" style="font-size: 12pt;" >{{ $rows->operation_name }}</option>
                           @endforeach
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Workstation Name:</label>
                        <input type="text" class="form-control form-control-lg" name="workstation_name" id="edit_workstation_name" required>
                     </div>
                     <div class="form-group">
                        <label>Order No:</label>
                        <input type="text" class="form-control form-control-lg" name="order_no" id="edit_order_no" required>
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
