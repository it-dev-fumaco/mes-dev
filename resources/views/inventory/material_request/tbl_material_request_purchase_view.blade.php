<div class="col-md-12">
  
<table class="table ">
        <col style="width: 70%;">
        <col style="width: 30%;">
      @php
        if($purchase_list->status == "Pending"){
        $badge="info";

        }elseif($purchase_list->status == "Ordered"){
          $badge="success";
        }elseif($purchase_list->status == "Cancelled"){
          $badge="danger";
        }elseif($purchase_list->status == "Stopped"){
          $badge="danger";
        }elseif($purchase_list->status == "Partially Ordered"){
          $badge="warning";
        }else{
          $badge="";
        }
        if($purchase_list->per_ordered == 100){
         $stat="Receive";
         $badgee="primary";

        }elseif($purchase_list->per_ordered > 0){
         $stat="Partially Receive";
         $badgee="primary";

                                    
       }else{
        $stat="";
        $badgee="";

       }
       @endphp                  
      <thead style="font-size: 10px;font-weight: bold;border: 1px solid #ABB2B9;background-color: #ABB2B9;padding: 7px 5px 6px 12px;;margin:0;">
        <th colspan="2" class="text-left" style="background-color: #D5D8DC; border: 1px solid #ABB2B9;font-weight: bold;margin-left:-5px;">Material Request Details</th>
      </thead>
       <tbody>
        @php
          $r_date = \Carbon\Carbon::parse($purchase_list->schedule_date)->format('M d, Y');                
        @endphp
          <tr style="font-size: 10pt;">
            <td class="text-left" style="border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;" rowspan="3"><span style="font-size: 17px;font-weight: bold; padding-right: 10px;"> {{$purchase_list->name}}</span><span class="badge badge-{{$badge}}" style="display: inline-block;font-size: 15px;">{{ $purchase_list->status }}</span><span style="display: block;"><i>{{$purchase_list->customer_name}}</i></span><span style="display: block;">{{$purchase_list->project}}</span></td>
          </tr>
          <tr>
            <td class="text-left" style="border: 1px solid #ABB2B9;padding:0;margin:0;"><span style="margin-right: 30px;font-weight: bold;margin-left: 5px;">Required Date:</span><span>{{$r_date}}</span></td>
           </tr>
          <tr>
              <td class="text-left" style="border: 1px solid #ABB2B9;padding:0;margin:0;"><span style="margin-right: 30px;font-weight: bold;margin-left: 5px;">Type:</span><span>{{$purchase_list->purchase_request}}</span></td>
           </tr>
      </tbody>
    </table>
    <table class="table table-bordered" style="padding:0;margin:0;border: 1px solid #ABB2B9;">
      <col style="width: 5%;">
      <col style="width: 40%;">
      <col style="width: 10%;">
      <col style="width: 10%;">
      <col style="width: 15%;">
      <col style="width: 20%;">
    <thead style="font-size: 9px;padding:0;margin:0;border: 1px solid #ABB2B9;">
      <tr>
        <th style="background-color:  #D5D8DC; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;font-size: 15px;" class="text-left" colspan="6"><b>Item Details</b></th>
      </tr>
      <tr>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>No.</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>Item Code</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>QTY</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>UOM</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>For Warehouse</b></th>
      <th style="background-color:   #f2f3f4; border: 1px solid #ABB2B9;padding: 7px 5px 6px 12px;font-weight: bold;" class="text-center"><b>Required Date</b></th>
      </tr>
    </thead>
    <tbody style="font-size:11px;">
      @forelse($purchase_list_item as $row)
      @php
          $i_date = \Carbon\Carbon::parse($row->schedule_date)->format('M d, Y');
          $num = (int)$row->qty;                
        @endphp
      <tr>
        <td class="text-center">
          <span>{{ $row->idx }}</span>
        </td>
        <td class="text-left"><span style="font-weight: bold;padding-right: 5px;">{{ $row->item_code }}</span>-{!! $row->description !!}</td>
        <td class="text-center">{{ $num }}</td>
        <td class="text-center">{{ $row->stock_uom }}</td>
        <td class="text-center">{{ $row->warehouse }}</td>
        <td class="text-center">{{ $i_date }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="5">No Records Found.</td>
      </tr>
      @endforelse 
    </tbody>
  </table>
</div>
