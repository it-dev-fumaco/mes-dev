@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'production_schedule_assembly',
])

@section('content')
<div class="panel-header">
	<div class="header text-center" style="margin-top: -70px;">
		<div class="row">
			<div class="col-md-12">
				<table style="text-align: center; width: 60%;">
					<tr>
						<td style="width: 36%; border-right: 5px solid white;">
							<h2 class="title">
								<div class="pull-right" style="margin-right: 20px;">
									<span style="display: block; font-size: 15pt;">{{ date('M-d-Y') }}</span>
									<span style="display: block; font-size: 10pt;">{{ date('l') }}</span>
								</div>
							</h2>
						</td>
						<td style="width: 14%; border-right: 5px solid white;">
							<h2 class="title" style="margin: auto; font-size: 17pt;"><span id="current-time">--:--:-- --</span></h2>
						</td>
						<td style="width: 50%">
							<h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt; font-size: 19pt;">Production Scheduling - Assembly</h2>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="content" style="margin-top: -200px;">
  <div class="row">
    <div class="card" style="background-color:   #a6acaf;">
      <div class="col-md-4 offset-md-8" style="text-align: right; margin-bottom: -50px;">
        <a href="/production_schedule_calendar_assembly">
            <button type="button" class="btn btn-primary" id="btn-prod-sched">
              <i class="now-ui-icons ui-1_calendar-60" style="padding-right: 10px;"></i>Production Calendar
            </button>
        </a>
      </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist" style="display: non1e;  background-color:   #a6acaf ;font-weight: bold;border:none;">
          <li class="nav-item">
             <a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1" role="tab" aria-controls="step1" aria-selected="true">Production Schedule</a>
          </li>
          <li class="nav-item">
             <a class="nav-link" id="step2-tab" data-toggle="tab" href="#step2" role="tab" aria-controls="step2" aria-selected="false">Production Schedule per Workstation</a>
          </li>
        </ul>
        <div class="tab-content" style="min-height: 620px; background-color:   #a6acaf ;">
          <div class="tab-pane active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="scrolling outer">
                  <div class="inner" id="inner">
                    <table>
                      <tr>
                        <td class="th">
                          <div class="card" style="background-color:#e5e7e9;">
                            <div class="card-header" style="margin-top: -15px;">
                              <h5 class="card-title text-center" style="font-size: 17px;"><b>Unscheduled Prod. Order(s)</b></h5>
                            </div>
                            <div class="card-body sortable_list connectedSortable" id="unscheduled" style="height: 750px; position: relative; overflow-y: auto;">
                              @foreach($unscheduled as $i => $row)
                              <div class="card {{ $row['status'] }} {{ $row['drag']  }}" id="sched-{{ $row['name'] }}" data-duration="{{$row['cycle_in_seconds']}}" data-index="{{ $row['id'] }}" data-position="{{ $row['order_no'] }}" data-card="unscheduled" data-delivery="{{ $row['delivery_date'] }}" data-name="{{ $row['production_order'] }}" style="margin-top: -13px;">
                                <div class="card-body" style="font-size: 8pt; margin-top: -3px;">
                                  <table style="width: 100%; border:none;">
                                    <tr>
                                      <td colspan="4" style="font-size: 10pt;">
                                        <div class="form-check" style="font-size:10pt;margin-left:-20px;margin-top:-10pt;" id="form-check">
                                          <label class="customcontainer">
                                            <input class="selectbyall" type="checkbox" id="print-{{ $row['name'] }}" value="{{ $row['name'] }}" data-dateslct="" data-checkme="{{ $row['name'] }}">
                                            <span class="checkmark"></span>
                                          </label>
                                        </div>
                                        <span class="hvrlink" style="padding-left: 30px;"><a style="color: black;" href="#" class="prod_order_link_to_search" data-prod="{{ $row['name'] }}"><b>{{ $row['name'] }}</b> [{{ $row['status'] }}]</a></span>
                                           <div class="details-pane" style="font-size:8pt;">
                                              <h5 class="title">{{ $row['name'] }}</b> [{{ $row['status'] }}]</h5>
                                                <p class="desc">
                                                  <span style="font-size: 14px;font-weight: bold;"><i>{{ $row['sales_order'] }}</i></span><br>
                                                  <b>Item Description:</b><br>
                                                  <b>{{ $row['production_item'] }}</b>-{{ $row['description'] }}
                                                  <br>
                                                  <i>CTD Qty: <b>{{ $row['produced_qty'] }} {{ $row['stock_uom'] }}</b></i>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                    @if($row['customer'])
                                    <tr>
                                      <td colspan="2" width="100" style="padding-top: 5px;"><b>{{ $row['customer'] }}</b></td>
                                      <td colspan="2" style="text-align: right;">Delivery Date: {{ date('M-d-Y', strtotime($row['delivery_date'])) }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                      <td colspan="4" style="font-size: 13px;"><i>{{ $row['production_item'] }} - <span><b>{{  $row['strip'] }}</b></span></i></td>
                                      {{--<td colspan="2" style="text-align: right;">Qty: <span style="font-size: 9pt;">{{ number_format($row['qty']) }} {{ $row['stock_uom'] }}</span></td>--}}
                                    </tr>
                                    <tr>
                                      <td colspan="4" style="font-size: 13px;">Cycle Time:<span style="padding-left:3px;"><i>{{ $row['cycle_time'] }}</span></i></td>
                                    </tr>
                                    <tr>
                                      <td colspan="2">Qty: <span style="font-size: 9pt;">{{ number_format($row['produced_qty']) }} / {{ number_format($row['qty']) }} {{ $row['stock_uom'] }}</span></td>
                                      <td colspan="2"><span class="pull-right"><b>{{ $row['classification'] }}</b></span></td>
                                    </tr>
                                    <input type="hidden" value="{{$row['cycle_in_seconds']}}">
                                    
                                  </table>
                                      @php
                                if($row['process_stat'] == 'Ready'){
                                  $colorme="#58d68d";
                                  $margins="-17px";
                                }else if($row['process_stat'] == 'Not Started'){
                                  $colorme="black";
                                }else{
                                  $colorme="black";
                                }
                                @endphp
                                <div class="col-md-12 text-center" style="margin-top:-17px; padding-bottom: -10px;"><span  style="text-align: center;font-size:13px;color: {{ $colorme }};"><b>{{ $row['process_stat'] }}</b></span>
                                </div>
                                </div>
                              </div>
                              @endforeach
                            </div>
                          </div>
                        </td>
                      </tr>
                    </table>
                    <table>
                      <tr id="container">
                        @foreach($scheduled as $r)
                        <td class="td unique_container">
                        <div class="card" style="background-color:#e5e7e9;" id="id-{{ $r['schedule'] }}-id" data-id="{{ $r['schedule'] }}">
                              <div class="card-header" style="margin-top: -15px;">
                              <input type="hidden" id="divcount-{{ $r['schedule'] }}" style="margin-bott0m:20px;" value="{{$r['estimates_in_seconds']}}">
                                <h5 class="card-title text-center" style="font-size: 14pt;padding-left:40px;">
                                  <img src="{{ asset('img/calendar4.png') }}" width="20">
                                  <span class="goto_machine_kanban" style="cursor: pointer;" data-date="{{ date('Y-m-d', strtotime($r['schedule'])) }}">
                                    {{ date('D, M-d-Y', strtotime($r['schedule'])) }} <span style="font-size: 11pt;"><b>{{ (date('Y-m-d') == date('Y-m-d', strtotime($r['schedule']))) ? '[Today]' : '' }}</b></span>
                                  </span> 
                                    @if(date('Y-m-d', strtotime($r['schedule'])) >= date('Y-m-d'))
                                    <img src="{{ asset('img/scheduling.png') }}" width="25" class="goto_machine_kanban" style="cursor: pointer;" data-date="{{ date('Y-m-d', strtotime($r['schedule'])) }}">
                                      <img src="{{ asset('img/print.png') }}" width="25" class="printbtnprint" data-print="{{ $r['schedule'] }}">
                                    @else
                                    <img src="{{ asset('img/down.png') }}" width="25">
                                    <img src="{{ asset('img/print.png') }}" width="25" class="printbtnprint" data-print="{{ $r['schedule'] }}">

                                    @endif
                                    {{--<span style="display:block;"><span style="display:inline-block;font-size:14px;">Estimated Duration:</span><span id="duration-{{ $r['schedule'] }}" style="padding-left:3px;font-size:14px;display:inline-block;">{{$r['estimates']}}</span></span>--}}

                                </h5>
                                <br>
                                
                                <div class="form-check" style="font-size:10pt;margin-bottom:40px;margin-top:-65px;">
                                    <label class="customcontainer">
                                  <input type="checkbox" style="color:black;font-weight:bold;"  id="check-{{ $r['schedule'] }}" class="checkmeall" data-checkall="{{ $r['schedule'] }}">
                                  <span class="checkmark1"></span>
                                </label>
                                </div>
                                
                                <input type="hidden" id="tryme-{{ $r['schedule'] }}" class="printbox"></input>
                                
                                @forelse($r['shift'] as $i => $sched)
                                  <span class="text-center" style="font-size:8pt;display:block; margin-top: -75px;"><span style="display: {{($sched['shift_type'] == 'Special Shift') ? '' : 'none'}}">Shift - &nbsp;</span><span style="display: {{($sched['shift_type'] == 'Overtime Shift') ? '' : 'none'}}">Overtime - &nbsp;</span>{{ $sched['time_in'] }}&nbsp;- &nbsp;{{ $sched['time_out'] }}</span>
                                  @empty
                                  <span class="text-center" style="font-size:8pt;display:block; margin-top: -8px;">
                                  </span>
                                @endforelse
                                
                              </div>
                              <div class="card-body sortable_list connectedSortable" id="{{ $r['schedule'] }}" style="height: 750px; position: relative; overflow-y: auto;">
                                @foreach($r['orders'] as $i => $order)
                                 @php
                                if( $order['drag'] == 'not_move'){
                                  $divcolor="#EB984E";
                                  $statdiv ='not_move';
                                }else if($order['status'] == 'Completed'){
                                  $divcolor="#58d68d";
                                  $statdiv ='Completed';
                                }else{
                                  $divcolor="white";
                                  $statdiv ='ontime';
                                }
                                if($r['schedule'] > $order['delivery_date']){
                                  $divcolor="#e74c3c";
                                }

                                @endphp
                                <div class="card {{ $order['status'] }} {{ $order['drag'] }}" id="sched-{{ $order['production_order'] }}" data-statdiv="{{ $statdiv }}" data-index="{{ $order['id'] }}" data-position="{{ $order['order_no'] }}" data-card="{{ $r['schedule'] }}" data-name="{{ $order['production_order'] }}" data-duration="{{$order['cycle_in_seconds']}}" data-delivery="{{ $order['delivery_date'] }}" style="margin-top: -10px;background-color: {{$divcolor}};">

                                  <span class="production-order-class d-none">{{ $order['production_order'] }}</span>
                                  <span class="reference-class d-none">{{ $order['sales_order'] }}</span>
                                  <span class="item-code-class d-none">{{ $order['production_item'] }}</span>
                                  <span class="description-class d-none">{{ $order['description'] }}</span>
                                  <span class="delivery-date-class d-none">{{ $order['delivery_date'] }}</span>
                                  <span class="qty-to-manufacture-class d-none">{{ $order['qty'] }} {{ $order['stock_uom'] }}</span>
                                  
                                  <div class="card-body" style="font-size: 8pt;margin-top: -5px;">                
                                    <table style="width: 100%;">
                                      
                                      <tr>
                                        <td colspan="3" style="font-size:10pt;">
                                        <div class="form-check" style="font-size:10pt;margin-left:-20px;margin-top:-10pt;" id="form-check">

                                          <label class="customcontainer">
                                            <input class="selectbyall" type="checkbox" id="print-{{ $order['name'] }}" value="{{ $order['name'] }}" data-dateslct="{{ $r['schedule'] }}" data-checkme="{{ $order['name'] }}">
                                            <span class="checkmark"></span>
                                          </label>
                                        </div>
                                        
                                        
                                        <span class="hvrlink"><a href="#" class="prod_order_link_to_search" style="color: black;" data-prod="{{ $order['name'] }}"><b style="padding-left:30px;">{{ $order['name'] }}</b> [{{ $order['status'] }}]</a></span>
                                        <div class="details-pane" style="font-size:8pt;">
                                            <h5 class="title">{{ $order['name'] }}</b> [{{ $order['status'] }}]</h5>
                                                <p class="desc">
                                                  <span style="font-size: 14px;font-weight: bold;"><i>{{ $order['sales_order'] }}</i></span><br>
                                                  <b>Item Description:</b><br>
                                                  <b>{{ $order['production_item'] }}</b>-{{ $order['description'] }}
                                                  <br>
                                                  <i>CTD Qty: <b>{{ $order['produced_qty'] }} {{ $order['stock_uom'] }}</b></i>
                                                </p>
                                        </div> {!! $order['batch'] !!}
                                      </td>
                                        <td><span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $order['order_no'] }}</span></td>
                                      </tr>
                                      @if($order['customer'])
                                      <tr>
                                        <td colspan="2"><b>{{ $order['customer'] }}</b></td>
                                        <td colspan="2" style="text-align: right;"><span style="text-align: right;">Delivery Date:{{ date('M-d-Y', strtotime($order['delivery_date'])) }}</span></td>
                                      </tr>
                                      @endif
                                      <tr>
                                        <td colspan="4"  style="font-size: 13px;"><i>{{ $order['production_item'] }} - <span><b>{{ $order['strip'] }}</b></span></i></td>
                                        {{--<td colspan="2" style="text-align: right;">Qty: <span style="font-size: 9pt">{{ number_format($order['qty']) }} {{ $order['stock_uom'] }}</span></td>--}}
                                      </tr>
                                      <tr>
                                      <td colspan="4" style="font-size: 13px;">Cycle Time:<span style="padding-left:3px;"><i>{{ $order['cycle_time'] }}</span></i></td>
                                      </tr>
                                      <tr>
                                        <td colspan="2">Qty: <span style="font-size: 9pt">{{ number_format($order['produced_qty']) }} / {{ number_format($order['qty']) }} {{ $order['stock_uom'] }}</span></td>
                                        <td colspan="2"><span class="pull-right"><b>{{ $order['classification'] }}</b></span></td>
                                      </tr>
                                      
                                    
                                     <input type="hidden" value="{{$order['cycle_in_seconds']}}" class="counting{{ $r['schedule'] }}">
                                   
                                      
                                    </table>
                                @php
                                if($order['process_stat'] == 'Ready'){
                                  $colorme="#58d68d";
                                }else if($order['process_stat'] == 'Not Started'){
                                  $colorme="black";
                                }else{
                                  $colorme="black";
                                }
                                @endphp
                                <div class="col-md-12 text-center" style="margin-top:-17px; padding-bottom: -10px;"><span  style="text-align: center;font-size:13px;color: {{ $colorme }};"><b>{{ $order['process_stat'] }}</b></span>
                                </div>
                              </div>
                            </div>
                            @endforeach
                              </div>
                            </div>
                          </td>
                      @endforeach
                      </tr>
                    </table>
                  </div>
                </div>
				      </div>
				    </div>
          </div>
          <div class="tab-pane" id="step2" role="tabpanel" aria-labelledby="step2-tab">
            <form id="filter-frm">
              <div class="row" style="margin-top: 8px;">
                <div class="col-md-2">
                  <div class="form-group">
                    <select name="select_customer" id="sel-customer" class="form-control sel2">
                      <option value="">Select Customer</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <select name="select_reference" id="sel-reference" class="form-control sel2">
                      <option value="">Select Reference No</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <select name="select_parent_item" id="sel-parent-item" class="form-control sel2">
                      <option value="">Select Parent Item</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <select name="select_item" id="sel-item" class="form-control sel2">
                      <option value="">Select Item</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2" style="padding: 0; margin: 0;">
                  <button type="button" class="btn btn-primary" id="clear-filters-btn" style="padding: 8px 10px; margin: 0;">Clear Filters</button>
                </div>
              </div>
            </form>
            <div id="production-schedule-per-workstation-div"></div>
          </div>
        </div>
      </div>
    </div>
  </div>


