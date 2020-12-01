@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'workstation_overview',
])

@section('content')
<div class="panel-header" style="margin-top: -50px;">
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
                     <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Workstation Overview</h2>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -110px;">
   <div id="dashboard-rows"></div>     
</div>

<!-- Modal -->
<div class="modal fade" id="workstation-tasks-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document" style="min-width: 50%;">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="modal-title"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="table-responsive">
               <table class="table table-striped" id="workstation-tasks-table" style="font-size: 9pt;">
                  <col style="width: 11%;">
                  <col style="width: 62%;">
                  <col style="width: 12%;">
                  <col style="width: 15%;">
                  <thead class="text-primary" style="font-size: 8pt;">
                     <th class="text-center"><b>Prod. No.</b></th>
                     <th class="text-center"><b>Item Code</b></th>
                     <th class="text-center"><b>Qty</b></th>
                     <th class="text-center"><b>Status</b></th>
                  </thead>
                  <tbody></tbody>
               </table>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
   $(document).ready(function(){
      getContent();
      setInterval(getContent, 3000);
      function getContent(){
         $.ajax({
            url:"/workstation_task_list",
            type:"GET",
            success:function(data){
               $('#dashboard-rows').html(data);
            }
         });
      }

      $(document).on('click', '.view-workstation-tasks', function(e){
         var workstation = $(this).data('workstation');
         $.ajax({
             url:"/get_workstation_task/" + workstation,
             type:"GET",
             success:function(data){
                var row = '';
                $.each(data, function(i, d){
                   if (d.status == 'Pending') {
                      badge_color = 'primary';
                   }else if (d.status == 'In Progress') {
                      badge_color = 'warning';
                   }else if (d.status == 'Completed') {
                      badge_color = 'success';
                   }else{
                      badge_color = 'default';
                   }
 
                   var ops = '';
                   if (d.operator_name) {
                      ops = '<br><span style="font-size: 9pt;">' + d.operator_name + '<br>' + d.machine + '</span>';
                   }
 
                   var qty = (d.status == 'Unassigned') ? d.bal : d.qty_to_manufacture;
                   row += '<tr>' +
                      '<td class="text-center">' + d.production_order + '</td>' +
                      '<td class="text-justify"><b>' + d.item_code + '</b><br>' + d.description + '</td>' +
                      '<td class="text-center" style="font-size: 9pt;">' + qty + '</td>' +
                      '<td class="text-center">' +
                         '<span class="badge badge-' +  badge_color +'" style="font-size: 9pt; color: #fff;">' + d.status + '</span>' +
                         ops + '</td>';
                      '</tr>';
                });
 
                $('#workstation-tasks-table tbody').html(row);
                $('#workstation-tasks-modal .modal-title').text(workstation);
                $('#workstation-tasks-modal').modal('show');
             }
          }); 
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
   });
</script>
@endsection