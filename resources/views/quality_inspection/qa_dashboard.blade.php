@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'qa_dashboard',
    'pageHeader' => 'Quality Assurance Dashboard',
  'pageSpan' => Auth::user()->employee_name . ' - ' . $user_details->designation_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0 ml-0 mr-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
  <div class="col-md-12 p-2">
    <div class="card m-0">
      {{-- style="min-height: 900px;" --}}
      <div class="card-body p-1">
        <div class="row p-0 m-0">
          <div class="col-9 p-1">
            <div class="card shadow-none border">
              <div class="card-header p-0">
                {{-- style="background-color: #012f6a;" --}}
                <h6 class="text-white1 font-weight-bold text-left m-2 rounded-top" style="font-size: 10.5pt;">In Process Quality Inspection Log(s)</h6>
                <table class="w-100 table-bordered text-center rounded" border="1">
                  <col style="width: 25%;">
                  <col style="width: 25%;">
                  <col style="width: 26%;">
                  <col style="width: 12%;">
                  <col style="width: 12%;">
                  <tbody>
                    <tr class="text-uppercase font-weight-bold text-white" style="background-color: #012f6a;">
                      <th rowspan="2" class="p-1">Operation</th>
                      <th rowspan="2" class="p-1">QA Inspector</th>
                      <th rowspan="2" class="p-1">Inspection Logs</th>
                      <th colspan="2" class="p-1">Qty</th>
                    </tr>
                    <tr class="text-white">
                      <th class="bg-success p-0">Inspected</th>
                      <th class="bg-danger p-0">Rejects</th>
                    </tr>
                    <tr class="bg-dark text-white">
                      <td class="p-2 text-uppercase">Fabrication</td>
                      <td class="p-2">
                        @forelse ($summary['fabrication']['inspectors'] as $qa_staff)
                          @php
                            $qa_staff_name = array_key_exists($qa_staff, $qa_staffs) ? $qa_staffs[$qa_staff] : null;
                          @endphp
                          <span class="d-block">{{ $qa_staff_name }}</span>
                        @empty
                          <span class="d-block">-</span>
                        @endforelse
                      </td>
                      <td class="p-2">{{ $summary['fabrication']['total_logs'] }}</td>
                      <td class="p-2">{{ $summary['fabrication']['qty_checked'] }}</td>
                      <td class="p-2">{{ $summary['fabrication']['qty_rejects'] }}</td>
                    </tr>
                    <tr class="bg-dark text-white">
                      <td class="p-2 text-uppercase">Painting</td>
                      <td class="p-2">
                        @forelse ($summary['painting']['inspectors'] as $qa_staff)
                          @php
                            $qa_staff_name = array_key_exists($qa_staff, $qa_staffs) ? $qa_staffs[$qa_staff] : null;
                          @endphp
                          <span class="d-block">{{ $qa_staff_name }}</span>
                        @empty
                          <span class="d-block">-</span>
                        @endforelse
                      </td>
                      <td class="p-2">{{ $summary['painting']['total_logs'] }}</td>
                      <td class="p-2">{{ $summary['painting']['qty_checked'] }}</td>
                      <td class="p-2">{{ $summary['painting']['qty_rejects'] }}</td>
                    </tr>
                    <tr class="bg-dark text-white">
                      <td class="p-2 text-uppercase">Assembly</td>
                      <td class="p-2">
                        @forelse ($summary['assembly']['inspectors'] as $qa_staff)
                          @php
                            $qa_staff_name = array_key_exists($qa_staff, $qa_staffs) ? $qa_staffs[$qa_staff] : null;
                          @endphp
                          <span class="d-block">{{ $qa_staff_name }}</span>
                        @empty
                          <span class="d-block">-</span>
                        @endforelse
                      </td>
                      <td class="p-2">{{ $summary['assembly']['total_logs'] }}</td>
                      <td class="p-2">{{ $summary['assembly']['qty_checked'] }}</td>
                      <td class="p-2">{{ $summary['assembly']['qty_rejects'] }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              {{-- <div class="card-body p-0" style="min-height: 200px;" id="wip-orders-div"></div> --}}
            </div>
            <div class="card shadow-none border">
              <div class="card-header pt-2 pl-3 pr-3 pb-2" style="background-color:#D35400;">
                <h6 class="text-white font-weight-bold text-left m-0 rounded-top" style="font-size: 10.5pt;">Reject(s)/Defect(s) for Confirmation <span class="badge badge-info float-right" id="reject-count-overall">0</span></h6>
              </div>
              <div class="card-body pb-1 pl-1 pr-1 pt-0" style="min-height: 300px;">
                <ul class="nav nav-tabs m-0 border-0 p-0 dashboard-custom-tabs" role="tablist" style="font-size: 9pt;">
                  <li class="nav-item font-weight-bold">
                    <a class="nav-link active border rounded m-1 pb-1 pt-1" data-toggle="tab" href="#fab" role="tab" data-operation="1">Fabrication <span class="badge badge-primary" id="reject-count-fabrication">0</span></a>
                  </li>
                  <li class="nav-item font-weight-bold">
                    <a class="nav-link border rounded m-1 pb-1 pt-1" data-toggle="tab" href="#pa" role="tab" data-operation="2">Painting <span class="badge badge-primary" id="reject-count-painting">0</span></a>
                  </li>
                  <li class="nav-item font-weight-bold">
                    <a class="nav-link border rounded m-1 pb-1 pt-1" data-toggle="tab" href="#wa" role="tab" data-operation="3">Wiring and Assembly <span class="badge badge-primary" id="reject-count-assembly">0</span></a>
                  </li>
                </ul>
                <div class="tab-content" id="reject-for-confirmation-content">
                  <div class="tab-pane fade show active" id="fab" role="tabpanel" data-operation="1">
                    <div class="form-group mb-1 ml-1 mr-1 pull-right" style="margin-top: -35px;">
                      <input type="text" class="form-control rounded bg-white search-reject-confirmation" data-operation="1" placeholder="Search" style="padding: 6px 8px;" autocomplete="off">
                    </div>
                    <div class="table-div m-1 p-0"></div>
                  </div>
                  <div class="tab-pane fade" id="pa" role="tabpanel" data-operation="2">
                    <div class="form-group mb-1 ml-1 mr-1 pull-right" style="margin-top: -35px;">
                      <input type="text" class="form-control rounded bg-white search-reject-confirmation" data-operation="2" placeholder="Search" style="padding: 6px 8px;" autocomplete="off">
                    </div>
                    <div class="table-div m-1 p-0"></div>
                  </div>
                  <div class="tab-pane fade" id="wa" role="tabpanel" data-operation="3">
                    <div class="form-group mb-1 ml-1 mr-1 pull-right" style="margin-top: -35px;">
                      <input type="text" class="form-control rounded bg-white search-reject-confirmation" data-operation="3" placeholder="Search" style="padding: 6px 8px;" autocomplete="off">
                    </div>
                    <div class="table-div m-1 p-0"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-3 p-1">
            <div class="card shadow-none border">
              <div class="card-header pt-2 pl-3 pr-3 pb-2" style="background-color: #D35400;">
                <h6 class="text-white font-weight-bold text-left m-0 rounded-top" style="font-size: 10.5pt;">For Outgoing Inspection</h6>
              </div>
              <div class="card-body p-0" style="min-height: 550px;" id="wip-orders-div"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="panel-header d-none"></div>
<div class="row p-0 d-none" style="margin-top: -190px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
    <div class="col-md-12">
      <ul class="nav nav-tabs" role="tablist" id="qa-dashboard-tabs">
        <li class="nav-item">
           <a class="nav-link active" data-toggle="tab" href="#tab0" role="tab" aria-controls="tab0" aria-selected="true">Quick View</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Rejection Report</a>
        </li>
      </ul>
      <div class="tab-content" style="min-height: 500px;">
        <div class="tab-pane active" id="tab0" role="tabpanel" aria-labelledby="tab0">
          <div class="row">
            <div class="col-md-12">
              <div class="card" style="border-radius: 0 0 3px 3px;">
                <div class="card-body">
                  <div class="row m-0">
                    <div class="col-md-4">
                      <div class="card" id="fabrication-card">
                        <div class="card-body">
                          <div class="row text-center">
                            <div class="col-md-12">
                              <h5 class="text-center text-uppercase font-weight-bold">Fabrication</h5>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Produced</span>
                              <span class="span-qty produced-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Inspected</span>
                              <span class="span-qty inspected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Reject</span>
                              <span class="span-qty rejected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                          </div>
                          <div class="row text-center mt-4">
                            <div class="col-md-12">
                              <table style="width: 100%; border: 1px solid #99A3A4;">
                                <tr style="background-color: #2E86C1;" class="text-white">
                                  <td style="width: 50%;"><span class="span-title">Production Order</span></td>
                                  <td style="width: 50%;"><span class="span-title">Completed / WIP</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty production-order-count">0</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty completed-wip-count">0</span>
                                  </td>
                                </tr>
                                {{-- <tr style="background-color: #2E86C1;" class="text-white">
                                  <td><span class="span-title">Performance</span></td>
                                  <td><span class="span-title">Efficiency</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty performance">0%</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty qa-efficiency">0%</span>
                                  </td>
                                </tr> --}}
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card" id="painting-card">
                        <div class="card-body">
                          <div class="row text-center">
                            <div class="col-md-12">
                              <h5 class="text-center text-uppercase font-weight-bold">Painting</h5>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Produced</span>
                              <span class="span-qty produced-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Inspected</span>
                              <span class="span-qty inspected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Reject</span>
                              <span class="span-qty rejected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                          </div>
                          <div class="row text-center mt-4">
                            <div class="col-md-12">
                              <table style="width: 100%; border: 1px solid #99A3A4;">
                                <tr style="background-color: #2E86C1;" class="text-white">
                                  <td style="width: 50%;"><span class="span-title">Production Order</span></td>
                                  <td style="width: 50%;"><span class="span-title">Completed / WIP</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty production-order-count">0</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty completed-wip-count">0</span>
                                  </td>
                                </tr>
                                {{-- <tr style="background-color: #2E86C1;" class="text-white">
                                  <td><span class="span-title">Performance</span></td>
                                  <td><span class="span-title">Efficiency</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty performance">0%</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty qa-efficiency">0%</span>
                                  </td>
                                </tr> --}}
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card" id="assembly-card">
                        <div class="card-body">
                          <div class="row text-center">
                            <div class="col-md-12">
                              <h5 class="text-center text-uppercase font-weight-bold">Assembly</h5>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Produced</span>
                              <span class="span-qty produced-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Inspected</span>
                              <span class="span-qty inspected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                            <div class="col-md-4">
                              <span class="span-title">Reject</span>
                              <span class="span-qty rejected-qty">0</span>
                              <span class="span-uom">Piece(s)</span>
                            </div>
                          </div>
                          <div class="row text-center mt-4">
                            <div class="col-md-12">
                              <table style="width: 100%; border: 1px solid #99A3A4;">
                                <tr style="background-color:#2E86C1;" class="text-white">
                                  <td style="width: 50%;"><span class="span-title">Production Order</span></td>
                                  <td style="width: 50%;"><span class="span-title">Completed / WIP</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty production-order-count">0</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty completed-wip-count">0</span>
                                  </td>
                                </tr>
                                {{-- <tr style="background-color: #2E86C1;" class="text-white">
                                  <td><span class="span-title">Performance</span></td>
                                  <td><span class="span-title">Efficiency</span></td>
                                </tr>
                                <tr>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty performance">0%</span>
                                  </td>
                                  <td style="border: 1px solid #99A3A4;">
                                    <span class="span-qty qa-efficiency">0%</span>
                                  </td>
                                </tr> --}}
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header" style="background-color: #0277BD;">
                          <div class="row" style="margin-top: -15px;">
                            <div class="col-md-8" style="padding: 10px;">
                                <h5 class="text-white font-weight-bold align-middle" style="font-size:13pt; margin: 0;">Reject Confirmation</h5>
                            </div>
                            <div class="col-md-4" style="padding: 8px;">
                                <div class="form-group" style="margin: 0;">
                                    <input type="text" id="search-reject-confirmation" class="form-control" placeholder="Search" style="background-color: white; padding: 6px 8px;" autocomplete="off">
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-body">
                          <div id="reject-confirmation-tbl-div"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
       
        <div class="tab-pane" id="tab2" role="tabpanel" aria-labelledby="tab2">
          <div class="row">
            <div class="col-md-12">
              <div class="card" style="border-radius: 0 0 3px 3px;">
                <div class="card-body p-1">
                  <div class="row m-0">
                    <div class="col-md-12 m-0">
                      <div class="row">
                        <div class="col-md-12 m-0 p-0" style="margin-top: -50px;">
                          <ul class="nav nav-tabs" role="tablist" id="reject-dashboard-tabs">
                            <li class="nav-item">
                              <a class="nav-link active" id="fab_tab_reject" data-toggle="tab" href="#fab0" role="tab" aria-controls="fab0" aria-selected="true">Fabrication</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link " id="pain_tab_reject" data-toggle="tab" href="#pan1" role="tab" aria-controls="pan1" aria-selected="false">Painting</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link " id="assem_tab_reject" data-toggle="tab" href="#assem2" role="tab" aria-controls="assem2" aria-selected="false" id="olu_click">Wiring and Assembly</a>
                            </li>
                          </ul>
                          <div class="tab-content" style="min-height: 500px;">
                            {{-- Fabrication Rejection Report --}}
                            <div class="tab-pane active" id="fab0" role="tabpanel" aria-labelledby="fab0">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="fab_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="fab_reject_filter" id="fab_reject_filter">
                                                  {{-- @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach --}}
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="fab_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="fab_yearpicker" style="font-weight: bolder;" name="fab_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="1">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_fab_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_fab_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_fab_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_fab_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12" style="">
                                                <div id="tbl_fab_log_reject_report" style="width: 100%;overflow: auto;"></div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            {{-- Painting Rejection Report --}}
                            <div class="tab-pane" id="pan1" role="tabpanel" aria-labelledby="pan1">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="pain_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="pain_reject_filter" id="pain_reject_filter">
                                                  {{-- @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach --}}
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="pain_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="pain_yearpicker" style="font-weight: bolder;" name="pain_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="2">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_pain_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_pain_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_pain_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_pain_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12" style="">
                                                <div id="tbl_pain_log_reject_report" style="width: 100%;overflow: auto;"></div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            {{-- Assembly Rejection Report --}}
                            <div class="tab-pane" id="assem2" role="tabpanel" aria-labelledby="assem2">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="card mb-0" style="border-radius: 0 0 3px 3px;">
                                    <div class="card-body">
                                      <div class="row">
                                        <div class="col-md-6 offset-md-6">
                                          <div class="row">
                                            <div class="col-md-4 p-0">
                                              <div class="form-group text-right">
                                                <label for="assem_reject_filter" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Reject Category:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                              <div class="form-group">
                                                <select class="form-control form-control-lg text-center class-dynamic m-0" name="assem_reject_filter" id="assem_reject_filter">
                                                  {{-- @foreach($reject_category as $rows)
                                                    <option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
                                                  @endforeach --}}
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <div class="form-group text-right">
                                                <label for="assem_yearpicker" class="text-dark font-weight-bold m-2 p-0" style="font-size: 11pt;">Year:</label>
                                              </div>
                                            </div>
                                            <div class="col-md-2 p-0">
                                              <div class="form-group">
                                                <select id="assem_yearpicker" style="font-weight: bolder;" name="assem_yearpicker" class="form-control form-control-lg">
                                                  <option value="2017" {{ date('Y') == 2017 ? 'selected' : '' }}>2017</option>
                                                  <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                                  <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                                  <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                                  <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                                  <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                                                  <option value="2023" {{ date('Y') == 2023 ? 'selected' : '' }}>2023</option>
                                                  <option value="2024" {{ date('Y') == 2024 ? 'selected' : '' }}>2024</option>
                                                  <option value="2025" {{ date('Y') == 2025 ? 'selected' : '' }}>2025</option>
                                                  <option value="2026" {{ date('Y') == 2026 ? 'selected' : '' }}>2026</option>
                                                </select>
                                              </div>
                                            </div>
                                            <div class="col-md-1 p-0">
                                              <img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="3">
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-12 m-0">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_assem_chart_reject" class="text-center font-weight-bold"></h5>
                                                    <canvas id="tbl_assem_reject_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="card">
                                              <div class="card-body">
                                                <div class="col-md-12">
                                                  <h5 id="label_assem_rate_chart_reject" class="text-center font-weight-bold"></h5>
                                                  <canvas id="tbl_assem_rate_report_chart" height="120"></canvas>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12">
                                                <div id="tbl_assem_log_reject_report" style="width: 100%;overflow: auto;"></div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


        </div>
      </div>
    </div>
  </div>

<style>
  .classheader{
    position: sticky; top: 0; 
    z-index: 2;
    position: -webkit-sticky;
    border: 0.8px solid white;
  }
  .margin-top{
    margin-top: -18px;
  }
  #fabrication .form-control, #painting .form-control{
      border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  #assembly .form-control{
      border: 1px solid #ccc;
    border-radius: 3px;
    box-shadow: none;
    margin-bottom: 15px;
  }
  .span-title{
    display: block;
    font-weight: bold;
    font-size: 11pt;
  }
  .span-qty{
    display: block;
    font-weight: bold;
    font-size: 22pt;
  }
  .span-uom{
    display: block;
    font-size:8pt;
  }
