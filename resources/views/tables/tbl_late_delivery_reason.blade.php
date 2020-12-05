<div class="table-responsive" style="font-size:13px;">

<table class="table table-striped">
          <col style="width: 10%;">
          <col style="width: 80%;">
          <col style="width: 10%;">
        <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
          <th class="text-center"><b>No.</b></th>
          <th class="text-center"><b>Reschedule Delivery Reason</b></th>                                
          <th class="text-center"><b>Action(s)</b></th>
        </thead>
        <tbody>
          @forelse($list as $index => $row)
            <tr>
            <td>{{ $row->reschedule_reason_id }}</td>
            <td class="text-left">{{ $row->reschedule_reason }}</td>
            <td>
              <button type='button' class='btn btn-default hover-icon btn_edit_late_delivery' data-id="{{ $row->reschedule_reason_id }}" data-reason="{{ $row->reschedule_reason }}" >
                <i class='now-ui-icons design-2_ruler-pencil'></i>
              </button>                
            </td>
            @empty
            <tr>
              <td colspan="6" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse

          </tr> 
        </tbody>
      </table>
      <center>
<div id="late_delivery_pagination" class="col-md-12 text-center" style="text-align: center;">
{{ $list->links() }}
</div>
</center>
</div>
