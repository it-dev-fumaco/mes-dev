@extends('layouts.user_app', [
  'namePage' => 'MES',
  'activePage' => 'maintenance_request_page',
  'pageHeader' => 'Maintenance Request(s)',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
  <div class="col-9 p-0">
    @php
      $status_arr = array('Pending', 'On Hold', 'In Process', 'Done');;
    @endphp
    <ul class="nav nav-tabs nav-justified">
      <li class="tab custom-nav-link col-2" heading="Justified" style="background-color: #808495 !important">
        <a data-toggle="tab" onclick="changeTab('fabrication', 1)" href="#tab-fabrication">
          <span class="tab-number" id="fabrication-count">{{ $fabrication }}</span> 
          <span class="tab-title">&nbsp;Fabrication&nbsp;</span> 
        </a>
      </li>
      <li class="tab custom-nav-link col-2" heading="Justified" style="background-color: #2196E3 !important">
        <a data-toggle="tab" onclick="changeTab('painting', 2)" href="#tab-painting">
          <span class="tab-number" id="painting-count">{{ $painting }}</span> 
          <span class="tab-title">&nbsp;Painting&nbsp;</span> 
        </a>
      </li>
      <li class="tab custom-nav-link col-2" heading="Justified" style="background-color: #8BC753 !important">
        <a data-toggle="tab" onclick="changeTab('wiring', 3)" href="#tab-wiring">
          <span class="tab-number" id="wiring-count">{{ $wiring }}</span> 
          <span class="tab-title">&nbsp;Wiring and Assembly&nbsp;</span> 
        </a>
      </li>
    </ul>
    <div class="tab-content" style="margin-top: -1px;">
      <div class="tab-pane active" id="tab-fabrication">
        <div class="tab-heading tab-heading--gray p-1">
          <div class="row p-0 m-0">
            <div class="col-6 p-0">
              <input class="d-none" type="text" value="All" id="fabrication-current-status">
              <div class="row m-0 p-1">
                @foreach ($status_arr as $status)
                <label class="PillList-item p-0 mb-0 mr-2 ml-0">
                  <input type="checkbox" class="fabrication-checkbox m-0" value="{{ $status }}">
                  <span class="PillList-label">{{ $status }}</span>
                </label>
                @endforeach
              </div>
            </div>
            <div class="col-6 p-0 text-right">
              <div class="form-group d-inline-block m-1">
                <input type="text" data-div="fabrication" data-op="1" class="form-control bg-white fabrication-search-filter maintenance-search rounded" placeholder="Search">
              </div>
            </div>
          </div>
        </div>
        <div class="row p-0 m-0">
          <div class="col-12 m-0 p-1 bg-white" id="fabrication-div" style="min-height:500px; border-top: 1px solid #D3D7DA;"></div>
        </div>
      </div>
      <div class="tab-pane" id="tab-painting">
        <div class="tab-heading tab-heading--blue p-1">
          <div class="row p-0 m-0">
            <div class="col-6 p-0">
              <input class="d-none" type="text" value="All" id="painting-current-status">
              <div class="row m-0 p-1">
                @foreach ($status_arr as $status)
                <label class="PillList-item p-0 mb-0 mr-2 ml-0">
                  <input type="checkbox" class="painting-checkbox m-0" value="{{ $status }}">
                  <span class="PillList-label">{{ $status }}</span>
                </label>
                @endforeach
              </div>
            </div>
            <div class="col-6 p-0 text-right">
              <div class="form-group d-inline-block m-1">
                <input type="text" data-div="painting" data-op="2" class="form-control bg-white painting-search-filter maintenance-search rounded" placeholder="Search">
              </div>
            </div>
          </div>
        </div>
        <div class="row p-0 m-0">
          <div class="col-12 m-0 p-1 bg-white" id="painting-div" style="min-height:500px; border-top: 1px solid #D3D7DA;"></div>
        </div>
      </div>
      <div class="tab-pane" id="tab-wiring">
        <div class="tab-heading tab-heading--green p-1">
          <div class="row p-0 m-0">
            <div class="col-6 p-0">
              <input class="d-none" type="text" value="All" id="wiring-current-status">
              <div class="row m-0 p-1">
                @foreach ($status_arr as $status)
                <label class="PillList-item p-0 mb-0 mr-2 ml-0">
                  <input type="checkbox" class="wiring-checkbox m-0" value="{{ $status }}">
                  <span class="PillList-label">{{ $status }}</span>
                </label>
                @endforeach
              </div>
            </div>
            <div class="col-6 p-0 text-right">
              <div class="form-group d-inline-block m-1">
                <input type="text" data-div="wiring" data-op="3" class="form-control bg-white wiring-search-filter maintenance-search rounded" placeholder="Search">
              </div>
            </div>
          </div>
        </div>
        <div class="row p-0 m-0">
          <div class="col-12 m-0 p-1 bg-white" id="wiring-div" style="min-height:500px; border-top: 1px solid #D3D7DA;"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-3 pl-2 pr-2">
    <div class="text-right">
      <button class="btn btn-secondary mr-0 ml-0 mb-1" data-toggle="modal" data-target="#importModal">Import</button>
      <button class="btn btn-primary mr-0 ml-0 mb-1" data-toggle="modal" data-target="#add-mr-modal">Add Maintenance Request</button>

      <!-- Modal -->
      <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Import a File</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form action="/machine_breakdown/import" method="post" enctype="multipart/form-data">
              @csrf
              <div class="modal-body">
                <div class="custom-file p-1 text-left">
                  <input type="file" class="custom-file-input" id="customFile" name="file" required>
                  <label class="custom-file-label" for="customFile">Choose File</label>
                </div>
                <div class="container-fluid text-left mt-2">
                  <span>Download Template: <a href="{{ asset('/storage/files/Machine-Breakdown-Import-Template.xlsx') }}">Machine-Breakdown-Import-Template.xlsx</a></span>
                  <br><br>
                  <span class="font-italic">* Red columns are required fields</span>
                </div>
              </div>
              <div class="modal-footer p-1">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Import</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div style="background-color: #F7F7F9; margin-top: 2px;">
      <div class="container p-2">
        <h5 class="text-center font-weight-bold" style="font-size: 13pt;">Machines for Maintenance</h5>
      </div>
      <div class="container" style="height: 750px; overflow-y: scroll; overflow-x: hidden">
        @foreach ($machine_arr as $machine)
          <div class="card m-1">
            <div class="card-body row">
              <div class="col-2 p-1">
                <center>
                  <img src="{{ asset($machine['image']) }}" alt="" class="w-100">
                </center>
              </div>
              <div class="col-10">
                <span class="card-title" style="font-weight: bold">
                  {{ $machine['machine_id'] }} <span class="badge badge-danger">{{ $machine['pending_breakdowns'] }}</span><br/>
                  {{ $machine['machine_name'] }}
                </span>
                <p class="card-text">{{ $machine['total_breakdowns'] }} total breakdowns</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add-mr-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <form action="/save_maintenance_request" method="POST" id="save-maintenance-request-form">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title">Maintenance Request</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" style="color: #fff;">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="d-flex flex-row">
            <div class="col-8">
              <div class="row">
                <div class="col-6 mb-2">
                  <small class="m-1">Machine Code</small>
                  <select class="form-control rounded" name="machine_id" id="sel-machine-id" required>
                    <option value="">Select Machine</option>
                    @foreach ($machines as $r)
                    <option value="{{ $r->machine_code }}" data-name="{{ $r->machine_name }}">{{ $r->machine_code . ' - ' . $r->machine_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6 mb-2">
                  <small class="m-1">Machine Name</small>
                  <input type="text" class="form-control rounded" id="sel-machine-name" readonly>
                </div>
                <div class="col-6 mb-2">
                  <small class="m-1">Building/Equipment</small>
                  <input type="text" class="form-control rounded" name="building" required>
                </div>
                <div class="col-6 mb-2">
                  @php
                      $selection_arr = ['Planned', 'Emergency', 'New Installation', 'Transfer of Facilities', 'Outside Repair'];
                  @endphp
                  <small class="m-1">Type</small>
                  <select class="form-control rounded" name="type" required>
                    @foreach ($selection_arr as $item)
                      <option value="{{ $item }}">{{ $item }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6 mb-2">
                  <small class="m-1">Breakdown Type</small>
                  <select class="form-control rounded" name="category" id="sel-category" required>
                    <option value="">Select Category</option>
                    <option value="Breakdown">Breakdown</option>
                    <option value="Corrective">Corrective</option>
                  </select>
                </div>
                <div class="col-6 mb-2 d-none">
                  <small class="m-1">Breakdown Reason</small>
                  <select class="form-control rounded" name="breakdown_reason" id="sel-breakdown-reason">
                    <option value="">Select Breakdown Reason</option>
                    <option value="Malfunction">Malfunction</option>
                  </select>
                </div>
                <div class="col-6 mb-2 d-none">
                  <small class="m-1">Corrective Reason</small>
                  <select class="form-control rounded" name="corrective_reason" id="sel-corrective-reason">
                    <option value="">Select Corrective Reason</option>
                    <option value="Mechanical Issue">Mechanical Issue</option>
                    <option value="Electrical Issue">Electrical Issue</option>
                  </select>
                </div>
                <div class="col-12 mb-2">
                  <small class="m-1">Complaints/Problems</small>
                  <textarea class="form-control rounded border" name="complaints"></textarea>
                </div>
                <div class="col-12 mb-2">
                  <small class="m-1">Findings</small>
                  <textarea class="form-control rounded border" name="findings" id="sel-findings"></textarea>
                </div>
                <div class="col-12 mb-2 d-none">
                  <small class="m-1">Work Done</small>
                  <textarea class="form-control rounded border" name="work_done" id="sel-work-done"></textarea>
                </div>
                <div class="col-12 mb-2 d-none">
                  <small class="m-1">Hold Reason</small>
                  <textarea class="form-control rounded border" name="hold_reason" id="sel-hold-reason"></textarea>
                </div>
                <div class="col-12 mb-2">
                  <small class="m-1">Remarks</small>
                  <textarea class="form-control rounded border" name="remarks" id="sel-remarks"></textarea>
                </div>
              </div>
            </div>
            <div class="col-4">
              <div class="row">
                <div class="col-12 mb-2">
                  <small class="m-1">Status</small>
                  <select class="form-control rounded" name="status" id="sel-status" required>
                    <option value="Pending">Pending</option>
                    <option value="On Hold">On Hold</option>
                    <option value="In Process">In Process</option>
                    <option value="Done">Done</option>
                  </select>
                </div>
                <div class="col-12 mb-2">
                  <small class="m-1">Date Reported</small>
                  <input type="date" class="form-control rounded" name="date_reported" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" id="sel-date-reported" required>
                </div>
                <div class="col-12 mb-2">
                  <small class="m-1">Reported By</small>
                  <select class="form-control rounded" name="reported_by" id="sel-reported-by" required>
                    <option value="">Select Reported By</option>
                    @foreach ($operators as $s)
                    <option value="{{ $s->employee_name }}">{{ $s->employee_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12 mb-2 d-none">
                  <small class="m-1">Date Resolved</small>
                  <input type="date" class="form-control rounded" name="date_resolved" id="sel-date-resolved">
                </div>
                <div class="col-12 mb-2">
                  @php
                      $maintenance_staffs = collect($operators)->where('department', 'Plant Services');
                  @endphp
                  <div class="row">
                    <select class="d-none form-control rounded" id="sel-assigned-maintenance-staff">
                      <option value="">Select Maintenance Staff</option>
                      @foreach ($maintenance_staffs as $e)
                      <option value="{{ $e->operator_id }}">{{ $e->employee_name }}</option>
                      @endforeach
                    </select>
                    <div class="container-fluid mx-auto">
                      <br/>
                      <table class="table table-bordered" id="maintenance-table" style="font-size: 9pt;">
                        <thead>
                            <tr>
                                <td scope="col" class="text-center p-2">Maintenenance Staff</td>
                                <td class="text-center p-2" style="width: 10%;">
                                    <button type="button" class="btn btn-outline-primary btn-sm add-row-btn" id="add-staff-btn" data-table="#maintenance-table" data-select="#sel-assigned-maintenance-staff">Add</button>
                                </td>
                            </tr>
                        </thead>
                        <tbody></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 text-center">
            <button class="btn btn-primary btn-lg" type="submit" id="save-maintenance-request-btn">Submit</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
@section('style')
<style>
    textarea:focus, input:focus{
    outline: none;
  }
  .ui-autocomplete {
    position: absolute;
    z-index: 2150000000 !important;
    cursor: default;
    border: 2px solid #ccc;
    padding: 5px 0;
    border-radius: 2px;
  }
  .custom-nav-link{
    padding: 5px;
  }
  .custom-nav-link a{
    text-decoration: none;
  }
  .ui-tab-container.ui-tab-default .nav-tabs {
    border: 0;
  }
  .tab-heading {
    width: 100%;
    padding: 1em .5em;
  }
  .tab-heading h4 {
    margin: 0;
    padding: 0;
  }
  .tab-heading--blue {
    background-color: #2196E3;
    color: #FFF;
  }
  .tab-heading--orange {
    background-color: #EA9034;
    color: #FFF;
  }
  .tab-heading--reddish {
    background-color: #E86B46;
    color: #FFF;
  }
  .tab-heading--teal {
    background-color: #22D3CC;
    color: #FFF;
  }
  .tab-heading--green {
    background-color: #8BC753;
    color: #FFF;
  }
  .tab-heading--gray {
    background-color: #808495;
    color: #FFF;
  }
  .tab-heading--ltgray {
    background-color: #F3F3F3;
    color: #242424;
  }
  .ui-tab-container .nav-tabs > li.active > a,
  .ui-tab-container .nav-tabs > li > a {
    background: transparent;
    border: 0;
    border-width: 0;
    outline: 0;
  }
  .ui-tab-container .nav-tabs > li.active > a:hover, .ui-tab-container .nav-tabs > li.active > a:focus,
  .ui-tab-container .nav-tabs > li > a:hover,
  .ui-tab-container .nav-tabs > li > a:focus {
    background-color: transparent;
    border: 0;
    border-width: 0;
    outline: 0;
  }
  li.tab .tab-number {
    color: #FFF;
    font-weight: 800;
    font-size: 1.2em;
    display: block;
    text-align: center;
    margin-bottom: .25em;
  }
  li.tab .tab-title {
    color: #FFF;
    font-size: .8em;
    display: block;
    text-align: center;
    text-transform: uppercase;
  }
  li.tab.in-progress-tab {
    background-color: #EA9034;
  }
  li.tab.task-queue-tab {
    background-color: #2196E3;
  }
  li.tab.bug-queue-tab {
    background-color: #E86B46;
    color: #FFF;
  }
  li.tab.awaiting-feedback-tab {
    background-color: #22D3CC;
    color: #FFF;
  }
  li.tab.completed-tab {
    background-color: #8BC753;
    color: #FFF;
  }
  li.tab.next-deploy-tab {
    background-color: #808495;
    color: #FFF;
  }
  li.tab.search-tab {
    background-color: #F3F3F3;
  }
  li.tab.search-tab .tab-number {
    color: #242424;
  }
  li.tab.search-tab .tab-title {
    color: #242424;
  }
  .ticket-status-widget .tab-content {
    background: #FFF;
    padding: 0;
  }
  .PillList-item {
    cursor: pointer;
    display: inline-block;
    float: left;
    font-size: 14px;
    font-weight: normal;
    line-height: 20px;
    margin: 0 12px 12px 0;
    text-transform: capitalize;
  }
  .PillList-item input[type="checkbox"] {
    display: none;
  }
  .PillList-item input[type="checkbox"]:checked + .PillList-label {
    background-color: #F96332;
    border: 1px solid #F96332;
    color: #fff;
    padding-right: 16px;
    padding-left: 16px;
  }
  .PillList-label {
    border: 1px solid #FFF;
    border-radius: 20px;
    color: #FFF;
    display: block;
    padding: 7px 28px;
    text-decoration: none;
  }
  .PillList-item
    input[type="checkbox"]:checked
    + .PillList-label
    .Icon--checkLight {
    display: none;
  }
  .PillList-item input[type="checkbox"]:checked + .PillList-label .Icon--addLight,
  .PillList-label .Icon--checkLight,
  .PillList-children {
    display: none;
  }
  .PillList-label .Icon {
    width: 12px;
    height: 12px;
    margin: 0 0 0 12px;
  }
  .Icon--smallest {
    font-size: 12px;
    line-height: 12px;
  }
  .Icon {
    background: transparent;
    display: inline-block;
    font-style: normal;
    vertical-align: baseline;
    position: relative;
  }
</style>
@endsection
@section('script')
<script type="text/javascript" src="{{  asset('js/printThis.js') }}"></script>
<script>
  @if(session()->has('error'))
      showNotification("danger", "{{ session()->get('error') }}", "now-ui-icons ui-1_check");
  @endif

  function clone_table(table, select){
    var clone_select = $(select).html();
    var row = '<tr class="staff-row">' +
      '<td class="p-2">' +
        '<select name="maintenance_staff[]" class="form-control w-100" style="width: 100%;" required>' + clone_select + '</select>' +
      '</td>' +
      '<td class="text-center d-print-none">' +
        '<button type="button" class="btn btn-outline-danger btn-sm remove-td-row"><i class="now-ui-icons ui-1_simple-remove font-weight-bold" style="cursor: pointer; font-size: 9pt;"></i></button>' +
      '</td>' +
    '</tr>';

    $(table).append(row);
  }

  $(document).on('click', '.add-row-btn', function (e){
    e.preventDefault();
    var table = $(this).data('table') + ' tbody';
    var select = $(this).data('select');
    clone_table(table, select);
  });

  $(document).on('click', '.remove-td-row', function(e){
    e.preventDefault();
    $(this).closest("tr").remove();
  });

  // Add the following code if you want the name of the file appear on select
  $(".custom-file-input").change(function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
  });

  $('#sel-machine-id').change(function(e) {
    e.preventDefault();
    $('#sel-machine-name').val($(this).find(':selected').data('name'));
  });

  $('#sel-category').change(function(e) {
    e.preventDefault();
    if ($(this).val() == 'Breakdown') {
      $('#sel-breakdown-reason').attr('required', true);
      $('#sel-breakdown-reason').closest('div').removeClass('d-none');
      $('#sel-corrective-reason').removeAttr('required');
      $('#sel-corrective-reason').closest('div').addClass('d-none');
    } else {
      $('#sel-corrective-reason').attr('required', true);
      $('#sel-corrective-reason').closest('div').removeClass('d-none');
      $('#sel-breakdown-reason').removeAttr('required');
      $('#sel-breakdown-reason').closest('div').addClass('d-none');
    }
  });

  $('#sel-status').change(function(e) {
    e.preventDefault();

    $('#sel-work-done').removeAttr('required');
    $('#sel-work-done').closest('div').addClass('d-none');
    $('#sel-hold-reason').removeAttr('required');
    $('#sel-hold-reason').closest('div').addClass('d-none');
    $('#sel-date-resolved').removeAttr('required');
    $('#sel-date-resolved').closest('div').addClass('d-none');
     
    var s = $(this).val();
    if (s == 'On Hold') {
      $('#sel-findings').attr('required', true);
      $('#sel-findings').closest('div').removeClass('d-none');
      $('#sel-hold-reason').attr('required', true);
      $('#sel-hold-reason').closest('div').removeClass('d-none');
    }

    if (s == 'Done') {
      $('#sel-work-done').attr('required', true);
      $('#sel-work-done').closest('div').removeClass('d-none');
      $('#sel-date-resolved').attr('required', true);
      $('#sel-date-resolved').closest('div').removeClass('d-none');
      $('#sel-assigned-maintenance-staff').attr('required', true);
    }
  });

  $('#save-maintenance-request-form').submit(function(e) {
    e.preventDefault();
    $('#save-maintenance-request-btn').attr('disabled', true);
    $.ajax({
      url: $(this).attr('action'),
      type:"POST",
      data: $(this).serialize(),
      success:function(data){
        if (data.success) {
          showNotification("success", data.message, "now-ui-icons ui-1_check");
          $('#add-mr-modal').modal('hide');
          var status = $('#fabrication-current-status').val() ? $('#fabrication-current-status').val() : 'All';
          get_maintenance_request_list(1, status, $('.fabrication-search-filter').val(),'#fabrication-div');
          var status = $('#painting-current-status').val() ? $('#painting-current-status').val() : 'All';
          get_maintenance_request_list(1, status, $('.painting-search-filter').val(),'#painting-div');
          var status = $('#wiring-current-status').val() ? $('#wiring-current-status').val() : 'All';
          get_maintenance_request_list(1, status, $('.wiring-search-filter').val(),'#wiring-div');

          $('.staff-row').remove();
          $("#save-maintenance-request-form").trigger("reset");
        } else {
          showNotification("danger", data.message, "now-ui-icons ui-1_check");
          $('#save-maintenance-request-btn').removeAttr('disabled');
        }
      }
    });
  });

  $('#add-mr-modal').on('hidden.bs.modal', function () {
    $("#save-maintenance-request-form").trigger("reset");
    $('#save-maintenance-request-btn').removeAttr('disabled');
  });

  $('#add-mr-modal').on('show.bs.modal', function() {
    $('#sel-breakdown-reason').removeAttr('required');
    $('#sel-breakdown-reason').closest('div').addClass('d-none');
    $('#sel-corrective-reason').removeAttr('required');
    $('#sel-corrective-reason').closest('div').addClass('d-none');
  });

  $(document).on('click', '.machine-details', function(e){
    status_check($(this).data('breakdown'));
    enable_submit($(this).data('breakdown'));
  });

  // tabs
  function changeTab(operation, op){
    get_maintenance_request_list(op, $('#'+operation+'-current-status').val(), $('.'+operation+'-search-filter').val(), '#'+operation+'-div', 1);
  }

  function status_check(machine_breakdown_id){
    if($('#'+machine_breakdown_id+'-status').val() == 'On Hold'){
      $('#'+machine_breakdown_id+'-findings-container').slideDown();
      $('#'+machine_breakdown_id+'-hold-container').slideDown();
      $('#'+machine_breakdown_id+'-work-done-container').slideUp();
      $('#'+machine_breakdown_id+'-hold-reason').prop('required', true);
      $('#'+machine_breakdown_id+'-work-done').prop('required', false);
      $('#'+machine_breakdown_id+'-findings').prop('required', false);
      $('#'+machine_breakdown_id+'-maintenance-staff').prop('required', false);
    }else if($('#'+machine_breakdown_id+'-status').val() == 'In Process'){
      $('#'+machine_breakdown_id+'-findings-container').slideDown();
      $('#'+machine_breakdown_id+'-date-started').slideDown();
      $('#'+machine_breakdown_id+'-date-resolved').slideUp();
      $('#'+machine_breakdown_id+'-work-done-container').slideUp();
      $('#'+machine_breakdown_id+'-hold-container').slideUp();
      $('#'+machine_breakdown_id+'-findings').prop('required', true);
      $('#'+machine_breakdown_id+'-hold-reason').prop('required', false);
      $('#'+machine_breakdown_id+'-work-done').prop('required', false);
      $('#'+machine_breakdown_id+'-maintenance-staff').prop('required', true);
    }else if($('#'+machine_breakdown_id+'-status').val() == 'Done'){
      $('#'+machine_breakdown_id+'-work-done-container').slideDown();
      $('#'+machine_breakdown_id+'-findings-container').slideDown();
      $('#'+machine_breakdown_id+'-date-started').slideDown();
      $('#'+machine_breakdown_id+'-date-resolved').slideDown();
      $('#'+machine_breakdown_id+'-hold-container').slideUp();
      $('#'+machine_breakdown_id+'-hold-reason').prop('required', false);
      $('#'+machine_breakdown_id+'-work-done').prop('required', true);
      $('#'+machine_breakdown_id+'-findings').prop('required', true);
      $('#'+machine_breakdown_id+'-maintenance-staff').prop('required', true);
    }else{
      $('#'+machine_breakdown_id+'-date-started').slideUp();
      $('#'+machine_breakdown_id+'-date-resolved').slideUp();
      $('#'+machine_breakdown_id+'-work-done-container').slideUp();
      $('#'+machine_breakdown_id+'-hold-container').slideUp();
      $('#'+machine_breakdown_id+'-findings-container').slideUp();
      $('#'+machine_breakdown_id+'-hold-reason').prop('required', false);
      $('#'+machine_breakdown_id+'-work-done').prop('required', false);
      $('#'+machine_breakdown_id+'-findings').prop('required', false);
      $('#'+machine_breakdown_id+'-maintenance-staff').prop('required', false);
    }
  }

  function enable_submit(machine_breakdown_id){
    var findings = parseInt($('#'+machine_breakdown_id+'-findings').val().length);
    var work_done = parseInt($('#'+machine_breakdown_id+'-work-done').val().length);
    var hold_reason = parseInt($('#'+machine_breakdown_id+'-hold-reason').val().length);
    if(findings > 255 || work_done > 255 || hold_reason > 255){
      $('#'+machine_breakdown_id+'-submit-btn').prop('disabled', true);
      $('#'+machine_breakdown_id+'-info').removeClass('d-none');
    }else{
      $('#'+machine_breakdown_id+'-submit-btn').prop('disabled', false);
      $('#'+machine_breakdown_id+'-info').addClass('d-none');
    }
  }

  // search
  $(".maintenance-search").keyup(function(){
    var operation = $(this).data('div');
    var op = parseInt($(this).data('op'));
    var status = $('#'+operation+'-current-status').val();
    var query = $('.'+operation+'-search-filter').val();
    var div = '#'+operation+'-div';
    get_maintenance_request_list(op, status, query, div, 1);
  });

  // pill tabs
  $(".fabrication-checkbox").click(function(){
    if($(this).prop('checked') == true){
      status += $(this).val() + ',';
    }else if($(this).prop('checked') == false){
      status = status.replace($(this).val() + ',', '');
    }

    if(status == ''){
      $('#fabrication-current-status').val('All');
    }else{
      $('#fabrication-current-status').val(status);
    }

    query = $('.fabrication-search-filter').val();
    get_maintenance_request_list(1, $('#fabrication-current-status').val(), query, '#fabrication-div', 1);
  });

  $(".painting-checkbox").click(function(){
    if($(this).prop('checked') == true){
      status += $(this).val() + ',';
    }else if($(this).prop('checked') == false){
      status = status.replace($(this).val() + ',', '');
    }

    if(status == ''){
      $('#painting-current-status').val('All');
    }else{
      $('#painting-current-status').val(status);
    }

    query = $('.painting-search-filter').val();
    get_maintenance_request_list(2, $('#painting-current-status').val(), query, '#painting-div', 1);
  });

  $(".wiring-checkbox").click(function(){
    if($(this).prop('checked') == true){
      status += $(this).val() + ',';
    }else if($(this).prop('checked') == false){
      status = status.replace($(this).val() + ',', '');
    }

    if(status == ''){
      $('#wiring-current-status').val('All');
    }else{
      $('#wiring-current-status').val(status);
    }

    query = $('.wiring-search-filter').val();
    get_maintenance_request_list(3, $('#wiring-current-status').val(), query,'#wiring-div', 1);
  });

  // pagination links
  $(document).on('click', '.custom-fabrication-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var query = $('.fabrication-search-filter').val();
    var status = $('#fabrication-current-status').val() ? $('#fabrication-current-status').val() : 'All';
    get_maintenance_request_list(1, status, query,'#fabrication-div', page);
  });

  $(document).on('click', '.custom-painting-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var query = $('.painting-search-filter').val();
    var status = $('#painting-current-status').val() ? $('#painting-current-status').val() : 'All';
    get_maintenance_request_list(1, status, query,'#painting-div', page);
  });

  $(document).on('click', '.custom-wiring-pagination a', function(event){
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    var query = $('.wiring-search-filter').val();
    var status = $('#wiring-current-status').val() ? $('#wiring-current-status').val() : 'All';
    get_maintenance_request_list(1, status, query,'#wiring-div', page);
  });

  // main function
  function get_maintenance_request_list(operation, status, query, div, page){
    if(parseInt(operation) == 1){
      var op = '#fabrication';
    }else if(parseInt(operation) == 2){
      var op = '#painting';
    }else if(parseInt(operation) == 3){
      var op = '#wiring';
    }
    $.ajax({
      url: "/maintenance_request_list/?page="+page,
      type:"GET",
      data: {
        search_string: query,
        operation: operation,
        status: status
      },
      success:function(data){
        $(div).html(data);
        $(op+'-count').text($(op+'-total').val());
      }
    });
  }

  $(document).ready(function(){
    get_maintenance_request_list(1, 'All', $('.fabrication-search-filter').val(), '#fabrication-div', 1);

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

  function showNotification(color, message, icon){
    $.notify({
      icon: icon,
      message: message
    },{
      type: color,
      timer: 5000,
      placement: {
        from: 'top',
        align: 'center'
      }
    });
  }
</script>
@if (Session::has('success'))
<script>
  $(document).ready(function(){
    var message = "{{ Session::get('success') }}";
    showNotification("success", message, "now-ui-icons ui-1_check");
  });
</script>
@endif
@endsection