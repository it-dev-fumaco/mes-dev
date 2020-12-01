<table class="table table-striped">
   <col style="width: 11%;">
   <col style="width: 62%;">
   <col style="width: 12%;">
   <col style="width: 15%;">
   <thead class="text-primary" style="font-size: 8pt;">
      <th class="text-center"><b>Prod. No.</b></th>
      <th class="text-center"><b>Item Details</b></th>
      <th class="text-center"><b>Balance Qty</b></th>
      {{-- <th class="text-center">Priority</th> --}}
      <th class="text-center"><b>Status</b></th>
   </thead>
   <tbody style="font-size: 9pt;">
      @php
      $high_priority = collect($unassigned_tasks)->where('priority', 'High')->count();
      // $accept_btn_prop = ($high_priority > 0) ? 'disabled' : '';
      @endphp
      @forelse($unassigned_tasks as $row)
      <tr>
         <td class="text-center">{{ $row['production_order'] }}</td>
         <td><b>{{ $row['production_item'] }}</b><br>{{ $row['description'] }}<br>
            <div class="container">
               <div class="row">
                  <ul class="breadcrumb-c">
                     @foreach($row['workstations'] as $w)
                     <li class="{{ $w['status'] }}">
                        <a href="javascript:void(0);" class="truncate">{{ $w['workstation'] }}</a>
                     </li>
                     @endforeach
                  </ul>
               </div>
            </div>
         </td>
         <td class="text-center" style="font-size: 12pt;">{{ number_format($row['completed_qty']) }}</td>
         {{-- <td class="text-center">{{ $row['priority'] }}</td> --}}
         <td class="text-center">
            <button class="btn btn-danger btn-lg accept-btn" data-name="{{$row['tsdname']}}" data-status="Accepted" {{-- {{ (!$loop->first) ? 'disabled' : '' }} --}}>Accept</button>
         </td>
      </tr>
      @empty
      <tr>
         <td colspan="5" class="text-center">No unassigned task(s) found in this workstation.</td>
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