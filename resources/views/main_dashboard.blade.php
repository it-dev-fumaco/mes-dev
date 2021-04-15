@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'main_dashboard',
])

@section('content')
@include('modals.view_for_feedback_list_modal')
<div class="panel-header">
   <div class="header text-center"> 
      <div class="row">
        <div class="col-md-12" style="margin-top:-48px;">
            <table style="text-align: center; width: 100%;">
              <tr>
                <td style="width: 25%; border-right: 5px solid white;">
                  <h5 class="title">
                    <div class="pull-right" style="margin-right: 20px;">
                      <span style="display: block; font-size: 18pt;">{{ date('M-d-Y') }}</span>
                      <span style="display: block; font-size: 11pt;">{{ date('l') }}</span>
                    </div>
                  </h5>
                </td>
                <td style="width: 14%; border-right: 5px solid white;">
                  <h5 class="title" style="margin: auto; font-size: 30pt;"><span id="current-time">--:--:-- --</span></h5>
                </td>
                <td style="width: 50%">
                  <h4 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Production Dashboard</h4>
                  <span class="title text-left" style="margin-left: 20px; margin: auto 20pt;float:left;">
                    <i>{{ Auth::user()->employee_name }} - {{ $user_details->designation_name }}</i>
                  </span>
                </td>
              </tr>
            </table>
            <input type="hidden" name="date_today" id="date_today" value="{{ date('Y-m-d') }}">
        </div>
      </div>
   </div>
</div>

