<!-- Modal -->
<div class="modal fade" id="edit-shift-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document" style="min-width: 40%;">
      <form action="/edit_shift" method="POST" id="edit-shift-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title "> Edit Shift<br>
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
               </button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="shift_id" id="shift_id_edit" class="shift_id">
               <div class="form-row">
                   <div class="form-group col-md-6">
                     <label for="time_in">Time in</label>
                     <input type="text" class="form-control time time_in" autocomplete="off" name="time_in" id="time_in_edit" placeholder="Time in" required>
                   </div>
                   <div class="form-group col-md-6">
                     <label for="time_out">Time out</label>
                     <input type="text" class="form-control time time_out" autocomplete="off" name="time_out" id="time_out_edit" placeholder="Time out" required>
                   </div>
            
                 <div class="form-group col-md-6">
                   <label for="shift_type">Shift type</label>
                     <select id="shift_type_edit" name="shift_type" class="form-control shift_type" required>
                       <option value="Regular Shift">Regular Shift</option>
                       <option value="Overtime Shift">Overtime Shift</option>
                       <option value="Special Shift">Special Shift</option>
                     </select>
                 </div>
                 <input type="hidden" name="old_shift_type" class="shift_type">
                 <input type="hidden" name="old_operation_id" class="old_operation_id">
                 {{--<div class="form-group col-md-6">
                     <label for="qty_capacity"> QTY Capacity</label>
                     <input type="text" class="form-control qty_capacity" id="qty_capacity_edit" name="qty_capacity" placeholder="QTY Capacity" required>
                   </div>--}}
                   <div class="form-group col-md-6">
                     <label for="qty_capacity">Operation</label>
                     <select class="form-control operation" name="operation" id="operation" required>
                           @foreach($operation_list as $row)
                           <option value="{{ $row->operation_id }}" style="font-size: 12pt;">{{ $row->operation_name }}</option>
                           @endforeach
                      </select>
                  </div>
                 <div class="form-group col-md-12">
                     <label for="remarks"> Remarks</label>
                     <textarea name="remarks" id="remarks_edit" cols="30" rows="10" class="form-control remarks"></textarea>
                     {{--<input type="text" class="form-control remarks" id="remarks_edit" name="remarks" placeholder="Remarks"> --}}
                   </div>
                   
                   
              </div>
              <hr>
               <div class="form-row">
                  <div class="form-group col-md-6">
                     <h5 class="pull-left"><b>Break Time</b></h5>
                  </div>  
                  <div class="form-group col-md-6">
                     <button type="button" class="btn btn-primary pull-right" id="edit-break-button" style="margin: 5px;">
                        <i class="now-ui-icons ui-1_simple-add"></i> Add
                     </button>
                  </div>
                  <div class="col-md-12">
                  <div id="old_ids"></div>
                  <table class="table" id="editbreak-table" style="font-size: 10px;">
                    <col style="width: 5%;">
                    <col style="width: 30%;">
                    <col style="width: 30%;">
                    <col style="width: 30%;">
                    <col style="width: 5%;">
                     <thead>
                        <tr>
                           <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                           <th style="width: 30%; text-align: center;font-weight: bold;">Category:</th>
                           <th style="width: 30%; text-align: center;font-weight: bold;">Time From</th>
                           <th style="width: 30%; text-align: center;font-weight: bold;">Time To</th>
                           <th style="width: 5%; text-align: center;font-weight: bold;"></th>
                        </tr>
                     </thead>
                     <tbody class="table-body text-center">
                        <tr>
                           
                        </tr>
                     </tbody>
                  </table>
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
