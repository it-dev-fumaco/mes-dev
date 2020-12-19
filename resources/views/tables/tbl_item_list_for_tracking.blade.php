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
      @forelse($so_item_list as $r)
       @foreach($r['item'] as $rows)
      <tr>
        <td class="text-center" style="font-weight: bold; font-size: 15pt;">
          {{$rows['sales_order']}}
        </td>
        <td class="text-center">
          {{$rows['customer']}}
        </td>
        <td class="text-center">
          {{$rows['item_code']}}
        </td>
        <td class="text-center">
          {!! str_limit($rows['description'], $limit = 100, $end = '...') !!}
        </td>
        <td class="text-center">
          {{ number_format($rows['qty']) }} 
        </td>
        <td class="text-center">
          {{$rows['project']}}
        </td>
        <td class="text-center">
          {{$rows['delivery_date']}}
        </td>
        <td class="text-center">
          <!-- <a href="/get_bom_tracking/{{$r['guide_id']}}/{{$rows['item_code']}}"> -->
          <button class="btn btn-info btn-icon btn-round btn_trackmodal" data-itemcode="{{$rows['item_code']}}" data-guideid="{{$r['guide_id']}}" data-customer="{!! str_limit($rows['customer'], $limit = 50, $end = '...') !!}">
            <i class="now-ui-icons ui-1_zoom-bold" style="font-size: 15pt;"></i>
          </button>
        <!-- </a> -->
        </td>

      </tr>
      @endforeach
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
     {{ $production_orders->links() }}
    </div>
  </center>