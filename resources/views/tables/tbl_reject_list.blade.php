<div class="table-responsive" style="font-size:13px;">

<table class="table table-striped">
          <col style="width: 5%;">
          <col style="width: 14.16%;">
          <col style="width: 15.16%;">
          <col style="width: 15.16%;">
          <col style="width: 12.16%;">
          <col style="width: 14.16%;">
          <col style="width: 14.16%;">
          <col style="width: 15%;">
        <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
          <th class="text-center"><b>No.</b></th>
          <th class="text-left"><b>Reject Category</b></th>
          <th class="text-left"><b>Reject Checklist</b></th>
          <th class="text-left"><b>Reject Reason</b></th> 
          <th class="text-left"><b>Responsible</b></th> 
          <th class="text-left"><b>Material Type</b></th> 
          <th class="text-left"><b>Recommended Action</b></th>                                  
          <th class="text-center"><b>Action(s)</b></th>
        </thead>
        <tbody>
          @forelse($reject_list as $index => $row)
            <tr>
            <td>{{ $row->reject_list_id }}</td>
            <td class="text-left">{{ $row->reject_category_name }}</td>
            <td class="text-left">{{ $row->reject_checklist }}</td>
            <td class="text-left">{{ $row->reject_reason }}</td>
            <td class="text-left">{{ $row->responsible }}</td>
            <td class="text-left">{{ $row->material_type }}</td>
            <td class="text-left">{{ $row->recommended_action }}</td>
            <td>
              <button type='button' class='btn btn-default hover-icon edit-rejectlist-btn' data-id="{{ $row->reject_list_id }}" data-ctgID="{{ $row->reject_category_id }}" data-rjtlist="{{ $row->reject_checklist }}" data-rjtreason="{{ $row->reject_reason }}" data-responsible="{{$row->responsible}}" data-owner="{{ $row->owner }}" data-action="{{ $row->recommended_action }}" data-mtype="{{$row->material_type}}" data-reloadtbl="Quality Assurance">
                <i class='now-ui-icons design-2_ruler-pencil'></i>
              </button>
                
                  <button type='button' class='btn btn-default hover-icon btn-delete-rejectlist' data-id="{{ $row->reject_list_id }}" data-ctgID="{{ $row->reject_category_id }}" data-rjtlist="{{ $row->reject_checklist }}" data-rjtreason="{{ $row->reject_reason }}" data-reloadtbl="Quality Assurance"><i class='now-ui-icons ui-1_simple-remove'></i></button>
                
            </td>
            @empty
            <tr>
              <td colspan="8" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse

          </tr> 
        </tbody>
      </table>
      <center>
<div id="reject_check_list_pagination" class="col-md-12 text-center" style="text-align: center;">
{{ $reject_list->links() }}
</div>
</center>
</div>
