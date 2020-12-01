<!-- Modal -->
<div class="modal fade" id="add-shift-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document" style="min-width: 40%;">
      <form action="/add_shift" method="POST" id="add-shift-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title "> Add Shift<br>
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="form-row">
                   <div class="form-group col-md-6">
                     <label for="time_in">Time in</label>
                     <input type="text" class="form-control time" autocomplete="off" name="time_in" id="time_in" placeholder="Time in" required>
                   </div>
                   <div class="form-group col-md-6">
                     <label for="time_out">Time out</label>
                     <input type="text" class="form-control time" autocomplete="off" name="time_out" id="time_out" placeholder="Time out" required>
                   </div>
                 <div class="form-group col-md-6">
                   <label for="shift_type">Shift type</label>
                     <select id="shift_type" name="shift_type" class="form-control" required>
                       <option value="Regular Shift">Regular Shift</option>
                       <option value="Overtime Shift">Overtime Shift</option>
                       <option value="Special Shift">Special Shift</option>
                     </select>
                 </div>
                 <div class="form-group col-md-6">
                     <label for="qty_capacity"> QTY Capacity</label>
                     <input type="text" class="form-control" id="qty_capacity" name="qty_capacity" placeholder="QTY Capacity" required>
                  </div>
                  <div class="form-group col-md-6">
                     <label for="qty_capacity">Operation</label>
                     <select class="form-control" name="operation" id="operation" required>
                           @foreach($operation_list as $row)
                           <option value="{{ $row->operation_id }}" style="font-size: 12pt;">{{ $row->operation_name }}</option>
                           @endforeach
                      </select>
                  </div>
                 <div class="form-group col-md-6">
                     <label for="remarks"> Remarks</label>
                     <input type="text" class="form-control" id="remarks" name="remarks" placeholder="Remarks"> 
                   </div>
                   
                   
               </div>
               <hr>
               <div class="form-row">
                  <div class="form-group col-md-6">
                     <h5 class="pull-left"><b>Break Time</b></h5>
                  </div>  
                  <div class="form-group col-md-6">
                     <button type="button" class="btn btn-primary pull-right" id="add-break-button" style="margin: 5px;">
                        <i class="now-ui-icons ui-1_simple-add"></i> Add
                     </button>
                  </div>
                  <div class="col-md-12">
                  <table class="table" id="addbreak-table" style="font-size: 10px;">
                    <col style="width: 5%;">
                    <col style="width: 30%;">
                    <col style="width: 30%;">
                    <col style="width: 30%;">
                    <col style="width: 5%;">
                     <thead>
                        <tr>
                           <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                           <th style="width: 30%; text-align: center;font-weight: bold;" id="th_checklist">Category:</th>
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
