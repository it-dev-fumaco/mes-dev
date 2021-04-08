
        <table class="table table-striped text-center" style="margin-top:-30px;">
              <col style="width:11%">
              <col style="width:11%">
              <col style="width:11%">
              <col style="width:11%">
              <col style="width:11%">
              <col style="width:11%">
              <col style="width:12%">
              <col style="width:11%">
              <col style="width:11%">

              <thead class="text-primary" style="font-size: 8pt;">
                <th class="text-center"><b>ID</b></th>
                <th class="text-center"><b>Machine Code</b></th>
                <th class="text-center"><b>Breakdown Type</b></th>
                <th class="text-center"><b>Reason</b></th>
                <th class="text-center"><b>Date Reported</b></th>
                <th class="text-center"><b>Reported By</b></th>
                <th class="text-center"><b>Assigned Main. Staff</b></th>
                <th class="text-center"><b>Status</b></th>
                <th class="text-center"><b>Action/s</b></th>
              </thead>
              <tbody style="font-size: 10pt;">
                @forelse($maintenance as $row)
                <tr>
                  <td class="text-center" style="font-size:18px;">
                    <span class="font-weight-bold d-block">{{ $row['machine_breakdown_id'] }}</span>
                  </td>
                  <td class="text-center">
                    <span class="font-weight-bold d-block" style="font-size:15px;">{{ $row['machine_id'] }}</span>
                    <span class="font-italic d-block">({{ $row['machine_name'] }})</span>
                  </td>
                  <td class="text-center">{{ $row['type'] }}</td>
                  <td class="text-center">{{ ($row['type'] == "Corrective")? $row['corrective_reason']: $row['breakdown_reason'] }}</td>
                  <td class="text-center">{{ \Carbon\Carbon::parse($row['date_reported'])->format('M d, Y h:i a') }}</td>
                  <td class="text-center">{{ $row['reported_by'] }}</td>
                  <td class="text-center">
                    @if(count($row['main_staff'] ) > 0)
                      @forelse($row['main_staff'] as $r)
                      <span class=" d-block" style="font-size:12px;">{{ $r->employee_name}}</span>
                      @empty
                      <span class=" d-block" style="font-size:12px;">-</span>
                      @endforelse
                    @else
                    <span class=" d-block" style="font-size:12px;">-</span>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="badge badge-secondary" style="font-size: 12pt;">{{ $row['status'] }}</span>
                  </td>
                  <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Action
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item complete-task" data-id="{{ $row['machine_breakdown_id'] }}"  data- href="#">Maintenance Request</a>
                          <a class="dropdown-item assign-main-staff" data-id="{{ $row['machine_breakdown_id'] }}" href="#">Assigned Maintenance Staff</a>
                          <a class="dropdown-item printbtnprintpm" data-id="{{ $row['machine_breakdown_id'] }}" data-dtype="pm"  data- href="#">Print</a>
                        </div>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="9" class="text-center">No pending request found</td>
                </tr>
                @endforelse
              </tbody>
        </table>
        <center>
            <div id="paginate-maintenance-request-pending" class="col-md-12 text-center" style="text-align: center;">
             {{ $maintenance->links() }}
            </div>
          </center>


<script type="text/javascript">
      $(document).on('click', '.complete-task-view', function(event){
        event.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            url:"/get_maintenance_request_details",
            type:"GET",
            data: {id: id},
            success:function(data){
                $('#tbl_maintenance_dtls').html(data);
            }
        });
        $("#maintenance_status" ).attr("disabled", true);
        $("#date_resolve_picker" ).attr("disabled", true);
        $("#maintennace_type" ).attr("disabled", true);
        $("#findings" ).attr("disabled", true);
        $("#work_done" ).attr("disabled", true);
        $("#t_duration" ).attr("disabled", true);
        $("#submit_id_form" ).hide();

        
        $('#confirm-task-for-breakdown-modal').modal('show');
        // $('#set_assign_maintenance_staff').val(staff);

        
        // var page = $(this).attr('href').split('page=')[1];
        
    });
</script>