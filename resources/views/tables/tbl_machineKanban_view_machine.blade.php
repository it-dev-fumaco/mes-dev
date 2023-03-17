<div class="scrolling outer" style="width: 100%">
  <div class="inner">
    <table style="width: 100%;">
      <tr>
        @foreach($machine_loads as $rowws)
        <td style="padding-right: 10px;">
          <div class="card justify-content-center" style="overflow-y: auto;width: 350px; padding:0px;">
            <div class="card-header justify-content-center"
              style="background-color: #f96332; color: white;line-height: 18px; margin-top: -10px;padding-bottom: 1px;">
              <h6 class="card-title text-center" style="font-size: 2vw;font-size: 13px;">
                <b>{{ $rowws['machine_code'] }} - {{ $rowws['machine_name'] }}</b>
              </h6>
            </div>
            <div class="card-body connectedSortable sortable_list" style="height: 500px; position: relative;"
              id="{{ $rowws['machine_code'] }}" data-mprocess="{{ $rowws['machine_process']}}" data-status="Accepted">
              @forelse($rowws['machine_load'] as $rowsss)
              @php
              if($rowsss->stat == 'Completed'){
                $colorme="#58d68d";
                $blickme="";
              }else if($rowsss->stat == 'In Progress'){
                $colorme="#EB984E";
                $blickme="text-blink";
              }else if(date('Y-m-d', strtotime($rowsss->planned_start_date)) == date('Y-m-d',
              strtotime($scheduleDate))){
                $colorme="";
                $blickme="";
              }else if(date('Y-m-d', strtotime($rowsss->planned_start_date)) != date('Y-m-d',
              strtotime($current_date))){
                $colorme="#ec7063";
                $blickme="";
              }else{
                $colorme="";
                $blickme="";
              }
              $convert_timee= $rowsss->duration;
              @endphp
              <div id="sched_orders" class="card {{ $rowsss->stat}} {{ $blickme }}" data-index="{{ $rowsss->jtname }}"
                data-position="{{ $rowsss->order_no}}" data-card="{{ $rowsss->machine }}" data-qtyaccepted="0"
                data-mprocess="{{ $rowsss->process_name }}" data-prod="{{ $rowsss->production_order }}"
                data-sales="{{ ($rowsss->sales_order == '')? $rowsss->material_request : $rowsss->sales_order  }}"
                data-customer="{{ $rowsss->customer }}" data-desc="{{ $rowsss->description }}"
                data-proditem="{{ $rowsss->item_code }}"
                data-qty="{{ number_format($rowsss->qty_to_manufacture) }} {{ $rowsss->stock_uom }}"
                data-cpt="{{ ($rowsss->remarks == 'Override')? number_format($rowsss->good_qty):number_format($rowsss->qty_to_manufacture) }} {{ $rowsss->stock_uom }}"
                data-process=" -[{{ $rowsss->process_name }}]" data-mprocessvalidation="{{ $rowsss->process_name }}"
                data-delivery="{{ date('m-d-Y', strtotime($rowsss->delivery_date))}}"
                data-planstartdate="{{ date('m-d-Y', strtotime($rowsss->planned_start_date)) }}"
                data-modaltitle="{{ $rowsss->production_order }} -[{{ $rowsss->process_name }}]"
                data-operatorname="{{ $rowsss->operator_name }}" data-machinecode="[ {{ $rowsss->machine }} ]"
                data-starttime="{{ date('Y-m-d h:i:s a', strtotime($rowsss->from_time)) }}"
                data-totime="{{ date('Y-m-d h:i:s a', strtotime($rowsss->to_time)) }}"
                data-duration="{{ $convert_timee }}" data-machinename="{{ $rowsss->machine_name }}"
                data-taskstatus="{{ $rowsss->stat }}" data-cycletime="{{ number_format($rowsss->good_qty) }}"
                style="background-color: {{ $colorme }};margin-top: -10px;margin-bottom: 10px;height: 30px;width: 300px;"
                data-ws="{{ $rowws[ 'workstation_name' ] }}" data-wsid="{{ $rowws['workstation_id'] }}"
                data-qty-accepted="{{ number_format($rowsss->qty_to_manufacture) }}"
                data-timelog="{{ $rowsss->time_log_id }}" data-remark="{{ $rowsss->remarks }}">
                <div class="card-body" style="font-size: 8pt;margin-top: -10px;">
                  <table style="width: 100%;">
                    <tr>
                      <td>
                        <span class="hvrlink"><b>{{ $rowsss->production_order }} - {{ ($rowsss->sales_order == "")?
                            $rowsss->material_request: $rowsss->sales_order }}</b></span>
                        <div class="details-pane">
                          <h5 class="title">{{ $rowsss->production_order }}<span>[{{ ($rowsss->stat == "Accepted") ?
                              "Not Started": $rowsss->stat }}]</span></h5>
                          <p class="desc">
                            <b>Process:</b> <b>{!! $rowsss->process_name !!}</b><br>
                            <b>{{ $rowsss->item_code }}:</b> {!! $rowsss->description !!}<br>

                            Qty: <b>{{ number_format($rowsss->qty_to_manufacture) }} {{ $rowsss->stock_uom }}</b><br>
                            <b>{{ ($rowsss->sales_order == "")? $rowsss->material_request : $rowsss->sales_order
                              }}</b><br>
                            Customer: <b>{{ $rowsss->customer }}</b><br>
                            Planned Start Date: <b>{{ date('m-d-Y', strtotime($rowsss->planned_start_date)) }}</b><br>

                          </p>
                        </div>

                      </td>
                      <td><b>{{ ($rowsss->good_qty == 0) ? "": $rowsss->good_qty }}&nbsp;{{ ($rowsss->good_qty == 0) ?
                          "": $rowsss->stock_uom }}</b></td>

                      <td colspan="3"><span class="pull-right badge badge-primary" style="font-size: 9pt;">{{
                          $rowsss->order_no }}</span></td>
                    </tr>

                  </table>
                </div>
              </div>
              @empty
              <div class="container text-center">
                No Production Order(s) Found.
              </div>
              @endforelse
            </div>
          </div>
        </td>
        @endforeach
      </tr>
    </table>
  </div>
