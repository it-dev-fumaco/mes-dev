@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'production_planning_assembly',
    'pageHeader' => 'Planning Wizard - Assembly',
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
                     <div>
                        <a href="/planning_wizard/no_bom" class="btn btn-secondary">Wizard - Item without BOM</a>
                        <a href="/assembly/wizard" class="btn btn-primary">Wizard - Item with BOM</a>
                     </div>
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20px; margin-top: -50px;">1. Select Sales Order / Material Request</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-6 offset-md-3">
                           <form id="get-somr-frm">
                              @if (request('item'))
                              @foreach (request('item') as $r)
                              <input type="hidden" value="{{ $r }}" name="item[]">
                              @endforeach
                              @endif
                              @if (request('bom'))
                              @foreach (request('bom') as $s => $e)
                              <input type="hidden" value="{{ $e }}" name="bom[{{ $s }}]">
                              @endforeach
                              @endif
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
                                             <option value="Sales Order" {{ request('ref_type') ? (request('ref_type') == 'SO' ? 'selected' : '') : '' }}>Sales Order</option>
                                             <option value="Material Request" {{ request('ref_type') ? (request('ref_type') == 'MREQ' ? 'selected' : '') : '' }}>Material Request</option>
                                          </select>
                                       </div>
                                    </td>
                                    <td class="text-right align-middle">
                                       <span style="margin-right: 10px; font-weight: bold;" id="reference-name">Sales Order</span>:
                                    </td>
                                    <td class="align-middle">
                                       <div class="form-group" style="margin: 0;">
                                          <input type="hidden" id="ref-no" value="{{ request('ref') }}">
                                          <input type="text" id="reference-no" class="form-control form-control-lg" value="{{ request('ref') ? request('ref') : 'SO-' }}" autocomplete="off">
                                       </div>
                                    </td>
                                    <td class="align-middle">
                                       <button type="submit" class="btn btn-primary" id="get-btn" style="margin-left: 10px;">Get</button>
                                    </td>
                                 </tr>
                              </table>
                           </form>
                        </div>
                        <div class="col-md-12">
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
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">2. Get Finished Goods</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-12">
                           <div class="so-details-div"></div>
                           <div id="parts-list-div"></div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10 offset-md-1">
                           <div class="pull-right">
                              <button class="btn btn-lg btn-primary prev-btn" data-target='a1'>Previous</button>
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
                                 <th class="text-center"><b>Actual Qty</b></th>
                                 <th class="text-center"><b>Planned Qty</b></th>
                                 <th class="text-center"><b>Planned Start Date</b></th>
                                 {{--  <th class="text-center"><b>WIP Warehouse</b></th>  --}}
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
                              <button class="btn btn-lg btn-primary prev-btn" id="create-prod-prev-btn" data-target='a2'>Previous</button>
                              <button class="btn btn-lg btn-primary" id="get-req-items">Next</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="tab-pane" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                     <h4 class="title text-center" style="margin-left: 20px; margin: auto 20pt;">4. Material Planning</h4>
                     <div class="row" style="margin-top: 10px;">
                        <div class="col-md-10 offset-md-1">
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
                              <button class="btn btn-lg btn-primary prev-btn" data-target='a3'>Previous</button>
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
                              <button class="btn btn-lg btn-primary prev-btn" data-target='a4'>Previous</button>
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
               if(jqXHR.status == 401) {
                  showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
               }
               
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            },
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

      function load_req_items(url){
         $.ajax({
            url: url,
            type:"GET",
            success:function(data){
               $('#material-planning-div').html(data);
            }
         });
      }

      $(document).on('click', '.change-raw-mat-btn', function(e){
         e.preventDefault();

         var url = $(this).data('req-url');

         $('#req-url-val').val(url);

         var $row = $(this).closest('tr');
         var item_code = $row.find('.item-code').text();
         var description = $row.find('.item-description').text();
         var classification = $row.find('.item-classification').text();
         var source_warehouse = $row.find('.source-wh-select').val();

         var img = $row.find('.img-thumbnail').eq(0).attr('src');

         $('#a-img').attr('href', img);
         $('#b-img').attr('src', img);
         
         $('#raw-mat-item-code').text(item_code);
         $('#raw-mat-description').text(description);
         $('#item-classification-raw-mat').val(classification);	
		
	      $('#input-raw-mat').val(item_code);

         get_raw_actual_qty(item_code, source_warehouse);

         $('#change-raw-mat-modal input[name="production_order_item_id"]').val($(this).data('id'));

         $.ajax({
            url: "/get_available_warehouse_qty/" + item_code,
            type:"GET",
            success: function(data){
               $('#change-raw-mat-modal .inv-list').html(data);
            }
         });

         $('#change-raw-mat-modal').modal('show');
      });

      $(document).on('click', '.selected-item', function(e){
         e.preventDefault();
         var item_code = $(this).data('item-code');
         var description = $(this).data('description');
         var img = $(this).data('img');
         $('#raw-mat-item-code').text(item_code);
         $('#raw-mat-description').text(description);

         $('#a-img').attr('href', img);
         $('#b-img').attr('src', img);

         $.ajax({
            url: "/get_available_warehouse_qty/" + item_code,
            type:"GET",
            success: function(data){
               $('#change-raw-mat-modal .inv-list').html(data);
            }
         });

         get_raw_actual_qty(item_code,  $('#raw-mat-warehouse').text());

         $('#input-raw-mat').val(item_code);
      });

      $('#input-raw-mat').keyup(function(e){
         e.preventDefault();
         var q = $(this).val();
         $.ajax({
            url: "/assembly/get_raw_materials_item/" + $('#item-classification-raw-mat').val() + '?autocomplete=1',
            type:"GET",
            data: {q: q},
            success: function(data){
               $('#autocomplete-box').show();
               $('#autocomplete-box').html(data);
            }
         });
      });

      function get_raw_actual_qty(item_code, warehouse){
         $.ajax({
            url: "/get_actual_qty/" + item_code + "/" + warehouse,
            type:"GET",
            success: function(data){
               var qty = parseFloat(data) + parseInt(0);
               $('#raw-mat-actual-qty').text(qty);
               $('#raw-mat-warehouse').text(warehouse);
            }
         });
      }

      $('#change-raw-mat-frm').submit(function(e){
         e.preventDefault();
         var req_url = $('#req-url-val').val();

         $.ajax({
            url: "/assembly/submit_change_raw_material",
            type:"POST",
            data: $(this).serialize(),
            success: function(data){
               if(data.success == 0){
                  showNotification("danger", data.message, "now-ui-icons travel_info");
                  return false;
               }
               
               var t = $('.tr-' + data.values.parent);
               t.find('.item-code').eq(0).text(data.values.item_code);
               t.find('.item-description').eq(0).text(data.values.description);
               t.find('.item-classification').eq(0).text(data.values.item_classification);
               t.find('.source-wh-select').eq(0).val(data.values.source_warehouse);
               t.find('.stock-uom').eq(0).text(data.values.stock_uom);
               t.find('.item-name').eq(0).text(data.values.item_name);

               load_req_items(req_url);

               $('#change-raw-mat-modal').modal('hide');
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

      $('.close-wizard-btn').click(function(e){
         e.preventDefault();
         location.reload();
      });

      function get_warehouse(item_classification, type){
         var row = '';
         $.ajax({
            url: "/get_warehouses/Wiring and Assembly/" + item_classification,
            type:"GET",
            datatype: "json",
            async: false,
            success: function(data){
               $.each(data, function(i, v){
                  // if(type == 'source'){
                     // row += '<option value="' + v.source_warehouse + '">' + v.source_warehouse + '</option>';
                  // }else{
                     row += '<option value="' + v.warehouse + '">' + v.warehouse + '</option>';
                  // }
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

      if ($('#ref-no').val()) {
         submit_somr_form();
      }

      function submit_somr_form() {
         var items_from = $('#items-from').val();
         $.ajax({
            url: "/assembly/get_reference_details/" + items_from + "/" + $('#reference-no').val(),
            type:"GET",
            data: $('#get-somr-frm').serialize(),
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
      }

      $('#get-somr-frm').submit(function(e){
         e.preventDefault();
         submit_somr_form();
      });

      // get part
      var a1 = 0;
      $('#get-parts').click(function(){
         $('.so-details-div').html($('#so-details-tbl').html());
         a1++;
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

         if(a1 == 1){
            $.ajax({
               url: "/assembly/get_parts",
               type:"GET",
               data: {so: so, bom: bom, idx: idx, qty: qty, item_reference_id: item_reference_id, delivery_date: delivery_date},
               success:function(data){
                  if(data.message) {
                     showNotification("danger", data.message, "now-ui-icons travel_info");
                     return false;
                  }

                  $('#parts-list-div').html(data);

                  if (bom.length > 0) {
                     $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
                  }else{
                     showNotification("danger", 'No Item(s) Found.', "now-ui-icons travel_info");
                     a1 = 0;
                  }
                  window.location.hash = '#next';
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  if(jqXHR.status == 401) {
                     showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
                  }
                  a1 = 0;
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
               },
            });
         }
      });

      $(document).on("click", ".review-bom-row", function(e){
         e.preventDefault();
         $.ajax({
            url: "/view_bom_for_review/" + $(this).data('bom'),
            type:"GET",
            data: {operation_name: 'Assembly'},
            success:function(data){
               if(data.message) {
                  showNotification("danger", data.message, "now-ui-icons travel_info");
                  return false;
               }

               $('#review-bom-details-div').html(data);
               $('#review-bom-modal').modal('show');
            },
            error: function(jqXHR, textStatus, errorThrown) {
               if(jqXHR.status == 401) {
                  showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
               }
               
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
            }
         });
         
         $('#review-bom-modal .modal-title').html('Review & Finalize BOM [' + $(this).data('bom') + ']');
         $('#review-bom-modal #bom-idx').text($(this).data('idx'));
      });

      var a2 = 0;
      $('.get-parts-prodorder').click(function(e){
         e.preventDefault();
         a2++;
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
            // var wip_wh = s_wh;
            // var fg_wh = targetwh;
            var prod_order = $(this).find('.production-order').eq(0).text();
            if (prod_order) {
               // wip_wh = '<option selected>' + $(this).find('.wip-warehouse').eq(0).text() + '</option>';
               s_wh = '<option selected>' + $(this).find('.s-warehouse').eq(0).text() + '</option>';
            }

            if (prod_order) {
               // fg_wh = '<option selected>' + $(this).find('.fg-warehouse').eq(0).text() + '</option>';
               t_wh = '<option selected>' + $(this).find('.fg-warehouse').eq(0).text() + '</option>';
            }

            var disable_sel = (prod_order) ? 'disabled' : null;

            var disabled = parseInt($(this).find('.available-qty').eq(0).text()) <= 0 ? 'disabled' : null;

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
                  '<div class="form-group" style="margin: 0;"><input type="text" value="' + $(this).find('.available-qty').eq(0).text() + '" class="form-control form-control-lg qty" style="text-align: center; font-size: 11pt;"></div>' +
               '</td>' +
               '<td class="text-center">' +
                  '<div class="input-group" style="margin: 0;"><input type="text" class="form-control form-control-lg date-picker planned-date" style="text-align: center; font-size: 11pt;" value="' + $(this).find('.planned-start-date').text() + '"><div class="input-group-append"><button class="btn btn-info view-sched-task" type="button"><i class="now-ui-icons ui-1_zoom-bold"></i></button></div></div></td>' +
               // '<td class="text-center"><div class="form-group" style="margin: 0;"><select class="form-control form-control-lg source" '+disable_sel+'>' + s_wh + '</select></div></td>' +
               '<td class="text-center"><div class="form-group" style="margin: 0;"><select class="form-control form-control-lg target">' + t_wh + '</select></div></td>' +
               '<td class="text-center">' +
                  '<div class="btn-group">' +
                     '<button type="button" class="btn btn-primary create-production-btn" ' + disabled + ' >' +
                        '<i class="now-ui-icons ui-1_simple-add"></i> Production Order' +
                     '</button>' +
                  '</div>' +
                  '</td>' +
               '</tr>';

            n++;
         });

         $('#parts-production-tbl tbody').append(row);
   
         if(a2 == 1){
            $('.nav-tabs li > .active').parent().next().find('a[data-toggle="tab"]').tab('show');
         }else{
            showNotification("danger", 'No Item(s) Found.', "now-ui-icons travel_info");
            a2 = 0;
         }

         window.location.hash = '#next';
      });

      $(document).on('click', '#submit-bom-review-btn', function(){
         var bombtn = $(this).data('id');
         var idx = $('#review-bom-modal #bom-idx').text();

         var operation = 'Wiring and Assembly';

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
               if (data.status == 0) {
                  showNotification("danger", data.message, "now-ui-icons travel_info");
                  return false;
               }

               $('#review-bom-modal').modal('hide');
               $('#parts-list').find('#'+idx+''+bombtn).removeClass('unchecked').addClass('now-ui-icons ui-1_check text-success');
               showNotification("success", data.message, "now-ui-icons ui-1_check");
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
            case 'a4':
               a4 = 0;
               break;
            default:
         }

         window.location.hash = '#next';
      });

      var a3 = 0;
      $('#get-req-items').click(function(){
         var production_order = [];
         var parent_code = [];
         a3++;
         $("#parts-production-tbl > tbody > tr").each(function () {
            str = $.trim($(this).find('.btn').eq(1).text());
            if (str.indexOf('PROM-') > -1) {
              production_order.push(str);
            }
         });

         if(a3 == 1){
            $.ajax({
               url: "/assembly/get_production_req_items",
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
                     showNotification("danger", 'No Item(s) Found.', "now-ui-icons travel_info");
                     a3 = 0;
                  }
                  window.location.hash = '#next';
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  if(jqXHR.status == 401) {
                     showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
                  }
                  a3 = 0;
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
               },
            });
         }
      });

      
      var a4 = 0;
      $('.finish-btn').click(function(e){
         e.preventDefault();
         a4++;
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

         if(a4 == 1){
            $.ajax({
               url: "/production_planning_summary",
               type:"GET",
               data: {
                  production_orders: production_orders,
                  order_id: order_details.split(' ')[3]
               },
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
                  a4 = 0;
                  console.log(jqXHR);
                  console.log(textStatus);
                  console.log(errorThrown);
               },
            });
         }
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

      $(document).on('change', '.source-wh-select', function(){
         var item = $(this).closest('tr').find('.item-code').text();
         var warehouse = $(this).val();
         var $row = $(this).closest('tr');

         var item_group = $(this).find(':selected').data('item-group');

         var item_groups_mr = ["Raw Material"];

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

               if((item_groups_mr.indexOf(item_group) > -1) && (on_stock < req)){
                  $row.addClass('item-row');
               }else{
                  $row.removeClass('item-row');
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
            source: $row.find('.source').val(),
            target: $row.find('.target').val(),
            reference_no: $row.find('.reference-no').text(),
            bom: $row.find('.bom').text(),
            operation: 'Wiring and Assembly',
            item_reference_id: $row.find('.item-reference-id').text(),
            delivery_date: $row.find('.delivery-date').text(),
         }

         $btn.attr('disabled', true);

         $.ajax({
            url: "/create_production_order",
            type:"POST",
            data: data,
            success:function(data){
               if (data.success < 1) {
                  showNotification("danger", data.message, "now-ui-icons travel_info");
                  $btn.removeAttr('disabled');

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
               if(jqXHR.status == 401) {
                  showNotification("danger", 'Session Expired. Please refresh the page and login to continue.', "now-ui-icons travel_info");
               }
               
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

         $('#view-bom-modal .modal-title').html(sel_val);
         $('#view-bom-modal').modal('show');
      });

      

      $(document).on('click', '.create-mr-btn', function(e){
         e.preventDefault();
         var $btn = $(this);
         var $row = $(this).closest('tr');
         var reference_no = $row.find('.reference-no').text();
         var item_code = $row.find('.item-code').text();
         var description = $row.find('.item-description').text();
         var warehouse = $row.find('.source-wh-select').val();
         var qty = $row.find('.balance-qty').text();
         var stock_uom = $row.find('.stock-uom').text();

         data = {
            reference_no: reference_no,
            item_code: item_code,
            description: description,
            warehouse: warehouse,
            qty: qty,
            stock_uom: stock_uom,
         }

         $.ajax({
            url: "/create_material_request",
            type:"POST",
            data: data,
            success:function(response){
               $btn.removeClass('btn-info create-mr-btn').addClass('btn-success');
               $btn.html('<i class="now-ui-icons ui-1_check"></i> ' + response);
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
               t_warehouse: t_warehouse,
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
            //data: data,
            success:function(response){
               if (response.success == 0) {
                  showNotification("danger", response.message, "now-ui-icons travel_info");

                  return false;
               }
               // $btn.removeClass('btn-primary create-ste-btn').addClass('btn-success');
               // $btn.html('<i class="now-ui-icons ui-1_check"></i> ' + response);
               if (response.error) {
                  showNotification("danger", 'There was a problem creating stock entry.', "now-ui-icons travel_info");

                  return false;
               }

               $btn.removeClass('btn-primary create-ste-btn').addClass('btn-success');
               $btn.html('<i class="now-ui-icons ui-1_check"></i> STE');
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
   });
</script>
<script>
$('#btnPrint').on("click", function () {
      $('#printmodalbody').printThis({
      });
    });
</script>
@endsection