<style type="text/css">
.details-pane {
display: none;
  color: #414141;
  background: #f1f1f1;
  border: 1px solid #a9a9a9;
  position: absolute;
  top: 20px;
  left: 0;
  z-index: 1;
  width: 300px;
  padding: 6px 8px;
  text-align: left;
  -webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  -moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
  white-space: normal;
}

.details-pane h5 {
  font-size: 1.5em;
  line-height: 1.1em;
  margin-bottom: 4px;
  line-height: 8px;
}

.details-pane h5 span {
  font-size: 0.75em;
  font-style: italic;
  color: #555;
  padding-left: 15px;
    line-height: 8px;

}

.details-pane .desc {
  font-size: 1.0em;
  margin-bottom: 6px;
    line-height: 16px;

}
/** hover styles **/
span.hvrlink:hover + .details-pane {
  display: block;
}
.details-pane:hover {
  display: block;
}
/**end **/
.scrolling table {
    table-layout: fixed;
    width: 100%;
  }

.customcontainer {
  display: block;
  position: relative;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

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



/* When the checkbox is checked, add a blue background */
.customcontainer input:checked ~ .checkmark {
  background-color: #2196F3;
}
/* When the checkbox is checked, add a blue background */
.customcontainer input:checked ~ .checkmark1 {
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

/* Show the checkmark when checked */
.customcontainer input:checked ~ .checkmark:after {
  display: block;
}
.customcontainer input:checked ~ .checkmark1:after {
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


  .scrolling .td, .th {
    vertical-align: top;
    padding: 10px;
    width: 450px;
  }

  .scrolling .th {
    position: absolute;
    left: 0;
    width: 450px;
  }

  .outer {
    position: relative
  }

  .inner {
    overflow-x: auto;
    overflow-y: visible;
    margin-left: 450px;
  }

  .perc {position:absolute; display:none; top: 0; line-height:20px; right:10px;color:black; font-weight:bold;}

  .container1 {
    position: relative;
    width: 100%;
    height: 20px;
    background-color: white;
    border-radius: 4px;
    margin: 10px auto;
  }

  .container1:after { position: absolute; top:0; right: 10px;line-height: 20px;}
  
  .fillmult {
    height: 100%;
    width: 0;
    background-color: #3498db;
    border-radius: 4px;
    line-height: 20px;
    text-align: left;
  }

  .fillmult span {
    padding-left: 10px;
    color: black;
  }

  .bordersample {
    border-style: solid;
    border-color: red;
  }

  .dot {
    height: 12px;
    width: 12px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }

  .custom-modal-calendar{
    max-width: 80% !important;
    min-height: 70% !important;
  }
</style>

@include('modals.select_late_delivery_reason_modal')
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<script>
  $(document).ready(function(){   
      
    get_customers(); 
    $('#clear-filters-btn').click(function(e){
      e.preventDefault();
      $('#filter-frm select').each(function() {
        $(this).empty();
      });

      get_customers();
      get_reference_nos();
      get_items('parent', null, null, null);
      get_items('sub-parent', null, null, null);
      get_items('child', null, null, null);

      production_schedule_per_workstation();
    });
    $('input[type="checkbox"]').each(function(){
  	$(this).prop('checked', false);
    });
    $(".printbox").val('');
    $('#filter-frm select').change(function(){
      var sel_value = $(this).val();
      if($(this).attr('name') == 'select_customer'){
        get_reference_nos(sel_value);
      }

      if($(this).attr('name') == 'select_reference'){
        get_items('parent', sel_value, null, null);
      }

      if($(this).attr('name') == 'select_parent_item'){
        get_items('child', $('#sel-reference').val(), $('#sel-parent-item').val(), null);
        get_items('sub-parent', $('#sel-reference').val(), $('#sel-parent-item').val(), $('#sel-sub-parent-item').val());
      }

      if($(this).attr('name') == 'select_sub_parent_item'){
        get_items('child', $('#sel-reference').val(), $('#sel-parent-item').val(), $('#sel-sub-parent-item').val());
      }
      
      production_schedule_per_workstation();
    });

    $('.sel2').select2({
      dropdownParent: $("#filter-frm"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false
    });

    production_schedule_per_workstation();
    function production_schedule_per_workstation(){
      $.ajax({
        url:"/production_schedule_per_workstation",
        type:"GET",
        data: $('#filter-frm').serialize(),
        success:function(data){
          $('#production-schedule-per-workstation-div').html(data);
        }
      }); 
    }

    function get_reference_nos(customer){
      $('#sel-reference').empty();
      var opt = '<option value="">Select Reference No</option>';
      if(customer){
        $.ajax({
          url:"/get_customer_reference_no/" + customer,
          type:"GET",
          success:function(data){
            $.each(data, function(i, d){
              opt += '<option value="' + d + '">' + d + '</option>';
            });
  
            $('#sel-reference').append(opt);
          }
        });

        return false;
      }
      $('#sel-reference').append(opt);
    }

    function get_customers(){
      $('#sel-customer').empty();
      var opt = '<option value="">Select Customer</option>';
      $.ajax({
        url:"/get_customers",
        type:"GET",
        success:function(data){
          $.each(data, function(i, d){
            opt += '<option value="' + d + '">' + d + '</option>';
          });

          $('#sel-customer').append(opt);
        }
      });
    }

    function get_items(item_type, reference, parent_item, sub_parent_item){
      var data = {
        item_type: item_type,
        parent_item: parent_item,
        sub_parent_item: sub_parent_item,
      }

      var sel = (item_type == 'parent') ? $('#sel-parent-item') : $('#sel-item'); 
      sel = (item_type == 'sub-parent') ? $('#sel-sub-parent-item') : sel; 
      var sel_text = (item_type == 'parent') ? 'Select Parent Item' : 'Select Item'; 
      sel_text = (item_type == 'sub-parent') ? 'Select Sub Parent Item' : sel_text; 

      sel.empty();
      var opt = '<option value="">' + sel_text + '</option>';
      if(reference){
        $.ajax({
          url:"/get_reference_production_items/" + reference,
          type:"GET",
          data: data,
          success:function(data){
            console.log(data);
            $.each(data, function(i, d){
              opt += '<option value="' + d + '">' + d + '</option>';
            });
  
            sel.append(opt);
          }
        });

        return false;
      }
      
      sel.append(opt);
    }

    $('#select-late-delivery-reason-modal form').submit(function(e){
      e.preventDefault();

      $.ajax({
        url:"/update_rescheduled_delivery_date",
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          console.log(data);
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#select-late-delivery-reason-modal').modal('hide');
          }
        },
        error : function(data) {
          console.log(data.responseText);
        }
      });
    });
    
    $('#inner').scrollLeft(450);
    $( ".sortable_list" ).sortable({
      connectWith: ".connectedSortable",
      appendTo: 'body',
      helper: 'clone',
      update:function(event, ui) {
        var card_id = this.id;
        
        $(this).children().each(function(index){
          if ($(this).attr('data-position') != (index + 1) || $(this).attr('data-card') != card_id) {
            $(this).attr('data-position', (index + 1)).attr('data-card', card_id).addClass('updated');
            $(this).find('.badge').text( (index + 1));
          }
        });
       
        
        // var sum = 0;
        // $(clssn).each(function(){
        //     sum += parseFloat(this.value);
        //     $(divcount).val(sum);
        // });

        var pos = [];
        $('.updated').each(function(){
          var name = $(this).attr('data-index');
          var position = $(this).attr('data-position');
          var prod = $(this).attr('data-name');
          pos.push([name, position, card_id, prod]);
          $(this).removeClass('updated');
        });

        if (pos) {
          $.ajax({
            url:"/reorder_production",
            type:"POST",
            dataType: "text",
            data: {
              positions: pos
            },
            success:function(data){
               // console.log(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
        }
      },
      receive: function(ev, ui) {

        var production_order = ui.item.find('.production-order-class').eq(0).text();
        var reference = ui.item.find('.reference-class').eq(0).text();
        var item_code = ui.item.find('.item-code-class').eq(0).text();
        var description = ui.item.find('.description-class').eq(0).text();
        var qty = ui.item.find('.qty-to-manufacture-class').eq(0).text();
        var delivery_date = ui.item.find('.delivery-date-class').eq(0).text();
        
        $('#custom-production-order').val(production_order);

        if(new Date($(this).attr('id')) > new Date(delivery_date)){
          $('#select-late-delivery-reason-modal .production-order').text(production_order);
          $('#select-late-delivery-reason-modal .reference').text(reference);
          $('#select-late-delivery-reason-modal .item-code').text(item_code);
          $('#select-late-delivery-reason-modal .item-description').text(description);
          $('#select-late-delivery-reason-modal .qty-uom').text(qty);
          $('#select-late-delivery-reason-modal .delivery-date').text(delivery_date);
          $('#select-late-delivery-reason-modal .rescheduled-date').text($(this).attr('id'));

          $('#select-late-delivery-reason-modal input[name="delivery_date"]').val(delivery_date);
          $('#select-late-delivery-reason-modal input[name="reschedule_date"]').val($(this).attr('id'));

          $('#select-late-delivery-reason-modal').modal('show');
        }

        var id_check = '#print-' + ui.item.data('name');
        var sched = $(this).attr('id');
        var delivery_date =  ui.item.data('delivery');
        var date = $(this).attr('id');
        var prod_id = "#sched-" +  ui.item.data('name');
        $(id_check).attr("data-dateslct", sched); //setter
        // alert("hi" + delivery_date);

        if(ui.item.hasClass("not_move") || ui.item.hasClass("Completed")){
          ui.sender.sortable("cancel");
        }else{
          if(ui.sender){

          var senderid = ui.sender.attr("id");
          var sender_div = '#divcount-'+ senderid;
          var id_input = ui.item.data('duration');
          var div_input = '#divcount-'+ sched;
          var div_input_val = $(div_input).val();
          var div_sender_val = $(sender_div).val();
          var sum =  parseInt(id_input, 10) +  parseInt(div_input_val, 10);
          var diff =  parseInt(div_sender_val, 10) - parseInt(id_input, 10);
          
          $(div_input).val(sum);
          $(sender_div).val(diff);
          show_duration(div_input,sched);
          show_duration(sender_div,senderid);
        }
        
        if($(this).attr('id') == "unscheduled"){
          $(prod_id).css('background-color', 'white');
        }else if(date > delivery_date){
          $(prod_id).css('background-color', '#e74c3c');
        }else{
          if(ui.item.data('statdiv') == "not_move"){
            $(prod_id).css('background-color', '#EB984E');
          }else if(ui.item.data('statdiv') == "not_move"){
            $(prod_id).css('background-color', '#58d68d');
          }else{
            $(prod_id).css('background-color', 'white');
          }
        }

        $.ajax({
          url:"/update_production_task_schedules",
          type:"POST",
          dataType: "text",
          data: {
            production_order: ui.item.data('name'),
            planned_start_date: $(this).attr('id'),
          },
          success:function(data){
              // console.log(data);
          },
          error : function(data) {
            console.log(data.responseText);
          }
        });

        }
          

          
      }
    }).disableSelection();

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    
    //$('.container1 > div').each(function(){
    //var width=$(this).data('width');
    //var length=$(this).data('length');
    //$(this).animate({ width: width }, 2500);
    //$(this).after( '<span class="perc">' + length + '</span>');
    //$('.perc').delay(3000).fadeIn(1000);
    //}); 

    setInterval(updateClock, 1000);
    function updateClock(){
      var currentTime = new Date();
      var currentHours = currentTime.getHours();
      var currentMinutes = currentTime.getMinutes();
      var currentSeconds = currentTime.getSeconds();
      // Pad the minutes and seconds with leading zeros, if required
      currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
      currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;
      // Choose either "AM" or "PM" as appropriate
      var timeOfDay = (currentHours < 12) ? "AM" : "PM";
      // Convert the hours component to 12-hour format if needed
      currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;
      // Convert an hours component of "0" to "12"
      currentHours = (currentHours === 0) ? 12 : currentHours;
      currentHours = (currentHours < 10 ? "0" : "") + currentHours;
      // Compose the string for display
      var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

      $("#current-time").html(currentTimeString);
    }

    $(document).on('click', '.goto_machine_kanban', function(){
      var date = $(this).data('date');
      window.location.href = "/production_schedule_monitoring_assembly/" + date;        
    });
  });
</script>
<script type="text/javascript">
        $(document).on('click', '.goto_machine_kanban', function(){
        var date = $(this).data('date');
         window.location.href = "/production_schedule_monitoring_assembly/" + date;        
      });
</script>
<script>
  $("input:checkbox").change(function() {
      var someObj = {};

      someObj.slectedbox = [];
      someObj.unslectedbox = [];
      name = $(this).data('dateslct');
      inputid = "#tryme-"+name;
      idme = '#'+name + " input:checkbox";
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
      if($(noCheckedbox).length === $(noCheckbox).length)
      {
          $(uncheckme).prop("checked",true);
      }
      else
      {
          $(uncheckme).prop("checked",false);
      }

      $(inputid).val(someObj.slectedbox);
  });
</script>
<script>
$('.checkmeall').change(function() {
  var someObj = {};
      someObj.slectedbox = [];
      someObj.unslectedbox = [];
      name = $(this).data('checkall');
      inputid = "#tryme-"+name;
      idme = '#'+name + " .selectbyall";
      idmee = '#'+name + " input:checkbox";
 $(idme).prop("checked", this.checked);
      $(idmee).each(function() {

      if ($(this).is(":checked")) {
          someObj.slectedbox.push($(this).attr("data-checkme"));
      } else {
          someObj.unslectedbox.push($(this).attr("data-checkme"));
      }
      });
$(inputid).val(someObj.slectedbox);
});
</script>

<script>
$(document).on('click', '.printbtnprint', function(){
        // var tryval = $('#tryme').val();
        var divname = $(this).data('print');
        var inputid = "#tryme-"+divname;
        var tryval = $(inputid).val();
        if(tryval == ''){
          showNotification("danger", "No selected Production Order", "now-ui-icons travel_info");

        }else{
          window.location.href = "/selected_print_job_tickets/" + tryval;        

        }
      });
</script>
<script>
$(document).on('click', '.prod_order_link_to_search', function(e){
    e.preventDefault();
    var prod = $(this).data('prod');
    $('#jt-workstations-modal .modal-title').text(prod);
    if(prod){
      getJtDetails($(this).data('prod'));
    }else{
      showNotification("danger", 'No Job Ticket found.', "now-ui-icons travel_info");
    }
  });
</script>
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
    function getJtDetails(jtno){
      $('#process-bc').empty();
      $('#jt-details-tbl tbody').empty();
      $.ajax({
        url:"/get_jt_details/" + jtno,
        type:"GET",
        success:function(data){
          $('#jt-details-col .produced-qty').text(data.totals.produced_qty);
          $('#jt-details-col .total-good').text(data.totals.total_good);
          $('#jt-details-col .total-reject').text(data.totals.total_reject);
          $('#jt-details-col .balance-qty').text(data.totals.balance_qty);

          if (data.item_details.sales_order) {
            $('#jt-details-col .ref-type').text('SO No.');
            $('#jt-details-col .ref-no').text(data.item_details.sales_order);
          }
  
          if (data.item_details.material_request) {
            $('#jt-details-col .ref-type').text('MREQ No.');
            $('#jt-details-col .ref-no').text(data.item_details.material_request);
          }
  
          $('#jt-details-col .prod-no').text(data.item_details.production_order);
          $('#jt-details-col .cust').text(data.item_details.customer);
          $('#jt-details-col .proj').text(data.item_details.project);
          $('#jt-details-col .qty').text(data.item_details.qty_to_manufacture);
          $('#jt-details-col .del-date').text(data.item_details.delivery_date);
          $('#jt-details-col .item-code').text(data.item_details.item_code);
          $('#jt-details-col .desc').text(data.item_details.description);
          $('#jt-details-col .sched-date').text(data.item_details.planned_start_date);
          $('#jt-details-col .task-status').text(data.item_details.status);
          if (data.item_details.status == 'Late') {
            $('#jt-details-col .task-status').removeClass('badge-info').addClass('badge-danger');
          }else{
            $('#jt-details-col .task-status').removeClass('badge-danger').addClass('badge-info');
          } 
     
          var r = '';
          $.each(data.operations, function(i, v){
            if(v.workstation == "Spotwelding"){
              var spotclass= "spotclass";
              var icon = '<span style="font-size:15px;">&nbsp; >></span>';
            }else{
              var spotclass= "";
              var icon="";
            }
            r += '<tr>' +
              '<td class="text-center" style="border: 1px solid #ABB2B9;" rowspan="' + v.count + '"><span class="'+ spotclass +'" data-jobticket="'+v.job_ticket +'" data-prodno="'+ v.production_order+'"><b>' + v.workstation + icon+'</b></span></td>';
            if (v.operations.length > 0) {
              $.each(v.operations, function(i, d){
                machine = (d.machine_code) ? d.machine_code : '-';
                operator_name = (d.operator_name) ? d.operator_name : '-';
                from_time = (d.from_time) ? d.from_time : '-';
                to_time = (d.to_time) ? d.to_time : '-';
                var inprogress_class = (d.status == 'In Progress') ? 'active-process' : '';
               
                if(v.process == "Housing and Frame Welding"){
                  qc_status = '';
                }else{
                   var qc_status = (d.qa_inspection_status == 'QC Passed') ? "qc_passed" : "qc_failed";
                    qc_status = (d.qa_inspection_status == 'Pending') ? '' : qc_status;
                }
                r += '<td class="text-center '+inprogress_class+' '+qc_status+'" style="border: 1px solid #ABB2B9;"><b>' + v.process + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.good) + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>' + Number(d.reject) + '</b></td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + machine + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + from_time + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + to_time + '</td>' +
                    '<td class="text-center '+inprogress_class+'" style="border: 1px solid #ABB2B9;">' + operator_name + '</td>' +
                    '</tr>';
              });
            }else{
              r += '<td class="text-center" style="border: 1px solid #ABB2B9;"><b>' + v.process + '</b></td>' +
                    '<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>' +
                    '<td class="text-center" style="font-size: 15pt; border: 1px solid #ABB2B9;"><b>0</b></td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '<td class="text-center" style="border: 1px solid #ABB2B9;">-</td>' +
                    '</tr>';
            }
          });

          var p = '';
          $.each(data.process, function(i, d){
            p += '<li class="'+ d.status +'">'+
                '<a href="javascript:void(0);">' + d.workstation + '</a>' +
                '</li>';
            });
  
          $('#process-bc').append(p);
          $('#jt-details-tbl tbody').append(r);
          $('#jt-workstations-modal').modal('show');
        }
      });
    }
</script>

<script>
function show_duration(div_input, sched){
          var div_val = $(div_input).val();
          var dur_hours = Math.floor(div_val / 3600);
          var dur_minutes = Math.floor((div_val / 60) % 60);
          var dur_seconds = Math.floor(div_val % 60);
          var div_label = "#duration-"+ sched;

          var dur_hours_label = (dur_hours > 0) ? dur_hours +'h' : "";
          var dur_minutes_label = (dur_minutes > 0) ? dur_minutes +'m' : "";
          var dur_seconds_label = (dur_seconds > 0) ? dur_seconds +'s' : "";

          $(div_label).text(dur_hours_label + ' '+ dur_minutes_label + ' ' + dur_seconds_label);
}</script>
@endsection