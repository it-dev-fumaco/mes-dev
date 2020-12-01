@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'production_planning',
])

@section('content')
<div class="panel-header" style="margin-top: -70px;">
   <div class="header text-center">
      <div class="row">
         <div class="col-md-8 text-white">
            <table style="text-align: center; width: 100%;">
               <tr>
                  <td style="width: 30%; border-right: 5px solid white;">
                     <div class="pull-right title mr-3">
                        <span class="d-block m-0 p-0" style="font-size: 14pt;">{{ date('M-d-Y') }}</span>
                        <span class="d-block m-0 p-0" style="font-size: 10pt;">{{ date('l') }}</span>
                     </div>
                  </td>
                  <td style="width: 20%; border-right: 5px solid white;">
                     <h3 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h3>
                  </td>
                  <td style="width: 50%">
                     <h3 class="title text-left p-0 ml-3" style="margin: auto 20pt;">Planning Wizard - Fabrication</h3>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<br>
<div class="content" style="margin-top: -145px;">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <ul class="nav nav-tabs d-none" id="myTab" role="tablist">
                  <li class="nav-item">
                     <a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1" role="tab" aria-controls="step1" aria-selected="true">Select Sales Order</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="step2-tab" data-toggle="tab" href="#step2" role="tab" aria-controls="step2" aria-selected="false">Get Part(s)</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="step3-tab" data-toggle="tab" href="#step3" role="tab" aria-controls="step3" aria-selected="false">Get Part Code</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="step4-tab" data-toggle="tab" href="#step4" role="tab" aria-controls="step4" aria-selected="false">Material Planning</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="step5-tab" data-toggle="tab" href="#step5" role="tab" aria-controls="step5" aria-selected="false">Summary</a>
                  </li>
               </ul>
               <!-- Tab panes -->
               <div class="tab-content" style="min-height: 620px;">
                  <div class="tab-pane active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20px;">1. Select Sales Order / Material Request</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-6 offset-md-3">
                           <form id="get-somr-frm">
                              <table style="width: 100%;">
                                 <col style="width: 20%;">
                                 <col style="width: 25%;">
                                 <col style="width: 17%;">
                                 <col style="width: 27%;">
                                 <col style="width: 11%;">
                                 <tr>
                                    <td class="text-right align-middle">
                                       <span style="margin-right: 10px;"><b>Get Items From:</b></span>
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
                                       <span style="margin-right: 10px; font-weight: bold;" id="reference-name">Sales Order</span>:
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
                        <div class="col-md-10 offset-md-1">
                           <div id="so-item-list-div"></div>
                        </div>
                     </div>
                     <div class="row btn-div" hidden>
                        <div class="col-md-10 offset-md-1">
                           <div class="pull-right">
                              <button id="get-parts" class="btn btn-lg btn-primary">Next</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">2. Get Sub-Assemblies</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-10 offset-md-1">
                           <div class="so-details-div"></div>
                           <div id="parts-list-div"></div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10 offset-md-1">
                           <div class="pull-right">
                              <button class="btn btn-lg btn-primary prev-btn">Previous</button>
                              <button class="btn btn-lg btn-primary get-parts-prodorder">Next</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="step3" role="tabpanel" aria-labelledby="step3-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">3. Create Production Order</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                           <div class="so-details-div"></div>
                           <table class="table table-hover table-bordered" id="parts-production-tbl">
                              <col style="width: 3%;">
                              <col style="width: 8%;">
                              <col style="width: 28%;">
                              <col style="width: 8%;">
                              <col style="width: 8%;">
                              <col style="width: 12%;">
                              <col style="width: 11%;">
                              <col style="width: 11%;">
                              <col style="width: 11%;">
                              <thead class="text-white bg-secondary" style="font-size: 8pt;">
                                 <th class="text-center"><b>No.</b></th>
                                 <th class="text-center"><b>Item Code</b></th>
                                 <th class="text-center"><b>Description</b></th>
                                 <th class="text-center"><b>Ordered Qty</b></th>
                                 <th class="text-center"><b>Available Qty</b></th>
                                 <th class="text-center"><b>Planned Qty</b></th>
                                 <th class="text-center"><b>Planned Start Date</b></th>
                                 <th class="text-center"><b>Target Warehouse</b></th>
                                 <th class="text-center"><b>Action</b></th>
                              </thead>
                              <tbody style="font-size: 9pt;"></tbody>
                           </table>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10 offset-md-1">
                           <div class="pull-right">
                              <button class="btn btn-lg btn-primary prev-btn" id="create-prod-prev-btn">Previous</button>
                              <button class="btn btn-lg btn-primary" id="get-req-items">Next</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">4. Material Planning</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                           <div class="pull-right p-0" style="position: absolute; top: -10px; right: 12px;">
                              <button type="button" class="btn btn-info m-0" id="create-material-req-btn">
                                 <i class="now-ui-icons ui-1_simple-add"></i> Material Request
                              </button>
                           </div>
                           <div class="so-details-div"></div>
                           <div id="material-planning-div"></div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10 offset-md-1">
                           <div class="pull-right">
                              <button class="btn btn-lg btn-primary prev-btn">Previous</button>
                              <button class="btn btn-lg btn-primary finish-btn">Finish</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="step5" role="tabpanel" aria-labelledby="step5-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">5. Production Planning Summary</h4>
                     <div style="right: 120px; margin: -40px 50px 10px 10px; padding: 5px; position: absolute;">
                        <a href="#" id="print-withdrawal-slip-btn" class="btn btn-primary">
                           Print Withdrawal Slip
                        </a>
                     </div>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-10 offset-md-1" id="last-tab">
                           <div class="so-details-div"></div>
                           <div id="planning-summary-div"></div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10 offset-md-1">
                           <div class="pull-right">
                              <button class="btn btn-lg btn-primary prev-btn">Previous</button>
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
</div>

