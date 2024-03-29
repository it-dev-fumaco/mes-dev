@extends('layouts.user_app', [
  'namePage' => 'Fabrication',
  'activePage' => 'production_schedule',
  'operation' => $operation_name_text,
  'pageHeader' => $operation_name_text . ' Scheduling',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
@include('modals.view_for_feedback_list_modal')
  <div class="row p-2" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="card" style="background-color: #a6acaf;">
      <div class="row m-0 p-0">
        <div class="col-4 p-0">
          <div class="d-flex flex-row p-0 m-0">
            <div>
              <ul class="nav nav-tabs mt-2" id="myTab" role="tablist" style="font-weight: bold;border:none;font-size:12px;">
                <li class="nav-item">
                  <a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1" role="tab" aria-controls="step1" aria-selected="true">Production Schedule</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="step3-tab" data-toggle="tab" href="#step3" role="tab" aria-controls="step3" aria-selected="false">Ready for Painting</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-7 offset-1 p-0">
          <input type="hidden" id="primary-operation-id" value="{{ $primary_id }}">
          <input type="hidden" id="reload_point_page">
          <button type="button" class="btn btn-success mr-1 mt-1 mb-0" id="ready-for-feedback-btn">
            Ready for Feedback <span class="badge badge-danger m-0" id="ready-for-feedback-badge" style="font-size: 9pt;">0</span>
          </button>
          @canany(['assign-production-order-schedule', 'reschedule-delivery-date-production-order', 'reschedule-delivery-date-order'])
            <button type="button" class="btn btn-primary mr-1 mt-1 mb-0" id="btn-prod-sched">
              <i class="now-ui-icons ui-1_calendar-60 mr-1"></i> Production Calendar
            </button>
          @endcanany
          <button type="button" class="btn text-center btn-prod-notif mr-1 mt-1 mb-0">
            <div class="containerbadge d-block" id="badge" style="padding-right:80px;margin-top:-10px;">
              <a class="entypo-bell"></a>
            </div>
            <div style="margin-top:-24px; padding-top:2px;padding-bottom:2px;margin-right:-30px;">
              <span style="display:inline-block; vertical-align: text-bottom;">Notifications</span>
            </div>
          </button>
          <a href="/production_schedule/{{$primary_id}}" class="mt-1 p-1" id="btn-prod-refresh" style="color:black;">
            <i class="now-ui-icons arrows-1_refresh-69 btn-refresh-page" style="font-size:25px;font-weight:bolder;color:black; vertical-align: middle;"></i>
          </a>
        </div>
        <div class="col-12 bg-white">
          <table class="w-50 m-0 p-0" id="filter-form">
            <col style="width: 40%;">
            <col style="width: 25%;">
            <col style="width: 25%;">
            <col style="width: 10%;">
            <tr>
              <td>
                <div class="form-group mb-0 mr-1">
                  <select class="form-control select-custom" id="customer-filter">
                    <option value="all">Select Customer</option>
                    @foreach ($filters['customers'] as $i => $customer)
                    <option value="{{ $customer }}">{{ $customer }}</option>
                    @endforeach
                  </select>
                </div>
              </td>
              <td>
                <div class="form-group mb-0 mr-1">
                  <select class="form-control select-custom" id="reference-filter">
                    <option value="all">Select Reference No.</option>
                    @foreach ($filters['reference_nos'] as $i => $reference)
                    <option value="{{ $reference }}">{{ $reference }}</option>
                    @endforeach
                  </select>
                </div>
              </td>
              <td>
                <div class="form-group mb-0 mr-1">
                  <select class="form-control select-custom" id="parent-item-filter">
                    <option value="all">Select Parent Item</option>
                    @foreach ($filters['parent_items'] as $i => $parent_item)
                    <option value="{{ $parent_item }}">{{ $parent_item }}</option>
                    @endforeach
                  </select>
                </div>
              </td>
              <td class="pl-2">
                <button class="btn btn-secondary btn-mini p-2 btn-block m-0" id="clear-kanban-filters">Clear</button>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="tab-content" style="min-height: 620px;">
        <div class="tab-pane bg-white" id="step3" role="tabpanel" aria-labelledby="step3-tab">
          <div class="row" style="margin-top: 10px;">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <div class="row">
                    {{-- <div class="col-md-4 offset-md-8">
                      <div class="form-group">
                        <input type="text" class="form-control" id="search-ready-for-feedback" placeholder="Search">
                      </div>
                    </div> --}}
                    <div class="col-md-12">
                      <div id="ready-for-painting-table"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane active kanban-filtering bg-white" id="step1" role="tabpanel" aria-labelledby="step1-tab">
          <div class="row p-0 m-0">
            <div class="col-md-12 p-0">
              <div class="scrolling outer">
                <div class="inner" id="inner">
                  <table>
                    <tr>
                      <td class="th">
                        <div class="card" style="background-color:#e5e7e9;">
                          <div class="card-header" style="margin-top: -15px;">
                            <h5 class="card-title text-center" style="font-size: 15px;"><b>Unscheduled Prod. Order(s)</b></h5>
                          </div>
                          <div class="card-body sortable_list connectedSortable pt-3 pl-2 pb-2 pr-2" id="unscheduled" style="height: 750px; position: relative; overflow-y: auto;">
                            @foreach($unscheduled as $i => $row)
                            @php
                            if( $row['status'] == 'Completed'){
                              $divcolor="#58d68d";
                            }else if($row['status'] == 'In Progress'){
                              $divcolor="#EB984E";
                            }else{
                              $divcolor="white";
                            }
                            @endphp
                            <div data-parent-item="{{ $row['parent_item_code'] }}" data-customer="{{ $row['customer'] }}" data-reference-no="{{ $row['sales_order'] }}" class="kanban-card card {{ $row['status'] }}" data-duration="" data-index="{{ $row['id'] }}" data-position="{{ $row['order_no'] }}" data-parentitemcode="{{ $row['parent_item_code'] }}" data-itemcode="{{ $row['item_code'] }}" data-delivery="{{ $row['delivery_date'] }}" data-card="unscheduled" data-name="{{ $row['production_order'] }}" style="margin-top: -13px;background-color: {{$divcolor}};">                              
                              <div class="card-body p-2" style="font-size: 8pt; margin-top: -3px;">                              
                                <table style="width: 100%;">
                                  <tr>
                                    <td class="align-middle" colspan="4" style="font-size: 10pt;">
                                      <div class="form-check d-inline-block" style="font-size: 10pt; margin-top:-10px;" id="form-check">
                                        <label class="customcontainer">
                                          <input class="selectbyall" type="checkbox" id="print-{{ $row['name'] }}" value="{{ $row['name'] }}" data-dateslct="" data-checkme="{{ $row['name'] }}">
                                          <span class="checkmark"></span>
                                        </label>
                                      </div>
                                      <span class="hvrlink">
                                        <a style="color: black;" href="#" class="prod_order_link_to_search" data-prod="{{ $row['name'] }}">
                                          <b>{{ $row['name'] }}</b><span style="font-size: 8pt;"> [{{ $row['status'] }}]</span>
                                        </a>
                                        @php 
                                        if($row['job_ticket_print'] == "1"  &&  $row['withdrawal_slip_print'] == "1"){
                                          $print_stat="JT/WS Printed";
                                        }else if($row['job_ticket_print'] == "1"){
                                          $print_stat="JT Printed";
                                        }else if($row['withdrawal_slip_print'] =="1"){
                                          $print_stat="WS Printed";
                                        }else{
                                          $print_stat="";
                                        }
                                        $print_stat="WS Printed";
                                        @endphp
                                      </span>
                                      <div class="details-pane" style="font-size:8pt;">
                                        <h5 class="title">{{ $row['name'] }}</b> [{{ $row['status'] }}]</h5>
                                        <p class="desc">
                                          <span style="font-size: 14px;font-weight: bold;"><i>{{ $row['sales_order'] }}</i></span><br>
                                          <b>Item Description:</b><br><b>{{ $row['production_item'] }}</b>-{{ $row['description'] }}<br>
                                          <i>CTD Qty: <b>{{ $row['produced_qty'] }} {{ $row['stock_uom'] }}</b></i>
                                        </p>
                                      </div>
                                    </td>
                                  </tr>
                                  @if($row['customer'])
                                  <tr>
                                    <td colspan="4"><b>{{ $row['customer'] }}</b></td>
                                  </tr>
                                  <tr>
                                    <td colspan="2" class="font-weight-bold">{{ $row['classification'] }}</td>
                                    <td colspan="2" style="font-size: 7pt;">Del. Date: {{ date('M-d-Y', strtotime($row['delivery_date'])) }}</td>
                                  </tr>
                                  @endif
                                  <tr>
                                    <td colspan="4" style="font-size: 12px;"><i>{{ $row['production_item'] }} - <span><b>{{  $row['strip'] }}</b></span></i></td>
                                  </tr>
                                  <tr>
                                    <td><span style="font-size: 7pt">{{ number_format($row['produced_qty']) }} / {{ number_format($row['qty']) }}</span></td>
                                    <td colspan="2">
                                      @php
                                    if($row['process_stat'] == 'Ready'){
                                      $colorme="#58d68d";
                                      $margins="-17px";
                                    }else if($row['process_stat'] == 'Not Started'){
                                      $colorme="black";
                                    }else{
                                      $colorme="black";
                                    }
                                    if($row['process_stat'] == "Material For Issue"){
                                      $stat_badge ="danger";
                                    }else if($row['process_stat'] == "Material Issued"){
                                      $stat_badge ="info";
                                    }else if($row['process_stat'] == "Ready For Feedback"){
                                      $stat_badge ="success";
                                    }else{
                                      $stat_badge ="warning";
                                    }
                                    @endphp
                                      <div class="text-center create-ste-btn"  data-production-order="{{ $row['name'] }}">
                                        <span class="badge badge-{{$stat_badge}} text-white" style="font-size:8pt;">
                                          <b>{{ $row['process_stat']}}</b>
                                        </span>
                                      </div>
                                    </td>
                                    <td class="text-center">
                                      <span style="font-size: 7pt;">{{ $print_stat }}</span>
                                    </td>
                                  </tr>
                                  @if ($row['approximate_cycle_time'] && $row['approximate_cycle_time'] != '')
                                  <tr>
                                    <td colspan="4" class="text-center" style="font-size: 7pt;">Approx. Duration: {{ $row['approximate_cycle_time'] }}</td>
                                  </tr>
                                  @endif
                                  <input type="hidden" value="">
                                </table>
                              </div>
                            </div>
                            @endforeach
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <table>
                    <tr id="containers">
                      @foreach($scheduled as $r)
                      @php
                        $shift = isset($shift_schedule[$r['schedule']]) ? $shift_schedule[$r['schedule']][0] : [];
                      @endphp
                      <td class="td unique_container">
                        <div class="card" style="background-color:#e5e7e9; height: 800px;" id="id-{{ $r['schedule'] }}-id" data-id="{{ $r['schedule'] }}" data-order-count="{{ count($r['orders']) }}">
                          <div class="card-header p-1">
                            <input type="hidden" id="divcount-{{ $r['schedule'] }}" value="">
                            <div class="d-flex flex-row justify-content-between align-items-middle m-0">
                              <div class="p-0 goto_machine_kanban" style="cursor: pointer;" data-date="{{ date('Y-m-d', strtotime($r['schedule'])) }}">
                                <div class="d-flex flex-row align-items-start m-0">
                                  <span class="p-0" style="float: left; font-size: 25pt; margin-top: -8px;">{{ date('d', strtotime($r['schedule'])) }}</span>
                                  <div class="ml-1">
                                    <span class="d-block" style="font-size: 10pt;">{{ date('l', strtotime($r['schedule'])) }} <b>{{ (date('Y-m-d') == date('Y-m-d', strtotime($r['schedule']))) ? '[Today]' : '' }}</b></span>
                                    <span class="d-block" style="font-size: 8pt;">{{ date('F', strtotime($r['schedule'])) }}</span>
                                    @if ($shift)
                                      <span class="d-block" style="font-size: 8pt;">
                                        {{ $shift->time_in.' - '.$shift->time_out }}
                                      </span>
                                    @endif
                                  </div>
                                </div>
                              </div>
                              <div class="p-0">
                                <div class="form-check d-inline-block" style="font-size: 10pt;">
                                  <label class="customcontainer">
                                    <input type="checkbox" id="check-{{ $r['schedule'] }}" class="checkmeall" data-checkall="{{ $r['schedule'] }}">
                                    <span class="checkmark1"></span>
                                  </label>
                                </div>
                                @if(date('Y-m-d', strtotime($r['schedule'])) >= date('Y-m-d'))
                                <div class="btn-group p-0 m-0" role="group">
                                  <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle pb-1 pt-1 pl-2 pr-2 m-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ asset('img/printpng.png') }}" width="20"> 
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item pb-1 pt-1 pl-2 pr-2 printbtnprint" href="#" data-print="{{ $r['schedule'] }}">Job Ticket</a>
                                    <a class="dropdown-item pb-1 pt-1 pl-2 pr-2 printbtnwidrawal" href="#" data-print="{{ $r['schedule'] }}">Withdrawal Slip</a>
                                  </div>
                                </div>
                                <img src="{{ asset('img/shift.png') }}" width="28" class="btnshift" data-print="{{ $r['schedule'] }}" data-date="{{ date('Y-m-d', strtotime($r['schedule'])) }}">
                                @else
                                <img src="{{ asset('img/down.png') }}" width="20">
                                <div class="btn-group p-0 m-0" role="group">
                                  <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle pb-1 pt-1 pl-2 pr-2 m-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ asset('img/printpng.png') }}" width="20"> 
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <a class="dropdown-item pb-1 pt-1 pl-2 pr-2 printbtnprint" href="#" data-print="{{ $r['schedule'] }}">Job Ticket</a>
                                    <a class="dropdown-item pb-1 pt-1 pl-2 pr-2 printbtnwidrawal" href="#" data-print="{{ $r['schedule'] }}">Withdrawal Slip</a>
                                  </div>
                                </div>
                                <img src="{{ asset('img/shift.png') }}" width="28" class="btnshift" data-print="{{ $r['schedule'] }}" data-date="{{ date('Y-m-d', strtotime($r['schedule'])) }}">
                                @endif
                                @if($r['duplicate_item_code'] < 0)
                                <i class="now-ui-icons ui-1_bell-53 bell show-merge-modal" style="font-size: 15pt;"></i>
                                @endif
                                <span style="display:none;">
                                  <span style="display:inline-block;font-size:14px;">Estimated Duration:</span>
                                  <span id="duration-{{ $r['schedule'] }}" style="padding-left:3px;font-size:14px;display:inline-block;"></span>
                                </span>
                                <input type="hidden" id="tryme-{{ $r['schedule'] }}" class="printbox"></input>
                              </div>
                            </div>
                          </div>
                          <div class="card-body sortable_list connectedSortable pt-3 pl-2 pb-2 pr-2" id="{{ $r['schedule'] }}" style="height: 700px; position: relative; overflow-y: auto; margin-top: -10px;">
                          @foreach($r['orders'] as $i => $order)
                            @php
                            if( $order['status'] == 'Completed'){
                              $divcolor="#58d68d";
                            }else if($order['status'] == 'In Progress'){
                              $divcolor="#EB984E";
                            }else{
                              $divcolor="white";
                            }
                            if($primary_id == "3"){
                              if($r['schedule'] > $order['delivery_date']){
                                  $divcolor="#e74c3c";
                                }
                            }
                            @endphp
                            <div data-parent-item="{{ $order['parent_item_code'] }}" data-customer="{{ $order['customer'] }}" data-reference-no="{{ $order['sales_order'] }}" class="kanban-card card {{ $order['status'] }}" data-index="{{ $order['id'] }}" data-position="{{ $order['order_no'] }}" data-card="{{ $r['schedule'] }}" data-name="{{ $order['production_order'] }}" data-delivery="{{ $order['delivery_date'] }}" data-parentitemcode="{{ $order['parent_item_code'] }}" data-itemcode="{{ $order['item_code'] }}" data-duration="" style="margin-top: -10px; background-color: {{$divcolor}};"> 
                              <div class="d-none">
                                <span class="production-order-class">{{ $order['production_order'] }}</span>
                                <span class="production-order-class">{{ $order['production_order'] }}</span>
                                <span class="reference-class">{{ $order['sales_order'] }}</span>
                                <span class="item-code-class">{{ $order['production_item'] }}</span>
                                <span class="description-class">{{ $order['description'] }}</span>
                                <span class="delivery-date-class">{{ $order['delivery_date'] }}</span>
                                <span class="qty-to-manufacture-class">{{ $order['qty'] }} {{ $order['stock_uom'] }}</span>
                              </div>                                 
                              <div class="card-body p-2" style="font-size: 8pt; margin-top: -5px;">
                                <table style="width: 100%;">
                                  <tr>
                                    <td class="align-middle" colspan="4" style="font-size:10pt;">
                                      <div class="form-check d-inline-block" style="font-size: 10pt; margin-top:-10px;" id="form-check">
                                        <label class="customcontainer">
                                          <input class="selectbyall" type="checkbox" id="print-{{ $order['name'] }}" value="{{ $order['name'] }}" data-dateslct="{{ $r['schedule'] }}" data-checkme="{{ $order['name'] }}">
                                          <span class="checkmark"></span>
                                        </label>
                                      </div>
                                      <span class="hvrlink">
                                        @php 
                                        if($order['job_ticket_print'] == "1"  &&  $order['withdrawal_slip_print'] == "1"){
                                          $print_stat="JT/WS Printed";
                                        }else if($order['job_ticket_print'] == "1"){
                                          $print_stat="JT Printed";
                                        }else if($order['withdrawal_slip_print'] =="1"){
                                          $print_stat="WS Printed";
                                        }else{
                                          $print_stat="";
                                        }
                                        @endphp
                                        <a href="#" class="prod_order_link_to_search text-dark" data-prod="{{ $order['name'] }}">
                                          <b>{{ $order['name'] }}</b>
                                          <span style="font-size: 8pt;"> [{{ $order['status'] }}]</span>
                                        </a>
                                      </span>
                                      <div class="details-pane" style="font-size:8pt;">
                                        <h5 class="title">{{ $order['name'] }}</b> [{{ $order['status'] }}]</h5>
                                        <p class="desc">
                                          <span style="font-size: 14px;font-weight: bold;"><i>{{ $order['sales_order'] }}</i></span><br>
                                          <b>Item Description:</b><br>
                                          <b>{{ $order['production_item'] }}</b>-{{ $order['description'] }}<br>
                                          <i>CTD Qty: <b>{{ $order['produced_qty'] }} {{ $order['stock_uom'] }}</b></i>
                                        </p>
                                      </div>
                                      <span class="pull-right badge badge-primary badgecount" style="font-size: 9pt;">{{ $order['order_no'] }}</span>
                                    </td>
                                  </tr>
                                  @if($order['customer'])
                                  <tr>
                                    <td colspan="4"><b>{{ $order['customer'] }}</b></td>
                                  </tr>
                                  <tr>
                                    <td colspan="2" class="font-weight-bold">{{ $order['classification'] }}</td>
                                    <td colspan="2" style="font-size: 7pt;">Del. Date: {{ date('M-d-Y', strtotime($order['delivery_date'])) }}</td>
                                  </tr>
                                  @endif
                                  <tr>
                                    <td colspan="4"  style="font-size: 12px;"><i>{{ $order['production_item'] }} - <span><b>{{ $order['strip'] }}</b></span></i></td>
                                  </tr>
                                  <tr>
                                    <td><span style="font-size: 7pt">{{ number_format($order['produced_qty']) }} / {{ number_format($order['qty']) }}</span></td>
                                    <td colspan="2">
                                      @php
                                      if($order['process_stat'] == 'Ready'){
                                        $colorme="#58d68d";
                                      }else if($order['process_stat'] == 'Not Started'){
                                        $colorme="black";
                                      }else{
                                        $colorme="black";
                                      }
                                      
                                      if($order['process_stat'] == "Material For Issue"){
                                        $stat_badge ="danger";
                                      }else if($order['process_stat'] == "Material Issued"){
                                        $stat_badge ="info";
                                      }else if($order['process_stat'] == "Cancelled"){
                                        $stat_badge ="danger";
                                      }else if($order['process_stat'] == "Ready For Feedback"){
                                        $stat_badge ="primary";
                                      }else if($order['process_stat'] == "Partial Feedbacked"){
                                        $stat_badge ="success";
                                      }else if($order['process_stat'] == "Feedbacked"){
                                        $stat_badge ="success";
                                      }else{
                                        $stat_badge ="warning";
                                      }
                                      @endphp
                                      <div class="text-center create-ste-btn"  data-production-order="{{ $order['name'] }}">
                                        <span class="badge badge-{{$stat_badge}} text-white" style="font-size:8pt;">
                                          <b>{{ $order['process_stat']}}</b>
                                        </span>
                                      </div>
                                    </td>
                                    <td class="text-center">
                                      <span style="font-size: 7pt;">{{ $print_stat }}</span>
                                    </td>
                                    @if ($order['approximate_cycle_time'] && $order['approximate_cycle_time'] != '')
                                    <tr>
                                      <td colspan="4" class="text-center" style="font-size: 7pt;">Approx. Duration: {{ $order['approximate_cycle_time'] }}</td>
                                    </tr>
                                    @endif
                                  </tr>
                                  <input type="hidden" value="" class="counting{{ $r['schedule'] }}">
                                </table>
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
      </div>
    </div>
  </div>

