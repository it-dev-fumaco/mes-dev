<!-- Modal -->
<div class="modal fade" id="edit-shift-schedule-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/edit_shift_schedule" method="POST" id="edit-shift-schedule-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title "> Edit Shift Schedule<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="form-row">
                  <input type="hidden" class="shift_sched_id" name="shift_sched_id">
                  <div class="form-group col-md-6">
                    <label for="sched_date">Date</label>
                    <input type="text" autocomplete="off" id="sched_date" name="sched_date" class="form-control date sched_date" placeholder="Date" required>
                  </div>
                  <div class="form-group col-md-6">
                   <label for="shift_id">Shift</label>
                     <select id="shift_id" name="shift_id" class="form-control shift_id" required>
                        @foreach($shift_list as $row)
                           <option value="{{ $row->shift_id }}" style="font-size: 12pt;"><b>{{ $row->shift_type }}</b>- <i>{{ $row->operation_name }}</i></option>
                        @endforeach
                     </select>
                  </div>
                 
                  <div class="form-group col-md-12">
                     <label for="remarks"> Remarks</label>
                     <input type="text" class="form-control remarks" autocomplete="off" id="remarks" name="remarks" placeholder="Remarks" required> 
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
