<div class="table-responsive" style="font-size: 10pt; overflow:hidden;">
  <table class="table table-striped">
    <col style="width: 16%;">
    <col style="width: 14%;">
    <col style="width: 10%;">
    <col style="width: 25%;">
    <col style="width: 5%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <col style="width: 10%;">
    <thead class="text-primary" style="font-size: 9pt;">
      <th class="text-center"><b>Sales Order</b></th>
      <th class="text-center"><b>Customer</b></th>
      <th class="text-center"><b>Item Code</b></th>
      <th class="text-center"><b>Description</b></th>
      <th class="text-center"><b>QTY</b></th>
      <th class="text-center"><b>Project</b></th>
      <th class="text-center"><b>Delivery Date</b></th>
      <th colspan="2" class="text-center"><b>Actions</b></th>
    </thead>
    <tbody>
      @forelse($production_order_list as $row)
      <tr>
        <td class="text-center" style="font-weight: bold; font-size: 15pt;">{{ $row['reference_no'] }}</td>
        <td class="text-center">{{ $row['customer'] }}</td>
        <td class="text-center">{{ $row['item_code'] }}</td>
        <td class="text-center">{!! str_limit($row['description'], $limit = 100, $end = '...') !!}</td>
        <td class="text-center">{{ number_format($row['qty']) }}</td>
        <td class="text-center">{{ $row['project'] }}</td>
        <td class="text-center">{{ $row['delivery_date'] }}</td>
        <td class="text-center">
          @if ($row['production_order'] && $row['bom_no'] == null)
          <button class="btn btn-info btn-icon btn-round prod-details-btn" data-jtno="{{ $row['production_order'] }}">
            <i class="now-ui-icons ui-1_zoom-bold" style="font-size: 15pt;"></i>
          </button>
          @else
          <button class="btn btn-info btn-icon btn-round btn_trackmodal" data-itemcode="{{$row['item_code']}}" data-guideid="{{$row['reference_no']}}" data-erpreferenceno="{{$row['erp_reference_no']}}" data-customer="{!! str_limit($row['customer'], $limit = 50, $end = '...') !!}">
            <i class="now-ui-icons ui-1_zoom-bold" style="font-size: 15pt;"></i>
          </button>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td class="text-center" colspan="9">No Production Order(s) found.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
  </div>
  <center>
    <div class="col-md-12 text-center tbl_item_list_pagination" id="tbl_item_list_pagination">
     {{ $query->links() }}
    </div>
  </center>