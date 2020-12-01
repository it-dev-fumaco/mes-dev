<div class="row">
	<div class="col-md-12" style="float: right;">
		<div style="float: right;margin-top: -60px;" >
		@if($machine_workstations->status == "On-going Maintenance")
            <label class="card-title" style="margin-right: 50px;">
            <span class="dot text-blink" style="background-color:#d35400;"></span> {{ $machine_workstations->status }}
            </label>
            @else
            <label class="card-title" style="margin-right: 50px;">
            <span class="dot" style="background-color: {{ $machine_workstations->status == 'Available' ? '#28B463' : '#717D7E' }};"></span> {{ $machine_workstations->status }}
            </label>
            @endif
        </div>

	</div>
    <div class="col-md-5" style="margin-top: -10px;">
    	<div class="text-center">
    	@php
         $img = $machine_workstations->image ? $machine_workstations->image: '/storage/machine/machine_default.png'
         @endphp
		<img src="{{ asset($img) }}" width="150" class="">
    	</div>
      	<div style="font-size: 9pt;text-align: center;">
			<label><b>{{ $machine_workstations->machine_name }}</b> &nbsp;({{ $machine_workstations->machine_code }})</label>
			<br><label><b>Model:</b> {{ $machine_workstations->model }}</label>
		</div>
		<div class="col-md-12 justify-content-center" style="margin: 0 auto;" >
                                                <div class="single-chart text-center" style="margin: 0 auto;">
                                                   <svg viewBox="0 0 36 36" class="circular-chart blue" style="margin: 0 auto;">
                                                   <path class="circle-bg"
                                                                       d="M18 2.0845
                                                                         a 15.9155 15.9155 0 0 1 0 31.831
                                                                         a 15.9155 15.9155 0 0 1 0 -31.831"
                                                                     />
                                                   <path class="circle"
                                                                       stroke-dasharray="{{ $percetage  > 101 ? '100': $percetage  }}, 100"
                                                                       d="M18 2.0845
                                                                         a 
                                                                         15.9155 15.9155 0 0 1 0 31.831
                                                                         a 15.9155 15.9155 0 0 1 0 -31.831"
                                                                     />
                                                   <text x="18" y="20.35" class="percentage" style="font-weight: bold;">{{ $percetage  > 101 ? '100': $percetage  }}%</text>
                                                   </svg>
                                              </div>
                                              <div class="text-center">

                                              
                                              	<span style="font-size: 8pt;"><b>Workstation:</b></span> <br>
                                                <span style="font-size: 8pt;">{{ $workstation }}</span> 
                                                <br>
                                                <span style="font-size: 8pt;"><b>Queuing Time:</b></span><br>
                                                <span style="font-size: 9pt;">{{ $quetime }}</span> 
                                                <br>
                                                <br><h6 class="text-center"><b>{{ $completedqty }}/ {{ $acceptedqty == 0 ? $completedqty:$acceptedqty }} Qty</b></h6>
                                             </div>
                                         </div>

    </div>
    <div class="col-md-7">
      	<ul class="nav nav-tabs" id="myTab" role="tablist" style="font-size: 8pt;">
          <li class="nav-item">
            <a class="nav-link active" id="breakdown-tab" data-toggle="tab" href="#breakdown" role="tab" aria-controls="breakdown" aria-selected="true">Breakdown</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="corrective-tab" data-toggle="tab" href="#corrective" role="tab" aria-controls="corrective" aria-selected="false">Corrective</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="schedule-tab" data-toggle="tab" href="#schedule" role="tab" aria-controls="schedule" aria-selected="true">Maintenance Schedule</a>
          </li>
        </ul>
         <div class="tab-content">
          <div class="tab-pane active" id="breakdown" role="tabpanel" aria-labelledby="breakdown-tab">
            <div class="table-responsive text-center">
            				<div style="padding-top: 20px;" id="breakdown_chart_chart">
            				  <select style="width: 27%;" id="chart_breakdown_month" class="month" data-machinecode="{{ $machine_workstations->machine_code }}" onchange="breakdown_filter('{{ $machine_workstations->machine_code }}')">
                                 <option value="01" {{ date('m') == 01 ? 'selected' : '' }}>January</option>
                                 <option value="02" {{ date('m') == 02 ? 'selected' : '' }}>February</option>
                                 <option value="03" {{ date('m') == 03 ? 'selected' : '' }}>March</option>
                                 <option value="04" {{ date('m') == 04 ? 'selected' : '' }}>April</option>
                                 <option value="05" {{ date('m') == 05 ? 'selected' : '' }}>May</option>
                                 <option value="06" {{ date('m') == 06 ? 'selected' : '' }}>June</option>
                                 <option value="07" {{ date('m') == 07 ? 'selected' : '' }}>July</option>
                                 <option value="08" {{ date('m') == 8 ? 'selected' : '' }}>August</option>
                                 <option value="09" {{ date('m') == 9 ? 'selected' : '' }}>September</option>
                                 <option value="10" {{ date('m') == 10 ? 'selected' : '' }}>October</option>
                                 <option value="11" {{ date('m') == 11 ? 'selected' : '' }}>November</option>
                                 <option value="12" {{ date('m') == 12 ? 'selected' : '' }}>December</option>
                              </select>
                              <select style="width: 15%;" id="chart_breakdown_year" class="year" data-machinecode="{{ $machine_workstations->machine_code }}" onchange="breakdown_filter('{{ $machine_workstations->machine_code }}')">
                                 <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                 <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                 <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                 <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                 <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                              </select>
                            </div>
              <canvas id="breakdown_chart" width="150" height="100" style="padding-top: 30px;"></canvas>
            </div>
          </div>
          <div class="tab-pane" id="corrective" role="tabpanel" aria-labelledby="corrective-tab">
            <div class="row" id="jt-details-col" style="margin-top: 10px;">
            	<div class="table-responsive text-center">
            		<div style="padding-top: 20px;" id="corrective_chart_chart">
            				  <select style="width: 27%;" id="chart_corrective_month" class="month" data-machinecode="{{ $machine_workstations->machine_code }}" onchange="corrective_filter('{{ $machine_workstations->machine_code }}')">
                                 <option value="01" {{ date('m') == 01 ? 'selected' : '' }}>January</option>
                                 <option value="02" {{ date('m') == 02 ? 'selected' : '' }}>February</option>
                                 <option value="03" {{ date('m') == 03 ? 'selected' : '' }}>March</option>
                                 <option value="04" {{ date('m') == 04 ? 'selected' : '' }}>April</option>
                                 <option value="05" {{ date('m') == 05 ? 'selected' : '' }}>May</option>
                                 <option value="06" {{ date('m') == 06 ? 'selected' : '' }}>June</option>
                                 <option value="07" {{ date('m') == 07 ? 'selected' : '' }}>July</option>
                                 <option value="08" {{ date('m') == 8 ? 'selected' : '' }}>August</option>
                                 <option value="09" {{ date('m') == 9 ? 'selected' : '' }}>September</option>
                                 <option value="10" {{ date('m') == 10 ? 'selected' : '' }}>October</option>
                                 <option value="11" {{ date('m') == 11 ? 'selected' : '' }}>November</option>
                                 <option value="12" {{ date('m') == 12 ? 'selected' : '' }}>December</option>
                              </select>
                              <select style="width: 15%;" id="chart_corrective_year" class="year" data-machinecode="{{ $machine_workstations->machine_code }}" onchange="corrective_filter('{{ $machine_workstations->machine_code }}')">
                                 <option value="2018" {{ date('Y') == 2018 ? 'selected' : '' }}>2018</option>
                                 <option value="2019" {{ date('Y') == 2019 ? 'selected' : '' }}>2019</option>
                                 <option value="2020" {{ date('Y') == 2020 ? 'selected' : '' }}>2020</option>
                                 <option value="2021" {{ date('Y') == 2021 ? 'selected' : '' }}>2021</option>
                                 <option value="2022" {{ date('Y') == 2022 ? 'selected' : '' }}>2022</option>
                              </select>
                            </div>
                        </div>
            	<canvas id="corrective_chart" width="150" height="100" style="padding-top: 30px;"></canvas>
            </div>
          </div>
          <div class="tab-pane" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
            <div class="row" id="jt-details-col" style="margin-top: 10px;">
            	<canvas id="schedule_chart" width="150" height="100" style="padding-top: 30px;"></canvas>
            </div>
          </div>
        </div>
       	
    </div>
  </div>





