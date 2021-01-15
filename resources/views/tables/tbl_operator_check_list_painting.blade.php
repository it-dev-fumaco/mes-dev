<div class="table-responsive" style="font-size:12px;">

<table class="table table-striped">
          <col style="width: 5%;">
          <col style="width: 17.5%;">
          <col style="width: 22.5%;">
          <col style="width: 22.5%;">
          <col style="width: 23.5%;">
          <col style="width: 10%;">
        <thead class="text-primary" style="font-size: 9px;font-weight: bold;">
          <th class="text-center"><b>No.</b></th>
          <th class="text-left"><b>Workstation</b></th>
          <th class="text-left"><b>Category</b></th>
          <th class="text-left"><b>Process</b></th>
          <th class="text-left"><b>Reason</b></th>
          
          <th class="text-center"><b>Action(s)</b></th>
        </thead>
        <tbody>
          @forelse($check_list as $index => $row)
            <tr>
            <td>{{ $row->operator_reject_list_setup_id }}</td>
            <td class="text-left">{{ $row->workstation_name }}</td>
            <td class="text-left"> {{ $row->reject_category_name }}</td>
            <td class="text-left"> {{ $row->process_name }}</td>
            <td class="text-left"> {{ $row->reject_reason }}</td>
            
            <td>
                <a href="#" class="hover-icon"  data-toggle="modal" style="padding-left: 5px;">
                  <button type='button' class='btn btn-default btn-delete-opchecklist' data-id="{{$row->operator_reject_list_setup_id}}" data-workstation="{{$row->workstation_name}}" data-rejectchecklist="{{$row->reject_reason}}" data-operation="{{$row->operation_name}}" data-reloadtbl="Painting"><i class='now-ui-icons ui-1_simple-remove'></i></button>
                </a>
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
<div id="operator_checklist_list_pagination_painting" class="col-md-12 text-center" style="text-align: center;">
{{ $check_list->links() }}
</div>
</center>
</div>
