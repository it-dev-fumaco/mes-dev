@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'production_planning_assembly',
    'pageHeader' => 'Planning Wizard - Item w/o BOM',
   'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
   <div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
      <div class="col-md-12 m-0 p-0">
         <div class="card">
            <div class="card-body">
               <ul class="nav nav-tabs d-none" id="myTab" role="tablist">
                  <li class="nav-item">
                     <a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1" role="tab" aria-controls="step1" aria-selected="true">Get Order Details</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="step2-tab" data-toggle="tab" href="#step2" role="tab" aria-controls="step2" aria-selected="false">Create Withdrawal Slip</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="step3-tab" data-toggle="tab" href="#step3" role="tab" aria-controls="step3" aria-selected="false">Update Process</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="step4-tab" data-toggle="tab" href="#step4" role="tab" aria-controls="step4" aria-selected="false">Summary</a>
                  </li>
               </ul>
               <!-- Tab panes -->
               <div class="tab-content" style="min-height: 620px;">
                  <div class="tab-pane active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                     <div>
                        <a href="/planning_wizard/no_bom" class="btn btn-primary">Wizard - Item without BOM</a>
                        <a href="/assembly/wizard" class="btn btn-secondary">Wizard - Item with BOM</a>
                     </div>
                     <h4 class="title text-center wizard-h4">1. Select Sales Order / Material Request</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-7 mx-auto">
                           <form id="get-somr-frm">
                              <table style="width: 100%;">
                                 <col style="width: 20%;">
                                 <col style="width: 25%;">
                                 <col style="width: 17%;">
                                 <col style="width: 27%;">
                                 <col style="width: 11%;">
                                 <tr>
                                    <td class="text-right align-middle">
                                       <span style="margin-right: 10px; white-space: nowrap"><b>Get Items From:</b></span>
                                    </td>
                                    <td class="align-middle">
                                       <div class="form-group" style="margin: 0;">
                                          <select class="form-control form-control-lg" id="items-from">
                                             <option value="Sales Order">Sales Order</option>
                                             <option value="Material Request">Material Request</option>
                                          </select>
                                       </div>
                                    </td>
                                    <td class="text-right align-middle">
                                       <span style="margin-right: 10px; font-weight: bold; white-space: nowrap" id="reference-name">Sales Order</span>:
                                    </td>
                                    <td class="align-middle">
                                       <div class="form-group" style="margin: 0;">
                                          <input type="text" id="reference-no" class="form-control form-control-lg" value="SO-" autocomplete="off">
                                       </div>
                                    </td>
                                    <td class="align-middle">
                                       <button type="submit" class="btn btn-primary" id="get-btn" style="margin-left: 10px;">Get</button>
                                    </td>
                                 </tr>
                              </table>
                           </form>
                        </div>
                        <div class="col-12">
                           <div id="so-item-list-div"></div>
                        </div>
                     </div>
                     <div class="row btn-div" hidden>
                        <div class="col-12">
                           <div class="pull-right">
                              <button id="get-parts" class="btn btn-lg btn-primary">Next</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">2. Create Item Withdrawal Slip</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-12">
                           <div class="so-details-div"></div>
                           <div id="material-planning-div"></div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-12">
                           <div class="pull-right">
                              <button class="btn btn-lg btn-primary prev-btn" data-target='a1'>Previous</button>
                              <button class="btn btn-lg btn-primary get-parts-prodorder">Next</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="step3" role="tabpanel" aria-labelledby="step3-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">3. Enter Workstation / Process</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-12">
                           <div class="so-details-div"></div>
                           <div id="view-operations-div"></div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-12">
                           <div class="pull-right">
                              <button class="btn btn-lg btn-primary prev-btn" id="create-prod-prev-btn" data-target='a2'>Previous</button>
                              <button class="btn btn-lg btn-primary finish-btn">Finish</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">4. Production Planning Summary</h4>
                     <div style="right: 120px; margin: -40px 50px 10px 10px; padding: 5px; position: absolute;">
                        <a href="#" id="print-withdrawal-slip-btn" class="btn btn-primary">
                           Print Withdrawal Slip
                        </a>
                     </div>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-12" id="last-tab">
                           <div class="so-details-div"></div>
                           <div id="planning-summary-div"></div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-12">
                           <div class="pull-right">
                              <button class="btn btn-lg btn-primary prev-btn" data-target='a3'>Previous</button>
                              <button class="btn btn-lg btn-primary close-wizard-btn">Close</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

