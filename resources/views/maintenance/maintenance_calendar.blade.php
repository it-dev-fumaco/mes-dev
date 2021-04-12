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
                  <h5 class="text-black font-weight-bold align-middle" style="padding-left: 60px;">Maintenance Calendar  - {{$operation_name}}</h5>
              </div>
              <div class="col-md-4" style="float: right;margin-top: -30px;"><h6 class="text-center" style="padding-top: 30px;display: inline-block;">Legend:</h6>
                  <span class="dot_css" style="background-color:#45b39d; margin-left: 25px;"></span>
                  <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Preventive Maintenance</span>
                  <span class="dot_css" style="background-color:#bb8fce; margin-left: 25px;"></span>
                  <span style="font-size: 12pt; padding-right: 13px;padding-left: 3px;">Maintenance Request</span>
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

<style type="text/css">
    .dot_css {
    height: 12px;
    width: 12px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }
  #datetodayModal .form-control{
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;  
  }
  #reschedule-deli-modal .form-control{
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;  
  }
  .text_shorter {
    display: block;
  width:auto;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  font-size: 8px;
}

</style>
@endsection

@section('script')
<link rel="stylesheet" href="{{ asset('css/calendar/fullcalendar.css') }}" />
   <script src="{{ asset('css/calendar/moment.min.js') }}"></script>
   <script src="{{ asset('css/calendar/fullcalendar.min.js') }}"></script>
<script>
  $(document).ready(function() {
    $('#planned_start_datepicker').val('');
      $(document).on('change', '.custom-control-input', function(){
        if($('input[name="prodname[]"]:checked').length == 0){
          $( "#planned_start_datepicker" ).prop( "disabled", true );
        }else{
          $( "#planned_start_datepicker" ).prop( "disabled", false );
        }
        var someObj = {};
        someObj.slectedbox = [];
        someObj.unslectedbox = [];
        name = $(this).data('dateslct');
        inputid = "#selected_prod_order";
        console.log(someObj.slectedbox);
        
        $('.custom-control-input').each(function() {
          if ($(this).is(":checked")) {
            someObj.slectedbox.push($(this).attr("data-dateslct"));
          } else {
            someObj.unslectedbox.push($(this).attr("data-dateslct"));
          }
        });    
        $(inputid).val(someObj.slectedbox);
      });
      $(document).on('change', '#planned_start_datepicker', function(){
        var prod_list = $('#selected_prod_order').val();
        var operation = $('#operation_id').val();
        var planned = $(this).val();
        if(operation == "3"){
          $.ajax({
                url: '/get_assembly_prod_calendar',
                type: 'get',
                data: { 'prod_list': prod_list, 'planned':planned},
                success: function(data)
                  {
                    if(data.success == 0){
                      $("#fabrication_update").attr("disabled", false);

                    }else{
                      $('#tbl_container_details').html(data);
                      $('#reschedule-deli-modal').modal('show');
                      $("#fabrication_update").attr("disabled", true);
                    }
                  },
                error: function(jqXHR, textStatus, errorThrown) {
                  console.log(jqXHR);
                  onsole.log(textStatus);
                  console.log(errorThrown);
                }
          });
        }else{
          $("#fabrication_update").attr("disabled", false);

        }
        console.log(prod_list);
      });
      
      if($('input[name="prodname[]"]:checked').length == 0){
        $( "#planned_start_datepicker" ).prop( "disabled", true );
      }else{
        $( "#planned_start_datepicker" ).prop( "disabled", false );
      }
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
        views: {
          month: {
            eventLimit: 8
          }
        },
        
        dayClick: function(date, jsEvent, view) {
          $('#datetodayModal').modal();
          var newDate = changeDate(date.format());
          var normal_date_format = date.format();
          var primary_operation_id = $('#operation_id').val();
          $("#date_tobe_sched_shift").val(normal_date_format);
          tbl_shift_schedule_sched(1, normal_date_format,primary_operation_id);
          $('#datetodayModal .modal-title').text(newDate);
          get_default_shift_sched(normal_date_format,primary_operation_id );
          $('#datetodayModal').modal('show');
          prod_list_calendar(normal_date_format,primary_operation_id);
          $("#tbl_opration_id").val(primary_operation_id);
          $('#planned_start_datepicker').val('');

        },
        eventClick: function(event, jsEvent, view) {
          if (event.status == "Completed") {  
          }else{
            $('#start_time').val(moment(event.start).format('YYYY-MM-DD'));
            $('#prod_no').val(event.id);
            $('#editModal').modal();
          }
        
        },
        eventRender: function(event, element){
        if (event.status == "Completed") {
             event.startEditable = false;
  
             }
         var btn = document.createElement("div");
         var prod = '<span style="font-size:18px;"><b>'+ event.id + '</b></span>'+ '<b> &nbsp;['+ event.frequency + ']</b>';
        var p = '<b>Machine:</b>' + event.machine;
        var r = '<b> Customer:</b>&nbsp; '+ event.customer;
        var qty = '<b> QTY:</b>&nbsp; '+ event.qty + '&nbsp;' + event.uom;
        var delivery_date = '<b> Maintenance Type:</b>&nbsp; '+ event.description;
        var fortooltip= prod+ '<br><span style="font-size:14px;">'+ p+'<br>'+ delivery_date +'<br></span>';
          element.popover({
            html: true,
              animation:true,
              content: fortooltip,
              trigger: 'hover',
            container: 'body'
          });
        },
         eventSources: [{
            url: '/preventive_maintenance_calendar/'+ operation_id,
            type: 'GET',
            error: function() {
            }
         }],
        
      });
      
   });
</script>
<script type="text/javascript">
function changeDate(date){
    let currentDate = new Date(date);
    var fd = currentDate.toDateString();
    return fd;
    }
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
</script>
<script>
$(document).ready(function(){

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
    window.location.href = "/maintenance_dashboard";

});
</script>

<style type="text/css" media="print">
   @media print{@page {size: landscape}};
   @page {
  size: A4 landscape;
}
</style>

@endsection

