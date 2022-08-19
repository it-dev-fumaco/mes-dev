<div class="row">
   <div class="col-md-12">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" id="prm-tabs-1" role="tablist" style="display: none;">
         <li class="nav-item active">
            <a class="nav-link" id="tab_prm" data-toggle="tab" href="#tab-prm" role="tab" aria-controls="tab_prm" aria-selected="false"></a>
         </li>
         <li class="nav-item active">
            <a class="nav-link" id="tabsubmit" data-toggle="tab" href="#tabsubmit-prm" role="tab" aria-controls="tabsubmit" aria-selected="false">Submit</a>
         </li>
       </ul>
       <!-- Tab panes -->
       <form action="/submit_powder_record_monitoring" method="POST" id="powder_coating_monitoring_frm">
          @csrf
          <div class="tab-content" style="min-height: 500px;" id="prm-tabs">
            <div class="tab-pane active" id="tab-prm" role="tabpanel" aria-labelledby="tab_prm">
               <div class="text-white" style="float: left; margin-top: -65px; font-size: 16pt; font-weight: bold;">
                 
               </div>
               <div class="row" style="min-height: 420px;">
                  <div class="col-md-8" style="padding: 7px 5px 6px 12px;">
                        <div class="row">
                          <div class="col-md-12">
                              <div class="form-group text-left">
                                  <b><span class="date_today font-weight-bold" style="font-size: 16px;padding-left: 10px;">{{ $date_today }}</span></b>
                              </div>
                           </div>
                          
                          <div class="col-md-8">
                              <div class="form-group row text-center">
                                  <label for="operating_hrs" class="col-sm-3 col-form-label" style="font-size: 13px;display:inline;"><b>Shift:</b></label>
                                  <div class="col-sm-8">
                                       <select class="form-control" name="operating_hrs" id="operating_hrs">
                                          <option value="none">Select Shift</option>
                                          @foreach($shift as $row)
                                          <option value="{{$row->shift_id}}"> {{$row->shift_type}}: {{$row->time_in}}-{{$row->time_out}}</option>
                                          @endforeach
                                       </select>
                                  </div>
                              </div>
                           </div>
                           <div class="col-md-3 offset-md-9 p-1 text-right" style="margin-top: -93px;">
                              <button type="button" class="btn btn-default btn-stock-adjust-entry-painting m-0 add-row">+ Select Item</button>
                           </div>
                      </div>
                      <div class="row" style="padding: 7px 5px 6px 12px;">
                      <div class="col-md-12" style="padding: 7px 5px 6px 12px;">
                        <table class="table" id="additem-table" style="font-size: 10px;padding: 7px 5px 6px 12px;">
                           <col style="width: 5%;">
                           <col style="width: 16%;">
                           <col style="width: 16%;">
                           <col style="width: 16%;">
                           <col style="width: 16%;">
                           <col style="width: 16%;">
                           <col style="width: 5%;">
                              <thead>
                                 <tr>
                                    <th style="width: 5%; text-align: center;font-weight: bold;">No.</th>
                                    <th style="width: 16%; text-align: center;font-weight: bold;" id="th_checklist">Item Code:</th>
                                    <th style="width: 16%; text-align: center;font-weight: bold;">Description</th>
                                    <th style="width: 16%; text-align: center;font-weight: bold;" id="th_responsible">Current Qty</th>
                                    <th style="width: 16%; text-align: center;font-weight: bold;" id="th_action">Consumed Qty</th>
                                    <th style="width: 16%; text-align: center;font-weight: bold;" id="th_action">Balance Qty</th>
                                    <th style="width: 5%; text-align: center;font-weight: bold;"></th>
                                 </tr>
                              </thead>
                              <tbody class="table-body text-center" style="font-size:11px;">
                              </tbody>
                         </table>
                      </div>
                      </div>
                     
                  </div>
                  
                  <div class="col-md-4" style="padding: 7px 5px 6px 12px;">
                     <div class="row" style="padding: 7px 5px 6px 12px;">
                       {{--<div class="col-md-4">
                           <div class="form-group text-center">
                              <label for="">Previous</label>
                              <input type="text" class="form-control form-control-lg qty-input qty-powder" name="previous_inputs" data-edit="0" value="" id="previous_input" readonly>
                           </div>
                        </div>
                        
                        <div class="col-md-4">
                           <div class="form-group text-center">
                              <label for="">Consumed Ty</label>
                              <input type="text" class="form-control form-control-lg qty-input qty-powder" name="present_inputs" data-edit="1" id="present_input_qty" value="0" required>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <div class="form-group text-center">
                              <label for="">Available Qty</label>
                              <input type="text" class="form-control form-control-lg qty-input qty-powder" name="availbale_qty" data-edit="0" id="availbale_qty" value="0" readonly>
                           </div>
                        </div>--}}
                        <style>
                           .qty-input{
                              font-size: 20pt; text-align: center; font-weight: bolder;
                           }
                        </style>
                     </div>
                     <div class="text-center numpad-div" style="margin-top:50px;">
                        <div class="row1">
                           <span class="numpad numm">1</span>
                           <span class="numpad numm">2</span>
                           <span class="numpad numm">3</span>
                        </div>
                        <div class="row1">
                           <span class="numpad numm">4</span>
                           <span class="numpad numm">5</span>
                           <span class="numpad numm">6</span>
                        </div>
                        <div class="row1">
                           <span class="numpad numm">7</span>
                           <span class="numpad numm">8</span>
                           <span class="numpad numm">9</span>
                        </div>
                        <div class="row1">
                           <span class="numpad del"><</span>
                           <span class="numpad numm">0</span>
                           <span class="numpad decimal">.</span>
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
            <div class="tab-pane" id="tabsubmit-prm" role="tabpanel" aria-labelledby="tabsubmit">
               <div class="row" style="min-height: 420px;">
                  <div class="col-md-12 text-center" id="prm-enter-operator">
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
                           {{-- <span class="numpad num-test">1</span> --}}
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
 .select2-selection__rendered {
    line-height: 31px !important;
}
.select2-container .select2-selection--single {
    height: 37px !important;
}
.select2-selection__arrow {
    height: 37px !important;
}
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


  $('#powder-record-modal .prevtab').click(function(){
  $('.nav-tabs > .active').prev('li').find('a').trigger('click');
});
 </script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
