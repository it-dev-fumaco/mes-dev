<!-- Nav tabs -->
<ul class="nav nav-tabss" id="ppc-transfer-tabs" role="tablist" style="display: none;">
   {{-- @foreach($data as $r) --}}
   <li class="nav-item">
      <a class="nav-link active" id="{{ $data->id }}-tabs" data-toggle="tabs" href="#trans{{ $data->id }}" role="tab" aria-controls="trans{{ $data->id }}" aria-selected="true">{{ $data->id }}</a>
   </li>
   {{-- @endforeach --}}
   <li class="nav-item">
      <a class="nav-link" id="confirm-tabs" data-toggle="tabs" href="#confirms" role="tab" aria-controls="confirms" aria-selected="false">Confirmation</a>
   </li>
</ul>
<!-- Tab panes -->
<form action="/confirmTransferTask" method="POST" id="confirm-transfer-frm" class="confirm-transfer-frm">
   @csrf
<div class="tab-content" style="min-height: 300px;">
   {{-- @foreach($data as $r) --}}
   <div class="tab-pane active" id="trans{{ $data->id }}" role="tabpanel" aria-labelledby="{{ $data->id }}-tabs">
      <div class="row" style="min-height: 300px; font-size: 15pt;">
         <div class="col-md-12">

                     <label for="machine_code_selection"><b>Machine Assigned</b></label>
                     <select name="machine_id" class="form-control" id="machine_code_selection" onchange="getAssignProcessinMachine()">
                           @foreach($machine_for_workstation as $row)
                           <option value="{{ $row->machine_id }}" {{ ($data->machine == $row->machine_code) ? 'selected': '' }} >{{ $row->machine_code }} - {{ $row->machine_name}}
                           </option>
                           @endforeach
                           </select>
                     <label for="process_selection"><b>Process Assigned</b></label>
                     <select name="process_id" class="form-control" id="process_selection">
                           <option value="">
                           </option>
                     </select>
            <input type="hidden" name="jt_no" value="{{ $data->id }}">
            <input type="hidden" name="from_time" value="{{ $data->from_time }}">

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
                  <button type="button" class="toggle-manual-transfer" style="margin-bottom: 10px;">Tap here for Manual Entry</button>
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
      $('#transfer-task-numpad .num').on('click', function() {
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
    $("#confirm-transfer-frm input[type=text]" ).focus(function() {
      focused_input = $(this).attr('id');
    });
</script>
<script type="text/javascript">
       $('#transfer-task-numpad .clear').on('click', function() {
      $("#" + focused_input).focus();
      var x = "0";
      $("#" + focused_input).val(x);
    });
</script>
<script type="text/javascript">
       $('#transfer-task-numpad .del').on('click', function() {
      $("#" + focused_input).focus();
      var x = $("#" + focused_input).val();
      

      $("#" + focused_input).val(x.substring(0, x.length - 1));

      if ($("#" + focused_input).val().length == 0) {
        $("#" + focused_input).val('0');
      }
    });
</script>
<script type="text/javascript">

     $('#confirm-transfer-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#transfer-task-to-machine-modal').modal('hide');
            $('#transfer-search-modal').modal('hide');
          }
        }
      });
    });
</script>
<script type="text/javascript">
   function showNotification(color, message, icon){
        $.notify({
          icon: icon,
          message: message
        },{
          type: color,
          timer: 300,
          placement: {
            from: 'top',
            align: 'center'
          }
        });
      }
</script>
