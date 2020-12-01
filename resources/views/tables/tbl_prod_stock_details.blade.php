<div class="col-md-12">
  <table class="table">
    <col style="width: 70%;">
      <col style="width: 30%;">
        @php
        if($ste->item_status == "For Checking"){
          $badge="info";
          $tohide="";
          $submit="none";
          $stat_name="Pending";
        }elseif($ste->item_status == "Issued"){
          $badge="warning";
          $tohide="";
          $submit="";
          $stat_name='Issued';
        }else{
          $badge="";
          $tohide="";
          $submit="none";
          $stat_name=$ste->item_status;
        }
        if($ste->docstatus == "2"){
          $badge="danger";
          $tohide="none";
          $submit="none";
          $stat_name="Cancelled";
        }elseif($ste->docstatus == "1"){
          $badge="success";
          $tohide="none";
          $submit="none";
          $stat_name="Issued";
        }
        $r_date = \Carbon\Carbon::parse($ste->creation)->format('M d, Y');               
      @endphp                 
      <thead style="font-size: 10px;font-weight: bold;border: 1px solid #ABB2B9;background-color: #ABB2B9;padding: 7px 5px 6px 12px;;margin:0;">
        <th class="text-left" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;font-weight: bold;margin-left:-5px;">Material Tranfer to Manufacture</th>
        <th style="background-color: #D5D8DC;">
          <div class="pull-right">
          @if($ste->docstatus == 1)
          <button type="button" class="btn btn-success" disabled>Stock Entry Submitted</button>
          @elseif($ste->docstatus == 2)
          <button type="button" class="btn btn-danger" disabled>Stock Entry Cancelled</button>
          @else
          <button type="button" class="btn btn-primary" id="submit-ste-btn" data-ste="{{ $ste->name }}">Submit Stock Entry</button>
          @endif
          </div>
        </th>
      </thead>
       <tbody>
          <tr style="font-size: 10pt;">
            <td class="text-left" style="border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;" rowspan="2">
              <span style="font-size: 17px;font-weight: bold; padding-right: 10px;"> {{$ste->name}}</span>
              <span class="badge badge-{{$badge}}" style="display: inline-block;font-size: 15px;">{{ $stat_name }}</span>
              <span style="display: block;"><i>{{$ste->so_customer_name}}</i></span>
              <span style="display: block;">{{$ste->project}}</span>
            </td>
            <td class="text-left" style="border: 1px solid #ABB2B9;padding:0;margin:0;">
              <span style="margin-right: 30px;font-weight: bold;margin-left: 5px;">Transaction Date:</span><span>{{$r_date}}</span>
            </td>
          </tr>
          <tr>
              <td class="text-left" style="border: 1px solid #ABB2B9;padding:0;margin:0;">
                <span style="margin-right: 30px;font-weight: bold;margin-left: 5px;">Reference:</span><span>{{$ste->sales_order_no}}, &nbsp;{{$ste->material_request}}</span>
              </td>
           </tr>
      </tbody>
    </table>
    <table class="table table-bordered" style="padding:0;margin:0;border: 1px solid #ABB2B9;">
    <col style="width: 5%;">
      <col style="width: 25%;">
      <col style="width: 10%;">
      <col style="width: 15%;">
      <col style="width: 15%;">
      <col style="width: 15%;">
      <col style="width: 15%;">
    <thead style="font-size: 9px;padding:0;margin:0;border: 1px solid #ABB2B9;">
      <tr>
        <th style="background-color:  #D5D8DC; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;font-size: 15px;" class="text-left" colspan="9"><b>Item Details</b></th>
      </tr>
      <tr>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>No.</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>Item Code</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>QTY</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>Source Warehouse</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>Target Warehouse</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>Status</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>Issued By</b></th>
      </tr>
    </thead>
    <tbody style="font-size:11px;">
      @forelse($ste_item as $row)
      @php
          $i_date = \Carbon\Carbon::parse($row->creation)->format('M d, Y');
          if($row->date_modified == null){
            $date_iisued="";
          }else{
            $date_iisued = \Carbon\Carbon::parse($row->date_modified)->format('M d, Y');
          }

        @endphp
      <tr>
        <td class="text-center">
          <span>{{ $row->idx }}</span>
        </td>
        <td class="text-left"><span style="font-weight: bold;padding-right: 5px;">{{ $row->item_code }}</span>-{!! $row->description !!}</td>
        <td class="text-center"> <span style="font-size:12px;font-weight:bold;">{{ $row->qty }}</span> <br> <span style="font-size:10px;">{{ $row->stock_uom }}</span></td>
        <td class="text-center">{{ $row->s_warehouse }}</td>
        <td class="text-center">{{ $row->t_warehouse }}</td>
        <td class="text-center">{{ $row->status }}</td>
        <td class="text-center"> <span style="font-size:12px;font-weight:bold;">{{ $row->s_warehouse_personnel }}</span> <br> <span style="font-size:10px;">{{ $date_iisued }}</span></td>
        
      </tr>
      @empty
      <tr>
        <td colspan="5">No Records Found.</td>
      </tr>
      @endforelse 
    </tbody>
  </table>
</div>
