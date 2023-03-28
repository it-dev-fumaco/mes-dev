<!-- Modal -->
<div class="modal fade" id="enter-reject-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document" style="min-width: 98%;">
      <form id="reject-task-frm" action="/reject_task_spotwelding" method="post" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #C62828;">
               <h5 class="modal-title"><i class="now-ui-icons ui-1_simple-remove" style="font-size: 15pt;"></i> Enter Reject [<b>{{$workstation}}</b>]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="d-none">
               <input type="text" name="per_row_reject" class="per-row-reject">
               <input type="text" name="good" class="good-qty-input">
               <input type="text" name="id" class="timelog-id">
               <input type="text" name="production_order" class="production-order-input">
               <input type="text" name="workstation" class="workstation-input">
            </div>
            
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-8">
                     <div class="row">
                        <div class="col-md-12 text-center">
                           <div style="font-size: 14pt; margin-bottom: 8px;" class="text-center">
                              <span class="production-order" style="font-weight: bold;display:none;"></span>
                           </div>
                           
                           <div class="text-center">
                              <span style="display: block; font-size: 11pt;">PROCESS</span>
                              <span style="display: block; font-weight: bold; font-size: 14pt;" class="process-name"></span>
                           </div>
                           <div class="spotwelding_reject_list"></div>
                           {{--  <div class="form-group">
                              <label for="good-qty">Good Qty</label>
                              <input type="text" class="form-control" id="good-qty" name="good" value="0" readonly style="font-size: 14pt;">
                              <small class="form-text text-muted"  style="font-size: 14pt;"><b>Maximum: <span id="end-good-max-qty">0</span></b></small>
                           </div>  --}}
                           {{--  <div class="form-group">
                              <label for="reject-qty">Reject Qty</label>
                              <input type="text" class="form-control" id="reject-qty" name="reject" placeholder="Enter Reject" value="0" readonly style="font-size: 14pt;">
                           </div>  --}}
                           {{--  <div class="form-group" id="rejection-type-div" hidden>
                              <label for="reject-qty">Rejection Type</label>
                              <select class="form-control" name="rejection_type" id="rejection-type">
                                 <option value="">Select Rejection Type</option>
                                 <option value="Wrong bending">Wrong bending</option>
                                 <option value="Forming reject">Forming reject</option>
                                 <option value="Wrong punch">Wrong punch</option>
                                 <option value="Wrong cut-size">Wrong cut-size</option>
                                 <option value="Wrong guide">Wrong guide</option>
                                 <option value="Wrong program">Wrong program</option>
                                 <option value="Wrong thickness">Wrong thickness</option>
                                 <option value="Double punch">Double punch</option>
                                 <option value="Deform">Deform</option>
                                 <option value="Dent">Dent</option>
                                 <option value="Stain">Stain</option>
                                 <option value="Broken">Broken</option>
                                 <option value="Program error">Program error</option>
                                 <option value="Machine Set-Up Error">Machine Set-Up Error</option>
                              </select>
                           </div>  --}}
                           {{--  <div class="form-group">
                              <label for="rework-qty">Rework Qty</label>
                              <input type="text" class="form-control" id="rework-qty" name="rework" placeholder="Enter Rework" value="0" readonly style="font-size: 14pt;">
                           </div>  --}}
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
                        <div class="col-md-12" style="height: auto;right:5% ;left:5%">
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