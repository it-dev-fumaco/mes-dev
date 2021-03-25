
        <table class="table table-striped text-center" style="margin-top:-30px;">
          <col style="width:11%">
          <col style="width:11%">
          <col style="width:11%">
          <col style="width:13%">
          <col style="width:13%">
          <col style="width:11%">
          <col style="width:12%">
          <col style="width:8%">
          <col style="width:10%">
              <thead class="text-primary" style="font-size: 8pt;">
                <th class="text-center"><b>ID</b></th>
                <th class="text-center"><b>Machine Code</b></th>
                <th class="text-center"><b>Breakdown Type</b></th>
                <th class="text-center"><b>Work done</b></th>
                <th class="text-center"><b>Finding/s</b></th>
                <th class="text-center"><b>Date Resolved</b></th>
                <th class="text-center"><b>Duration</b></th>
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
                  <td class="text-center">
                    <span class="font-weight-bold d-block" style="font-size:15px;">{{ $row['type'] }}</span>
                    <span class="font-italic d-block">{{ ($row['type'] == "Corrective")? $row['corrective_reason']: $row['breakdown_reason'] }}</span>
                  </td>
                  <td class="text-center">{{($row['work_done'] == null)? "-" :$row['work_done']  }}</td>
                  <td class="text-center">
                    {{($row['findings'] == null)? "-" :$row['findings']  }}
                  </td>
                  <td class="text-center">
                    <span class="font-weight-bold d-block" style="font-size:12px;">{{ \Carbon\Carbon::parse($row['date_resolved'])->format('M d, Y h:i a') }}</span>
                    {{-- <span class="font-italic d-block">({{ $row['assigned_maintenance_staff }})</span> --}}
                  </td>
                  <td class="text-center">
                    <span class="font-italic d-block">{{$row['duration']}}</span>
                  </td>
                  <td >
                    <span class="badge badge-success" style="font-size: 12pt;">{{ $row['status'] }}</span>
                  </td>
                  <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Action
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item complete-task-view" data-id="{{ $row['machine_breakdown_id'] }}"  data- href="#">Maintenance Request Details</a>
                          <a class="dropdown-item printbtnprint" data-id="{{ $row['machine_breakdown_id'] }}"  data- href="#">Print</a>

                        </div>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="9" class="text-center">No completed request found</td>
                </tr>
                @endforelse
              </tbody>
        </table>
        <center>
            <div id="paginate-maintenance-request-done" class="col-md-12 text-center" style="text-align: center;">
             {{ $maintenance->links() }}
            </div>
          </center>

<script type="text/javascript">
</script>