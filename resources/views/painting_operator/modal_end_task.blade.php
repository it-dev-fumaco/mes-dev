<!-- Modal -->
<div class="modal fade" id="end-task-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document" style="min-width: 70%;">
      <form id="end-task-frm" action="/end_painting" method="post" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title"><img src="{{ asset('img/task_complete.png') }}" width="30" height="29"> Complete Task [<b>Painting</b>]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <input type="hidden" name="id" class="timelog-id">
            <input type="hidden" name="balance_qty" class="balance-qty">
            <input type="hidden" name="production_order" class="production-order-input">
            <input type="hidden" name="workstation" class="workstation-input">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <div class="row">
                        <div class="col-md-12 text-center">
                           <div style="font-size: 14pt; margin-bottom: 8px;" class="text-center">
                              <span class="production-order" style="font-weight: bold;"></span>
                           </div>
                           <div class="form-group">
                              <label for="completed-qty" style="font-size: 14pt;">Enter Completed Qty</label>
                              <input type="text" class="form-control form-control-lg" name="completed_qty" id="completed-qty"  value="0" readonly style="font-size: 20pt; text-align: center;">
                              <small class="form-text text-muted" style="font-size: 14pt;"><b>Maximum: <span class="max-qty">0</span></b></small>
                           </div>
                           <div class="text-center">
                              <span style="display: block; font-size: 11pt;">PROCESS</span>
                              <span style="display: block; font-weight: bold; font-size: 14pt;" class="process-name"></span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="text-center">
                        <div class="row">
                           <span class="num numpad">1</span>
                           <span class="num numpad">2</span>
                           <span class="num numpad">3</span>
                        </div>
                        <div class="row">
                           <span class="num numpad">4</span>
                           <span class="num numpad">5</span>
                           <span class="num numpad">6</span>
                        </div>
                        <div class="row">
                           <span class="num numpad">7</span>
                           <span class="num numpad">8</span>
                           <span class="num numpad">9</span>
                        </div>
                        <div class="row">
                           <span class="del numpad"><</span>
                           <span class="num numpad">0</span>
                           <span class="clear numpad">Clear</span>
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