@extends('layouts.painting_app', [
    'namePage' => 'Painting',
    'activePage' => 'calendar',
])
@section('content')
<div class="panel-header">
  <div class="header text-center" style="margin-top: -70px;">
    <div class="row">
      <div class="col-md-12">
        <table style="text-align: center; width: 60%;">
          <tr>
            <td style="width: 36%; border-right: 5px solid white;">
              <h2 class="title">
                <div class="pull-right" style="margin-right: 20px;">
                  <span style="display: block; font-size: 15pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 10pt;">{{ date('l') }}</span>
                </div>
              </h2>
            </td>
            <td style="width: 14%; border-right: 5px ;">
              <h2 class="title" style="margin: auto; font-size: 17pt;"><span id="current-time">--:--:-- --</span></h2>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="content" style="margin-top: -200px;">
    <div class="row" style="margin-top: 12px;">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-md-8">
                <a href="/production_schedule/0" class="pull-left">
              <img src="{{ asset('storage/back.png') }}"  width="40" height="40"/>
            </a>
                  <h5 class="text-black font-weight-bold align-middle" style="padding-left: 60px;">Production Calendar - Painting</h5>
              </div>
              <div class="col-md-4" style="float: right;margin-top: -30px;"><h6 class="text-center" style="padding-top: 30px;display: inline-block;">Legend:</h6>
                  <span class="dot_css" style="background-color:#717D7E; margin-left: 25px;"></span>
                  <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Not Started</span>
                  <span class="dot_css" style="background-color:#EB984E; margin-left: 25px;"></span>
                  <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">In Progress</span>
                  <span class="dot_css" style="background-color:#58d68d; margin-left: 25px;"></span>
                  <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Completed</span>
                 
               </div>
            </div>
          </div>
          <div class="card-body">
            <div class="inner-box" id="div1">
              <div id="calendar"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title" id="modal-title ">
                 Change Planned Start Date
               </h5>
            </div>
            <div class="modal-body">
                <label>Date:</label>
                <br />
                <input type="date" class="form-control" name="start_time" id="start_time">
                <input type="hidden" class="form-control" name="prod_no" id="prod_no">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <input type="button" class="btn btn-primary" id="painting_update" value="Save">
            </div>
         </div>
   </div>
</div>

<style type="text/css">
    .dot_css {
    height: 12px;
    width: 12px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }
</style>
@endsection


@section('script')
<link rel="stylesheet" href="{{ asset('css/calendar/fullcalendar.css') }}" />
   <script src="{{ asset('css/calendar/moment.min.js') }}"></script>
   <script src="{{ asset('css/calendar/fullcalendar.min.js') }}"></script>
<script>
  $(document).ready(function() {
      var calendar = $('#calendar').fullCalendar({
           customButtons: {
            myCustomButton: {
             text: 'Print',
             click: function() {
              window.print();
             }
             }
          },

         header:{
            left:'prev,next today',
            center:'title',
            right:'month,agendaWeek,agendaDay'
         },
         editable: true,
         droppable: true,
        eventLimit: true, 
        eventClick: function(event, jsEvent, view) {
          if (event.status == "Completed") {  
          }else{
            $('#start_time').val(moment(event.start).format('YYYY-MM-DD'));
            $('#prod_no').val(event.id);
            $('#editModal').modal();
          }
        
        },
        eventDrop: function(event, element, revertFunc) {
        if (event.status == "Completed") {
                  revertFunc();
        }else{
                    $.ajax({
                        url: '/calendar_painting/update_planned_start_date',
                        type: 'POST',
                        data: { 'scheduledtime': event.start.format(), 'prodid' : event.id },
                        success: function(event)
                                    {
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                          console.log(jqXHR);
                          console.log(textStatus);
                          console.log(errorThrown);
                        }

                    });
        }

                    

      },
     
        eventRender: function(event, element){
        if (event.status == "Completed") {
             event.startEditable = false;
  
             }
         var btn = document.createElement("div");
         var prod = '<span style="font-size:18px;"><b>'+ event.production_order + '</b></span>';
        var p = '<b>'+ event.item_code + '</b>-' + event.description;
        var r = '<b> Customer:</b>&nbsp; '+ event.customer;
        var qty = '<b> QTY:</b>&nbsp; '+ event.qty + '&nbsp;' + event.uom;
        var tooltp = '<b> Sales Order:</b>&nbsp; '+ event.sales_order;
        var delivery_date = '<b>Delivery Date:</b>&nbsp;'+ event.delivery_date;
        var fortooltip= prod+ '<br><span style="font-size:14px;">'+ tooltp +'<br>'+  p+'<br>'+ delivery_date +'<br>'+ r +'<br>'+ qty+'</span>';
          element.popover({
            html: true,
              animation:true,
              content: fortooltip,
              trigger: 'hover',
            container: 'body'
          });
        },

         eventSources: [{
            url: '/get_production_schedule_calendar_painting',
            type: 'GET',
            error: function() {
            }
         }],
        
      });
   });
