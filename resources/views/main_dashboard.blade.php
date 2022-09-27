@extends('layouts.user_app', [
    'namePage' => 'Fabrication',
    'activePage' => 'main_dashboard',
])

@section('content')
@include('modals.view_for_feedback_list_modal')
<div class="panel-header">
   <div class="header text-center"> 
      <div class="row">
        <div class="col-md-12" style="margin-top:-70px;">
            <table style="text-align: center; width: 100%;">
              <tr>
                <td style="width: 25%; border-right: 5px solid white;">
                  <h5 class="title">
                    <div class="pull-right" style="margin-right: 20px;">
                      <span style="display: block; font-size: 18pt;">{{ date('M-d-Y') }}</span>
                      <span style="display: block; font-size: 11pt;">{{ date('l') }}</span>
                    </div>
                  </h5>
                </td>
                <td style="width: 14%; border-right: 5px solid white;">
                  <h5 class="title" style="margin: auto; font-size: 30pt;"><span id="current-time">--:--:-- --</span></h5>
                </td>
                <td style="width: 50%">
                  <h4 class="title text-left" style="margin-left: 20px; margin: auto 20pt;">Production Dashboard</h4>
                  <span class="title text-left" style="margin-left: 20px; margin: auto 20pt;float:left;">
                    <i>{{ Auth::user()->employee_name }} - {{ $user_details->designation_name }}</i>
                  </span>
                </td>
              </tr>
            </table>
            <input type="hidden" name="date_today" id="date_today" value="{{ date('Y-m-d') }}">
        </div>
      </div>
   </div>
</div>