<iframe id="iframe-print" style="display: none;"></iframe>

<!-- Modal -->
<div class="modal fade" id="review-bom-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="min-width: 70%;">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bolder;">Modal Title</h5>
            <span style="display: none;" id="bom-idx"></span>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div id="production-order-wizard-nobom" class="d-none"></div>
            <div id="review-bom-details-div"></div>
         </div>
      </div>
   </div>
</div>

<div class="modal fade bd-example-modal-lg" id="print_modal_js_ws" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="min-width:60%; width:60%;">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #ffffff;">
          <h5 class="modal-title">
          <img src="{{ asset('img/preview.png') }}" width="40">
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="printdiv" style="height:600px;overflow-y:auto;" >
          <div id="printmodalbody" style='page-break-after:always;'></div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          <button id="btnPrint" type="button" class="btn btn-primary">Print</button>
        </div>
      </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="create-batch-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bolder;">Create Batch</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-6 offset-md-3">
                  <div class="form-group">
                     <label>Batch Qty</label>
                     <input type="number" class="form-control form-control-lg" id="batch-qty">
                     <div class="d-none">
                        <input type="number" class="form-control form-control-lg" id="planned-qty">
                        <input type="text" class="form-control form-control-lg" id="orig-row">
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary submit-batch-qty">Submit</button>
         </div>
      </div>
   </div>
</div>
 <style>
   #autocomplete-box {
      position:absolute;
      width:95%;
      display:none;
      overflow:hidden;
      border:1px #CCC solid;
      background-color: white;
      display: block;
      z-index: 11;
   }
</style>

@include('assembly_wizard.modal_change_raw_material')
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{  asset('js/printThis.js') }}"></script>

