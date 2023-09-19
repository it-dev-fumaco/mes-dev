@if ($operation_details->operation_name == 'Fabrication')
<div class="row mt-1 mb-0 mr-0 ml-0 p-0">
    <div class="col-md-2 p-2">
        <div class="card m-0" style="background-color:#D5D8DC; min-height: 780px;">
            <div class="card-header p-2">
                <h6 class="text-center m-0 p-0 font-weight-bolder">Workstation</h6>
            </div>
            <div class="card-body overflow-auto pt-0" style="height: 740px;">
                <ul class="nav flex-column font-weight-bolder" role="tablist" style="font-size: 10pt;"
                    id="workstation-tabs">
                    @foreach($workstation_list as $row)
                    <li class="nav-item text-center" style="cursor: pointer;">
                        <a class="nav-link {{ ($loop->first) ? 'active' : '' }}"
                            data-workstation-id="{{ $row->workstation_id }}" data-toggle="tab">{{ $row->workstation_name
                            }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-10 m-0 p-0">
        <div id="loader-wrapper" hidden>
            <div id="loader"></div>
            <div class="loader-section section-left" style="border: 8px solid white;"></div>
            <div class="loader-section section-right" style="border: 8px solid white;"></div>
        </div>
        <div class="scrolling-wrapper row flex-row flex-nowrap m-0 p-0" style="overflow-x: auto;"
            id="machine-schedule-div-content"></div>
    </div>
</div>
@endif
@if (strpos($operation_details->operation_name, 'Assembly'))
<div class="row mt-1 mb-0 mr-0 ml-0 p-0">
    <div class="col-md-2 p-2">
        <div class="card m-0" style="background-color:#D5D8DC; min-height: 800px;">
            <div class="card-header" style="margin-top: -15px;">
                <h5 class="card-title text-center font-weight-bold" style="font-size: 13px;">Unassigned Prod. Order(s)
                </h5>
            </div>
            <div class="card-body custom-sortable custom-sortable-connected overflow-auto" id="unassigned"
                style="height: 740px;">
                @foreach ($production_machine_board['unassigned_production'] as $i => $row)
                @php
                if($row->status == 'Not Started'){
                $b = 'secondary text-white';
                }elseif($row->status == 'In Progress'){
                $b = 'warning';
                }else{
                $b = 'success text-white';
                }
                @endphp
                <div class="card bg-{{ $b }} view-production-order-details"
                    data-production-order="{{ $row->production_order }}" data-position="{{ $i + 1 }}"
                    data-card="unassigned">
                    <div class="card-body">
                        <div class="pull-right">
                            <span class="badge badge-primary badge-number" style="font-size: 8pt;"></span>
                        </div>
                        <span class="d-block font-weight-bold" style="font-size: 9pt;">{{ $row->production_order }} [{{
                            $row->sales_order }}{{ $row->material_request }}]</span>
                        <small class="d-block" style="font-size: 7pt;">{{ $row->customer }}</small>
                        <span class="d-block mt-1" style="font-size: 8pt;"><b>{{ $row->item_code }}</b> {!!
                            strtok(strip_tags($row->description), ',') !!}</span>
                        <span class="d-block" style="font-size: 8pt;">[{{ $row->qty_to_manufacture }} {{ $row->stock_uom
                            }}]</span>
                        <span class="d-block mt-1 font-weight-bold text-white" style="font-size: 8pt;">{{
                            ($row->classification) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="card-footer card-footer__events text-white p-0 m-0" style="background-color: #254d78;">
                <div class="d-flex flex-row m-0 text-uppercase">
                    <div class="p-2 col-md-6 text-left" style="font-size: 8pt;">Total Qty: <b>{{
                            number_format(collect($production_machine_board['unassigned_production'])->sum('qty_to_manufacture'))
                            }}</b></div>
                    <div class="p-2 col-md-6 text-right" style="font-size: 8pt;">On Queue: <b>{{
                            number_format(count($production_machine_board['unassigned_production'])) }}</b></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-10 m-0 p-0">
        <div class="scrolling-wrapper row flex-row flex-nowrap m-0 p-0" style="overflow-x: auto;">
            @foreach ($production_machine_board['assigned_production_orders'] as $machine)
            <div class="col-md-2 p-2" style="min-width: 350px !important;">
                <div class="card m-0" style="background-color:#D5D8DC; min-height: 800px;">
                    <div class="card-header" style="margin-top: -15px;">
                        <h5 class="card-title text-center font-weight-bold" style="font-size: 13px;">{{
                            $machine['machine_name'] }}</h5>
                        <div class="pull-right p-1" style="margin-top: -40px;">
                            <img src="{{ asset('img/print.png') }}" width="25" class="print-schedule"
                                data-machine="{{ $machine['machine_code'] }}">
                        </div>
                    </div>
                    <div class="card-body custom-sortable custom-sortable-connected overflow-auto mb-0"
                        id="{{ $machine['machine_code'] }}" style="height: 740px;">
                        @foreach ($machine['production_orders'] as $j => $row)
                        @php
                        $b = 'success text-white';
                        if (in_array($row->production_order, $machine['on_going_production_orders'])) {
                        $b = ' active-process';
                        } else {
                        if($row->status == 'Not Started'){
                        $b = 'secondary text-white';
                        }elseif($row->status == 'In Progress'){
                        $b = 'warning';
                        }else{
                        $b = 'success text-white';
                        }
                        }
                        @endphp
                        <div class="card bg-{{ $b }} view-production-order-details"
                            data-production-order="{{ $row->production_order }}" data-position="{{ $j + 1 }}"
                            data-card="{{ $row->machine_code }}">
                            <div class="card-body">
                                <div class="pull-right">
                                    <span class="badge badge-primary badge-number" style="font-size: 8pt;">{{
                                        $row->order_no }}</span>
                                </div>
                                <span class="d-block font-weight-bold" style="font-size: 9pt;">{{ $row->production_order
                                    }} [{{ $row->sales_order }}{{ $row->material_request }}]</span>
                                <small class="d-block" style="font-size: 7pt;">{{ $row->customer }}</small>
                                <span class="d-block mt-1" style="font-size: 8pt;"><b>{{ $row->item_code }}</b> {!!
                                    strtok(strip_tags($row->description), ',') !!} </span>
                                <span class="d-block" style="font-size: 8pt;">[{{ $row->qty_to_manufacture }} {{
                                    $row->stock_uom }}]</span>
                                <span class="d-block mt-1 font-weight-bold" style="font-size: 8pt;">{{
                                    ($row->classification) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer card-footer__events text-white p-0 m-0" style="background-color: #254d78;">
                        <div class="d-flex flex-row m-0 text-uppercase">
                            <div class="p-2 col-md-6 text-left" style="font-size: 8pt;">Total Qty: <b>{{
                                    number_format(collect($machine['production_orders'])->sum('qty_to_manufacture'))
                                    }}</b> unit(s)</div>
                            <div class="p-2 col-md-6 text-right" style="font-size: 8pt;">On Queue: <b>{{
                                    number_format(count($machine['production_orders'])) }}</b></div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<script>
    var scheduled_date = $('#schedule-date-val').val();
    function update_badge_number(id){
        $(id).children().each(function(index){
            $(this).find('.badge-number').text( (index + 1));
        });
    }
    
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
                scheduled_date
              },
              success:function(data){
                console.log(data);
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
</script>