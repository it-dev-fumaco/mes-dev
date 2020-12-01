@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'machine_profile',
])

@section('content')
@include('modals.edit_machine_modal')
{{-- @include('modals.add_machine_process_modal') --}}

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
              <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Machine Profile</h2>
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
         <div class="row">
          <!-- <button type="button" class="btn btn-primary" id="add-machine-button"><i class="now-ui-icons ui-1_simple-add"></i> Add Machine</button> -->
          <div class="col-md-8 offset-md-2">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#edit-machine-modal" style="float: right;"><i class="now-ui-icons design-2_ruler-pencil"></i> Edit</button>
            
          </div>

          <div class="col-md-4" style="margin-top: -30px;">
            <div class="form-group" style="float: right;">
                <div style="text-align: center;" id="machine_test">
                  @php
                    $img = ($machine_list->image == null) ? '/storage/machine/change_image.png' : $machine_list->image;
                  @endphp
                  <div>
                      <img src="{{ asset($img) }}" width="190" height="180" class="imgPreview1">
                  </div>                
                </div>
            </div>       
          </div>
          <input type="hidden" name="machineID" id="machineID" value="{{ $machine_list->machine_code }}">
          <div class="col-md-8">
            <table style="width: 100%;">
              <tbody>
                <tr>
                  <td style="line-height: 25px;">
                    @if($machine_list->status == "On-going Maintenance")
                      <label style="font-size: 12pt; ">
                        <span class="dot text-blink" style="background-color:#d35400;"></span> {{ $machine_list->status }}
                      </label>
                    @else
                      <label style="font-size: 12pt;">
                        <span class="dot" style="background-color: {{ $machine_list->status == 'Available' ? '#28B463' : '#717D7E' }};"></span>{{ $machine_list->status }}
                      </label>
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>
            <table style="width: 80%;">
              <tbody>

                <tr>
                  <td style="width: 15%; line-height: 25px;"><b>Machine Code:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 25px;">{{ $machine_list->machine_code }}</td>
                  
                </tr>
                <tr>
                  <td style="width: 15%;line-height: 25px;"><b>Machine Id:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 25px;">{{ $machine_list->machine_id }}</td>
                  <td style="width: 10%;line-height: 25px;"><b>Type:</b></td>
                  <td style="width: 20%;text-align: left; line-height: 25px;">{{ $machine_list->type }}</td>
                </tr>
                <tr>
                   <td style="width: 15%;line-height: 25px;"><b>Machine Name:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 25px;">{{ $machine_list->machine_name }}</td>
                  <td style="width: 10%;line-height: 25px;"><b>Model:</b></td>
                  <td style="width: 20%;text-align: left; line-height: 25px;">{{ $machine_list->model }}</td>
                </tr>
                

              </tbody>
              
            </table>
            
          </div>
          <div class="col-md-8 offset-md-2">
            <h5 style="padding-left: 30px;">Machine Breakdown History</h5>
              <div id="tbl_machine_profile" style="padding-left: 30px;"></div>
          </div>
          {{-- <div class="col-md-8 offset-md-2">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-machine-process-modal" style="float: right;"> Add Process</button>
            
          </div> --}}
          <div class="col-md-8 offset-md-2">
            <h5 style="padding-left: 30px;">Machine Process</h5>
            <div style="padding-left: 30px;">
              <table class="table table-bordered" style="font-size: 12px; width: 100%;">
              <thead>
                <tr style="font-size: 10px;" class="text-center">
                  <th scope="col" style="font-weight: bold;"><b>#</b></th>
                  <th scope="col" style="font-weight: bold;"><b>Process</b></th>
                  {{-- <th scope="col" style="font-weight: bold;"><b>Action/s</b></th>--}}
                  
                  
                </tr>
              </thead>
              <tbody>
                @forelse($process_list as $rows)
                     
                     <tr>
                     
                        <td class="text-center">{{ $rows->process_id }}</td>
                        <td class="text-center">{{ $rows->process_name }}</td>
                        {{-- <td class="text-center">
                          <a href="#" class="hover-icon"  data-toggle="modal" data-target="#delete-machine-process-{{ $rows->id }}-modal">
                            <button type='button' class='btn btn-danger btn-sm'><i class='now-ui-icons ui-1_simple-remove' style="font-size: 8pt;font-weight: bold;"></i></button>
                              
                          </a>                                                       
                        </td>
                        @include('modals.delete_machine_process_modal') --}}
                     </tr>
                     
                     @empty
                     <tr>
                        <td colspan="10" class="text-center">No record found.</td>
                     </tr>
                     @endforelse
                
              </tbody>
            </table>
            </div>
                          
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
   .dot {
  height: 15px;
  width: 15px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
}
   .dotsmall {
  height: 8px;
  width: 8px;
  background-color: #bbb;
  border-radius: 50%;
  display: inline-block;
}
   .text-blink {color: orange;
  animation: blinker 1s linear infinite;
}
#table-wrapper {
  position:relative;
}
#table-scroll {
  height:350px;
  overflow:auto;  
  margin-top:20px;
}
#table-wrapper table {
  width:100%;

}
#table-wrapper table * {
  color:black;
}
#table-wrapper table thead th .text {
  position:absolute;   
  top:-20px;
  z-index:2;
  height:20px;
  width:35%;
  border:1px solid;
}
@keyframes blinker {  
  50% { opacity: 0; }
}

</style>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script>
$(document).ready(function(){
  getMachine_list();
  tbl_machine_breakdown();
  get_workstation_process();
  $('.schedule-date').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });
  $(document).on('click', '#add-machine-button', function(){
    $('#add-machine-modal').modal('show');
    });

  $(document).on('click', '#add-machine-process-button', function(){
    $('#add-machine-process-modal').modal('show');
    });

  $(document).on('click', '#machine_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    tbl_machine_breakdown(page);
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
       $("#edit-machine-frm .upload").change(function () {
         if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#edit-machine-frm .imgPreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
         }
      });
</script>
<script type="text/javascript">
  function tbl_machine_breakdown(page){
    var idno = $('#machineID').val();
  
    $.ajax({
          url:"/get_tbl_machine_profile/?page="+page,
          type:"GET",
          data:{ id:idno },
          success:function(data){
            $('#tbl_machine_profile').html(data);
          }
        });  
}
</script>
<script type="text/javascript">
  function get_workstation_process(){
    var workstation = $('#workstation_process').val();

        $.ajax({
          url:"/get_workstation_process_jquery/"+ workstation,
          type:"GET",
          success:function(data){
            $('#process').html(data);
          }
        }); 
  }
</script>




@endsection