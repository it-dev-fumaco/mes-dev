<!-- Modal -->
<div class="modal fade" id="delete-process-workstation-{{ $rows->id }}-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <form action="/delete_process_workstation" method="POST" id="delete-process-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  Delete Process
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                        <input type="hidden" name="process_id" value="{{ $rows->id }}">
                        <input type="hidden" name="process_name" value="{{ $rows->process_name }}">
                        <input type="hidden" name="workstation" value="{{ $rows->workstation_name }}">
                        <div class="row" style="margin-top: -3%;">
                           <div class="col-sm-12">
                              <span style="font-size: 10pt;">Delete <b>{{ $rows->process_name }}</b> permanently ?</span>
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
