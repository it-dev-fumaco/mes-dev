@extends('layouts.user_app', [
'namePage' => 'Fabrication',
'activePage' => 'workstation_profile',
'pageHeader' => 'Workstation Profile',
'pageSpan' => Auth::user()->employee_name
])

@section('content')

<div class="panel-header"></div>
<div class="row p-0" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 p-0 m-0">
              <div class="d-flex flex-row m-0 p-0 align-items-center">
                <div class="col-4 p-0">
                  <div class="d-flex align-items-center m-0 p-0">
                    <a href="/production_settings" class="pull-left col-2">
                      <img src="{{ asset('storage/back.png') }}" class="w-100"/>
                    </a>
                    <div class="d-block col-10">
                      <span style="font-size: 20pt;" class="d-block font-weight-bold">{{ $list->workstation_name }}</span>
                      <span style="font-size: 12pt; text-transform: uppercase" class="d-block font-weight-bold text-uppercase">{{ $list->operation }}</span>
                    </div>
                  </div>
                </div>
                <div class="col-4">
                  <h5 class="text-center m-0"><b>Assigned Workstation Process List</b></h5>
                </div>
                <div class="col-4 text-right">
                  <button class="btn btn-primary text-right" data-toggle="modal" data-target="#add-process-modal">
                    <i class="now-ui-icons ui-1_simple-add"></i> Add Process
                  </button>
                </div>
              </div>
              <div id="tbl_process_workstation_list" class="mt-3"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add-process-modal" tabindex="-1" role="dialog"
aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalLabel">Add Process</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <form action="/process_assignment/{{ $id }}/add" class="add-process-form" method="post">
      @csrf
      <div class="modal-body">
        <p><b>Process</b></p>
        <div id="add-process-container">
          <select name="process" class="form-control process-list" style="width: 100% !important;"></select>
        </div>
        <br>
        <br>
        <table id="add-process-machine-tbl" class="table table-striped w-100">
          <col style="width: 80%">
          <col style="width: 20%">
          <tr>
            <th>Machine</th>
            <th>
              <div id="add-process-machine-container">
                <button class="btn btn-sm btn-primary w-100 add-row" data-tbl="#add-process-machine-tbl" style="font-size: 9pt;"><i class="now-ui-icons ui-1_simple-add"></i> Add</button>
              </div>
            </th>
          </tr>
          <tr>
            <td colspan=2>
              <select name="process_machines[]" class="form-control machine-list" style="width: 100% !important;"
                required></select>
            </td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="add-process-btn">Save changes</button>
      </div>
    </form>
  </div>
</div>
</div>

<div id="active-tab"></div>
<style type="text/css">
  #active-tab {
    display: none;
  }

  .scrolltbody tbody {
    display: block;
    height: 620px;
    overflow-y: scroll;
  }

  .scrolltbody thead,
  .scrolltbody tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
  }

  .scrolltbody thead {
    width: calc(100% - 1em)
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

  .upload-btn {
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

  .upload-btn1 {
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

  .span_bold {
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
    const workstation_id = '{{ $id }}';
    load_machine_select($('#add-process-machine-container'))
    get_tbl_workstation_process();
    setInterval(updateClock, 1000);

    $(document).on('click', '#add-machine-button', function(){
      $('#add-workstation-machine-modal').modal('show');
    });

    $(document).on('submit', '.add-process-form', function (e){
      e.preventDefault();
      if(check_form_validity($(this))){
        $.ajax({
          url: $(this).attr('action'),
          type: "post",
          data: $(this).serialize(),
          success:(response) => {
            if(!response.success){
              showNotification("danger", response.message, "now-ui-icons travel_info");
              return false
            }

            showNotification("success", response.message, "now-ui-icons ui-1_check");
            $('.modal').modal('hide')
            reset_form()
            get_tbl_workstation_process();
          },
          error:(xhr) => {
            showNotification("danger", 'An error occured. Please try again.', "now-ui-icons travel_info");
          }
        });
      }
    })

    $(document).on('click', '.remove-assigned-process', function (e){
      e.preventDefault();
      var id = $(this).data('id')
      $.ajax({
        url:"/process_assignment/" + workstation_id + "/remove/" + id,
        type:"GET",
        success:(response) => {
          if(!response.success){
            showNotification("danger", response.message, "now-ui-icons travel_info");
            return false
          }

          showNotification("success", response.message, "now-ui-icons ui-1_check");
          $('.modal').modal('hide')
          get_tbl_workstation_process();
        },
        error:(xhr) => {
          showNotification("danger", 'An error occured. Please try again.', "now-ui-icons travel_info");
        }
      });  
    })

    $('.process-list').select2({
      placeholder: 'Select a process',
      dropdownParent: $('#add-process-container'),
      ajax: {
          url: '/process_select_data',
          method: 'GET',
          dataType: 'json',
          data: (data) => {
              return {
                  q: data.term
              };
          },
          processResults: (response) => {
              return {
                  results: response
              };
          },
          cache: true
      }
    });

    $(document).on('click', '.add-row', function(e){
      e.preventDefault();
      var tbl = $(this).data('tbl');
      clone_row(tbl)
    })

    $(document).on('click', '.remove-row', function(e){
			e.preventDefault();
			$(this).closest("tr").remove();
		});

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

    function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 300,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }

    function clone_row(table, select) {
      var uniq = 'row-' + uniqId();
      var row = '<tr class="clone">' +
        '<td>' +
            '<div id="row-' + uniq + '">' +
              '<select name="process_machines[]" class="form-control machine-list" style="width: 100% !important;" required></select>' +
            '</div>' +
        '</td>' +
        '<td class="text-center">' +
          '<button class="btn btn-sm btn-danger remove-row">&times;</button>' +
        '</td>' +
      '</tr>';

      $(table).append(row);
      load_machine_select($('#row-' + uniq));
    }

    const uniqId = (() => {
      let i = 0;
      return () => {
          return i++;
      }
    })();

    function load_machine_select(parent){
      console.log(parent)
      $('.machine-list').select2({
        placeholder: 'Select a machine',
        dropdownParent: parent,
        ajax: {
          url: '/machine_select_data',
          method: 'GET',
          dataType: 'json',
          data: (data) => {
              return {
                  q: data.term
              };
          },
          processResults: (response) => {
              return {
                  results: response
              };
          },
          cache: true
        }
      });
    }

    function get_tbl_workstation_process(page){
      $.ajax({
        url:"/workstation_profile/" + workstation_id,
        type:"GET",
        data:{ page:page },
        success: (data) => {
          $('#tbl_process_workstation_list').html(data);
        },
        error: (xhr) => {
          showNotification("danger", 'An error occured. Please try again.', "now-ui-icons travel_info");
        }
      });  
    }

    function check_form_validity(form){
      var reportValidity = form[0].reportValidity();

      return reportValidity ? 1 : 0;
    }

    function reset_form(){
      $('select').empty().trigger('change')
      $('.clone').remove()
    }
});
</script>

@endsection