<!-- Modal -->
<div class="modal fade" id="enter-reject-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document" style="min-width: 98%;">
      <form id="reject-task-frm" action="/reject_painting" method="post" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #C62828;">
               <h5 class="modal-title"><i class="now-ui-icons ui-1_simple-remove" style="font-size: 15pt;"></i> Enter Reject [<b>Painting</b>]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <input type="hidden" name="id" class="timelog-id">
            <input type="hidden" name="process_id" class="process-id-input">
            <input type="hidden" name="production_order" class="production-order-input">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-8">
                     <div class="row">
                        <div class="col-md-12 text-center">
                           <div style="font-size: 14pt; margin-bottom: 8px;" class="text-center">
                              <span class="production-order" style="font-weight: bold;"></span>
                           </div>
                           
                           <div class="text-center">
                              <span style="font-size: 11pt;">PROCESS:</span>
                              <span style="font-weight: bold; font-size: 14pt;" class="process-name"></span>
                           </div>
                           <div class="op_reject_checklist"></div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="text-center">
                        <div class="form-group">
                           <label for="rejected-qty" style="font-size: 14pt;">Enter Rejected Qty</label>
                           <input type="text" class="form-control form-control-lg" name="rejected_qty" id="rejected-qty"  value="0" readonly style="font-size: 20pt; text-align: center;">
                           <small class="form-text text-muted" style="font-size: 14pt;"><b>Maximum: <span class="max-qty">0</span></b></small>
                        </div>
                        <div class="col-12" style="height: auto;right:5% ;left:5%">
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