<div class="modal fade" id="view-notifications-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #0277BD;">
        <h5 class="modal-title" id="modal-title ">
          <i class="now-ui-icons ui-1_bell-53"></i> Production Order - Action/s Needed
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" id="notifmyTab" role="tablist" style="display: non1e;font-weight: bold;border:none;font-size:12px;">
          <li class="nav-item">
            <a class="nav-link active" id="prod-tab" data-toggle="tab" href="#prod-href" role="tab" aria-controls="prod" aria-selected="true">
              <span style="padding-left:20px;">Not Started Production Orders</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="inprog-tab" data-toggle="tab" href="#inprog" role="tab" aria-controls="inprog" aria-selected="false">Inactive In Progress</a>
          </li>
          <li class="nav-item"  id="tab_change_code">
            <a class="nav-link" id="change_code_tab" data-toggle="tab" href="#change_code_prod" role="tab" aria-controls="changecode" aria-selected="false">Change Code Alert</a>
          </li>
          <li class="nav-item"  id="tab_warning_fromshift">
            <a class="nav-link" id="warning_fromshift_tab" data-toggle="tab" href="#warning_fromshift" role="tab" aria-controls="warning_fromshift" aria-selected="false">Shift Warning</a>
          </li>
        </ul>
        <div class="tab-content" style="min-height: 620px;">
          <div class="tab-pane active" id="prod-href" role="tabpanel" aria-labelledby="prod-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <form id="notif_form">
                          @csrf
                          <div class="row">
                            <div class="col-md-3">
                              <div>
                                <h6 style="display:inline-block;">Sales Order:</h6>
                              </div>
                            </div>
                            <div class="col-md-3" style="display:inline;margin-left:-70px;">
                              <div>
                                <select style="display:inline;margin-left:-80px;" name="sales_order_notif" id="sel-sales-order-notif" class="sel-notif">
                                  <option value="">Select Sales Order</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div>
                                <h6 style="display:inline-block; margin-left:40px;">Customer:</h6>
                              </div>
                            </div>
                            <div class="col-md-3" style="display:inline-block; margin-left:-40px;">
                              <div>
                                <select name="customer_notif" id="sel-customer-notif" class="sel-notif">
                                  <option value="">Select Customer</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                      <div class="col-md-12">
                        <div class="table-full-width table-responsive" style="height: 600px; position: relative;" id="tbl-notifications"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="inprog" role="tabpanel" aria-labelledby="inprog-tab">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <form id="notif_form_inprogress">
                          @csrf
                          <div class="row">
                            <div class="col-md-3">
                              <div>
                                <h6 style="display:inline-block;">Sales Order:</h6>
                              </div>
                            </div>
                            <div class="col-md-3" style="display:inline;margin-left:-70px;">
                              <div>
                                <select style="display:inline;margin-left:-80px;" name="sales_order_notif" id="sel-sales-order-notif-inprogress" class="sel-notif inprogress-notif">
                                  <option value="">Select Sales Order</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div>
                                <h6 style="display:inline-block; margin-left:40px;">Customer:</h6>
                              </div>
                            </div>
                            <div class="col-md-3" style="display:inline-block; margin-left:-40px;">
                              <div>
                                <select name="customer_notif" id="sel-customer-notif-inprogress" class="sel-notif inprogress-notif">
                                  <option value="">Select Customer</option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                      <div class="col-md-12">
                        <div class="table-full-width table-responsive" style="height: 600px; position: relative;" id="tbl-notifications-inprogress"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="change_code_prod" role="tabpanel" aria-labelledby="change_code_tab">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="table-full-width table-responsive" style="height: 600px; position: relative;" id="tbl-notifications-change-code"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="warning_fromshift" role="tabpanel" aria-labelledby="change_code_tab">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="table-full-width table-responsive" style="height: 600px; position: relative;" id="tbl-warning-fromshift"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer p-2 pr-3">
        <button type="button" class="btn btn-secondary m-0" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="reschedmodal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <form action="/reschedule_production_from_notif" method="POST" id="reschedule-prod-form">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title" id="modal-title ">Reschedule Planned Start Date</h5>
        </div>
        <div class="modal-body">
          <label>Date:</label><br />
          <input type="date" class="form-control" name="start_time" id="start_time">
          <input type="hidden" class="form-control" name="prod_id" id="reched_prod_no">
          <input type="hidden" class="form-control" name="resched_operation_id" id="resched_operation_id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-primary" value="Save">
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="unschedmodal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <form action="/unschedule_production_from_notif" method="POST" id="unschedule-prod-form">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title" id="modal-title ">Unschedule Planned Start Date</h5>
        </div>
        <div class="modal-body">
          <label>Do you want to unschedule</label><span id="prod-label-unschedule" style="padding-left:5px;font-weight:bold;"></span>?
          <input type="hidden" class="form-control" name="prod_id" id="unsched_prod_no">
          <input type="hidden" class="form-control" name="unsched_operation_id" id="unsched_operation_id">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-primary" value="Save">
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="cancelprodmodal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <form action="/cancel_production_from_notif" method="POST" id="cancel-prod-form">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title" id="modal-title">Cancel Production order</h5>
        </div>
        <div class="modal-body">
          <label>Do you want to cancel</label><span id="prod-label-cancel" style="padding-left:5px;font-weight:bold;"></span>?
          <input type="hidden" class="form-control" name="prod_id" id="cancel_prod_no">
          <input type="hidden" class="form-control" name="tabselected" id="cancel-tabselected">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-primary" value="Save">
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="completeprodmodal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <form action="/complete_production_from_notif" method="POST" id="complete-prod-form">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title" id="modal-title">Complete Production order</h5>
        </div>
        <div class="modal-body">
          <label>Do you want to complete</label><span id="prod-label-complete" style="padding-left:5px;font-weight:bold;"></span>?
          <input type="hidden" class="form-control" name="prod_id" id="complete_prod_no">
          <input type="hidden" class="form-control" name="tabselected" id="complete-tabselected">
          <input type="hidden" class="form-control" name="complete_operation_id" id="complete_operation_id">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-primary" value="Save">
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="update-ste-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="#" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title">Update Stock Entry</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 offset-md-2 p-0">
              <div class="form-group text-center">
                <input type="text" id="stock-entry-id">
                <input type="text" id="production-order-id">
                <p style="font-size: 12pt;" class="text-center m-0">Enter Qty</p>
                <input type="text" value="0" class="form-control form-control-lg" name="qty" style="text-align: center;">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="modal fade bd-example-modal-lg" id="print_modal_js_ws" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="min-width:60%; width:60%;">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #ffffff;">
          <h5 class="modal-title">
          <img src="{{ asset('img/preview.png') }}" width="40">
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="printdiv" style="height:600px;overflow-y:auto;" >
          <div id="printmodalbody" style='page-break-after:always;'></div>
        </div>
        <div class="modal-footer" style="padding: 5px 10px;">
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          <button id="btnPrint" type="button" class="btn btn-primary">Print</button>
        </div>
      </div>
  </div>
