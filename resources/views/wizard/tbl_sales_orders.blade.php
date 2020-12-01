{{-- <table class="table table-stri1ed table-hover">
  <thead class="text-primary">
    <th class="text-center"><b>Sales Order</b></th>
    <th class="text-center"><b>Customer</b></th>
    <th class="text-center"><b>Sales Type</b></th>
    <th class="text-center"><b>SO Date</b></th>
    <th class="text-center"><b>Delivery Date</b></th>
    <th class="text-center"><b>Actions</b></th>
  </thead>
  <tbody>
    @forelse($open_so as $idx => $so)
    <tr>
      <td class="text-center">{{ $so->name }}</td>
      <td class="text-left">{{ $so->customer }}</td>
      <td class="text-center">{{ $so->sales_type }}</td>
      <td class="text-center">{{ $so->transaction_date }}</td>
      <td class="text-center">{{ $so->delivery_date }}</td>
      <td class="td-actions text-center">
        <button type="button" rel="tooltip" class="btn btn-info view-row" data-id="{{ $so->name }}" data-type="Sales Order" data-status="{{ $so->status }}">
          <i class="now-ui-icons ui-1_zoom-bold"></i>
        </button>
        <button type="button" rel="tooltip" class="btn btn-danger delete-row">
          <i class="now-ui-icons ui-1_simple-remove"></i>
        </button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="9" class="text-center">No Sales Order(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table> --}}