<div class="content" style="margin-top: -134px;">
  <div class="row">
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-12 p-0">
          <table>
            <tr>
              @php
              $a = array_intersect($mes_user_operations, ['Painting', 'Fabrication', 'Wiring and Assembly']);
              @endphp
              @if (count($a) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="/item_feedback" class="text-center">
                    <img src="{{ asset('storage/Main Icon/production_orders.png') }}" style="width: 45%; height: 60%; margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">Production Orders</footer>
                </div>
              </td>
              @endif
              @php
              $b = array_intersect($mes_user_operations, ['Fabrication']);
              @endphp
              @if (count($b) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="/wizard" class="text-center"><img src="{{ asset('storage/Main Icon/production_planning.png') }}"  style="width:45%; height:60%;margin-left: auto;
                    margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">Planning</footer>
                </div>
              </td>
              @endif
              @php
              $b1 = array_intersect($mes_user_operations, ['Wiring and Assembly']);
              @endphp
              @if (count($b1) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="/assembly/wizard" class="text-center"><img src="{{ asset('storage/Main Icon/production_planning.png') }}"  style="width:45%; height:60%;margin-left: auto;
                    margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">Planning</footer>
                </div>
              </td>
              @endif
              @php
              $c = array_intersect($mes_user_operations, ['Fabrication', 'Painting', 'Wiring and Assembly']);
              if(in_array('Fabrication', $mes_user_operations)){
                $link = '/production_schedule/1';
              }else if(in_array('Wiring and Assembly', $mes_user_operations)){
                $link = '/production_schedule/3';
              }else{
                $link = '/production_schedule_painting';
              }
              @endphp
              @if (count($c) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="{{ $link }}" class="text-center"><img src="{{ asset('storage/Main Icon/production_order_schedule.png') }}"  style="width:45%; height:60%;margin-left: auto;
                    margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">Scheduling</footer>
                </div>
              </td>
              @endif
              @php
              $d = array_intersect($mes_user_operations, ['Fabrication', 'Wiring and Assembly']);
              @endphp
              @if (count($d) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="/stock_entry" class="text-center"><img src="{{ asset('storage/Main Icon/material_requests.png') }}"  style="width:45%; height:60%;margin-left: auto;
                    margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">Widthrawal Slips</footer>
                </div>
              </td>
              @endif
              @php
              $e = array_intersect($mes_user_operations, ['Fabrication', 'Painting', 'Wiring and Assembly']);
              @endphp
              @if (count($e) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="/bom" class="text-center"><img src="{{ asset('storage/Main Icon/bom_list.png') }}"  style="width:45%; height:60%;margin-left: auto;
                    margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">BOM List </footer>
                </div>
              </td>
              @endif
              @php
              $f = array_intersect($mes_user_operations, ['Fabrication', 'Painting', 'Wiring and Assembly']);
              @endphp
              @if (count($f) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="/maintenance_request" class="text-center"><img src="{{ asset('storage/Main Icon/maintenance_requests.png') }}"  style="width:45%; height:60%;margin-left: auto;
                    margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">Maintenance Request</footer>
                </div>
              </td>
              @endif
              @php
              $h = array_intersect($mes_user_operations, ['Fabrication', 'Wiring and Assembly']);
              @endphp
              @if (count($h) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="/inventory" class="text-center"><img src="{{ asset('storage/Main Icon/inventory.png') }}"  style="width:45%; height:60%;margin-left: auto;
                    margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">Inventory</footer>
                </div>
              </td>
              @endif
              @php
              $i = array_intersect($mes_user_operations, ['Fabrication', 'Painting', 'Wiring and Assembly']);
              @endphp
              @if (count($i) > 0)
              <td style="width: 10%;">
                <div class="menu-box">
                  <a href="/report_index" class="text-center"><img src="{{ asset('storage/Main Icon/reports.png') }}"  style="width:45%; height:60%;margin-left: auto;
                    margin-right: auto; display: block; padding-top: 10px;"></a>
                  <footer class="hmt small text-center" style="padding: 10px 0;">Reports & Analysis</footer>
                </div>
              </td>
              @endif
            </tr>
          </table>
        </div>
        <div class="col-md-12 p-0 mt-2">
          <div class="card" style="background-color: #f39c12;">
            <div class="card-body pb-0 pt-0">
              <div class="row">
                <div class="col-md-12 p-2">
                  <h5 class="text-white font-weight-bold text-center m-0">On-Going Production Order(s)</h5>
                </div>
              </div>
              <div class="row" style="background-color: #ffffff;height: auto; min-height: 578px;">
                <div class="col-md-12 p-0">
                  <div id="table_orders"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="row">
        <div class="col-md-12">
          <table style="width: 100%;">
            <tr>
              <td style="width: 64%; padding-right: 1%" class="align-top">
                <div class="card">
                  <div class="card-header text-center p-1">
                    <h5 class="title m-0" style="font-size: 15pt;">Current Production Orders</h5>
                  </div>
                  <div class="card-body p-0 pb-3 text-center text-white" style="min-height: 150px; background-color: #263238;">
                    <div class="row">
                      <div class="col-md-12 pt-1">
                        <table style="width: 100%;" id="totals-table">
                          <col style="width: 33%;">
                          <col style="width: 33%;">
                          <col style="width: 33%;">
                          <tr>
                             <td class="align-top">
                                <span class="span-value text-center" id="pending_count" style="display:block;font-size:35pt;font-weight:bold;">0</span>
                                <span class="span-title text-center" style="display:block;font-size:10pt;">Pending</span>
                                <span class="span-value text-center" id="pending_count_qty" style="display:block;font-size:15pt;font-weight:bold;">0</span>
                                <span class="span-title text-center" style="display:block;font-size: 8pt;">Piece(s)</span>
                             </td>
                             <td class="align-top">
                                <span class="span-value text-center" id="inprogress_count" style="display:block;font-size:35pt;font-weight:bold;">0</span>
                                <span class="span-title text-center" style="display:block;font-size:10pt;">In Progress</span>
                                <span class="span-value text-center" id="inprogress_count_qty" style="display:block;font-size:15pt;font-weight:bold;">0</span>
                                <span class="span-title text-center" style="display:block;font-size:8pt;">Piece(s)</span>
                             </td>
                             <td class="align-top">
                                <span class="span-value text-center" id="reject_count" style="display:block;font-size:35pt;font-weight:bold;">0</span>
                                <span class="span-title text-center" style="display:block;font-size:10pt;">Rejects</span>
                             </td>
                          </tr>
                       </table>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
              <td style="width: 34%; padding: 0 0.5%" class="align-top">
                <div class="card">
                  <div class="card-header text-center text-white p-2" style="height: 37px; background-color: #28b463">
                    <h5 class="title m-0" style="font-size: 12pt;">Ready for Feedback</h5>
                  </div>
                  <div class="card-body p-0 pb-3 text-center" style="min-height: 150px; background-color: #263238;">
                    <div class="row">
                      <div class="col-md-12 pt-1 text-white">
                        <span class="span-value text-center" id="completed_count" style="display:block;font-size:37pt;font-weight:bold;">0</span>
                        <span class="span-title text-center" style="display:block;font-size:10pt;">Production Order(s)</span>
                        <span class="span-value text-center" id="completed_count_qty" style="display:block;font-size:15pt;font-weight:bold;">0</span>
                        <span class="span-title text-center" style="display:block;font-size:8pt;">Piece(s)</span>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </table>
        </div>
         <div class="col-md-12">
          <div class="card" style="height:540px;" style="overflow-y: auto;">
            <div class="card-header text-center text-white p-2" style="background-color: #3498db">
              <h5 class="title m-0">Notifications</h5>
            </div>
            <div class="table-full-width table-responsive" style="height: 450px;position: relative;" id="tbl_notif_dash"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="view-notifications-modal" tabindex="-1" role="dialog">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header text-white" style="background-color: #0277BD;">
	        <h5 class="modal-title" id="modal-title ">
	          <i class="now-ui-icons ui-1_bell-53"></i> Notifications
	        </h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <div class="row">
	          <div class="col-md-12">
	            <div class="table-full-width table-responsive" style="height: 600px; position: relative;" id="tbl-notifications"></div>
	          </div>
	        </div>
	      </div>
	      <div class="modal-footer p-2 pr-3">
	        <button type="button" class="btn btn-secondary m-0" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
  
  <style>
    .menu-box{
      display: inline-block;
      background-color: #ffffff; 
      border-radius: 10px;
      border: 1px solid #dddddd;
      width: 96%;
      margin: 2%;
    }

    @-webkit-keyframes blinker {
      from { background-color: #CD6155; }
      to { background-color: inherit; }
    }
    @-moz-keyframes blinker {
      from { background-color: #CD6155; }
      to { background-color: inherit; }
    }
    @-o-keyframes blinker {
      from { background-color: #CD6155; }
      to { background-color: inherit; }
    }
    @keyframes blinker {
      from { background-color: #CD6155; }
      to { background-color: inherit; }
    }
    @-webkit-keyframes blinker_change_code {
      from { background-color: #f39c12; }
      to { background-color: inherit; }
    }
    @-moz-keyframes blinker_change_code {
      from { background-color: #f39c12; }
      to { background-color: inherit; }
    }
    @-o-keyframes blinker_change_code {
      from { background-color: #f39c12; }
      to { background-color: inherit; }
    }
    @keyframes blinker_change_code {
      from { background-color: #f39c12; }
      to { background-color: inherit; }
    }
    .blink{
      text-decoration: blink;
      -webkit-animation-name: blinker;
      -webkit-animation-duration: 1s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }
    .blink_changecode{
      text-decoration: blink;
      -webkit-animation-name: blinker_change_code;
      -webkit-animation-duration: 1s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }

    .tab-heading--blue {
    background-color: #2196E3;
    color: #FFF;
  }
  .tab-heading--orange {
    background-color: #EA9034;
    color: #FFF;
  }
  .tab-heading--reddish {
    background-color: #E86B46;
    color: #FFF;
  }
  .tab-heading--teal {
    background-color: #22D3CC;
    color: #FFF;
  }
  .tab-heading--green {
    background-color: #8BC753;
    color: #FFF;
  }
  .tab-heading--gray {
    background-color: #808495;
    color: #FFF;
  }
  .tab-heading--ltgray {
    background-color: #F3F3F3;
    color: #242424;
  }
  
  </style>

  <div class="modal fade" id="review-bom-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 70%;">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bolder;">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="text" id="production-order-val" style="display: none;">
            <div id="review-bom-details-div"></div>
          </div>
      </div>
    </div>
  </div>

  <!-- Modal Create STE Production Order -->
<div class="modal fade" id="create-ste-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="#" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Stock Entry</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 offset-md-2 p-0">
              <div class="form-group text-center">
                <input type="hidden" name="production_order">
                <input type="hidden" name="item_code">
                <input type="hidden" name="manual" value="1">
                <p style="font-size: 12pt;" class="text-center m-0">Production Order</p>
                <p style="font-size: 12pt;" class="text-center m-0">Item Code</p>
                <p style="font-size: 12pt;" class="text-center m-0">
                  <span>Qty to Manufacture = </span><span class="font-weight-bold">0</span> <span class="font-weight-bold">UoM</span></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>
  <iframe src="#" id="iframe-print" style="display: none;"></iframe>
  
  @include('modals.stock_withdrawal_modal')
@endsection

@section('script')

<script>
  $(document).ready(function(){
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

    $('#completed_count').click(function(e){
      e.preventDefault();
      get_for_feedback_production(1);
      $('#view-for-feedback-list-modal').modal('show');
    });
    
    function get_for_feedback_production(page, query){
      $.ajax({
        url: "/production_order_list/Awaiting Feedback?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
          $('#view-for-feedback-list-table').html(data);
        }
      });
    }
    $('#reschedule_delivery_frm').submit(function(e){
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
            $('#reschedule-delivery-modal').modal('hide');
            get_for_feedback_production(1);
          }
        }
      });
    });
    $(document).on('click', '.for-feedback-production-pagination a', function(event){
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      get_for_feedback_production(page);
    });

    $(document).on('keyup', '.search-feedback-prod', function(){
      var query = $(this).val();
      get_for_feedback_production(1, query);
    });

    $(document).on('click', '.view-bom-details-btn', function(e){
      e.preventDefault();
      $('#production-order-val').val($(this).data('production-order'));
      $.ajax({
        url: "/view_bom_for_review/" + $(this).data('bom'),
        type:"GET",
        success:function(data){
          $('#review-bom-details-div').html(data);
        }
      });

      $('#review-bom-modal .modal-title').html('BOM Update [' + $(this).data('bom') + ']');
      $('#review-bom-modal').modal('show');
    });

    $(document).on('click', '.print-transfer-slip-btn', function(e){
      e.preventDefault();

      $.ajax({
        url: "/print_fg_transfer_slip/" + $(this).data('production-order'),
        type:"GET",
        success:function(data){
          $("#iframe-print").attr("src", this.url);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      });
    });

    function get_production_order_items(id){
      $.ajax({
        url:"/get_production_order_items/"+ id,
        type:"GET",
        success:function(data){
          $('#tbl_view_transfer_details').html(data);
          $('#stock-entry-details-modal').modal('show');
        },
        error : function(data) {
          console.log(data.responseText);
        }
      });
    }
  
    function get_target_warehouse(operation_id, target_warehouse){
      $('#target-warehouse-sel').empty();
      $.ajax({
        url: "/get_target_warehouse/" + operation_id,
        type:"GET",
        success:function(data){
          var opt = '';
          $.each(data, function(i, d){
            var selected = (target_warehouse == d) ? 'selected' : '';
            opt += '<option value="' + d + '" ' + selected + '>' + d + '</option>';
          });

          $('#target-warehouse-sel').append(opt);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        },
      });
    }

    $(document).on('click', '.delete-pending-mtfm-btn', function(e){
      e.preventDefault();
      var $row = $(this).closest('tr');
      var item_code = $row.find('span').eq(0).text();
      var ste_no = $row.find('td').eq(0).text();
      $('#delete-pending-mtfm-modal input[name="sted_id"]').val($(this).data('id'));
      $('#delete-pending-mtfm-modal input[name="production_order"]').val($(this).data('production-order'));
      $('#delete-pending-mtfm-modal input[name="ste_no"]').val(ste_no);
      $('#delete-pending-mtfm-modal .modal-body span').eq(0).text(item_code);
      $('#delete-pending-mtfm-modal .modal-body span').eq(1).text('('+ste_no+')');
      $('#delete-pending-mtfm-modal').modal('show');
    });
  
    $('#delete-pending-mtfm-modal form').submit(function(e){
      e.preventDefault();
      var production_order = $('#delete-pending-mtfm-modal input[name="production_order"]').val();
      var sted_id = $('#delete-pending-mtfm-modal input[name="sted_id"]').val();
      var ste_no = $('#delete-pending-mtfm-modal input[name="ste_no"]').val();
     
      $.ajax({
        url:"/cancel_request/" + sted_id,
        type:"POST",
        data: {ste_no: ste_no},
        success:function(response){
          console.log(response);
          if (response.error == 1) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", response.message, "now-ui-icons travel_info");
            get_pending_material_transfer_for_manufacture(production_order);
            $('#delete-pending-mtfm-modal').modal('hide');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });

    function get_pending_material_transfer_for_manufacture(production_order){
      $.ajax({
        url:"/get_pending_material_transfer_for_manufacture/" + production_order,
        type:"GET",
        success:function(response){
          $('#feedback-production-items').html(response);
          
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    }

    $(document).on('click', '#submit-bom-review-btn', function(){
    var production_order = $('#production-order-val-bom').val();
    var operation_id = $('#operation_id_update_bom').val();
    var id = [];
    var workstation = [];
    var wprocess = [];
    var workstation_process = [];
    var bom = $('#bom-workstations-tbl input[name=bom_id]').val();
    var user = $('#bom-workstations-tbl input[name=username]').val();
    // var operation = $('#bom-workstations-tbl input[name=operation]').val();
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
      data: {user: user, id: id, workstation: workstation, wprocess: wprocess, production_order: production_order, operation:operation_id},
      success:function(data){
        if(data.status) {
          $('#review-bom-modal').modal('hide');
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

    $(document).on('click', '.create-feedback-btn', function(e){
      e.preventDefault();
  
      $('#submit-feedback-btn').removeAttr('disabled');
      var production_order = $(this).data('production-order');
      $('#confirm-feedback-production-modal input[name="production_order"]').val(production_order);
      get_pending_material_transfer_for_manufacture(production_order);
  
      $('#confirm-feedback-production-modal').modal('show');
    });

    $(document).on('click', '.submit-ste-btn', function(e){
      e.preventDefault();
      var production_order = $(this).data('production-order');
      $.ajax({
        url:"/submit_stock_entries/" + production_order,
        type:"POST",
        success:function(data){
          if(data.success == 2){
            showNotification("info", data.message, "now-ui-icons travel_info");
          }else if(data.success == 1){
            get_production_order_items(production_order);
            showNotification("success", data.message, "now-ui-icons ui-1_check");
          }else{
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

    function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 500,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }

    $('#get-notifications-btn').click(function(e){
      e.preventDefault();
      $.ajax({
        url:"/get_tbl_notif_dashboard",
        type:"GET",
        success:function(data){
          $('#tbl-notifications').html(data);
          $('#view-notifications-modal').modal('show');
        }
      }); 
    });
  
    table_po_orders();
    count_current_production();
    setInterval(notif_dashboard, 7000);

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
  function table_po_orders(){
    var date_today = $("#date_today").val();
    $.ajax({
        url:"/get_production_order_list/" + date_today,
        type:"GET",
        success:function(data){
          $('#table_orders').html(data);
        }
      }); 
  }

  function count_current_production(){
    var date_today = $("#date_today").val();
    $.ajax({
      url:"/count_current_production_order/" + date_today,
      type:"GET",
      success:function(data){
        $('#pending_count').text(data.pending);
        $('#inprogress_count').text(data.inProgress);
        $('#completed_count').text(data.completed);
        $('#pending_count_qty').text(data.pending_qty);
        $('#inprogress_count_qty').text(data.inProgress_qty);
        $('#completed_count_qty').text(data.completed_qty);
        $('#reject_count').text(data.reject);
      }
    }); 
  }

  function notif_dashboard(){
    $.ajax({
      url:"/get_tbl_notif_dashboard",
      type:"GET",
      success:function(data){
        $('#tbl_notif_dash').html(data);
      }
    }); 
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(document).on('click', '.btn-hide', function(e){
    e.preventDefault();
    var from_table = $(this).attr('data-frmtable')
    var data = {  
      timelog_id: $(this).attr('data-timelogid'), 
      frm_table: $(this).attr('data-frmtable')
    }
    if(from_table == "machine"){

    }else{
      $.ajax({
      url:"/hide_reject",
      type:"post",
      data:data,
      success:function(response){
        if (response.success > 0) {
          notif_dashboard();
        }else{
        }
      }, 
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    }); 
    }
  
  }); 
</script>
@endsection