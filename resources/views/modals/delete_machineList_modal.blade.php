<!-- Modal -->
<div class="modal fade" id="delete-machinelist-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <form action="/delete_machine" method="POST" id="delete-machine-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons ui-1_simple-remove"></i> Delete Machine<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <input type="hidden" name="machine_id" id="delete-machine-id">
                        <input type="hidden" name="machine_code" id="delete-machine-code">
                        <div class="row" style="margin-top: -3%;">
                           <div class="col-sm-12">
                              <span style="font-size: 10pt;">Delete Machine Code - <b><span id="machine_code_label"></span></b> ?</span>
                           </div>               
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
