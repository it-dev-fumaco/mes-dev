{{-- <!-- Modal -->
<div class="modal fade" id="transfer-search-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <form id="transfer-search-frm" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">Transfer Task to Machine</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <h6 class="text-center">Production Order No</h6>
                       <div class="row">
                         <div class="col-md-10 offset-md-1">
                           <div class="form-group">
                             <input type="text" class="form-control" name="transfer_no" id="transfer-jtno-scan" style="text-align: center; font-size: 15pt;" disabled>
                           </div>
                         </div>
                       </div>
                     <div class="row" style="margin-top: 30px;">
                       <div class="col-md-10 offset-md-1">
                         <h6 class="text-center">Scan Production Order for Machine Transfer</h6>
                         <img src="{{ asset('img/tap.gif') }}" style="margin-top: -20px;" />
                       </div>
                     </div>
                   </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="transfer-no-search">Enter Production Order No.</label>
                        <div class="input-group">
                           <div class="input-group-prepend">
                              <div class="input-group-text">PROM-</div>
                           </div>
                           <input type="text" class="form-control" id="transfer-no-search" style="font-size: 15pt;" required>
                        </div>
                     </div>
                     <div id="transfer-no-numpad" class="text-center numpad-div">
                        <div class="row1">
                           <span class="numpad prod-search-numpad" data-val="1" data-inputid="transfer-no-search">1</span>
                           <span class="numpad prod-search-numpad" data-val="2" data-inputid="transfer-no-search">2</span>
                           <span class="numpad prod-search-numpad" data-val="3" data-inputid="transfer-no-search">3</span>
                        </div>
                        <div class="row1">
                           <span class="numpad prod-search-numpad" data-val="4" data-inputid="transfer-no-search">4</span>
                           <span class="numpad prod-search-numpad" data-val="5" data-inputid="transfer-no-search">5</span>
                           <span class="numpad prod-search-numpad" data-val="6" data-inputid="transfer-no-search">6</span>
                        </div>
                        <div class="row1">
                           <span class="numpad prod-search-numpad" data-val="7" data-inputid="transfer-no-search">7</span>
                           <span class="numpad prod-search-numpad" data-val="8" data-inputid="transfer-no-search">8</span>
                           <span class="numpad prod-search-numpad" data-val="9" data-inputid="transfer-no-search">9</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('transfer-no-search').value=document.getElementById('transfer-no-search').value.slice(0, -1);"><</span>
                           <span class="numpad prod-search-numpad" data-val="0" data-inputid="transfer-no-search">0</span>
                           <span class="numpad" onclick="document.getElementById('transfer-no-search').value='';">Clear</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-8 offset-md-2">
                     <button type="submit" class="btn btn-block btn-primary btn-lg">SEARCH</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>
<script type="text/javascript">
  function showNotification(color, message, icon){
        $.notify({
          icon: icon,
          message: message
        },{
          type: color,
          timer: 300,
          placement: {
            from: 'top',
            align: 'center'
          }
        });
      }
</script> --}}