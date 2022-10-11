@extends('layouts.user_app', [
    'namePage' => 'MES',
    'activePage' => 'qa_dashboard',
    'pageHeader' => 'QA Rejection Report',
  'pageSpan' => Auth::user()->employee_name
])

@section('content')
<div class="panel-header"></div>
<div class="row p-0 ml-0 mr-0" style="margin-top: -213px; margin-bottom: 0; margin-left: 0; margin-right: 0; min-height: 850px;">
	<div class="col-md-12 p-2">
		<div class="card m-0">
			<div class="card-body p-1">
				<div class="row p-0 m-0">
					<div class="col-12 p-1">
						<div class="card shadow-none">
							<div class="card-body pb-1 pl-1 pr-1 pt-0" style="min-height: 300px;">
								<ul class="nav nav-tabs m-0 border-0 p-0" role="tablist" id="reject-dashboard-tabs" style="font-size: 9pt;">
									<li class="nav-item font-weight-bold">
										<a class="nav-link active border rounded m-1 pb-1 pt-1" id="fab_tab_reject" data-toggle="tab" href="#fab0" role="tab" aria-controls="fab0" aria-selected="true">Fabrication</a>
									</li>
									<li class="nav-item font-weight-bold">
										<a class="nav-link border rounded m-1 pb-1 pt-1" id="pain_tab_reject" data-toggle="tab" href="#pan1" role="tab" aria-controls="pan1" aria-selected="false">Painting</a>
									</li>
									<li class="nav-item font-weight-bold">
										<a class="nav-link border rounded m-1 pb-1 pt-1" id="assem_tab_reject" data-toggle="tab" href="#assem2" role="tab" aria-controls="assem2" aria-selected="false">Wiring and Assembly</a>
									</li>
								</ul>
								<div class="tab-content" style="min-height: 500px;">
									{{-- Fabrication Rejection Report --}}
									<div class="tab-pane active" id="fab0" role="tabpanel" aria-labelledby="fab0">
										<div class="row p-0 m-0">
											<div class="col-md-12 p-0">
												<div class="row p-0 m-0">
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
																		@foreach($reject_category as $rows)
																		<option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
																		@endforeach
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
																		@for ($y = 2017; $y <= date('Y'); $y++)
																		<option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
																		@endfor
																	</select>
																</div>
															</div>
															<div class="col-md-1 p-0">
																<img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="1">
															</div>
														</div>
													</div>
													<div class="col-md-6 p-2">
														<div class="p-3 border">
															<h5 id="label_fab_chart_reject" class="text-center font-weight-bold"></h5>
															<canvas id="tbl_fab_reject_report_chart" height="120"></canvas>
														</div>
													</div>
													<div class="col-md-6 p-2">
														<div class="p-3 border">
															<h5 id="label_fab_rate_chart_reject" class="text-center font-weight-bold"></h5>
															<canvas id="tbl_fab_rate_report_chart" height="120"></canvas>
														</div>
													</div>
													<div class="col-md-12 p-2">
														<div class="p-0">
															<div id="tbl_fab_log_reject_report" style="width: 100%;overflow: auto;"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									{{-- Painting Rejection Report --}}
									<div class="tab-pane" id="pan1" role="tabpanel" aria-labelledby="pan1">
										<div class="row p-0 m-0">
											<div class="col-md-12 p-0">
												<div class="row p-0 m-0">
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
																		@foreach($reject_category as $rows)
																		<option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
																		@endforeach
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
																		@for ($y = 2017; $y <= date('Y'); $y++)
																		<option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
																		@endfor
																	</select>
																</div>
															</div>
															<div class="col-md-1 p-0">
																<img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="2">
															</div>
														</div>
													</div>
													<div class="col-md-6 p-2">
														<div class="p-3 border">
															<h5 id="label_pain_chart_reject" class="text-center font-weight-bold"></h5>
															<canvas id="tbl_pain_reject_report_chart" height="120"></canvas>
														</div>
													</div>
													<div class="col-md-6 p-2">
														<div class="p-3 border">
															<h5 id="label_pain_rate_chart_reject" class="text-center font-weight-bold"></h5>
															<canvas id="tbl_pain_rate_report_chart" height="120"></canvas>
														</div>
													</div>
													<div class="col-md-12 p-2">
														<div class="p-0">
															<div id="tbl_pain_log_reject_report" style="width: 100%;overflow: auto;"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									{{-- Assembly Rejection Report --}}
									<div class="tab-pane" id="assem2" role="tabpanel" aria-labelledby="assem2">
										<div class="row p-0 m-0">
											<div class="col-md-12 p-0">
												<div class="row p-0 m-0">
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
																		@foreach($reject_category as $rows)
																		<option value="{{$rows->reject_category_id}}">{{$rows->reject_category_name}}</option>
																		@endforeach
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
																		@for ($y = 2017; $y <= date('Y'); $y++)
																		<option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
																		@endfor
																	</select>
																</div>
															</div>
															<div class="col-md-1 p-0">
																<img src="{{ asset('img/print.png') }}" width="35" class="ml-3 mt-1 print-rejection-report-btn" data-operation-id="3">
															</div>
														</div>
													</div>
													<div class="col-md-6 p-2">
														<div class="p-3 border">
															<h5 id="label_assem_chart_reject" class="text-center font-weight-bold"></h5>
															<canvas id="tbl_assem_reject_report_chart" height="120"></canvas>
														</div>
													</div>
													<div class="col-md-6 p-2">
														<div class="p-3 border">
															<h5 id="label_assem_rate_chart_reject" class="text-center font-weight-bold"></h5>
															<canvas id="tbl_assem_rate_report_chart" height="120"></canvas>
														</div>
													</div>
													<div class="col-md-12 p-2">
														<div class="p-0">
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
<script>
  $(document).ready(function(){
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
          console.log('Error fetching data!');
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
          console.log('Error fetching data!');
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
          console.log('Error fetching data!');
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
          console.log('Error fetching data!');
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
          console.log('Error fetching data!');
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
          console.log('Error fetching data!');
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
@endsection