</script>
<script type="text/javascript">
  $('#painting_update').click(function(e) {
        e.preventDefault();
        var data = {
            _token: '{{ csrf_token() }}',
            prod_id: $('#prod_no').val(),
            start_time: $('#start_time').val(),
        };

        $.post('{{ route('painting.ajax_update') }}', data, function( result ) {
            $('#calendar').fullCalendar('removeEvents', $('#prod_no').val());

           $('#calendar').fullCalendar('refetchEvents');

            $('#editModal').modal('hide');
        });

    });
</script>
<script type="text/javascript">
  $('.printBtn').on('click', function (){
     window.print();
  });
</script>


<script type="text/javascript">
function printElem(div1) {
  var headerElements = document.getElementsByClassName('fc-header');//.style.display = 'none';
  for(var i = 0, length = headerElements.length; i < length; i++) {
    headerElements[i].style.display = 'none';
  }
  var toPrint = document.getElementById('div1').cloneNode(true);

  for(var i = 0, length = headerElements.length; i < length; i++) {
        headerElements[i].style.display = '';
  }

  var linkElements = document.getElementsByTagName('link');
  var link = '';
  for(var i = 0, length = linkElements.length; i < length; i++) {
    link = link + linkElements[i].outerHTML;
  }

  var styleElements = document.getElementsByTagName('style');
  var styles = '';
  for(var i = 0, length = styleElements.length; i < length; i++) {
    styles = styles + styleElements[i].innerHTML;
   }

 var popupWin = window.open('', '_blank',  'left=0,top=0,width=800,height=600,toolbar=0,scrollbars=0,status=0');
  popupWin.document.open();
  popupWin.document.write('<html><title></title>'+link
 +'<style>'+styles+'</style></head><body">')
  popupWin.document.write('<link href="{{ asset('css/calendar/fullcalendar.css') }}" rel="stylesheet" type="text/css" />');
  popupWin.document.write('<link href="{{ asset('css/calendar/fullcalendar.print.css') }}" rel="stylesheet" type="text/css" />');

  popupWin.document.write(toPrint.innerHTML);
  popupWin.document.write('</html>');
  popupWin.document.close();  
 setTimeout(popupWin.print(), 1000);

}

 //  var popupWin = window.open('', '_blank',  'left=0,top=0,width=800,height=600,toolbar=0,scrollbars=0,status=0');
 //  popupWin.document.open();
 //  popupWin.document.write('<html><title></title>'+link
 // +'<style>'+styles+'</style></head><body">')
 //  popupWin.document.write('<link href="{{ asset('css/calendar/fullcalendar.css') }}" rel="stylesheet" type="text/css" />');
 //  popupWin.document.write('<a class="printBtn hidden-print"><i class="fa fa-print"></i> Print</a>');
 //  popupWin.document.write(toPrint.innerHTML);
 //  popupWin.document.write('</html>');
 //  popupWin.document.focus();
 //  popupWin.document.close();
 // setTimeout(popupWin.print(), 20000);  



</script>
<script>
$(document).ready(function(){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(document).on('click', '.prod-details-btn', function(e){
    e.preventDefault();
    var jtno = $(this).data('jtno');
    $('#jt-workstations-modal .modal-title').text(jtno);
    if(jtno){
      getJtDetails($(this).data('jtno'));
    }else{
      showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
    }
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
  
  function getJtDetails(jtno){
    $('#process-bc').empty();
    $('#jt-details-tbl tbody').empty();
    $.ajax({
      url:"/get_jt_details/" + jtno,
      type:"GET",
      success:function(data){
        console.log(data);
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
          r += '<tr>' +
            '<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="' + v.count + '"><b>' + v.workstation + '</b></td>';
            $.each(v.operations, function(i, d){
              machine = (d.machine_code) ? d.machine_code : '-';
              operator_name = (d.operator_name) ? d.operator_name : '-';
              from_time = (d.from_time) ? d.from_time : '-';
              to_time = (d.to_time) ? d.to_time : '-';
              var inprogress_class = (d.status == 'In Progress') ? 'active-process' : '';
              var qc_status = (d.qa_inspection_status == 'QC Passed') ? "qc_passed" : "qc_failed";
              qc_status = (d.qa_inspection_status == 'Pending') ? '' : qc_status;
              r += '<td class="text-center '+inprogress_class+' '+qc_status+'" style="border: 1px solid #ABB2B9;"><b>' + d.process + '</b></td>' +
                  '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.good) + '</b></td>' +
                  '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.reject) + '</b></td>' +
                  '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + machine + '</td>' +
                  '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + from_time + '</td>' +
                  '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + to_time + '</td>' +
                  '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + operator_name + '</td>' +
                  '</tr>';
            });
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

  $('.modal').on('hidden.bs.modal', function(){
    var frm = $(this).find('form')[0];
    if (frm) frm.reset();
  });

  $(document).on('show.bs.modal', '.modal', function (event) {
    var zIndex = 1040 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function() {
      $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
  });
});

</script>
<style type="text/css" media="print">
   @media print{@page {size: landscape}};
   @page {
  size: A4 landscape;
}
</style>

@endsection

