@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'main_dashboard',
    'pageHeader' => 'Production Floor Monitoring',
    'pageSpan' => Auth::user()->employee_name . ' - ' . $user_details->designation_name
])

@section('content')
@include('modals.view_for_feedback_list_modal')
<div class="panel-header"></div>
<input type="hidden" name="date_today" id="date_today" value="{{ date('Y-m-d') }}">
<div class="row p-0 ml-0 mr-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
  <div class="col-md-12 p-2">
    <div class="card m-0" style="min-height: 900px;">
      <div class="card-body p-1">
        <div class="row p-0 m-0">
          <div class="col-6">
            <span class="d-block font-weight-bold text-left p-2 text-uppercase">Scheduled Orders Today</span>
            <div class="d-flex flex-row">
              <div class="pl-2 col-3" style="border-left: 5px solid #229954;">
                <small class="d-block">Sales Order</small>
                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="d-sales-orders">0</span>
              </div>
              <div class="pl-2 col-3" style="border-left: 5px solid #E67E22;">
                <small class="d-block">Consignment Order</small>
                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="d-consignment-orders">0</span>
              </div>
              <div class="pl-2 col-3" style="border-left: 5px solid #48C9B0;">
                <small class="d-block">Sample Order</small>
                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="d-sample-orders">0</span>
              </div>
              <div class="pl-2 col-3" style="border-left: 5px solid #229954;">
                <small class="d-block">Other(s)</small>
                <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="d-other-orders">0</span>
              </div>
            </div>
          </div>
          <div class="col-3 text-center">
            <span class="d-block font-weight-bold p-2 text-uppercase">Quality Assurance</span>
            <span class="d-block font-weight-bolder" style="font-size: 17pt;" id="d-quality-inspections">0</span>
            <small class="d-block">Inspection Created</small>
          </div>
          <div class="col-3">
            <div class="row">
              <div class="col-8 offset-2 p-1">
                <div class="card mt-2" style="background-color: #0277BD;">
                  <div class="card-body p-0 text-center text-white">
                    <h5 class="font-weight-bold align-middle m-1 ml-2 text-uppercase" style="font-size: 9pt;">For Feedback</h5>
                    <div class="d-block pb-2 pt-2" style="background-color: #263238;">
                      <span class="d-block font-weight-bold" style="font-size: 19pt;" id="for-feedback-count">0</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row p-0 m-0">
          <div class="col-9 p-1">
            <div id="dashboard-operator-output-div"></div>
            <div class="card shadow-none border">
              <div class="card-header pt-0 pl-3 pr-1 pb-0 bg-warning">
                <div class="d-flex flex-row justify-content-between align-items-center">
                  <h6 class="text-white font-weight-bold text-left m-0 p-0 rounded-top" style="font-size: 10.5pt;">On-Going Production Order(s)</h6>
                  <ul class="nav nav-tabs m-0 border-0 p-0 dashboard-custom-tabs" role="tablist" style="font-size: 9pt;">
                    <li class="nav-item font-weight-bold">
                      <a class="nav-link active border rounded m-1 pb-1 pt-1 text-dark" data-toggle="tab" href="#fab" role="tab">Fabrication</a>
                    </li>
                    <li class="nav-item font-weight-bold">
                      <a class="nav-link border rounded m-1 pb-1 pt-1 text-dark" data-toggle="tab" href="#pa" role="tab">Painting</a>
                    </li>
                    <li class="nav-item font-weight-bold">
                      <a class="nav-link border rounded m-1 pb-1 pt-1 text-dark" data-toggle="tab" href="#wa" role="tab">Wiring and Assembly</a>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="card-body pb-1 pl-1 pr-1 pt-0" style="min-height: 300px;">
                <div class="tab-content" id="on-going-production-orders-content">
                  <div class="tab-pane fade show active" id="fab" role="tabpanel" data-operation="1">
                    <div class="table-div m-1 p-0"></div>
                  </div>
                  <div class="tab-pane fade" id="pa" role="tabpanel" data-operation="2">
                    <div class="table-div m-1 p-0"></div>
                  </div>
                  <div class="tab-pane fade" id="wa" role="tabpanel" data-operation="3">
                    <div class="table-div m-1 p-0"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-3 p-1">
            <div id="rejections-div"></div>
            <div id="idle-operators-div"></div>
            <div id="idle-machines-div"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="view-notifications-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #0277BD;">
        <h5 class="modal-title" id="modal-title">
          <i class="now-ui-icons ui-1_bell-53"></i> Notifications
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#logs">Activity Logs</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#warnings">Warnings&nbsp;<span class="badge badge-danger" id="warnings-badge-2" style="font-size: 14px">0</span></a>
          </li>
        </ul>
        <div class="tab-content p-0">
          <div id="logs" class="container-fluid tab-pane p-0 active">
            <h5 class="title m-0 text-uppercase p-2 text-center text-white" style="background-color:#3498db;">Activity Logs</h5>
            <div id="tbl-notifications" style="height: 700px; overflow-y: scroll;"></div>
          </div>
          <div id="warnings" class="container-fluid tab-pane p-0">
            <h5 class="title m-0 text-uppercase">Warnings</h5>
            <div id="tbl-warnings" style="height: 700px; overflow-y: scroll;"></div>
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
  .dashboard-custom-tabs .nav-link.active {
    background-color: #b2babb  !important;
    color: #ffffff !important;
  }
  .menu-box{
    display: inline-block;
    background-color: #ffffff; 
    border: 1px solid #dddddd;
    width: 96%;
    margin: 8%;
  }
  @-webkit-keyframes blinker {
    from { background-color: rgba(196, 21, 45, 0.2)/* #CD6155 */; }
    to { background-color: inherit; }
  }
  @-moz-keyframes blinker {
    from { background-color: rgba(196, 21, 45, 0.2)/* #CD6155 */; }
    to { background-color: inherit; }
  }
  @-o-keyframes blinker {
    from { background-color: rgba(196, 21, 45, 0.2)/* #CD6155 */; }
    to { background-color: inherit; }
  }
  @keyframes blinker {
    from { background-color: rgba(196, 21, 45, 0.2)/* #CD6155 */; }
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
  .logs .active{
    background-color: #3498DB !important;
    color: #fff !important;
    border: 2px solid #3498DB !important;
  }
  .circle-chart {
    width: 80px;
    height: 50px;
  }
  .circle-chart__circle {
    stroke: #00acc1;
    stroke-width: 2;
    stroke-linecap: square;
    fill: none;
    animation: circle-chart-fill 2s reverse; /* 1 */ 
    transform: rotate(-90deg); /* 2, 3 */
    transform-origin: center; /* 4 */
  }
  .circle-chart__circle--negative {
    transform: rotate(-90deg) scale(1,-1); /* 1, 2, 3 */
  }
  .circle-chart__background {
    stroke: #efefef;
    stroke-width: 2;
    fill: none; 
  }
  .circle-chart__info {
    animation: circle-chart-appear 2s forwards;
    opacity: 0;
    transform: translateY(0.3em);
  } 
  .circle-chart__percent {
    alignment-baseline: central;
    text-anchor: middle;
    font-size: 7px;
  }
  .circle-chart__subline {
    alignment-baseline: central;
    text-anchor: middle;
    font-size: 3px;
  }
  .success-stroke {
    stroke: #00C851;
  }
  .warning-stroke {
    stroke: #ffbb33;
  }
  .danger-stroke {
    stroke: #ff4444;
  }
  @keyframes circle-chart-fill {
    to { stroke-dasharray: 0 100; }
  }
  @keyframes circle-chart-appear {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  .skills_section{
    width: 100%;
    margin: 0 auto;
    margin-bottom: 80px;
  }
  .skills-area {
    margin-top: 5%;
    display: flex;
    flex-wrap: wrap;
  }
  .single-skill {
    width: 25%;
    margin-bottom: 80px;
  }
  .success-stroke {
    stroke: rgb(129, 86, 252);
  }
  .circle-chart__background {
    stroke: #ede4e4;
    stroke-width: 2;
  }
  /* Extra small devices (portrait phones, less than 576px) */
  @media (max-width: 575.98px) {
    .skill-icon {
      width: 50%;
    }
    .skill-icon i {
      font-size: 70px;
    }
    .single-skill {
      width: 50%;
    }
  }
  .progress {
    margin:20px auto;
    padding:0;
    width:90%;
    height: 22px;
    overflow:hidden;
    background:#e5e5e5;
    border-radius:6px;
  }
  .bar {
    position:relative;
    float:left;
    min-width:1%;
    height:100%;
    background:cornflowerblue;
  }
  .percent {
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    margin:0;
    font-family:tahoma,arial,helvetica;
    font-size: 10px;
    color:white;
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
                  <span>Qty to Manufacture = </span><span class="font-weight-bold">0</span> <span class="font-weight-bold">UoM</span>
                </p>
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
    $('#mark-done-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr('action');
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#mark-done-modal').modal('hide');
            $('#jtname-modal').modal("hide");
            $('#view-machine-task-modal').modal("hide");
            loadwip();
          }else{
            showNotification("danger", data.message, "now-ui-icons travel_info");
            return false;
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      }); 
    });
    
    dashboard_operator_output();
    function dashboard_operator_output(){
      $.ajax({
        url:"/dashboard_operator_output",
        type:"GET",
        success:function(data){
          $('#dashboard-operator-output-div').html(data);
        }
      }); 
    }

    function dashboard_numbers(){
      $.ajax({
        url:"/dashboard_numbers",
        type:"GET",
        success:function(data){
          $('#d-sales-orders').text(data.sales_orders);
          $('#d-consignment-orders').text(data.consignment_orders);
          $('#d-sample-orders').text(data.sample_orders);
          $('#d-other-orders').text(data.other_orders);
          $('#d-quality-inspections').text(data.quality_inspections);
          $('#for-feedback-count').text(data.for_feedback);
        }
      }); 
    }

    function idle_machines(){
      $.ajax({
        url:"/idle_machines",
        type:"GET",
        success:function(data){
          $('#idle-machines-div').html(data);
        }
      }); 
    }

    function idle_operators(operation){
      $.ajax({
        url:"/idle_operators",
        type:"GET",
        data: {operation: operation},
        success:function(data){
          $('#idle-operators-div').html(data);
        }
      }); 
    }

    function dashboard_rejections(){
      $.ajax({
        url:"/dashboard_rejections",
        type:"GET",
        success:function(data){
          $('#rejections-div').html(data);
        }
      }); 
    }

    var date_today = $("#date_today").val();

    load_dashboard();
    function load_dashboard(){
      idle_machines();
      idle_operators();
      dashboard_numbers();
      dashboard_rejections();
      $( "#on-going-production-orders-content .tab-pane" ).each(function( index ) {
        const operation = $(this).data('operation');
        const el = $(this);
        
        get_ongoing_production_orders(operation, el);
      });
    }

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

    $(document).on("click", ".delete-row", function(e){
      e.preventDefault();
      $(this).parents("tr").remove();
    });

    function get_ongoing_production_orders(operation, el){
      $.ajax({
        url:"/get_production_order_list/" + date_today,
        type:"GET",
        data: {operation: operation},
        success:function(data){
          $(el).find('.table-div').eq(0).html(data);
        }
      }); 
    }
  
    $(document).on('click', '#for-feedback-count', function(e){
      e.preventDefault();
      var operation_id = $(this).data('operation-id');
      $('#view-for-feedback-list-operation-id').text(operation_id);
      get_for_feedback_production(1);
      $('#view-for-feedback-list-modal').modal('show');
    });
    
    function get_for_feedback_production(page){
      var operation_id = $('#view-for-feedback-list-operation-id').text();
      var query = $('#view-for-feedback-list-search-box').val();
      $.ajax({
        url: "/production_order_list/Awaiting Feedback?page=" + page,
        type:"GET",
        data: {search_string: query, operation: operation_id},
        success:function(data){
          $('#view-for-feedback-list-table').html(data);
        }
      });
    }

    $(document).on('click', '.custom-production-pagination a', function(event){
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      get_for_feedback_production(page);
    });

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

    $(document).on('keyup', '#view-for-feedback-list-search-box', function(){
      get_for_feedback_production(1);
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
      $('#stock-entry-details-modal .modal-title').text(id);
      $('#stock-entry-details-modal .modal-title').data('production-order', id);
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

      notif_dashboard('#tbl-notifications');
      warnings_dashboard()
      $('#view-notifications-modal').modal('show');
    });

    warnings_dashboard();
    function notif_dashboard(el){
      $.ajax({
        url:"/get_tbl_notif_dashboard",
        type:"GET",
        success:function(data){
          $(el).html(data);
        }
      });
    }

    function warnings_dashboard(){
      $.ajax({
        url:"/get_tbl_warnings_dashboard",
        type:"GET",
        success:function(data){
          $('#tbl-warnings').html(data);
          $('#warnings-badge-1').text($('#warnings-count').val());
          $('#warnings-badge-2').text($('#warnings-count').val());
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
      if(from_table != "machine"){
        $.ajax({
          url:"/hide_reject",
          type:"post",
          data:data,
          success:function(response){
            if (response.success > 0) {
              notif_dashboard('#tbl-notifications');
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
  });
</script>
@endsection