@foreach ($data as $row)
<div class="col-md-2 p-2 mb-0" style="min-width: 350px !important;">
	<div class="card m-0 p-0" style="background-color:#D5D8DC; min-height: 780px;">
		<div class="card-header bg-primary p-0" style="border: 1px solid transparent;">
			<h5 class="card-title text-center font-weight-bold m-2 text-truncate text-uppercase" style="font-size: 10pt;">{{ $row['process_name'] }}</h5>
			<div class="pull-right p-1" style="margin-top: -37px;">
				<button type="button" class="btn btn-neutral btn-icon btn-round btn-sm view-btn-modal p-0 m-0" style="border-radius: 1em;" data-process-id="{{ $row['process_id'] }}" data-process-name="{{ $row['process_name'] }}" >
					<i style="font-size: 10pt;" class="now-ui-icons ui-1_zoom-bold"></i>
				 </button>
			</div>
		</div>
		<div class="card-body custom-sortable custom-sortable-connected overflow-auto p-0 m-0" style="height: 740px;">
			<div class="card-body connectedSortable sortable_list scrolling inner sorrtt p-2" style="height: 707px;" id="{{ $row['process_id'] }}" >
        @foreach($row['tasks'] as $i => $task)
        @php
          $bg_color = null;
          if(date('Y-m-d', strtotime($task['planned_start_date'])) < date('Y-m-d', strtotime(\Carbon\Carbon::now()))){
            $bg_color = "#ec7063";
          }

          if($task['status'] == 'Completed'){
            $bg_color = "#58d68d";
          }
        @endphp
				<div class="card p-0 m-0 {{ ($task['status'] == 'In Progress') ? 'text-blink' : '' }} view-details {{ $task['job_ticket_status'] }}" style="background-color: {{ $bg_color }}" data-position="{{ $i + 1 }}" data-card="{{ $row['process_id'] }}" data-index="{{ $task['job_ticket_id'] }}">
          <span class="d-none timelog-status">{{ $task['status'] }}</span>
          <span class="d-none timelog-id">{{ $task['timelog_id'] }}</span>
          <span class="d-none remarks">{{ $task['remarks'] }}</span>
          <span class="d-none workstation-id">{{ $task['workstation_id'] }}</span>
          <span class="d-none jobticket-id">{{ $task['job_ticket_id'] }}</span>
          <span class="d-none production-order">{{ $task['production_order'] }}</span>
          <span class="d-none workstation">{{ $task['workstation'] }}</span>
          <span class="d-none planned-start-date">{{ $task['planned_start_date'] }}</span>
          <span class="d-none delivery-date">{{ $task['delivery_date'] }}</span>
          <span class="d-none process-name">{!! $row['process_name'] !!}</span>
          <span class="d-none reference-no">{{ (!$task['sales_order']) ? $task['material_request'] : $task['sales_order'] }}</span>
          <span class="d-none customer">{{ $task['customer'] }}</span>
          <span class="d-none item-code">{{ $task['item_code'] }}</span>
          <span class="d-none item-description">{!! $task['description'] !!}</span>
          <span class="d-none qty-to-manufacture">{{ number_format($task['qty_to_manufacture']) }}</span>
          <span class="d-none completed-qty">{{ number_format($task['completed_qty']) }}</span>
          <span class="d-none stock-uom">{{ $task['stock_uom'] }}</span>
          <span class="d-none machine-code">{{ $task['machine_code'] }}</span>
          <span class="d-none machine-name">{{ $task['machine_name'] }}</span>
          <span class="d-none start-time">{{ $task['from_time'] }}</span>
          <span class="d-none end-time">{{ $task['to_time'] }}</span>
          <span class="d-none duration-in-mins">{{ $task['duration_in_mins'] }}</span>
          <span class="d-none cycle-time-in-mins">{{ $task['cycle_time_in_mins'] }}</span>
          <span class="d-none operator-name">{{ $task['operator_name'] }}</span>
					<div class="card-body pb-2 pt-2 pr-1 pl-1" style="font-size: 9pt;">
						<table style="width: 100%;">
							<col style="width: 65%;">
							<col style="width: 25%;">
							<col style="width: 10%;">
						  <tr>
							 	<td class="text-left">
									<span class="hvrlink font-weight-bold">{{ $task['production_order'] }} - {{ (!$task['sales_order']) ? $task['material_request']: $task['sales_order'] }}</span>
									<div class="details-pane">
								  	<h5 class="title">{{ $task['production_order'] }}<span>[{{ (!$task['status']) ? 'Not Started' : $task['status'] }}]</span></h5>
										<p class="desc">
                      <span class="font-weight-bold d-block">Process: {!! $row['process_name'] !!}</span>
                      <span class="d-block"><b>{{ $task['item_code'] }}:</b> {!! $task['description'] !!}</span>
                      <span class="d-block">Qty: <b>{{ number_format($task['qty_to_manufacture']) }} {{ $task['stock_uom'] }}</b></span>
                      <span class="d-block font-weight-bold">{{ (!$task['sales_order']) ? $task['material_request'] : $task['sales_order'] }}</span>
                      <span class="d-block">Customer: <b>{{ $task['customer'] }}</b></span>
                      <span class="d-block">Planned Start Date: <b>{{ $task['planned_start_date'] }}</b></span>
										</p>
									</div>
								</td>
								<td class="text-center font-weight-bold">
                  <span style="font-size: 8pt;">{{ number_format($task['completed_qty']) }} {{ $task['stock_uom'] }}</span>
                </td>
                <td class="text-right">
                  <span class="pull-right badge badge-primary" style="font-size: 8pt;">{{ $task['order_no'] }}</span>
                </td>
              </tr>
            </table>
          </div>
        </div>
        @endforeach
      </div>
      <div class="card-footer card-footer__events text-white p-0 m-0" style="background-color: #254d78;">
        <div class="d-flex flex-row m-0 text-uppercase">
          <div class="p-2 col-md-6 text-left" style="font-size: 8pt;">Total Qty: <b>{{ number_format($row['total_qty']) }}</b> unit(s)</div>
          <div class="p-2 col-md-6 text-right" style="font-size: 8pt;">On Queue: <b>{{ number_format($row['task_count']) }}</b></div>
        </div>
      </div> 
		</div>
	</div>
