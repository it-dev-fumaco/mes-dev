
        <table class="table table-striped text-center" style="margin-top:-30px;">
            <col style="width:10%">
            <col style="width:11.43%">
            <col style="width:11.43%">
            <col style="width:11.43%">
            <col style="width:11.43%">
            <col style="width:11.41%">
            <col style="width:10%">

            <thead class="text-primary" style="font-size: 8pt;">
              <th class="text-center"><b>ID</b></th>
              <th class="text-center"><b>Machine Code</b></th>
              <th class="text-center"><b>Task</b></th>
              <th class="text-center"><b>Maintenance Schedule Type</b></th>
              <th class="text-center"><b>Assigned Main. Staff</b></th>
              <th class="text-center"><b>Operation</b></th>
              <th class="text-center"><b>Action/s</b></th>
            </thead>
            <tbody style="font-size: 10pt;">
              @forelse($maintenance as $row)
              <tr>
                <td class="text-center" style="font-size:18px;">
                  <span class="font-weight-bold d-block">{{ $row['preventive_maintenance_id'] }}</span>
                </td>
                <td class="text-center">
                  <span class="font-weight-bold d-block" style="font-size:15px;">{{ $row['machine_code'] }}</span>
                  <span class="font-italic d-block">({{ $row['machine_name'] }})</span>
                </td>
                <td class="text-left">
                    <ol>
                       
                        @foreach($row['task'] as $r)
                        <li>{{$r->preventive_maintenance_task}}</li>
                        @endforeach
                      </ol>  
                    
                </td>
                
                <td class="text-center">{{ $row['maintenance_schedule_type']}}</td>
                <td class="text-center">
                    @if(count($row['staff'] ) > 0)
                      @forelse($row['staff'] as $r)
                      <span class=" d-block" style="font-size:12px;">{{ $r->employee_name}}</span>
                      @empty
                      <span class=" d-block" style="font-size:12px;">-</span>
                      @endforelse
                    @else
                    <span class=" d-block" style="font-size:12px;">-</span>
                    @endif
                  </td>
                <td class="text-center">{{ $row['operation_name']}}</td>


              
                <td>
                  <div class="btn-group">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                      </button>
                      <div class="dropdown-menu">
                        <a class="dropdown-item pm-edit" data-id="{{ $row['preventive_maintenance_id'] }}" data-op="{{ $row['operation_id'] }}" data-schedtype="{{ $row['maintenance_schedule_type_id'] }}"  data-machineid="{{ $row['machine_id'] }}"  data- href="#">Update Preventive Maintenance Request</a>
                        <a class="dropdown-item assign-main-staff" data-id="{{ $row['preventive_maintenance_id'] }}"  href="#">Assigned Maintenance Staff</a>
                        <a class="dropdown-item delete-pm" data-id="{{ $row['preventive_maintenance_id'] }}" data-dtype="pm" data- href="#">Delete</a>
                        <a class="dropdown-item printbtnprintpm" data-id="{{ $row['preventive_maintenance_id'] }}" data-dtype="pm" data- href="#">Print</a>
                      </div>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">No pending request found</td>
              </tr>
              @endforelse
            </tbody>
      </table>
      <center>
          <div id="paginate-pm-pending" class="col-md-12 text-center" style="text-align: center;">
           {{ $maintenance->links() }}
          </div>
        </center>
