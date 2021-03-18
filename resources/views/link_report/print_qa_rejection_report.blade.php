<div class="print">
	<table style="margin-bottom: 10px;">
		<tr class="m">
			<td style="width: 710px !important;">
				<div style="margin-right: 5px; border: 1px solid;">
					<div id="ctx-1-label" style="font-size: 9pt; font-weight: bold; margin: 7px 1px 1px 1px;"></div>
					<canvas id="ctx-1"></canvas>
				</div>
			</td>
			<td style="width: 710px !important;">
				<div style="margin-left: 5px; border: 1px solid;">
					<div id="ctx-2-label" style="font-size: 9pt; font-weight: bold; margin: 7px 1px 1px 1px;"></div>
					<canvas id="ctx-2"></canvas>
				</div>
			</td>
		</tr>
	</table>
	<table border="1">
		<col style="width: 370px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<col style="width: 70px;">
		<thead>
			<tr>
				<th>{{ $reject_category_name }}</th>
				@foreach($month_column as $values)
				<th>{{ $values }}</th>
				@endforeach
				<th>Total</th>
				<th rowspan="2">Reject Rate</th>
				<th rowspan="2">Target</th>
			</tr>
			<tr>
				<th><b>Total Output</b></th>
				@foreach($total_output_per_month as $topm)
				<th>{{ $topm['sum'] }}</th>
				@endforeach
				<th>{{ $total_output }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($data as $rows => $row)
			<tr>
				<td>
					<span style="margin-left: 8px;">
						<b>{{ $row['series'] }}.</b> <i>{{$row['reject']}}</i>
					</span>
				</td>
				@foreach($row['data'] as $r)
				<td class="m">{{ $r['sum'] }}</td>
				@endforeach
				<td class="m">{{ $row['per_month'] }}</td>
				<td class="m"><b>{{$row['per_rate']}}<small>%</small></b></td>
				<td class="m"><b>2.0000<small>%</small></b></td>
			</tr>
			@endforeach 
			<tr>
				<th>Total Reject</th>
				@foreach($total_reject_per_month as $trpm)
				<th>{{ $trpm['sum'] }}</th>
				@endforeach
				<th>{{ $total_reject }}</th>
				<th rowspan="2">{{ $reject_rate_for_total_reject }}<small>%</small></th>
				<th rowspan="2">2.0000<small>%</small></th>
			</tr>
			<tr>
				<th>Reject Rate</th>
				@foreach($reject_rate as $rr)
				<th>{{ $rr['sum'] }}<small>%</small></th>
				@endforeach
				<th>{{$total_reject_rate}}<small>%</small></th>
			</tr>
		</tbody>
	</table>
</div>



	<script src="{{ asset('js/core/ajax.min.js') }}"></script>
	<script src="{{ asset('/js/plugins/chartjs.min.js') }}"></script>

	<style type="text/css">
		table { 
			width: 1420px !important;
			font-size: 7pt;
			border-collapse: collapse;
			font-family: verdana;
		}

		table td, th{
			padding: 0.2%;
		}
		
		@media print{@page {size: landscape}}

		.m{
			text-align: center;
		}

		canvas{
			width: 705px !important;
		}
	</style>

	<script>

		var reject_category = "{{ $requests['reject_category'] }}";  
		var reject_cat_name = "{{ $requests['reject_name'] }}";      
		var operation = "{{ $requests['operation'] }}";
		var year = "{{ $requests['year'] }}";
		var data = {
			operation: operation,
			reject_category : reject_category,
			reject_name: reject_cat_name,
			year: year
		}
		 
		 bar_chart();
		 function bar_chart(){
			  $('#ctx-1-label').text(reject_cat_name + ' Reject ('+ year +')');
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
	  
					  var ctx = $("#ctx-1");
					  if (window.ctx_1 != undefined) {
						  window.ctx_1.destroy();
					  }
	  
					  window.ctx_1 = new Chart(ctx, {
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

		  lin_chart();
		  function lin_chart(){
			
			  $('#ctx-2-label').text('Reject Rate ('+ year +')');
	  
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
					  for(var i in data.year) {
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
	  
					  var ctx = $("#ctx-2");
	  
					  if (window.ctx_2 != undefined) {
						  window.ctx_2.destroy();
					  }
	  
					  window.ctx_2 = new Chart(ctx, {
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

		  //window.print();
	 </script>
