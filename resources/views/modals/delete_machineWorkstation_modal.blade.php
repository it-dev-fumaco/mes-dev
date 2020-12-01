<!-- Modal -->
<div class="modal fade" id="delete-machineworkstation-{{ $row->workstation_machine_id }}-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <form action="/delete_workstation_machine" method="POST" id="delete-machine-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons ui-1_simple-remove"></i> Remove Machine<br>
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <input type="hidden" name="machine_id" value="{{ $row->workstation_machine_id }}">
                        <input type="hidden" name="machine_code" value="{{ $row->machine_code }}">
                        <input type="hidden" name="workstation" value="{{ $row->workstation }}">
                        <div class="row" style="margin-top: -3%;">
                           <div class="col-sm-12">
                              <span style="font-size: 10pt;">Remove <b>{{ $row->machine_code }}</b> as assinged <br>machine</b> ?</span>
                           </div>               
                        </div>
                  </div>

               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               &nbsp;
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>
