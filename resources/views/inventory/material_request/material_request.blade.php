@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'pageHeader' => 'Material Request',
    'activePage' => 'material_request',
    'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header" style="margin-top: -73px;">
  <div class="header text-center">
    <div class="row">
      <div class="col-md-12">
        <table style="text-align: center; width: 100%; font-size: 10px;">
          <tr>
            <td style="width: 27%; border-right: 5px solid white;">
              <h2 class="title">
                <div class="pull-right" style="margin-right: 20px;">
                  <span style="display: block; font-size: 20pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 8pt;">{{ date('l') }}</span>
                </div>
              </h2>
            </td>
            <td style="width: 14%; border-right: 5px solid white;">
              <h3 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h3>
            </td>
            <td style="width: 59%">
              <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Material Request</h2>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="content" style="margin-top: -118px;">
  <div class="row">
    <div class="col-md-2">
      <div class="card" style="background-color: #0277BD;" >
        <div class="card-body" style="padding-bottom: 0;">
          <div class="row">
            <div class="col-md-12" style="margin-top: -10px;">
              <h5 class="text-white" style="font-size: 13pt; margin-bottom: 5px;">Filter/s</h5>
            </div>
          </div>
          <div class="row" style="background-color: #ffffff; padding-top: 9px; height: 500px;">
           <div class="col-md-12" style="margin: 0;height: 780px;" id="filter_material_purchase_request">
                                              <span style="display: block; font-size: 9pt; margin-top: -8px;"></span>
                                                <div class="form-group">
                                                  <label style="color: black;">Transaction Date:</label>
                                                  <input type="text" class="date form-control" name="daterange" autocomplete="off" placeholder="Select Date From" id="purchase_daterange" value="" style="text-align:center;display:inline-block;width:100%;height:30px;" onchange="tbl_log_fabrication()">
                                                </div>
                                                  <div class="form-group" style="margin-top: -14px;">
                                                      <label style="color: black;">Item Code</label>
                                                      <select class="form-control text-center sel5 " name="item_code" id="purchase_item_code">
                                                          <option value="" selected> Select Item Code</option>
                                                          @foreach($item_list as $row)
                                                          <option value="{{$row->name}}">{{$row->name}}</option>
                                                          @endforeach
                                                        </select>
                                                  </div>

                                                  <div class="form-group">
                                                      <label style="color: black;">Sales Order</label>
                                                       <select class="form-control text-center sel5 " name="prod" id="purchase_so">
                                                          <option value="" selected> Select Sales Order</option>
                                                          @foreach($so_list as $row)
                                                          <option value="{{$row->name}}">{{$row->name}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                  <div class="form-group">
                                                      <label style="color: black;">Customer</label>
                                                      <select class="form-control text-center sel5 " name="customer" id="purchase_customer">
                                                          <option value="" selected> Select Customer</option>
                                                          @foreach($customers as $customer)
                                                            <option value="{{ $customer }}">{{ $customer }}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                   <div class="form-group">
                                                      <label style="color: black;">Project</label>
                                                      <select class="form-control text-center sel5 " name="customer" id="purchase_project">
                                                          <option value="" selected> Select Project</option>
                                                          @foreach($projects as $project)
                                                          <option value="{{ $project }}">{{ $project }}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                                   
                                                   
                                                   <div class="form-group">
                                                      <label style="color: black;">Status</label>
                                                      <select class="form-control text-center sel5 " name="qa_status" id="purchase_status">
                                                          <option value="" selected> Select Status</option>
                                                          @foreach($mreq_stat as $row)
                                                          <option value="{{$row->status}}">{{$row->status}}</option>
                                                          @endforeach
                                                        </select>
                                                   </div>
                                            </div>
          </div>
        </div>
      </div>

      
    </div>
    <div class="col-md-10">
      <div class="card" style="background-color: #0277BD;" >
        <div class="card-body" style="padding-bottom: 0;">
          <div class="row">
            <div class="col-md-6">
              
              <h5 class="text-white font-weight-bold align-middle">Material Request</h5>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="text"  class="form-control" placeholder="Search Material Request" id="material-request-purchase-filter" style="background-color: #ffffff;">
              </div>
            </div>
          </div>
          <div class="row" style="background-color: #ffffff;height: auto; min-height: 500px;">
            <div class="card card-nav-tabs card-plain">
              <div class="card-body ">
                <div class="col-md-12" id="inventory_history">
                  <div class="row">
                     <div class="col-md-12">
                      <button class="btn btn-primary add-material-request-btn" id="add-material-request-btn" style="float:right;margin-top:-5px;">+ Material Request</button>
                     </div>      
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                       <div class="tbl_material_request" id="tbl_material_request"></div>
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
</div>
<!-- Modal Manual Create Production Prder -->
<div class="modal fade" id="add-material-request-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
    <form action="/save_material_purchase" method="post" autocomplete="off" id="add-material-request-frm">
      @csrf
      <input type="hidden" name="operation" value="Fabrication">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i> Material Request for Purchase<br>
               </h5>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Purchase Request</label>
                    <select class="form-control sel4" id="purchase_type" name="purchase_type">
                      <option value="Local">Local</option>
                      <option value="Imported">Imported</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6" id="input-so-div">
                  <div class="form-group">
                    <label>Sales Order</label>
                    <select class="form-control sel-reference-no sel-so sel4" id="sel-so" name="sales_order" data-type="SO" required>
                      <option value="">Select SO</option>
                      @forelse($so_list as $so)
                      <option value="{{ $so->name }}">{{ $so->name }}</option>
                      @empty
                      <option value="">No Sales Order(s) Found.</option>
                      @endforelse
                    </select>
                  </div>
                </div>
                <div class="col-md-12" style="margin-top: -6px;">
                  <div class="form-group">
                    <label>Customer</label>
                    <input type="text" name="customer" class="form-control readonly">
                  </div>
                </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-12">
                  <div class="form-group">
                    <label>Required Date</label>
                    <input type="input" name="required_date_all" id="required_date_all" class="form-control date-picker" required>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    <label>Project</label>
                    <input type="text" name="project" class="form-control readonly">
                  </div>
                </div>
            </div>
          </div>
          <div class="col-md-12">
                  <a href="#" class="btn btn-primary add-row">
                    <i class="now-ui-icons ui-1_simple-add"></i>Add
                  </a>
                  <table class="table" id="material-purchase-table" style="font-size: 10px;border: 1px solid #ABB2B9;">
                     <thead style="border: 1px solid #ABB2B9;">
                        <tr style="border: 1px solid #ABB2B9;">
                           <th style="border: 1px solid #ABB2B9;width: 5%; text-align: center;font-weight: bold;">No.</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Item Code</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Quantity</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Stock UOM</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">For Warehouse</th>
                           <th style="border: 1px solid #ABB2B9;width: 18%; text-align: center;font-weight: bold;">Required Date</th>
                           <th style="border: 1px solid #ABB2B9;width: 5%; text-align: center;font-weight: bold;"></th>
                        </tr>
                     </thead>
                     <tbody class="table-body text-center" style="border: 1px solid #ABB2B9;">
                        <tr>
                           <td>1</td>
                           <td>
                                <select class="form-control sel4" name="new_item_code[]" required>
                                  <option value="">Select Item Code</option>
                                  @forelse($item_list as $item)
                                  <option value="{{ $item->name }}">{{ $item->name }}</option>
                                  @empty
                                  <option value="">No Item(s) Found.</option>
                                  @endforelse
                                </select>

                           </td>
                           <td>
                                <input type="text" name="qty[]" class="form-control input-multi" required>
                           </td>
                           <td>
                                <input type="text" name="uom" class="form-control input-multi readonly" id="">
                           </td>
                           
                           <td>
                              <select class="form-control sel4" name="new_warehouse[]" required>
                                @forelse($warehouse_list as $warehouse)
                                <option value="{{ $warehouse }}">{{ $warehouse }}</option>
                                @empty
                                <option value="">No Warehouse(s) Found.</option>
                                @endforelse
                              </select>
                           </td>
                           <td>
                              <input type="input" name="required_date[]" class="form-control date-picker input-multi input-date" required>

                           </td>
                           <td><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>
                        </tr>
                     </tbody>
                  </table>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info">Submit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="view-material-request-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 60%;">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #0277BD;color:white;">
               <h5 class="modal-title" id="modal-title ">
                  <i class="now-ui-icons"></i>Material Request Details<br>
               </h5>
            </div>
        <div class="modal-body">
          <div id="tbl_view_purchase_details" class="table-responsive"></div>
        </div>
      </div>
  </div>
</div>
<div class="modal fade" id="cancel-material-request-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/cancel_material_purchase_request" method="POST" id="cancel-material-request-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title">
                <span>Delete Item Warehouse</span>
                <span class="sampling-delete-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <div class="form-group" style="font-size: 12pt;">
                        <div class="tbl_view_purchase_details"> <span>Are you sure you want to cancel </span><span id="purchase-label-cancel" style="font-weight: bold; padding-left: 2px;"></span>?</div>
                        <input type="hidden" name="purchase_id" id="purchase_id">
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer" style="padding: 5px 8px;">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
         </div>
      </form>
   </div>
</div>

<style>
  .input-multi{
    height: 35px;
    font-size: 12px;
    text-align: center;
  }
 .modal-lg-custom {
      max-width: 80% !important;
  }
  #add-material-request-modal .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #filter_material_purchase_request .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #add-material-request-modal .form-control:hover, #add-material-request-modal .form-control:focus, #add-material-request-modal .form-control:active {
    box-shadow: none;
  }
  #add-material-request-modal .form-control:focus {
    border: 1px solid #34495e;
  }

  .select2.select2-container {
    width: 100% !important;
  }

  .select2.select2-container .select2-selection {
    border: 1px solid #ccc;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    height: 34px;
    margin-bottom: 15px;
    outline: none;
    transition: all 0.15s ease-in-out;
  }

  .select2.select2-container .select2-selection .select2-selection__rendered {
    color: #333;
    line-height: 32px;
    padding-right: 33px;
  }

  .select2.select2-container .select2-selection .select2-selection__arrow {
    background: #f8f8f8;
    border-left: 1px solid #ccc;
    -webkit-border-radius: 0 3px 3px 0;
    -moz-border-radius: 0 3px 3px 0;
    border-radius: 0 3px 3px 0;
    height: 32px;
    width: 33px;
  }

  .select2.select2-container.select2-container--open .select2-selection.select2-selection--single {
    background: #f8f8f8;
  }

  .select2.select2-container.select2-container--open .select2-selection.select2-selection--single .select2-selection__arrow {
    -webkit-border-radius: 0 3px 0 0;
    -moz-border-radius: 0 3px 0 0;
    border-radius: 0 3px 0 0;
  }

  .select2.select2-container.select2-container--open .select2-selection.select2-selection--multiple {
    border: 1px solid #34495e;
  }

  .select2.select2-container.select2-container--focus .select2-selection {
    border: 1px solid #34495e;
  }

  .select2.select2-container .select2-selection--multiple {
    height: auto;
    min-height: 34px;
  }

  .select2.select2-container .select2-selection--multiple .select2-search--inline .select2-search__field {
    margin-top: 0;
    height: 32px;
  }

  .select2.select2-container .select2-selection--multiple .select2-selection__rendered {
    display: block;
    padding: 0 4px;
    line-height: 29px;
  }

  .select2.select2-container .select2-selection--multiple .select2-selection__choice {
    background-color: #f8f8f8;
    border: 1px solid #ccc;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    margin: 4px 4px 0 0;
    padding: 0 6px 0 22px;
    height: 24px;
    line-height: 24px;
    font-size: 12px;
    position: relative;
  }

  .select2.select2-container .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
    position: absolute;
    top: 0;
    left: 0;
    height: 22px;
    width: 22px;
    margin: 0;
    text-align: center;
    color: #e74c3c;
    font-weight: bold;
    font-size: 16px;
  }

  .select2-container .select2-dropdown {
    background: transparent;
    border: none;
    margin-top: -5px;
  }

  .select2-container .select2-dropdown .select2-search {
    padding: 0;
  }

  .select2-container .select2-dropdown .select2-search input {
    outline: none;
    border: 1px solid #34495e;
    border-bottom: none;
    padding: 4px 6px;
  }

  .select2-container .select2-dropdown .select2-results {
    padding: 0;
  }

  .select2-container .select2-dropdown .select2-results ul {
    background: #fff;
    border: 1px solid #34495e;
  }

  .select2-container .select2-dropdown .select2-results ul .select2-results__option--highlighted[aria-selected] {
    background-color: #3498db;
  }
  .inline-style{
    padding: 7px 5px 6px 12px;
  }
