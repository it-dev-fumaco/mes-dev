<!-- Modal -->
<div class="modal fade" id="delete-shift-sched-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <form action="/delete_shift_schedule" method="POST" id="delete-shift-sched-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title ">
                  Delete Shift Schedule
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <input type="hidden" name="shift_sched_id" id="delete_shift_sched_id" class="delete_shift_sched_id">
                        <div class="row" style="margin-top: -3%;">
                           <div class="col-sm-12">
                              <span style="font-size: 10pt;">Delete shift schedule permanently?</span>
                           </div>               
                        </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding-top: -100px;">Cancel</button>
               &nbsp;
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
