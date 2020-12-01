<!-- Modal -->
<div class="modal fade" id="change-process-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/update_process_maindashboard" method="POST" id="change-process-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title">
                <span>Change Process</span>
                <span class="workstation-text"></span></h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-8 offset-md-2">
                     <div class="form-group" style="font-size: 16pt;">
                        <label for="qty-accepted">Select Process</label>
                        <input type="hidden" name="id" required id="jt-index">
                        <select id="sel-process" class="form-control" name="process" style="font-size: 16pt;"></select>
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