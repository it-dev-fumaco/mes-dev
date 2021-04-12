<div class="table-responsive" style="font-size: 11px;">
    <table class="table table-striped text-center">
        <col style="width: 5%;">
        <col style="width: 40%;">
        <col style="width: 40%;">
        <col style="width: 15%;">
      <thead class="text-primary">
        <th class="text-center"><b>No.</b></th>
        <th class="text-left"><b>Preventive Maintenance Task</b></th>
        <th class="text-left"><b>Preventive Maintenance Description</b></th>
        <th class="text-center"><b>Action(s)</b></th>
      </thead>
      <tbody style="font-size:13px;">
        @forelse($list as $row)
        <tr>
          <td class="text-center">
            <span>{{ $row->preventive_maintenance_task_id }}</span>
          </td>
          <td class="text-left">{{ $row->preventive_maintenance_task }}</td>
          <td class="text-left">{{ $row->preventive_maintenance_desc }}</td>
          <td class="text-center">
            <button type='button' class='btn btn-default hover-icon edit-pmt-btn' data-pmtaskdesc="{{$row->preventive_maintenance_desc}}" data-pmtask="{{$row->preventive_maintenance_task}}" data-pmid="{{$row->preventive_maintenance_task_id}}">
              <i class='now-ui-icons design-2_ruler-pencil'></i>
            </button>
            <button type='button' class='btn btn-default hover-icon delete-pmt-btn' data-pmtaskdesc="{{$row->preventive_maintenance_desc}}" data-pmtask="{{$row->preventive_maintenance_task}}" data-pmid="{{$row->preventive_maintenance_task_id}}">
              <i class='now-ui-icons ui-1_simple-remove'></i>
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">No Preventive Maintenance Task Found.</td>
        </tr>
        @endforelse 
      </tbody>
    </table>
    
    <center>
      <div class="col-md-12 text-center" id="search_maintenance_sched_type_pagination">
       {{ $list->links() }}
      </div>
    </center>
  </div>