<table class="table table-striped table-bordered text-center">
  <col style="width: 35%;">
  <col style="width: 25%;">
  <col style="width: 20%;">
  <col style="width: 10%;">
  <col style="width: 10%;">
  <thead class="text-primary text-uppercase font-weight-bold" style="font-size: 6pt;">
    <th class="text-center p-2"><b>Lot Size(PCS)</b></th>
    <th class="text-center p-2"><b>Sample Size</b></th>
    <th class="text-center p-2"><b>AC</b></th>
    <th class="text-center p-2"><b>RE</b></th>                                  
    <th class="text-center p-2"><b>Action</b></th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($sampling_plan as $index => $row)
    <tr>
      <td class="text-center p-2"><b>{{ $row->lot_size_min }}</b> TO <b>{{ $row->lot_size_max }}</b></td>
      <td class="text-center p-2">{{ $row->sample_size }}</td>
      <td class="text-center p-2">{{ $row->acceptance_level }}</td>
      <td class="text-center p-2">{{ $row->reject_level }}</td>
      <td class="text-center p-2">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon btn-delete-sampling_plan' data-id="{{$row->sampling_plan_id}}" data-category="{{$row->reject_category_id}}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr> 
    @empty
    <tr>
      <td colspan="6" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="sampling_visual_pagination" class="col-md-12 text-center">{{ $sampling_plan->links() }}</div>