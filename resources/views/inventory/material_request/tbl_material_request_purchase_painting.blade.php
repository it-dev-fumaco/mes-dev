<div class="table-responsive" style="font-size:13px;">

<table class="table table-striped">
          <col style="width: 5%;">
          <col style="width: 9%;">
          <col style="width: 9%;">
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col style="width: 10%;">
          <col style="width: 9%;">
          <col style="width: 9%;">
          <col style="width: 9%;">
        <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
          <th class="text-center"><b>No</b></th>
          <th class="text-center"><b>PREQ/MREQ</b></th>
          <th class="text-center"><b>Required Date</b></th>
          <th class="text-center"><b>Purchase Request</b></th>
          <th class="text-center"><b>Sales Order</b></th>
          <th class="text-center"><b>Customer</b></th>
          <th class="text-center"><b>Project</b></th>   
          <th class="text-center"><b>Transaction Date</b></th>
          <th class="text-center"><b>Created By</b></th>  
          <th class="text-center"><b>Status</b></th>                               
          <th class="text-center"><b>Action(s)</b></th>
        </thead>
        <tbody>
          @forelse($purchase_list as $index => $row)
          @php
           if($row->status == "Pending"){
            $badge="info";
            $tohide="none";
            $submit="";
           }elseif($row->status == "Ordered"){
              $badge="success";
              $tohide="";
              $submit="none";
            }elseif($row->status == "Cancelled"){
              $badge="danger";
              $tohide="none";
              $submit="none";
            }elseif($row->status == "Stopped"){
              $badge="danger";
              $tohide="none";
              $submit="none";
            }elseif($row->status == "Partially Ordered"){
              $badge="warning";
              $tohide="none";
              $submit="";
           }else{
              $badge="";
              $tohide="";
              $submit="none";
           }
           if($row->per_ordered == 100){
            $stat="Receive";
            $badgee="primary";
            $submit="";

           }elseif($row->per_ordered > 0){
            $stat="Partially Receive";
            $badgee="primary";
            $submit="";

            
           }else{
             $stat="";
             $badgee="";
           }
           if($row->docstatus == "2"){
              $badge="danger";
              $tohide="";
              $submit="none";
              $stat_name="Confirmed";
            }elseif($row->status == "Ordered"){
              $tohide="";
              $submit="none";
         }
           $r_date = \Carbon\Carbon::parse($row->schedule_date)->format('M d, Y');  
           $trans = \Carbon\Carbon::parse($row->creation)->format('M d, Y');               

          @endphp
            <tr>
            <td class="text-center">{{ $index +1 }}</td>
            <td class="text-center"><b><a class="view_purchase_detail_btn" href="#" data-id="{{ $row->name }}" style="color:black;">{{ $row->name }}</a></b></td>
            <td class="text-center">{{ $r_date }}</td>
            <td class="text-center">{{ $row->purchase_request }}</td>
            <td class="text-center">{{ $row->sales_order }}</td>
            <td class="text-center">{{ $row->customer_name }}</td>
            <td class="text-center">{{ $row->project }}</td>
            <td class="text-center">{{ $trans }}</td>
            <td class="text-center">{{ $row->owner }}</td>
            <td class="text-center" style="font-size: 17px;"><span class="badge badge-{{$badge}}">{{ $row->status }}</span><span class="" style="display: {{ ($stat == '')? 'none': 'block' }};font-size: 12px;">{{$stat}}</span></td>
            <td class="text-center">
            <button type="button" class="btn btn-primary cancel-purchase-btn" data-id="{{ $row->name }}" style="display: {{$submit}}">Cancel</button>
            <button type="button" class="btn btn-default view_purchase_detail_btn" data-id="{{ $row->name }}" style="display: {{$tohide}}">Details</button>
             {{-- <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item view_purchase_detail_btn" href="#" data-id="{{ $row->name }}">Details</a>
                    <a class="dropdown-item" href="#">Duplicate</a>
                    <a class="dropdown-item cancel-purchase-btn" href="#"  data-id="{{ $row->name }}" style="display: {{$tohide}}">Cancel</a>
                    
                  </div>
                </div>
                --}}
                
            </td>
            @empty
            <tr>
              <td colspan="9" class="text-center">No Record(s) Found.</td>
            </tr>
            @endforelse

          </tr> 
        </tbody>
      </table>
      </div>
        <div class="row">
        <div id="purchase_list_painting_pagination" class="col-md-8 text-center" style="text-align: center;">
          {{ $purchase_list->links() }}
        </div>
        <div class="col-md-4 text-right"><h6 style="display:inline"><b>Total Material Request: </b></h6><span style="display:inline-block;padding-right:70px;padding-left:20px; font-size:16px;font-weight:bolder;">{{ $count }}</span></div>
        </div>
      </div>

