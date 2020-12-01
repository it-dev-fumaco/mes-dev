@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'machine_list',
])

@section('content')
@include('modals.add_machine_modal')
<div class="panel-header" style="margin-top: -50px;">>
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
              <h3 class="title" style="margin: auto;"><span id="current-time">--:--:-- --</span></h3>
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
<div class="content" style="margin-top: -85px;">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          
          <div class="col-md-8 offset-md-2">
            <button type="button" class="btn btn-primary" id="add-machine-button" style="float: right;"><i class="now-ui-icons ui-1_simple-add"></i> Add Machine</button>
                  <div class="table-responsive">
                    <table class="table table-striped text-center">
                      <thead class="text-primary">
                        <th class="text-center"><b>No.</b></th>
                        <th class="text-center"><b>Machine Code</b></th>
                        <th class="text-center"><b>Machine Name</b></th>
                        <th class="text-center"><b>Status</b></th>
                        <th class="text-center"><b>Action(s)</b></th>
                      </thead>
                      <tbody>
                         @foreach($machine_list as $row)
                           <tr>
                           <td>{{ $row->id }}</td>
                           <td>{{ $row->machine_code }}</td>
                           <td>
                              {{ $row->machine_name }}
                           </td>
                           <td>{{ $row->status }}</td>
                           <td>
                              <a href="#" class="hover-icon"  data-toggle="modal" data-target="#edit-machinelist-{{ $row->id }}-modal">
                                <button type='button' class='btn btn-success'><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                              </a>
                              <a href="/goto_machine_profile/{{ $row->id }}" class="hover-icon"  data-toggle="modal"  style="padding-left: 5px;">
                                <button type='button' class='btn btn-info'><i class='now-ui-icons ui-1_zoom-bold'></i></button>
                              </a>
                              <a href="#" class="hover-icon"  data-toggle="modal" data-target="#delete-machinelist-{{ $row->id }}-modal" style="padding-left: 5px;">
                                <button type='button' class='btn btn-danger'><i class='now-ui-icons ui-1_simple-remove'></i></button>
                              </a>
                              
                              
                           </td>
                              @include('modals.edit_machineList_modal')
                              @include('modals.delete_machineList_modal')
                           @endforeach

                        </tr>
                      </tbody>
                    </table>
                      {{ $machine_list->links() }}
                  </div>
                </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="active-tab"></div>
<style type="text/css">
  #active-tab{
    display: none;
  }
  .scrolltbody tbody {
      display:block;
      height:620px;
      overflow-y:scroll;
  }
  .scrolltbody thead, .scrolltbody tbody tr {
      display:table;
      width:100%;
      table-layout:fixed;
  }
  .scrolltbody thead {
      width: calc( 100% - 1em )
  }
  .user-image {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.imgPreview {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.upload-btn{
   padding: 6px 12px;
}

.fileUpload {
   position: relative;
   overflow: hidden;
   font-size: 9pt;
}

.fileUpload input.upload {
   position: absolute;
   top: 0;
   right: 0;
   margin: 0;
   padding: 0;
   cursor: pointer;
   opacity: 0;
   filter: alpha(opacity=0);
}
.imgPreview1 {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
}

.upload-btn1{
   padding: 6px 12px;
}

.fileUpload1 {
   position: relative;
   overflow: hidden;
   font-size: 9pt;
}

.fileUpload1 input.upload1 {
   position: absolute;
   top: 0;
   right: 0;
   margin: 0;
   padding: 0;
   cursor: pointer;
   opacity: 0;
   filter: alpha(opacity=0);
}

</style>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script>
$(document).ready(function(){
  getMachine_list();
  $('.schedule-date').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });
  $(document).on('click', '#add-machine-button', function(){
    $('#add-machine-modal').modal('show');
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
      function getMachine_list(){
      $.ajax({
        url:"/get_machine_list",
        type:"GET",
        success:function(data){
          $('#tbl_machine_list').html(data);
        }
      });  
    }
});
</script>
<script type="text/javascript">
       $("#add-machine-frm .upload").change(function () {
         if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#add-machine-frm .imgPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
         }
      });
</script>
<script type="text/javascript">
       $("#machine_test .upload1").change(function () {
         if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#machine_test .imgPreview1').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
         }
         alert('hi');
      });  
</script>



@endsection