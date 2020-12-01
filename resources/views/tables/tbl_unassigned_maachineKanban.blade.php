<div class="card" style="background-color:#f8f9f9;">
  <div class="card-body" id="dashboard-rows">
    <div class="" style="float: right;margin-top: -30px;"><h6 class="text-center" style="padding-top: 30px;display: inline-block;">Legend:</h6>
      <span class="dot" style="background-color:#717D7E; margin-left: 12px;"></span>
      <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Not Started</span>
      <span class="dot" style="background-color:#EB984E; margin-left: 12px;"></span>
      <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">In Progress</span>
      <span class="dot" style="background-color:#58d68d; margin-left: 12px;"></span>
      <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Completed</span>
      <span class="dot" style="background-color:#ec7063; margin-left: 12px;"></span>
      <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Late</span>
    </div>
    <div class="card card-nav-tabs card-plain" style="margin-top: 40px;">
      <div class="card-body">
          <div class="scrolling outer" style="margin-top: -35px;">
            <div class="inner">
              <table style="width: 100%;">
                <tr>
                  @foreach($data['line'] as $rows)
                          <td style="padding-right: 10px;">
                            <div class="card justify-content-center" style="overflow-y: auto;width: 350px; padding:0px;">
                              <div class="card-header justify-content-center"  style="background-color: #f96332; color: white;line-height: 18px; margin-top: -10px;padding-bottom: 5px;height: 100%;">
                                <!-- <h6 class="card-title text-center" style="font-size: 2vw;font-size: 13px;">
                                  <b>{{ $rows['process'] }} </b>
                                </h6> -->
                                <h6 class="card-title text-center" style="font-size:80%;">{{ $rows['process'] }}</h6>
                                <button type="button" class="btn btn-neutral btn-icon btn-round btn-sm view-btn-modal" style="border-radius: 1em;float: right;margin-top: -35px;" data-processidview="{{ $rows['process_id'] }}" data-processname="{{ $rows['process'] }}" >
                                  <i style="font-size: 12pt;" class="now-ui-icons ui-1_zoom-bold"></i>
                                </button>
                              </div>
                              

                              <div class="card-body connectedSortable sortable_list scrolling inner sorrtt" style="height: 600px; position: relative; width: 100%;" id="{{ $rows['process_id'] }}" >
                              @foreach($rows['load'] as $roowsss)
                                @foreach($roowsss['load'] as $rowsss)
                                @php
                                if($rowsss->stat == 'Completed'){
                                  $colorme="#58d68d";
                                  $blickme="";
                                }else if($rowsss->stat == 'In Progress'){
                                  $colorme="#EB984E";
                                  $blickme="text-blink";
                                }else if(date('Y-m-d', strtotime($rowsss->planned_start_date)) == date('Y-m-d', strtotime($scheduleDate))){
                                  $colorme="";
                                  $blickme="";
                                }else if(date('Y-m-d', strtotime($rowsss->planned_start_date)) != date('Y-m-d', strtotime($current_date))){
                                  $colorme="#ec7063";
                                  $blickme="";
                                }else{
                                  $colorme="";
                                  $blickme="";
                                }
                                $convert_timee= $rowsss->duration;
                                @endphp
                                <div id="sched_orders" class="card {{ $rowsss->stat}} {{ $blickme }}" data-index="{{ $rowsss->jtname }}" data-position="{{ $rowsss->order_no}}" data-card="{{ $rowsss->machine }}"  data-mprocess="{{ $rowsss->process_name }}" data-prod="{{ $rowsss->production_order }}" data-sales="{{ ($rowsss->sales_order == '')? $rowsss->material_request : $rowsss->sales_order  }}" data-customer="{{ $rowsss->customer }}" data-desc="{{ $rowsss->description }}" data-proditem="{{ $rowsss->item_code }}" data-qty="{{ number_format($rowsss->qty_to_manufacture) }} {{ $rowsss->stock_uom }}" data-cpt="{{ number_format($rowsss->good_qty) }} {{ $rowsss->stock_uom }}" data-process=" -[{{ $rowsss->process_name }}]" data-mprocessvalidation="{{ $rowsss->process_name }}" data-delivery="{{ date('m-d-Y', strtotime($rowsss->delivery_date))}}" data-planstartdate="{{ date('m-d-Y', strtotime($rowsss->planned_start_date)) }}" data-modaltitle="{{ $rowsss->production_order }} -[{{ $rowsss->process_name }}]" data-operatorname="{{ $rowsss->operator_name }}" data-machinecode="[ {{ $rowsss->machine }} ]" data-starttime="{{ date('Y-m-d h:i:s a', strtotime($rowsss->from_time)) }}" data-totime="{{ date('Y-m-d h:i:s a', strtotime($rowsss->to_time)) }}" data-duration="{{ $convert_timee }}" data-machinename="{{ $rowsss->machine_name }}"   data-taskstatus="{{ $rowsss->stat }}"  data-cycletime="{{ number_format($rowsss->good_qty) }}" style="background-color: {{ $colorme }};margin-top: -10px;margin-bottom: 10px;height: 30px;width: 300px;"  data-ws="{{ $data['production_line'] }}" data-wsid="{{ $data['workstation_id'] }}" data-qtyaccepted="{{ $rowsss->qty_to_manufacture  }}" data-timelog="{{ $rowsss->time_log_id }}" data-remark="{{ $rowsss->remarks }}" >
                                  <div class="card-body" style="font-size: 8pt;margin-top: -10px;">
                                    <table style="width: 100%;">
                                      <tr>
                                        <td>
                                          <span class="hvrlink"><b>{{ $rowsss->production_order }} - {{ ($rowsss->sales_order == "")? $rowsss->material_request: $rowsss->sales_order }}</b></span>
                                          <div class="details-pane">
                                            <h5 class="title">{{ $rowsss->production_order }}<span>[{{ ($rowsss->stat == "Pending") ? "Not Started": $rowsss->stat }}]</span></h5>
                                                      <p class="desc">
                                                        <b>Process:</b> <b>{!! $rowsss->process_name !!}</b><br>
                                                         <b>{{ $rowsss->item_code }}:</b> {!! $rowsss->description !!}<br>

                                                         Qty: <b>{{ number_format($rowsss->qty_to_manufacture) }} {{ $rowsss->stock_uom }}</b><br>
                                                         <b>{{ ($rowsss->sales_order == "")? $rowsss->material_request : $rowsss->sales_order }}</b><br>
                                                         Customer: <b>{{ $rowsss->customer }}</b><br>
                                                         <!-- Item Feedback: <b>{{-- $rowsss->item_feed --}}</b><br> -->
                                                         Planned Start Date: <b>{{ date('m-d-Y', strtotime($rowsss->planned_start_date)) }}</b><br>
                                                         
                                                      </p>
                                                   </div>

                                                        </td>
                                                        <td><b>{{ ($rowsss->good_qty == 0) ? "": $rowsss->good_qty  }}&nbsp;{{ ($rowsss->good_qty == 0) ? "": $rowsss->stock_uom }}</b></td>

                                                        <td colspan="3"><span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $rowsss->order_no }}</span></td>
                                                      </tr>

                                                    </table>
                                                  </div>
                                                </div>
                                              @endforeach
                                             @endforeach
                                            </div>
                                            <div class="card-footer text-muted card-footer__events" style="background-color: #254d78;color: white;">
                                            <h6 style="color: white;height: 2px; font-size: 8pt; display:inline;margin-top:2px;">Total QTY - {{ $roowsss['total_qty'] }}  Piece(s)</h6>
                                            <h6 style="color: white;height: 2px; font-size: 8pt; display:inline-block; float:right;margin-top:5px;">On Queue - {{ $roowsss['load_count'] }}</h6>
                                            </div>
                                          </div>

                                        </td>
                          @endforeach
                        </tr>
                        </table>
                        </div>
                      </div>
                      
                      </div>
                    </div>
                  </div>   
                </div>

          <div class="modal fade" id="view-machine-task-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Modal Title</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-12">
                        <div id="tbl-view-machine-task-modal"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="confirm-task-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Modal Title</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-12">
                        <div id="tbl-confirm-task-modal">
                        Hay YES or No!
                        </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary confirm-btn-for-drag">Confirm</button>
                </div>
              </div>
            </div>
          </div>

