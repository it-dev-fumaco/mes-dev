{{-- <!-- Modal -->
<div class="modal fade" id="accept-task-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="min-width: 50%;">
      <form action="/accept_task" method="POST" id="accept-task-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title">Accept Task [{{$workstation}}]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <input type="hidden" name="id" id="tsdname">
            <input type="hidden" name="status" id="status">
            <input type="hidden" name="operator_id" value="@if(Auth::user()){{ Auth::user()->user_id }}@endif">
            <input type="hidden" name="machine" value="{{ $machine_details->machine_code }}">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <div class="row">
                        <div class="col-md-12">
                           <div class="form-group">
                              <label for="qty-accepted">Accepted Quantity</label>
                              <input type="text" class="form-control" id="qty-accepted" name="qty_accepted" autofocus readonly>
                              <small class="form-text text-muted">Maximum: <span id="accept-max-qty">0</span></small>
                           </div>
                           <div class="form-group">
                              <label for="operator-name">Operator Name</label>
                              <input type="text" class="form-control" id="operator-name" name="operator_name" value="@if(Auth::user()){{ Auth::user()->employee_name }}@endif" readonly>
                           </div>
                           <div class="form-group">
                              <label for="machine">Machine</label>
                              <input type="text" class="form-control" id="machine" name="machine_name" value="{{ $machine_details->machine_name }}" readonly>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div id="qty-accepted-numpad" class="text-center">
                        <div class="row">
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '1';">1</span>
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '2';">2</span>
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '3';">3</span>
                        </div>
                        <div class="row">
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '4';">4</span>
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '5';">5</span>
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '6';">6</span>
                        </div>
                        <div class="row">
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '7';">7</span>
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '8';">8</span>
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '9';">9</span>
                        </div>
                        <div class="row">
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value.slice(0, -1);"><</span>
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value=document.getElementById('qty-accepted').value + '0';">0</span>
                           <span class="numpad" onclick="document.getElementById('qty-accepted').value='';">Clear</span>
                        </div>
                     </div>
                  </div>
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