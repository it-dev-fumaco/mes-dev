{{--  @if($type == 'Random Inspection')  --}}
<div class="row">
    <div class="col-md-12">
       <!-- Nav tabs -->
       <ul class="nav nav-tabs" role="tablist" style="display: none;">
          <li class="nav-item">
             <a class="nav-link active" id="first-tab-reject-confirmation" data-toggle="tab" href="#tab1-reject-confirmation" role="tab" aria-controls="tab1" aria-selected="true">QC</a>
          </li>
          <li class="nav-item">
             <a class="nav-link" id="second-tab-reject-confirmation" data-toggle="tab" href="#tab2-reject-confirmation" role="tab" aria-controls="tab2" aria-selected="false">Confirm</a>
          </li>
       </ul>
       <!-- Tab panes -->
       <form action="/submit_quality_check" method="POST" id="random-check-frm">
          @csrf
          <div class="tab-content" style="min-height: 500px;">
             <div class="tab-pane active" id="tab1-reject-confirmation" role="tabpanel" aria-labelledby="first-tab">
                <div class="row" style="min-height: 420px;">
                   <div class="col-md-6">
                      <input type="hidden" id="qc-chk-count" value="0">
                      <input type="hidden" name="id" value="{{ $job_ticket_details->job_ticket_id }}">
                      <input type="hidden" id="qc-status" name="qc_inspection_status">
                      <input type="text" name="qc_type" value="Reject Confirmation">
                      Prod. Order: <b><span class="prod-order">{{ $production_order_details->production_order }}</span></b><br>
                      Process: <b>{{ $job_ticket_details->process_name }}</b><br><br>
                      <b>{{ $production_order_details->item_code }}</b><br>{{ $production_order_details->description }}
                      <br>
                      
                      <div class="form">
                         <h6 class="text-center">Checklist</h6>
                         <div class="inputGroup">
                            <input id="option1" name="option1" type="checkbox" class="qc-chk" />
                            <label for="option1">Visual</label>
                         </div>
                         <div class="inputGroup">
                            <input id="option2" name="option2" type="checkbox" class="qc-chk" />
                            <label for="option2">Length </label>
                            {{--  - {{ $data['length']}}  --}}
                         </div>
                         <div class="inputGroup">
                            <input id="option3" name="option3" type="checkbox" class="qc-chk" />
                            <label for="option3">Width</label>
                            {{--  - {{ $data['width']}}  --}}
                         </div>
                         <div class="inputGroup">
                            <input id="option4" name="option4" type="checkbox" class="qc-chk" />
                            <label for="option4">Thickness</label>
                            {{--  - {{ $data['thickness']}}  --}}
                         </div>
                      </div>
                   </div>
                   <div class="col-md-6">
                      <div class="row">
                         <div class="col-md-4">
                            <div class="form-group text-center">
                               <label for="">Total Qty</label>
                               <h3 class="text-center qty-to-manufacture">{{ $production_order_details->qty_to_manufacture }}</h3>
                            </div>
                         </div>
                         <div class="col-md-4">
                            <div class="form-group text-center">
                               <label for="sampling-qty">Qty Checked</label>
                               <input type="text" class="form-control form-control-lg" name="sampling_qty" id="sampling-qty" value="0" autofocus readonly required style="font-size: 20pt; text-align: center; font-weight: bolder;">
                            </div>
                         </div>
                         <div class="col-md-4">
                            <div class="form-group text-center">
                               <label for="reject-qty">Reject Qty</label>
                               <input type="text" class="form-control form-control-lg" name="reject_qty" id="reject-qty" value="{{ $job_ticket_details->reject }}" readonly required style="font-size: 20pt; text-align: center; font-weight: bolder;">
                            </div>
                         </div>
                      </div>
                      <div class="text-center numpad-div">
                         <div class="row1">
                            <span class="numpad num">1</span>
                            <span class="numpad num">2</span>
                            <span class="numpad num">3</span>
                         </div>
                         <div class="row1">
                            <span class="numpad num">4</span>
                            <span class="numpad num">5</span>
                            <span class="numpad num">6</span>
                         </div>
                         <div class="row1">
                            <span class="numpad num">7</span>
                            <span class="numpad num">8</span>
                            <span class="numpad num">9</span>
                         </div>
                         <div class="row1">
                            <span class="numpad del"><</span>
                            <span class="numpad num">0</span>
                            <span class="numpad clear">Clear</span>
                         </div>
                      </div>
                   </div>
                </div>
                <div class="row">
                   <div class="col-md-6">
                      <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
                   </div>
                   <div class="col-md-6">
                      <button type="button" class="btn btn-primary btn-block btn-lg nexttab">Next</button>
                   </div>
                </div>
             </div>
             <div class="tab-pane" id="tab2-reject-confirmation" role="tabpanel" aria-labelledby="second-tab">
                <div class="row" style="min-height: 420px;">
                   <div class="col-md-6">
                      <div class="row">
                         <div class="col-md-12">
                            <div class="text-center">
                               <span class="badge badge-danger" id="qc-badge" style="font-size: 18pt; margin: 15px;">QC Failed</span>
                            </div>
                            <div class="text-center" style="font-size: 14pt;">
                               Prod. Order: <b>{{ $production_order_details->production_order }}</b><br><br>
                               <b>{{ $production_order_details->item_code }}</b>
                            </div>
                         </div>
                         
                         <div class="col-md-6">
                            <div class="text-center" style="font-size: 14pt; margin-top: 5px;">
                               <span>Qty Checked: </span> <span id="qc-qty" style="font-weight: bold; font-size: 14pt;">0</span>
                            </div>
                         </div>
                         <div class="col-md-6">
                            <div class="text-center" style="font-size: 14pt; margin-top: 5px;">
                               <span>Reject Qty: </span> <span id="qc-rej-qty" style="font-weight: bold; font-size: 14pt;">0</span>
                            </div>
                         </div>
                      </div>
                      <br>
                     <div class="form-group" id="sel-rejection-type">
                         <label style="font-size: 14pt;">Rejection Type</label>
                         <select class="form-control form-control-lg" style="font-size: 16pt;" name="rejection_type">
                            <option value="" selected>-Select Rejection Type-</option>
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
                      </div>
                      <div class="form-group" id="sel-dispo">
                         <label style="font-size: 14pt;">Remarks</label>
                         <select class="form-control form-control-lg" style="font-size: 16pt;" name="sel_dispo">
                            <option value="" selected>-Select Remarks-</option>
                            <option value="Scrap">Scrap</option>
                            <option value="Rework">Rework</option>
                         </select>
                      </div>
                   </div>
                   <div class="col-md-6 text-center">
                      <h5 class="text-center">Scan Authorized QC Employee ID</h5>
                      <div class="form-group">
                         <input type="password" class="form-control form-control-lg" name="inspected_by" readonly style="text-align: center; font-size: 30pt; font-weight: bolder;" id="inspected-by">
                      </div>
                      <button type="button" class="toggle-manual-input" style="margin-bottom: 10px;">Tap here for Manual Entry</button>
                      <div class="text-center numpad-div manual" style="display: none;">
                         <div class="row1">
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '1';">1</span>
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '2';">2</span>
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '3';">3</span>
                         </div>
                         <div class="row1">
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '4';">4</span>
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '5';">5</span>
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '6';">6</span>
                         </div>
                         <div class="row1">
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '7';">7</span>
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '8';">8</span>
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '9';">9</span>
                         </div>
                         <div class="row1">
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value.slice(0, -1);"><</span>
                            <span class="numpad" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '0';">0</span>
                            <span class="numpad" onclick="document.getElementById('inspected-by').value='';">Clear</span>
                         </div>
                      </div>
                      <img src="{{ asset('img/tap.gif') }}" style="margin-top: -20px; width: 60%;" />
                   </div>
                </div>
                <div class="row">
                   <div class="col-md-6">
                      <button type="button" class="btn btn-secondary btn-block btn-lg prevtab">Previous</button>
                   </div>
                   <div class="col-md-6">
                      <button type="submit" class="btn btn-info btn-block btn-lg">Submit</button>
                   </div>
                </div>
             </div>
          </div>
       </form>
    </div>
 </div>
 
 <style type="text/css">
 .inputGroup {
 background-color: #fff;
 display: block;
 margin: 5px 0;
 position: relative;
 }
 .inputGroup label {
 padding: 3px 20px;
 /*border: 1px solid red;*/
 width: 100%;
 display: block;
 text-align: left;
 color: #3C454C;
 cursor: pointer;
 position: relative;
 z-index: 2;
 transition: color 200ms ease-in;
 overflow: hidden;
 }
 .inputGroup label:before {
 width: 10px;
 height: 10px;
 border-radius: 50%;
 content: '';
 background-color: #5562eb;
 position: absolute;
 left: 50%;
 top: 50%;
 -webkit-transform: translate(-50%, -50%) scale3d(1, 1, 1);
 transform: translate(-50%, -50%) scale3d(1, 1, 1);
 transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
 opacity: 0;
 z-index: -1;
 }
 .inputGroup label:after {
 width: 32px;
 height: 32px;
 content: '';
 border: 2px solid #D1D7DC;
 background-color: #fff;
 background-image: url("data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
 background-repeat: no-repeat;
 background-position: 2px 3px;
 border-radius: 50%;
 z-index: 2;
 position: absolute;
 right: 30px;
 top: 50%;
 -webkit-transform: translateY(-50%);
 transform: translateY(-50%);
 cursor: pointer;
 transition: all 200ms ease-in;
 }
 .inputGroup input:checked ~ label {
 color: #fff;
 }
 .inputGroup input:checked ~ label:before {
 -webkit-transform: translate(-50%, -50%) scale3d(56, 56, 1);
 transform: translate(-50%, -50%) scale3d(56, 56, 1);
 opacity: 1;
 }
 .inputGroup input:checked ~ label:after {
 background-color: #54E0C7;
 border-color: #54E0C7;
 }
 .inputGroup input {
 width: 32px;
 height: 32px;
 order: 1;
 z-index: 2;
 position: absolute;
 right: 30px;
 top: 50%;
 -webkit-transform: translateY(-50%);
 transform: translateY(-50%);
 cursor: pointer;
 visibility: hidden;
 }
 
 .form {
 padding: 0 16px;
 max-width: 550px;
 margin: 10px 0 auto auto;
 font-size: 15px;
 font-weight: 600;
 line-height: 36px;
 }
 
 </style>