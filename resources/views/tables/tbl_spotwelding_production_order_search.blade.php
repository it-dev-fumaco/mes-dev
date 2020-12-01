<div class="col-md-12" style="padding: 0; font-size: 9pt;">
@foreach($task_list as $index => $row)
 <span style="font-size: 13pt;margin-top: -13px; display: block; font-weight: bold;"><b>{{ $row['sales_order'] }}</b></span>
  <span style="font-size: 13pt; display: inline-block; font-weight: bold;"><b>{{ $row['item_code'] }}</b></span>
              <span style="font-size: 11pt; display: inline;">-{!! $row['description'] !!}</span>
         <table style="width: 100%;margin-top: -40px;">
          <tr>
            <td style="width: 60%; padding: 5px;margin-top: -10px;">
              
            </td>
            <td></td>
            <td style="width: 10%; background-color: #D5D8DC; border: 1px solid #ABB2B9;" class="align-top text-center">
              <span style="font-size: 10pt; color: #707B7C; display: block;">QTY</span>
              <span style="font-size: 15pt; display: block;"><b>{{ $row['qty_to_manufacture'] }}</b></span>
              <span style="font-size: 9pt; display: block;">{{ $row['stock_uom'] }}</span>
            </td>
            <td style="width: 10%; background-color: #D5D8DC; border: 1px solid #ABB2B9;" class="align-top text-center">
              <span style="font-size: 10pt; color: #707B7C; display: block;">IN PROCESS</span>
              <span style="font-size: 15pt; display: block;"><b>{{ $row['qty_to_manufacture'] - $row['completed_qty'] }}</b></span>
              <span style="font-size: 9pt; display: block;">{{ $row['stock_uom'] }}</span>
            </td>
            
            <td style="width: 10%; background-color: #D5D8DC; border: 1px solid #ABB2B9;" class="align-top text-center">
              <span style="font-size: 10pt; color: #707B7C; display: block;">REJECT</span>
              <span style="font-size: 15pt; display: block;"><b>{{ $total_rejects }}</b></span>
              <span style="font-size: 9pt; display: block;">{{ $row['stock_uom'] }}</span>
            </td>
            <td style="width: 10%; background-color: #D5D8DC; border: 1px solid #ABB2B9;" class="align-top text-center">
              <span style="font-size: 10pt; color: #707B7C; display: block;">COMPLETED</span>
              <span style="font-size: 15pt; display: block;"><b>{{ $row['completed_qty'] }}</b></span>
              <span style="font-size: 9pt; display: block;">{{ $row['stock_uom'] }}</span>
            </td>
            
          </tr>
        </table>
    @endforeach
        <br>
           <table class="table table-bordered" border="1">
             <tbody>
              <tr>
                 <th class="text-center">PROCESS</th>
                 <th class="text-center">MACHINE</th>
                 <th class="text-center">DURATION</th>
                 <th class="text-center">GOOD</th>
                 <th class="text-center">REJECT</th>
                 <th class="text-center">OPERATOR</th>
               </tr>
               @forelse($logs as $log)
               <tr style="font-size: 10pt;" class="{{ ($log['status'] == 'In Progress') ? 'blink-bg' : '' }}">
                 <td class="text-center">{{ $log['process_description'] }}<br>
                  <span style="font-size: 10pt;" class="badge badge-{{ ($log['status'] == 'Completed') ? 'success' : 'warning' }} text-white">{{ $log['status']}}</span>
                  </td>
                 <td class="text-center">{{ $log['machine'] }}</td>
                 <td class="text-center">{{ ($log['status'] == 'Completed') ? $log['duration'] : '-' }}</td>
                 <td class="text-center">{{ $log['completed_qty'] }}</td>
                 <td class="text-center">{{ $log['reject'] }}</td>
                 <td class="text-center">{{ $log['operator_name'] }}</td>
               </tr>
               @empty
               <tr>
                 <td class="text-center" colspan="6">No Record(s) Found.</td>
               </tr>
               @endforelse
             </tbody>
           </table>
        
          </div>
          <style>
  .blinking{
    animation:blinkingText 1.2s infinite;
  }

  @keyframes blinkingText{
    0%{     color: #273746;    }
    49%{    color: #273746; }
    60%{    color: transparent; }
    99%{    color:transparent;  }
    100%{   color: #273746;    }
  }

  .operator-badge {
    position:relative;
    padding-top:15px;
    padding-bottom: 5px;
    display:inline-block;
  }

  .notify-badge{
    position: absolute;
    right:-5px;
    top:12px;
    background: #f57f17;
    text-align: center;
    border-radius: 30px;
    color:white;
    padding:5px 8px;
    font-size:8px;
  }

  .part-items{
    cursor: pointer;
  }

  .selected-part{
    background-color:  #85929e  !important;
  }

  .completed{
    background-color: #2ecc71;
  }

  .in-progress{
    background-color: #f39c12;
  }

  @-webkit-keyframes blinker-bg {
    from { background-color: #f5b041; }
    to { background-color: inherit; }
  }
  
  @-moz-keyframes blinker-bg {
    from { background-color: #f5b041; }
    to { background-color: inherit; }
  }

  @-o-keyframes blinker-bg {
    from { background-color: #f5b041; }
    to { background-color: inherit; }
  }

  @keyframes blinker-bg {
    from { background-color: #f5b041; }
    to { background-color: inherit; }
  }

  .blink-bg{
    text-decoration: blink;
    -webkit-animation-name: blinker-bg;
    -webkit-animation-duration: 1s;
    -webkit-animation-iteration-count:infinite;
    -webkit-animation-timing-function:ease-in-out;
    -webkit-animation-direction: alternate;
  }
</style>