<style type="text/css">

  .scrolling table {
/*    table-layout: fixed;
*/    width: 100%;
}
.scrolling .td, .th {
  padding: 10px;
/*  width: 600px;
*/}
.tdds {
/*  padding-right: : 30px;

/*white-space: nowrap;
*/}

.parent-td{
  padding: 10px;
  width: 4px;
  float: left;
}
.scrolling .th {
  position: relative;
  left: 0;
  width: /*600px*/;
}
.outer {
  position: relative
}
.inner {
  overflow-x: auto;

}
.nav-item .active{
  background-color: #f96332;
  font-weight: bold;
  color:#ffffff;
}
/** page structure **/
.thumbs ul{
   padding: 0;
   margin: 0;
}

.thumbs ul li {
  display: block;
  position: relative;
  float: left;
  margin: 0;
  padding: 0;
}

/** detail panel **/
.details-pane {
display: none;
  color: #414141;
  background: #f1f1f1;
  border: 1px solid #a9a9a9;
  position: absolute;
  top: 20px;
  left: 0;
  z-index: 1;
  width: 300px;
  padding: 6px 8px;
  text-align: left;
  -webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  -moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  white-space: normal;
}

.details-pane h5 {
  font-size: 1.5em;
  line-height: 1.1em;
  margin-bottom: 4px;
  line-height: 8px;
}

