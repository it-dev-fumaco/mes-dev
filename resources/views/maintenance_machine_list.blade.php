@extends('layouts.user_app', [
    'namePage' => 'Machine List',
    'activePage' => 'maintenance_machine_list',
    'pageHeader' => 'Machine List',
    'pageSpan' => Auth::user()->employee_name
])

@section('content')
@include('modals.edit_machineList_modal')
@include('modals.delete_machineList_modal')
@include('modals.add_machine_modal')

<div class="panel-header"></div>
<div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="col-12 p-2 rounded bg-white">
        <div class="d-flex flex-row align-items-center">
            <h5 class="col-6 p-0 mt-0 mb-0 ml-3">Machine List</h5>
            <div class="col-4 p-0">
                <div class="form-group p-0 m-0">
                    <input type="text" id="machine-list" class="form-control rounded d-block" placeholder="Search">
                </div>
            </div>
            <div class="col-2 text-center">
                <button type="button" class="btn btn-primary m-0" id="add-machine-button"><i class="now-ui-icons ui-1_simple-add"></i> Add Machine</button>
            </div>
        </div>
        <div class="row">
            <div class="col-12 p-3" id="tbl_setting_machine_list"></div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
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
            $('#edit-machine-frm select[name="operation"]').val($(this).data('operation')).change();

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
            
            $('#delete-machine-id').val(machine_id);
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