<!-- Modal -->
<div class="modal fade" id="delete-process-setup-list-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width:30%;">
      <form action="/delete_process_setup_list" method="POST" id="delete-process-setup-list-modal-frm">
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
                        <input type="hidden" name="delete_process_id" id="delete_process_id">
                        <div class="row" style="margin-top: -3%;font-size:11.2pt;">
                           <div class="col-sm-12">
                           <br>

                           <label style="padding-left: 10px;display:inline;">Delete</label><label style="padding-left: 10px;display:inline;font-weight:bold;" id="delete_process_name_input"></label><label style="padding-left: 10px;display:inline;">permanently?</label>
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