<script>
  $(document).ready(function(){
    $('.sel2').select2({
    dropdownParent: $("#powder-record-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  
  });
</script>
<script>

   // $('.num-test').click(function (){
   //    var num = $(this).text();
   //    $('#inspected-by').val( $('#inspected-by').val() + (num) );
   // });

    $('#powder-record-modal .next-tab').click(function(){
      
      var item = $('.item_code_selection').val();
      var qty = $('#present_input_qty').val();
      var op = $('#operating_hrs').val();
      var current = $('.select-input').val();
      console.log(qty);
      if( item == "none" ){
        showNotification("danger", 'Pls Select Power Coat Variant');
        return false;
      }else if(qty <= 0){

        showNotification("danger", 'Qty must be greater than 0');
        return false;
      }else if(op == "none"){

      showNotification("danger", 'Please Select Shift');
      return false;
      }else if(current == "0" || current == ""){

      showNotification("danger", 'Please check Item with No Stock');
      return false;
      }else{
        $('.nav-tabs > .active').next('li').find('a').trigger('click');

      }

  
});
</script>
<script type="text/javascript">
    $('#powder-record-modal .add-row').click(function(e){
         e.preventDefault();
         var row = '';
         $.ajax({
            url: "/get_powder_coat_item",
            type:"get",
            cache: false,
            success: function(response) {
               row += '<option value="none">--Select Item Code--</option>';
               $.each(response, function(i, d){
                  row += '<option value="' + d.name + '">' + d.name + '</option>';
               });
               var thizz = document.getElementById('additem-table');
              var id = $(thizz).closest('table').find('tr:last td:first').text();
               var validation = isNaN(parseFloat(id));
               if(validation){
                var new_id = 1;
               }else{
                var new_id = parseInt(id) + 1;
               }
              //  alert(new_id);
               var len2 = new_id;
               var id_unique="desc"+len2;
               var current_uniq="current"+len2;
               var comsump_uniq="comsump"+len2;
               var bal_uniq="bal"+len2;
               
               // alert(id_unique);
               var tblrow = '<tr>' +
                  '<td>'+len2+'</td>' +
                  '<td style="font-size:16px;"><select name="item_code[]" class="form-control onchange-selection-item_code count-row"  data-idcolumn='+id_unique+' data-id='+len2+' required>'+row+'</select></td>' +
                  '<td style="font-size:12px;" ><label id='+id_unique+'></label></td>' +
                  '<td><input type="text"   style="display:inline-block;width:75px;font-size:20px;" class="form-control select-input" data-edit="0" data-id='+len2+'  name="current[]" required id='+current_uniq+' readonly><span style="display:inline;vertical-align: middle;padding-left:2px; font-size:12px;"><b>KG</b></span></td></td>' +
                  '<td><input type="text"  style="display:inline-block;width:75px;font-size:20px;" class="form-control select-input" data-vali="consumption" value="0" data-edit="1" data-id='+len2+'  name="consum[]" id='+comsump_uniq+' style="background-color:white;" readonly required><span style="display:inline;vertical-align: middle;padding-left:2px; font-size:12px;"><b>KG</b></span></td></td>' +
                  '<td><input type="text" style="display:inline-block;width:75px;font-size:20px;" class="form-control select-input balance" data-edit="0" data-id='+len2+'  name="bal[]" id='+bal_uniq+' required readonly><span style="display:inline;vertical-align: middle;padding-left:2px; font-size:12px;"><b>KG</b></span></td></td>' +
                  '<td class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></td>' +
                  '</tr>';

               $("#powder_coating_monitoring_frm #additem-table").append(tblrow);
               // autoRowNumberAddKPI();
            },
            error: function(response) {
               alert('Error fetching Designation!');
            }
         });
      });
      $(document).on('change', '.onchange-selection-item_code ', function(){
           var val = $(this).val();
           var id_for_second_selection = $(this).attr('data-idcolumn');
           var id = $(this).attr('data-id');
           var current = "#current"+id;
           var comsump = "#comsump"+id;
           var bal = "#bal"+id;
         //   alert(current);
           var format_id_for_second_selection = "#"+id_for_second_selection;
           $.ajax({
            url:"/get_powder_coat_desc/"+ val,
            type:"GET",
            success:function(data){
               if (data.success < 1) {
               showNotification("danger", data.message, "now-ui-icons travel_info");
               $(format_id_for_second_selection).text(data.desc);
               $(current).val("0");
               $(comsump).val("0");
               $(bal).val("");
               }else{
               $(format_id_for_second_selection).text(data.desc);
               $(current).val(data.current);
               $(comsump).val("0");
               $(bal).val("");

               }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
      });
      $(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();
      });
</script>