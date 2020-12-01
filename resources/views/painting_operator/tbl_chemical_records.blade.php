<div class="row">
   <div class="col-md-12">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" id="pcr-tabs-1" role="tablist" style="display: none;">
         <li class="nav-item active">
            <a class="nav-link" id="tab_pcr" data-toggle="tab" href="#tab-pcr" role="tab" aria-controls="tab_pcr" aria-selected="false"></a>
         </li>
         <li class="nav-item active">
            <a class="nav-link" id="tabsubmit" data-toggle="tab" href="#tabsubmit-pcr" role="tab" aria-controls="tabsubmit" aria-selected="false">Submit</a>
         </li>
       </ul>
       <!-- Tab panes -->
       <form action="/submit_painting_chemical_records" method="POST" id="painting_chemical_records_frm">
          @csrf
          <div class="tab-content" style="min-height: 500px;" id="pcr-tabs">
            <div class="tab-pane active" id="tab-pcr" role="tabpanel" aria-labelledby="tab_pcr">
               <div class="text-white" style="float: left; margin-top: -65px; font-size: 16pt; font-weight: bold;">
                 
               </div>
               <div class="row" style="min-height: 420px;">
                  <div class="col-md-6">
                      <div class="text-center"><h5><b>DEGREASING</h5></b></div>

                      <div class="row">
                        <div class="col-md-6">
                           <div class="form-group text-center">
                              <label for="">Free AKALI 6.5-7.5</label>
                              <input type="text" value="0" class="form-control form-control-lg qty-input" name="deg_freealkali" data-edit="1" data-chemtype="freealkali" id="deg_freealkali" required>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group text-center">
                              <label for="" style="display: inline-block;font-size: 13px;" id="degreasing_add">Increase/Decrease</label><label style="display: inline;font-size: 13px;" id="degreasing_type_label"> Point</label>
                              <input type="hidden" name="degreasing_type_input" id="degreasing_type_input">
                              <input type="text" class="form-control form-control-lg qty-input" name="degreasing_type" data-edit="1" id="degreasing_type" data-chemtype="degreasing_type_val" value="0" required disabled>
                           </div>
                        </div>
                        <style>
                           .qty-input{
                              font-size: 20pt; text-align: center; font-weight: bolder;
                           }
                        </style>
                     </div>
                      <div class="text-center"><h6 style="display: inline-block;">Status:</h6><h6 style="display: inline;padding-left: 10px;" id="stat_degreasing"></h6></div>
                     <div class="text-center" style="padding-top: 10px;"><h5><b>PHOSPATING</b></h5></div>
                     <div class="row">
                        <div class="col-md-6 align-bottom">
                           <div class="form-group text-center">
                              <label for="" style="display: inline-block; line-height: 8px;">PB3100R</label>
                              <label for="replenshing" style="display: inline-block;line-height: 10px;">Replenshing Acid 16-20</label>
                              <input type="text" value="0" class="form-control form-control-lg qty-input" name="replenshing" data-edit="1" id="replenshing" data-chemtype="PB3100R" required>
                           </div>
                        </div>
                        <div class="col-md-6 align-bottom">
                           <div class="form-group text-center" style="padding-top: 18px;">
                              <label for="" style="display: inline-block;font-size: 13px;" id="replenshing_add">Increase/ Decrease</label><label style="display: inline;font-size: 13px;" id="replenshing_type_label"> Point</label>
                              <input type="hidden" name="replenshing_type_input" id="replenshing_type_input">
                              <input type="text" class="form-control form-control-lg qty-input" name="replenshing_type" data-edit="1" id="replenshing_type" data-chemtype="replenshing_type_val" value="0" required disabled>
                           </div>
                        </div>
                        <style>
                           .qty-input{
                              font-size: 20pt; text-align: center; font-weight: bolder;
                           }
                        </style>
                     </div>
                     <div class="text-center"><h6 style="display: inline-block;">Status:</h6><h6 style="display: inline;padding-left: 10px;" id="stat_replenshing"></h6></div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group text-center">
                              <label for="" style="display: inline-block; line-height: 8px;">AC-131</label>
                              <label for="accelerator" style="display: inline-block;line-height: 10px;">Accelerator 6.0-9.0</label>
                              <input type="text" value="0"  data-chemtype="AC-131" class="form-control form-control-lg qty-input" name="accelerator" data-edit="1" id="accelerator" required>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group text-center" style="padding-top: 18px;">
                            <label for="" style="display: inline-block;font-size: 13px;" id="accelerator_add">Increase/ Decrease</label><label style="display: inline;font-size: 13px;" id="accelerator_type_label"> Point</label>
                              <input type="hidden" name="accelerator_type_input" id="accelerator_type_input">
                              <input type="text" class="form-control form-control-lg qty-input" name="accelerator_type" data-edit="1" id="accelerator_type" value="0" data-chemtype="accelerator_type_val" required disabled>
                           </div>
                        </div>
                        <style>
                           .qty-input{
                              font-size: 20pt; text-align: center; font-weight: bolder;
                           }
                        </style>
                     </div>
                     <div class="text-center"><h6 style="display: inline-block;">Status:</h6><h6 style="display: inline;padding-left: 10px;" id="stat_accelerator"></h6></div>
                     
                  </div>
                  <div class="col-md-6" >
                    <label style="padding-top: 20px;">{!! $note !!}</label>
                     <div class="text-center numpad-div" style="padding-top: 50px;">
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
                           <span class="numpad numm decimal">.</span>
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
            <div class="tab-pane" id="tabsubmit-pcr" role="tabpanel" aria-labelledby="tabsubmit">
               <div class="row" style="min-height: 420px;">
                  <div class="col-md-12 text-center" id="pcr-enter-operator">
                     <h5 class="text-center">Scan Authorized Employee ID</h5>
                     <div class="form-group">
                        <input type="password" class="form-control form-control-lg" name="inspected_by" readonly style="text-align: center; font-size: 20pt; font-weight: bolder;margin:0 auto;width:400px;text-align: center;" id="inserted_by">
                     </div>
                     <div class="col-md-12" style="margin-bottom: 10px;margin:0 auto;text-align: center;">
                       <button type="button" class="toggle-manual-input" style="margin-bottom: 10px;margin:0 auto;text-align: center;">Tap here for Manual Entry</button>
                     </div>
                    <div class="col-md-8 offset-md-2">
                      <div class="text-center numpad-div manual col-md-12" style="display: none;">
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '1';">1</span>
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '2';">2</span>
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '3';">3</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '4';">4</span>
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '5';">5</span>
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '6';">6</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '7';">7</span>
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '8';">8</span>
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '9';">9</span>
                        </div>
                        <div class="row1">
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value.slice(0, -1);"><</span>
                           <span class="numpad" onclick="document.getElementById('inserted_by').value=document.getElementById('inserted_by').value + '0';">0</span>
                           <span class="numpad" onclick="document.getElementById('inserted_by').value='';">Clear</span>
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
 <script type="text/javascript">
$('#chemical-records-modal .next-tab').click(function(){
  var stat_acc = $('#stat_accelerator').text();
  var stat_rep = $('#stat_replenshing').text();
  var stat_deg = $('#stat_degreasing').text();
      if(stat_acc == "Good" && stat_rep == "Good" && stat_deg == "Good"){
        
        $('.nav-tabs > .active').next('li').find('a').trigger('click');
      }else{
        showNotification("danger", 'Status must be Good', "now-ui-icons travel_info");
        return false;
      }
  
});

  $('#chemical-records-modal .prevtab').click(function(){
  $('.nav-tabs > .active').prev('li').find('a').trigger('click');
});
 </script>
