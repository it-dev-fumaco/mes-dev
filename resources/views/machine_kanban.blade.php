@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'machine_schedule',
])

@section('content')
@include('modals.machine_kanban_modal')

<div class="panel-header">
   <div class="header text-center" style="margin-top: -60px;">
      <div class="row">
         <div class="col-md-12">
            <table style="text-align: center; width: 100%;">
               <tr>
                  <td style="width: 36%; border-right: 5px solid white;">
                     <h2 class="title">
                        <div class="pull-right" style="margin-right: 20px;">
                          <span style="display: block; font-size: 15pt;">Schedule Date: {{ date('M-d-Y', strtotime($schedule_date)) }}
                           <!-- <span style="display: block; font-size: 15pt;">Schedule Date: {{ date('l', strtotime($schedule_date)) }} -->
                            @foreach($shift_sched as $i => $sched)
                              <span class="text-center" style="font-size:8pt;display:block;"><span style="display: {{($sched['shift_type'] == 'Special Shift') ? '' : 'none'}}">Shift - &nbsp;</span><span style="display: {{($sched['shift_type'] == 'Overtime Shift') ? '' : 'none'}}">Overtime - &nbsp;</span>{{ $sched['time_in'] }}&nbsp;- &nbsp;{{ $sched['time_out'] }}</span>
                            @endforeach
                           {{-- <input type="text" name="scheduleDate" id="scheduleDate" value="{{ $schedule_date }}"> --}}
                           {{-- <input type="text" name="tab_id_active" id="tab_id_active"> --}}
                           {{-- <input type="text" name="workstation_active" id="workstation_active"> --}}
                           {{-- <input type="text" name="workstation_id_active" id="workstation_id_active"> --}}
                           <input type="hidden" name="schedule_date" id="schedule_date" value="{{ $schedule_date }}">
                           <input type="hidden" name="current_workstation" id="current_workstation" value="{{$current_workstation_details->workstation_id}}">

                        </div>
                     </h2>
                  </td>
                  <td style="width: 50%">
                     <h5 class="title text-left" style="margin-left: 20px; margin: auto 15pt;">Machine Scheduling</h5>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>

<div class="content">
  <div class="row" style="margin-top: -160px; min-height: 700px; padding-right: 0px;">
    <div class="col-md-2">
      <div class="card" style="background-color:#f8f9f9;" id="workstation_navbar">
        <div class="card-body">
          <h6 class="text-center">Workstation</h6>
          <ul class="nav flex-column workstation_navbar" id="myTab" role="tablist" style="font-size: 10pt;">
            @foreach($workstation_list as $row)
            <li class="nav-item text-center" style="cursor: pointer;">
              @php
                $ahrefcolor= ($row->workstation_id == $current_workstation_details->workstation_id) ? '': 'black';
              @endphp
              <a class="nav-link  {{ ($row->workstation_id == $current_workstation_details->workstation_id) ? 'active' : 'inactive' }}" href="/machine_kanban/{{ $row->workstation_id }}/{{ $schedule_date }}" style="color: {{ $ahrefcolor }}">{{ $row->workstation_name }}</a>
            </li>
            @endforeach 
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-10" id="reload_me_tbl">
    
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

/*#sched_orders{
width: 100px; height: 100px; padding: 0.5em; float: left; margin: 10px 10px 10px 0; 
}
#unsched_orders{
  width: 150px; height: 150px; padding: 0.5em; float: left; margin: 10px;
}
*/
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



</style>

<!-- Modal -->
<div class="modal fade" id="change-process-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/update_process" method="POST" id="change-process-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title">
                <span>Change Process</span>
                <span class="workstation-text"></span></h5>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-8 offset-md-2">
                     <div class="form-group" style="font-size: 16pt;">
                        <label for="qty-accepted">Select Process</label>
                        <input type="hidden" name="id" required id="jt-index">
                        <select id="sel-process" class="form-control" name="process" style="font-size: 16pt;"></select>
                     </div>
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Submit</button>
            </div>
         </div>
      </form>
   </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reset-task-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="width: 30%;">
      <form action="/reset_task" method="POST" id="reset-task-frm">
         @csrf
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modal-title">
                <span>Reset Task</span>
                <span class="workstation-text" style="font-weight: bolder;"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    <h5 class="text-center">Reset start and end time?</h5>
                    <input type="hidden" name="id" required id="jt-index">
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
               <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
         </div>
      </form>
   </div>
