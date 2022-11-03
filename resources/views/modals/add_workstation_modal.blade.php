<!-- Modal -->
<div class="modal fade" id="add-workstation-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_workstation" method="POST" id="add-worktation-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons ui-1_simple-add"></i> Add Workstation<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                     
                     <div class="form-group">
                        <label for="operation">Operation:</label>
                        <select class="form-control" name="operation" id="operation" required>
                           @foreach($operation_list as $row)
                           <option value="{{ $row->operation_id }}" style="font-size: 12pt;">{{ $row->operation_name }}</option>
                           @endforeach
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Workstation Name:</label>
                        <input type="text" class="form-control form-control-lg" name="workstation_name" required>
                     </div>
                     <div class="form-group">
                        <label>Order No:</label>
                        <input type="text" class="form-control form-control-lg" name="order_no" required>
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