</style>
@endsection


@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>

<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />

<script type="text/javascript">
$(document).ready(function(){
  // purchase_list();

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $(document).on('click', '#add-material-request-btn', function(){
    $('#add-material-request-modal').modal('show');
    $('#target-wh option:contains(A Warehouse P2 - FI)').prop({selected: true});
    $('#target-wh').trigger('change');
    $("#material-purchase-table tbody").empty();
      

  });
  $('.sel4').select2({
    dropdownParent: $("#add-material-request-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false
  });
  $('.sel5').select2({
    dropdownParent: $("#filter_material_purchase_request"),
    dropdownAutoWidth: false,
    width: '100%',
    cache: false,
    placeholder: "",
    allowClear: true
  });

  $('#purchase_daterange').daterangepicker({
    "showDropdowns": true,
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    "linkedCalendars": false,
    "autoUpdateInput": false,
    "alwaysShowCalendars": true,
  }, function(start, end, label) {
    console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    filter_from_purchase_date_range();
  });

   $('#purchase_daterange').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
      filter_from_purchase_date_range();
  });
   

  $('.readonly').each(function(){
    $(this).attr('readonly','readonly');
  });
  $('.sel-reference-no').change(function(){
    var ref_type = $(this).data('type');
    var ref_no = $(this).val();
    $.ajax({
      url:"/get_reference_details/" + ref_type + "/" + ref_no,
      type:"GET",
      success:function(response){
        if (response.success == 0) {
          showNotification("danger", 'No BOM found for Item ' + item_code, "now-ui-icons travel_info");
        }else{
          $('#add-material-request-modal input[name="customer"]').val(response.customer);
          $('#add-material-request-modal input[name="project"]').val(response.project);
          var classification = (response.purpose) ? response.purpose : response.sales_type;
          classification = (classification != 'Sample') ? 'Customer Order' : 'Sample';
          $('#add-material-request-modal input[name="classification"]').val(classification);
        }
      }
    });
  });
  $('#sel-item').change(function(e){
    e.preventDefault();
    if ($(this).val()) {
      $.ajax({
        url:"/get_item_details/" + $(this).val(),
        type:"GET",
        success:function(response){
          if (response.success == 0) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            $('#add-material-request-modal textarea[name="description"]').text(response.description);
            $('#add-material-request-modal input[name="stock_uom"]').val(response.stock_uom);
            $('#add-material-request-modal input[name="item_classification"]').val(response.item_classification); 
          }
        }
      });
    }
  });
  $('.date-picker').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });
  $(document).on('click', '#purchase_list_pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         purchase_list(page);
  });
  $('#add-material-request-frm').submit(function(e){
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
            $('#add-material-request-modal').modal('hide');
            $('#add-material-request-frm').trigger("reset");
            purchase_list();

          }
        }
      });
    });
  $('#cancel-material-request-frm').submit(function(e){
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
            $('#cancel-material-request-modal').modal('hide');
            $('#cancel-material-request-frm').trigger("reset");
            purchase_list();

          }
        }
      });
    });
  $(document).on('click', '.cancel-purchase-btn', function(e){
     e.preventDefault();
      var id = $(this).attr("data-id");
      $('#purchase_id').val(id);
      $('#purchase-label-cancel').text(id);
      $('#cancel-material-request-modal').modal('show');

  });
  $(document).on('click', '.view_purchase_detail_btn', function(){
    var id = $(this).attr("data-id");
    $.ajax({
        url:"/get_material_request_for_purchase/"+id,
        type:"GET",
        success:function(data){
          $('#tbl_view_purchase_details').html(data);
        }
      });
    $('#view-material-request-modal').modal('show');
    });

  $(document).on('keyup', '#material-request-purchase-filter', function(){
    var query = $(this).val();
    purchase_list(1, query);
  });

     
});



