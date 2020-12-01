<table class="table custom-table-striped text-center">
   <colgroup>
    <col style="width:13%">
    <col style="width:10%">
    <col style="width:10%">
    <col style="width:6%">
    <col style="width:8%">
    <col style="width:10%">
    <col style="width:10%">
    <col style="width:5%">
    <col style="width:5%">
    <col style="width:8%">
    <col style="width:10%">
    <col style="width:5%">
  </colgroup> 
   <thead class="text-primary" style="font-size: 7pt;">
    <th class="text-center sticky-header"><b>P.O</b></th>
    <th class="text-center sticky-header"><b>Customer</b></th>
    <th class="text-center sticky-header"><b>Item</b></th>
    <th class="text-center sticky-header"><b>QTY</b></th>
    <th class="text-center sticky-header"><b>Loading</b></th>
    <th class="text-center sticky-header"><b>Unloading</b></th>
      <th class="text-center sticky-header"><b>CPT QTY</b></th>
      <th class="text-center sticky-header"><b>Bal</b></th>
      <th class="text-center sticky-header"><b>Reject</b></th>
      <th class="text-center sticky-header"><b>QA</b></th>
      <th class="text-center sticky-header"><b>Notes</b></th>
      <th class="text-center sticky-header"><b>Action/s</b></th>
   </thead>
   <tbody style="font-size: 9pt;">
         @forelse($data as $row)
         <tr style="display:{{(number_format($row['qty']) <= $row['feedback_qty'])? 'none':''}};">
            <td class="text-center" rowspan="2"><span class="badge badge-info" style="font-size: 9pt;margin-right:10px;">{{ $row['sequence']}}</span><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn" style="color: black;">{{ $row['production_order'] }}</a></td>
            <td class="text-center" rowspan="2">{{ $row['customer'] }}</td>
            <td class="text-center" rowspan="2">{{ $row['item_code'] }}- {{$row['item_description']}}</td>
            <td class="text-center" rowspan="2">{{ $row['qty'] }} {{ $row['stock_uom'] }}</td>
            @foreach($row['job_ticket'] as $rows)
            @php
                                   if($rows->status == "Completed"){
                                     $status="#2ecc71";
                                   }else if($rows->status == "In Progress"){
                                     $status="#f4d03f";
                                   }else{
                                     $status="#b2babb";
                                   }
                                   if($row['prod_status'] == 'Completed'){
                                     $display = '';
                                   }else{
                                     $display = '2';
                                   }
                                   @endphp
            <td class="text-center" style="background-color: {{$status}};color:white;" rowspan="{{$display}}"><span style="font-size: 9pt;">{{ $rows->status }}</span><span style="font-size: 9pt;display:{{ ($rows->completed_qty <= 0)? 'none': 'block' }};">( {{ $rows->completed_qty }} )</span></td>
            @endforeach
            <td class="text-center" rowspan="2">{{ number_format($row['completed_qty']) }} {{ $row['stock_uom'] }} </td>
            <td class="text-center" rowspan="2">{{ number_format($row['balance_qty']) }} {{ $row['stock_uom'] }}  </td>        
            
            <td class="text-center" rowspan="2">{{ $row['reject'] }} {{ $row['stock_uom'] }}</td>
   
            <td class="text-center" rowspan="2"><span class="badge" style="font-size: 9pt;"></span><span style="display: block;"></span></td>
            <td class="text-center" rowspan="2">{{ $row['remarks']}} </td>
            <td rowspan="2">
             <div class="btn-group">
               <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 Action
               </button>
               <div class="dropdown-menu">
                 <a class="dropdown-item mark-done-btn" href="#" id="mark-done-btn" data-workstationid="{{-- $row['workstation_id'] --}}" data-jtid="{{-- $row['jtname'] --}}" data-workstation="{{-- $row['workstation_plot'] --}}" data-qtyaccepted="{{-- $row['qty_accepted'] --}}" data-prod="{{$row['production_order']}}">Mark as Done</a>
                 <a class="dropdown-item create-feedback-btn" data-production-order="{{ $row['production_order'] }}" data-completed-qty="{{ $row['completed_qty'] }}" href="#">Feedback</a>
                 <a class="dropdown-item addnotes" href="#" id="addnotes-btn" data-workstationid="{{-- $row['workstation_id'] --}}" data-jtid="{{-- $row['jtname'] --}}" data-workstation="{{-- $row['workstation_plot'] --}}" data-qtyaccepted="{{-- $row['qty_accepted'] --}}" data-prod="{{$row['production_order']}}" data-remarks="{{$row['remarks'] }}">Add Notes</a>
                 <a class="dropdown-item editcpt_qty" href="#" id="editcpt-qty-btn" data-prod="{{$row['production_order']}}" data-qty="{{ $row['qty'] }}">Edit</a>
   
               </div>
             </div>
           </td>
         </tr>
         @php
                                   if($row['prod_status'] == 'Completed'){
                                     $display = 'none';
                                     $colpsn = '2';
                                     $colorme = '#2ecc71';
                                   }else{
                                     $display='none';
                                     $colorme = '';
                                     $colpsn = '1';
                                   }
                                   @endphp
   
         <tr class="heightcustom" style="background-color: {{ $colorme }};color:white; display:{{(number_format($row['qty']) <= $row['feedback_qty'])? 'none':''}};">
           
           <td class="text-center" colspan="{{ $colpsn }}" style="height: 20px;border: none;">{{ ($row['prod_status'] == 'Completed')? $row['duration']:'' }} </td>
           <td style="display:{{$display}} ;">
           </td>
         </tr>
         @empty
         <tr>
            <td colspan="12" class="text-center">No task(s) found</td>
         </tr>
         @endforelse
   </tbody>
