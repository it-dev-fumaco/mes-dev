<!-- Modal -->
<div class="modal fade" id="delete-workstation-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <form action="/delete_workstation" method="POST" id="delete-workstation-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title ">
                  Delete Workstation
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <input type="hidden" name="workstation_id" id="delete-workstation-id">
                        <input type="hidden" name="workstation_name"  id="delete-workstation-name">

                        <div class="row" style="margin-top: -3%;">
                           <div class="col-sm-12">
                              <span style="font-size: 10pt;">Delete <b><span id="workstation_name_label"></b> under <span id="operation_label"></span></span> permanently ?</span>
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
