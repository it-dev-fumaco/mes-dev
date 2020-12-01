@extends('layouts.user_app', [
'namePage' => 'Fabrication',
'activePage' => 'assembly_conveyor_assignment',
])

@section('content')
<div class="panel-header">
  <div class="header text-center" style="margin-top: -70px;">
    <div class="row">
      <div class="col-md-12">
        <table style="text-align: center; width: 60%; margin-left: 50px;">
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
              <h2 class="title text-left" style="margin-left: 20px; margin: auto 20pt; font-size: 19pt;">Assembly Schedule [{{ $scheduled_date }}]</h2>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="content" style="margin-top: -200px;">
	<div class="row">
		<div class="col-md-12">
			<div class="card p-2">
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1" role="tab" aria-controls="step1" aria-selected="true">Conveyor Assignment</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" id="step2-tab" data-toggle="tab" href="#step2" role="tab" aria-controls="step2" aria-selected="false">Production Output</a>
					</li>
				</ul>
				<input type="hidden" id="scheduled-date" value="{{ $scheduled_date }}">
				<div class="tab-content" style="min-height: 620px;">
					<div class="tab-pane active" id="step1" role="tabpanel" aria-labelledby="step1-tab">
					  <div class="row m-0 p-0">
						 	<div class="col-md-2 p-2">
								<div class="card m-0" style="background-color:#D5D8DC; min-height: 800px;">
									<div class="card-header" style="margin-top: -15px;">
										<h5 class="card-title text-center" style="font-size: 15px;"><b>Unassigned Prod. Order(s)</b></h5>
									</div>
									<div class="card-body custom-sortable custom-sortable-connected" id="unassigned" style="min-height: 740px;">
										@foreach ($unassigned_production as $i => $row)
										<div class="card bg-white view-production-order-details" data-production-order="{{ $row->production_order }}" data-position="{{ $i + 1 }}" data-card="unassigned">
											<div class="card-body">
												<div class="pull-right">
													<span class="badge badge-primary badge-number" style="font-size: 9pt;"></span>
												</div>
												<span class="d-block font-weight-bold" style="font-size: 11pt;">{{ $row->production_order }} [{{ $row->sales_order }}{{ $row->material_request }}]</span>
												<span class="d-block mt-1 font-italic">{{ $row->item_code }} [{{ $row->qty_to_manufacture }} {{ $row->stock_uom }}]</span>
											</div>
										</div>
										@endforeach
									</div>
								</div>
							</div>
							<div class="col-md-10 m-0 p-0">
								<div class="scrolling-wrapper row flex-row flex-nowrap m-0 p-0" style="overflow-x: auto;">
									@foreach ($assigned_production_orders as $machine)
									<div class="col-md-2 p-2" style="min-width: 350px !important;">
										<div class="card m-0" style="background-color:#D5D8DC; min-height: 800px;">
											<div class="card-header" style="margin-top: -15px;">
												<h5 class="card-title text-center" style="font-size: 15px;"><b>{{ $machine['machine_name'] }}</b></h5>
												<div class="pull-right p-1" style="margin-top: -40px;">
													<img src="{{ asset('img/print.png') }}" width="25" class="print-schedule" data-machine="{{ $machine['machine_code'] }}">
												</div>
											</div>
											<div class="card-body custom-sortable custom-sortable-connected" id="{{ $machine['machine_code'] }}" style="min-height: 740px;">
												@foreach ($machine['production_orders'] as $j => $row)
												<div class="card bg-white view-production-order-details" data-production-order="{{ $row->production_order }}" data-position="{{ $j + 1 }}" data-card="{{ $row->machine_code }}">
													<div class="card-body">
														<div class="pull-right">
															<span class="badge badge-primary badge-number" style="font-size: 9pt;">{{ $row->order_no }}</span>
														</div>
														<span class="d-block font-weight-bold" style="font-size: 11pt;">{{ $row->production_order }} [{{ $row->sales_order }}{{ $row->material_request }}]</span>
														<span class="d-block mt-1 font-italic">{{ $row->item_code }} [{{ $row->qty_to_manufacture }} {{ $row->stock_uom }}]</span>
													</div>
												</div>
												@endforeach
											</div>
										</div>
									</div>
									@endforeach
								</div>
							</div>
					  	</div>
					</div>
					<div class="tab-pane" id="step2" role="tabpanel" aria-labelledby="step2-tab">
					
					</div>
				</div>
			</div>
		</div>
  </div>
</div>


<iframe id="frame-print" style="display: none;"></iframe>
@endsection

@section('script')
<script>
  	$(document).ready(function(){
		var scheduled_date = $('#scheduled-date').val();
		$( ".custom-sortable" ).sortable({
			connectWith: ".custom-sortable-connected",
			appendTo: 'body',
			helper: 'clone',
			update:function(event, ui) {
				var card_id = this.id;
				$(this).children().each(function(index){
					if ($(this).attr('data-position') != (index + 1) || $(this).attr('data-card') != card_id) {
						$(this).attr('data-position', (index + 1)).attr('data-card', card_id).addClass('updated');
					}
				});
			
				var pos = [];
				$('.updated').each(function(){
					var production_order = $(this).attr('data-production-order');
					var order_no = $(this).attr('data-position');
					pos.push([production_order, order_no, card_id]);
					$(this).removeClass('updated');
				});

				if (pos) {
					$.ajax({
						url:"/update_conveyor_assignment",
						type:"POST",
						dataType: "text",
						data: {
							list: pos,
							scheduled_date: scheduled_date
						},
						success:function(data){
							//console.log(data);
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
				update_badge_number('#' + this.id);
			}
		}).disableSelection();

		$(document).on('click', '.print-schedule', function(e){
			e.preventDefault();
			$('#frame-print').attr('src', '/assembly/print_machine_schedule/' + scheduled_date + '/' + $(this).data('machine'));
		});

		function update_badge_number(id){
			$(id).children().each(function(index){
				$(this).find('.badge-number').text( (index + 1));
			});
		}
	
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
  });
</script>
@endsection