<iframe id="iframe-print" style="display: none;"></iframe>

<!-- Modal -->
<div class="modal fade" id="view-bom-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="min-width: 50%;">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bolder;">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div id="bom-details-div"></div>
         </div>
      </div>
   </div>
</div>

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
            <div id="review-bom-details-div"></div>
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
                     <input type="number" class="form-control form-control-lg" id="planned-qty" style="display: none;">
                     <input type="number" class="form-control form-control-lg" id="batch-qty">
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

<!-- Modal -->
<div class="modal fade" id="view-sched-task-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 80%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-weight: bolder;">Modal Title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <div id="sched-task-div"></div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="create-material-request-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="font-weight: bolder;">Material Request</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <form action="/generate_material_request" method="POST" autocomplete="off">
            @csrf
            <div class="row">
               <div class="col-md-8">
                  <dl class="row ml-2">
                     <dt class="col-sm-3 mt-1">Reference No.:</dt>
                     <dd class="col-sm-9 mt-1"><span class="reference-no"></span></dd>

                     <dt class="col-sm-3 mt-1">Customer:</dt>
                     <dd class="col-sm-9 mt-1"><span class="customer"></span></dd>
                   
                     <dt class="col-sm-3 mt-1">Project:</dt>
                     <dd class="col-sm-9 mt-1"><span class="project"></span></dd>
                   
                     <dt class="col-sm-3 mt-1">Delivery Date:</dt>
                     <dd class="col-sm-9 mt-1"><span class="delivery-date"></span></dd>
                   </dl>
               </div>
               <div class="col-md-4 pr-5">
                  <div class="form-group mb-2 mr-1">
                     <label for="required-date">Required Date</label>
                     <input type="text" id="required-date" class="date-picker form-control" name="schedule_date" style="text-align: center;">
                  </div>
                  <div class="form-group mb-2 mr-2">
                     <label for="required-date">Purchase Request</label>
                     <select name="purchase_request" class="form-control" required>
                        <option value="Local">Local</option>
                        <option value="Imported">Imported</option>
                     </select>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <table class="table table-bordered">
                     <col style="width: 45%;">
                     <col style="width: 10%;">
                     <col style="width: 20%;">
                     <col style="width: 17%;">
                     <col style="width: 8%;">
                     <thead style="font-size: 7pt;">
                        <th class="text-center font-weight-bold">Item Code</th>
                        <th class="text-center font-weight-bold">Quantity</th>
                        <th class="text-center font-weight-bold">Warehouse</th>
                        <th class="text-center font-weight-bold">Required Date</th>
                        <th class="text-center font-weight-bold">Action</th>
                     </thead>
                     <tbody id="mr-items"></tbody>
                  </table>
               </div>
               <div class="col-md-6 offset-md-3">
                  <div class="row">
                     <div class="col-md-6">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                     </div>
                     <div class="col-md-6">
                        <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Cancel</button>
                     </div>
                  </div>
               </div>
            </form>
         </div>
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
@include('wizard.modal_select_available_scrap')
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{  asset('js/printThis.js') }}"></script>

