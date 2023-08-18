<div id="all-production-orders-table" style="font-size: 9pt;">
    <table class="table table-striped">
      <col style="width: 10%;">
      <col style="width: 35%;">
      <col style="width: 5%;">
      <col style="width: 5%;">
      <col style="width: 5%;">
      <col style="width: 12%;">
      <col style="width: 7%;">
      <col style="width: 10%;">
      <col style="width: 16%;">
      <thead class="text-primary" style="font-size: 7pt;">
        <th class="text-center"><b>Prod. No.</b></th>
        <th class="text-center"><b>Item Code</b></th>
        <th class="text-center"><b>Qty</b></th>
        <th class="text-center"><b>Completed</b></th>
        <th class="text-center"><b>Feedbacked</b></th>
        <th class="text-center"><b>Customer</b></th>
        <th class="text-center"><b>Delivery Date</b></th>
        <th class="text-center"><b>Target Warehouse</b></th>
        <th class="text-center"><b>Status</b></th>
        <th class="text-center"><b>Actions</b></th>
      </thead>
      <tbody id="tbody_checkprint">
        @forelse($production_order_list as $i => $r)
        <tr>
          <td class="text-center" style="padding: 3px;">
            @if ($r['production_order_status'] == 'Completed')
                <div class="form-check" style="font-size:8pt;margin-left:10px;display:{{($r['count_ste_entries'] == 0)? 'none':'' }}; vertical-align: baseline;" id="form-check">
                    <label class="customcontainer" style="margin-top:0px;">
                        <input class="print_slip" type="checkbox" id="print-{{ $r['production_order'] }}" value="{{ $r['production_order'] }}" data-dateslct="print-{{ $r['production_order'] }}" data-checkme="{{ $r['production_order'] }}">
                        <span class="checkmark2"></span>
                    </label>
                </div>
            @endif
            <a href="#" style="margin-left:20px;margin-top:50px;" data-jtno="{{ $r['production_order'] }}" class="prod-details-btn"><i class="now-ui-icons ui-1_zoom-bold"></i> {{ $r['production_order'] }}</a>
          </td>
          <td class="text-left" style="padding: 3px;"><b>{{ $r['item_code'] }}</b><br>{!! $r['description'] !!}<span class="d-block mt-2 font-italic" style="font-size: 8pt;"><b>Created by:</b> {{ $r['owner'] }} - {{ $r['created_at'] }}</span></td>
          <td class="text-center" style="padding: 3px;">
            <span style="font-size: 12pt; display: block; font-weight: bold;">{{ number_format($r['qty']) }}</span>
            <span>{{ $r['stock_uom'] }}</span>
          </td>
          <td class="text-center" style="padding: 3px;">
            <span style="font-size: 12pt; display: block; font-weight: bold;">{{ number_format($r['produced_qty']) }}</span>
            <span>{{ $r['stock_uom'] }}</span>
          </td>
          <td class="text-center" style="padding: 3px;">
            <span style="font-size: 12pt; display: block; font-weight: bold;">{{ number_format($r['feedback_qty']) }}</span>
            <span>{{ $r['stock_uom'] }}</span>
          </td>
          <td class="text-center" style="padding: 3px;">{{ $r['sales_order_no']}}{{ $r['material_request']}}<br>{{ $r['customer'] }}</td>
          <td class="text-center" style="padding: 3px;">@if($r['delivery_date']){{date('M-d-Y', strtotime($r['delivery_date']))}}@endif</td>
          <td class="text-center" style="padding: 3px;">{{ $r['target_warehouse'] }}</td>
          <td class="text-center" style="font-size: 10pt; padding: 3px;">
            @php
              $status_badge = '#000';
              switch ($r['status']) {
                case 'For Feedback':
                case 'Partially Feedbacked':
                case 'For Partial Feedback':
                  $status_badge = '#22D3CC';
                  break;
                case 'On Queue':
                  $status_badge = '#17A2B8';
                  break;
                case 'Feedbacked':
                  $status_badge = '#28A745';
                  break;
                case 'Cancelled':
                case 'Closed':
                case 'Unknown Status':
                  $status_badge = '#808495';
                  break;
                case 'Material Issued':
                  $status_badge = '#28A745';
                  break;
                case 'Material For Issue':
                  $status_badge = '#DC3545';
                  break;
                case 'In Progress':
                  $status_badge = '#FFC107';
                  break;
                default:
                  $status_badge = '#000';
                  break;
              }
            @endphp
            <span class="badge tab-heading--" style="background-color: {{ $status_badge }}; color: {{ $r['status'] == 'In Progress' ? '#000' : '#fff' }};">{{ $r['status'] }}</span>
            <br><br>
            @foreach($r['ste_entries'] as $entry)
            {{ $entry }}
            @endforeach</td>
     
          <td class="text-center p-1">
            <div class="btn-group dropleft">
              <button type="button" class="btn p-2 btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Action
              </button>
              <div class="dropdown-menu">
                @if(!in_array($r['status'], ['Cancelled', 'Feedbacked', 'Closed']))
                  @canany(['update-wip-production-order-process'])
                    <a class="dropdown-item view-bom-details-btn" href="#" data-bom="{{ $r['bom'] }}" data-production-order="{{ $r['production_order'] }}">Update Process</a>
                  @endcanany
                  @canany(['reschedule-delivery-date-production-order'])
                    <a class="dropdown-item resched-deli-btn" href="#" data-production-order="{{ $r['production_order'] }}">Reschedule Delivery Date</a>
                  @endcanany
                @endif
                @if($r['status'] == 'Feedbacked')
                  <a class="dropdown-item" href="#"><i class="now-ui-icons ui-1_check"></i> {{$r['ste_manufacture']}}</a>
                @else
                  @canany(['create-production-order-feedback', 'cancel-production-order-feedback'])
                    @if ($r['produced_qty'] > $r['feedback_qty'])
                        <a class="dropdown-item create-feedback-btn" href="#" data-production-order="{{ $r['production_order'] }}" data-completed-qty="{{ ($r['produced_qty']) - ($r['feedback_qty']) }}" data-target-warehouse="{{ $r['target_warehouse'] }}" data-operation="{{ $r['operation_id'] }}">Create Feedback</a>
                    @endif
                  @endcanany
                @endif
                @canany(['print-withdrawal-slip'])
                  @if(in_array($r['status'], ['Partially Feedbacked', 'Feedbacked']))
                    <a class="dropdown-item print-transfer-slip-btn" data-production-order="{{ $r['production_order'] }}" href="#">Print Transfer Slip</a>
                  @endif
                @endcanany
                <a class="dropdown-item create-ste-btn" href="#" data-production-order="{{ $r['production_order'] }}" data-item-code="{{ $r['item_code'] }}" data-qty="{{ number_format($r['qty']) }}" data-uom="{{ $r['stock_uom'] }}">View Materials</a>
                @if(!in_array($r['status'], ['Cancelled', 'Feedbacked']))
                  @canany(['cancel-production-order'])
                    <a class="dropdown-item cancel-production-btn" href="#"data-production-order="{{ $r['production_order'] }}">Cancel Production</a>
                  @endcanany
                  @if ($r['status'] == 'Closed')
                    @canany(['reopen-production-order'])
                      <a class="dropdown-item re-open-production-btn" href="#"data-production-order="{{ $r['production_order'] }}">Re-open Production</a>
                    @endcanany
                  @else
                    @canany(['close-production-order'])
                      <a class="dropdown-item close-production-btn" href="#"data-production-order="{{ $r['production_order'] }}">Close Production</a>
                    @endcanany
                  @endif
                @endif
                @canany(['override-production-order'])
                  @if(!in_array($r['status'], ['Cancelled', 'Feedbacked', 'Completed', 'Closed', 'Partially Feedbacked']))
                    <a class="dropdown-item override-production-btn" href="#" data-production-order="{{ $r['production_order'] }}">Override Production</a>
                  @endif
                @endcanany
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td class="text-center" colspan="13">No Record(s) found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

    <center>
      <div class="col-md-12 text-center custom-production-pagination" data-status="Completed" data-div="#production-orders-div">
       {{ $production_orders->links() }}
      </div>
    </center>

    <script>
  $('.print_slip:input:checkbox').change(function() {
    var someObj = {};

    someObj.slectedbox = [];
    someObj.unslectedbox = [];
    name = $(this).data('dateslct');
    inputid = "#prod_list_print";
    idme = '#tbody_checkprint '+ " input:checkbox";
    noCheckedbox = "#"+ name +" input:checkbox:checked";
    noCheckbox = "#"+ name +" input:checkbox";
    uncheckme= '#check-'+name;
    
    $(idme).each(function() {
      if ($(this).is(":checked")) {
        someObj.slectedbox.push($(this).attr("data-checkme"));
      } else {
        someObj.unslectedbox.push($(this).attr("data-checkme"));
      }
    });

    
    $(inputid).val(someObj.slectedbox);
    // alert(someObj.slectedbox);

  });

