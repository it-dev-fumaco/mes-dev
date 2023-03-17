<div class="modal-content">
   <div class="text-white rounded-top" style="background-color: #f57f17;">
      <div class="d-flex flex-row justify-content-between pt-2 pb-2 pr-3 pl-3 align-items-center">
         <h5 class="font-weight-bold m-0 p-0" style="font-size: 14pt;">{{ $reject_category }}</h5>
         <div class="float-right">
            <h5 class="modal-title font-weight-bold p-0 mr-3 font-italic d-inline-block" style="font-size: 14pt;">{{ $workstation_details->workstation_name }} - {{ $production_order_details->production_order }}</h5>
            <button type="button" class="close d-inline-block ml-3" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
      </div>
   </div>
   <div class="modal-body p-2">
      <div class="row p-0 m-0">
         <div class="col-md-12 p-0">
            <table style="width: 100%; border-color: #D5D8DC;">
               <col style="width: 20%;">
               <col style="width: 70%;">
               <col style="width: 10%;">
               <tr style="font-size: 8pt;">
                  <td class="text-center" style="border: 1px solid #ABB2B9;">
                     <span class="d-block font-weight-bold">{{ $production_order_details->sales_order }}{{ $production_order_details->material_request }}</span>
                     <span class="d-block">{{ $production_order_details->customer }}</span>
                  </td>
                  <td class="text-justify p-1" style="border: 1px solid #ABB2B9;"><span class="font-weight-bold">{{ $production_order_details->item_code }}</span> - {{ $production_order_details->description }}</td>
                  <td class="text-center" style="border: 1px solid #ABB2B9; font-size: 14pt;">{{ $production_order_details->qty_to_manufacture }}</td>
               </tr>
               <tr style="font-size: 8pt;" class="selected-item-tbl-cell d-none">
                  <td class="text-justify" colspan="3" style="border: 1px solid #ABB2B9;">
                     <div class="d-flex flex-row">
                        <img src="#" alt="-" class="m-1 selected-item-image" style="width: 50px; height: 50px;">
                        <div class="m-1">
                           <span class="font-weight-bold selected-item-code">-</span>
                           <span class="selected-item-description">-</span>
                        </div>
                     </div>
                  </td>
               </tr>
            </table>
         </div>
         <div class="col-md-12 p-0">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="qi-tabs-1" role="tablist" style="display: none;">
               <li class="nav-item">
                  <a class="nav-link {{ count($production_order_items) > 0 ? 'active' : '' }} custom-tabs-1" id="tabitem" data-toggle="tab" href="#tab-item-for-inspection" role="tab" aria-controls="tablast" aria-selected="false">Item for Inspection</a>
               </li>
               @php
                  $index_tab = 0;
               @endphp
               @foreach($checklist as $category => $list)
               @php
                  $tab = $index_tab;
               @endphp
               <li class="nav-item">
                  <a class="nav-link {{ ($loop->first && count($production_order_items) <= 0) ? 'active' : '' }} custom-tabs-1" id="tab{{ $tab }}" data-toggle="tab" href="#tab{{ $tab }}-inspection" role="tab" aria-controls="tab{{ $tab }}" aria-selected="{{ ($loop->first) ? 'true' : '' }}">{{ $category }}</a>
               </li>
               @php
                  $index_tab = $index_tab + 1;
               @endphp
               <li class="nav-item">
                  <a class="nav-link custom-tabs-1" id="tab-occurence" data-toggle="tab" href="#tab-{{ $tab }}-occurence" role="tab" aria-controls="tab-{{ $tab }}" aria-selected="false">{{ $category }} Reject Type Occurence</a>
               </li>
               @endforeach
               <li class="nav-item">
                  <a class="nav-link custom-tabs-1" id="tablast" data-toggle="tab" href="#tablast-inspection" role="tab" aria-controls="tablast" aria-selected="false">Inspection Result</a>
               </li>
            </ul>
            <!-- Tab panes -->
            <form action="/submit_quality_inspection" method="POST" id="quality-inspection-frm">
               @csrf
               <div class="tab-content mt-2" style="min-height: 400px;" id="inspection-tabs">
                  <div class="tab-pane {{ count($production_order_items) > 0 ? 'active' : '' }}" id="tab-item-for-inspection" role="tabpanel" aria-labelledby="tablast">
                     <div class="row p-0 m-0" style="min-height: 420px;">
                        <div class="col-md-12 p-0">
                           <table class="table table-striped table-bordered">
                              <col style="width: 54%;">
                              <col style="width: 12%;">
                              <col style="width: 12%;">
                              <col style="width: 12%;">
                              <col style="width: 10%;">
                              <thead class="text-primary text-uppercase" style="font-size: 5pt;">
                                 <th class="text-center font-weight-bold p-2">Item Description</th>
                                 <th class="text-center font-weight-bold p-2">Required Qty</th>
                                 <th class="text-center font-weight-bold p-2">Inspected Qty</th>
                                 <th class="text-center font-weight-bold p-2">Rejected Qty</th>
                                 <th class="text-center font-weight-bold p-2">Action</th>
                              </thead>
                              <tbody>
                                 @forelse ($production_order_items as $r)
                                 @php
                                    $img = 'http://athenaerp.fumaco.local/storage';
												$img .= array_key_exists($r->item_code, $item_images) ? "/img/" . $item_images[$r->item_code] : "/icon/no_img.png";

                                    $poinspected_qty = array_key_exists($r->item_code, $inspected_component_qtys) ? $inspected_component_qtys[$r->item_code][0]->inspected_qty : 0;
                                    $porejected_qty = array_key_exists($r->item_code, $inspected_component_qtys) ? $inspected_component_qtys[$r->item_code][0]->rejected_qty : 0;
                                 @endphp
                                 <tr>
                                    <td class="text-justify p-1">
                                       <div class="d-flex flex-row">
                                          <img src="{{ $img }}" alt="{{ $r->item_code }}" class="m-1" style="width: 50px; height: 50px;">
                                          <div class="m-1">
                                             <span class="d-block font-weight-bold">{{ $r->item_code }}</span>
                                             <span class="d-block" style="font-size: 8pt;">{{ strip_tags($r->description) }}</span>
                                          </div>
                                       </div>
                                    </td>
                                    <td class="text-center p-2 font-weight-bold">{{ number_format($r->required_qty) }}</td>
                                    <td class="text-center p-2 font-weight-bold">{{ number_format($poinspected_qty) }}</td>
                                    <td class="text-center p-2 font-weight-bold">{{ number_format($porejected_qty) }}</td>
                                    <td class="text-center p-2">
                                       <button type="button" class="btn pb-2 pr-3 pt-2 pl-3 btn-primary select-item-for-inspection-btn" data-img="{{ $img }}" data-item-code="{{ $r->item_code }}" data-item-desc="{{ strip_tags($r->description) }}" data-required-qty="{{ $r->required_qty * 1 }}">Inspect</button>
                                    </td>
                                 </tr>
                                 @empty
                                 <tr>
                                    <td colspan="5" class="text-center text-uppercase text-muted">No item(s) found</td>
                                 </tr>
                                 @endforelse
                              </tbody>
                           </table>
                        </div>            
                     </div>
                  </div>
                  @php
                     $index = 0;
                  @endphp
                  @forelse($checklist as $category => $list)
                  @php
                     $tab = $index;
                  @endphp
                  <div class="tab-pane {{ ($loop->first && count($production_order_items) <= 0) ? 'active' : '' }}" id="tab{{ $tab }}-inspection" role="tabpanel" aria-labelledby="tab{{ $tab }}">
                     <div class="row p-0 m-0">
                        <div class="col-8 p-0">
                           @foreach($list as $ind => $result)
                           <span class="checklist-category d-none">{{ $ind }}</span>
                           <div class="p-0">
                              @if(stripos($ind, 'Variable') !== false)
                              <table style="width: 100%;">
                                 <col style="width: 70%;">
                                 <col style="width: 30%;">
                                 @foreach ($result as $r)
                                 @if(strtolower($r->reject_checklist) == 'material type')
                                 <tr>
                                    <td class="p-1">
                                       <div class="inputGroup chk-list m-0">
                                          <input id="option{{ $r->reject_list_id }}" name="option{{ $r->reject_list_id }}" type="checkbox" class="qc-chk select-all-{{ $tab }}" data-reject-reason="{{ $r->reject_reason }}" value="{{ $r->reject_list_id }}" />
                                          <label for="option{{ $r->reject_list_id }}">{{ $r->reject_checklist }}</label>
                                       </div>
                                    </td>
                                    <td class="p-1">
                                       <div class="form-group m-0">
                                          <select class="form-control m-0 rounded p-2" id="option{{ $r->reject_list_id }}-input">
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
                                    <td class="p-1">
                                       <div class="inputGroup chk-list">
                                          <input id="option{{ $r->reject_list_id }}" name="option{{ $r->reject_list_id }}" type="checkbox" class="qc-chk select-all-{{ $tab }}" data-reject-reason="{{ $r->reject_reason }}" value="{{ $r->reject_list_id }}" />
                                          <label for="option{{ $r->reject_list_id }}">{{ $r->reject_checklist }}</label>
                                       </div>
                                    </td>
                                    <td class="p-1">
                                       <div class="input-group m-0 text-center">
                                          <input type="text" class="form-control rounded p-2 qty-input" data-edit="1" id="option{{ $r->reject_list_id }}-input" value="0" readonly  style="background-color:#E5E8E8;">
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
                                       <li class="pb-1 pt-0 pl-1 pr-1 d-block">
                                          <div class="inputGroup chk-list">
                                             <input id="option{{ $r0->reject_list_id }}" name="option{{ $r0->reject_list_id }}" type="checkbox" class="qc-chk select-all-{{ $tab }}" data-reject-reason="{{ $r0->reject_reason }}" value="{{ $r0->reject_list_id }}" />
                                             <label class="border" for="option{{ $r0->reject_list_id }}">{{ $r0->reject_checklist }}</label>
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
                                       <li class="pb-1 pt-0 pl-1 pr-1 d-block">
                                          <div class="inputGroup chk-list">
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
                        <div class="col-md-4">
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="form-group text-center text-uppercase">
                                    <label for="" style="font-size: 10px;">Qty</label>
                                    <input type="text" class="form-control rounded p-2 qty-input selected-item-required-qty" data-edit="0" id="tab{{ $tab }}-inspection-qty" value="{{ $production_order_details->qty_to_manufacture }}" readonly required>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group text-center text-uppercase">
                                    <label for="check-qty" style="font-size: 10px;">Qty Checked</label>
                                    <input type="text" class="form-control rounded p-2 qty-input" name="qty_checked" data-edit="1" id="tab{{ $tab }}-inspection-qty-checked" value="0" readonly required>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group text-center text-uppercase">
                                    <label for="reject-qty" style="font-size: 10px;">Reject Qty</label>
                                    <input type="text" class="form-control rounded p-2 qty-input" name="qty_reject" data-edit="1" id="tab{{ $tab }}-inspection-qty-reject" value="0" readonly required>
                                 </div>
                              </div>
                           </div>
                           <div class="text-center numpad-div enter-checked-qty">
                              <div class="row1">
                                 <span class="numpad1 num">1</span>
                                 <span class="numpad1 num">2</span>
                                 <span class="numpad1 num">3</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1 num">4</span>
                                 <span class="numpad1 num">5</span>
                                 <span class="numpad1 num">6</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1 num">7</span>
                                 <span class="numpad1 num">8</span>
                                 <span class="numpad1 num">9</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1 del"><</span>
                                 <span class="numpad1 num">0</span>
                                 <span class="numpad1 num">.</span>
                              </div>
                           </div>
                           <div class="row form mt-5">
                              <div class="col-md-12">
                                 <div class="inputGroup pb-1 pt-0 pl-1 pr-1">
                                    <input id="select-all-{{ $tab }}" class="select-all-checklist-per-tab" type="checkbox" />
                                    <label for="select-all-{{ $tab }}" class="border">Select All</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        @if($loop->first && count($production_order_items) <= 0)
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
                  <div class="tab-pane" id="tab-{{ $tab }}-occurence" role="tabpanel" aria-labelledby="tab-{{ $tab }}">
                     <div class="row p-0 mb-0 mt-3 ml-0 mr-0" style="min-height: 380px;">
                        <div class="col-8 p-0">
                           <h6 class="text-center">Enter Qty per Reject Classification</h6>
                           <div class="row p-0 m-0" style="font-size: 12px;" id="occurence-div">
                             
                           </div>
                        </div>
                        <div class="col-md-4 pt-3">
                           <div class="text-center numpad-div enter-checked-qty">
                              <div class="row1">
                                 <span class="numpad1 num">1</span>
                                 <span class="numpad1 num">2</span>
                                 <span class="numpad1 num">3</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1 num">4</span>
                                 <span class="numpad1 num">5</span>
                                 <span class="numpad1 num">6</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1 num">7</span>
                                 <span class="numpad1 num">8</span>
                                 <span class="numpad1 num">9</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1 del"><</span>
                                 <span class="numpad1 num">0</span>
                                 <span class="numpad1 num">.</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <button type="button" class="btn btn-secondary btn-block btn-lg prev-tab">Previous</button>
                        </div>
                        <div class="col-md-6">
                           <button type="button" class="btn btn-primary btn-block btn-lg next-tab" data-tab-id="tab-{{ $tab }}-occurence" id="tab{{ $tab }}-occurence-next-btn">Next</button>
                        </div>
                     </div>
                  </div>
                  @empty
                  <div class="container-fluid text-center">
                     <div class="col-8 mx-auto">
                        <h5 class="mt-5 ml-3" style="border-left: 10px solid #17A2B8 !important;">Quality Inspection for this workstation is not set up. <br> Please contact QA Manager.</h5>
                     </div>
                  </div>
                  @endforelse
                  <div class="tab-pane" id="tablast-inspection" role="tabpanel" aria-labelledby="tablast">
                     <div class="row p-0 m-0" style="min-height: 420px;">
                        <div class="col-md-8">
                           <table style="width: 100%;">
                              <col style="width: 33%;">
                              <col style="width: 67%;">
                              <tr>
                                 <td class="text-center font-weight-bold" style="font-size: 18pt;" colspan="2">
                                    <span id="qc-status">QC STATUS</span>
                                 </td>
                              </tr>
                              <tr>
                                 <td class="text-center font-weight-bold" style="font-size: 12pt; padding: 3px 3px 0 3px;" colspan="2">{{ $process_details->process_name }}</td>
                              </tr>
                              <tr>
                                 <td class="text-center font-weight-bold" style="font-size: 11pt; padding: 8px 3px 0 3px;" colspan="2">
                                    Qty Checked: <span id="final-qty-checked">0</span> {{ $production_order_details->stock_uom }}
                                 </td>
                              </tr>
                              <tr>
                                 <td class="text-center font-weight-bold" style="font-size: 11pt; padding: 8px 3px 0 3px;" colspan="2">
                                    Rejected Qty: <span id="final-qty-rejected">0</span> {{ $production_order_details->stock_uom }}
                                 </td>
                              </tr>
                              <tr>
                                 <td colspan="2">
                                    <div id="qa-result-div" class="mt-2"></div>
                                 </td>
                              </tr>
                              <tr>
                                 <td colspan="2" class="text-center">
                                    <div id="qa-result-div-1" class="mt-2"></div>
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
                        <div class="col-md-4 text-center" id="qc-enter-operator">
                           <h5 style="font-size: 12pt;" class="text-center">Scan Authorized QC Employee ID</h5>
                           <input type="hidden" name="time_log_id" id="time-log-id-input" value="{{ $timelog_details->time_log_id }}">
                           <input type="hidden" name="workstation" value="{{ $workstation_details->workstation_name }}">
                           <input type="hidden" name="total_rejects" id="total-rejects-input" value="0">
                           <input type="hidden" name="total_checked" id="total-checked-input" value="0">
                           <input type="hidden" name="rejection_types" id="rejection-types-input">
                           <input type="hidden" name="rejection_values" id="rejection-values-input">
                           <input type="hidden" name="inspection_type" id="inspection-type-input" value="{{ $inspection_type }}">
                           <input type="hidden" name="reject_category_id" value="{{ request('reject_category') }}">
                           <input type="hidden" name="item_code" class="selected-item-code-val">
                           <input type="hidden" name="reject_level" value="{{ $reject_levels[$index] }}">
                           <input type="hidden" name="sample_size" value="{{ $sample_sizes[$index] }}">
                           <input type="hidden" name="checklist_url" value="{{ count($production_order_items) > 0 ? \Request::getRequestUri() : null }}">
                         
                           <div class="form-group">
                              <input type="password" class="form-control p-2 rounded" name="inspected_by" readonly style="text-align: center; font-size: 16pt; font-weight: bolder;" id="inspected-by">
                           </div>
                           <button type="button" class="btn btn-secondary btn-sm toggle-manual-input" style="margin-bottom: 10px;">Tap here for Manual Entry</button>
                           <div class="text-center numpad-div manual enter-checked-qty" style="display: none;">
                              <div class="row1">
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '1';">1</span>
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '2';">2</span>
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '3';">3</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '4';">4</span>
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '5';">5</span>
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '6';">6</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '7';">7</span>
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '8';">8</span>
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '9';">9</span>
                              </div>
                              <div class="row1">
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value.slice(0, -1);"><</span>
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value=document.getElementById('inspected-by').value + '0';">0</span>
                                 <span class="numpad1" onclick="document.getElementById('inspected-by').value='';">Clear</span>
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
   </div>
</div>
<style type="text/css">
   .qty-input{
      font-size: 16pt; text-align: center; font-weight: bolder;
   }
   .enter-checked-qty .numpad1{
      display: inline-block !important;
      border: 1px solid #333 !important;
      border-radius: 5px !important;
      text-align: center !important;
      width: 25%;
      height: 25%;
      padding: 10px !important;
      margin: 3px !important;
      font-size: 13pt !important;
      color: inherit !important;
      background: rgba(255, 255, 255, 0.7) !important;
      transition: all 0.3s ease-in-out !important;
   }
   .inputGroup {
      display: block;
      margin: 0;
      position: relative;
      width: 100%;
   }
   .inputGroup label {
      font-size: 9pt;
      padding: 8px 8px 8px 45px;
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
      padding: 20px;
   }
   .inputGroup label:after {
      width: 20px;
      height: 20px;
      content: '';
      border: 2px solid #D1D7DC;
      background-color: #fff;
      background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='3 3 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
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
      width: 20px;
      height: 20px;
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
 </style>