</div>
<div class="modal fade" id="delete-shift-sched-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document" style="min-width:30%;">
     <form action="/delete_shift_schedule" method="POST" id="delete-shift-sched-frm">
        @csrf
        <div class="modal-content">
          <div class="modal-header text-white" style="background-color: #0277BD;">
              <h5 class="modal-title" id="modal-title ">
                 Delete Shift Schedule
              </h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true">×</span>
              </button>
           </div>
           <div class="modal-body" style="font-size:18px;">
              <div class="row">
                 <div class="col-md-12">
                       <input type="hidden" name="shift_sched_id" id="delete_shift_sched_id" class="delete_shift_sched_id">
                       <input type="hidden" name="delete_shift_date" id="delete_shift_date" class="delete_shift_date">
                       <div class="row" style="margin-top: -3%;">
                          <div class="col-sm-12" style="margin-top:10px;">
                             <span >Delete shift schedule permanently?</span>
                          </div>               
                       </div>
                 </div>

              </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding-top: -100px;">Cancel</button>
              &nbsp;
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </div>
     </form>
  </div>
</div>
<div class="modal fade" id="datetodayModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document" style="min-width: 69%;">
    <form action="/add_shift_schedule_prod" method="POST" id="add_shift_schedule_frm">
      @csrf
        <div class="modal-content">
          <div class="modal-header text-white align-top" style="background-color: #0277BD;">
            <img src="{{ asset('img/calendar4.png') }}" width="30" class="align-middle" style="display: inline-block; margin-right:5px;">
            <h5 class="modal-title" style="display: inline;">&nbsp;Shift Schedule</h5>
            <button type="button" class="close btn-close-click-validator" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
           <div class="modal-body">
             <div class="row">
               <div class="col-md-8" style="padding: 1px 2px 1px 5px;">
                 <div class="row">
                   <div class="col-md-12">
                    <div class="card">
                      <div class="card-header"  style="padding: 1px 1px 1px 1px;">
                       <h5 class="card-title" style="font-size:13pt;">&nbsp;&nbsp;<b>Shift Schedule</b></h5>
                      </div>
                      <input type="hidden" class="selected_prod_order" id="selected_prod_order">
                      <div class="card-body">
                        <input type="hidden" id='tbl_opration_id' name="operation_id">
                       <input type="hidden" id="date_tobe_sched_shift" name="date">
                       <input type="hidden" id="date_reload_tbl" name="date_reload_tbl">
                       <input type="hidden" name="pagename" value="prod_sched" id="pagename">
                       <div id="default_shift_sched" style="margin-top: -10px;"></div>
                       <div id="old_ids"></div>
                         <table class="table table-bordered" style="margin-top: 5px;">
                           <col style="width: 50%;">
                           <col style="width: 20%;">
                           <col style="width: 20%;">
                           <col style="width: 10%;">
                           <tr>
                             <th class="text-center">Shift</th>
                             <th class="text-center">Time-in</th>
                             <th class="text-center">Time-out</th>
                             <th></th>
                           </tr>
                           <tbody id="shiftsched-table">
                           </tbody>
                         </table>
                       <div class="pull-left">
                         <button type="button" class="btn btn-info btn-sm" id="add-row-shift-btn">
                           <i class="now-ui-icons ui-1_simple-add"></i> Add Row
                         </button>
                       </div>
                      </div>
                    </div>
                   </div>
                   <div class="col-md-12">
                    <div class="card" style="min-height: 150px;">
                      <div class="card-header"  style="padding: 1px 1px 1px 1px;">
                       <h5 class="card-title" style="font-size:13pt;">&nbsp;&nbsp;<b>Moved Planned Start Date</b></h5>
                      </div>
                      <div class="card-body">
                        <div class="form-group" style="margin-top:-10px;">
                          <label> <i><b>Note:</b> Select production order from the list to reschedule. </i></label>
                        </div>
                        <div class="form-group">
                          <label for="planned_start_datepicker" style="font-size: 12pt; color: black; display: inline-block; margin-right: 1%;"><b>Date:</b></label>
                          <input type="date" class="form-control" name="planned_start_datepicker" id="planned_start_datepicker" style="display: inline-block; width: 80%; font-weight: bolder;">
                       </div>
                      </div>
                    </div>
                  </div>
                 </div>
               </div>
               <div class="col-md-4"  style="padding: 1px 2px 1px 5px;">
                <div class="card">
                  <div class="card-header" style="padding: 1px 1px 1px 1px;">
                   <h5 class="card-title" style="font-size:13pt; margin-left:3px;">&nbsp;<b>Scheduled Production Order</b></h5>
                  </div>
                  <div class="card-body" style="height:400px;overflow:auto;">
                    <div>
                      <div id="prod_list_calendar" style="margin-top:-5px;font-size:10px;" ></div>
                    </div>
                  </div>
                </div>
               </div>
             </div>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary" id="fabrication_update">Submit</button>
            </div>
        </div>
      </form>
  </div>