</table>

<style type="text/css">
  .custom-table-striped > tbody > tr:nth-child(4n+1) > td, .custom-table-striped > tbody > tr:nth-child(4n+1) > th {
   background-color: whitesmoke;
}
  td.heightcustom > div {
    width: 100%;
    height: 100%;
    overflow:hidden;
}
td.heightcustom {
    height: 20px;
}
  .breadcrumb-c {
    font-size: 8pt;
    font-weight: bold;
    padding: 0px;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 10px;
    margin-bottom: 10px;
    width: 100%;
    border-radius: 4px;
}
th.sticky-header {
  position: sticky;
  top: 0;
  z-index: 10;
  background-color: white;
}
.breadcrumb-c>li {
    display: table-cell;
    vertical-align: top;
    width: 1%;
}

.breadcrumb-c>li+li:before {
    padding: 0;
}

.breadcrumb-c li a {
    color: white;
    text-decoration: none;
    padding: 1px 0 1px 5px;
    position: relative;
    display: inline-block;
    width: calc( 100% - 10px );
    background-color: hsla(0, 0%, 83%, 1);
    text-align: center;
    text-transform: capitalize;
}

.breadcrumb-c li.completed a {
    background: brown;
    background: hsla(153, 57%, 51%, 1);
}

.breadcrumb-c li.completed a:after {
    border-left: 30px solid hsla(153, 57%, 51%, 1);
}

.breadcrumb-c li.active a {
    background: #ffc107;
}

.breadcrumb-c li.active a:after {
    border-left: 30px solid #ffc107;
}

.breadcrumb-c li:first-child a {
    padding-left: 1px;
}

.breadcrumb-c li:last-of-type a {
    width: calc( 100% - 38px );
}

.breadcrumb-c li a:before {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid white;
    position: absolute;
    top: 50%;
    margin-top: -50px;
    margin-left: 1px;
    left: 100%;
    z-index: 1;
}

.breadcrumb-c li a:after {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid hsla(0, 0%, 83%, 1);
    position: absolute;
    top: 50%;
    margin-top: -50px;
    left: 100%;
    z-index: 2;
}


  .truncate {
    white-space: nowrap;
    /*overflow: hidden;*/
    text-overflow: ellipsis;
  }
</style>



<script type="text/javascript">
      function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 500,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }
</script>
<script type="text/javascript">
    $(document).on('click', '.prod-details-btn', function(e){
    e.preventDefault();
    var jtno = $(this).data('jtno');
    $('#jt-workstations-modal .modal-title').text(jtno);
    if(jtno){
      getJtDetails($(this).data('jtno'));
    }else{
      showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
    }
  });
  

</script>