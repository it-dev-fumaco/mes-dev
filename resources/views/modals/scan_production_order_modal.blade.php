{{-- <!-- Modal Scan Production Order --> --}}
<div class="modal fade" id="scan-production-order-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
         <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title">Scan Production Order [<b>@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif</b>]</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            <input type="hidden" id="workstation" value="@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif">
         </div>
         <div class="modal-body" style="min-height: 480px;">
            <div class="row">
               <div class="col-md-12">
                  <div class="row" id="enter-production-order">
                     <div class="col-md-10 offset-md-1">
                        <h6 class="text-center">Scan your Job Ticket</h6>
                        <div class="row">
                           <div class="col-md-10">
                              <div class="form-group">
                                 <div class="input-group">
                                    <div class="input-group-prepend">
                                       <div class="input-group-text">PROM-</div>
                                    </div>
                                    <input type="text" class="form-control" id="production-order" style="font-size: 15pt;" required>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-2" style="padding: 0; margin-top: -15px;">
                              <center>
                                 <img src="{{ asset('img/tap.gif') }}" width="260" height="60" id="toggle-jt-numpad">
                              </center>
                           </div>
                        </div>
                       
                        <div id="jt-numpad" style="display: none;">
                        <div class="text-center">
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
                              <span class="numpad num">-</span>
                              <span class="numpad num">0</span>
                              <span class="numpad" onclick="document.getElementById('production-order').value=document.getElementById('production-order').value.slice(0, -1);"><</span>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <button type="button" class="btn btn-block btn-primary btn-lg submit-enter-production-order">SUBMIT</button>
                           </div>
                        </div>
                        </div>
                        <div id="jt-scan-img">
                           <center>
                              <img src="{{ asset('img/scan-barcode.png') }}" width="220" height="240" style="margin: 40px 10px 10px 10px;">
                           </center>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

