<div class="row">
   <div class="col-md-12">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" id="wdm-tabs-1" role="tablist" style="display: none;">
         <li class="nav-item active">
            <a class="nav-link" id="tab_wdm" data-toggle="tab" href="#tab-wdm" role="tab" aria-controls="tab_wdm" aria-selected="false"></a>
         </li>
         <li class="nav-item active">
            <a class="nav-link" id="tabsubmit" data-toggle="tab" href="#tabsubmit-wdm" role="tab" aria-controls="tabsubmit" aria-selected="false">Submit</a>
         </li>
      </ul>
      <!-- Tab panes -->
      <form action="/submit_water_discharge_monitoring" method="POST" id="water_discharge_monitoring_frm">
         @csrf
         <div class="tab-content" style="min-height: 500px;" id="wdm-tabs">
            <div class="tab-pane active" id="tab-wdm" role="tabpanel" aria-labelledby="tab_wdm">
               <div class="row" style="min-height: 420px;">
                  <div class="col-md-6">
                     <div class="row mt-4">
                        <div class="col-md-10 offset-md-1">
                           <div class="form-group text-center">
                              <label class="font-weight-bold text-uppercase">Transaction Date</label>
                              <input type="text" class="form-control m-0 rounded-0" name="water_date" id="datepairExample_water" style="text-align: center; font-size: 15pt;" value="{{ $formatted_transaction_date }}" readonly>
                           </div>
                        </div>
                        <div class="col-md-10 offset-md-1 mt-3">
                           <div class="form-group text-center">
                              <label class="font-weight-bold text-uppercase">Operating Hours</label>
                              <input type="text" class="form-control m-0 rounded-0" data-edit="1" name="operating_hrs" required id="operating_hrs" style="text-align: center; font-size: 14pt;" value="{{ $op_hrs }}">
                           </div>
                        </div>
                        <div class="col-md-10 offset-md-1 mt-5">
                           <div class="form-group text-center">
                              <label class="font-weight-bold text-uppercase">Incoming Water Discharged</label>
                              <input type="text" class="form-control qty-input m-0 rounded-0" name="incoming_water_discharged" required id="incoming_water_discharged" style="text-align: center;" value="0" readonly>
                              <label style="font-size: 17px;"><b>cm<sup>3</sup></b></label>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group text-center">
                              <label for="">Previous</label>
                              <input type="text" class="form-control form-control-lg qty-input" name="previous_inputs" data-edit="0" value="{{ $previous_wd }}" id="previous_input" readonly>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group text-center">
                              <label for="">Present</label>
                              <input type="text" class="form-control form-control-lg qty-input" name="present_inputs" data-edit="1" id="present_input" value="0" required>
                           </div>
                        </div>
                     </div>
                     <div class="text-center numpad-div">
                        <div class="row1">
                           <span class="numpad numm num">1</span>
                           <span class="numpad numm num">2</span>
                           <span class="numpad numm num">3</span>
                        </div>
                        <div class="row1">
                           <span class="numpad numm num">4</span>
                           <span class="numpad numm num">5</span>
                           <span class="numpad numm num">6</span>
                        </div>
                        <div class="row1">
                           <span class="numpad numm num">7</span>
                           <span class="numpad numm num">8</span>
                           <span class="numpad numm num">9</span>
                        </div>
                        <div class="row1">
                           <span class="numpad numm del"><</span>
                           <span class="numpad numm num">0</span>
                           <span class="numpad numm clear">Clear</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
                  </div>
                  <div class="col-md-6">
                     <button type="button" class="btn btn-primary btn-block btn-lg next-tab">Next</button>
                  </div>
               </div>
            </div>
            <div class="tab-pane" id="tabsubmit-wdm" role="tabpanel" aria-labelledby="tabsubmit">
               <div class="row" style="min-height: 420px;">
                  <div class="col-md-12 text-center" id="wdm-enter-operator">
                     <h5 class="text-center">Scan Authorized Employee ID</h5>
                     <div class="form-group">
                        <input type="password" class="form-control form-control-lg" name="inspected_by" readonly style="text-align: center; font-size: 20pt; font-weight: bolder;margin:0 auto;width:400px;text-align: center;" id="inspected-by">
                     </div>
                     <div class="col-md-12" style="margin-bottom: 10px;margin:0 auto;text-align: center;">
                       <button type="button" class="toggle-manual-input" style="margin-bottom: 10px;margin:0 auto;text-align: center;">Tap here for Manual Entry</button>
                     </div>
                     <div class="col-md-8 offset-md-2">
                        <div class="text-center numpad-div manual col-md-12" style="display: none;">
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
                     </div>
                     <img src="{{ asset('img/tap.gif') }}" style="margin-top: -20px; width: 60%;" />
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-block btn-lg prevtab">Previous</button>
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
      .qty-input{
         font-size: 20pt; text-align: center; font-weight: bolder;
      }
      .datepicker { font-size: 15pt !important; }
      .datepicker .day{ padding: 15px !important;}
      .datepicker .datepicker-switch{ padding: 10px !important;}
   </style>

   <script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
   <link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
   <script type="text/javascript">
      $('#water-discharged-modal .next-tab').click(function(){
         $('.nav-tabs > .active').next('li').find('a').trigger('click');
      });

      $('#water-discharged-modal .prevtab').click(function(){
         $('.nav-tabs > .active').prev('li').find('a').trigger('click');
      });

      $('#datepairExample_water').datepicker({
         'format': "M d, yyyy",
         'autoclose': true,
      }).on('changeDate', function(e) {
         get_water_discharged_modal_details(e.format());
      });

      function get_water_discharged_modal_details(date){
         $.ajax({
            url:"/get_water_discharged_modal_details",
            type:"GET",
            data: {transaction_date: date},
            success:function(response){
              $('#water_discharged_div').html(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            },
         }); 
      }
   </script>
