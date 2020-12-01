<!-- Nav tabs -->
<ul class="nav nav-tabs" id="qi-tabs" role="tablist" style="display: none;">
   {{-- @foreach($data as $r) --}}
   <li class="nav-item">
      <a class="nav-link active" id="{{ $data->tsdname }}-tab" data-toggle="tab" href="#rej{{ $data->tsdname }}" role="tab" aria-controls="{{ $data->tsdname }}" aria-selected="true">{{ $data->tsdname }}</a>
   </li>
   {{-- @endforeach --}}
   <li class="nav-item">
      <a class="nav-link" id="confirm-tab" data-toggle="tab" href="#confirm" role="tab" aria-controls="confirm" aria-selected="false">Confirmation</a>
   </li>
</ul>
<!-- Tab panes -->
<form action="/confirm_reject" method="POST" id="confirm-qc-reject-frm">
   @csrf
<div class="tab-content" style="min-height: 400px;">
   {{-- @foreach($data as $r) --}}
   <div class="tab-pane active" id="rej{{ $data->tsdname }}" role="tabpanel" aria-labelledby="{{ $data->tsdname }}-tab">
      <div class="row" style="min-height: 350px;">
         <div class="col-md-6">
            <div class="row">
               <div class="col-md-12">
                  <input type="hidden" name="tsdname[]" value="{{$data->tsdname}}">
                  <div class="form-group">
                     <label for="rejection-type-qa-frm">Rejection Reason</label>
                     @php
                     $reject_types = ['Not Reject', 'Wrong bending', 'Forming reject', 'Wrong punch', 'Wrong cut-size', 'Wrong guide', 'Wrong program', 'Wrong thickness', 'Double punch', 'Deform', 'Dent', 'Stain', 'Broken', 'Program error', 'Machine Set-Up Error'];
                     @endphp
                     <select class="form-control rejection-type-sel" id="ii{{$data->tsdname}}" name="rejection_type[]" required style="font-size: 18pt;" data-id="i{{$data->tsdname}}">
                        @foreach($reject_types as $type)
                        <option value="{{$type}}" {{ ($data->rejection_type == $type) ? 'selected' : '' }}>{{$type}}</option>
                        @endforeach
                     </select>

                  </div>
               </div>
               <div class="col-md-6 text-center">
                  <div class="form-group">
                     <label for="reject-qty-qa-frm">Accepted Qty</label>
                     <h3 class="text-center">{{ number_format($data->qty_accepted) }}</h3>
                  </div>
               </div>
               <div class="col-md-6 text-center">
                  <div class="form-group">
                     <label for="reject-qty-qa-frm">Reject Qty</label>
                     <input type="text" class="form-control" name="reject_qty[]" id="i{{$data->tsdname}}" value="{{ number_format($data->reject) }}" readonly required style="font-size: 20pt; text-align: center; font-weight: bolder;">
                  </div>
               </div>
            </div>
            Prod. Order: <b>{{ $data->production_order }}</b><br>
            <b>{{ $data->item_code }}</b><br>{{ $data->description }}<br><br>Operator: <b>{{ $data->operator_name }}</b>
         </div>
         <div class="col-md-6">
            <div class="text-center numpad-div">
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '1';">1</span>
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '2';">2</span>
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '3';">3</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '4';">4</span>
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '5';">5</span>
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '6';">6</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '7';">7</span>
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '8';">8</span>
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '9';">9</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value.slice(0, -1);"><</span>
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value=document.getElementById('i{{$data->tsdname}}').value + '0';">0</span>
                  <span class="numpad" onclick="document.getElementById('i{{$data->tsdname}}').value='';">Clear</span>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-md-6">
            {{-- @if($loop->first) --}}
            <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
            {{-- @else --}}
            {{-- <button type="button" class="btn btn-secondary btn-block btn-lg prevtab">Previous</button> --}}
            {{-- @endif --}}
         </div>
         <div class="col-md-6">
            <button type="button" class="btn btn-primary btn-block btn-lg nexttab" data-req="{{ number_format($data->qty_accepted) }}" data-id="i{{$data->tsdname}}">Verify</button>
         </div>
      </div>
   </div>
   {{-- @endforeach --}}
   <div class="tab-pane" id="confirm" role="tabpanel" aria-labelledby="confirm-tab">
      <div class="row" style="min-height: 350px;">
         <div class="col-md-6">
            <div class="text-center" style="font-size: 14pt;">
               Prod. Order: <b>{{ $data->production_order }}</b><br><br>
               <b>{{ $data->item_code }}</b>
            </div>
            <br>
            <div class="form-group text-center" id="remarks-div-rejects">
               <label>Remarks</label>
               <select class="form-control" style="font-size: 18pt;" name="sel_dispo">
                  <option value="" selected>--Select Remarks--</option>
                  <option value="Scrap">Scrap</option>
                  <option value="Rework">Rework</option>
               </select>
            </div>
            <div class="form-group text-center" id="remarks-div-rejects-1">
               <label>Remarks</label>
               <h3 class="reject-remarks">Not Reject</h3>
            </div> 
         </div>
         <div class="col-md-6 text-center">
            <h5 class="text-center">Tap Authorized QC Employee ID</h5>
            {{-- <div class="row"> --}}
               {{-- <div class="col-md-8 offset-md-2"> --}}
                  <div class="form-group">
                     <input type="text" class="form-control form-control-lg" name="inspected_by" readonly style="text-align: center; font-size: 20pt; font-weight: bolder;" id="inspected-by">
                  </div>
                  <button type="button" class="toggle-manual-input" style="margin-bottom: 10px;">Tap here for Manual Entry</button>
               {{-- </div> --}}
            {{-- </div> --}}
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