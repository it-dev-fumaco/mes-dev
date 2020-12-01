<!-- Nav tabs -->
<ul class="nav nav-tabs" id="qi-tabs-1" role="tablist" style="display: none;">
   {{-- @foreach($data as $i => $r) --}}
   <li class="nav-item">
      <a class="nav-link active" id="{{ $data['tsdname'] }}-tab" data-toggle="tab" href="#qcmsri" role="tab" aria-controls="{{ $data['tsdname'] }}" aria-selected="true">{{ $data['tsdname'] }}</a>
   </li>
   {{-- @endforeach --}}
   <li class="nav-item">
      <a class="nav-link" id="confirm-tab" data-toggle="tab" href="#confirm" role="tab" aria-controls="confirm" aria-selected="false">Confirmation</a>
   </li>
</ul>
<!-- Tab panes -->
<form action="/confirm_machine_setup_random" method="POST" id="confirm-qc-msri-frm">
   @csrf
<div class="tab-content" style="min-height: 440px;">
   {{-- @foreach($data as $i => $r) --}}
   <div class="tab-pane active" id="qcmsri" role="tabpanel" aria-labelledby="{{ $data['tsdname'] }}-tab">
      <div class="row" style="min-height: 390px;">
         <div class="col-md-6">
            <input type="hidden" name="qc_type" value="{{$type}}">
            <input type="hidden" name="tsdname[]" value="{{$data['tsdname']}}">
            <input type="hidden" id="qc-chk-count{{$data['tsdname']}}">
            <input type="hidden" id="qc-status{{$data['tsdname']}}" name="qc_inspection_status[]">
            Prod. Order: <b>{{ $data['production_order'] }}</b><br><br>
            <b>{{ $data['production_item'] }}</b><br>{{ $data['description'] }}
            @if($type == 'Random Inspection')
            <div class="form">
               <h6 class="text-center">Checklist</h6>
               <div class="inputGroup">
                  <input id="option1" name="option1" type="checkbox" class="{{$data['tsdname']}}" />
                  <label for="option1">Visual</label>
               </div>
               <div class="inputGroup">
                  <input id="option2" name="option2" type="checkbox" class="{{$data['tsdname']}}" />
                  <label for="option2">Length - {{ $data['length']}}</label>
               </div>
               <div class="inputGroup">
                  <input id="option3" name="option3" type="checkbox" class="{{$data['tsdname']}}" />
                  <label for="option3">Width - {{ $data['width']}}</label>
               </div>
               <div class="inputGroup">
                  <input id="option4" name="option4" type="checkbox" class="{{$data['tsdname']}}" />
                  <label for="option4">Thickness - {{ $data['thickness']}}</label>
               </div>
            </div>
            @endif
         </div>
         <div class="col-md-6">
            <div class="row">
               <div class="col-md-{{ ($type == 'Quality Check') ? '12 text-center' : '6' }}">
                  <div class="form-group">
                     <label for="">Qty Produced</label>
                     <h3 class="text-center">{{ number_format($data['good']) }}</h3>
                  </div>
               </div>
               @php
               $qty = ($type == 'Quality Check') ? (int)$data['good'] : (int)$data['sampling_qty'];
               $qty_text = ($type == 'Quality Check') ? 'Setup Qty' : 'Qty Reject';
               $qty_text1 = ($type == 'Quality Check') ? 'Qty Checked' : 'Qty Reject';
               @endphp

               <div class="col-md-6" {{ ($type == 'Quality Check') ? 'hidden' : '' }}>
                  <div class="form-group">
                     <label for="reject-qty-qa-frm">{{ $qty_text }}</label>
                     <input type="text" class="form-control" name="qty[]" id="sqty{{$data['tsdname']}}" value="{{ number_format($qty) }}" readonly required style="font-size: 20pt; text-align: center; font-weight: bolder;">
                  </div>
               </div>
            </div>
            <div class="text-center numpad-div" {{ ($type == 'Quality Check') ? 'hidden' : '' }}>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '1';">1</span>
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '2';">2</span>
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '3';">3</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '4';">4</span>
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '5';">5</span>
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '6';">6</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '7';">7</span>
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '8';">8</span>
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '9';">9</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value.slice(0, -1);"><</span>
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value=document.getElementById('sqty{{$data['tsdname']}}').value + '0';">0</span>
                  <span class="numpad" onclick="document.getElementById('sqty{{$data['tsdname']}}').value='';">Clear</span>
               </div>
            </div>
         </div>
         @if($type == 'Quality Check')
         <div class="col-md-8 offset-md-2">
            <div class="form">
               <h6 class="text-center">Checklist</h6>
               <div class="inputGroup">
                  <input id="option1" name="option1" type="checkbox" class="{{$data['tsdname']}}" />
                  <label for="option1">Visual</label>
               </div>
               <div class="inputGroup">
                  <input id="option2" name="option2" type="checkbox" class="{{$data['tsdname']}}" />
                  <label for="option2">Length - {{ $data['length']}}</label>
               </div>
               <div class="inputGroup">
                  <input id="option3" name="option3" type="checkbox" class="{{$data['tsdname']}}" />
                  <label for="option3">Width - {{ $data['width']}}</label>
               </div>
               <div class="inputGroup">
                  <input id="option4" name="option4" type="checkbox" class="{{$data['tsdname']}}" />
                  <label for="option4">Thickness - {{ $data['thickness']}}</label>
               </div>
            </div>
         </div>
         @endif
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
            <button type="button" class="btn btn-primary btn-block btn-lg nexttab" data-qty="{{ number_format($data['qty_accepted']) }}" data-id="{{$data['tsdname']}}">Next</button>
         </div>
      </div>
   </div>
   {{-- @endforeach --}}
   <div class="tab-pane" id="confirm" role="tabpanel" aria-labelledby="confirm-tab">
      <div class="row" style="min-height: 390px;">
         <div class="col-md-6">
            <div class="text-center">
               <span class="badge badge-danger" id="qc-badge" style="font-size: 18pt; margin: 15px;">QC Failed</span>
            </div>
            <div class="text-center" style="font-size: 14pt;">
               Prod. Order: <b>{{ $data['production_order'] }}</b><br><br>
               <b>{{ $data['production_item'] }}</b>
            </div>
            <div class="text-center" style="font-size: 14pt; margin-top: 5px;">
               <span id="qty-type">{{$qty_text1}}: </span> <span id="qc-qty" style="font-weight: bold; font-size: 14pt;">0</span>
            </div>
            <br>
            <div class="form-group text-center" id="sel-dispo">
               <label>Remarks</label>
               <select class="form-control" style="font-size: 18pt;" name="sel_dispo">
                  <option value="" selected>--Select Remarks--</option>
                  <option value="Scrap">Scrap</option>
                  <option value="Rework">Rework</option>
               </select>
            </div> 
         </div>
         <div class="col-md-6 text-center">
            <h5 class="text-center">Tap Authorized QC Employee ID</h5>
            <div class="form-group">
               <input type="text" class="form-control form-control-lg" name="inspected_by" readonly style="text-align: center; font-size: 20pt; font-weight: bolder;" id="inspected-by">
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