<div class="content" style="margin-top: -180px;">
  <div class="row">
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-12 p-0">
          <table>
            <tr>
              @php
              $a = array_intersect($mes_user_operations, ['Painting', 'Fabrication', 'Wiring and Assembly']);
              @endphp
              @if (count($a) > 0)
              <td class="mm1" style="width: 10%; background-color: #2C3E50;">
                <a href="/item_feedback" class="text-center">
                <div class="menu-box m-1" style="background-color: transparent; border: none;">
                   <img src="{{ asset('storage/Main Icon/production_orders.png') }}" style="width: 25%; height: 40%; margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;filter:  brightness(0) invert(1);">
                  <footer class="hmt small text-center text-white" style="padding: 10px 0;">Production Orders</footer>
                </div>
              </a>
              <style>
              td.mm1:hover {
                background-color:#797D7F !important;
              }
              </style>
              </td>
              @endif
              @php
              $b = array_intersect($mes_user_operations, ['Fabrication']);
              @endphp
              @if (count($b) > 0)
              <td class="mm1" style="width: 10%; background-color: #566573;">
                <a href="/wizard" class="text-center">
                  <div class="menu-box m-1" style="background-color: transparent; border: none;">
                    <img src="{{ asset('storage/Main Icon/production_planning.png') }}"  style="width:25%; height:40%;margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;filter:  brightness(0) invert(1);">
                    <footer class="hmt small text-center text-white" style="padding: 10px 0;">Fabrication Planning</footer>
                  </div>
                </a>
              </td>
              @endif
              @php
              $b1 = array_intersect($mes_user_operations, ['Wiring and Assembly']);
              @endphp
              @if (count($b1) > 0)
              <td class="mm1" style="width: 10%; background-color: #808B96;">
                <a href="/assembly/wizard" class="text-center">
                  <div class="menu-box m-1" style="background-color: transparent; border: none;">
                    <img src="{{ asset('storage/Main Icon/production_planning.png') }}"  style="width:25%; height:40%;margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;filter:  brightness(0) invert(1);">
                    <footer class="hmt small text-center text-white" style="padding: 10px 0;">Assembly Planning</footer>
                  </div>
                </a>
              </td>
              @endif
              @php
              $c = array_intersect($mes_user_operations, ['Fabrication', 'Painting', 'Wiring and Assembly']);
              if(in_array('Fabrication', $mes_user_operations)){
                $link = '/production_schedule/1';
              }else if(in_array('Wiring and Assembly', $mes_user_operations)){
                $link = '/production_schedule/3';
              }else{
                $link = '/production_schedule_painting';
              }
              @endphp
              @if (count($c) > 0)
              <td class="mm1" style="width: 10%; background-color: #ABB2B9;">
                <a href="{{ $link }}" class="text-center">
                  <div class="menu-box m-1" style="background-color: transparent; border: none;">
                    <img src="{{ asset('storage/Main Icon/production_order_schedule.png') }}"  style="width:25%; height:40%;margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;">
                    <footer class="hmt small text-center text-dark" style="padding: 10px 0;">Scheduling</footer>
                  </div>
                </a>
              </td>
              @endif
              @php
              $d = array_intersect($mes_user_operations, ['Fabrication', 'Wiring and Assembly']);
              @endphp
              @if (count($d) > 0)
              {{-- <td style="width: 10%;">
                <a href="/stock_entry" class="text-center">
                  <div class="menu-box m-1">
                    <img src="{{ asset('storage/Main Icon/material_requests.png') }}"  style="width:25%; height:40%;margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;">
                    <footer class="hmt small text-center text-dark" style="padding: 10px 0;">Widthrawal Slips</footer>
                  </div>
                </a>
              </td> --}}
              @endif
              @php
              $e = array_intersect($mes_user_operations, ['Fabrication', 'Painting', 'Wiring and Assembly']);
              @endphp
              @if (count($e) > 0)
              <td class="mm1" style="width: 10%; background-color:#D5D8DC;">
                <a href="/bom" class="text-center">
                  <div class="menu-box m-1" style="background-color: transparent; border: none;">
                    <img src="{{ asset('storage/Main Icon/bom_list.png') }}"  style="width:25%; height:40%;margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;">
                    <footer class="hmt small text-center text-dark" style="padding: 10px 0;">BOM List </footer>
                  </div>
                </a>
              </td>
              @endif
              @php
              $f = array_intersect($mes_user_operations, ['Fabrication', 'Painting', 'Wiring and Assembly']);
              @endphp
              @if (count($f) > 0)
              <td class="mm1" style="width: 10%; background-color:#EAECEE;">
                <a href="/maintenance_request" class="text-center">
                  <div class="menu-box m-1" style="background-color: transparent; border: none;">
                      <img src="{{ asset('storage/Main Icon/maintenance_requests.png') }}"  style="width:25%; height:40%;margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;">
                    <footer class="hmt small text-center text-dark" style="padding: 10px 0;">Maintenance Request</footer>
                  </div>
                </a>
              </td>
              @endif
              @php
              $h = array_intersect($mes_user_operations, ['Fabrication', 'Wiring and Assembly']);
              @endphp
              @if (count($h) > 0)
              {{-- <td style="width: 10%;">
                <a href="/inventory" class="text-center">
                  <div class="menu-box m-1">
                    <img src="{{ asset('storage/Main Icon/inventory.png') }}"  style="width:25%; height:40%;margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;">
                    <footer class="hmt small text-center text-dark" style="padding: 10px 0;">Inventory</footer>
                  </div>
                </a>
              </td> --}}
              @endif
              @php
              $i = array_intersect($mes_user_operations, ['Fabrication', 'Painting', 'Wiring and Assembly']);
              @endphp
              @if (count($i) > 0)
              <td class="mm1" style="width: 10%; background-color:#F4F6F6;">
                <a href="/report_index" class="text-center">
                  <div class="menu-box m-1" style="background-color: transparent; border: none;">
                    <img src="{{ asset('storage/Main Icon/reports.png') }}"  style="width:25%; height:40%;margin-left: auto;
                      margin-right: auto; display: block; padding-top: 10px;">
                    <footer class="hmt small text-center text-dark" style="padding: 10px 0;">Reports & Analysis</footer>
                  </div>
                </a>
              </td>
              @endif
            </tr>
          </table>
        </div>
        <div class="col-md-12 p-0 mt-2">
          <div class="card m-0">
            <div class="card-body p-1">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item font-weight-bold">
                  <a class="nav-link active" data-toggle="tab" href="#fab" role="tab">Fabrication</a>
                </li>
                <li class="nav-item font-weight-bold">
                  <a class="nav-link" data-toggle="tab" href="#pa" role="tab">Painting</a>
                </li>
                <li class="nav-item font-weight-bold">
                  <a class="nav-link" data-toggle="tab" href="#wa" role="tab">Wiring and Assembly</a>
                </li>
              </ul>
              <div class="tab-content" id="on-going-production-orders-content">
                <div class="tab-pane fade show active" id="fab" role="tabpanel" data-operation="1">
                  <div class="row m-0 p-0">
                    <div class="col-md-12 m-0 pr-1 pl-1 pt-0">
                      <div class="row m-0 p-0">
                        <div class="col-md-12 table-div m-0 pr-1 pl-1 pt-0"></div>
                        <div class="col-md-6 table-div m-0 pr-1 pl-1 pt-0"></div>
                        <div class="col-md-6 table-div m-0 pr-1 pl-1 pt-0"></div>
                      </div>
                    </div>
                    <div class="col-md-12 table-div m-0 pr-1 pl-1 pt-0"></div>
                  </div>
                </div>
                <div class="tab-pane fade" id="pa" role="tabpanel" data-operation="2">
                  <div class="row m-0 p-0">
                    <div class="col-md-12 m-0 pr-1 pl-1 pt-0">
                      <div class="row m-0 p-0">
                        <div class="col-md-12 table-div m-0 pr-1 pl-1 pt-0"></div>
                        <div class="col-md-6 table-div m-0 pr-1 pl-1 pt-0"></div>
                        <div class="col-md-6 table-div m-0 pr-1 pl-1 pt-0"></div>
                      </div>
                    </div>
                    <div class="col-md-12 table-div m-0 pr-1 pl-1 pt-0"></div>
                  </div>
                </div>
                <div class="tab-pane fade" id="wa" role="tabpanel" data-operation="3">
                  <div class="row m-0 p-0">
                    <div class="col-md-12 m-0 pr-1 pl-1 pt-0">
                      <div class="row m-0 p-0">
                        <div class="col-md-12 table-div m-0 pr-1 pl-1 pt-0"></div>
                        <div class="col-md-6 table-div m-0 pr-1 pl-1 pt-0"></div>
                        <div class="col-md-6 table-div m-0 pr-1 pl-1 pt-0"></div>
                      </div>
                    </div>
                    <div class="col-md-12 table-div m-0 pr-1 pl-1 pt-0"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <style>
      #production-order-totals .custom-text-1{
        font-size: 20pt;
        font-weight: bold;
        margin: 0;
        padding: 0;
      }

      #production-order-totals .custom-text-2{
        font-size: 7pt;
      }

      #production-order-totals .custom-text-3{
        font-size: 10pt;
      }

      .custom-table-fixed-1 {
        display: block;
        width: 100%;
      }
      .custom-table-fixed-1 thead tr {
        display: block;
      }
      .custom-table-fixed-1 tbody {
        display: block;
        overflow-y: scroll;
        width: 100%;
        overflow-x: hidden;
      }
      .custom-table-fixed-1 tfoot tr {
        display: block;
      }
  </style>
    <div class="col-md-4">
      <div class="row">
        <div class="col-md-12">
          @php
            $fabrication_active = null;
            $wa_active = null;
            $painting_active = null;
            if(in_array('Fabrication', $permitted_production_operation)){
              $fabrication_active = 'active';
            }else if(!in_array('Fabrication', $permitted_production_operation) and in_array('Wiring and Assembly', $permitted_production_operation)){
              $wa_active = 'active';
            }else{
              $painting_active = 'active';
            }
          @endphp
          <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
              @if (in_array('Fabrication', $permitted_production_operation))
              <div class="carousel-item {{ $fabrication_active }}">
                <table class="w-100 text-center text-white mt-1" id="production-order-totals" style="background-color: #263238;">
                  <tr>
                    <td colspan="3" class="text-dark bg-white">
                      <span class="font-weight-bold text-uppercase">Fabrication</span>
                    </td>
                    <td rowspan="2" style="background-color: #28b463";>
                      <span class="font-weight-bold">For Feedback</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="bg-white text-dark"><span class="font-weight-bold">Planned</span></td>
                    <td class="bg-white text-dark"><span class="font-weight-bold">In Progress</span></td>
                    <td class="bg-white text-dark"><span class="font-weight-bold">Done</span></td>
                  </tr>
                  <tr>
                    <td>
                      <span class="d-block custom-text-1" id="fab-planned">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1" id="fab-wip">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1" id="fab-done">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1 for-feedback-count" id="fab-for-feedback" data-operation-id="1">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                  </tr>
                  <tr style="border-bottom: 1px solid #ABB2B9;">
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="fab-planned-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="fab-wip-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="fab-done-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="fab-for-feedback-qty">-</span> <small>Piece(s)</small>
                    </td>
                  </tr>
                </table>
              </div>
              @endif
              @if (in_array('Wiring and Assembly', $permitted_production_operation))
              <div class="carousel-item {{ $wa_active }}">
                <table class="w-100 text-center text-white mt-1 border border-secondary" id="production-order-totals" style="background-color: #263238;">
                  <tr>
                    <td colspan="3" class="text-dark bg-white">
                      <span class="font-weight-bold text-uppercase">Wiring & Assembly</span>
                    </td>
                    <td rowspan="2" style="background-color: #28b463";>
                      <span class="font-weight-bold">For Feedback</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="bg-white text-dark"><span class="font-weight-bold">Planned</span></td>
                    <td class="bg-white text-dark"><span class="font-weight-bold">In Progress</span></td>
                    <td class="bg-white text-dark"><span class="font-weight-bold">Done</span></td>
                  </tr>
                  <tr>
                    <td>
                      <span class="d-block custom-text-1" id="wa-planned">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1" id="wa-wip">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1" id="wa-done">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1 for-feedback-count" id="wa-for-feedback" data-operation-id="3">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                  </tr>
                  <tr style="border-bottom: 1px solid #ABB2B9;">
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="wa-planned-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="wa-wip-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="wa-done-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="wa-for-feedback-qty">-</span> <small>Piece(s)</small>
                    </td>
                  </tr>
                </table>
              </div>
              @endif
              @if (in_array('Painting', $permitted_production_operation))
              <div class="carousel-item {{ $painting_active }}">
                <table class="w-100 text-center text-white mt-1 border border-secondary" id="production-order-totals" style="background-color: #263238;">
                  <tr>
                    <td colspan="3" class="text-dark bg-white">
                      <span class="font-weight-bold text-uppercase">Painting</span>
                    </td>
                    <td rowspan="2" style="background-color: #28b463";>
                      <span class="font-weight-bold">For Feedback</span>
                    </td>
                  </tr>
                  <tr>
                    <td class="bg-white text-dark"><span class="font-weight-bold">Planned</span></td>
                    <td class="bg-white text-dark"><span class="font-weight-bold">In Progress</span></td>
                    <td class="bg-white text-dark"><span class="font-weight-bold">Done</span></td>
                  </tr>
                  <tr>
                    <td>
                      <span class="d-block custom-text-1" id="pa-planned">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1" id="pa-wip">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1" id="pa-done">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                    <td>
                      <span class="d-block custom-text-1 for-feedback-count" id="pa-for-feedback" data-operation-id="2">-</span>
                      <small class="custom-text-2">Production Order(s)</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="pa-planned-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="pa-wip-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="pa-done-qty">-</span> <small>Piece(s)</small>
                    </td>
                    <td class="pb-2">
                      <span class="custom-text-3 font-weight-bold" id="pa-for-feedback-qty">-</span> <small>Piece(s)</small>
                    </td>
                  </tr>
                </table>
              </div>
              @endif
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev" style="width: 20px !important">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next" style="width: 20px !important">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
            </a>
          </div>
        </div>
        </div>
        <div class="container-fluid mt-2 p-0">
          <ul class="nav nav-tabs bg-white" role="tablist">
						<li class="nav-item logs">
							<a class="nav-link active" data-toggle="tab" href="#logs">Activity Logs</a>
						</li>
						<li class="nav-item logs">
							<a class="nav-link" data-toggle="tab" href="#warnings">Warnings&nbsp;<span class="badge badge-danger" id="warnings-badge" style="font-size: 14px">0</span></a>
						</li>
					</ul>

          <div class="tab-content p-0">
            <div id="logs" class="container-fluid tab-pane p-0 active">
              <div class="card p-0" style="height: 600px;">
                <div class="card-header text-center text-white p-2" style="background-color: #3498db">
                  <h5 class="title m-0 text-uppercase">Activity Logs</h5>
                </div>
                <div class="table-full-width table-responsive" style="height: 550px; position: relative;" id="tbl-notifications"></div>
              </div>
            </div>

            <div id="warnings" class="container-fluid tab-pane p-0">
              <div class="card p-0" style="height: 600px;">
                <div class="card-header text-center text-white p-2" style="background-color: #3498db">
                  <h5 class="title m-0 text-uppercase">Warnings</h5>
                </div>
                <div class="table-full-width table-responsive" style="height: 550px; position: relative;" id="tbl-warnings"></div>
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
	          <i class="now-ui-icons ui-1_bell-53"></i> Notifications
	        </h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	        <div class="row">
	          <div class="col-md-12">
	            <div class="table-full-width table-responsive" style="height: 600px; position: relative;" id="tbl-notifications-modal"></div>
	          </div>
	        </div>
	      </div>
	      <div class="modal-footer p-2 pr-3">
	        <button type="button" class="btn btn-secondary m-0" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
  
  <style>
    .menu-box{
      display: inline-block;
      background-color: #ffffff; 
      border: 1px solid #dddddd;
      width: 96%;
      margin: 8%;
    }

    @-webkit-keyframes blinker {
      from { background-color: rgba(196, 21, 45, 0.2)/* #CD6155 */; }
      to { background-color: inherit; }
    }
    @-moz-keyframes blinker {
      from { background-color: rgba(196, 21, 45, 0.2)/* #CD6155 */; }
      to { background-color: inherit; }
    }
    @-o-keyframes blinker {
      from { background-color: rgba(196, 21, 45, 0.2)/* #CD6155 */; }
      to { background-color: inherit; }
    }
    @keyframes blinker {
      from { background-color: rgba(196, 21, 45, 0.2)/* #CD6155 */; }
      to { background-color: inherit; }
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
    .blink{
      text-decoration: blink;
      -webkit-animation-name: blinker;
      -webkit-animation-duration: 1s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }
    .blink_changecode{
      text-decoration: blink;
      -webkit-animation-name: blinker_change_code;
      -webkit-animation-duration: 1s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }

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

  .logs .active{
    background-color: #3498DB !important;
    color: #fff !important;
    border: 2px solid #3498DB !important;
  }
  
  </style>

  <div class="modal fade" id="review-bom-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="min-width: 70%;">
      <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" style="font-weight: bolder;">Modal Title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="text" id="production-order-val" style="display: none;">
            <div id="review-bom-details-div"></div>
          </div>
      </div>
    </div>
  </div>

  <!-- Modal Create STE Production Order -->
<div class="modal fade" id="create-ste-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form action="#" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Create Stock Entry</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8 offset-md-2 p-0">
              <div class="form-group text-center">
                <input type="hidden" name="production_order">
                <input type="hidden" name="item_code">
                <input type="hidden" name="manual" value="1">
                <p style="font-size: 12pt;" class="text-center m-0">Production Order</p>
                <p style="font-size: 12pt;" class="text-center m-0">Item Code</p>
                <p style="font-size: 12pt;" class="text-center m-0">
                  <span>Qty to Manufacture = </span><span class="font-weight-bold">0</span> <span class="font-weight-bold">UoM</span></p>
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
  <iframe src="#" id="iframe-print" style="display: none;"></iframe>
  
  @include('modals.stock_withdrawal_modal')
@endsection

@section('script')
<script>
  $(document).ready(function(){
    $('#mark-done-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr('action');
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#mark-done-modal').modal('hide');
            $('#jtname-modal').modal("hide");
            $('#view-machine-task-modal').modal("hide");
            loadwip();
          }else{
            showNotification("danger", data.message, "now-ui-icons travel_info");
            return false;
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      }); 
    });

    function dashboard_in_process_projects(operation){
      $.ajax({
        url:"/dashboard_in_process_projects",
        type:"GET",
        data: {operation: operation},
        success:function(data){
          if (operation == 1) {
            $('#fabrication-in-process-projects-list').html(data);
          } else if (operation == 3) {
            $('#assembly-in-process-projects-list').html(data);
          } else {
            $('#painting-in-process-projects-list').html(data);
          }
        }
      }); 
    }

    function dashboard_numbers(operation){
      $.ajax({
        url:"/dashboard_numbers",
        type:"GET",
        data: {operation: operation},
        success:function(data){
          if (operation == 1) {
            $('#fab-scheduled').text(data.scheduled);
            $('#fab-for-feedback').text(data.for_feedback);
            $('#fab-ongoing').text(data.ongoing);
            $('#fab-completed').text(data.completed);
            $('#fab-quality-inspections').text(data.quality_inspections);
            $('#fab-rejects').text(data.rejects);
            $('#fab-daily-output').text(data.daily_output);
          } else if (operation == 3) {
            $('#wa-scheduled').text(data.scheduled);
            $('#wa-for-feedback').text(data.for_feedback);
            $('#wa-ongoing').text(data.ongoing);
            $('#wa-completed').text(data.completed);
            $('#wa-quality-inspections').text(data.quality_inspections);
            $('#wa-rejects').text(data.rejects);
            $('#wa-daily-output').text(data.daily_output);
          } else {
            $('#p-scheduled').text(data.scheduled);
            $('#p-for-feedback').text(data.for_feedback);
            $('#p-ongoing').text(data.ongoing);
            $('#p-completed').text(data.completed);
            $('#p-quality-inspections').text(data.quality_inspections);
            $('#p-rejects').text(data.rejects);
            $('#p-daily-output').text(data.daily_output);
          }
        }
      }); 
    }

    function dashboard_machine_status(operation){
      $.ajax({
        url:"/dashboard_machine_status",
        type:"GET",
        data: {operation: operation},
        success:function(data){
          if (operation == 1) {
            $('#fabrication-machines-status-table').html(data);
          } else if (operation == 3) {
            $('#assembly-machines-status-table').html(data);
          } else {
            $('#painting-machines-status-table').html(data);
          }
        }
      }); 
    }

    function dashboard_operator_list(operation){
      $.ajax({
        url:"/dashboard_operator_list",
        type:"GET",
        data: {operation: operation},
        success:function(data){
          if (operation == 1) {
            $('#fabrication-operators-table').html(data);
          } else if (operation == 3) {
            $('#assembly-operators-table').html(data);
          } else {
            $('#painting-operators-table').html(data);
          }
        }
      }); 
    }

    var date_today = $("#date_today").val();

    function loadwip(){
      $( "#on-going-production-orders-content .tab-pane" ).each(function( index ) {
        const operation = $(this).data('operation');
        const el = $(this);
        
        get_ongoing_production_orders(operation, el);
      });
    }

    load_dashboard();
    count_current_production();
    function load_dashboard(){
      $( "#on-going-production-orders-content .tab-pane" ).each(function( index ) {
        const operation = $(this).data('operation');
        const el = $(this);
        
        get_ongoing_production_orders(operation, el);
        get_qa(operation, el);
        // get_machine_status_per_operation(operation, el);
        // maintenance_schedules_per_operation(operation, el);
      });
    }

    function maintenance_schedules_per_operation(operation, el){
      $.ajax({
        url:"/maintenance_schedules_per_operation/" + operation,
        type:"GET",
        data: {scheduled_date: date_today},
        success:function(data){
          $(el).find('.table-div').eq(2).html(data);
        }
      }); 
    }

    function get_machine_status_per_operation(operation, el){
      $.ajax({
        url:"/get_machine_status_per_operation/" + operation,
        type:"GET",
        data: {scheduled_date: date_today},
        success:function(data){
          $(el).find('.table-div').eq(1).html(data);
        }
      }); 
    }

    // setInterval(load_dashboard, 10000);
    // setInterval(count_current_production, 8000);

    setInterval(notif_dashboard('#tbl-notifications'), 7000);

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

    function get_qa(operation, el){
      $.ajax({
        url:"/qa_monitoring_summary/" + date_today,
        type:"GET",
        data: {operation: operation},
        success:function(data){
          $(el).find('.table-div').eq(3).html(data);
        }
      }); 
    }

    function get_ongoing_production_orders(operation, el){
      $.ajax({
        url:"/get_production_order_list/" + date_today,
        type:"GET",
        data: {operation: operation},
        success:function(data){
          $(el).find('.table-div').eq(0).html(data);
        }
      }); 
    }
  
    $(document).on('click', '.for-feedback-count', function(e){
      e.preventDefault();
      var operation_id = $(this).data('operation-id');
      $('#view-for-feedback-list-operation-id').text(operation_id);
      get_for_feedback_production(1);
      $('#view-for-feedback-list-modal').modal('show');
    });
    
    function get_for_feedback_production(page){
      var operation_id = $('#view-for-feedback-list-operation-id').text();
      var query = $('#view-for-feedback-list-search-box').val();
      $.ajax({
        url: "/production_order_list/Awaiting Feedback?page=" + page,
        type:"GET",
        data: {search_string: query, operation: operation_id},
        success:function(data){
          $('#view-for-feedback-list-table').html(data);
        }
      });
    }
    $('#reschedule_delivery_frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#reschedule-delivery-modal').modal('hide');
            get_for_feedback_production(1);
          }
        }
      });
    });
    $(document).on('click', '.for-feedback-production-pagination a', function(event){
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      get_for_feedback_production(page);
    });

    $(document).on('click', '.custom-production-pagination a', function(event){
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      get_for_feedback_production(page);
    });

    

    $(document).on('keyup', '#view-for-feedback-list-search-box', function(){
      get_for_feedback_production(1);
    });

    $(document).on('click', '.view-bom-details-btn', function(e){
      e.preventDefault();
      $('#production-order-val').val($(this).data('production-order'));
      $.ajax({
        url: "/view_bom_for_review/" + $(this).data('bom'),
        type:"GET",
        success:function(data){
          $('#review-bom-details-div').html(data);
        }
      });

      $('#review-bom-modal .modal-title').html('BOM Update [' + $(this).data('bom') + ']');
      $('#review-bom-modal').modal('show');
    });

    $(document).on('click', '.print-transfer-slip-btn', function(e){
      e.preventDefault();

      $.ajax({
        url: "/print_fg_transfer_slip/" + $(this).data('production-order'),
        type:"GET",
        success:function(data){
          $("#iframe-print").attr("src", this.url);
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      });
    });

    function get_production_order_items(id){
      $.ajax({
        url:"/get_production_order_items/"+ id,
        type:"GET",
        success:function(data){
          $('#tbl_view_transfer_details').html(data);
          $('#stock-entry-details-modal').modal('show');
        },
        error : function(data) {
          console.log(data.responseText);
        }
      });
    }
  
    function get_target_warehouse(operation_id, target_warehouse){
      $('#target-warehouse-sel').empty();
      $.ajax({
        url: "/get_target_warehouse/" + operation_id,
        type:"GET",
        success:function(data){
          var opt = '';
          $.each(data, function(i, d){
            var selected = (target_warehouse == d) ? 'selected' : '';
            opt += '<option value="' + d + '" ' + selected + '>' + d + '</option>';
          });

          $('#target-warehouse-sel').append(opt);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(jqXHR);
            console.log(textStatus);
            console.log(errorThrown);
        },
      });
    }

    $(document).on('click', '.delete-pending-mtfm-btn', function(e){
      e.preventDefault();
      var $row = $(this).closest('tr');
      var item_code = $row.find('span').eq(0).text();
      var ste_no = $row.find('td').eq(0).text();
      $('#delete-pending-mtfm-modal input[name="sted_id"]').val($(this).data('id'));
      $('#delete-pending-mtfm-modal input[name="production_order"]').val($(this).data('production-order'));
      $('#delete-pending-mtfm-modal input[name="ste_no"]').val(ste_no);
      $('#delete-pending-mtfm-modal .modal-body span').eq(0).text(item_code);
      $('#delete-pending-mtfm-modal .modal-body span').eq(1).text('('+ste_no+')');
      $('#delete-pending-mtfm-modal').modal('show');
    });
  
    $('#delete-pending-mtfm-modal form').submit(function(e){
      e.preventDefault();
      var production_order = $('#delete-pending-mtfm-modal input[name="production_order"]').val();
      var sted_id = $('#delete-pending-mtfm-modal input[name="sted_id"]').val();
      var ste_no = $('#delete-pending-mtfm-modal input[name="ste_no"]').val();
     
      $.ajax({
        url:"/cancel_request/" + sted_id,
        type:"POST",
        data: {ste_no: ste_no},
        success:function(response){
          console.log(response);
          if (response.error == 1) {
            showNotification("danger", response.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", response.message, "now-ui-icons travel_info");
            get_pending_material_transfer_for_manufacture(production_order);
            $('#delete-pending-mtfm-modal').modal('hide');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
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

    $(document).on('click', '.create-feedback-btn', function(e){
      e.preventDefault();
      
      $('#submit-feedback-btn').removeAttr('disabled');
      var production_order = $(this).data('production-order');
      $('#confirm-feedback-production-modal input[name="production_order"]').val(production_order);
      get_pending_material_transfer_for_manufacture(production_order);
      $('#confirm-feedback-production-modal').modal('show');
    });

    $(document).on('click', '.submit-ste-btn', function(e){
      e.preventDefault();
      var production_order = $(this).data('production-order');
      $.ajax({
        url:"/submit_stock_entries/" + production_order,
        type:"POST",
        success:function(data){
          if(data.success == 2){
            showNotification("info", data.message, "now-ui-icons travel_info");
          }else if(data.success == 1){
            get_production_order_items(production_order);
            showNotification("success", data.message, "now-ui-icons ui-1_check");
          }else{
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

    $('#get-notifications-btn').click(function(e){
      e.preventDefault();

      notif_dashboard('#tbl-notifications-modal');
      $('#view-notifications-modal').modal('show');
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

    function count_current_production(){
      $.ajax({
        url:"/count_current_production_order/" + date_today,
        type:"GET",
        success:function(data){
          $('#fab-planned').text(data.fab_planned);
          $('#fab-planned-qty').text(data.fab_planned_qty);
          $('#fab-wip').text(data.fab_wip);
          $('#fab-wip-qty').text(data.fab_wip_qty);
          $('#fab-done').text(data.fab_done);
          $('#fab-done-qty').text(data.fab_done_qty);
          $('#fab-for-feedback').text(data.fab_for_feedback);
          $('#fab-for-feedback-qty').text(data.fab_for_feedback_qty);
          $('#wa-planned').text(data.wa_planned);
          $('#wa-planned-qty').text(data.wa_planned_qty);
          $('#wa-wip').text(data.wa_wip);
          $('#wa-wip-qty').text(data.wa_wip_qty);
          $('#wa-done').text(data.wa_done);
          $('#wa-done-qty').text(data.wa_done_qty);
          $('#wa-for-feedback').text(data.wa_for_feedback);
          $('#wa-for-feedback-qty').text(data.wa_for_feedback_qty);
          $('#pa-planned').text(data.pa_planned);
          $('#pa-planned-qty').text(data.pa_planned_qty);
          $('#pa-wip').text(data.pa_wip);
          $('#pa-wip-qty').text(data.pa_wip_qty);
          $('#pa-done').text(data.pa_done);
          $('#pa-done-qty').text(data.pa_done_qty);
          $('#pa-for-feedback').text(data.pa_for_feedback);
          $('#pa-for-feedback-qty').text(data.pa_for_feedback_qty);
        }
      }); 
    }
  });
</script>

<script type="text/javascript">
  warnings_dashboard();
  function notif_dashboard(el){
    // setInterval(function() {
      $.ajax({
        url:"/get_tbl_notif_dashboard",
        type:"GET",
        success:function(data){
          $(el).html(data);
        }
      });
    // }, 5000);
  }

  function warnings_dashboard(){
    // setInterval(function() {
      $.ajax({
        url:"/get_tbl_warnings_dashboard",
        type:"GET",
        success:function(data){
          $('#tbl-warnings').html(data);
          $('#warnings-badge').text($('#warnings-count').val());
        }
      });
    // }, 5000);
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(document).on('click', '.btn-hide', function(e){
    e.preventDefault();
    var from_table = $(this).attr('data-frmtable')
    var data = {  
      timelog_id: $(this).attr('data-timelogid'), 
      frm_table: $(this).attr('data-frmtable')
    }
    if(from_table == "machine"){

    }else{
      $.ajax({
      url:"/hide_reject",
      type:"post",
      data:data,
      success:function(response){
        if (response.success > 0) {
          notif_dashboard('#tbl-notifications');
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
</script>
@endsection