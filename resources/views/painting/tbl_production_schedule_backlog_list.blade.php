<table class="table table-striped text-center">
   <colgroup>
   <col style="width:10%;">
    <col style="width:12%;">
    <col style="width:16%;">
    <col style="width:10%;">
    <col style="width:6%;">
    <col style="width:8%;">
    <col style="width:8%;">
    <col style="width:8%;">
    <col style="width:6%;">
    <col style="width:6%;">
    <col style="width:10%;">
  </colgroup> 
   <thead class="text-primary stick" style="font-size: 7pt;">
   <th class="text-center sticky-header"><b>P.O</b></th>
    <th class="text-center sticky-header"><b>Customer</b></th>
    <th class="text-center sticky-header"><b>Planned Start Date</b></th>
    <th class="text-center sticky-header"><b>Item</b></th>
    <th class="text-center sticky-header"><b>QTY</b></th>
    <th class="text-center sticky-header"><b>Loading</b></th>
    <th class="text-center sticky-header"><b>Unloading</b></th>
      <th class="text-center sticky-header"><b>CPT QTY</b></th>
      <th class="text-center sticky-header"><b>Bal</b></th>
      <th class="text-center sticky-header"><b>Reject</b></th>
      <th class="text-center sticky-header"><b>Action/s</b></th>
   </thead>
   <tbody style="font-size: 9pt;">
     
      @forelse($data as $row)
      <tr>
      <td class="text-center"><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn" style="color: black;">{{ $row['production_order'] }}</a></td>
         <td class="text-center" >{{ $row['customer'] }}</td>
         <td class="text-center" >{{ $row['planned_start_date']}}</td>
         <td class="text-center" >{{ $row['item_code'] }}- {{$row['item_description']}}</td>
         <td class="text-center" >{{ $row['qty'] }} {{ $row['stock_uom'] }}</td>
         @foreach($row['job_ticket'] as $rows)
         @php
                                if($rows->status == "Completed"){
                                  $status="#2ecc71";
                                }else if($rows->status == "In Progress"){
                                  $status="#f4d03f";
                                }else{
                                  $status="#b2babb";
                                }
                                @endphp
         <td class="text-center" style="background-color: {{$status}};color:white;"><span style="font-size: 9pt;">{{ $rows->status }}</span><span style="font-size: 9pt;display:{{ ($rows->completed_qty <= 0)? 'none': 'block' }};">( {{ $rows->completed_qty }} )</span></td>
         @endforeach
         <td class="text-center">{{ number_format($row['completed_qty']) }} {{ $row['stock_uom'] }} </td>
         <td class="text-center">{{ number_format($row['balance_qty']) }} {{ $row['stock_uom'] }}  </td>        
         
         <td class="text-center">{{ $row['reject'] }} {{ $row['stock_uom'] }}</td>
        <td>
          <div class="btn-group">
            <button type="button" class="btn btn-primary btn-movedtoday" aria-expanded="false" data-prod="{{ $row['production_order'] }}">
              Move Today
            </button>
            
          </div>
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
.stick{
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


</script>