{{-- <!-- Modal -->
<div class="modal fade" id="remove-task-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <form action="/transfer_qty" method="POST" id="remove-task-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Remove Task [{{ $workstation }}]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <input type="hidden" name="id" id="remove-qty-tsid">
            <input type="hidden" name="operator_name" value="@if(Auth::user()){{ Auth::user()->employee_name }}@endif">
            <div class="modal-body">
               <input type="hidden" class="form-control" id="remove-qty" name="transfer_qty">
               <div class="form-group">
                  <label for="qty-accepted">Item to Manufacture</label>
                  <p id="production-item-remove"></p>
               </div>
               <div class="form-group">
                  <p>Qty: <span id="remove-qty-txt" style="font-size: 12pt; font-weight: bold;">0</span></p>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
                  </div>
                  <div class="col-md-6">
                     <button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div> --}}