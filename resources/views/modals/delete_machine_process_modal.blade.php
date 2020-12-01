<!-- Modal -->
<div class="modal fade" id="delete-machine-process-{{ $rows->id }}-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-sm" role="document">
      <form action="/delete_machine_process" method="POST" id="delete-machine-process-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                  Delete Machine Process
               </h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12 text-center">
                        <input type="hidden" name="process_id" value="{{ $rows->id }}">
                        <div class="row" style="margin-top: -3%;">
                           <div class="col-sm-12">
                              <span style="font-size: 10pt; text-align: center;">Delete <b>{{ $rows->process }}</b> from the <br>list?</span>
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
