<!-- Modal -->
<div class="modal fade" id="rework-qty-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <form action="/rework_qty" method="POST" id="rework-qty-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title">Rework Qty [{{ $workstation }}]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <input type="hidden" name="id" id="rework-qty-tsid">
            <input type="hidden" name="operator_name" value="@if(Auth::user()){{ Auth::user()->employee_name }}@endif">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-8 offset-md-2">
                     <div class="form-group">
                        <label for="qty-accepted">Rework Qty</label>
                        <input type="text" class="form-control" id="rework-qty-input" name="rework_qty" readonly>
                        <small class="form-text text-muted">Maximum: <span id="max-rework-qty">0</span></small>
                     </div>
                  </div>
                  <div class="col-md-10 offset-md-1">
                     <div id="rework-qty-numpad" class="text-center">
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '1';">1</span>
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '2';">2</span>
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '3';">3</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '4';">4</span>
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '5';">5</span>
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '6';">6</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '7';">7</span>
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '8';">8</span>
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '9';">9</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value.slice(0, -1);"><</span>
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value=document.getElementById('rework-qty-input').value + '0';">0</span>
                           <span class="numpad" onclick="document.getElementById('rework-qty-input').value='';">Clear</span>
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
</div>
