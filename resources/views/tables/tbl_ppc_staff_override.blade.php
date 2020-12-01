<!-- Nav tabs -->
<ul class="nav nav-tabss" id="ppc-staff-tabs" role="tablist" style="display: none;">
   {{-- @foreach($data as $r) --}}
   <li class="nav-item">
      <a class="nav-link active" id="{{ $data->id }}-tabs" data-toggle="tabs" href="#over{{ $data->id }}" role="tab" aria-controls="over{{ $data->id }}" aria-selected="true">{{ $data->id }}</a>
   </li>
   {{-- @endforeach --}}
   <li class="nav-item">
      <a class="nav-link" id="confirm-tabs" data-toggle="tabs" href="#confirms" role="tab" aria-controls="confirms" aria-selected="false">Confirmation</a>
   </li>
</ul>
<!-- Tab panes -->
<form action="/confirm_override" method="POST" id="confirm-override-frm">
   @csrf
<div class="tab-content" style="min-height: 300px;">
   {{-- @foreach($data as $r) --}}
   <div class="tab-pane active" id="over{{ $data->id }}" role="tabpanel" aria-labelledby="{{ $data->id }}-tabs">
      <div class="row" style="min-height: 300px;">
         <div class="col-md-6">
            <div class="row">

                  <div class="form-group col-md-6">
                     <label for="completed-qty"><b>Accepted Qty</b></label>
                     <input type="text" class="form-control" id="accepted-qty-override" name="accepted"  placeholder="Enter Accepted" style="font-size: 14pt;" readonly value="{{ $data->qty_accepted }}">
                  </div>
                  <div class="form-group col-md-6">
                     <label for="good-qty"><b>Good Qty</b></label>
                     <input type="text" class="form-control" id="good-qty-override" name="good" placeholder="Enter Good" style="font-size: 14pt;" readonly value="{{ $data->good }}">
                      
                  </div>
                  <div class="form-group col-md-6">
                      <label for="reject-qty"><b>Reject Qty</b></label>
                      <input type="text" class="form-control" id="reject-qty-override" name="reject" placeholder="Enter Reject" style="font-size: 14pt;" readonly value="{{ $data->reject }}">
                   </div>
                  <div class="form-group col-md-6">
                     <label for="rework-qty"><b>Rework Qty</b></label>
                      <input type="text" class="form-control" id="rework-qty-override" name="rework" placeholder="Enter Rework" value="{{ $data->rework }}" readonly style="font-size: 14pt;">
                  </div>
                  <div class="form-group col-md-6" id="sel-dispo">
                     <label><b>Status</b></label>
                     <select class="form-control" style="font-size: 14pt;" name="status">
                        <option value="In Progress" selected>In Progress</option>
                        <option value="Accepted">Restart</option>
                     </select>
                  </div>
            </div>
            <input type="hidden" name="jt_no" value="{{ $data->id }}">
            <input type="hidden" name="from_time" value="{{ $data->from_time }}">

         </div>

         <div class="col-md-6">
                     <div id="override-task-numpad" class="text-center">
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
         <div class="row">
            <div class="col-md-12" id="warning_div" style="padding-left: 30px;">
               <p class="warning-text">WARNING: Authorized User Only; Actual data will be modified. </p>
            </div>                    
         </div>
      </div>

      <div class="row">
         <div class="col-md-6">
            {{-- @if($loop->first) --}}
            <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
            {{-- @else --}}
            {{-- <button type="button" class="btn btn-secondary btn-block btn-lg prevtabs">Previous</button> --}}
            {{-- @endif --}}
         </div>
         <div class="col-md-6">
            <button type="button" class="btn btn-primary btn-block btn-lg nexttabs">Confirm</button>
         </div>
      </div>
   </div>
   {{-- @endforeach --}}
   <div class="tab-pane" id="confirms" role="tabpanel" aria-labelledby="confirm-tabs">
      <div class="row" style="min-height: 350px;">
         
         <div class="col-md-8 offset-md-2 text-center">
            <h5 class="text-center">Tap Authorized PPC Employee ID</h5>
            <div class="row"> 
               <div class="col-md-8 offset-md-2"> 
                  <div class="form-group">
                     <input type="text" class="form-control form-control-lg" name="inspected_by" readonly style="text-align: center; font-size: 20pt; font-weight: bolder;" id="inspected-bys">
                  </div>
                  <button type="button" class="toggle-manual-override" style="margin-bottom: 10px;">Tap here for Manual Entry</button>
               </div> 
            </div> 
            <div class="text-center numpad-div manuals" style="display: none;">
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '1';">1</span>
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '2';">2</span>
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '3';">3</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '4';">4</span>
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '5';">5</span>
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '6';">6</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '7';">7</span>
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '8';">8</span>
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '9';">9</span>
               </div>
               <div class="row1">
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value.slice(0, -1);"><</span>
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value=document.getElementById('inspected-bys').value + '0';">0</span>
                  <span class="numpad" onclick="document.getElementById('inspected-bys').value='';">Clear</span>
               </div>
            </div>
            <img src="{{ asset('img/tap.gif') }}" style="margin-top: -20px; width: 60%;" />
         </div>
      </div>

      <div class="row">
         <div class="col-md-6">
            <button type="button" class="btn btn-secondary btn-block btn-lg prevtabs">Previous</button>
         </div>
         <div class="col-md-6">
            <button type="submit" class="btn btn-info btn-block btn-lg">Submit</button>
         </div>
      </div>
   </div>
</div>
</form>



<script type="text/javascript">
      $('#override-task-numpad .num').on('click', function() {
      var route =  "#" + focused_input;
      $(route).focus();
      var x = $(route).val();
      var y = $(this).text();

      if (x == 0) {
        x = '';
      }
      var me= x + y;
      $(route).val(x + y);
    });
       var focused_input = null;
    $("#confirm-override-frm input[type=text]" ).focus(function() {
      focused_input = $(this).attr('id');
    });
</script>
<script type="text/javascript">
       $('#override-task-numpad .clear').on('click', function() {
      $("#" + focused_input).focus();
      var x = "0";
      $("#" + focused_input).val(x);
    });
</script>
<script type="text/javascript">
       $('#override-task-numpad .del').on('click', function() {
      $("#" + focused_input).focus();
      var x = $("#" + focused_input).val();
      

      $("#" + focused_input).val(x.substring(0, x.length - 1));

      if ($("#" + focused_input).val().length == 0) {
        $("#" + focused_input).val('0');
      }
    });
</script>