</div>

<style type="text/css">
  .scrolling table {
    /*    table-layout: fixed;
*/
    width: 100%;
  }

  .scrolling .td,
  .th {
    padding: 10px;
    /*  width: 600px;
*/
  }

  .tdds {
    /*  padding-right: : 30px;

/*white-space: nowrap;
*/
  }

  .parent-td {
    padding: 10px;
    width: 4px;
    float: left;
  }

  .scrolling .th {
    position: relative;
    left: 0;
    width:
      /*600px*/
    ;
  }

  .outer {
    position: relative
  }

  .inner {
    overflow-x: auto;

  }

  .nav-item .active {
    background-color: #f96332;
    font-weight: bold;
    color: #ffffff;
  }

  /** page structure **/
  .thumbs ul {
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
    -webkit-box-shadow: 1px 3px 3px rgba(0, 0, 0, 0.4);
    -moz-box-shadow: 1px 3px 3px rgba(0, 0, 0, 0.4);
    box-shadow: 1px 3px 3px rgba(0, 0, 0, 0.4);
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
  span.hvrlink:hover+.details-pane {
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

  .for-color-black {
    color: black;
  }

  @keyframes blinkingBackground {
    0% {
      background-color: #ffffff;
    }

    25% {
      background-color: #EB984E;
    }

    50% {
      background-color: #ffffff;
    }

    75% {
      background-color: #EB984E;
    }

    100% {
      background-color: #ffffff;
    }
  }

  .modal-md {
    max-width: 35% !important;
  }
</style>