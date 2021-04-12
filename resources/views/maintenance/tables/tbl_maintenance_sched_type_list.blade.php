<div class="table-responsive" style="font-size: 11px;">
    <table class="table table-striped text-center">
        <col style="width: 5%;">
        <col style="width: 26.7%;">
        <col style="width: 26.7%;">
        <col style="width: 26.6%;">
        <col style="width: 15%;">
      <thead class="text-primary">
        <th class="text-center"><b>No.</b></th>
        <th class="text-left"><b>Maintenance Schedule Type</b></th>
        <th class="text-left"><b>Start Date</b></th>
        <th class="text-left"><b>Operation</b></th>
        <th class="text-center"><b>Action(s)</b></th>
      </thead>
      <tbody style="font-size:13px;">
        @forelse($list as $row)
        <tr>
          <td class="text-center">
            <span>{{ $row->maintenance_schedule_type_id }}</span>
          </td>
          <td class="text-left">{{ $row->maintenance_schedule_type }}</td>
          <td class="text-left">{{ $row->start_date }}</td>
          <td class="text-left">{{ $row->operation_name }}</td>
          <td class="text-center">
            <button type='button' class='btn btn-default hover-icon edit-main-sched-btn' data-maintype="{{$row->maintenance_schedule_type}}" data-maintypeid="{{$row->maintenance_schedule_type_id}}" data-startdate="{{$row->start_date}}" data-opid="{{$row->operation_id}}">
              <i class='now-ui-icons design-2_ruler-pencil'></i>
            </button>
            <button type='button' class='btn btn-default hover-icon delete-main-sched-btn' data-opname="{{ $row->operation_name }}" data-maintype="{{$row->maintenance_schedule_type}}" data-maintypeid="{{$row->maintenance_schedule_type_id}}" data-startdate="{{$row->start_date}}" data-opid="{{$row->operation_id}}">
              <i class='now-ui-icons ui-1_simple-remove'></i>
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">No Maintenance Schedule Type Found.</td>
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