<script>
   $(document).ready(function(){
      $('body').click(function () {
         $("#autocomplete-box").hide();
      });

      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });

      $(document).on('click', '.prod-view-btn', function(e){
         e.preventDefault();
         var jtno = $(this).text();
         $('#jt-workstations-modal .modal-title').text(jtno);
         if(jtno){
            getJtDetails(jtno);
         }else{
            showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
         }
      });

      function getJtDetails(jtno){
         $('#process-bc').empty();
         $('#jt-details-tbl tbody').empty();
         $.ajax({
            url:"/get_jt_details/" + jtno,
            type:"GET",
            success:function(data){
               if (data.success < 1) {
                  showNotification("danger", data.message, "now-ui-icons travel_info");
               }else{
                  $('#production-search-content').html(data);
                  $('#jt-workstations-modal').modal('show');
               }
            }
         });
      }

      var create_batch_row = [];
      $(document).on('click', '.create-batch-btn', function(e){
         e.preventDefault();
         create_batch_row = $(this).closest('tr');
         $('#planned-qty').val(create_batch_row.find('.qty-to-manufacture').val());
         $('#batch-qty').val(create_batch_row.find('.qty-to-manufacture').val());
         $('#orig-row').val($(this).data('orig-row'));
         $('#create-batch-modal').modal('show');
      });

      $('.submit-batch-qty').click(function(e){
         e.preventDefault();
         
         var batch_qty = $('#batch-qty').val();
         var planned_qty = $('#planned-qty').val();
         var row_id = (typeof($('#orig-row').val()) != "undefined") ? $('#orig-row').val() : '';

         if (batch_qty <= 0) {
            showNotification("danger", 'Qty cannot be less than or equal to 0', "now-ui-icons travel_info");
            return false;
         }

         if (parseInt(batch_qty) >= parseInt(planned_qty)) {
            showNotification("danger", 'Qty should be less than ' + planned_qty, "now-ui-icons travel_info");
            return false;
         }
         
         var tr = create_batch_row.closest('tr');

         var clone = tr.clone().attr('id', '').addClass(row_id);
         create_batch_row.after(clone);

         if(typeof(tr.attr('id')) != "undefined" && tr.attr('id') != ''){
            $('.' + row_id).find('.item-details').remove();
            $('.' + row_id).find('.planned-qty').remove();
            $('.' + row_id).find('.planned-prod-orders').remove();
         }

         if(row_id && typeof($('#' + row_id)) != "undefined"){
            $('#' + row_id).find('.item-details').attr('rowspan', $('.' + row_id).length + 1);
            $('#' + row_id).find('.planned-qty').attr('rowspan', $('.' + row_id).length + 1);
            $('#' + row_id).find('.planned-prod-orders').attr('rowspan', $('.' + row_id).length + 1);
         }

         create_batch_row.find('.qty-to-manufacture').eq(0).val(planned_qty - batch_qty);
         clone.find('.qty-to-manufacture').eq(0).val(batch_qty);
         autonumbertablerow();
         $('#create-batch-modal').modal('hide');
      });

      function autonumbertablerow(){
         $('#item-list tbody tr').each(function (idx) {
            $(this).children("td:eq(0)").html(idx + 1);
         });
      }

      $('.close-wizard-btn').click(function(e){
         e.preventDefault();
         location.reload();
      });

      $(document).on('click', '.date-picker', function(e){
         $(this).datepicker({
            'format': 'yyyy-mm-dd',
            'autoclose': true,
         });

         $(this).datepicker('show');
      });

      $('#items-from').change(function(){
         var ref_name = $(this).val();
         $('#reference-name').text(ref_name);
         if (ref_name == 'Sales Order') {
            $('#reference-no').val('SO-');
         }else{
            $('#reference-no').val('MREQ-');
         }
      });

      $('#get-somr-frm').submit(function(e){
         e.preventDefault();
         var items_from = $('#items-from').val();
         $.ajax({
            url: "/assembly/get_reference_details/" + items_from + "/" + $('#reference-no').val(),
            type:"GET",
            data: {no_bom: 1},
            success:function(data){
               if (data.message) {
                  var alert = '<div class="alert alert-danger text-center" role="alert" style="font-size: 12pt;">'+ data.message + '</div>';
                  $('#so-item-list-div').html(alert);
               }else{
                  $('#so-item-list-div').html(data);
                  $('.btn-div').removeAttr('hidden');
               }
            },
            error: function(jqXHR, textStatus, errorThrown) {
               if(jqXHR.status == 401) {
                  showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
               }

               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      // get part
      var a1 = 0;
      $('#get-parts').click(function(){
         $('.so-details-div').html($('#so-details-tbl').html());
         a1++;
         var production_order = [];
         $('.production-order-no').each(function(){
            production_order.push($.trim($(this).text()));
         });

         if(a1 == 1){
            $.ajax({
               url: "/assembly/get_production_req_items?no_bom=1",
               type:"GET",
               data: {production_orders: production_order},
               success:function(data){
                  if (data.message) {
                     showNotification("danger", data.message, "now-ui-icons travel_info");
                     return false;
                  }

                  $('#material-planning-div').html(data);

                  if (production_order.length > 0) {
                     $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
                  }else{
                     a1 = 0;
                  }
                  window.location.hash = '#next';
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  if(jqXHR.status == 401) {
                     showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
                  }
                  
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
               },
            });
         }
      });

      $(document).on('click', '.view-production-process', function(){
         $('#production-order-wizard-nobom').text($(this).data('production-order'));
         $.ajax({
            url: "/view_bom_for_review/No BOM",
            type:"GET",
            data:{production: $(this).data('production-order')},
            success:function(data){
               $('#review-bom-details-div').html(data);
            }
         });

         $('#review-bom-modal .modal-title').html('Update Process');
         $('#review-bom-modal').modal('show');
      });

      $(document).on('change', '#sel-workstation', function(){
         var workstation = $(this).val();
         $('#sel-process').empty();
         if (workstation) {
            $.ajax({
               url: '/get_workstation_process/' + workstation,
               type:"GET",
               success:function(data){
                  if (data.length > 0) {
                     var opt = '<option value="">Select Process</option>';
                     $.each(data, function(i, v){
                        opt += '<option value="' + v.process_id + '">' + v.process_name + '</option>';
                     });

                     $('#sel-process').append(opt);
                  }
               }
            });
         }
      });

      $(document).on('click', '#submit-bom-review-btn', function(){
         var production_order = $('#production-order-wizard-nobom').text();
         var operation_id = null;
         var id = [];
         var workstation = [];
         var wprocess = [];
         var workstation_process = [];
         var bom = 'no_bom';

         $("#bom-workstations-tbl > tbody > tr").each(function () {
            id.push($(this).find('span').eq(0).text());
            workstation.push($(this).find('td').eq(1).text());
            wprocess.push($(this).find('select').eq(0).val());
            workstation_process.push($(this).find('select option:selected').eq(0).text());
         });

         var filtered_process = wprocess.filter(function (el) {
            return el != null && el != "";
         });

         if (workstation.length != filtered_process.length) {
            showNotification("danger", 'Please select process', "now-ui-icons travel_info");
            return false;
         }

         var processArr = workstation_process.sort();
         var processDup = [];
         for (var i = 0; i < processArr.length - 1; i++) {
            if (processArr[i + 1] == processArr[i]) {
                  processDup.push(processArr[i]);
                  showNotification("danger", 'Process <b>' + processArr[i] + '</b> already exist.', "now-ui-icons travel_info");
                  return false;
            }
         }

         $.ajax({
            url: '/submit_bom_review/' + bom,
            type:"POST",
            data: {id: id, workstation: workstation, wprocess: wprocess, production_order: production_order, operation:operation_id},
            success:function(data){
               if(data.status) {
                  $('#review-bom-modal').modal('hide');
                  $('#nobom' + production_order).removeClass('btn-primary pprocess').addClass('btn-success');
                  $('#nobom' + production_order).html('<i class="now-ui-icons ui-1_check"></i> Update Process');
                  showNotification("success", data.message, "now-ui-icons ui-1_check");
               } else {
                  showNotification("danger", data.message, "now-ui-icons travel_info");
               }
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            }
         });
      });
  

      $(document).on('click', '#add-operation-btn', function(){
         var workstation = $('#sel-workstation option:selected').text();
         var wprocess = $('#sel-process').val();
         if (!$('#sel-workstation').val()) {
            showNotification("info", 'Please select Workstation', "now-ui-icons travel_info");
            return false;
         }

         var rowno = $('#bom-workstations-tbl tr').length;
         var sel = '<div class="form-group" style="margin: 0;"><select class="form-control form-control-lg">' + $('#sel-process').html() + '</select></div>';
         if (workstation) {
            var markup = "<tr><td class='text-center'>" + rowno + "</td><td>" + workstation + "</td><td>" + sel + "</td><td class='td-actions text-center'><button type='button' class='btn btn-danger delete-row'><i class='now-ui-icons ui-1_simple-remove'></i></button></td></tr>";
            $("#bom-workstations-tbl tbody").append(markup);
         }
      });

      var a2 = 0;
      $('.get-parts-prodorder').click(function(e){
         e.preventDefault();
         a2++;
         if ($('.create-ste-btn').length > 0) {
            showNotification("danger", 'Please create withdrawal slips for all production orders.', "now-ui-icons travel_info");
            return false;
         }

         var production_order = [];
         $('.production-order-no').each(function(){
            production_order.push($.trim($(this).text()));
         });

         if(a2 == 1){
            $.ajax({
               url: "/view_operations_wizard",
               type:"GET",
               data: {production_orders: production_order},
               success:function(data){
                  if (data.message) {
                     showNotification("danger", data.message, "now-ui-icons travel_info");
                     return false;
                  }

                  $('#view-operations-div').html(data);

                  if (production_order.length > 0) {
                     $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
                  }else{
                     a2 = 0;
                  }
                  window.location.hash = '#next';
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  if(jqXHR.status == 401) {
                     showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
                  }
                  
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
               },
            });
         }
      });

      $('.prev-btn').click(function(){         
         $('.nav-tabs li > .active').parent().prev().find('a[data-toggle="tab"]').tab('show');
         var target = $(this).data('target');

         switch(target){
            case 'a1':
               a1 = 0;
               break;
            case 'a2':
               a2 = 0;
               break;
            case 'a3':
               a3 = 0;
               break;
            default:
         }

         window.location.hash = '#next';
      });

      var a3 = 0;
      $('.finish-btn').click(function(e){
         if ($('.pprocess').length > 0) {
            showNotification('danger', 'Please update workstation / process for all production orders.', "now-ui-icons travel_info");
            return false;
         }

         a3++;
         e.preventDefault();
         var order_details = $('#last-tab').find('.text-center span').eq(0).text();
         var notif = '<div class="alert alert-success" role="alert">' +
            '<span class="d-none">'+order_details+'</span>' +
            '<div class="container">' +
              '<div class="alert-icon">' +
                '<i class="now-ui-icons ui-2_like"></i><span class="ml-1 font-weight-bold">Production Orders created for ' + order_details.split(' ')[3] + '</span>' +
              '</div>' +
            '</div>' +
          '</div>';
          $('#last-tab').find('.text-center').eq(0).html(notif);

         var production_orders = [];
         $('#req-items-tbl > tbody > tr').each(function(){
            var prod = $(this).find('.production-order').eq(0).text();
            if (production_orders.indexOf(prod) === -1) {
               production_orders.push(prod);
            }         
         });

         if(a3 == 1){
            $.ajax({
               url: "/production_planning_summary",
               type:"GET",
               data: {production_orders: production_orders},
               success:function(data){
                  if (data.message) {
                     showNotification('danger', data.message, "now-ui-icons travel_info");
                     return false;
                  }

                  $('#planning-summary-div').html(data);

                  $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
                  window.location.hash = '#next';
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  if(jqXHR.status == 401) {
                     showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
                  }
                  
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
               },
            });
         }
      });

      $(document).on('click', '#print-withdrawal-slip-btn', function(e){
         e.preventDefault();
         var production_orders = [];
         $('#summary-tbl > tbody > tr').each(function(){
            production_orders.push($.trim($(this).find('td').eq(1).text()));
         });
         var product= production_orders.toString(); 

         $.ajax({
            url: "/print_withdrawals",
            type:"GET",
            data: {production_orders: product},
            success:function(data){
               if (data.success < 1) {
                  showNotification("danger", data.message, "now-ui-icons travel_info"); //show alert message
               }else{
                  $('#printmodalbody').html(data);
                  $('#print_modal_js_ws').modal('show');
               }
            },
            error: function(jqXHR, textStatus, errorThrown) {
               if(jqXHR.status == 401) {
                  showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
               }
               
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      $(document).on("click", ".delete-row", function(e){
         e.preventDefault();
         $(this).parents("tr").remove();
      });

      $(document).on("click", ".create-production-btn", function(e){
         e.preventDefault();
         var $btn = $(this);
         var $row = $(this).closest('tr');

         data = {
            reference_type: $('#items-from').val(),
            reference_no: $('#reference-no').val(),
            item_code: $row.find('.item-code').text(),
            description: $row.find('.item-description').text(),
            qty: $row.find('.qty').text(),
            planned_date: $row.find('.planned-date').val(),
            target: $row.find('.target').val(),
            item_reference_id: $row.find('.item-reference-id').text(),
            qty_to_manufacture: $row.find('.qty-to-manufacture').val()
         }

         $btn.attr('disabled', true);
         $btn.next().attr('disabled', true);
         $row.find('.delete-row').eq(0).attr('disabled', true);

         $.ajax({
            url: "/create_production_order_without_bom",
            type:"POST",
            data: data,
            success:function(data){
               if (data.success < 1) {
                  showNotification("danger", data.message, "now-ui-icons travel_info");
                  $btn.removeAttr('disabled');
                  $row.find('.delete-row').eq(0).removeAttr('disabled');

                  return false;
               }

               $row.find('.target').prop('disabled', true);
               $row.find('.planned-date').prop('disabled', true);
               $row.find('.qty').prop('disabled', true);

               $btn.removeClass('btn-primary create-production-btn').addClass('btn-success production-order-no');
               // hide dropdown and x
               $btn.next().addClass('d-none');
               $row.find('.delete-row').eq(0).addClass('d-none');
               $btn.html('<i class="now-ui-icons ui-1_check"></i> ' + data.message);
            },
            error: function(jqXHR, textStatus, errorThrown) {
               if(jqXHR.status == 401) {
                  showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
               }

               $btn.removeAttr('disabled');
               
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      $(document).on('click', '.create-ste-btn', function(e){
         e.preventDefault();
         var $btn = $(this);
         var $row = $(this).closest('tr');
         var production_order = $row.find('.production-order').text();

         var arr = [];
         $('.tr-' + production_order).each(function(){
            var id = $(this).find('.item-id').eq(0).text();
            var item_code = $(this).find('.item-code').eq(0).text();
            var description = $(this).find('.item-description').eq(0).text();
            var s_warehouse = $(this).find('.source-wh-select').eq(0).val();
            var t_warehouse = $(this).find('.wip-warehouse').eq(0).text();
            var qty = $(this).find('.req-qty').eq(0).text();
            var stock_uom = $(this).find('.stock-uom').eq(0).text();
            var item_name = $(this).find('.item-name').eq(0).text();

            arr.push({
               id: id,
               item_code: item_code,
               item_name: item_name,
               description: description,
               s_warehouse: s_warehouse,
               qty: qty,
               stock_uom: stock_uom,
            });
         });

         data = {
            production_order: production_order,
            req_items: arr
         }

         $.ajax({
            url: "/generate_stock_entry/" + production_order,
            type:"POST",
            success:function(response){
               if (response.success == 0) {
                  showNotification("danger", response.message, "now-ui-icons travel_info");

                  return false;
               }

               if (response.error) {
                  showNotification("danger", 'There was a problem creating stock entry.', "now-ui-icons travel_info");

                  return false;
               }

               $btn.removeClass('btn-primary create-ste-btn').addClass('btn-success');
               $btn.html('<i class="now-ui-icons ui-1_check"></i> ' + response.id);
            },
            error: function(jqXHR, textStatus, errorThrown) {
               if(jqXHR.status == 401) {
                  showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
               }
               
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      $(document).on('change', '.source-wh-select', function(){
         var item = $(this).closest('tr').find('.item-code').text();
         var warehouse = $(this).val();
         var $row = $(this).closest('tr');

         var req = $row.find('.req-qty').text();
         $.ajax({
            url: "/get_actual_qty/" + item +'/' + warehouse,
            type:"GET",
            success:function(data){
               var on_stock = data * 1;
               var balance = on_stock - req;
               var on_stock_color = (on_stock < req) ? 'red' : 'green';
               var balance_color = (balance > 0) ? 'green' : 'red';

               $row.find('.stock-qty').text(on_stock).css('color', on_stock_color);
               $row.find('.balance-qty').text(balance).css('color', balance_color);
               $row.find('.uom-stock').css('color', on_stock_color);
               $row.find('.uom-bal').css('color', balance_color);

               if (balance > 0) {
                  $row.find('.mr-btn').attr('disabled', true);
               }else{
                  $row.find('.mr-btn').removeAttr('disabled');
               }
            },
            error: function(jqXHR, textStatus, errorThrown) {
               if(jqXHR.status == 401) {
                  showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
               }
               
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      function showNotification(color, message, icon){
         $.notify({
            icon: icon,
            message: message
         },{
            type: color,
            timer: 2000,
            placement: {
               from: 'top',
               align: 'center'
            }
         });
      }

      setInterval(updateClock, 1000);
      function updateClock(){
         var currentTime = new Date();
         var currentHours = currentTime.getHours();
         var currentMinutes = currentTime.getMinutes();
         var currentSeconds = currentTime.getSeconds();
         // Pad the minutes and seconds with leading zeros, if required
         currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
         currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
         // Choose either "AM" or "PM" as appropriate
         var timeOfDay = (currentHours < 12) ? "AM" : "PM";
         // Convert the hours component to 12-hour format if needed
         currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
         // Convert an hours component of "0" to "12"
         currentHours = (currentHours === 0) ? 12 : currentHours;
         currentHours = (currentHours < 10 ? "0" : "") + currentHours;
         // Compose the string for display
         var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

         $("#current-time").html(currentTimeString);
      }

      $(document).on('show.bs.modal', '.modal', function (event) {
         var zIndex = 1040 + (10 * $('.modal:visible').length);
         $(this).css('z-index', zIndex);
         setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
         }, 0);
      });
   });
</script>
@endsection