</style>

<!-- Modal -->
<div class="modal fade" id="quality-inspection-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="min-width: 80%;">
     <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #f57f17;">
           <h5 class="modal-title">&nbsp;
              QA <span class="qc-type"></span> <span class="qc-workstation font-weight-bold"></span>
           </h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
        </div>
        <div class="modal-body">
           <div id="quality-inspection-div"></div>
        </div>
     </div>
  </div>
</div>

<div class="modal fade" id="print-report-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="min-width: 78%;">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #0277BD;">
        <h5 class="modal-title font-weight-bold">Print Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 500px;">
        <div class="row">
          <div class="col-md-12" style="overflow: hidden;">
            <iframe src="#" id="print-report-iframe" class="d-no1ne zoom-frame" height="100%" width="100%" style="min-height: 800px;"></iframe>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="print-report-btn">Print</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{ asset('js/daterange/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/daterange/daterangepicker.css') }}" />

<script type="text/javascript" src="{{ asset('js/standalone/select2.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/standalone/select2.full.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.min.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('js/standalone/select2.css') }}" />

<script>
  $(document).ready(function(){
    load_dashboard();
    function load_dashboard(){
      count_reject_for_confirmation();
      $("#reject-for-confirmation-content .tab-pane").each(function( index ) {
        const operation = $(this).data('operation');
        const el = $(this);
        reject_for_confirmation(operation, el);
      });
    }

    function reject_for_confirmation(operation, el, page = null){
      var q = $(el).find('.search-reject-confirmation').eq(0).val();
      $.ajax({
        url:"/get_reject_for_confirmation/" + operation + "?page=" + page,
        type:"GET",
        data: {q},
        success:function(data){
          $(el).find('.table-div').eq(0).html(data);
        }
      });
    }

    function count_reject_for_confirmation(){
      $.ajax({
        url:"/count_reject_for_confirmation",
        type:"GET",
        success:function(data){
          $('#reject-count-fabrication').text(data.fabrication);
          $('#reject-count-painting').text(data.painting);
          $('#reject-count-assembly').text(data.assembly);
          $('#reject-count-overall').text(data.overall);
        }
      });
    }

    $(document).on('keyup', '.search-reject-confirmation', function(){
      var operation = $(this).data('operation');
      var el = $(this).closest('.tab-pane');
      reject_for_confirmation(operation, el);
    });

    $(document).on('click', '.paginate-reject-confirmation a', function(event){
      event.preventDefault();
      var el = $(this).closest('.tab-pane');
      var operation = el.data('operation');
      var page = $(this).attr('href').split('page=')[1];
      reject_for_confirmation(operation, el, page);
    });

    $(document).on('click', '.reject-confirmation-btn', function(e){
      e.preventDefault();
      
      var inspection_type = $(this).data('inspection-type');
      var workstation = $(this).data('workstation');
      var production_order = $(this).data('production-order');
      var process_id = $(this).data('process-id');
      var qa_id = $(this).data('qaid');

      $.ajax({
        url:"/get_reject_confirmation_checklist/" + production_order + "/" + workstation + "/" + process_id + "/" + qa_id,
        type:"GET",
        success:function(data){
          $('#quality-inspection-div').html(data);
          $('#quality-inspection-modal .qc-type').text(inspection_type);
          $('#quality-inspection-modal .qc-workstation').text('[' + workstation + ']');
          $('#quality-inspection-modal').modal('show');
        }
      });
    });

    $(document).on('submit', '#reject-confirmation-frm', function(e){
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          console.log(data);
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#quality-inspection-modal').modal('hide');
            reject_for_confirmation(data.details.operation_id, $("#reject-for-confirmation-content .tab-pane").find('.active'));
            count_reject_for_confirmation();
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

    $(document).on('click', '.print-rejection-report-btn', function(e){
      var a = $(this).parent().parent();
      var reject_category = a.find('select').eq(0).val();
      var reject_cat_name = a.find('select option:selected').eq(0).text();      
      var operation = $(this).data('operation-id');
      var year = a.find('select').eq(1).val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#print-report-iframe').attr('src', "/print_qa_rejection_report?" + $.param( data ));

      $('#print-report-modal').modal('show');
    });

    $('#print-report-modal').on('hidden.bs.modal', function (e) {
      $('#print-report-iframe').attr('src', "");
    });

    $('#print-report-btn').click(function(e){
      $("#print-report-iframe").get(0).contentWindow.print();
    });

    $(document).on('change', '#fab_reject_filter', function(event){
      tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
      fab_optyStats();
      tbl_fab_reject_rate_chart();
    });
  
    $(document).on('change', '#fab_yearpicker', function(event){
      tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
      fab_optyStats();
      tbl_fab_reject_rate_chart();
    });

    $(document).on('change', '#assem_reject_filter', function(event){
      tbl_assem_log_reject_report();
      assem_optyStats();
      tbl_assem_reject_rate_chart();
    }); 
  
    $(document).on('change', '#assem_yearpicker', function(event){
      tbl_assem_log_reject_report();
      assem_optyStats();
      tbl_assem_reject_rate_chart();
    });

    $(document).on('change', '#pain_reject_filter', function(event){
      tbl_pain_log_reject_report();
      pain_optyStats();
      tbl_pain_reject_rate_chart();
    });

    $(document).on('change', '#pain_yearpicker', function(event){
      tbl_pain_log_reject_report();
      pain_optyStats();
      tbl_pain_reject_rate_chart();
    });

    

    function tbl_fab_log_reject_report(div_id){
      var reject_category = $('#fab_reject_filter').val();  
      var reject_cat_name = $('#fab_reject_filter option:selected').text();      
      var operation = 1;
      var year = $('#fab_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $.ajax({
        url:"/rejection_report",
        type:"GET",
        data: data,
        success:function(data){
          $(div_id).html(data);
        }
      });
    }

    function tbl_pain_log_reject_report(){
      var reject_category = $('#pain_reject_filter').val();  
      var reject_cat_name = $('#pain_reject_filter option:selected').text();      
      var operation = 2;
      var year = $('#pain_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $.ajax({
        url:"/rejection_report",
        type:"GET",
        data: data,
        success:function(data){
          $('#tbl_pain_log_reject_report').html(data);
        }
      });
    }

    function tbl_assem_log_reject_report(){
      var reject_category = $('#assem_reject_filter').val();  
      var reject_cat_name = $('#assem_reject_filter option:selected').text();      
      var operation = 3;
      var year = $('#assem_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }
      $.ajax({
        url:"/rejection_report",
        type:"GET",
        data: data,
        success:function(data){
          $('#tbl_assem_log_reject_report').html(data);
        }
      });
    }

    function fab_optyStats(){
      var reject_category = $('#fab_reject_filter').val();  
      var reject_cat_name = $('#fab_reject_filter option:selected').text();      
      var operation = 1;
      var year = $('#fab_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_fab_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');

      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var reject = [];
          var val = [];
          var series=[];

          for(var i in data.year) {
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
            val.push(data.year[i].per_month);
          }

          var chartdata = {
            labels: series,
            data1:reject,
            datasets : [{
              data: val,
              backgroundColor: '#2874a6',
              borderColor: "#2874a6",
              borderWidth: 3,
              label: "Total Reject/s",
            }]
          };

          var ctx = $("#tbl_fab_reject_report_chart");
          if (window.fab_optyCtx != undefined) {
              window.fab_optyCtx.destroy();
          }

          window.fab_optyCtx = new Chart(ctx, {
            type: 'bar',
            data: chartdata,
            options: {
            tooltips: {
              callbacks: {
                title: function (t, d) {
                  return d['data1'][t[0]['index']];
                },
              },
            },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    function pain_optyStats(){
      var reject_category = $('#pain_reject_filter').val();
      var reject_cat_name = $('#pain_reject_filter option:selected').text();      
      var operation = 2;
      var year = $('#pain_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }
      $('#label_pain_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');
      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var reject = [];
          var val = [];
          var series=[];
          
          for(var i in data.year) {
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
            val.push(data.year[i].per_month);
          }
          
          var chartdata = {
            labels: series,
            data1:reject,
            datasets : [{
              data: val,
              backgroundColor: '#2874a6',
              borderColor: "#2874a6",
              borderWidth: 3,
              label: "Total Reject/s",
            }]
          };
          
          var ctx = $("#tbl_pain_reject_report_chart");
          if (window.pain_optyCtx != undefined) {
            window.pain_optyCtx.destroy();
          }
          
          window.pain_optyCtx = new Chart(ctx, {
            type: 'bar',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    function assem_optyStats(){
      var reject_category = $('#assem_reject_filter').val();  
      var reject_cat_name = $('#assem_reject_filter option:selected').text();      
      var operation = 3;
      var year = $('#assem_yearpicker').val();

      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_assem_chart_reject').text(reject_cat_name + ' Reject ('+ year +')');

      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var reject = [];
          var val = [];
          var series=[];
          
          for(var i in data.year) {
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
            val.push(data.year[i].per_month);
          }
          
          var chartdata = {
            labels: series,
            data1:reject,
            datasets : [{
              data: val,
              backgroundColor: '#2874a6',
              borderColor: "#2874a6",
              borderWidth: 3,
              label: "Total Reject/s",
            }]
          };
          
          var ctx = $("#tbl_assem_reject_report_chart");
          if (window.assem_optyCtx != undefined) {
            window.assem_optyCtx.destroy();
          }
          
          window.assem_optyCtx = new Chart(ctx, {
            type: 'bar',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
      
    }

    function tbl_fab_reject_rate_chart(){
      var reject_category = $('#fab_reject_filter').val();  
      var reject_cat_name = $('#fab_reject_filter option:selected').text();      
      var operation = 1
      var year = $('#fab_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_fab_rate_chart_reject').text('Reject Rate ('+ year +')');

      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var numberWithCommas = function(x) {
            return x.substring(0,10) + '...';
          };

          var reject = [];
          var target = [];
          var planned =[];
          var rate =[];
          var series =[];

          for(var i in data.year) {
            rate.push(data.year[i].per_rate);
            planned.push(data.year[i].target);
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
          }
          
          var chartdata = {
            data1:reject,
            labels: series,
            datasets : [{
              data: rate,
              backgroundColor: '#3cba9f',
              borderColor: "#3cba9f",
              label: "Reject Rate",
              fill: false
            },
            {
              data: planned,
              backgroundColor: '#3e95cd',
              borderColor: "#3e95cd",
              label: "Target",
              fill: false
            }]
          };
          
          var ctx = $("#tbl_fab_rate_report_chart");

          if (window.tbl_chartCtx != undefined) {
              window.tbl_chartCtx.destroy();
          }

          window.tbl_chartCtx = new Chart(ctx, {
            type: 'line',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
              elements: {
                line: {
                  tension: 0 // disables bezier curves
                }
              },
              scales: {         
                xAxes: [
                  { 
                    ticks: {
                      maxRotation: 90,
                      callbacks: {
                        title: function (tooltipItems, data) {
                          return data.labels[tooltipItems[0].index]
                        }
                      },
                    }, 
                  }
                ]
              }
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    function tbl_pain_reject_rate_chart(){
      var reject_category = $('#pain_reject_filter').val();  
      var reject_cat_name = $('#pain_reject_filter option:selected').text();      
      var operation = 2;
      var year = $('#pain_yearpicker').val();
      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_pain_rate_chart_reject').text('Reject Rate ('+ year +')');
      
      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var numberWithCommas = function(x) {
            return x.substring(0,10) + '...';
          };

          var reject = [];
          var target = [];
          var planned =[];
          var rate =[];
          var series =[];

          for(var i in data.year) {
            rate.push(data.year[i].per_rate);
            planned.push(data.year[i].target);
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
          }

          var chartdata = {
            data1:reject,
            labels: series,
            datasets : [{
              data: rate,
              backgroundColor: '#3cba9f',
              borderColor: "#3cba9f",
              label: "Reject Rate",
              fill: false
            },
            {
              data: planned,
              backgroundColor: '#3e95cd',
              borderColor: "#3e95cd",
              label: "Target",
              fill: false
            }]
          };
          
          var ctx = $("#tbl_pain_rate_report_chart");
          
          if (window.tbl_pain_chartCtx != undefined) {
            window.tbl_pain_chartCtx.destroy();
          }
          
          window.tbl_pain_chartCtx = new Chart(ctx, {
            type: 'line',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
              elements: {
                line: {
                  tension: 0 // disables bezier curves
                }
              },
              scales: {         
                xAxes: [
                  { 
                    ticks: {
                      maxRotation: 90,
                      callbacks: {
                        title: function (tooltipItems, data) {
                          return data.labels[tooltipItems[0].index]
                        }
                      },
                    }, 
                  }
                ]
              }
            }
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    function tbl_assem_reject_rate_chart(){
      var reject_category = $('#assem_reject_filter').val();  
      var reject_cat_name = $('#assem_reject_filter option:selected').text();      
      var operation = 3;
      var year = $('#assem_yearpicker').val();

      var data = {
        operation: operation,
        reject_category : reject_category,
        reject_name: reject_cat_name,
        year: year
      }

      $('#label_assem_rate_chart_reject').text('Reject Rate ('+ year +')');
      
      $.ajax({
        url: "/rejection_report_chart",
        method: "GET",
        data: data,
        success: function(data) {
          var numberWithCommas = function(x) {
            return x.substring(0,10) + '...';
          };

          var reject = [];
          var target = [];
          var planned =[];
          var rate =[];
          var series =[];

          for(var i in data.year) {
            rate.push(data.year[i].per_rate);
            planned.push(data.year[i].target);
            reject.push(data.year[i].reject);
            series.push(data.year[i].series);
          }
      
          var chartdata = {
            data1:reject,
            labels: series,
            datasets : [{
              data: rate,
              backgroundColor: '#3cba9f',
              borderColor: "#3cba9f",
              label: "Reject Rate",
              fill: false
            },
            {
              data: planned,
              backgroundColor: '#3e95cd',
              borderColor: "#3e95cd",
              label: "Target",
              fill: false
            }]
          };
          
          var ctx = $("#tbl_assem_rate_report_chart");
          
          if (window.tbl_assem_chartCtx != undefined) {
            window.tbl_assem_chartCtx.destroy();
          }
          
          window.tbl_assem_chartCtx = new Chart(ctx, {
            type: 'line',
            data: chartdata,
            options: {
              tooltips: {
                callbacks: {
                  title: function (t, d) {
                    return d['data1'][t[0]['index']];
                  },
                },
              },
              responsive: true,
              legend: {
                position: 'top',
                labels:{
                  boxWidth: 11
                }
              },
              elements: {
                line: {
                  tension: 0 // disables bezier curves
                }
              },
              scales: {         
                xAxes: [
                  { 
                    ticks: {
                      maxRotation: 90,
                      callbacks: {
                        title: function (tooltipItems, data) {
                          return data.labels[tooltipItems[0].index]
                        }
                      },
                    }, 
                  }
                ]
              }
            }
            
          });
        },
        error: function(data) {
          alert('Error fetching data!');
        }
      });
    }

    $(document).on('click', '#pain_tab_reject', function(event){
      tbl_pain_log_reject_report();
      pain_optyStats();
      tbl_pain_reject_rate_chart();
    });
    $(document).on('click', '#assem_tab_reject', function(event){
      tbl_assem_log_reject_report();
      assem_optyStats();
      tbl_assem_reject_rate_chart();
    });

    $(document).on('click', '#fab_tab_reject', function(event){
      tbl_fab_log_reject_report('#tbl_fab_log_reject_report');
      fab_optyStats();
      tbl_fab_reject_rate_chart();
    });

    var active_tab= $('#reject-dashboard-tabs .active').text();
    if(active_tab == "Fabrication"){
      $('#fab_tab_reject').trigger('click');
    }


    get_quick_view_data();

    function get_quick_view_data(){
      $.ajax({
        url:"/get_quick_view_data",
        type:"GET",
        success:function(data){
          $('#fabrication-card .produced-qty').text(data.fabrication.produced_qty);
          $('#painting-card .produced-qty').text(data.painting.produced_qty);
          $('#assembly-card .produced-qty').text(data.assembly.produced_qty);

          $('#fabrication-card .inspected-qty').text(data.fabrication.inspected_qty);
          $('#painting-card .inspected-qty').text(data.painting.inspected_qty);
          $('#assembly-card .inspected-qty').text(data.assembly.inspected_qty);

          $('#fabrication-card .rejected-qty').text(data.fabrication.rejected_qty);
          $('#painting-card .rejected-qty').text(data.painting.rejected_qty);
          $('#assembly-card .rejected-qty').text(data.assembly.rejected_qty);

          $('#fabrication-card .production-order-count').text(data.fabrication.production_order);
          $('#painting-card .production-order-count').text(data.painting.production_order);
          $('#assembly-card .production-order-count').text(data.assembly.production_order);

          $('#fabrication-card .completed-wip-count').text(data.fabrication.completed_wip_production_orders);
          $('#painting-card .completed-wip-count').text(data.painting.completed_wip_production_orders);
          $('#assembly-card .completed-wip-count').text(data.assembly.completed_wip_production_orders);

          // $('#fabrication-card .performance').text(data.fabrication.performance + '%');
          // $('#painting-card .performance').text(data.painting.performance + '%');
          // $('#assembly-card .performance').text(data.assembly.performance + '%');

          // $('#fabrication-card .qa-efficiency').text(data.fabrication.qa_efficiency + '%');
          // $('#painting-card .qa-efficiency').text(data.painting.qa_efficiency + '%');
          // $('#assembly-card .qa-efficiency').text(data.assembly.qa_efficiency + '%');
        }
      });
    }



    function showNotification(color, message, icon){
      $.notify({
        icon: icon,
        message: message
      },{
        type: color,
        timer: 1000,
        placement: {
          from: 'top',
          align: 'center'
        }
      });
    }

    
  });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('.sel3').select2({
      dropdownParent: $("#filter_qa_inspection_painting"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false,
      placeholder: "",
      allowClear: true
    });
    $('.sel4').select2({
      dropdownParent: $("#filter_qa_inspection_assembly"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false,
      placeholder: "",
      allowClear: true
    });
    $('.sel6').select2({
      dropdownParent: $("#filter_qa_inspection_fabrication"),
      dropdownAutoWidth: false,
      width: '100%',
      cache: false,
      placeholder: "",
      allowClear: true
    });
  });
</script>

<script type="text/javascript">
    $(document).on('click', '#fabrication-btn-export', function(){
      var date = $('#daterange').val();
      var workstation = $('#workstation').val();
      var startDate = $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD');
     
      var customer= $('#customer').val();
      var prod=$('#prod').val();
      var item_code= $('#item_code').val();
      var status= $('#qa_status').val();
      var processs= $('#process').val();
      var qa_inspector= $('#qa_inspector').val();
      var operator= $('#operator').val();
      if(customer ==""){
        var customer="none";
      }
      if(prod ==""){
        var prod="none";
      }
      if(item_code ==""){
        var item_code="none";
      }
      if(status ==""){
        var status="none";
      }
      if(processs ==""){
        var processs="none";
      }
      if(qa_inspector ==""){
        var qa_inspector="none";
      }
      if(operator ==""){
        var operator="none";
      }
      if(workstation == 'none'){
        
      }else if(date == ""){
        
      }else{
          location.href= "/get_tbl_qa_inspection_log_export/"+ startDate +"/" +endDate+"/"+workstation+"/"+ customer +"/" + prod + "/" + item_code + "/" + status + "/" + processs + "/" + qa_inspector + "/" +  operator;
      }

    });
</script>

<script type="text/javascript">
    $(document).on('click', '#painting-btn-export', function(){
      var date = $('#daterange_painting').val();
      var workstation = $('#workstation_painting').val();
      var startDate = $('#daterange_painting').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var endDate = $('#daterange_painting').data('daterangepicker').endDate.format('YYYY-MM-DD');
     
      var customer= $('#customer_painting').val();
      var prod=$('#prod_painting').val();
      var item_code= $('#item_code_painting').val();
      var status= $('#qa_status_painting').val();
      var processs= $('#process_painting').val();
      var qa_inspector= $('#qa_inspector_painting').val();
      var operator= $('#operator_painting').val();

      if(workstation == 'none'){
        
      }else if(date == ""){
        
      }else{
          location.href= "/get_tbl_qa_inspection_log_export_painting/"+ startDate +"/" +endDate+"/"+workstation+"/"+ customer +"/" + prod + "/" + item_code + "/" + status + "/" + processs + "/" + qa_inspector + "/" +  operator;
      }

    });
</script>
@endsection