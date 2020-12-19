<div class="table-responsive" style="font-size:12px;">

<table class="table table-striped">
          <col style="width: 5%;">
          <col style="width: 23.75%;">
          <col style="width: 23.75%;">
          <col style="width: 23.75%;">
          <col style="width: 23.75%;">
        <thead class="text-primary" style="font-size: 9px;font-weight: bold;">
          <th class="text-center"><b>No.</b></th>
          <th class="text-left"><b>Category</b></th>
          <th class="text-left"><b>Reason</b></th>
          <th class="text-left"><b>Material Type</b></th>
          <th class="text-left"><b>Recommended Action</b></th>

        </thead>
        <tbody>
          @forelse($check_list as $index => $row)
            <tr>
            <td>{{ $row->reject_list_id }}</td>
            <td class="text-left">{{ $row->reject_category_name }}</td>
            <td class="text-left"> {{ $row->reject_reason }}</td>
            <td class="text-left"> {{ $row->material_type }}</td>
            <td class="text-left"> {{ $row->recommended_action }}</td>
            
            @empty
            <tr>
              <td colspan="6" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse
          </tr>
        </tbody>
      </table>
      <center>
<div id="operator_checklist_list_pagination_assembly" class="col-md-12 text-center" style="text-align: center;">
{{ $check_list->links() }}
</div>
</center>
</div>