</div>

@endsection

@section('script')
<script>
  $(document).ready(function(){
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
          $('#jt-details-col .produced-qty').text(data.totals.produced_qty);
          $('#jt-details-col .total-good').text(data.totals.total_good);
          $('#jt-details-col .total-reject').text(data.totals.total_reject);
          $('#jt-details-col .balance-qty').text(data.totals.balance_qty);

          if (data.item_details.sales_order) {
            $('#jt-details-col .ref-type').text('SO No.');
            $('#jt-details-col .ref-no').text(data.item_details.sales_order);
          }
  
          if (data.item_details.material_request) {
            $('#jt-details-col .ref-type').text('MREQ No.');
            $('#jt-details-col .ref-no').text(data.item_details.material_request);
          }
  
          $('#jt-details-col .prod-no').text(data.item_details.production_order);
          $('#jt-details-col .cust').text(data.item_details.customer);
          $('#jt-details-col .proj').text(data.item_details.project);
          $('#jt-details-col .qty').text(data.item_details.qty_to_manufacture);
          $('#jt-details-col .del-date').text(data.item_details.delivery_date);
          $('#jt-details-col .item-code').text(data.item_details.item_code);
          $('#jt-details-col .desc').text(data.item_details.description);
          $('#jt-details-col .sched-date').text(data.item_details.planned_start_date);
          $('#jt-details-col .task-status').text(data.item_details.status);
          if (data.item_details.status == 'Late') {
            $('#jt-details-col .task-status').removeClass('badge-info').addClass('badge-danger');
          }else{
            $('#jt-details-col .task-status').removeClass('badge-danger').addClass('badge-info');
          } 
     
          var r = '';
          $.each(data.operations, function(i, v){
            if(v.workstation == "Spotwelding"){
              var spotclass= "spotclass";
              var icon = '<span style="font-size:15px;">&nbsp; >></span>';
            }else{
              var spotclass= "";
              var icon="";
            }
            r += '<tr>' +
              '<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="' + v.count + '"><span class="'+ spotclass +'" data-jobticket="'+v.job_ticket +'" data-prodno="'+ v.production_order+'"><b>' + v.workstation + icon+'</b></span></td>';
            if (v.operations.length > 0) {
              $.each(v.operations, function(i, d){
                machine = (d.machine_code) ? d.machine_code : '-';
                operator_name = (d.operator_name) ? d.operator_name : '-';
                from_time = (d.from_time) ? d.from_time : '-';
                to_time = (d.to_time) ? d.to_time : '-';
                var inprogress_class = (d.status == 'In Progress') ? 'active-process' : '';
               
                if(v.process == "Housing and Frame Welding"){
                  qc_status = '';
                }else{
                   var qc_status = (d.qa_inspection_status == 'QC Passed') ? "qc_passed" : "qc_failed";
                    qc_status = (d.qa_inspection_status == 'Pending') ? '' : qc_status;
                }
                r += '<td class="text-center '+inprogress_class+' '+qc_status+'" style="border: 1px solid #ABB2B9;"><b>' + v.process + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.good) + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.reject) + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + machine + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + from_time + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + to_time + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + operator_name + '</td>' +
                    '</tr>';
              });
            }else{
              r += '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.process + '</b></td>' +
                    '<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>' +
                    '<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '</tr>';
            }
          });

          var p = '';
          $.each(data.process, function(i, d){
            p += '<li class="'+ d.status +'">'+
                '<a href="javascript:void(0);">' + d.workstation + '</a>' +
                '</li>';
            });
  
          $('#process-bc').append(p);
          $('#jt-details-tbl tbody').append(r);
          $('#jt-workstations-modal').modal('show');
        }
      });
    }

    reload_me();
    $('.workstation_navbar  a.active').css("color", "white");
    $('.workstation_navbar  a.inactive').css("color", "black");

    $(document).on('click', '#reset-time-btn', function(){
      if ($('#machine_kanban_details #card-status').val() == 'Unassigned') {
        showNotification("danger", 'Unable to reset task.', "now-ui-icons travel_info");
        return false;
      }

      if ($('#machine_kanban_details #task-status').val() == 'Completed') {
        showNotification("danger", 'Unable to reset task.', "now-ui-icons travel_info");
        return false;
      }

      var workstation = $('#machine_kanban_details .workstation-name').text();
      var jtid = $('#machine_kanban_details #jt-id').val();

      $('#reset-task-modal #jt-index').val(jtid);

      $('#reset-task-modal .workstation-text').text('[' + workstation + ']');
      $('#reset-task-modal').modal('show');
   
    });
     $(document).on('click', '.spotclass', function(event){
      event.preventDefault();
      var jtid = $(this).attr('data-jobticket');
      var prod = $(this).attr('data-prodno');
      $.ajax({
        url: "/spotwelding_production_order_search/" + jtid,
        type:"GET",
        success:function(data){
            $('#spotwelding-div').html(data);
            $('#spotwelding-modal .prod-title').text(prod+" - Spotwelding");
            $('#spotwelding-modal').modal('show');
        }
      });
    });
    $('#reset-task-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr('action');
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#reset-task-modal').modal('hide');
            $('#jtname-modal').modal("hide");
            $('#view-machine-task-modal').modal("hide");
            reload_me();
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

    $(document).on('click', '#change-process-btn', function(){
      var disable_change = ['Completed', 'In Progress']
      if (disable_change.indexOf($('#machine_kanban_details #task-status').val()) > -1) {
        showNotification("danger", 'Unable to change Process.', "now-ui-icons travel_info");
        return false;
      }

      $('#change-process-modal #sel-process').empty();
      var workstation_id = $('#machine_kanban_details #workstation-id').val();
      var current_process = $('#machine_kanban_details .process').text();
      var workstation = $('#machine_kanban_details .workstation-name').text();
      var jtid = $('#machine_kanban_details #jt-id').val();

      $('#change-process-modal #jt-index').val(jtid);

      $('#change-process-modal .workstation-text').text('[' + workstation + ']');

      $.ajax({
        url: "/get_process_list/" + workstation_id,
        type:"GET",
        success:function(data){
          var row = '';
          $.each(data, function(i, v){
            var selected = (current_process == v.process_name) ? 'selected' : '';
            row += '<option value="' + v.process_id + '" '+ selected +'>' + v.process_name + '</option>';
          });

          $('#change-process-modal #sel-process').append(row);
          $('#change-process-modal').modal('show');
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      });
    });

    $('#change-process-frm').submit(function(e){
      e.preventDefault();
      var current_process = $('#machine_kanban_details .process').text();
      var new_process = $('#change-process-modal #sel-process option:selected').text();
      if (current_process == new_process) {
        showNotification("info", 'No changes made.', "now-ui-icons travel_info");
        return false;
      }else{
        var url = $(this).attr('action');
        $.ajax({
          url: url,
          type:"POST",
          data: $(this).serialize(),
          success:function(data){
            if (data.success) {
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#change-process-modal').modal('hide');
              $('#jtname-modal').modal("hide");
              $('#view-machine-task-modal').modal("hide");
              reload_me();
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
      }      
    });

    $('#jtname-modal').on('hidden.bs.modal', function(){
        var frm = $(this).find('form')[0];
        if (frm) frm.reset();
      });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
  });
</script>



<script type="text/javascript">
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
</script>

<script type="text/javascript">
  function reload_me(){
    $.ajax({
        url:"/machine_kanban_tbl/{{$current_workstation_details->workstation_id}}/{{ $schedule_date }}",
        type:"GET",
        success:function(data){
          $('#reload_me_tbl').html(data);
        //   var tabid = $('#tab_id_active').val();
        //   var hreftab = "#"+ tabid;
        //   $('.nav-item-link a').css('color', 'black');

        //   $('.nav-item a[href="#' + tabid + '"]').tab('show');
        //   $('.nav-item a[href="#' + tabid + '"]').css('color', 'white');
        //   $('.nav-item .active').css('color', 'white');
        }
      }); 
  }
</script>
@endsection


