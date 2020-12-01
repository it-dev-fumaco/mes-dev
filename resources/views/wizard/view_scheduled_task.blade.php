<div class="row">
   <div class="col-md-8 offset-md-2">
      <dl class="row">
         <dt class="col-sm-2 text-right">Item Code:</dt>
         <dd class="col-sm-10"><b>{{ $item_details->name }}</b></dd>
         <dt class="col-sm-2 text-right">Description:</dt>
         <dd class="col-sm-10">{!! $item_details->description !!}</dd>
      </dl>
   </div>
</div>
<div class="container">
   <div class="row">
      <ul class="breadcrumb-c" style="font-size: 10pt;">
         @foreach($operations as $ops)
         <li class="completed">
            <a href="javascript:void(0);">{{ $ops->workstation }}<br>{{ $ops->process }}</a>
         </li>
         @endforeach
      </ul>
   </div>
</div>
<br>
<div class="row">
   <div class="col-md-8 offset-md-2 text-center">
      <span class="dot" style="background-color: #A9A9B0;"></span> <label style="margin-right: 12px;">Not Started</label>
      <span class="dot" style="background-color: #DAA520;"></span> <label style="margin-right: 12px;">In Process</label>
      <span class="dot" style="background-color: #3CB371;"></span> <label style="margin-right: 12px;">Completed</label>
   </div>
   <div class="con">
      <div class="col-md-12">
         <table class="table table-bordered">
            <thead class="text-primary text-center" style="font-size: 8pt;">
               <th><b>Workstation</b></th>
               @foreach($schedules as $row)
               <th><b>{{ $row['date'] }}</b></th>
               @endforeach
            </thead>
            <tbody style="font-size: 8pt;">
               @foreach($scheduled_production_orders as $row1)
               <tr>
                  <td style="width: 100px;">{{ $row1['workstation'] }}</td>
                  @foreach($row1['scheduled_production_orders'] as $row2)
                  <td style="width: 100px;">
                     <table>
                        <tr>
                           @forelse($row2['production_orders'] as $row3)
                           @php
                           if ($row3['status'] == 'Completed') {
                              $color = '#3CB371';
                           }else if ($row3['status'] == 'In Process') {
                              $color = '#DAA520';
                           }else{
                              $color = '#A9A9B0';
                           }
                           @endphp
                           <td class="text-white" style="background-color: {{ $color }}">
                              <div class="thumbs">
                                 <ul>
                                    <li>
                                       <span class="hvrlink">{{ $row3['item_code'] }} - {{ $row3['qty'] }}</span>
                                       <div class="details-pane">
                                          <h3 class="title">{{ $row3['production_order'] }}<span>[{{ $row3['status'] }}]</span></h3>
                                          <p class="desc">
                                             <b>{{ $row3['item_code'] }}:</b> {!! $row3['description'] !!}<br>
                                             Qty: <b>{{ number_format($row3['qty']) }} {{ $row3['stock_uom'] }}</b><br>
                                             Sales Order: <b>{{ $row3['sales_order'] }}</b><br>
                                             Customer: <b>{{ $row3['customer'] }}</b><br>
                                             Delivery Date: <b>{{ $row3['delivery_date'] }}</b>
                                             @if(in_array($row3['status'], ['Completed', 'In Process']))
                                             <br>Start Time: <b>{{ $row3['time_details']['start_time'] }}</b>
                                             @endif
                                             @if($row3['status'] == 'Completed')
                                             <br>End Time: <b>{{ $row3['time_details']['end_time'] }}</b><br>Duration: <b>{{ $row3['time_details']['duration'] }}</b>
                                             @endif
                                          </p>
                                       </div>
                                    </li>
                                 </ul>
                              </div>
                           </td>
                           @empty
                           <td>No Production Order(s) Found.</td>
                           @endforelse
                        </tr>
                     </table>
                  </td>
                  @endforeach
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>
</div>

<style type="text/css">
   .dot {
    height: 13px;
    width: 13px;
    background-color: #bbb;
    display: inline-block;
  }
.con {
   width: 500em;
   overflow-x: scroll;
   white-space: nowrap;
   padding: 5px;
   min-height: 600px;
}

/** page structure **/
.thumbs ul{
   padding: 0;
   margin: 0;
}

.thumbs ul li {
  display: block;
  position: relative;
  float: left;
  margin: 0;
  padding: 0;
}

/** detail panel **/
.details-pane {
  display: none;
  color: #414141;
  background: #f1f1f1;
  border: 1px solid #a9a9a9;
  position: absolute;
  top: 20px;
  left: 0;
  z-index: 99999;
  width: 500px;
  padding: 6px 8px;
  text-align: left;
  -webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  -moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  white-space: normal;
}

.details-pane h3 {
  font-size: 2.0em;
  line-height: 1.1em;
  margin-bottom: 4px;
}

.details-pane h3 span {
  font-size: 0.75em;
  font-style: italic;
  color: #555;
  padding-left: 15px;
}

.details-pane .desc {
  font-size: 1.2em;
  margin-bottom: 6px;
}

/** hover styles **/
span.hvrlink:hover + .details-pane {
  display: block;
}
.details-pane:hover {
  display: block;
}
</style>