</div>
@endforeach

<style type="text/css">
  .scrolling table {
    width: 100%;
  }

  .scrolling .td, .th {
    padding: 10px;
  }
  
  .parent-td{
    padding: 10px;
    width: 4px;
    float: left;
  }

  .scrolling .th {
    position: relative;
    left: 0;
  }

  .outer {
    position: relative
  }

  .inner {
    overflow-x: auto;
  }

  .nav-item .active{
    background-color: #f96332;
    font-weight: bold;
    color:#ffffff;
  }
  /** page structure **/
  .thumbs ul{
     padding: 0;
     margin: 0;
  }
  
  .thumbs ul li {
    display: block;
    position: relative;
    float: left;
    margin: 0;
    padding: 0;
  }
  
  /** detail panel **/
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

  .dot {
    height: 12px;
    width: 12px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }

  .text-blink {
    color: black;
    animation: blinkingBackground 2s linear infinite;
  }

  .for-color-black{
    color: black;
  }

  @keyframes blinkingBackground{
    0%    { background-color: #ffffff;}
    25%   { background-color: #EB984E;}
    50%   { background-color: #ffffff;}
    75%   { background-color: #EB984E;}
    100%  { background-color: #ffffff;}
  }
  .modal-md {
      max-width: 35% !important;
  }
</style>

<script>
  $(function(){
    $( ".sortable_list" ).sortable({
      connectWith: ".connectedSortable",
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
          var name = $(this).data('index');
          pos.push([name, card_id]);
          $(this).removeClass('updated');
        });

        $.ajax({
          url:"/reorder_productions",
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
      },   
      receive: function() {
        showNotification("success", 'Process sucessfully updated.', "now-ui-icons ui-1_check");
        if($(this).hasClass("In Progress") || $(this).hasClass("Completed") ){
          $(this).sortable("cancel");
        }
      }       
      }).disableSelection();

      function showNotification(color, message, icon){
        $.notify({
          icon: icon,
          message: message
        },{
          type: color,
          timer: 3000,
          placement: {
            from: 'top',
            align: 'center'
          }
        });
      }
  });
</script>