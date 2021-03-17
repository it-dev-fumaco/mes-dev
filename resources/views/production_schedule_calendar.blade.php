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

<div class="modal fade" id="datetodayModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document" style="min-width: 68%;">
        <div class="modal-content">
          <div class="modal-header text-white align-top" style="background-color: #0277BD;">
            <img src="{{ asset('img/calendar4.png') }}" width="30" class="align-middle" style="display: inline-block; margin-right:5px;">
            <h5 class="modal-title" style="display: inline;">&nbsp;Shift Schedule</h5>
            <button type="button" class="close btn-close-click-validator" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           <div class="modal-body">
             <div class="row">
               <div class="col-md-8" style="padding: 1px 2px 1px 5px;">
                 <div class="row">
                   <div class="col-md-12">
                    <div class="card">
                      <div class="card-header"  style="padding: 1px 1px 1px 1px;">
                       <h5 class="card-title" style="font-size:13pt;">&nbsp;&nbsp;<b>Shift Schedule</b></h5>
                      </div>
                      <input type="hidden" class="selected_prod_order" id="selected_prod_order">
                      <div class="card-body">
                        <input type="hidden" id='tbl_opration_id' name="operation_id">
                       <input type="hidden" id="date_tobe_sched_shift" name="date">
                       <input type="hidden" id="date_reload_tbl" name="date_reload_tbl">
                       <input type="hidden" name="pagename" value="calendar" id="pagename">
                       <div id="default_shift_sched" style="margin-top: -10px;"></div>
                       <div id="old_ids"></div>
                         <table class="table table-bordered" style="margin-top: 5px;">
                           <col style="width: 50%;">
                           <col style="width: 20%;">
                           <col style="width: 20%;">
                           <col style="width: 10%;">
                           <tr>
                             <th class="text-center">Shift</th>
                             <th class="text-center">Time-in</th>
                             <th class="text-center">Time-out</th>
                             <th></th>
                           </tr>
                           <tbody id="shiftsched-table">
                           </tbody>
                         </table>
                       <div class="pull-left">
                         <button type="button" class="btn btn-info btn-sm" id="add-row-shift-btn">
                           <i class="now-ui-icons ui-1_simple-add"></i> Add Row
                         </button>
                       </div>
                      </div>
                    </div>
                   </div>
                   <div class="col-md-12">
                    <div class="card" style="min-height: 150px;">
                      <div class="card-header"  style="padding: 1px 1px 1px 1px;">
                       <h5 class="card-title" style="font-size:13pt;">&nbsp;&nbsp;<b>Moved Planned Start Date</b></h5>
                      </div>
                      <div class="card-body">
                        <div class="form-group" style="margin-top:-10px;">
                          <label> <i><b>Note:</b> Please Check the box of the production order to moved</i></label>
                        </div>
                        <div class="form-group">
                          <label for="planned_start_datepicker" style="font-size: 12pt; color: black; display: inline-block; margin-right: 1%;"><b>Date:</b></label>
                          <input type="date" class="form-control" name="planned_start_datepicker" id="planned_start_datepicker" style="display: inline-block; width: 80%; font-weight: bolder;">
                       </div>
                      </div>
                    </div>
                  </div>
                 </div>
               </div>
               <div class="col-md-4"  style="padding: 1px 2px 1px 5px;">
                <div class="card">
                  <div class="card-header" style="padding: 1px 1px 1px 1px;">
                   <h5 class="card-title" style="font-size:13pt; margin-left:3px;">&nbsp;<b>Scheduled Production Order</b></h5>
                  </div>
                  <div class="card-body" style="height:400px;overflow:auto;">
                    <div>
                      <div id="prod_list_calendar" style="margin-top:-5px;" ></div>
                    </div>
                  </div>
                </div>
               </div>
             </div>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               <input type="button" class="btn btn-primary" id="fabrication_update" value="Save">
            </div>
        </div>
  </div>
