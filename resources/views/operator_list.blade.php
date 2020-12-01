@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'operators',
])

@section('content')
<div class="panel-header">
  <div class="header text-center">
    <div class="row">
      <div class="col-md-12">
        <table style="text-align: center; width: 100%;">
          <tr>
            <td style="width: 43%; border-right: 5px solid white;">
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
            <td style="width: 43%">
              <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Operator List</h2>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="content" style="margin-top: -110px;">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-body">
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="fabrication-tab" data-toggle="tab" href="#fabrication" role="tab" aria-controls="fabrication" aria-selected="true">Fabrication</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="painting-tab" data-toggle="tab" href="#painting" role="tab" aria-controls="painting" aria-selected="false">Painting</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="assembly-tab" data-toggle="tab" href="#assembly" role="tab" aria-controls="assembly" aria-selected="false">Assembly</a>
            </li>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content" style="min-height: 620px;">
            <div class="tab-pane active" id="fabrication" role="tabpanel" aria-labelledby="fabrication-tab">
              <div class="row" style="margin-top: 12px;">
                <div class="col-md-12">
                  <h5 class="title text-center">Fabrication Section</h5>
                </div>
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead class="text-primary">
                        <th class="text-center"><b>Employee ID</b></th>
                        <th class="text-center"><b>Employee Name</b></th>
                        <th class="text-center"><b>Designation</b></th>
                        <th class="text-center"><b>Employment Status</b></th>
                      </thead>
                      <tbody>
                        @php 
                        $fabrication = collect($operators)->where('designation_id', 47);
                        @endphp
                        @forelse($fabrication as $r)
                        <tr>
                          <td class="text-center"><a href="#!" data-id="{{ $r->user_id }}" class="view-operator-prof-btn">{{ $r->employee_id }}</a></td>
                          <td class="text-left">{{ $r->employee_name }}</td>
                          <td class="text-center">{{ $r->designation_name }}</td>
                          <td class="text-center">{{ $r->employment_status }}</td>
                        </tr>
                        @empty
                        <tr>
                          <td class="text-center" colspan="2">No Production Order(s) found.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="painting" role="tabpanel" aria-labelledby="painting-tab">
              <div class="row" style="margin-top: 12px;">
                <div class="col-md-12">
                  <h5 class="title text-center">Painting Section</h5>
                </div>
                <div class="col-md-12">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead class="text-primary">
                            <th class="text-center"><b>Employee ID</b></th>
                            <th class="text-center"><b>Employee Name</b></th>
                            <th class="text-center"><b>Designation</b></th>
                            <th class="text-center"><b>Employment Status</b></th>
                          </thead>
                          <tbody>
                            @php 
                            $painting = collect($operators)->where('designation_id', 53);
                            @endphp
                            @forelse($painting as $r)
                            <tr>
                              <td class="text-center"><a href="#!" data-id="{{ $r->user_id }}" class="view-operator-prof-btn">{{ $r->employee_id }}</a></td>
                              <td class="text-left">{{ $r->employee_name }}</td>
                              <td class="text-center">{{ $r->designation_name }}</td>
                              <td class="text-center">{{ $r->employment_status }}</td>
                            </tr>
                            @empty
                            <tr>
                              <td class="text-center" colspan="2">No Production Order(s) found.</td>
                            </tr>
                            @endforelse
                          </tbody>
                        </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="assembly" role="tabpanel" aria-labelledby="assembly-tab">
              <div class="row" style="margin-top: 12px;">
                <div class="col-md-12">
                  <h5 class="title text-center">Assembly Section</h5>
                </div>
                <div class="col-md-12">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead class="text-primary">
                            <th class="text-center"><b>Employee ID</b></th>
                            <th class="text-center"><b>Employee Name</b></th>
                            <th class="text-center"><b>Designation</b></th>
                            <th class="text-center"><b>Employment Status</b></th>
                          </thead>
                          <tbody>
                            @php 
                            $assembly = collect($operators)->where('designation_id', 46);
                            @endphp
                            @forelse($assembly as $r)
                            <tr>
                              <td class="text-center"><a href="#!" data-id="{{ $r->user_id }}" class="view-operator-prof-btn">{{ $r->employee_id }}</a></td>
                              <td class="text-left">{{ $r->employee_name }}</td>
                              <td class="text-center">{{ $r->designation_name }}</td>
                              <td class="text-center">{{ $r->employment_status }}</td>
                            </tr>
                            @empty
                            <tr>
                              <td class="text-center" colspan="2">No Production Order(s) found.</td>
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
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">
          <h6 class="card-title text-center">On Leave / Absent Today</h6>
        </div>
        <div class="card-body">
          <table class="table">
            <tbody class="table-body">
              @forelse($out_today as $out_of_office)
              @php 
                $img = ($out_of_office->image) ? 'https://10.0.0.5/' . $out_of_office->image : asset('img/user.png');
              @endphp
              <tr>
                <td style="width: 60%;">
                  <img src="{{ $img }}" width="65" height="45" style="float: left; padding-right: 10px;">
                  <span class="approver-name">{{ $out_of_office->employee_name }}</span><br>
                  <cite>{{ $out_of_office->designation }} - {{ $out_of_office->department }}</cite>
                </td>
                <td class="text-center" style="width: 40%;">{{ $out_of_office->leave_type }}<br>({{ $out_of_office->time_from }} - {{ $out_of_office->time_to }})</td>
              </tr>
              @empty
              <tr>
                <td class="text-center" colspan="2">No Employee(s) Found.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="operator-profile-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 68%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal Title</h5>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
            <div style="margin: auto; width: 50%;">
              <img src="#" id="emp-image">
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <dl class="row">
                  <dt class="col-md-6 text-right">Employee ID</dt>
                  <dd class="col-md-6 text-left"><span class="emp-id"></span></dd>
                  <dt class="col-md-6 text-right">Employee Name</dt>
                  <dd class="col-md-6 text-left"><span class="emp-name"></span></dd>
                  <dt class="col-md-6 text-right">Designation</dt>
                  <dd class="col-md-6 text-left"><span class="designation"></span></dd>
                  <dt class="col-md-6 text-right">Employee Status</dt>
                  <dd class="col-md-6 text-left"><span class="emp-status"></span></dd>
                </dl>
              </div>
              <div class="col-md-12">
                <table style="width: 100%; margin-top: 15px;">
                  <col style="width: 33%;">
                  <col style="width: 34%;">
                  <col style="width: 33%;">
                  <thead style="font-size: 9pt;">
                    <th class="text-center"><b>Assigned Task(s)</b></th>
                    <th class="text-center"><b>Completed Task(s)</b></th>
                    <th class="text-center"><b>Output Qty</b></th>
                  </thead>
                  <tbody style="font-size: 16pt;">
                    <td class="text-center"><span class="total-assigned">0</span></td>
                    <td class="text-center"><span class="total-completed">0</span></td>
                    <td class="text-center"><span class="qty">0</span></td>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Task History</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Machine Assignment(s)</a>
          </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
          <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <div class="table-responsive">
              <table class="table scrolltbody" id="task-history-tbl">
                <thead class="text-primary" style="font-size: 7pt;">
                  <th class="text-center"><b>Operation</b></th>
                  <th class="text-center"><b>Item Code</b></th>
                  <th class="text-center"><b>Qty</b></th>
                  <th class="text-center"><b>Good</b></th>
                  <th class="text-center"><b>Reject</b></th>
                  <th class="text-center"><b>Rework</b></th>
                  <th class="text-center"><b>From</b></th>
                  <th class="text-center"><b>To</b></th>
                  <th class="text-center"><b>Duration</b></th>
                </thead>
                <tbody style="font-size: 9pt;"></tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="table-responsive">
              <table class="table scrolltbody" id="machine-assignment-tbl">
                <thead class="text-primary" style="font-size: 7pt;">
                  <th class="text-center" style="width: 40%;"><b>Machine</b></th>
                  <th class="text-center" style="width: 10%;"><b>Workstation</b></th>
                  <th class="text-center" style="width: 24%;"><b>Operator(s)</b></th>
                  <th class="text-center" style="width: 13%;"><b>From Time</b></th>
                  <th class="text-center" style="width: 13%;"><b>To Time</b></th>
                </thead>
                <tbody style="font-size: 9pt;"></tbody>
              </table>
            </div>
          </div>
        </div>
          </div>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<style type="text/css">