</script>
<script>
   $(document).ready(function(){
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
   });
</script>
<script type="text/javascript">
  function purchase_list(page, query){
    $.ajax({
        url:"/get_tbl_material_request/?page="+page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
          $('#tbl_material_request').html(data);
        }
      });
  }
</script>
<script type="text/javascript">
  $(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();
      });
</script>
<script type="text/javascript">
  $('#add-material-request-modal .add-row').click(function(e){
         e.preventDefault();
         var col1 = '';
         var col3 = '';
         var datee = ($('#required_date_all').val() == '')? "": $('#required_date_all').val();
         $.ajax({
            url: "/get_selection_box_in_item_code_warehouse",
            type:"get",
            cache: false,
            success: function(response) {
               col1 += '<option value="none">Select Item Code</option>';
               col3 += '<option value="none">Select Warehouse</option>';
               $.each(response.item_list, function(i, d){
                  col1 += '<option value="' + d.name + '">' + d.name + '</option>';
               });
               $.each(response.warehouse, function(i, d){
                  col3 += '<option value="' + d + '">' + d + '</option>';
               });
               var thizz = document.getElementById('material-purchase-table');
               var id = $(thizz).closest('table').find('tr:last td:first').text();
               var validation = isNaN(parseFloat(id));
               if(validation){
                var new_id = 1;
               }else{
                var new_id = parseInt(id) + 1;
               }
               var len2 = new_id;
               var id_unique="purchase"+len2;
               var tblrow = '<tr class="inline-style">' +
                  '<td class="inline-style">'+len2+'</td>' +
                  '<td class="inline-style"><select class="form-control sel4 onchange-selection" name="new_item_code[]" required data-idcolumn='+id_unique+'>'+col1+'</select></td>' +
                  '<td class="inline-style"><input type="text" name="qty[]" class="form-control input-multi" required></td>' +
                  '<td class="inline-style"><input type="text" name="uom" class="form-control input-multi readonly-input e" style="text-center:center;" id='+ id_unique +'></td>' +
                  '<td class="inline-style"><select class="form-control sel4" name="new_warehouse[]" required>'+col3+'</select></td>' +
                  '<td class="inline-style"><input type="input" name="required_date[]" class="form-control  date-picker input-multi input-date" required value='+datee+' ></td>' +
                  '<td class="inline-style"><a class="delete"><i class="now-ui-icons ui-1_simple-remove" style="color: red;"></i></a></td>' +
                  '</tr>';

               $("#add-material-request-modal #material-purchase-table").append(tblrow);
               $('.sel4').select2({
                    dropdownParent: $("#add-material-request-modal"),
                    dropdownAutoWidth: false,
                    width: '100%',
                    cache: false
                  });
               $('.date-picker').datepicker({
                  'format': 'yyyy-mm-dd',
                  'autoclose': true
                });
               $('.readonly-input').attr('readonly', true);
               
               autoRowNumberAddKPI_purchase();
            },
            error: function(response) {
               alert('Error fetching Designation!');
            }
         });
      });
    $(document).on('change', '.onchange-selection', function(){
           var first_selection_data = $(this).val();
           var id_for_second_selection = $(this).attr('data-idcolumn');
           var format_id_for_second_selection = "#"+id_for_second_selection;
            $.ajax({
            url:"/get_uom_item_selected_in_purchase/"+first_selection_data,
            type:"GET",
            success:function(data){
              $(format_id_for_second_selection).val(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
      });
    $(document).on('change', '#required_date_all', function(){
           var datee = $("#required_date_all").val();
          $('.input-date').val(datee);
      });
</script>
<script type="text/javascript">
    function autoRowNumberAddKPI_purchase(){
         $('#add-material-request-modal #material-purchase-table tbody tr').each(function (idx) {
            $(this).children("td:eq(0)").html(idx + 1);
         });
      }
</script>
<script type="text/javascript">
      function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 3000,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }
</script>
<script>
$(document).on('change', '.sel5', function(event){
  var date = $('#purchase_daterange').val();
  var startDate = $('#purchase_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
  var endDate = $('#purchase_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
  var data = {
        item_code: $('#purchase_item_code').val(),
        so:$('#purchase_so').val(),
        customer: $('#purchase_customer').val(),
        status: $('#purchase_status').val(),
        project: $('#purchase_project').val()
      }
 
    $.ajax({
          url:"/tbl_filter_material_purchase_request/"+ startDate +"/" +endDate,
          type:"GET",
          data: data,
          success:function(data){
            
            $('#tbl_material_request').html(data);
          }
        });
  
  });
</script> 
<script>
function filter_from_purchase_date_range(){
  var date = $('#purchase_daterange').val();
  var startDate = $('#purchase_daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
  var endDate = $('#purchase_daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
  var data = {
        item_code: $('#purchase_item_code').val(),
        so:$('#purchase_so').val(),
        customer: $('#purchase_customer').val(),
        status: $('#purchase_status').val(),
        project: $('#purchase_project').val()
      }

    $.ajax({
          url:"/tbl_filter_material_purchase_request/"+ startDate +"/" +endDate,
          type:"GET",
          data: data,
          success:function(data){
            
            $('#tbl_material_request').html(data);
          }
        });
  
  };
</script>
@endsection