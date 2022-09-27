<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <col style="width: 5%;">
  <col style="width: 20%;">
  <col style="width: 25%;">
  <col style="width: 40%;">
  <col style="width: 10%;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="text-center p-2"><b>No.</b></th>
    <th class="text-center p-2"><b>Type</b></th>
    <th class="text-center p-2"><b>Reject Category</b></th>
    <th class="text-center p-2"><b>Description</b></th>                                  
    <th class="text-center p-2"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($reject_category as $index => $row)
    <tr>
      <td class="text-center p-2">{{ $row->reject_category_id }}</td>
      <td class="text-center p-2">{{ $row->type }}</td>
      <td class="text-center p-2">{{ $row->reject_category_name }}</td>
      <td class="text-left p-2">{{ $row->category_description }}</td>
      <td class="text-center p-2">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon edit-reject-category-btn' data-id="{{ $row->reject_category_id }}" data-type="{{ $row->type }}" data-category="{{ $row->reject_category_name }}" data-categorydesc="{{ $row->category_description }}">
          <i class='now-ui-icons design-2_ruler-pencil'></i>
        </button>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon btn-delete-reject-category' data-id="{{ $row->reject_category_id }}" data-type="{{ $row->type }}" data-category="{{ $row->reject_category_name }}" data-categorydesc="{{ $row->category_description }}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr> 
    @empty
    <tr>
      <td colspan="6" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="reject_category_pagination" class="col-md-12 text-center">{{ $reject_category->links() }}</div>