.scrolltbody tbody {
    display:block;
    height:400px;
    overflow:auto;
}
.scrolltbody thead, .scrolltbody tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;
}
.scrolltbody thead {
    width: calc( 100% - 1em )
}

</style>
@endsection

@section('script')
<script>
$(document).ready(function(){
  $('.view-operator-prof-btn').click(function(e){
    e.preventDefault();
    $('#operator-profile-modal #home-tab').tab('show');
    $('#emp-image').attr('src', '{{ asset('img/user.png') }}');
    $('#machine-assignment-tbl tbody').empty();
    $('#task-history-tbl tbody').empty();
    $.ajax({
      url:"/operator_profile/" + $(this).data('id'),
      type:"GET",
      success:function(data){
        $('#operator-profile-modal .modal-title').text(data.operator_details.department);
        $('#operator-profile-modal .emp-id').text(data.operator_details.employee_id);
        $('#operator-profile-modal .emp-name').text(data.operator_details.employee_name);
        $('#operator-profile-modal .designation').text(data.operator_details.designation_name);
        $('#operator-profile-modal .emp-status').text(data.operator_details.employment_status);

        $('#operator-profile-modal .total-assigned').text(data.totals.assigned);
        $('#operator-profile-modal .total-completed').text(data.totals.completed);
        $('#operator-profile-modal .qty').text(data.totals.qty);

        if (data.operator_details.image) {
          $('#emp-image').attr('src', 'https://10.0.0.5/' + data.operator_details.image);
        }

        var r = '';
        $.each(data.task_histories, function(i, d){
          r += '<tr>' +
            '<td class="text-center">' + d.workstation + '</td>' +
            '<td class="text-center">' + d.item_code + '</td>' +
            '<td class="text-center">' + Number(d.completed_qty) + '</td>' +
            '<td class="text-center">' + Number(d.good) + '</td>' +
            '<td class="text-center">' + Number(d.reject) + '</td>' +
            '<td class="text-center">' + Number(d.rework) + '</td>' +
            '<td class="text-center">' + d.from_time + '</td>' +
            '<td class="text-center">' + d.to_time + '</td>' +
            '<td class="text-center">' + d.duration + '</td>' +
          '</tr>';
        });

        var m = '';
        $.each(data.machine_assignment, function(i, d){
          m += '<tr>' +
            '<td class="text-left" style="width: 40%;"><b>[' + d.machine + ']</b> ' + d.machine_name + '</td>' +
            '<td class="text-center" style="width: 10%;">' + d.workstation + '</td>' +
            '<td class="text-center" style="width: 24%;">-</td>' +
            '<td class="text-center" style="width: 13%;">' + d.from_time + '</td>' +
            '<td class="text-center" style="width: 13%;">' + d.to_time + '</td>' +
          '</tr>';
        });

        $('#machine-assignment-tbl tbody').append(m);
        $('#task-history-tbl tbody').append(r);
      }
    });

    $('#operator-profile-modal').modal('show');
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