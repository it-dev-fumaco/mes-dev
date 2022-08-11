@extends('layouts.user_app', [
    'namePage' => 'Machine List',
    'activePage' => 'maintenance_machine_list',
])

@section('content')
@include('modals.edit_machineList_modal')
@include('modals.delete_machineList_modal')
@include('modals.add_machine_modal')

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
                     <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Machine List</h2>
                  </td>
               </tr>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="content" style="margin-top: -110px;">
    <div class="row p-0">
        <div class="col-8 mx-auto p-0" style="background-color: #F7F7F9; margin-top: 60px;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-3 offset-7">
                        <input type="text" id='machine-list' class="form-control m-3" placeholder="Search">
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary w-100" id="add-machine-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Machine</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 p-3" id="tbl_setting_machine_list"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script>
        $(document).ready(function(){
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

            setting_machine_list(1, $('#machine-list').val());
            function setting_machine_list(page, query){
                $.ajax({
                    url:"/get_tbl_setting_machine_list?page=" + page,
                    data: {search_string: query},
                    type:"GET",
                    success:function(data){
                        $('#tbl_setting_machine_list').html(data);
                    }
                });
            }

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

            $(document).on('click', '.btn-edit-machine', function(){
                var machine_id = $(this).data('machineid');
                var machine_name = $(this).data('machinename');
                var reference_key = $(this).data('referencekey');
                var machine_code = $(this).data('machinecode');
                var status = $(this).data('status');
                var type = $(this).data('type');
                var model = $(this).data('model');
                var image = $(this).data('image');
                var imagevar = image;
                // alert(imagevar);
                $("#machine_image").attr("src","");
                $("#machine_image").attr("src",imagevar);
                $('#edit_orig_image').val(image);
                $('#editt_machineid').val(reference_key);
                $('#edit_origmachine_code').val(machine_code);

                $('#edit_machineid').val(machine_id);
                $('#edit_machinecode').val(machine_code);
                $('#edit_machine_name').val(machine_name);
                $('#edit_machine_type').val(type);
                $('#edit_machine_model').val(model);
                $('#edit_machine_status').val(status);
                // $('#machine_image_forupload').val(image);
                
                $('#edit-machine-modal').modal('show');
            });

            $('#edit-machine-frm').submit(function(e){
                e.preventDefault();
                var url = $(this).attr("action");
                var form1 = $(this).get(0); 
                $.ajax({
                    url: url,
                    type:"POST",
                    data: new FormData(form1),
                    processData: false,
                    contentType: false,
                    success:function(data){
                        if (data.success < 1) {
                            showNotification("danger", data.message, "now-ui-icons travel_info");
                        }else{
                            showNotification("success", data.message, "now-ui-icons ui-1_check");
                            $('#edit-machine-modal').modal('hide');
                            $('#edit-machine-frm').trigger("reset");
                            $('#edit-machine-modal').trigger("reset");
                            $("#edit-machine-frm .imgPreview").attr("src","");
                            setting_machine_list();
                        }
                    }
                });
            });

            $(document).on('click', '.btn-delete-machine', function(){
                var machine_id = $(this).data('machineid');
                var machine_name = $(this).data('machinename');
                var reference_key = $(this).data('referencekey');
                var machine_code = $(this).data('machinecode');
                var status = $(this).data('status');
                var type = $(this).data('type');
                var model = $(this).data('model');
                var image = $(this).data('image');
                
                // alert(operation);
                $('#delete-machine-id').val(machine_id);
                // alert(machine_id);
                $('#delete-machine-code').val(machine_code);
                $('#machine_code_label').text(machine_code);
                
                $('#delete-machinelist-modal').modal('show');
            });

            $('#delete-machine-frm').submit(function(e){
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
                            $('#delete-machinelist-modal').modal('hide');
                            setting_machine_list();
                        }
                    }
                });
            });

            $('#machine-list').keyup(function(){
                var query = $(this).val();
                setting_machine_list(1, query);
            });

            $(document).on('click', '#add-machine-button', function(){
                $('#add-machine-modal').modal('show');
                $('#add-worktation-frm').trigger("reset");
            });

            $(document).on('click', '#setting_machine_list_pagination a', function(event){
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                var query = $('#machine-list').val();
                setting_machine_list(page, query);
            });

            @if (session()->has('success'))
                showNotification("success", "{{ session()->get('success') }}", "now-ui-icons ui-1_check");
            @endif

        });
    </script>
@endsection