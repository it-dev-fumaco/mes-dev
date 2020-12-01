<!-- Modal -->
<div class="modal fade" id="transfer-qty-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 27%;">
      <form action="/transfer_qty" method="POST" id="transfer-qty-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Re-assign Qty [{{ $workstation }}]</h5>
            </div>
            <input type="hidden" name="id" id="transfer-qty-tsid">
            <input type="hidden" name="operator_name" value="@if(Auth::user()){{ Auth::user()->employee_name }}@endif">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-8 offset-md-2">
                     <div class="form-group">
                        <label for="qty-accepted">Transfer Qty</label>
                        <input type="text" class="form-control" id="transfer-qty" name="transfer_qty" readonly>
                        <small class="form-text text-muted">Maximum: <span id="max-transfer-qty">0</span></small>
                     </div>
                  </div>
                  <div class="col-md-12">
                     <div id="transfer-qty-numpad" class="text-center">
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '1';">1</span>
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '2';">2</span>
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '3';">3</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '4';">4</span>
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '5';">5</span>
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '6';">6</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '7';">7</span>
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '8';">8</span>
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '9';">9</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value.slice(0, -1);"><</span>
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value=document.getElementById('transfer-qty').value + '0';">0</span>
                           <span class="numpad" onclick="document.getElementById('transfer-qty').value='';">Clear</span>
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
