@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
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
                <a href="#" class="pull-left" id="back_id">
              <img src="{{ asset('storage/back.png') }}"  width="40" height="40"/>
            </a>
                  <h5 class="text-black font-weight-bold align-middle" style="padding-left: 60px;">Production Calendar  - {{$operation_name}}</h5>
              </div>
              <div class="col-md-4" style="float: right;margin-top: -30px;"><h6 class="text-center" style="padding-top: 30px;display: inline-block;">Legend:</h6>
                  <span class="dot_css" style="background-color:#717D7E; margin-left: 25px;"></span>
                  <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Not Started</span>
                  <span class="dot_css" style="background-color:#EB984E; margin-left: 25px;"></span>
                  <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">In Progress</span>
                  <span class="dot_css" style="background-color:#58d68d; margin-left: 25px;"></span>
                  <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Completed</span>
                  <input type="hidden" value="{{$operation_id}}" id="operation_id">
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
                <input type="button" class="btn btn-primary" id="fabrication_update" value="Save">
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
      var operation_id= $('#operation_id').val();
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
                url: '/calendar/update_planned_start_date',
                type: 'POST',
                data: { 'scheduledtime': event.start.format(), 'prodid' : event.id, 'operation_id': operation_id},
                success: function(event)
                  {},
                error: function(jqXHR, textStatus, errorThrown) {
                  console.log(jqXHR);
                  onsole.log(textStatus);
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
         var prod = '<span style="font-size:18px;"><b>'+ event.id + '</b></span>';
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
            url: '/get_production_schedule_calendar/'+ operation_id,
            type: 'GET',
            error: function() {
            }
         }],
        
      });
   });
</script>
<script type="text/javascript">
  $('#fabrication_update').click(function(e) {
    var operation_id= $('#operation_id').val();
        e.preventDefault();
        var data = {
            _token: '{{ csrf_token() }}',
            prod_id: $('#prod_no').val(),
            start_time: $('#start_time').val(),
            operation_id: operation_id
        };

        $.post('{{ route('fabrication.ajax_update') }}', data, function( result ) {
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

  // $(document).on('click', '.prod-details-btn', function(e){
  //   e.preventDefault();
  //   var jtno = $(this).data('jtno');
  //   $('#jt-workstations-modal .modal-title').text(jtno);
  //   if(jtno){
  //     getJtDetails($(this).data('jtno'));
  //   }else{
  //     showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
  //   }
  // });

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
$(document).on('click', '#back_id', function(event){
  event.preventDefault();
  var operation_id= $('#operation_id').val();
    window.location.href = "/production_schedule/"+ operation_id;

});

</script>
<style type="text/css" media="print">
   @media print{@page {size: landscape}};
   @page {
  size: A4 landscape;
}
</style>

@endsection