</div>
<div class="modal fade" id="reschedule-deli-modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" role="document" style="min-width:40%;">
    <form action="/calendar_update_rescheduled_delivery_date" id="calendar_update_rescheduled_delivery_date_form" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header  text-white" style="background-color: #0277BD;" >
          <h5 class="modal-title">Reschedule Delivery Date</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12" id="tbl_container_details">
            
            </div>
          </div>
        </div>
        <input type="hidden" class="tbl_reload_deli_modal" name="reload_tbl" value="reloadpage">
        <div class="modal-footer" style="padding: 5px 10px;">
          <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="selectShiftModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0277BD">
        <h5 class="modal-title" style="color: #fff;">Select Schedule - <span id="shift-selected-date">Date</span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/save_shift_schedule" id="shift_schedule_form" method="post">
        <div class="modal-body">
          <div class="d-none">
            <input type="text" name="schedule_date" id="schedule_date" value="">
            <input type="text" name="operation_id" value="{{ $operation_id }}">
            <input type="text" name="production_order" id="shift-production-order" value="">
          </div>
          <div class="container p-0">
            <div class="card">
              <div class="card-body text-center" style="border-left: 10px solid #0277BD">
                <span style="font-size: 8pt;"><i class="now-ui-icons travel_info"></i>&nbsp;Please select production schedule for this date</span>
              </div>
            </div>
          </div>
          <select name="selected_shift" class="form-control" id="shift-selection" required>
            <option selected value="">Select Shift</option>
            @foreach ($shifts as $shift)
              <option value="{{ $shift->shift_id }}">
                {{ $shift->shift_type.' - '.$shift->time_in.' - '.$shift->time_out }}
              </option>
            @endforeach
          </select>
          <br>
          <label style="font-size: 9pt">Note</label>
          <textarea name="remarks" id="shift-remarks" rows="5" class="form-control" placeholder="Remarks..."></textarea>
        </div>
        <div class="modal-footer">
          <button type="submit" id="save-shift-btn" class="btn btn-primary">Save Shift Schedule</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div id="loader-wrapper" hidden>
  <div id="loader"></div>
  <div class="loader-section section-left"></div>
  <div class="loader-section section-right"></div>
</div>

@include('modals.modal_select_item_merge')
@include('modals.select_late_delivery_reason_modal')