<script>
   $(document).ready(function(){
      $(document).on('click', '#create-material-req-btn', function(e){
         e.preventDefault();
         var row = '';
         var row_values = [];
         var ref = null;
         $('#req-items-tbl .item-row').each(function(){
            var reference_no = $(this).find('.reference-no').eq(0).text();
            var item_code = $(this).find('.item-code').eq(0).text();
            var warehouse = $(this).find('.source-wh-select').eq(0).html();
            var required_qty = $(this).find('.req-qty').eq(0).text();
            var production_order = $(this).find('.production-order').eq(0).text();
            var current_stock = $(this).find('.stock-qty').eq(0).text();
            var description = $(this).find('.item-description').eq(0).text();

            ref = reference_no;

            row_values.push({
               reference_no: reference_no,
               item_code: item_code,
               warehouse: warehouse,
               required_qty: required_qty * 1,
               production_order: production_order,
               current_stock: current_stock,
               description: description,
            });
         });

         var result = [];
         row_values.reduce(function(res, value) {
            if (!res[value.item_code]) {
               res[value.item_code] = {
                  reference_no: value.reference_no,
                  item_code: value.item_code,
                  warehouse: value.warehouse,
                  required_qty: 0,
                  production_order: value.production_order,
                  current_stock: value.current_stock,
                  description: value.description,
               };
               result.push(res[value.item_code]);
            }
            
            res[value.item_code].required_qty += value.required_qty * 1;
            return res;
         }, {});

         if(result.length <= 0){
            showNotification("info", 'All required raw material has sufficient stocks.', "now-ui-icons travel_info");

            return false;
         }

         $.each(result, function( index, value ) {
            var badge = (parseFloat(value.current_stock) < value.required_qty) ? 'danger' : 'success';
            row += '<tr>' + 
               '<td class="text-justify">' + 
                  '<span class="d-block font-weight-bold item-code">' + value.item_code + '</span>' +
                  '<span class="d-block" style="font-size: 8pt;">' + value.description + '</span>' +
                  '<input type="hidden" name="production_order[]" value="' + value.production_order + '">' +
                  '<input type="hidden" name="reference_no[]" value="' + value.reference_no + '">' +
                  '<input type="hidden" name="item_code[]" value="' + value.item_code + '">' +
                '</td>' +
                '<td class="text-center"><input type="text" class="form-control req-qty" name="required_qty[]" value="' + value.required_qty + '" style="text-align: center;"><p class="d-block mt-1 mb-0 p-0"><span class="stock-qty badge badge-'+ badge +'">'+ value.current_stock +'</span></p></td>' +
                '<td><select name="warehouse[]" class="form-control mr-sel-wh">' + value.warehouse + '</select></td>' +
                '<td><input type="text" class="form-control date-picker required-date-row" name="required_date[]" required style="text-align: center;"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-danger remove-row p-2">Remove</button></td>' +
                '</tr>';
         });

         $('#mr-items').html(row);

         var customer = $('#so-details-tbl table tbody td').eq(1).text();
         var project = $('#so-details-tbl table tbody td').eq(11).text();
         var delivery_date = $('#so-details-tbl table tbody td').eq(9).text();
         var reference_no = ref;

         $('#create-material-request-modal .reference-no').text(reference_no);
         $('#create-material-request-modal .project').text(project);
         $('#create-material-request-modal .delivery-date').text(delivery_date);
         $('#create-material-request-modal .customer').text(customer);

        $('#create-material-request-modal').modal('show');
      });

      $(document).on('change', '.mr-sel-wh', function(){
         var item = $(this).closest('tr').find('.item-code').text();
         var warehouse = $(this).val();
         var $row = $(this).closest('tr');

         var req = $row.find('.req-qty').val();
         $.ajax({
            url: "/get_actual_qty/" + item +'/' + warehouse,
            type:"GET",
            success:function(data){
               var on_stock = data * 1;
               if(on_stock < (req * 1)){
                  $row.find('.stock-qty').text(on_stock).removeClass('badge-success').addClass('badge-danger');
               }else{
                  $row.find('.stock-qty').text(on_stock).removeClass('badge-danger').addClass('badge-success');
               }
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      $('#create-material-request-modal form').submit(function(e){
         e.preventDefault();

         $.ajax({
            url: '/generate_material_request',
            type:"POST",
            data: $(this).serialize(),
            success:function(response){
               if (response.success == 0) {
                  showNotification("danger", 'There was a problem creating material request. Try again.', "now-ui-icons travel_info");

                  return false;
               }

               if (response.success == 1) {
                  $('#create-material-req-btn').removeClass('btn-info create-material-req-btn').addClass('btn-success');
                  $('#create-material-req-btn').html('<i class="now-ui-icons ui-1_check"></i> ' + response.id);
                  $('#create-material-request-modal').modal('hide');

                  return false;
               }

               if (response.success == 2) {
                  showNotification("info", response.message, "now-ui-icons travel_info");

                  return false;
               }
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            }
         });
      });

      $('#required-date').change(function(e){
         e.preventDefault();
         $('.required-date-row').val($(this).val());
      });

      $(document).on('click', '.remove-row', function(e){
         e.preventDefault();
         $(this).closest('tr').remove();
      });

      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });

      $('.close-wizard-btn').click(function(e){
         e.preventDefault();
         location.reload();
      });

      function get_warehouse(item_classification, type){
         var row = '';
         $.ajax({
            url: "/get_warehouses/Fabrication/" + item_classification,
            type:"GET",
            datatype: "json",
            async: false,
            success: function(data){
               $.each(data, function(i, v){
                  row += '<option value="' + v.warehouse + '">' + v.warehouse + '</option>';
               });
            }
         });

         return row;
      }

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
         if (items_from == 'Sales Order') {
            $.ajax({
               url: "/get_sales_order_details/" + $('#reference-no').val(),
               type:"GET",
               success:function(data){
                  if (data.message) {
                     var alert = '<div class="alert alert-danger text-center" role="alert" style="font-size: 12pt;">'+ data.message + '</div>';
                     $('#so-item-list-div').html(alert);
                  }else{
                     $('#so-item-list-div').html(data);
                     $('.btn-div').removeAttr('hidden');
                  }
               }
            });
         }else{
            $.ajax({
               url: "/get_material_request_details/" + $('#reference-no').val(),
               type:"GET",
               success:function(data){
                  if (data.message) {
                     var alert = '<div class="alert alert-danger text-center" role="alert" style="font-size: 12pt;">'+ data.message + '</div>';
                     $('#so-item-list-div').html(alert);
                  }else{
                     $('#so-item-list-div').html(data);
                     $('.btn-div').removeAttr('hidden');
                  }
               }
            });
         }
      });

      $(document).on('click', '#submit-bom-review-btn', function(){
         var bombtn = $(this).data('id');
         var idx = $('#review-bom-modal #bom-idx').text();

         var operation = 'Fabrication';

         var id = [];
         var workstation = [];
         var wprocess = [];
         var workstation_process = [];
         var bom = $('#bom-workstations-tbl input[name=bom_id]').val();
         var user = $('#bom-workstations-tbl input[name=username]').val();
         $("#bom-workstations-tbl > tbody > tr").each(function () {
            id.push($(this).find('span').eq(0).text());
            workstation.push($(this).find('td').eq(1).text());
            wprocess.push($(this).find('select').eq(0).val());
            workstation_process.push($(this).find('select option:selected').eq(0).text());
         });

         var wprocess1 = wprocess.filter(function (el) {
            return el != null && el != "";
         });

         if (workstation.length != wprocess1.length) {
            showNotification("info", 'Please select process', "now-ui-icons travel_info");
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
            data: {user: user, id: id, workstation: workstation, wprocess: wprocess, operation: operation},
            success:function(data){
               $('#review-bom-modal').modal('hide');
               $('#parts-list').find('#'+idx+''+bombtn).removeClass('unchecked').addClass('now-ui-icons ui-1_check text-success');
               showNotification("success", data.message, "now-ui-icons ui-1_check");
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

      $(document).on('change', '#sel-workstation', function(){
         $('#add-operation-btn').attr('disabled', true);
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

                     $('#add-operation-btn').removeAttr('disabled');
                     $('#add-operation-btn').text('Add Operation');
                  }else{
                     $('#add-operation-btn').text('No Assigned Process');
                  }
               }
            });
         }
      });

      $('.prev-btn').click(function(){         
         $('.nav-tabs li > .active').parent().prev().find('a[data-toggle="tab"]').tab('show');
      });

      $('#get-req-items').click(function(){
         var production_order = [];
         var parent_code = [];
         $("#parts-production-tbl > tbody > tr").each(function () {
            str = $.trim($(this).find('.btn').eq(1).text());
            if (str.indexOf('PROM-') > -1) {
              production_order.push(str);
            }
         });

         $.ajax({
            url: "/get_production_req_items",
            type:"GET",
            data: {production_orders: production_order},
            success:function(data){
               $('#material-planning-div').html(data);
            }
         });

         if (production_order.length > 0) {
            $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
         }
      });

      $('#get-parts').click(function(){
         $('.so-details-div').html($('#so-details-tbl').html());

         var bom = [];
         var idx = [];
         var qty = [];
         var so = [];
         var so_item = [];
         var item_reference_id = [];
         var delivery_date = [];
         $("#item-list > tbody > tr").each(function () {
            so.push($(this).find('span').eq(1).text());
            bom.push($(this).find('select').eq(0).val());
            idx.push($(this).find('span').eq(0).text());
            qty.push(parseFloat($(this).find('.qty').eq(0).text()));
            item_reference_id.push($(this).find('.item-reference-id').eq(0).text());
            delivery_date.push($(this).find('.delivery-date').eq(0).text());
         });

         $.ajax({
            url: "/get_parts",
            type:"GET",
            data: {so: so, bom: bom, idx: idx, qty: qty, item_reference_id: item_reference_id, delivery_date: delivery_date},
            success:function(data){
               $('#parts-list-div').html(data);
            }
         });

         if (bom.length > 0) {
            $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
         }
      });

      $('.finish-btn').click(function(e){
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

         $.ajax({
            url: "/production_planning_summary",
            type:"GET",
            data: {production_orders: production_orders},
            success:function(data){
               $('#planning-summary-div').html(data);
            }
         });

         $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
      });

      $('.get-parts-prodorder').click(function(e){
         e.preventDefault();
         var unreviewed_bom = $('#parts-list').find('.unchecked').length;
         if (unreviewed_bom > 0) {
            showNotification("info", 'Please review all BOM first.', "now-ui-icons travel_info");
            return false;
         }

         $('#parts-production-tbl tbody').empty();
         var row = '';
         var n = 1;

          $("#parts-list > tbody > tr").each(function () {
            var s_wh = get_warehouse($(this).find('.item-classification').eq(0).text(), 'source');
            var t_wh = get_warehouse($(this).find('.item-classification').eq(0).text(), 'target');
            
            var prod_order = $(this).find('.production-order').eq(0).text();
            if (prod_order) {
               s_wh = '<option selected>' + $(this).find('.s-warehouse').eq(0).text() + '</option>';
            }

            if (prod_order) {
               t_wh = '<option selected>' + $(this).find('.fg-warehouse').eq(0).text() + '</option>';
            }

            var disable_sel = (prod_order) ? 'disabled' : null;

            var btn;
            if (prod_order) {
               btn = '<button type="button" class="btn btn-success prod-order"><i class="now-ui-icons ui-1_check"></i> ' + $(this).find('.production-order').eq(0).text() + '</button>';
            }else{
               // btn = '<button type="button" class="btn btn-primary create-production-btn"><i class="now-ui-icons ui-1_simple-add"></i> Production Order</button>';

               btn = '<div class="btn-group"><button type="button" class="btn btn-primary create-production-btn"><i class="now-ui-icons ui-1_simple-add"></i> Production Order</button><button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 15px; font-size: 9pt;"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu create-batch-btn"><a class="dropdown-item" href="#">Create Batch</a></div></div>';
            }

            var actual_qty = $(this).find('.actual-qty').eq(0).text();
            if(actual_qty > 0){
               var stylecss = 'color: green;';
            }else{
               var stylecss = 'color: red;';
            }

            row += '<tr>' +
               '<td class="text-center">' + n + '</td>' +
               '<td class="text-center"><span class="delivery-date" style="display: none;">'+$(this).find('.delivery-date').text()+'</span><span class="item-reference-id" style="display: none;">'+$(this).find('.item-reference-id').text()+'</span><span class="sub-parent-item" style="display: none;">'+$(this).find('.sub-parent-item').text()+'</span><span class="parent-code" style="display: none;">'+$(this).find('.parent-code').text()+'</span><span class="reference-no" style="display: none;">'+$(this).find('.reference-no').eq(0).text()+'</span><span class="item-code">' + $(this).find('td').eq(1).text() + '</span><br><span class="bom">' + $(this).find('.review-bom-row').eq(0).text() + '</span></td>' +
               '<td class="text-justify item-description">' + $(this).find('td').eq(2).text() + '</td>' +
               '<td class="text-center" style="font-size: 11pt;">' + $(this).find('td').eq(3).text() + '</td>' +
               '<td class="text-center" style="font-size: 11pt; '+ stylecss+'"><b>' + actual_qty + '</b></td>' +
               '<td class="text-center">' +
                  '<div class="form-group" style="margin: 0;"><input type="text" value="' + $(this).find('.qty').eq(0).text() + '" class="form-control form-control-lg qty" style="text-align: center; font-size: 11pt;" ' + disable_sel + '></div>' +
               '</td>' +
               '<td class="text-center">' +
                  '<div class="input-group" style="margin: 0;"><input type="text" class="form-control form-control-lg date-picker planned-date" style="text-align: center; font-size: 11pt;" ' +disable_sel + ' value="' + $(this).find('.planned-start-date').text() + '"><div class="input-group-append"><button class="btn btn-info view-sched-task" type="button"><i class="now-ui-icons ui-1_zoom-bold"></i></button></div></div></td>' +
	               '<td class="text-center"><div class="form-group" style="margin: 0;"><select class="form-control form-control-lg target" ' + disable_sel + '>' + t_wh + '</select></div></td>' +
               '<td class="text-center">'+btn+'</td>' +
               '</tr>';

            n++;
         });

         $('#parts-production-tbl tbody').append(row);
   
         $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
      });

      var create_batch_row = '';
      $(document).on('click', '.create-batch-btn', function(e){
         e.preventDefault();
         create_batch_row = $(this).closest('tr');
         $('#planned-qty').val(create_batch_row.find('.qty').val());
         $('#batch-qty').val(create_batch_row.find('.qty').val());
         $('#create-batch-modal').modal('show');
      });

      $('.submit-batch-qty').click(function(e){
         e.preventDefault();
         var batch_qty = $('#batch-qty').val();
         var planned_qty = $('#planned-qty').val();
         if (batch_qty <= 0) {
            showNotification("danger", 'Qty cannot be less than or equal to 0', "now-ui-icons travel_info");
            return false;
         }

         if (batch_qty >= planned_qty) {
            showNotification("danger", batch_qty + 'Qty should be less than ' + planned_qty, "now-ui-icons travel_info");
            return false;
         }

         var row = '<tr>' +
            '<td class="text-center"><span class="sub-parent-item" style="display: none;">'+create_batch_row.find('.sub-parent-item').text()+'</span><span class="parent-code" style="display: none;">' + create_batch_row.find('.parent-code').text() +'</span><span class="sales-order" style="display: none;">' + create_batch_row.find('.sales-order').eq(0).text() + '</span></td>' +
            '<td class="text-center"><span class="item-code">' + create_batch_row.find('.item-code').eq(0).text() + '</span><br><span class="bom">' + create_batch_row.find('.bom').eq(0).text() + '</span></td>' +
            '<td class="text-justify item-description">' + create_batch_row.find('.item-description').eq(0).text() + '</td>' +
            '<td class="text-center" style="font-size: 11pt;">' + planned_qty + '</td>' +
            '<td class="text-center">' +
            '<div class="form-group" style="margin: 0;"><input type="text" value="' + batch_qty + '" class="form-control form-control-lg qty" style="text-align: center; font-size: 11pt;"></div>' +
            '</td>' +
            '<td class="text-center">' +
            '<div class="input-group" style="margin: 0;"><input type="text" class="form-control form-control-lg date-picker planned-date" style="text-align: center; font-size: 11pt;"><div class="input-group-append"><button class="btn btn-info" type="button"><i class="now-ui-icons ui-1_zoom-bold"></i></button></div></div></td>' +
            '<td class="text-center"><div class="form-group" style="margin: 0;"><select class="form-control form-control-lg wip">' + wipwh + '</select></div></td>' +
            '<td class="text-center"><div class="form-group" style="margin: 0;"><select class="form-control form-control-lg target">' + targetwh + '</select></div></td>' +
            '<td class="text-center"><div class="btn-group"><button type="button" class="btn btn-primary create-production-btn"><i class="now-ui-icons ui-1_simple-add"></i> Production Order</button><button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 15px; font-size: 9pt;"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu create-batch-btn"><a class="dropdown-item" href="#">Create Batch</a></div></div></td>' +
            '</tr>';

         create_batch_row.find('.qty').eq(0).val(planned_qty - batch_qty);
         create_batch_row.after(row);
         autonumbertablerow();
         $('#create-batch-modal').modal('hide');
      });

      $(document).on('click', '#print-withdrawal-slip-btn', function(e){
         e.preventDefault();
         var production_orders = [];
         $('#summary-tbl > tbody > tr').each(function(){
            production_orders.push($.trim($(this).find('td').eq(1).text()));
         });

         $.ajax({
            url: "/print_withdrawals",
            type:"GET",
            data: {production_orders},
            success:function(data){
              // window.open(this.url);
               $('#printmodalbody').html(data);
               // $('#print_modal_js_ws .modal-title').text("Withdrawal Slip Print Preview");
               $('#print_modal_js_ws').modal('show');
               // $("#iframe-print").attr("src", this.url);

            },
            error: function(jqXHR, textStatus, errorThrown) {
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
            parent_code: $row.find('.parent-code').text(),
            sub_parent_code: $row.find('.sub-parent-item').text(),
            item_code: $row.find('.item-code').text(),
            description: $row.find('.item-description').text(),
            qty: $row.find('.qty').val(),
            planned_date: $row.find('.planned-date').val(),
            target: $row.find('.target').val(),
            reference_no: $row.find('.reference-no').text(),
            bom: $row.find('.bom').text(),
            operation: 'Fabrication',
            item_reference_id: $row.find('.item-reference-id').text(),
	         delivery_date: $row.find('.delivery-date').text(),
         }

         $.ajax({
            url: "/create_production_order",
            type:"POST",
            data: data,
            success:function(data){
               if (data.success < 1) {
                  showNotification("danger", data.message, "now-ui-icons travel_info");

                  return false;
               }

               $row.find('.source').prop('disabled', true);
               $row.find('.target').prop('disabled', true);
               $row.find('.planned-date').prop('disabled', true);
               $row.find('.qty').prop('disabled', true);
               $row.find('.dropdown-toggle-split').hide();
               $('#create-prod-prev-btn').hide();
               $btn.removeClass('btn-primary create-production-btn').addClass('btn-success');
               $btn.html('<i class="now-ui-icons ui-1_check"></i> ' + data.message);
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      $(document).on("click", ".view-sched-task", function(e){
         e.preventDefault();
         var $row = $(this).closest('tr');
         var planned_date = $row.find('.planned-date').val();

         var data = {
            planned_date: planned_date,
            item_code: $row.find('.item-code').text(),
            bom: $row.find('.bom').text()
         }

         $.ajax({
            url: "/get_scheduled_production",
            type:"GET",
            data: data,
            success:function(data){
               var dateFormat = $.datepicker.formatDate('MM dd, yy', new Date(planned_date));
               $('#sched-task-div').html(data);
               $('#view-sched-task-modal .modal-title').text('Scheduled Date: ' + dateFormat);
               $('#view-sched-task-modal').modal('show');
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      $(document).on("click", ".view-bom", function(e){
         e.preventDefault();
         var sel_id = $(this).data('id');
         var sel_val = $('#'+ sel_id).val();

         $.ajax({
            url: "/view_bom/" + sel_val,
            type:"GET",
            success:function(data){
               $('#bom-details-div').html(data);
            }
         });

         $('#view-bom-modal .modal-title').html(sel_val);
         $('#view-bom-modal').modal('show');
      });

      $(document).on("click", ".review-bom-row", function(e){
         e.preventDefault();
         $.ajax({
            url: "/view_bom_for_review/" + $(this).data('bom'),
            type:"GET",
            success:function(data){
               $('#review-bom-details-div').html(data);
            }
         });

         $('#review-bom-modal .modal-title').html('Review & Finalize BOM [' + $(this).data('bom') + ']');
         $('#review-bom-modal #bom-idx').text($(this).data('idx'));
         $('#review-bom-modal').modal('show');
      });

      $(document).on('click', '.create-ste-btn', function(e){
         e.preventDefault();
         var $btn = $(this);
         var $row = $(this).closest('tr');
         var production_order = $row.find('.production-order').text();
         var item_code = $row.find('.item-code').text();

         var s_warehouses = [];
         $('.tr-' + production_order).each(function(){
            var s_warehouse = $(this).find('.source-wh-select').eq(0).val();

            s_warehouses.push(s_warehouse);
         });

         data = {
            production_order: production_order,
            item_code: item_code,
            s_warehouses: s_warehouses,
         }

         $.ajax({
            url: "/generate_stock_entry/" + production_order,
            type:"POST",
            data: data,
            success:function(response){
               console.log(response);
               if (response.error) {
                  showNotification("danger", 'There was a problem creating stock entry.', "now-ui-icons travel_info");

                  return false;
               }

               $btn.removeClass('btn-primary create-ste-btn').addClass('btn-success');
               $btn.html('<i class="now-ui-icons ui-1_check"></i> STE');
            },
            error: function(jqXHR, textStatus, errorThrown) {
               showNotification("danger", 'There was a problem creating stock entry.', "now-ui-icons travel_info");
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });

         var projected_scrap = $row.find('.projected-scrap-in-cubic-mm').text();
         if(projected_scrap){
            $.ajax({
               url: "/update_production_projected_scrap/" + production_order + "/" + projected_scrap,
               type:"POST",
               error: function(jqXHR, textStatus, errorThrown) {
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
               },
            });
         }
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

      function autonumbertablerow(){
         $('#parts-production-tbl tbody tr').each(function (idx) {
            $(this).children("td:eq(0)").html(idx + 1);
         });
      }

      $('#subassembly .remove-part-btn').click(function(e){
         e.preventDefault();
         $(this).parents("tr").remove();
         console.log('asd');
      });

      $(document).on('click', '.get-scrap-btn', function(e){
         e.preventDefault();

         var $row = $(this).closest('tr');
         fill_ste_frm($row);

         var projected_scrap = $row.find('.projected-scrap-in-cubic-mm').text();
         $('#scrap-frm input[name="projected_scrap"]').val(projected_scrap);

         var production_order = $(this).data('production-order');
         $('#req-url-input').val($(this).data('req-url'));
         
         $.ajax({
            url: "/display_available_scrap/" + production_order,
            type:"GET",
            success:function(response){
              $('#select-available-scrap-div').html(response);
              $('#select-scrap-modal').modal('show');
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });

      function fill_ste_frm($row){
         var production_order = $row.find('.production-order').text();
         var item_code = $row.find('.item-code').text();
         var s_warehouse = $row.find('.source-wh-select').val();
  
         $('#ste-frm input[name="production_order"]').val(production_order);
         $('#ste-frm input[name="item_code"]').val(item_code);
         $('#ste-frm input[name="s_warehouses[]"]').val(s_warehouse);
      }

      function load_req_items(url){
         $.ajax({
            url: url,
            type:"GET",
            success:function(data){
               $('#material-planning-div').html(data);
            }
         });
      }

      $(document).on('submit', '#scrap-frm', function(e){
         e.preventDefault();

         $.ajax({
            url: $(this).attr('action'),
            type:"POST",
            data: $(this).serialize(),
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });

         $.ajax({
            url: '/insert_scrap_used',
            type:"POST",
            data: $(this).serialize(),
            success:function(response){
               // console.log(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });   
         
         $.ajax({
            url: "/create_stock_entry",
            type:"POST",
            data: $('#ste-frm').serialize(),
            success:function(response){
               load_req_items($('#req-url-input').val());
               $('#select-scrap-modal').modal('hide');
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
         });
      });
   });
</script>
<script>

$('#btnPrint').on("click", function () {
      $('#printmodalbody').printThis({
      });
    });
</script>
@endsection