<div class="row">
   <div class="col-md-12 p-1">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" id="qi-tabs-1" role="tablist" style="display: none;">
         @php
            $index_tab = 0;
         @endphp
         @foreach($checklist as $category => $list)
         @php
            $tab = $index_tab;
         @endphp
         <li class="nav-item">
            <a class="nav-link {{ ($loop->first) ? 'active' : '' }} custom-tabs-1" id="tab{{ $tab }}" data-toggle="tab" href="#tab{{ $tab }}-inspection" role="tab" aria-controls="tab{{ $tab }}" aria-selected="{{ ($loop->first) ? 'true' : '' }}">{{ $category }}</a>
         </li>
         @php
            $index_tab = $index_tab + 1;
         @endphp
         @endforeach
         <li class="nav-item">
            <a class="nav-link custom-tabs-1" id="tablast" data-toggle="tab" href="#tablast-inspection" role="tab" aria-controls="tablast" aria-selected="false">Inspection Result</a>
         </li>
      </ul>
      <!-- Tab panes -->
      <form action="/submit_quality_inspection" method="POST" id="quality-inspection-frm">
         @csrf
         <div class="tab-content" style="min-height: 500px;" id="inspection-tabs">
            @php
               $index = 0;
            @endphp
            @foreach($checklist as $category => $list)
            @php
               $tab = $index;
            @endphp
            <div class="tab-pane {{ ($loop->first) ? 'active' : '' }}" id="tab{{ $tab }}-inspection" role="tabpanel" aria-labelledby="tab{{ $tab }}">
               <input type="hidden" name="reject_level" value="{{ $reject_levels[$index] }}">
               <input type="hidden" name="sample_size" value="{{ $sample_sizes[$index] }}">
               <div class="row p-0 m-0" style="min-height: 420px;">
                  <div class="col-md-7">
                     <input type="hidden" id="tab{{ $tab }}-inspection-validated-sample-size" value="1">
                     <span class="reject-level" style="display: none;">{{ $reject_levels[$index] }}</span>
                     <table style="width: 100%;">
                        <tr>
                           <td style="font-size: 12pt; padding: 3px 3px 0 3px;"><b>{{ $production_order_details->production_order }} - {{ $production_order_details->item_code }}</b></td>
                           <td class="text-right" style="font-size: 12pt; padding: 3px 3px 0 3px;">Sample Size: <span class="sample-size font-weight-bold">{{ $sample_sizes[$index] }}</span></td>
                        </tr>
                     </table>
                     @foreach($list as $ind => $result)
                        <div class="form p-0">
                           <h6 class="text-center"><span class="checklist-category">{{ $ind }}</span> - {{ $category }}</h6>
                           @if($loop->first)
                              <span class="chklist-cat" style="display: none;">No {{ $category }} found.</span>
                           @endif
                           @if(stripos($ind, 'Variable') !== false)
                              <table style="width: 100%;">
                                 <col style="width: 70%;">
                                 <col style="width: 30%;">
                                 @foreach ($result as $r)
                                    @if(strtolower($r->reject_checklist) == 'material type')
                                    <tr>
                                       <td>
                                          <div class="inputGroup m-1">
                                             <input id="option{{ $r->reject_list_id }}" name="option{{ $r->reject_list_id }}" type="checkbox" class="qc-chk select-all-{{ $tab }}" data-reject-reason="{{ $r->reject_reason }}" value="{{ $r->reject_list_id }}" />
                                             <label for="option{{ $r->reject_list_id }}">{{ $r->reject_checklist }}</label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group m-1">
                                             <select class="form-control form-control-lg" id="option{{ $r->reject_list_id }}-input">
                                                <option value=""></option>
                                                <option value="CRS">CRS</option>
                                                <option value="HRS">HRS</option>
                                                <option value="Aluminum">Aluminum</option>
                                             </select>
                                          </div>   
                                       </td>
                                    </tr>
                                    @endif
                                    @if(strtolower($r->reject_checklist) != 'material type')
                                    <tr>
                                       <td>
                                          <div class="inputGroup m-1">
                                             <input id="option{{ $r->reject_list_id }}" name="option{{ $r->reject_list_id }}" type="checkbox" class="qc-chk select-all-{{ $tab }}" data-reject-reason="{{ $r->reject_reason }}" value="{{ $r->reject_list_id }}" />
                                             <label for="option{{ $r->reject_list_id }}">{{ $r->reject_checklist }}</label>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="input-group m-1 text-center">
                                             <input type="text" class="form-control form-control-lg qty-input" data-edit="1" id="option{{ $r->reject_list_id }}-input" value="0" readonly  style="background-color:#E5E8E8;">
                                             <div class="input-group-append text-center">
                                             <span class="input-group-text" style="border-radius: 0px; background-color:#E5E8E8;">mm</span>
                                             </div>
                                          </div>
                                       </td>
                                    </tr>
                                    @endif
                                 @endforeach
                              </table>
                              @else
                                 @php
                                    $limit = ceil(count($result) / 2)
                                 @endphp
                                 @if ($limit > 1)
                                 <div class="row p-0 m-0">
                                    @foreach ($result->chunk($limit) as $res)
                                    <div class="col-md-6 p-0">
                                       <ul class="list-unstyled">
                                          @foreach ($res as $r0)
                                          <li class="p-1 d-block">
                                             <div class="inputGroup">
                                                <input id="option{{ $r0->reject_list_id }}" name="option{{ $r0->reject_list_id }}" type="checkbox" class="qc-chk select-all-{{ $tab }}" data-reject-reason="{{ $r0->reject_reason }}" value="{{ $r0->reject_list_id }}" />
                                                <label for="option{{ $r0->reject_list_id }}"><p style="line-height: 1.6;">{{ $r0->reject_checklist }}</p></label>
                                             </div>
                                          </li>
                                          @endforeach
                                       </ul>
                                    </div>
                                    @endforeach
                                 </div>
                                 @else
                                 <div class="row">
                                    <div class="col-md-12 pr-3 pl-3">
                                       <ul class="list-unstyled">
                                          @foreach ($result as $r1)
                                             <li class="p-1 d-block">
                                                <div class="inputGroup">
                                                   <input id="option{{ $r1->reject_list_id }}" name="option{{ $r1->reject_list_id }}" type="checkbox" class="qc-chk select-all-{{ $tab }}" data-reject-reason="{{ $r1->reject_reason }}" value="{{ $r1->reject_list_id }}" />
                                                   <label for="option{{ $r1->reject_list_id }}">{{ $r1->reject_checklist }}</label>
                                                </div>
                                             </li>
                                          @endforeach
                                       </ul>
                                    </div>
                                 </div>
                                 @endif
                              @endif
                           </div>
                        @endforeach
                     </div>
                  @php
                     $index = $index + 1;
                  @endphp
                  <div class="col-md-5">
                     <div class="row">
                        <div class="col-md-4">
                           <div class="form-group text-center">
                              <label for="">Qty</label>
                              <input type="text" class="form-control form-control-lg qty-input" data-edit="0" id="tab{{ $tab }}-inspection-qty" value="{{ $production_order_details->qty_to_manufacture }}" readonly>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group text-center">
                              <label for="check-qty">Qty Checked</label>
                              <input type="text" class="form-control form-control-lg qty-input" name="qty_checked" data-edit="1" id="tab{{ $tab }}-inspection-qty-checked" value="0" readonly required>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group text-center">
                              <label for="reject-qty">Reject Qty</label>
                              <input type="text" class="form-control form-control-lg qty-input" name="qty_reject" data-edit="1" id="tab{{ $tab }}-inspection-qty-reject" value="0" readonly required>
                           </div>
                        </div>
                        <style>
                           .qty-input{
                              font-size: 20pt; text-align: center; font-weight: bolder;
                           }
                        </style>
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
                           <span class="numpad num">.</span>
                        </div>
                     </div>
                     <div class="row form mt-5">
                        <div class="col-md-12">
                           <div class="inputGroup">
                              <input id="select-all-{{ $tab }}" class="select-all-checklist-per-tab" type="checkbox" />
                              <label for="select-all-{{ $tab }}">Select All</label>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
              <div class="row">
                  @if($loop->first)
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
                  </div>
                  @else
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-block btn-lg prev-tab">Previous</button>
                  </div>
                  @endif
                  <div class="col-md-6">
                     <button type="button" class="btn btn-primary btn-block btn-lg next-tab" data-tab-id="tab{{ $tab }}-inspection" id="tab{{ $tab }}-inspection-next-btn">Next</button>
                  </div>
               </div>
            </div>
            @endforeach
            <div class="tab-pane" id="tablast-inspection" role="tabpanel" aria-labelledby="tablast">
               <div class="row" style="min-height: 420px;">
                  <div class="col-md-6">
                     <table style="width: 100%;">
                        <col style="width: 33%;">
                        <col style="width: 67%;">
                        <tr>
                           <td class="text-center font-weight-bold" style="font-size: 18pt;" colspan="2">
                              <span id="qc-status">QC STATUS</span>
                           </td>
                        </tr>
                        <tr>
                           <td class="text-center font-weight-bold" style="font-size: 12pt; padding: 3px 3px 0 3px;" colspan="2">
                              {{ $production_order_details->production_order }} - {{ $production_order_details->item_code }}
                           </td>
                        </tr>
                        <tr>
                           <td class="text-center font-weight-bold" style="font-size: 12pt; padding: 3px 3px 0 3px;" colspan="2">
                              {{ $workstation_details->workstation_name }} - {{ $process_details->process_name }}
                           </td>
                        </tr>
                        <tr>
                           <td class="text-center font-weight-bold" style="font-size: 11pt; padding: 8px 3px 0 3px;" colspan="2">
                              Qty Checked: <span id="final-qty-checked">0</span> {{ $production_order_details->stock_uom }}
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2">
                              <div id="qa-result-div"></div>
                           </td>
                        </tr>
                        <tr>
                           <td colspan="2" class="text-center">
                              <div id="qa-result-div-1"></div>
                           </td>
                        </tr>
                        <tr class="reject-details-tr">
                           <td class="align-top font-weight-bold" style="font-size: 11pt; padding: 25px 3px 0 5px;">QA Remarks</td>
                           <td style="">
                              <div class="form-group" style="margin: 18px 0 0 0;">
                                 <select name="qc_remarks" id="qc-remarks-select" class="form-control form-control-lg" style="font-size: 16pt;">
                                    <option value=""></option>
                                    <option value="For Rework">Rework</option>
                                    <option value="Scrap">Scrap</option>
                                 </select>
                              </div>
                           </td>
                        </tr>
                     </table>
                  </div>
                  <div class="col-md-6 text-center" id="qc-enter-operator">
                     <h5 class="text-center">Scan Authorized QC Employee ID</h5>
                     <input type="hidden" name="time_log_id" id="time-log-id-input" value="{{ $timelog_details->time_log_id }}">
                     <input type="hidden" name="workstation" value="{{ $workstation_details->workstation_name }}">
                     <input type="hidden" name="total_rejects" id="total-rejects-input" value="0">
                     <input type="hidden" name="total_checked" id="total-checked-input" value="0">
                     <input type="hidden" name="rejection_types" id="rejection-types-input">
                     <input type="hidden" name="rejection_values" id="rejection-values-input">
                     <input type="hidden" name="inspection_type" id="inspection-type-input" value="{{ $inspection_type }}">
                     <div class="form-group">
                        <input type="password" class="form-control form-control-lg" name="inspected_by" readonly style="text-align: center; font-size: 20pt; font-weight: bolder;" id="inspected-by">
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
                     <button type="button" class="btn btn-secondary btn-block btn-lg prev-tab">Previous</button>
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
 
 <style type="text/css">
 .inputGroup {
 display: block;
 margin: 0;
 position: relative;
 width: 100%;
 }
 .inputGroup label {
 padding: 5px 20px 5px 47px;
 width: 100%;
 height: 100%;
 margin: 0;
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
 left: 10px;
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