</div>
<div class="modal fade" id="reschedule-deli-modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="min-width:40%;">
    <form action="/calendar_update_rescheduled_delivery_date" id="calendar_update_rescheduled_delivery_date_form" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header  text-white" style="background-color: #0277BD;" >
          <h5 class="modal-title">Reschedule Delivery Date</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12" id="tbl_container_details">
            
            </div>
          </div>
        </div>
        <input type="hidden" class="tbl_reload_deli_modal" name="reload_tbl" value="reloadpage">
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
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
        dayRender: function (date, cell) {
          var primary_operation_id = $('#operation_id').val();

          $.ajax({
              url:"/get_tbl_default_shift_sched",
              type:"GET",
              data: {date: date.format('YYYY-MM-DD') , operation: primary_operation_id},
              success:function(data){
                cell.css('position','relative');
                cell.append('<span class="text_shorter" style="position:absolute;bottom:0;left:0;right:0;display: block;font-size:2px;color:#000;text-align:center;cursor:pointer;">'+data+'</span>');
              }
          });
        },
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
  $('#calendar_update_rescheduled_delivery_date_form').submit(function(e){
      e.preventDefault();
        $.ajax({
          url:"/calendar_update_rescheduled_delivery_date",
          type:"POST",
          data: $(this).serialize(),
          success:function(data){
            console.log(data);
            if (data.success < 1) {
              showNotification("danger", data.message, "now-ui-icons travel_info");
            }else{
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#reschedule-deli-modal').modal('hide');
              $("#fabrication_update").attr("disabled", false);
            }
          },
          error : function(data) {
            console.log(data.responseText);
          }
        });
    });
    $('#fabrication_update').click(function(e) {
        e.preventDefault();
        var allEls = document.querySelectorAll('.required-dropdown');
        for (var i = 0; i < allEls.length; i++) {
            allEls[i].required = 'required';
        }
        var operation_id= $('#operation_id').val();
        var prodname = $("input[name='prodname[]']:checked").map(function(){return $(this).val();}).get();
        var planned_start_datepicker= $('#planned_start_datepicker').val();
        var pagename= $('#pagename').val();
        var date_reload_tbl= $('#pagename').val();
        var date = $('#date_tobe_sched_shift').val();
        var old_shift_sched = $("input[name='old_shift_sched[]']").map(function(){return $(this).val();}).get();
        var oldshift_sched_id = $("input[name='oldshift_sched_id[]']").map(function(){return $(this).val();}).get();
        var shifttype = $("input[name='shifttype[]']").map(function(){return $(this).val();}).get();
        var oldshift = $("select[name='oldshift[]']").map(function(){return $(this).val();}).get();
        var newshift = $("select[name='newshift[]']").map(function(){return $(this).val();}).get();
        var arrayvalidation = shifttype.includes('');
        if(arrayvalidation == true){
          showNotification("danger", 'Please Select Shift Type.', "now-ui-icons travel_info");
        }else{
          var data = {
            _token: '{{ csrf_token() }}',
            prodname: prodname,
            planned_start_datepicker:planned_start_datepicker,
            pagename: pagename,
            date_reload_tbl: date_reload_tbl,
            date: date,
            operation_id: operation_id,
            old_shift_sched:old_shift_sched,
            oldshift_sched_id:oldshift_sched_id,
            shifttype:shifttype,
            oldshift:oldshift,
            newshift:newshift
        };
        if(prodname.length != 0){
          if(planned_start_datepicker.length == 0){
              showNotification("danger", 'Please Select Planned Start Date.', "now-ui-icons travel_info");
              return false;
          }
        }
          $.post('{{ route('fabrication.ajax_update') }}', data, function( result ) {
            $('#calendar').fullCalendar('removeEvents', $('#prod_no').val());
            $('#calendar').fullCalendar('refetchEvents');
            $('#datetodayModal').modal('hide');
            $( "#planned_start_datepicker" ).prop( "disabled", true );
          });
        }
        
    });
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
<script>
function tbl_shift_schedule_sched(page, date, operation){
  $("#shiftsched-table").empty();
  $.ajax({
    url:"/get_tbl_shiftsched_list/?page="+page,
    type:"GET",
    data: {date_sched: date, operation: operation},
    success: function(data){
      var old_break = '';
      $.each(data.shift, function(i, d){
	      var sel_id = d.shift_id;
	      var break_id = d.shift_schedule_id;
	      old_break += '<input type="hidden" name="old_shift_sched[]" value="'+d.shift_schedule_id+'">';
	      console.log(d.shift_schedule_id);
        var s_type= d.shift_type;
	      var row1 = '';
	      $.each(data.shift_type, function(i, d){
	        selected = (d.shift_id == sel_id) ? 'selected' : null;
	        row1 += '<option value="' + d.shift_id  + '" '+selected+'>' + d.shift_type + '</option>';
	      });
	      var thizz = document.getElementById('shiftsched-table');
	      var id = $(thizz).closest('table').find('tr:last td:first').text();
	      var validation = isNaN(parseFloat(id));
	      if(validation){
	        var new_id = 1;
	      }else{
	        var new_id = parseInt(id) + 1;
	      }
	      var len2 = new_id;
	      var id_unique="shiftin"+len2;
        var id_unique1="shiftout"+len2;
        var id_unique2="shifttype"+len2;
        var id_stype= "#" + id_unique2;
	      var tblrow = '<tr>' +
          '<td style="display:none;">'+len2+'</td>' +
	        '<td class="p-1"><div class="form-group m-0"><input type="hidden" name="oldshift_sched_id[]"  value="'+break_id+'"><input type="hidden" style="width:100%;" name="shifttype[]" id='+id_unique2+'><select name="oldshift[]" data-shifttype='+id_unique2+' data-timein='+id_unique +' data-timeout='+id_unique1+' class="form-control m-0 count-row onchange-shift-select required-dropdown" required="required">'+row1+'</select></div></td>' +
	        '<td class="p-1"><div class="form-group m-0"><input type="text" autocomplete="off" placeholder="From Time" value="'+ d.time_in +'" id='+id_unique+' class="form-control m-0 select-input"  readonly></div></td>' +
	        '<td class="p-1"><div class="form-group m-0"><input type="text" autocomplete="off" placeholder="To Time" value="'+ d.time_out +'" id='+id_unique1+' class="form-control m-0 select-input" readonly></div></td>' +
	        '<td class="p-1 text-center"><button type="button" class="btn btn-danger btn-icon btn-icon-mini m-0 remove-row"><i class="now-ui-icons ui-1_simple-remove"></i></button></td>' +
	      '</tr>';
	      $("#datetodayModal #shiftsched-table").append(tblrow);
	      $("#datetodayModal #old_ids").html(old_break);
        $(id_stype).val(s_type);
	    });
    }
 }); 
}
function get_default_shift_sched(date, operation){
  $.ajax({
      url:"/get_tbl_default_shift_sched",
      type:"GET",
      data: {date: date , operation: operation},
      success:function(data){
        $('#default_shift_sched').html(data);
      }
  });
}
function add_row(){
  var date = $('#date_tobe_sched_shift').val();
  var operation = $('#operation_id').val();
  $.ajax({
    url:"/get_tbl_shiftsched_list",
    type:"GET",
    data: {date_sched: date, operation: operation},
    success: function(data){
      var row1 = '<option value=""></option>';
      $.each(data.shift_type, function(i, d){
        row1 += '<option value="' + d.shift_id + '">' + d.shift_type + '</option>';
      });
      var thizz = document.getElementById('shiftsched-table');
      var id = $(thizz).closest('table').find('tr:last td:first').text();
      var validation = isNaN(parseFloat(id));
      if(validation){
        var new_id = 1;
      }else{
        var new_id = parseInt(id) + 1;
      }
      var len2 = new_id;
      var id_unique="shiftin"+len2;
      var id_unique1="shiftout"+len2;
      var id_unique2="shifttype"+len2;
      var tblrow = '<tr>' +
        '<td style="display:none;">'+len2+'</td>' +
        '<td class="p-1"><div class="form-group m-0"><input type="hidden" name="shifttype[]" id='+id_unique2+'><select name="newshift[]" class="form-control m-0 count-row onchange-shift-select required-dropdown"  data-timein='+id_unique +' data-shifttype='+id_unique2+' data-timeout='+id_unique1+' required="required">'+row1+'</select></div></td>' +
        '<td class="p-1"><div class="form-group m-0"><input type="text" autocomplete="off" placeholder="From Time" value="" class="form-control m-0 select-input" id='+id_unique+'  readonly></div></td>' +
        '<td class="p-1"><div class="form-group m-0"><input type="text" autocomplete="off" placeholder="To Time" value="" class="form-control m-0 select-input" id='+id_unique1+'  readonly></div></td>' +
        '<td class="p-1 text-center"><button type="button" class="btn btn-danger btn-icon btn-icon-mini m-0 remove-row"><i class="now-ui-icons ui-1_simple-remove"></i></button></td>' +
        '</tr>';
      
      $("#datetodayModal #shiftsched-table").append(tblrow);
    } 
  });
}
$('#add-row-shift-btn').click(function(e){
      add_row();
});
$(document).on('change', '.onchange-shift-select', function(){
  var shift_id = $(this).val();
  var shift_in = $(this).attr('data-timein');
  var shift_out = $(this).attr('data-timeout');
  var shift_type = $(this).attr('data-shifttype');
  var show_data_shiftin = "#"+shift_in;
  var show_data_shiftout = "#"+shift_out;
  var show_data_shifttype = "#"+shift_type;
  $.ajax({
    url:"/shift_sched_details",
    data:{shift:shift_id},
    type:"GET",
    success:function(data){
      if(data == null){
        $(show_data_shiftin).val("");
        $(show_data_shiftout).val("");
        $(show_data_shiftout).val("");
      }else{
        $(show_data_shiftin).val(data.time_in);
        $(show_data_shiftout).val(data.time_out);
        $(show_data_shifttype).val(data.shift_type);
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      console.log(textStatus);
      console.log(errorThrown);
    }
  });
});
function prod_list_calendar(date, operation){
    var forpage ="Calendar";
    $.ajax({
      url:"/schedule_prod_calendar_details",
      type:"GET",
      data: {date: date , operation: operation, forpage:forpage},
      success:function(data){
        $('#prod_list_calendar').html(data);
      }
    });
}
</script>
<style type="text/css" media="print">
   @media print{@page {size: landscape}};
   @page {
  size: A4 landscape;
}
</style>

@endsection

