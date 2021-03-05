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
               <div class="text-white" style="float: left; margin-top: -65px; font-size: 16pt; font-weight: bold;">
                 
               </div>
               <div class="row" style="min-height: 420px;">
                  <div class="col-md-6">
                      <div class="row">
                          <div class="col-md-12">
                              <div class="form-group text-center">
                                  <input type="text" class="form-control" name="water_date" id="datepairExample_water"  style="margin:0 auto;width:200px;text-align: center;font-size: 17pt;font-weight:bold;">
                              </div>
                          </div>
                 
                          <div class="col-md-12" style="padding-top: 20px;">
                              <div class="form-group text-center">
                                  <label style="font-size: 14px;"><b>Operating Hrs</b></label>
                                  <input type="text" class="form-control" data-edit="1" name="operating_hrs" required id="operating_hrs" style="margin:0 auto;width:200px;text-align: center;font-size: 14pt;" value="{{ $op_hrs }}">
                              </div>
                          </div>
                          <div class="col-md-12" style="padding-top: 60px;">
                              <div class="form-group text-center">
                                  <label style="font-size: 17px;"><b>INCOMING WATER DISCHARGED</b></label>
                                  <input type="text" class="form-control qty-input" name="incoming_water_discharged" required id="incoming_water_discharged" style="margin:0 auto;width:250px;text-align: center;" value="0" readonly>
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
                        <style>
                           .qty-input{
                              font-size: 20pt; text-align: center; font-weight: bolder;
                           }
                        </style>
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
 <script type="text/javascript" src="{{ asset('css/datepicker/jquery.timepicker.js') }}"></script>
 <link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/jquery.timepicker.css') }}" />
 <script type="text/javascript" src="{{ asset('css/datepicker/datepair.js') }}"></script>
 <script type="text/javascript" src="{{ asset('css/datepicker/jquery.datepair.js') }}"></script>
 <script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
 <link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
 <script type="text/javascript">
 var d = new Date();
var ye = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(d);
var mo = new Intl.DateTimeFormat('en', { month: 'short' }).format(d);
var da = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(d);
var datee = `${mo} ${da}, ${ye}`;
    $('#datepairExample_water').val(datee);
$('#water-discharged-modal .next-tab').click(function(){
  $('.nav-tabs > .active').next('li').find('a').trigger('click');
});

  $('#water-discharged-modal .prevtab').click(function(){
  $('.nav-tabs > .active').prev('li').find('a').trigger('click');
});
$('#datepairExample_water').datepicker({
        'format': "M d, yyyy",
        'autoclose': true
    });
 </script>
