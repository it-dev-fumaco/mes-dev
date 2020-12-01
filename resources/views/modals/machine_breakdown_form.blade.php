<!-- Modal -->
<div class="modal fade" id="report-machine-breakdown-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <form action="/machine_breakdown_save" method="POST" id="machine-breakdown-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #6a1b9a;">
               <h5 class="modal-title" id="modal-title">
                  <i class="now-ui-icons ui-2_settings-90"></i>Maintenance Request<br>
                  <small>{{ $machine_details->machine_name }} [{{ $machine_details->machine_code }}]</small>
               </h5>
               <img src="{{ asset('img/warning.png')}}" width="60">
            </div>
            <input type="hidden" name="id" value="{{ $machine_details->machine_code }}">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label>Date Reported</label>
                        <input type="text" class="form-control form-control-lg" value="{{ Carbon\Carbon::parse(now())->format('Y-m-d h:i:s A') }}" readonly>
                     </div>
                     <div class="form-group">
                        <label>Reported By</label>
                        <input type="text" class="form-control form-control-lg" name="reported_by" value="@if(Auth::user()){{ Auth::user()->employee_name }}@endif" readonly>
                     </div>
                     
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="category">Breakdown Type</label>
                        <select class="form-control" name="category" id="category" onchange="loadbreakdown_validation()" required>
                           <option value="" style="font-size: 12pt;" selected="selected">Select Category</option>
                           <option value="Breakdown" style="font-size: 12pt;">Breakdown</option>
                           <option value="Corrective" style="font-size: 12pt;">Corrective</option>
                        </select>
                     </div>
                     <div class="form-group" style="display:none;" id="breakdown_reason_div">
                        <label for="breakdown_reason">Breakdown Reason</label>
                        <select class="form-control" name="breakdown_reason" id="breakdown_reason" readonly>
                           <option value="Malfunction" style="font-size: 12pt;" selected="">Malfunction</option>
                        </select>
                     </div>

                     <div class="form-group" style="display:none;" id="corrective_reason_div">
                        <label for="corrective_reason">Corrective Reason</label>
                        <select class="form-control" name="corrective_reason" id="corrective_reason">
                           <option value="" style="font-size: 12pt;">Select corrective Reason</option>
                           <option value="Mechanical Issue" style="font-size: 12pt;">Mechanical Issue</option>
                           <option value="Electrical Issue" style="font-size: 12pt;">Electrical Issue</option>
                        </select>
                     </div>
                     
                     <div class="form-group">   
                        <label for="remarks">Remarks</label>
                        <textarea style="width: 100%; resize: none; border-color: #ccc;" rows="3" name="remarks" id="remarks"></textarea>
                     </div>
                  </div>
                  <div class="col-md-12" id="warning_div" style="display:none;">
                     <p class="warning-text">WARNING: PRODUCTION WILL BE INTERRUPTED.</p>
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

<style type="text/css">
   .warning-text {color: red;
  animation: blinker 1s linear infinite;
}

@keyframes blinker {  
  50% { opacity: 0; }
}
</style>