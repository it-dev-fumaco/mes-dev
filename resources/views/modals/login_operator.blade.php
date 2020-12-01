<!-- Modal -->
{{-- <div class="modal fade" id="operator-login-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="min-width:  70%;">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title text-center" id="modal-title">{{ $workstation }} [{{ $machine_details->machine_name }}]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="window.history.back();">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <h6 class="text-center">Employee ID</h6>
                     <form action="/login_operator" method="POST" autocomplete="off" id="login-frm">
                        @csrf
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <div class="form-group">
                                 <input type="text" class="form-control form-control-lg" name="operator_id" id="operator-id" style="text-align: center;" readonly>
                              </div>
                           </div>
                        </div>
                     </form>
                     <div class="row" style="margin-top: 30px;">
                        <div class="col-md-10 offset-md-1">
                           <h6 class="text-center">Scan your ID to login</h6>
                           <img src="{{ asset('img/tap.gif') }}" style="margin-top: -20px;" /></div>
                     </div>
                     
                  </div>
                  <div class="col-md-6">
                     <h6 class="text-center">Enter Biometric ID</h6>
                     <form action="/login_operator" method="POST" autocomplete="off" id="manual-login-frm">
                        @csrf
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <div class="form-group">
                                 <input type="text" class="form-control form-control-lg" name="operator_id" id="user-id" style="text-align: center;" readonly>
                              </div>
                           </div>
                        </div>
                        <div class="text-center numpad-div">
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '1';">1</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '2';">2</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '3';">3</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '4';">4</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '5';">5</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '6';">6</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '7';">7</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '8';">8</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '9';">9</span>
                           </div>
                           <div class="row1">
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value.slice(0, -1);"><</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value=document.getElementById('user-id').value + '0';">0</span>
                              <span class="numpad" onclick="document.getElementById('user-id').value='';">Clear</span>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <button type="submit" class="btn btn-block btn-primary btn-lg">LOGIN</button>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div> --}}
            {{-- <div class="modal-footer">
               <button type="button" class="btn btn-secondary" onclick="window.history.back();">Cancel</button>
            </div> --}}
{{--          </div>
   </div>
</div> --}}