<style type="text/css">
  #loader-wrapper {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 99999;
	}
	#loader {
		display: block;
		position: relative;
		left: 50%;
		top: 50%;
		width: 150px;
		height: 150px;
		margin: -75px 0 0 -75px;
		border-radius: 50%;
		border: 3px solid transparent;
		border-top-color: #3498db;
		-webkit-animation: spin 2s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
		animation: spin 2s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
		z-index: 999999;
	}
	#loader:before {
		content: "";
		position: absolute;
		top: 5px;
		left: 5px;
		right: 5px;
		bottom: 5px;
		border-radius: 50%;
		border: 3px solid transparent;
		border-top-color: #e74c3c;
		-webkit-animation: spin 3s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
		animation: spin 3s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
	}
	#loader:after {
		content: "";
		position: absolute;
		top: 15px;
		left: 15px;
		right: 15px;
		bottom: 15px;
		border-radius: 50%;
		border: 3px solid transparent;
		border-top-color: #f9c922;
		-webkit-animation: spin 1.5s linear infinite; /* Chrome, Opera 15+, Safari 5+ */
		animation: spin 1.5s linear infinite; /* Chrome, Firefox 16+, IE 10+, Opera */
	}
	@-webkit-keyframes spin {
		0%   { 
			-webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
			-ms-transform: rotate(0deg);  /* IE 9 */
			transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
		}
		100% {
			-webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */
			-ms-transform: rotate(360deg);  /* IE 9 */
			transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
		}
	}
	@keyframes spin {
		0%   { 
			-webkit-transform: rotate(0deg);  /* Chrome, Opera 15+, Safari 3.1+ */
			-ms-transform: rotate(0deg);  /* IE 9 */
			transform: rotate(0deg);  /* Firefox 16+, IE 10+, Opera */
		}
		100% {
			-webkit-transform: rotate(360deg);  /* Chrome, Opera 15+, Safari 3.1+ */
			-ms-transform: rotate(360deg);  /* IE 9 */
			transform: rotate(360deg);  /* Firefox 16+, IE 10+, Opera */
		}
	}
	#loader-wrapper .loader-section {
		position: fixed;
		top: 0;
		width: 100%;
		height: 100%;
		background-color:  #e5e7e9 ;
		z-index: 1000;
		opacity: 50%;
		-webkit-transform: translateX(0);  /* Chrome, Opera 15+, Safari 3.1+ */
		-ms-transform: translateX(0);  /* IE 9 */
		transform: translateX(0);  /* Firefox 16+, IE 10+, Opera */
	}

 #shift-schedule-modal .form-control{
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;  
  }
  .shift-sched-class .form-control {
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  .shift-sched-class .form-control:hover, .shift-sched-class .form-control:focus, .shift-sched-class .form-control:active {
    box-shadow: none;
  }
  .shift-sched-class .form-control:focus {
    border: 1px solid #34495e;
  }

  .bell{
    font-size: 40px;
    width: 30px;
    height: 20px;
    color: red;
    -webkit-animation: ring 4s .7s ease-in-out infinite;
    -webkit-transform-origin: 50% 4px;
    -moz-animation: ring 4s .7s ease-in-out infinite;
    -moz-transform-origin: 50% 4px;
    animation: ring 4s .7s ease-in-out infinite;
    transform-origin: 50% 4px;
  }

  @-webkit-keyframes ring {
    0% { -webkit-transform: rotateZ(0); }
    1% { -webkit-transform: rotateZ(30deg); }
    3% { -webkit-transform: rotateZ(-28deg); }
    5% { -webkit-transform: rotateZ(34deg); }
    7% { -webkit-transform: rotateZ(-32deg); }
    9% { -webkit-transform: rotateZ(30deg); }
    11% { -webkit-transform: rotateZ(-28deg); }
    13% { -webkit-transform: rotateZ(26deg); }
    15% { -webkit-transform: rotateZ(-24deg); }
    17% { -webkit-transform: rotateZ(22deg); }
    19% { -webkit-transform: rotateZ(-20deg); }
    21% { -webkit-transform: rotateZ(18deg); }
    23% { -webkit-transform: rotateZ(-16deg); }
    25% { -webkit-transform: rotateZ(14deg); }
    27% { -webkit-transform: rotateZ(-12deg); }
    29% { -webkit-transform: rotateZ(10deg); }
    31% { -webkit-transform: rotateZ(-8deg); }
    33% { -webkit-transform: rotateZ(6deg); }
    35% { -webkit-transform: rotateZ(-4deg); }
    37% { -webkit-transform: rotateZ(2deg); }
    39% { -webkit-transform: rotateZ(-1deg); }
    41% { -webkit-transform: rotateZ(1deg); }
  
    43% { -webkit-transform: rotateZ(0); }
    100% { -webkit-transform: rotateZ(0); }
  }
  
  @-moz-keyframes ring {
    0% { -moz-transform: rotate(0); }
    1% { -moz-transform: rotate(30deg); }
    3% { -moz-transform: rotate(-28deg); }
    5% { -moz-transform: rotate(34deg); }
    7% { -moz-transform: rotate(-32deg); }
    9% { -moz-transform: rotate(30deg); }
    11% { -moz-transform: rotate(-28deg); }
    13% { -moz-transform: rotate(26deg); }
    15% { -moz-transform: rotate(-24deg); }
    17% { -moz-transform: rotate(22deg); }
    19% { -moz-transform: rotate(-20deg); }
    21% { -moz-transform: rotate(18deg); }
    23% { -moz-transform: rotate(-16deg); }
    25% { -moz-transform: rotate(14deg); }
    27% { -moz-transform: rotate(-12deg); }
    29% { -moz-transform: rotate(10deg); }
    31% { -moz-transform: rotate(-8deg); }
    33% { -moz-transform: rotate(6deg); }
    35% { -moz-transform: rotate(-4deg); }
    37% { -moz-transform: rotate(2deg); }
    39% { -moz-transform: rotate(-1deg); }
    41% { -moz-transform: rotate(1deg); }
  
    43% { -moz-transform: rotate(0); }
    100% { -moz-transform: rotate(0); }
  }
  
  @keyframes ring {
    0% { transform: rotate(0); }
    1% { transform: rotate(30deg); }
    3% { transform: rotate(-28deg); }
    5% { transform: rotate(34deg); }
    7% { transform: rotate(-32deg); }
    9% { transform: rotate(30deg); }
    11% { transform: rotate(-28deg); }
    13% { transform: rotate(26deg); }
    15% { transform: rotate(-24deg); }
    17% { transform: rotate(22deg); }
    19% { transform: rotate(-20deg); }
    21% { transform: rotate(18deg); }
    23% { transform: rotate(-16deg); }
    25% { transform: rotate(14deg); }
    27% { transform: rotate(-12deg); }
    29% { transform: rotate(10deg); }
    31% { transform: rotate(-8deg); }
    33% { transform: rotate(6deg); }
    35% { transform: rotate(-4deg); }
    37% { transform: rotate(2deg); }
    39% { transform: rotate(-1deg); }
    41% { transform: rotate(1deg); }
  
    43% { transform: rotate(0); }
    100% { transform: rotate(0); }
  }

  .sel-notif{
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }

  .badge-style{
    display: block;
    word-wrap:break-word;
    width: 165px;
    margin: 0 auto;
    white-space: normal;
    text-align: center;
  }

  .details-pane {
    display: none;
    color: #414141;
    background: #f1f1f1;
    border: 1px solid #a9a9a9;
    position: absolute;
    top: 20px;
    left: 0;
    z-index: 1;
    width: 330px;
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
  .details-pane-plan {
    display: none;
    color: #414141;
    background: #f1f1f1;
    border: 1px solid #a9a9a9;
    position: absolute;
    top: 0px;
    left: 0px;
    right: 0;
    margin: 0 auto;
    left: 0;
    z-index: 10;
    width: 230px;
    padding: 6px 8px;
    text-align: left;
    -webkit-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
    -moz-box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
    box-shadow: 1px 3px 3px rgba(0,0,0,0.4);
    white-space: normal;
  }

  .details-pane-plan h5 {
    font-size: 1.5em;
    line-height: 1.1em;
    margin-bottom: 4px;
    line-height: 8px;
  }

  .details-pane-plan h5 span {
    font-size: 0.75em;
    font-style: italic;
    color: #555;
    padding-left: 15px;
    line-height: 8px;
  }

  .details-pane-plan .desc {
    font-size: 1.0em;
    margin-bottom: 6px;
    line-height: 16px;
  }
  /** hover styles **/
  span.hvrlink-plan:hover + .details-pane-plan {
    display: block;

  }

  .details-pane-plan:hover {
    display: block;

  }

  /**end **/
  .scrolling table {
    table-layout: fixed;
    width: 100%;
  }

  .customcontainer {
    /* display: block; */
    /* position: relative; */
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
    height: 20px;
    width: 20px;
    border-radius: 2px;
    background-color: #eee;
  }

  .checkmark1 {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    border-radius: 4px;
    background-color: white;
  }
  .checkmark2 {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
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
    left: 8px;
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
    left: 8px;
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
  .scrolling .td, .th {
    vertical-align: top;
    padding: 5px;
    width: 320px;
  }

  .scrolling .th {
    position: absolute;
    left: 0;
    width: 320px;
  }

  .outer {
    position: relative
  }

  .inner {
    overflow-x: auto;
    overflow-y: visible;
    margin-left: 320px;
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

  .containerbadge {
    -webkit-perspective: 1000;
    -webkit-backface-visibility: hidden;
  }

  .badge-num {
    margin-right:-15px;
    box-sizing: border-box;
    font-family: "Trebuchet MS", sans-serif;
    background: #ff0000;
    cursor: default;
    border-radius: 50%;
    color: #fff;
    font-weight: bold;
    font-size: 0.6vw;
    height: 2rem;
    letter-spacing: -0.1rem;
    line-height: 1.55;
    margin-left: 0.1rem;
    margin-right: 0rem;
    border: 0.2rem solid #fff;
    text-align: center;
    display: inline-block;
    width: 2rem;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    animation: pulse 1.5s  linear infinite;
  }

  .badge-num:after {
    content: "";
    position: absolute;
    top: -0.1rem;
    left: -0.1rem;
    border: 2px solid rgba(255, 0, 0, 0.5);
    opacity: 0;
    border-radius: 50%;
    width: 100%;
    height: 100%;
    animation: sonar 1.5s  linear infinite;
  }
  .badge-num1 {
    margin-right:-15px;
    box-sizing: border-box;
    font-family: "Trebuchet MS", sans-serif;
    background: #ff0000;
    cursor: default;
    border-radius: 50%;
    color: #fff;
    font-weight: bold;
    font-size: 0.6vw;
    height: 2rem;
    letter-spacing: -0.1rem;
    line-height: 1.55;
    margin-left: 0.1rem;
    margin-right: 0rem;
    border: 0.2rem solid #fff;
    text-align: center;
    display: inline-block;
    width: 2rem;
    box-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
  }

  .badge-num1:after {
    content: "";
    position: absolute;
    top: -0.1rem;
    left: -0.1rem;
    border: 2px solid rgba(255, 0, 0, 0.5);
    opacity: 0;
    border-radius: 50%;


  }
  #datetodayModal .form-control{
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;  
  }
  #reschedule-deli-modal .form-control{
    border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;  
  }
  .text_shorter {
    display: block;
  width:auto;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  font-size: 8px;
}

  @keyframes sonar {
    0% {
      transform: scale(0.9);
      opacity: 1;
    }
    100% {
      transform: scale(2);
      opacity: 0;
    }
  }
  @keyframes pulse {
    0% {
      transform: scale(1);
    }
    20% {
      transform: scale(1.4);
    }
    50% {
      transform: scale(0.9);
    }
    80% {
      transform: scale(1.2);
    }
    100% {
      transform: scale(1);
    }
  }
</style>

<style>
  .tab-heading--blue {
    background-color: #2196E3;
    color: #FFF;
  }
  .tab-heading--orange {
    background-color: #EA9034;
    color: #FFF;
  }
  .tab-heading--reddish {
    background-color: #E86B46;
    color: #FFF;
  }
  .tab-heading--teal {
    background-color: #22D3CC;
    color: #FFF;
  }
  .tab-heading--green {
    background-color: #8BC753;
    color: #FFF;
  }
  .tab-heading--gray {
    background-color: #808495;
    color: #FFF;
  }
  .tab-heading--ltgray {
    background-color: #F3F3F3;
    color: #242424;
  }
  @-webkit-keyframes blinker_change_code {
      from { background-color: #f39c12; }
      to { background-color: inherit; }
    }
    @-moz-keyframes blinker_change_code {
      from { background-color: #f39c12; }
      to { background-color: inherit; }
    }
    @-o-keyframes blinker_change_code {
      from { background-color: #f39c12; }
      to { background-color: inherit; }
    }
    @keyframes blinker_change_code {
      from { background-color: #f39c12; }
      to { background-color: inherit; }
    }
  .blink_changecode{
      text-decoration: blink;
      -webkit-animation-name: blinker_change_code;
      -webkit-animation-duration: 1s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }
   .noshift_blink {
    color: red;
    animation: blinker 3s linear infinite;
  }

  @keyframes blinker {  
    50% { opacity: 0; }
  }
</style>


<!-- Modal Confirm Feedback Production Order -->
<div class="modal fade" id="confirm-feedback-production-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 55%;">
    <form action="#" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #0277BD;">
          <h5 class="modal-title">Production Order Feedback</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="production_order">
          <div class="row">
            <div class="col-md-12">
              <div id="feedback-production-items"></div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
  .kanban-filtering .kanban-card.active-1 {
    opacity: 1;
    display: block;
    -webkit-animation: fadeIn 0.65s ease forwards;
    animation: fadeIn 0.65s ease forwards;
  }
  
  @-webkit-keyframes fadeIn {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
  @keyframes fadeIn {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }

  .kanban-filtering .kanban-card {
    opacity: 0;
    display: none;
  }
  
  .ui-autocomplete {
    position: absolute;
    z-index: 2150000000 !important;
    cursor: default;
    border: 2px solid #ccc;
    padding: 5px 0;
    border-radius: 2px;
  }
</style>

@endsection

@section('script')

<link rel="stylesheet" href="{{ asset('css/jquery-ui-1-12.css') }}">
<script src="{{ asset('js/jquery-ui-1.12.js') }}"></script>


<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<script type="text/javascript" src="{{  asset('js/printThis.js') }}"></script>

<script>
  $(document).ready(function(){
    $('#close-production-modal form').submit(function(e){
      e.preventDefault();
      $.ajax({
        url: '/close_production_order',
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (!data.success) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            reload_notif_table_inprogress();
            reload_notif_table();
            $('#close-production-modal').modal('hide');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });
  
    $(document).on('click', '#add-operation-btn', function(){
    var workstation = $('#sel-workstation option:selected').text();
    var wprocess = $('#sel-process').val();
    if (!$('#sel-workstation').val()) {
      showNotification("info", 'Please select Workstation', "now-ui-icons travel_info");
      return false;
    }
    var rowno = $('#bom-workstations-tbl tr').length;
    var sel = '<div class="form-group" style="margin: 0;"><select class="form-control form-control-lg">' + $('#sel-process').html() + '</select></div>';
    if (workstation) {
      var markup = "<tr><td class='text-center'>" + rowno + "</td><td>" + workstation + "</td><td>" + sel + "</td><td class='td-actions text-center'><button type='button' class='btn btn-danger delete-row'><i class='now-ui-icons ui-1_simple-remove'></i></button></td></tr>";
      $("#bom-workstations-tbl tbody").append(markup);
    }
  });
  $(document).on("click", ".delete-row", function(e){
         e.preventDefault();
         $(this).parents("tr").remove();
      });
    $(document).on('click', '#submit-bom-review-btn', function(){
    var production_order = $('#production-order-val-bom').val();
    var operation_id = $('#operation_id_update_bom').val();
    var id = [];
    var workstation = [];
    var wprocess = [];
    var workstation_process = [];
    var bom = $('#bom-workstations-tbl input[name=bom_id]').val();
    var user = $('#bom-workstations-tbl input[name=username]').val();
    // var operation = $('#bom-workstations-tbl input[name=operation]').val();
    $("#bom-workstations-tbl > tbody > tr").each(function () {
      id.push($(this).find('span').eq(0).text());
      workstation.push($(this).find('td').eq(1).text());
      wprocess.push($(this).find('select').eq(0).val());
      workstation_process.push($(this).find('select option:selected').eq(0).text());
    });
    var filtered_process = wprocess.filter(function (el) {
      return el != null && el != "";
    });
    if (workstation.length != filtered_process.length) {
      showNotification("danger", 'Please select process', "now-ui-icons travel_info");
      return false;
    }
    var processArr = workstation_process.sort();
    var processDup = [];
    for (var i = 0; i < processArr.length - 1; i++) {
        if (processArr[i + 1] == processArr[i]) {
            processDup.push(processArr[i]);
            showNotification("danger", 'Process <b>' + processArr[i] + '</b> already exist.', "now-ui-icons travel_info");
            return false;
        }
    }
    $.ajax({
      url: '/submit_bom_review/' + bom,
      type:"POST",
      data: {user: user, id: id, workstation: workstation, wprocess: wprocess, production_order: production_order, operation:operation_id},
      success:function(data){
        if(data.status) {
          $('#review-bom-modal').modal('hide');
          showNotification("success", data.message, "now-ui-icons ui-1_check");
        } else {
          showNotification("danger", data.message, "now-ui-icons travel_info");
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  });
  
    $('#reload_point_page').val('');
    $('.select-custom').select2({
      dropdownParent: $("#filter-form"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false
    });

     // start by showing all items
     $('.kanban-filtering .kanban-card').addClass('active-1');
	  
     function filter_cards(fltr1, fltr2, fltr3) {
         
       // reset results list
       $('.kanban-filtering .kanban-card').removeClass('active-1');
       
       // the filtering in action for all criteria
       var selector = ".kanban-filtering .kanban-card";
       if (fltr1 !== 'all') {
            selector = selector + '[data-customer="' + fltr1 + '"]';
       }
 
       if (fltr2 !== 'all') {
         selector =  selector + '[data-reference-no="' + fltr2 + '"]';
       }
 
       if (fltr3 !== 'all') {
         selector =  selector + '[data-parent-item="' + fltr3 + '"]';
       }
       // show all results
       $(selector).addClass('active-1');
   }
 
    $('#filter-form select').change(function() {
      filter_cards($('#customer-filter').val(), $('#reference-filter').val(), $('#parent-item-filter').val());
    });
 
  $('#clear-kanban-filters').click(function(e){
    e.preventDefault();
  
    $('#customer-filter').val('all').trigger('change');
    $('#reference-filter').val('all').trigger('change');
    $('#parent-item-filter').val('all').trigger('change');
  
    filter_cards($('#customer-filter').val(), $('#reference-filter').val(), $('#parent-item-filter').val());
  });

    function get_pending_material_transfer_for_manufacture(production_order){
      $.ajax({
        url:"/get_pending_material_transfer_for_manufacture/" + production_order,
        type:"GET",
        success:function(response){
          $('#feedback-production-items').html(response);
          
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    }

    $('#confirm-feedback-production-modal').on('hide.bs.modal', function (e) {
      get_for_feedback_production(1);
    });
    
    $(document).on('click', '.show-merge-modal', function(e){
      e.preventDefault();

      var loading = '<i class="now-ui-icons loader_refresh spin"></i> Loading.. Please wait.';

      $('#select-production-merge-table').html(loading);

      

      $.ajax({
        url:"/get_scheduled_production_duplicate/2020-09-22",
        type:"GET",
        success:function(data){
          $('#select-production-merge-table').html(data);

          $('#select-production-merge-modal').modal('show');
        }
      });

      
    });
    $('#reschedule_delivery_frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if(data.success == 3){
            showNotification("danger", data.message, "now-ui-icons travel_info");
            $('#reschedule-delivery-modal').modal('hide');
          }else if (data.success == 0){
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#reschedule-delivery-modal').modal('hide');
            if(data.reload_tbl == "reloadpage"){ 
              setTimeout(function() {
                  location.reload();
              }, 3000);
            }else{
              get_for_feedback_production(1);
            }
          }
        }
      });
    });
    
    $('#ready-for-feedback-btn').click(function(e){
      e.preventDefault();
      get_for_feedback_production(1);
      $('#reschedule-delivery-modal .tbl_reload_deli_modal').val("reload_ajax");
      $('#view-for-feedback-list-modal').modal('show');
    });
    
    function get_for_feedback_production(page, query){
      $.ajax({
        url: "/production_order_list/Awaiting%20Feedback/?page=" + page,
        type:"GET",
        data: {search_string: query},
        success:function(data){
            $('#view-for-feedback-list-table').html(data);
        }
      });
    }

    $(document).on('click', '.custom-production-pagination a', function(event){
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      get_for_feedback_production(page);
    });

    $(document).on('keyup', '.search-feedback-prod', function(){
      var query = $(this).val();
      get_for_feedback_production(1, query);
    });

    count_current_production();
    function count_current_production(){
      $.ajax({
          url:"/count_current_production_order/{{ date('Y-m-d') }}",
          type:"GET",
          success:function(data){
          $('#ready-for-feedback-badge').text(data.completed);
          }
        }); 
    }
    painting_ready_list();
    function painting_ready_list(){
      $.ajax({
        url:"/painting_ready_list",
        type:"GET",
        data: {q: $('#search-ready-for-feedback').val()},
        success:function(data){
          $('#ready-for-painting-table').html(data);
        }
      });
    }
    
    $(document).on('keyup', '#search-ready-for-feedback', function(e){
      e.preventDefault();
      painting_ready_list();
    });
    
    //get_customers();
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
    $("#prod_list_print").val('');


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

    $('.sel-notif').select2({
      dropdownParent: $("#view-notifications-modal"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false
    });
    
    // production_schedule_per_workstation();
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
        $.ajax({url:"/get_customer_reference_no/" + customer,
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

    $('#shift_schedule_form').submit(function(e){
      e.preventDefault();

      $.ajax({
        url:"/save_shift_schedule",
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#schedule_date').val('');
            $('#shift-production-order').val('');
            $('#shift-selection').val('').trigger('change');
            $('#shift-remarks').val('');
            location.reload();
          }
        },
        error : function(data) {
          console.log(data.responseText);
        }
      });
    });

    var update_sched = 0;

    $('#inner').scrollLeft(320);
    $( ".sortable_list" ).sortable({
      connectWith: ".connectedSortable",
      appendTo: 'body',
      helper: 'clone',
      update:function(event, ui) {
        if($("#id-" + event.target.id + '-id').length !== 0) {
          if($("#id-" + event.target.id + '-id').data('order-count') < 1){
            $('#schedule_date').val(event.target.id);
            $('#shift-production-order').val(ui.item.data('name'));
            
            const monthNames = [
              "Jan. ", "Feb. ", "Mar. ", "Apr. ", "May. ", "Jun. ",
              "Jul. ", "Aug. ", "Sep. ", "Oct. ", "Nov. ", "Dec. "
            ];
            var formattedDate = new Date(event.target.id);
            var d = formattedDate.getDate();
            var m = formattedDate.getMonth();
            var y = formattedDate.getFullYear();

            $('#shift-selected-date').text(monthNames[m] + d + ", " + y);
            $('#selectShiftModal').modal('show');
            return false;
          }
        }
        var primary_operation_id = $('#primary-operation-id').val();
        var parent_id = ui.item.attr("data-parentitemcode");
        var item_code_id = ui.item.attr("data-itemcode");
        var delivery_id = ui.item.attr("data-delivery");
        var drop_to = $(ui.item[0]).parent().attr('id');
        var drop_from = $(ui.item[1]).parent().attr('id');
        var card_id = this.id;
        $(this).children().each(function(index){
          if ($(this).attr('data-position') != (index + 1) || $(this).attr('data-card') != card_id) {
            $(this).attr('data-position', (index + 1)).attr('data-card', card_id).addClass('updated');
            $(this).find('.badgecount').text( (index + 1));
          }
        });
        var pos = [];
        $('.updated').each(function(){
          var name = $(this).attr('data-index');
          var position = $(this).attr('data-position');
          var prod = $(this).attr('data-name');
          pos.push([name, position, card_id, prod]);
          // console.log(pos);
          $(this).removeClass('updated');
        });
        if(primary_operation_id == 3){//FOR ASSEMBLY VALIDATE IF THE ITEM CODE IS EQUAL TO PARENT ITEM CODE, IF THE TARGET (BOX) TO DROP IS UNSCHEDULED, IF THE DELIVERY DATE IS GREATER THAN THE TARGET BOX TO DROP
          if(parent_id != item_code_id || drop_to == "unscheduled" || drop_to <= delivery_id){
            if (pos) {
              $.ajax({
                url:"/reorder_production/"+ primary_operation_id,
                  type:"POST",
                  data: {
                    positions: pos
                  },
                  success:function(data){
                    if(data.success < 1){
                    }else{
                    }
                  },
                  error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                  }
              });
            }
          }
          $('#reschedule_delivery_frm').submit(function(e){ //FUNCTION ONCE THE SUBMIT BUTTON IS CLICK
            e.preventDefault();
            if (pos) {
              $.ajax({
                url:"/reorder_production/"+ primary_operation_id,// UPDATE PLANNED START DATE
                  type:"POST",
                  data: {
                    positions: pos
                  },
                  success:function(data){
                    if(data.success < 1){
                    }else{
                    }
                  },
                  error: function(jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(errorThrown);
                  }
              });
            }
          });
 
        }else{ //UPDATE PLANNED START DATE IF NOT OPERATION IS NOT ASSEMBLY
          if (pos) {
            $.ajax({
              url:"/reorder_production/"+ primary_operation_id,
              type:"POST",
              data: {
                positions: pos
              },
              error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
              }
            });
          }
        }
      },
      receive: function(ev, ui) {
        if($("#id-" + ev.target.id + '-id').length !== 0) {
          if($("#id-" + ev.target.id + '-id').data('order-count') < 1){
            return false;
          }
        }
        var primary_operation_id = $('#primary-operation-id').val();
        var primary_operation_id = $('#primary-operation-id').val();
        var data_delivery_date =  ui.item.data('delivery');
        var prod = ui.item.data('name');
        var parent_id = ui.item.attr("data-parentitemcode");
        var item_code_id = ui.item.attr("data-itemcode");
        var drop_to = $(ui.item[0]).parent().attr('id');
        if(primary_operation_id =="3"){
          var production_order = ui.item.find('.production-order-class').eq(0).text();
          var reference = ui.item.find('.reference-class').eq(0).text();
          var item_code = ui.item.find('.item-code-class').eq(0).text();
          var description = ui.item.find('.description-class').eq(0).text();
          var qty = ui.item.find('.qty-to-manufacture-class').eq(0).text();
          var delivery_date = ui.item.find('.delivery-date-class').eq(0).text();
          var parent_id = ui.item.attr("data-parentitemcode");
          var item_code_id = ui.item.attr("data-itemcode");
          var dragndrop = "reloadpage";
          $('#custom-production-order').val(production_order);
          if(parent_id == item_code_id){
            if(new Date($(this).attr('id')) > new Date(data_delivery_date)){
              $.ajax({
                url: "/reschedule_prod_details/" + prod,
                type:"GET",
                success:function(data){
                    $('#tbl_reschduled_deli').html(data);
                    $('#reschedule-delivery-modal').modal('show');
                    $('#reschedule-delivery-modal .close').hide();
                    $('#reschedule-delivery-modal .tbl_reload_deli_modal').val(dragndrop);
                  },
                  error: function(jqXHR, textStatus, errorThrown) {
                      console.log(jqXHR);
                      console.log(textStatus);
                      console.log(errorThrown);
                  },
                });
              }
            }
          $(document).on('click', '#reschedule-delivery-modal .btn-close', function(){
            ui.sender.sortable("cancel");
          });
        }
        var id_check = '#print-' + ui.item.data('name');
        var sched = $(this).attr('id');
        var date = $(this).attr('id');
        var prod_id = "#sched-" +  ui.item.data('name');
        $(id_check).attr("data-dateslct", sched); //setter
          $.ajax({
            url:"/drag_n_drop/"+ prod,
            type:"get",
            success:function(data){
              if(data.success < 1){
                ui.sender.sortable("cancel");
                showNotification("danger", "Unable to rechedule planned start date.       <br><b>Production Order has on-going Process.</b>", "now-ui-icons travel_info");
              }else{
                if(primary_operation_id == "3"){
                  if($(this).attr('id') == "unscheduled"){
                    $(prod_id).css('background-color', 'white');
                  }else if(date > data_delivery_date){
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
                if(parent_id != item_code_id || drop_to == "unscheduled" || drop_to <= data_delivery_date){
                    $.ajax({
                      url:"/update_production_task_schedules",
                      type:"POST",
                      data: {
                        production_order: ui.item.data('name'),
                        planned_start_date: ev.target.id,
                        current: ui.sender.attr('id')
                      },
                      error : function(data) {
                        console.log(data.responseText);
                      }
                    });
                  }
                  $('#reschedule_delivery_frm').submit(function(e){ //FUNCTION ONCE THE SUBMIT BUTTON IS CLICK
                    $.ajax({
                      url:"/update_production_task_schedules",
                      type:"POST",
                      data: {
                        production_order: ui.item.data('name'),
                        planned_start_date: ev.target.id,
                        current: ui.sender.attr('id')
                      },
                      error : function(data) {
                        console.log(data.responseText);
                      }
                    });
                  });
                }
                if(primary_operation_id < 3){
                  $.ajax({
                    url:"/update_production_task_schedules",
                    type:"POST",
                    dataType: "text",
                    data: {
                      production_order: ui.item.data('name'),
                      planned_start_date: ev.target.id,
                      current: ui.sender.attr('id')
                    },
                   
                    error : function(data) {
                      console.log(data.responseText);
                    }
                  });
                }
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
          var fromt = '#'+ ui.sender.attr('id');
          var to = '#'+sched;
          var datas = ['#'+ ui.sender.attr('id'),'#'+sched ];
          $(datas).each(function(index, value){
            var card_id = value;
            $(value).children().each(function(index, value){
              if ($(this).attr('data-position') != (index + 1) || $(this).attr('data-card') != card_id) {
                $(this).attr('data-position', (index + 1)).attr('data-card', card_id).addClass('updated');
                $(this).find('.badgecount').text( (index + 1));
              }     
            });
            var pos = [];
            $('.updated').each(function(){
              var name = $(this).attr('data-index');
              var position = $(this).attr('data-position');
              var prod = $(this).attr('data-name');
              pos.push([name, position, card_id, prod]);
              $(this).removeClass('updated');
            });
          });   
      }
    }).disableSelection();

    $('#selectShiftModal').on('hide.bs.modal', function (e) {
      $( ".sortable_list" ).sortable('cancel');
      showNotification("danger", "Unable to schedule planned start date.<br><b>Please select shift schedule first.</b>", "now-ui-icons travel_info");
    });
    
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 

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
  });
</script>

<script type="text/javascript">
  $(document).on('click', '.goto_machine_kanban', function(){
    var operation_id =$('#primary-operation-id').val();
    var date = $(this).data('date');
   
      window.location.href = "/production_schedule_monitoring/" + operation_id + "/" + date;
   
          
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
    
    if($(noCheckedbox).length === $(noCheckbox).length){
      $(uncheckme).prop("checked",true);
    } else {
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
    $('#loader-wrapper').removeAttr('hidden');
    
    $.ajax({
    url: "/selected_print_job_tickets/" + tryval,
    type:"GET",
    success:function(data){
      if (data.success < 1) {
      showNotification("danger", data.message, "now-ui-icons travel_info");
      }else{
      $('#printmodalbody').html(data);
      $('#print_modal_js_ws').modal('show');
      $('#loader-wrapper').attr('hidden', true);
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
    console.log(jqXHR);
    console.log(textStatus);
    console.log(errorThrown);
    }
    });
  }
});
$(document).on('click', '.printbtnwidrawal', function(){
  // var tryval = $('#tryme').val();
  var divname = $(this).data('print');
  var inputid = "#tryme-"+divname;
  var tryval = $(inputid).val();
  if(tryval == ''){
    showNotification("danger", "No selected Production Order", "now-ui-icons travel_info");
  }else{
    $('#loader-wrapper').removeAttr('hidden');
    $.ajax({
    url: "/print_withdrawals",
    type:"GET",
    data: {production_orders: tryval},
    success:function(data){
      if (data.success < 1) {
        showNotification("danger", data.message, "now-ui-icons travel_info");// show alert message
      }else{
      $('#printmodalbody').html(data);
      $('#print_modal_js_ws').modal('show');
      $('#loader-wrapper').attr('hidden', true);
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
    console.log(jqXHR);
    console.log(textStatus);
    console.log(errorThrown);
    }
    });
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
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            $('#production-search-content').html(data);
            $('#jt-workstations-modal').modal('show');
          }
          
        }
      });
    }
</script>



<script>
$(document).ready(function(){  

});

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
}
</script>

<script>
updateBadge();
function updateBadge() {
var badgeNum;
var badge = document.getElementById("badge");
var operation_id = $('#primary-operation-id').val();

$.ajax({
url:"/production_notif_fab_count/"+ operation_id,
type:"GET",
success:function(data){
var badgeChild = badge.children[0];
if (badgeChild.className === "badge-num")
badge.removeChild(badge.children[0]);

if(data == "0"){
  badgeNum = document.createElement("div");
  badgeNum.setAttribute("class", "badge-num1");
  badgeNum.innerText = data;
  var insertedElement = badge.insertBefore(badgeNum, badge.firstChild);
}else{
  badgeNum = document.createElement("div");
  badgeNum.setAttribute("class", "badge-num");
  badgeNum.innerText = data;
  var insertedElement = badge.insertBefore(badgeNum, badge.firstChild);

}
},
error : function(data) {
console.log(data.responseText);
}
});

}
</script>
<script>
$(document).on('click', '.btn-prod-notif', function(e){
  e.preventDefault();
  var operation_id= $('#primary-operation-id').val();
  get_notif_filters();
  reload_notif_table_inprogress();
  reload_tbl_change_code();
  $.ajax({
    url:"/get_all_prod_notif/"+ operation_id,
    type:"GET",
    success:function(data){
      $('#tbl-notifications').html(data);
      $('#view-notifications-modal').modal('show');
    },
    error : function(data) {
      console.log(data.responseText);
    }
  });
  warning_notif_for_custom_shift();
});
$(document).on('click', '.reschedule-prod-btn', function(e){
var prod = $(this).data('prod');
var operation_id= $('#primary-operation-id').val();
$('#resched_operation_id').val(operation_id);       
$('#reched_prod_no').val(prod);       
$('#reschedmodal').modal('show');


});

$(document).on('click', '.cancel-prod-btn', function(e){
var prod = $(this).data('prod');
var tabselect = $(this).data('tabselected');
$('#cancel_prod_no').val(prod);   
$('#cancel-tabselected').val(tabselect);  
$('#prod-label-cancel').text(prod);  
$('#cancelprodmodal').modal('show');

});
$(document).on('click', '.complete-prod-btn', function(e){
var prod = $(this).data('prod');
var tabselect = $(this).data('tabselected');
var operation_id= $('#primary-operation-id').val();
$('#complete_operation_id').val(operation_id); 
$('#complete_prod_no').val(prod);    
$('#prod-label-complete').text(prod);  
$('#completeprodmodal').modal('show');

});
$(document).on('click', '.unschedule-prod-btn', function(e){
var prod = $(this).data('prod');
var operation_id= $('#primary-operation-id').val();
$('#unsched_operation_id').val(operation_id);       
$('#unsched_prod_no').val(prod);      
$('#prod-label-unschedule').text(prod);   
$('#unschedmodal').modal('show');


});
$('#complete-prod-form').submit(function(e){
e.preventDefault();

$.ajax({
url: $(this).attr("action"),
type:"POST",
data: $(this).serialize(),
success:function(data){
if (data.success < 1) {
showNotification("danger", data.message, "now-ui-icons travel_info");
}else{
showNotification("success", data.message, "now-ui-icons travel_info");
reload_notif_table_inprogress();
$('#completeprodmodal').modal('hide');
updateBadge();
}
},
error: function(jqXHR, textStatus, errorThrown) {
console.log(jqXHR);
console.log(textStatus);
console.log(errorThrown);
}
});
});
$('#cancel-prod-form').submit(function(e){
e.preventDefault();

$.ajax({
url: $(this).attr("action"),
type:"POST",
data: $(this).serialize(),
success:function(data){
if (data.success < 1) {
showNotification("danger", data.message, "now-ui-icons travel_info");
}else{
showNotification("success", data.message, "now-ui-icons ui-1_check");
if(data.tabselect == "notstarted"){
reload_notif_table();
}else{
reload_notif_table_inprogress();
}
$('#cancelprodmodal').modal('hide');
updateBadge();
}
},
error: function(jqXHR, textStatus, errorThrown) {
console.log(jqXHR);
console.log(textStatus);
console.log(errorThrown);
}
});
});
$('#notif_form .sel-notif').change(function(e){
e.preventDefault();
var filter_content = "notif_form";
var page = 1;
var filters =  '&' + $('#' + filter_content).serialize();

reload_notif_table(filters);
});
$('#notif_form_inprogress .sel-notif').change(function(e){
e.preventDefault();
var filter_content = "notif_form_inprogress";
var page = 1;
var filters =  '&' + $('#' + filter_content).serialize();

reload_notif_table_inprogress(filters);
});
$('#reschedule-prod-form').submit(function(e){
e.preventDefault();

$.ajax({
url: $(this).attr("action"),
type:"POST",
data: $(this).serialize(),
success:function(data){
if (data.success < 1) {
showNotification("danger", data.message, "now-ui-icons travel_info");
}else{
showNotification("success", data.message, "now-ui-icons ui-1_check");
$('#reschedmodal').modal('hide');
reload_notif_table();
updateBadge();
}
},
error: function(jqXHR, textStatus, errorThrown) {
console.log(jqXHR);
console.log(textStatus);
console.log(errorThrown);
}
});
});
$('#unschedule-prod-form').submit(function(e){
e.preventDefault();

$.ajax({
url: $(this).attr("action"),
type:"POST",
data: $(this).serialize(),
success:function(data){
if (data.success < 1) {
showNotification("danger", data.message, "now-ui-icons travel_info");
}else{
showNotification("success", data.message, "now-ui-icons ui-1_check");
$('#unschedmodal').modal('hide');
reload_notif_table();
updateBadge();
}
},
error: function(jqXHR, textStatus, errorThrown) {
console.log(jqXHR);
console.log(textStatus);
console.log(errorThrown);
}
});
});
$(document).on('click', '.prod_order_link_to_tracking', function(event){
  event.preventDefault();
  var guideid = $(this).attr('data-guideid');
  var itemcode = $(this).attr('data-itemcode');
  $.ajax({
    url: "/get_bom_tracking",
    type:"GET",
    data: {
      guideid: guideid,
      itemcode: itemcode
    },
    success:function(data){
      if(typeof(data.success) != "undefined" && data.success == 0){
        showNotification("danger", data.message, "now-ui-icons travel_info");
      }else{
        $('#track-view-modal #tbl_flowchart').html(data);
        $('#track-view-modal').modal('show');
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      console.log(textStatus);
      console.log(errorThrown);
    }
  });
});


</script>
<script>
function reload_notif_table(filters){
var operation_id =$('#primary-operation-id').val();
$.ajax({
url:"/get_all_prod_notif/"+ operation_id,
type:"GET",
data: filters,
success:function(data){
$('#tbl-notifications').html(data);
$('#view-notifications-modal').modal('show');

},
error : function(data) {
console.log(data.responseText);
}
});
}
</script>
<script>
function reload_notif_table_inprogress(filters){
var operation_id = $('#primary-operation-id').val();
$.ajax({
url:"/get_all_prod_notif_inprogress/"+ operation_id,
type:"GET",
data: filters,
success:function(data){
$('#tbl-notifications-inprogress').html(data);
// $('#view-notifications-modal').modal('show');

},
error : function(data) {
console.log(data.responseText);
}
});
}
</script>
<script>
function get_notif_filters(){
var operation_id = $('#primary-operation-id').val();
$.ajax({
url:"/get_notif_filters/" + operation_id,
type:"GET",
success:function(data){
var m = '';
$.each(data.customer, function(i, d){
m += '<option value="' + d + '">' + d + '</option>';
});

var l = '';
$.each(data.so, function(i, d){
l += '<option value="' + d + '">' + d + '</option>';
});

var n = '';
$.each(data.customer_inprog, function(i, d){
n += '<option value="' + d + '">' + d + '</option>';
});

var o = '';
$.each(data.so_inprog, function(i, d){
o += '<option value="' + d + '">' + d + '</option>';
});

$('#sel-customer-notif').append(m);
$('#sel-sales-order-notif').append(l);
$('#sel-sales-order-notif-inprogress').append(o);
$('#sel-customer-notif-inprogress').append(n);
}
});
}

$(document).on('click', '#btn-prod-sched', function(event){
event.preventDefault();
var operation_id = $('#primary-operation-id').val();
  if(operation_id == 0){
    var operation_id = 2;
  }
  window.location.href = "/production_schedule_calendar/"+operation_id;

});
$('#btnPrint').on("click", function () {
  
  $('#printmodalbody').printThis({
  });
});
</script>
<script>
  function reload_tbl_change_code(filters){
  $.ajax({
  url:"/get_reload_tbl_change_code",
  type:"GET",
  success:function(data){
    if((!$.trim(data))) {
        $("#tab_change_code").hide();
    }else{
      $('#tbl-notifications-change-code').html(data);
      $("#tab_change_code").show();
    }
  },
  error : function(data) {
  console.log(data.responseText);
  }
  });
  }
  </script>
  <script>
    function changeDate(date){
    let currentDate = new Date(date);
    var fd = currentDate.toDateString();
    return fd;
    }
    $(document).on('click', '.btnshift', function(e){
      e.preventDefault();
      var normal_date_format = $(this).attr('data-date');
      var newDate = changeDate(normal_date_format);
      var reload_tbl = $(this).attr('data-reloadtbl');
      var primary_operation_id = $('#primary-operation-id').val();
      if(primary_operation_id == 0){
        var primary_operation_id= 2;
      }
      $("#date_tobe_sched_shift").val(normal_date_format);
      tbl_shift_schedule_sched(1, normal_date_format,primary_operation_id);
      $('#datetodayModal .modal-title').text(newDate);
      get_default_shift_sched(normal_date_format,primary_operation_id );
      $('#datetodayModal').modal('show');
      add_row();
      prod_list_calendar(normal_date_format,primary_operation_id);
      $("#tbl_opration_id").val(primary_operation_id);
      $('#planned_start_datepicker').val('');
      $('#datetodayModal').modal();
    
    });
</script>
<script>
  function warning_notif_for_custom_shift(filters){
  var operation_id = $('#primary-operation-id').val();
  $.ajax({
  url:"/get_warning_notif_for_custom_shift/"+ operation_id,
  type:"GET",
  data: filters,
  success:function(data){
  $('#tbl-warning-fromshift').html(data);  
  },
  error : function(data) {
  console.log(data.responseText);
  }
  });
  }
  </script>
<script>
  $(document).ready(function() {
    $('#planned_start_datepicker').val('');
      $(document).on('change', '.custom-control-input', function(){
        if($('input[name="prodname[]"]:checked').length == 0){
          $( "#planned_start_datepicker" ).prop( "disabled", true );
        }else{
          $( "#planned_start_datepicker" ).prop( "disabled", false );
        }
        var someObj = {};
        someObj.slectedbox = [];
        someObj.unslectedbox = [];
        name = $(this).data('dateslct');
        inputid = "#selected_prod_order";
        console.log(someObj.slectedbox);
        
        $('.custom-control-input').each(function() {
          if ($(this).is(":checked")) {
            someObj.slectedbox.push($(this).attr("data-dateslct"));
          } else {
            someObj.unslectedbox.push($(this).attr("data-dateslct"));
          }
        });    
        $(inputid).val(someObj.slectedbox);
      });
      $(document).on('change', '#planned_start_datepicker', function(){
        var prod_list = $('#selected_prod_order').val();
        var operation = $('#primary-operation-id').val();
        var planned = $(this).val();
        if(operation == "3"){
          $.ajax({
                url: '/get_assembly_prod_calendar',
                type: 'get',
                data: { 'prod_list': prod_list, 'planned':planned},
                success: function(data)
                  {
                    if(data.success == 0){
                      $("#fabrication_update").attr("disabled", false);

                    }else{
                      $('#tbl_container_details').html(data);
                      $('#reschedule-deli-modal').modal('show');
                      $("#fabrication_update").attr("disabled", true);
                    }
                  },
                error: function(jqXHR, textStatus, errorThrown) {
                  console.log(jqXHR);
                  onsole.log(textStatus);
                  console.log(errorThrown);
                }
          });
        }else{
          $("#fabrication_update").attr("disabled", false);

        }
        console.log(prod_list);
      });
      
      if($('input[name="prodname[]"]:checked').length == 0){
        $( "#planned_start_datepicker" ).prop( "disabled", true );
      }else{
        $( "#planned_start_datepicker" ).prop( "disabled", false );
      }
  });
</script>
<script>
$('#calendar_update_rescheduled_delivery_date_form').submit(function(e){
      e.preventDefault();
        $.ajax({
          url:"/calendar_update_rescheduled_delivery_date",
          type:"POST",
          data: $(this).serialize(),
          success:function(data){
            console.log(data);
            if (data.success < 1) {
              showNotification("danger", data.message, "now-ui-icons travel_info");
            }else{
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#reschedule-deli-modal').modal('hide');
              $("#fabrication_update").attr("disabled", false);
            }
          },
          error : function(data) {
            console.log(data.responseText);
          }
        });
});
$('#add_shift_schedule_frm').submit(function(e){
      e.preventDefault();
        $.ajax({
          url: $(this).attr("action"),
          type:"POST",
          data: $(this).serialize(),
          success:function(data){
            console.log(data);
            if (data.success < 1) {
              showNotification("danger", data.message, "now-ui-icons travel_info");
            }else{
              showNotification("success", data.message, "now-ui-icons ui-1_check");
              $('#datetodayModal').modal('hide');
              setTimeout(function() {
                  location.reload();
              }, 1000);
            }
          },
          error : function(data) {
            console.log(data.responseText);
          }
        });
    });

function tbl_shift_schedule_sched(page, date, operation){
  $("#shiftsched-table").empty();
  $.ajax({
    url:"/get_tbl_shiftsched_list/?page="+page,
    type:"GET",
    data: {date_sched: date, operation: operation},
    success: function(data){
      var old_break = '';
      $.each(data.shift, function(i, d){
	      var sel_id = d.shift_id;
	      var break_id = d.shift_schedule_id;
	      old_break += '<input type="hidden" name="old_shift_sched[]" value="'+d.shift_schedule_id+'">';
	      console.log(d.shift_schedule_id);
        var s_type= d.shift_type;
	      var row1 = '';
	      $.each(data.shift_type, function(i, d){
	        selected = (d.shift_id == sel_id) ? 'selected' : null;
	        row1 += '<option value="' + d.shift_id  + '" '+selected+'>' + d.shift_type + '</option>';
	      });
	      var thizz = document.getElementById('shiftsched-table');
	      var id = $(thizz).closest('table').find('tr:last td:first').text();
	      var validation = isNaN(parseFloat(id));
	      if(validation){
	        var new_id = 1;
	      }else{
	        var new_id = parseInt(id) + 1;
	      }
	      var len2 = new_id;
	      var id_unique="shiftin"+len2;
        var id_unique1="shiftout"+len2;
        var id_unique2="shifttype"+len2;
        var id_stype= "#" + id_unique2;
	      var tblrow = '<tr>' +
          '<td style="display:none;">'+len2+'</td>' +
	        '<td class="p-1"><div class="form-group m-0"><input type="hidden" name="oldshift_sched_id[]"  value="'+break_id+'"><input type="hidden" style="width:100%;" name="shifttype[]" id='+id_unique2+'><select name="oldshift[]" data-shifttype='+id_unique2+' data-timein='+id_unique +' data-timeout='+id_unique1+' class="form-control m-0 count-row onchange-shift-select" required>'+row1+'</select></div></td>' +
	        '<td class="p-1"><div class="form-group m-0"><input type="text" autocomplete="off" placeholder="From Time" value="'+ d.time_in +'" id='+id_unique+' class="form-control m-0 select-input"  readonly></div></td>' +
	        '<td class="p-1"><div class="form-group m-0"><input type="text" autocomplete="off" placeholder="To Time" value="'+ d.time_out +'" id='+id_unique1+' class="form-control m-0 select-input" readonly></div></td>' +
	        '<td class="p-1 text-center"><button type="button" class="btn btn-danger btn-icon btn-icon-mini m-0 remove-row"><i class="now-ui-icons ui-1_simple-remove"></i></button></td>' +
	      '</tr>';
	      $("#datetodayModal #shiftsched-table").append(tblrow);
	      $("#datetodayModal #old_ids").html(old_break);
        $(id_stype).val(s_type);
	    });
    }
 }); 
}
function get_default_shift_sched(date, operation){
  $.ajax({
      url:"/get_tbl_default_shift_sched",
      type:"GET",
      data: {date: date , operation: operation},
      success:function(data){
        $('#default_shift_sched').html(data);
      }
  });
}
function add_row(){
  var date = $('#date_tobe_sched_shift').val();
  var operation =$('#primary-operation-id').val();
  if(operation == 0){
        var operation= 2;
      }
  $.ajax({
    url:"/get_tbl_shiftsched_list",
    type:"GET",
    data: {date_sched: date, operation: operation},
    success: function(data){
      var row1 = '<option value=""></option>';
      $.each(data.shift_type, function(i, d){
        row1 += '<option value="' + d.shift_id + '">' + d.shift_type + '</option>';
      });
      var thizz = document.getElementById('shiftsched-table');
      var id = $(thizz).closest('table').find('tr:last td:first').text();
      var validation = isNaN(parseFloat(id));
      if(validation){
        var new_id = 1;
      }else{
        var new_id = parseInt(id) + 1;
      }
      var len2 = new_id;
      var id_unique="shiftin"+len2;
      var id_unique1="shiftout"+len2;
      var id_unique2="shifttype"+len2;
      var tblrow = '<tr>' +
        '<td style="display:none;">'+len2+'</td>' +
        '<td class="p-1"><div class="form-group m-0"><input type="hidden" name="shifttype[]" id='+id_unique2+'><select name="newshift[]" class="form-control m-0 count-row onchange-shift-select"  data-timein='+id_unique +' data-shifttype='+id_unique2+' data-timeout='+id_unique1+' required>'+row1+'</select></div></td>' +
        '<td class="p-1"><div class="form-group m-0"><input type="text" autocomplete="off" placeholder="From Time" value="" class="form-control m-0 select-input" id='+id_unique+'  readonly></div></td>' +
        '<td class="p-1"><div class="form-group m-0"><input type="text" autocomplete="off" placeholder="To Time" value="" class="form-control m-0 select-input" id='+id_unique1+'  readonly></div></td>' +
        '<td class="p-1 text-center"><button type="button" class="btn btn-danger btn-icon btn-icon-mini m-0 remove-row"><i class="now-ui-icons ui-1_simple-remove"></i></button></td>' +
        '</tr>';
      
      $("#datetodayModal #shiftsched-table").append(tblrow);
    } 
  });
}
$('#add-row-shift-btn').click(function(e){
      add_row();
});
$(document).on('change', '.onchange-shift-select', function(){
           var shift_id = $(this).val();
           var shift_in = $(this).attr('data-timein');
           var shift_out = $(this).attr('data-timeout');
           var shift_type = $(this).attr('data-shifttype');
           var show_data_shiftin = "#"+shift_in;
           var show_data_shiftout = "#"+shift_out;
           var show_data_shifttype = "#"+shift_type;
            $.ajax({
            url:"/shift_sched_details",
            data:{shift:shift_id},
            type:"GET",
            success:function(data){
              if(data == null){
                $(show_data_shiftin).val("");
                $(show_data_shiftout).val("");
                $(show_data_shiftout).val("");
              }else{
                $(show_data_shiftin).val(data.time_in);
                $(show_data_shiftout).val(data.time_out);
                $(show_data_shifttype).val(data.shift_type);
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              console.log(jqXHR);
              console.log(textStatus);
              console.log(errorThrown);
            }
          });
      });
  function prod_list_calendar(date, operation){
    var forpage ="Prod_Sched";
    $.ajax({
      url:"/schedule_prod_calendar_details",
      type:"GET",
      data: {date: date , operation: operation, forpage:forpage},
      success:function(data){
        $('#prod_list_calendar').html(data);
      }
    });
  }
</script>
@endsection
