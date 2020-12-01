@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'bom_list',
])

@section('content')
<div class="panel-header" style="margin-top: -20px;">
   <div class="header text-center">
    <div class="row">
         <div class="col-md-12">
            <table style="text-align: center; width: 100%;">
               <tr>
                  <td style="width: 36%; border-right: 5px solid white;">
                     <h2 class="title">
                        <div class="pull-right" style="margin-right: 20px;">
                           <span style="display: block; font-size: 20pt;">{{ date('M-d-Y') }}</span>
                           <span style="display: block; font-size: 12pt;">{{ date('l') }}</span>
                        </div>
                     </h2>
                  </td>
                  <td style="width: 14%; border-right: 5px solid white;">
                     <h2 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h2>
                  </td>
                  <td style="width: 50%">
                     <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Bill of Materials</h2>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -80px;">
   <div class="row">
      <div class="col-md-8 offset-md-2">
          <div class="card" style="background-color: #0277BD;">
              <div class="card-body" style="padding-bottom: 0;">
                  <div class="row">
                      <div class="col-md-9">
                          <h5 class="text-white font-weight-bold align-middle">BOM List</h5>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group">
                              <input type="text" class="form-control" placeholder="Search" id="search-bom">
                          </div>
                      </div>
                  </div>
                  <div class="row" style="background-color: #ffffff;">
                      <div class="col-md-12">
                        <div id="bom-list-tbl"></div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="view-bom-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="min-width: 70%;">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bolder;">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div id="view-bom-details-div"></div>
         </div>
      </div>
   </div>
</div>

@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script>
   $(document).ready(function(){
      $.ajaxSetup({
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
      });

      get_bom_list();
      function get_bom_list(page, query){
         $.ajax({
            url: "/get_bom_list?page=" + page,
            type:"GET",
            data: {search_string: query},
            success: function(response){
               $('#bom-list-tbl').html(response);
            }
         });
      }

      $(document).on('keyup', '#search-bom', function(){
         var query = $(this).val();
         get_bom_list(1, query);
      });

      $(document).on('click', '#bom-pagination a', function(event){
         event.preventDefault();
         var page = $(this).attr('href').split('page=')[1];
         get_bom_list(page);
      });

      $(document).on('click', '.view-bom-details-btn', function(e){
         e.preventDefault();
         console.log($(this).data('bom'));
         $.ajax({
            url: "/get_bom_details/" + $(this).data('bom'),
            type:"GET",
            success:function(data){
               $('#view-bom-details-div').html(data);
            }
         });

         $('#view-bom-modal .modal-title').html('BOM Update Tool [' + $(this).data('bom') + ']');
         $('#view-bom-modal').modal('show');
      });

      $(document).on('click', '#submit-bom-review-btn', function(){
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
            showNotification("danger", 'Please select Process', "now-ui-icons travel_info");
            return false;
         }

         $.ajax({
            url: '/submit_bom_review/' + bom,
            type:"POST",
            data: {user: user, id: id, workstation: workstation, wprocess: wprocess, workstation_process: workstation_process},
            success:function(data){
               console.log(data);
               $('#view-bom-modal').modal('hide');
               showNotification("success", data.message, "now-ui-icons ui-1_check");
               get_bom_list();
            }
         });
      });

      $(document).on('click', '#add-operation-btn', function(){
         var workstation = $('#sel-workstation option:selected').text();
         var wprocess = $('#sel-process').val();
         
         if (!$('#sel-workstation').val()) {
            showNotification("warning", 'Please select Workstation', "now-ui-icons travel_info");
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
                        opt += '<option value="' + v.id + '">' + v.process + '</option>';
                     });

                     $('#sel-process').append(opt);
                  }
               }
            });
         }
      });

      $(document).on("click", ".delete-row", function(e){
         e.preventDefault();
         $(this).parents("tr").remove();
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

      $(document).on('show.bs.modal', '.modal', function (event) {
         var zIndex = 1040 + (10 * $('.modal:visible').length);
         $(this).css('z-index', zIndex);
         setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
         }, 0);
      });

      $('.modal').on('hidden.bs.modal', function(){
        var frm = $(this).find('form')[0];
        if (frm) frm.reset();
      });
   });
</script>
@endsection