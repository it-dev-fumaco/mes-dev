{{-- <!-- Modal -->
<div class="modal fade" id="start-task-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <form action="/start_task" method="POST" id="start-task-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Start Task [{{$workstation}}]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <input type="hidden" name="id" id="tsdname-start">
            <input type="hidden" name="status" value="In Progress">
            <input type="hidden" name="operator_name" value="@if(Auth::user()){{ Auth::user()->employee_name }}@endif">
            <input type="hidden" name="operator_id" value="@if(Auth::user()){{ Auth::user()->user_id }}@endif">
            <div class="modal-body">
               <p>Please review the details below before tapping <i>'Confirm'</i>.</p>
               <div class="form-group">
                  <label for="qty-accepted">Item to Manufacture</label>
                  <p id="production-item"></p>
               </div>
               <div class="form-group">
                  <p>Accepted Qty: <span id="start-accepted-qty" style="font-size: 12pt; font-weight: bold;">0</span></p>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
                  </div>
                  <div class="col-md-6">
                     <button type="submit" class="btn btn-primary btn-block btn-lg">Confirm</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div> --}}