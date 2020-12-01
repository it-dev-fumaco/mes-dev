<!-- Modal -->
<div class="modal fade parent-modal" id="process-profile-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <form action="/save_process" method="POST" id="add-process-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                  <i class="now-ui-icons design-2_ruler-pencil"><span class="modal-title"></span></i>
               </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
                 </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <button type="button" class="btn btn-primary add-process-assignment">Process Assignment</button> 
                 </div>
                 <input type="hidden" name="process_id_to_assign" class="process_id_to_assign" id="process_id_to_assign">
                  <div class="col-md-12" id="tbl_assigned_process_div">
                     

                  </div>

               </div>
            </div>
          
         </div>
      </form>
   </div>
</div>