.details-pane h5 span {
  font-size: 0.75em;
  font-style: italic;
  color: #555;
  padding-left: 15px;
    line-height: 8px;

}

.details-pane .desc {
  font-size: 1.0em;
  margin-bottom: 6px;
    line-height: 16px;

}

/** hover styles **/
span.hvrlink:hover + .details-pane {
  display: block;
}
.details-pane:hover {
  display: block;
}
.dot {
    height: 12px;
    width: 12px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }
.text-blink {
    color: black;
    animation: blinkingBackground 2s linear infinite;
  }
  .for-color-black{
    color: black;
  }
  @keyframes blinkingBackground{
    0%    { background-color: #ffffff;}
    25%   { background-color: #EB984E;}
    50%   { background-color: #ffffff;}
    75%   { background-color: #EB984E;}
    100%  { background-color: #ffffff;}
  }
.modal-md {
    max-width: 35% !important;
}
</style>

{{-- <script src="{{ asset('/js/jquery.ui.touch-punch.js') }}"></script> --}}
<script>

</script>
<script>

  $(document).ready(function(){
  
    // alert($('#div-workstation-15 .unassignedtask-class').length);
    var tabid = $('#tab_id_active').val();
    var activeTab = $(".tab-content").find(".active");
    var workstation = activeTab.attr('data-workstation');
    var workstation_id = activeTab.attr('data-workstationid');
    $('#workstation_active').val(workstation);
    $('#workstation_id_active').val(workstation_id);
    var hreftab = "#"+ tabid;
    $('.nav-item a[href="#' + tabid + '"]').tab('show');

    $( ".sortable_list" ).sortable({
    connectWith: ".connectedSortable",
    appendTo: 'body',
    helper: 'clone',

    update:function(event, ui) {
      var card_id = this.id;
      var m_process = $(this).data('mprocess');
      var card_status = $(this).data('status');
      var elem = $(this);

      // var m_process = this.mprocess;
          $(".sorrtt").on("sortreceive", function(event, ui){
              var s_process = $(this).data('mprocess');
              var card = this.id;

                  if($(ui.item).hasClass("In Progress") || $(ui.item).hasClass("Completed") ){
                  elem.sortable("cancel");;
                  }
                  else{
                    $(this).children().addClass('updated');
                    showNotification("success", '<b>Process Sucessfully Updated</b>', "now-ui-icons ui-1_check");
                    }

          }); 
          
      var pos = [];
      $('.updated').each(function(){
        var name = $(this).data('index');
        var card =$(this).attr('data-card');
        var qty =$(this).attr('data-qtyaccepted');
        var position = $(this).attr('data-position');
        // var m_process = $(this).attr('data-mprocess');
        // alert(m_process);
        pos.push([name, card_id]);
        $(this).removeClass('updated');
         console.log(pos);
        
        // var name2 = "#div-workstation-"+ workstation + " .unassignedtask-class";
        // var divno = "#count_unassigned"+workstation;
        // $(divno).text($(name2).length);
      });
      $.ajax({
        url:"/reorder_productions",
        type:"post",
        dataType: 'JSON',
        data: {
          positions: pos
        },
        success:function(data){ 
        },
        error:function(data){
        reload_me();      

        }
      });
    },        
    }).disableSelection();

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
  });
</script>

<script type="text/javascript">
  $(document).on('click', '.view-btn-modal', function(e){
    var process_id = $(this).attr('data-processidview');
    var process_name = $(this).attr('data-processname');
    var current_workstation = $('#current_workstation').val();
    var scheduleDate = $('#schedule_date').val();
    // alert(scheduleDate);
    $.ajax({
          url:"/machineKanban_view_machineList/" + process_id + "/" + current_workstation + "/" + scheduleDate,
          type:"GET",
          success:function(response){
            $('#tbl-view-machine-task-modal').html(response);
            $('#view-machine-task-modal').modal('show');
            $('#view-machine-task-modal .modal-title').text(process_name);
          }
        });
  });
</script>
<!-- <script type="text/javascript">
        $(document).on('click', '.click-me', function(){
        var name = $(this).data('workstations');
        var date = $('#scheduleDate').val();
        // alert(name);
        $.ajax({
          url:"/machine_kanban/workstation/" + name + "/" + date,
          type:"GET",
          success:function(response){

            $('.table-me').html(response);
          }
        });
      });
</script> -->
<script type="text/javascript">
  function loadme(){
        var name = 'Shearing 1';
        var date = $('#scheduleDate').val();
        // alert(name);
        $.ajax({
          url:"/machine_kanban/workstation/" + name + "/" + date,
          type:"GET",
          success:function(response){

            $('.table-me').html(response);
          }
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
<script type="text/javascript">
   $(document).on('click', '#sched_orders', function(e){
    $('#machine_kanban_details #card-status').val($(this).parent().data('status'));
    $('#machine_kanban_details #task-status').val($(this).data('taskstatus'));
    
    var timelog = $(this).attr('data-timelog');
    var prod_no = $(this).attr('data-prod');
    var prod_desc = $(this).attr('data-desc');
    var prod_sales = $(this).attr('data-sales');
    var prod_customer = $(this).attr('data-customer');
    var prod_item = $(this).attr('data-proditem');
    var prod_qty = $(this).attr('data-qty');
    var prod_cpt = $(this).attr('data-cpt');
    var prod_process = $(this).attr('data-process');
    var prod_delivery_date = $(this).attr('data-delivery');
    var prod_planstart_date = $(this).attr('data-planstartdate');
    var prod_operator_name = $(this).attr('data-operatorname');
    var prod_machine = $(this).attr('data-machinecode');
    var prod_from_time = $(this).attr('data-starttime');
    var prod_to_time = $(this).attr('data-totime');
    var prod_hrs = $(this).attr('data-duration');
    var prod_inspected_by = $(this).attr('data-inspectedby');
    var prod_inspected_date = $(this).attr('data-inspectiondate');
    var prod_qa_remark = $(this).attr('data-inspectionstatus');
    var prod_qa_qty = $(this).attr('data-itemfeedback');
    var prod_machine_name = $(this).attr('data-machinename');
    var prod_status = $(this).attr('data-taskstatus');
    var prod_qc_type = $(this).attr('data-qctype');
    var prod_cycle_cpt = $(this).attr('data-cycletime');
    var prod_remarks = $(this).attr('data-remark');
    var prod_qtyaccepted = $(this).attr('data-qtyaccepted');
    var formattime = (Math.round((prod_hrs * 60) * 100) / 100).toFixed(2) + " min(s)";
    var totaltime= (Math.round((prod_hrs * 60) * 100) / 100).toFixed(2);
    var cycletime= (Math.round((totaltime / prod_cycle_cpt) * 100) / 100).toFixed(2)+ " min(s)";

    $('#machine_kanban_details #print-job-ticket-btn').attr('href', '/single_print_job_ticket/' + prod_no);
    $('#machine_kanban_details #modal-title').text($(this).data('prod'));
        $('#machine_kanban_details .workstation-name').text($(this).data('ws'));
        $('#machine_kanban_details #workstation-id').val($(this).data('wsid'));
        $('#machine_kanban_details #jt-id').val($(this).data('index'));
        $('#machine_kanban_details .process').text($(this).data('mprocessvalidation'));
        $('#machine_kanban_details #qty-override').val($(this).data('qtyaccepted'));

        $('#machine_kanban_details #production_order').text(prod_no);
        $('#machine_kanban_details #customer').text(prod_customer);
        $('#machine_kanban_details #sales_order').text(prod_sales);
        $('#machine_kanban_details #prod_desc').text(prod_desc);
        $('#machine_kanban_details #prod_item').text(prod_item);
        $('#machine_kanban_details #prod_qty').text(prod_qty);
        $('#machine_kanban_details #prod_cpt').text(prod_cpt);
        $('#machine_kanban_details #prod_process').text(prod_process);
        $('#machine_kanban_details #prod_delivery').text(prod_delivery_date);
        $('#machine_kanban_details #prod_planstart_date').text(prod_planstart_date);
        $('#machine_kanban_details #prod_machine_code').text(prod_machine);
        $('#machine_kanban_details #prod_machine_name').text(prod_machine_name);
        $('#machine_kanban_details #prod_start_time').text(prod_from_time);
        $('#machine_kanban_details #prod_end_time').text(prod_to_time);
        $('#machine_kanban_details #prod_duration').text(formattime);
        $('#machine_kanban_details #prod_cycle_time').text(cycletime);
        $('#machine_kanban_details #prod_operator').text(prod_operator_name);
        $('#machine_kanban_details #prod_inspected_by').text(prod_inspected_by);
        $('#machine_kanban_details #prod_inspected_date').text(prod_inspected_date);
        $('#machine_kanban_details #prod_qa_inspection_status').text(prod_qa_remark);
        $('#machine_kanban_details #prod_qc_type').text(prod_qc_type);
        let el = document.getElementById("badge_for_qa");


        if (prod_status == "Pending" || prod_status == "") {
          $('.div_machine_details').css('display', 'none');
          $('.div_quality_check').css('display', 'none');

        }else{
            if (prod_status == "In Progress") {
              $('#prod_end_time').css('display', 'none');
              $('#prod_duration').css('display', 'none');
              $('#label_duration').css('display', 'none');
              $('#label_cycle_time').css('display', 'none');
              $('#prod_cycle_time').css('display', 'none');
              $('#label_prod_end_time').css('display', 'none');
            }else{
              $('#prod_end_time').css('display', '');
              $('#prod_duration').css('display', '');
              $('#label_duration').css('display', '');
              $('#label_cycle_time').css('display', '');
              $('#label_prod_end_time').css('display', '');
              $('#prod_cycle_time').css('display', '');
            }
            if (prod_remarks == "Override") {
              $('#machine_kanban_details #prod_duration').text('Override');
              $('#machine_kanban_details #prod_cpt').text('Override');
              $('#machine_kanban_details #prod_cycle_time').text('Override');
              $('#machine_kanban_details #prod_machine_code').text('Override');
              $('#machine_kanban_details #prod_machine_name').text('Override');
              $('#prod_end_time').css('display', 'none');
              $('#prod_duration').css('display', 'none');
              $('#label_duration').css('display', 'none');
              $('#label_cycle_time').css('display', 'none');
              $('#label_prod_end_time').css('display', 'none');
              $('#prod_cycle_time').css('display', 'none');
              $('#machine_kanban_details #prod_machine_code').css('display', 'none');
              $('#machine_kanban_details #label_operator').css('display', 'none');
              $('#machine_kanban_details .label_start_time').css('display', 'none');
              $('#machine_kanban_details #prod_start_time').css('display', 'none');
              $('#machine_kanban_details #prod_operator').css('display', 'none');

              // $('#machine_kanban_details #prod_machine_name').text(prod_remarks);
              // $('#machine_kanban_details #prod_machine_code').text("");
              // $('#machine_kanban_details #prod_machine_name').addClass('badge badge-primary');
            }else{
              $('#machine_kanban_details #prod_cpt').text(prod_cpt);
              $('#machine_kanban_details #prod_machine_code').text(prod_machine);
              $('#machine_kanban_details #prod_machine_name').text(prod_machine_name);
              $('#machine_kanban_details #prod_start_time').text(prod_from_time);
              $('#machine_kanban_details #prod_duration').text(formattime);
              $('#machine_kanban_details #prod_cycle_time').text(cycletime);
              $('#prod_end_time').css('display', '');
              $('#prod_duration').css('display', '');
              $('#label_duration').css('display', '');
              $('#label_cycle_time').css('display', '');
              $('#label_prod_end_time').css('display', '');
              $('#prod_cycle_time').css('display', '');
              $('#machine_kanban_details #prod_machine_code').css('display', '');
              $('#machine_kanban_details #label_operator').css('display', '');
              $('#machine_kanban_details #label_start_time').css('display', '');
              $('#machine_kanban_details #prod_start_time').css('display', '');
            }
            if (prod_qc_type == "Quality Check") {
              $('.div_machine_details').css('display', '');
              $('.div_quality_check').css('display', '');
              if (prod_qa_remark == "QC Passed") {
                $('#badge_for_qa').removeClass('badge-danger');
                $('#badge_for_qa').addClass('badge-success');
                $('#badge_for_qa').removeClass('badge-primary');
               }else if(prod_qa_remark == "QC Failed"){
                $('#badge_for_qa').removeClass('badge-success');
                $('#badge_for_qa').addClass('badge-danger');
                $('#badge_for_qa').removeClass('badge-primary'); 
              }else{
                $('#badge_for_qa').removeClass('badge-danger');
                $('#badge_for_qa').addClass('badge-primary');
                $('#badge_for_qa').removeClass('badge-success');              
              }
            }else if(prod_qc_type == "Random Inspection"){
              $('.div_machine_details').css('display', '');
              $('.div_quality_check').css('display', '');
              if (prod_qa_remark == "QC Passed") {
                $('#badge_for_qa').removeClass('badge-danger');
                $('#badge_for_qa').addClass('badge-success');
                $('#badge_for_qa').removeClass('badge-primary');
               }else if(prod_qa_remark == "QC Failed"){
                $('#badge_for_qa').removeClass('badge-success');
                $('#badge_for_qa').addClass('badge-danger');
                $('#badge_for_qa').removeClass('badge-primary'); 
              }else{
                $('#badge_for_qa').removeClass('badge-danger');
                $('#badge_for_qa').addClass('badge-primary');
                $('#badge_for_qa').removeClass('badge-success');              
              }

            }else{
              $('.div_machine_details').css('display', '');
              $('.div_quality_check').css('display', 'none');
            }
        }
        $('#jt-details-tbl tbody').empty();
        if(timelog != ""){
          
        $.ajax({
        url:"/get_qa_details/" + timelog,
        type:"GET",
        success:function(data){
          if(data.qa_tables != ''){
            $('.div_machine_details').css('display', '');
              $('.div_quality_check').css('display', '');
          var r = '';
                $.each(data.qa_tables, function(i, v){
                  if (v.status == "QC Passed") {
                $('.badge_for_qa').removeClass('badge-danger');
                $('.badge_for_qa').addClass('badge-success');
                $('.badge_for_qa').removeClass('badge-primary');
               }else if(v.status == "QC Failed"){
                $('.badge_for_qa').removeClass('badge-success');
                $('.badge_for_qa').addClass('badge-danger');
                $('.badge_for_qa').removeClass('badge-primary'); 
              }else{
                $('.badge_for_qa').removeClass('badge-danger');
                $('.badge_for_qa').addClass('badge-primary');
                $('.badge_for_qa').removeClass('badge-success');              
              }
            
              
                    // qa_inspectype = (d.qa_inspection_type) ? d.qa_inspection_type : '-';
                    // inspect_date = (d.qa_inspection_date) ? d.qa_inspection_date : '-';
                    // qa_staff = (d.qa_staff_id) ? d.qa_staff_id : '-';
                    // sampling_qty = (d.d.sampling_qty) ? d.sampling_qty : '-';
                    // reject_qty = (d.rejected_qty) ? d.rejected_qty : '-';
                    // rework_qty = (d.for_rework_qty) ? d.for_rework_qty : '-';
                    // total_qty = (d.total_qty) ? d.total_qty : '-';
                    // status = (d.status) ? d.status : '-';
                    // var inprogress_class = (d.status == 'In Progress') ? 'active-process' : '';
                    // var qc_status = (d.qa_inspection_status == 'QC Passed') ? "qc_passed" : "qc_failed";
                    // qc_status = (d.qa_inspection_status == 'Pending') ? '' : qc_status;
                    r += '<tr>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.qa_inspection_type + '</b></td>' +
                        '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.qa_inspection_date + '</b></td>' +
                        '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.qa_staff_id + '</b></td>' +
                        '<td class="text-center" style="border: 1px solid #ABB2B9;">' + v.sampling_qty + '</td>' +
                        '<td class="text-center" style="border: 1px solid #ABB2B9;">' + v.rejected_qty + '</td>' +
                        '<td class="text-center" style="border: 1px solid #ABB2B9;">' + v.for_rework_qty + '</td>' +
                        '<td class="text-center" style="border: 1px solid #ABB2B9;">' + v.total_qty + '</td>' +
                        '<td class="text-center" style="border: 1px solid #ABB2B9;"><span class="badge text-center badge_for_qa">' + v.status + '</span></td>' +
                        '</tr>';
            
                    });
              $('#jt-details-tbl tbody').append(r);
          }
            }
          });
        }
        $('#jtname-modal').modal("show");

        
  });

</script>

<script type="text/javascript">
   $(document).on('click', '.get_id', function(e){    
    var id = $(this).attr('data-tabid');
    var workstation = $(this).data("workstations");
    var workstation_id = $(this).data("workstationsid");

    $('#tab_id_active').val(id);
    $('#workstation_active').val(workstation);
    $('#workstation_id_active').val(workstation_id);
        countUnassignedTasksForOperator(workstation)


    // alert(c);
  });
</script>
<script type="text/javascript">
      function countUnassignedTasksForOperator(workstation){
        var date = $('#scheduleDate').val();
        var workstation_id = $('#workstation_id_active').val();
        var workstation = workstation;

        // var name2 = "#div-workstation-"+ workstation + " .unassignedtask-class";
        var divno = "#count_unassigned"+workstation_id;

      $.ajax({
        url:"/get_count_unassignedTask/machineKanban/" + workstation + "/" + date,
        type:"GET",
        success:function(data){
          console.log(data);
          // $('.count_unassigned').text(data);
          $(divno).text(data);
          // console.log('updated');
        }
      });  
    }
</script>
