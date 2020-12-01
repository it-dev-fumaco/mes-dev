<!-- Modal -->
<div class="modal fade" id="delete-assigned" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <form action="/delete_process_workstation" method="POST" id="delete-assigned-machine-workstation-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  Delete
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <input type="hidden" name="process_id">
                        <div class="row" style="margin-top: -3%;">
                           <input type="hidden" name="delete_id" id="delete_id">
                           <div class="col-sm-12">
                              <span style="font-size: 10pt;">Delete <b><span id="delete_workstation"></span> - <span id="delete_machine"></span></b> permanently ?</span>
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