{{-- <!-- Modal Production Order Select Process --> --}}
<div class="modal fade" id="scan-production-order-step1-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
   <div class="modal-dialog" role="document" style="min-width: 90%;">
      <div class="modal-content">
         <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title"><b>@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif</b> - <span class="production-order"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            <input type="hidden" id="workstation" value="@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif">
         </div>
         <div class="modal-body" style="min-height: 500px;">
            <div class="row" id="step1-row" hidden>
               <div class="col-md-12">
                  <table style="width: 100%; border: 1px solid #EBEDEF;">
                     <col style="width: 20%;">
                     <col style="width: 60%;">
                     <col style="width: 20%;">
                     <tr class="text-center" style="background-color: #EBEDEF;">
                        <td>Reference No.</td>
                        <td>Item Details</td>
                        <td>Qty to Manufacture</td>
                     </tr>
                     <tr>
                        <td class="text-center" style=" border: 1px solid #EBEDEF;">
                           <span style="font-size: 13pt; display: block; font-weight: bold;" class="ref-no"></span>
                           <span style="font-size: 10pt; display: block;" class="customer"></span>
                        </td>
                        <td style=" border: 1px solid #EBEDEF;">
                           <span style="font-size: 13pt; display: block; font-weight: bold;" class="item-code"></span>
                           <span style="font-size: 10pt; display: block; color: #17202A;" class="description"></span>
                        </td>
                        <td class="text-center" style=" border: 1px solid #EBEDEF;">
                           <span style="font-size: 18pt;display: block; font-weight: bold;" class="qty"></span>
                           <span style="font-size: 10pt;display: block;" class="stock-uom"></span>
                        </td>                        
                     </tr>
                  </table>
               </div>
            </div>
            <div class="row" style="margin-top: 10px;">
               <div class="col-md-12">
                  <h5 class="title text-center">Select Process</h5>
               </div>
            </div>
            {{--  <div class="row" id="process-list-div" style="margin-top: 10px;"></div>  --}}
            <ul class="steps steps-5" id="process-list-div" style="display:table; margin:0 auto;"></ul>
            <style>
                  .steps {
                    margin: 0;
                    padding: 0;
                    /*overflow: hidden;*/
                  }
           
                  .steps em {
                    display: block;
                    font-size: 1.1em;
                    font-weight: bold;
                  }
                  .steps li {
                    float: left;
                    margin-left: 25px;
                    margin-bottom: 10px;
                    width: 250px; /* 100 / number of steps */
                    height: 100px; /* total height */
                    list-style-type: none;
                    padding: 5px 5px 5px 30px; /* padding around text, last should include arrow width */
                    border-right: 3px solid white; /* width: gap between arrows, color: background of document */
                    position: relative;
                  }
                  /* remove extra padding on the first object since it doesn't have an arrow to the left */
                  /* .steps li:first-child {
                    padding-left: 5px;
                  } */
                  /* white arrow to the left to "erase" background (starting from the 2nd object) */
                  .steps li:nth-child(n+1)::before {
                    position: absolute;
                    top:0;
                    left:0;
                    display: block;
                    border-left: 25px solid white; /* width: arrow width, color: background of document */
                    border-top: 50px solid transparent; /* width: half height */
                    border-bottom: 50px solid transparent; /* width: half height */
                    width: 0;
                    height: 0;
                    content: " ";
                  }
                  /* colored arrow to the right */
                  .steps li::after {
                    z-index: 1; /* need to bring this above the next item */
                    position: absolute;
                    top: 0;
                    right: -25px; /* arrow width (negated) */
                    display: block;
                    border-left: 25px solid #7c8437; /* width: arrow width */
                    border-top: 50px solid transparent; /* width: half height */
                    border-bottom: 50px solid transparent; /* width: half height */
                    width:0;
                    height:0;
                    content: " ";
                  }
                  
                  /* Setup colors (both the background and the arrow) */
                  .steps li.in_progress { background-color: #D68910; }
                  .steps li.in_progress::after { border-left-color: #D68910; }

                  .steps li.completed { background-color: #28B463; }
                  .steps li.completed::after { border-left-color: #28B463; }

                  .steps li.pending { background-color: #C0392B; }
                  .steps li.pending::after { border-left-color: #C0392B; }
            </style>
         </div>
      </div>
   </div>
</div>

<!-- Modal Production Order Select Machine -->
<div class="modal fade" id="scan-production-order-step2-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
   <div class="modal-dialog" role="document" style="min-width: 90%;">
      <div class="modal-content">
         <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title"><b>@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif</b> - <span class="production-order"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            <input type="hidden" id="workstation" value="@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif">
         </div>
         <div class="modal-body" style="min-height: 480px;">
            <div class="row" id="step2-row" hidden></div>
            <div class="row" style="margin-top: 10px;">
               <div class="col-md-12">
                  <h5 class="title text-center">Select Machine to Start</h5>
               </div>
            </div>
            <div class="row" id="machine-list-div" style="margin-top: 10px;"></div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <h5 class="text-black mt-5">Scan Machine ID</h5>
                    <img src="{{ asset('img/tap.gif') }}" style="margin-top: -50px;"  width="300" height="200"/>
                </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Modal Production Order Enter Operator ID -->
<div class="modal fade" id="scan-production-order-step3-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title"><b>@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif</b> - <span class="production-order"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
            <input type="hidden" id="workstation" value="@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif">
         </div>
         <div class="modal-body" style="min-height: 480px;">
            <form>
               @csrf

               <input type="hidden" name="scrap_id" value="0">
               <input type="hidden" name="workstation">
               
            <input type="hidden" name="production_order">
            <input type="hidden" name="process_id">
            <input type="hidden" name="machine_code">
            <div class="row" id="step3-row" hidden></div>
            <div class="row">
               <div class="col-md-12">
                  <div class="row" id="enter-operator-id">
                     <div class="col-md-10 offset-md-1">
                        <h6 class="text-center">Scan your Operator ID</h6>
                        <div class="row">
                           <div class="col-md-10">
                              <div class="form-group">
                                 <input type="text" class="form-control" id="operator-id" name="operator_id" style="font-size: 15pt;" required>
                              </div>
                           </div>
                           <div class="col-md-2" style="padding: 0; margin-top: -15px;">
                              <center>
                                 <img src="{{ asset('img/tap.gif') }}" width="260" height="60" id="toggle-operator-numpad">
                              </center>
                           </div>
                        </div>
                        <div id="operator-numpad" style="display: none;">
                        <div class="text-center">
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
                              <span class="numpad" onclick="document.getElementById('operator-id').value=document.getElementById('operator-id').value.slice(0, -1);"><</span>
                              <span class="numpad num">0</span>
                              <span class="numpad" onclick="document.getElementById('operator-id').value='';">Clear</span>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-10 offset-md-1">
                              <button type="button" class="btn btn-block btn-primary btn-lg submit-enter-operator-id">SUBMIT</button>
                           </div>
                        </div>
                        </div>
                        <div id="operator-scan-img">
                           <center>
                              <img src="{{ asset('img/operator-id.png') }}" width="280" height="200" style="margin: 40px 10px 10px 10px;">
                           </center>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </form>
         </div>
      </div>
   </div>
</div>