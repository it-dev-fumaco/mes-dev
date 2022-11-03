@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'workstation_profile',
])

@section('content')
@include('modals.add_workstation_to_process_profile_modal')
@include('modals.add_machine_to_workstation_processprofile')


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
              <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Processs Profile</h2>
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
          <div class="col-md-12">
            <a href="/settings_module" class="pull-left">
              <img src="{{ asset('storage/back.png') }}"  width="40" height="40"/>
            </a>
            <input type="hidden" name="process_id_val" id="process_id_val" value="{{ $process->id }}">
            <h3 style="padding-left: 50px;"><b>{{ $process->process }}</b></h3>

          </div>
          {{--<div style="margin-top: -23px; padding-left: 70px;"> <h6 style="line-height: 5px;"><b>{{ $list->operation }}</b><hr></h6>
          </div>
          --}}
          <div class="col-md-12"><button type="button" class="btn btn-primary" id="add-machine-to-workstation-button">Process Assignment</button> </div>
          <div class="col-md-10 offset-md-1">

            <div class="col-md-6" style="float: right;padding-right: 10px;">
                  <button type="button" class="btn btn-primary" id="add-machine-to-workstation-button" style="float: right;"> Add Machine </button>                    <h5 class="text-center"><b>Assigned to Machine</b></h5>

                  <div id="get_tbl_assigned_machine_process"></div>
              </div>
              <div class="col-md-6">
                <button type="button" class="btn btn-primary" id="add-workstation-button" style="float: right;"> Add Workstation</button>
                    <h5 class="text-center"><b>Assigned to Workstation</b></h5>
                <div id="get_tbl_assigned_workstation_process"></div>
                  
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
.span_bold{
  font-weight: bold;

}
.boldwrap {
  font-weight: bold;
}

.select2-rendered__match {
  background-color: yellow;
  color: black;
}
</style>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('css/datepicker/bootstrap-datepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker/bootstrap-datepicker.css') }}" />
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />
<script>
$(document).ready(function(){
  getMachine_list();
  get_tbl_workstation_process();
  get_tbl_assigned_machine_process();
  $('.schedule-date').datepicker({
    'format': 'yyyy-mm-dd',
    'autoclose': true
  });
  $(document).on('click', '#add-workstation-button', function(){
    $('#add-workstation-to-process-modal').modal('show');
    });

  $(document).on('click', '#add-machine-to-workstation-button', function(){
    $('#add-machine_to_workstation-processprofile-modal').modal('show');
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
      });  
</script>
<script type="text/javascript">
  function machine_assign_description(){
    var id = $('#machine_code_selection').val();
    data={
      id : id
    }
    $.ajax({
        url:"/get_machine_list",
        data:data,
        type:"GET",
        success:function(data){
          $('#tbl_machine_details').html(data);
        }
      });  
  }
</script>
<script type="text/javascript">
  function get_tbl_assigned_machine_process(page){
    var process_id = $('#process_id_val').val();
    $.ajax({
          url:"/get_tbl_assigned_machine_process/?page="+page,
          type:"GET",
          data:{ process_id:process_id },
          success:function(data){
            $('#get_tbl_assigned_machine_process').html(data);
          }
      });  
  }
</script>

<script type="text/javascript">
      $('#delete-process-frm').submit(function(e){
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
                // $('#add-workstation-modal').modal('hide');
                // $('#add-worktation-frm').trigger("reset");
                location.reload(true);

          }
        }
      });
    });
</script>
<script type="text/javascript">
    $(document).on('click', '#processWorkstation_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    get_tbl_workstation_process(page);
  });
    $(document).on('click', '#machineWorkstation_pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    get_tbl_workstation_machine(page);
  });
</script>
<script type="text/javascript">
  function get_tbl_workstation_process(page){
    var process_id = $('#process_id_val').val();
    // console.log(process_id);
    $.ajax({
          url:"/get_tbl_assigned_workstation_process/?page="+page,
          type:"GET",
          data:{ process_id:process_id },
          success:function(data){
            $('#get_tbl_assigned_workstation_process').html(data);
          }
      });  
  }
</script>
<script>

  function markMatch(text, term) {

    var startString = '<span class="boldwrap">';
    var endString = text.replace(startString, '');

    var match = endString.toUpperCase().indexOf(term.toUpperCase());
    var $result = $('<span></span>');

    if (match < 0) {
      return $result.text(text);
    }
    var elementToReplace = endString.substr(match, term.length);
    var $match = '<span class="select2-rendered__match">' + endString.substring(match, match + term.length) + '</span>';
    text = startString + endString.replace(elementToReplace, $match);

    // console.log(text);
    $result.append(text);
    return $result;
  }

  $('#machine_code_selection').select2({
    dropdownParent: $("#add-machine_to_workstation-processprofile-modal"),
    dropdownAutoWidth: false,
    width: '100%',
    templateResult: function(item) {
      if (item.loading) {
        return item.text;
      }
      var term = query.term || '';
      var $result = markMatch('<span class="boldwrap">' + item.text.substring(0, item.text.indexOf("-")) + '</span>' + item.text.substring(item.text.indexOf("-")), term);
      return $result;

    },

    language: {
      searching: function(params) {
        // Intercept the query as it is happening
        query = params;
        // Change this to be appropriate for your application
        return 'Searching...';
      }
    },
    cache: true
  });
</script>

@endsection