</script>

<style>
  /* Hide the browser's default checkbox */
  .customcontainer input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
  }

  /* Create a custom checkbox */
  .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    border-radius: 4px;
    background-color: #eee;
  }

  .checkmark1 {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    border-radius: 4px;
    background-color: white;
  }
  .checkmark2 {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    border-radius: 4px;
    background-color: #99a3a4;
  }

  /* When the checkbox is checked, add a blue background */
  .customcontainer input:checked ~ .checkmark {
    background-color: #2196F3;
  }

  /* When the checkbox is checked, add a blue background */
  .customcontainer input:checked ~ .checkmark1 {
    background-color: #2196F3;
  }

   /* When the checkbox is checked, add a blue background */
   .customcontainer input:checked ~ .checkmark2 {
    background-color: #2196F3;
  }


  /* Create the checkmark/indicator (hidden when not checked) */
  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
  }
  /* Create the checkmark/indicator (hidden when not checked) */
  .checkmark1:after {
    content: "";
    position: absolute;
    display: none;
  }
  /* Create the checkmark/indicator (hidden when not checked) */
  .checkmark2:after {
    content: "";
    position: absolute;
    display: none;
  }

  /* Show the checkmark when checked */
  .customcontainer input:checked ~ .checkmark:after {
    display: block;
  }
  .customcontainer input:checked ~ .checkmark1:after {
    display: block;
  }
  .customcontainer input:checked ~ .checkmark2:after {
    display: block;
  }
  /* Style the checkmark/indicator */
  .customcontainer .checkmark:after {
    left: 9px;
    top: 5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
  }

  .customcontainer .checkmark1:after {
    left: 9px;
    top: 5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
  }
  .customcontainer .checkmark2:after {
    left: 9px;
    top: 5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
  }
</style>
<script>
  $(document).ready(function(){
    $('#production-orders-total').html('{{ number_format($total_production_orders) }}');
  });
</script>