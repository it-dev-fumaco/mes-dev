<div class="container-fluid p-0">
    @forelse ($painting_processes as $painting)
      @php
        // $unloaded_completed = isset($tl_array[$painting->production_order]['Completed']['Unloading']) ? $tl_array[$painting->production_order]['Completed']['Unloading'] : 0;
        // $unloaded_in_progress = isset($tl_array[$painting->production_order]['In Progress']['Unloading']) ? $tl_array[$painting->production_order]['In Progress']['Unloading'] : 0;

        // $loaded_completed = isset($tl_array[$painting->production_order]['Completed']['Loading']) ? $tl_array[$painting->production_order]['Completed']['Loading'] : 0;
        // $loaded_in_progress = isset($tl_array[$painting->production_order]['In Progress']['Loading']) ? $tl_array[$painting->production_order]['In Progress']['Loading'] : 0;

        $unloaded_completed = isset($qty_array[$painting->production_order]['Completed']['Unloading']) ? collect($qty_array[$painting->production_order]['Completed']['Unloading'])->sum('good') : 0;
        $unloaded_in_progress = isset($qty_array[$painting->production_order]['In Progress']['Unloading']) ? collect($qty_array[$painting->production_order]['In Progress']['Unloading'])->sum('good') : 0;

        $loaded_completed = isset($qty_array[$painting->production_order]['Completed']['Loading']) ? collect($qty_array[$painting->production_order]['Completed']['Loading'])->sum('good') : 0;
        $loaded_in_progress = isset($qty_array[$painting->production_order]['In Progress']['Loading']) ? collect($qty_array[$painting->production_order]['In Progress']['Loading'])->sum('good') : 0;

        $total_unloaded = $unloaded_completed + $unloaded_in_progress;
        $total_loaded = $loaded_completed + $loaded_in_progress;

        $balance = $loaded_in_progress - $unloaded_in_progress;
        $balance = $balance > 0 ? $balance : 0;
      @endphp
        <div class="card m-1">
            <div class="card-body p-0 m-0">
                <div class="row p-1 m-0">
                    <div class="col-5 p-2" style="font-size: 8pt;">
                      <div class="waves-effect waves z-depth-4 d-flex flex-row justify-content-start align-items-center">
                        <div class="spinner-grow {{ $painting->status == 'Completed' ? 'd-none' : null }}" style="background-color: #F57F17">
                          <span class="sr-only">Loading...</span>
                        </div>&nbsp;<span style="font-size: 9pt;" class="font-weight-bold">{{ $painting->production_order }}</span>
                      </div>
                      <div class="text-justify">
                        <span><b>{{ $painting->item_code }}</b> - {{ $painting->description }}</span>
                      </div>
                      <div class="row mt-2">
                        <div class="col-4">
                          <span>To produce: <b>{{ $painting->qty_to_manufacture }}</b></span>
                        </div>
                        @if ($process_name == 'Loading')
                          @if ($total_unloaded > 0)
                            <div class="col-4">
                              <span class="badge badge-primary" style="font-size: 9pt;">Unloaded: <b>{{ $total_unloaded }}</b></span>
                            </div>
                          @endif
                        @else
                          @if ($painting->good > 0 && $painting->status != 'Completed')
                            <div class="col-4">
                              <span class="badge badge-primary" style="font-size: 9pt;">In Progress: <b>{{ $balance }}</b></span>
                            </div>
                          @endif
                        @endif
                        @if ($painting->reject > 0)
                          <div class="col-4">
                            <span class="badge badge-secondary" style="font-size: 9pt;">Reject: <b>{{ $painting->reject }}</b></span>
                          </div>
                        @endif
                      </div>
                      <div class="qc_passed p-0 {{ !isset($qa_check[$painting->time_log_id]) ? 'd-none' : null }}" style="margin-top: 10px; width: 40px; height: 40px;"></div>

                    </div>
                    <div class="col-2 p-0 d-flex flex-row justify-content-start align-items-center" style="font-size: 10pt;">
                      <div class="container text-center">
                        <h6 class="mt-2">
                          @if ($process_name == 'Loading')
                            In Progress
                          @else
                            Unloaded
                          @endif
                        </h6>
                        <h2 class="font-weight-bold">
                          @if ($process_name == 'Loading')
                              {{ $painting->good }}
                          @else
                              @if ($painting->status == 'Completed')
                                {{ isset($unloaded_per_time_log_id[$painting->time_log_id]) ? $unloaded_per_time_log_id[$painting->time_log_id][0]->good : 0 }}
                              @else
                                  {{ $total_unloaded }}
                              @endif
                          @endif
                        </h2>
                      </div>
                    </div>
                    <div class="col-5 p-2 d-flex flex-row justify-content-start align-items-center">
                        @php
                            $disable_restart = '';
                            if(isset($time_logs_unloading[$painting->production_order])){
                              if($time_logs_unloading[$painting->production_order][0]->good > 0){
                                $disable_restart = 'disabled';
                              }
                            }
                        @endphp
                        @if ($process_name == 'Loading')
                          <div class="col-4 p-0">
                            <button type="button" class="btn btn-block btn-danger sub-btn restart-task-btn" style="background-color: #00838f;" data-timelog-id="{{ $painting->time_log_id }}" {{ $disable_restart }}>
                              <i class="now-ui-icons loader_refresh" style="padding: 3px;"></i>
                              <br><span style="font-size: 8pt;">Restart</span>
                            </button>
                          </div>
                        @endif

                        <div class="col-4 p-0">
                            <button type="button" class="btn btn-block sub-btn quality-inspection-btn" data-timelog-id="{{ $painting->time_log_id }}" data-production-order="{{ $painting->production_order }}" data-processid="{{ $painting->process_id }}" data-inspection-type="Random Inspection" style="background-color: #f57f17;">
                              <i class="now-ui-icons ui-1_check" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Quality Check</span>
                            </button>
                        </div>

                        <div class="col-4 p-0">
                            <button type="button" class="btn btn-block btn-danger sub-btn" id="enter-reject-btn" data-process-name="Loading" data-production-order="{{ $painting->production_order }}" data-timelog-id="{{ $painting->time_log_id }}" data-good="{{ $balance }}">
                              <i class="now-ui-icons ui-1_simple-remove" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Enter Reject</span>
                            </button>
                        </div>

                        @if ($process_name == 'Unloading')
                          <div class="col-4 p-0">
                            @if ($painting->status == 'In Progress')
                              <button type="button" class="btn btn-block btn-warning end-task-btn sub-btn m-0" style="height: 90px;" data-timelog-id="{{ $painting->time_log_id }}" data-balance-qty="{{ $balance }}" data-processid="{{ $painting->process_id }}" data-process-name="{{ $process_name }}" data-production-order="{{ $painting->production_order }}">
                                <div class="waves-effect waves z-depth-4">
                                  <div class="spinner-grow">
                                    <span class="sr-only">Loading...</span>
                                  </div> <br>
                                  <span class="text-center font-weight-bold" style="color: #273746; font-size: 8pt;">In Progress</span>
                                </div>
                              </button>
                            @else
                              <button type="button" class="btn btn-block sub-btn" style="background-color: #28B463">
                                <i class="now-ui-icons ui-1_check" style="font-size: 13pt;"></i>
                                <br><span style="padding: 3px;">Completed</span>
                              </button>
                            @endif
                          </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        @if ($process_name == 'Loading')
          <div class="row">
            <div class="cards w-100" style="min-height: 500px;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="text-black mt-1 text-center text-uppercase">
                                Scan Job Ticket
                            </h5>
                            <div id="jt-scan-img">
                                <center>
                                <img src="{{ asset('img/tap.gif') }}" width="330" height="250" id="toggle-jt-numpad" style="margin: -15px 10px 50px 10px;">
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        @else
          <div class="row">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 p-5 text-center">
                    No In Progress Task(s)
                  </div>
                </div>
              </div>
            </div>
          </div> 
        @endif
    @endforelse
</div>

<style type="text/css">
    @-webkit-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @-moz-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @-o-keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    @keyframes blinker_break {
      from { background-color: #fa764b; }
      to { background-color: inherit; }
    }
    
    .blink_break{
      text-decoration: blink;
      -webkit-animation-name: blinker;
      -webkit-animation-duration: 3s;
      -webkit-animation-iteration-count:infinite;
      -webkit-animation-timing-function:ease-in-out;
      -webkit-animation-direction: alternate;
    }
  .qc_passed{
    background-image: url("{{ asset('img/chk.png') }}");
    background-size: 100%;
    background-repeat: no-repeat;
    background-position: left; 
  }

  .qc_failed{
    background-image: url("{{ asset('img/x.png') }}");
    background-size: 100%;
    background-repeat: no-repeat;
    background-position: left; 
  }
  
  .tap_here {
    animation: bounce 1s linear infinite;
  }

  .active-process {
    background-color: #FFC107;
    color: #000000;
    animation: blinkingBackground 2.5s linear infinite;
  }

  @keyframes blinkingBackground{
    0%    { background-color: #ffffff;}
    25%   { background-color: #FFC107;}
    50%   { background-color: #ffffff;}
    75%   { background-color: #FFC107;}
    100%  { background-color: #ffffff;}
  }

  .dot {
    height: 20px;
    width: 20px;
    background-color: #bbb;
    border-radius: 50%;
    display: inline-block;
  }

  .text-blink {color: orange;
    animation: blinker 1s linear infinite;
  }

  @keyframes blinker {  
    50% { opacity: 0; }
  }

  .numpad-div .row1{
    -webkit-user-select: none; /* Chrome/Safari */        
    -moz-user-select: none; /* Firefox */
    -ms-user-select: none; /* IE10+ */
    /* Not implemented yet */
    -o-user-select: none;
    user-select: none;   
  }

  .numpad{
    display: inline-block;
    border: 1px solid #333;
    border-radius: 5px;
    text-align: center;
    width: 27%;
    height: 27%;
    line-height: 60px;
    margin: 3px;
    font-size: 15pt;
    color: inherit;
    background: rgba(255, 255, 255, 0.7);
    transition: all 0.3s ease-in-out;
  }

  .numpad:active,
  .numpad:hover {
    cursor: pointer ;
    box-shadow: inset 0 0 2px #000000;
  }

  .breadcrumb-c {
    font-size: 8pt;
    font-weight: bold;
    padding: 0;
    background: transparent;
    list-style: none;
    overflow: hidden;
    margin-top: 3px;
    margin-bottom: 3px;
    width: 100%;
    border-radius: 4px;
  }

  .breadcrumb-c>li {
    display: table-cell;
    vertical-align: top;
    width: 0.8%;
  }

  .breadcrumb-c>li+li:before {
    padding: 0;
  }

  .breadcrumb-c li a {
    color: white;
    text-decoration: none;
    padding: 10px 0 10px 5px;
    position: relative;
    display: inline-block;
    width: calc( 100% - 10px );
    background-color: hsla(0, 0%, 83%, 1);
    text-align: center;
    text-transform: capitalize;
  }

  .breadcrumb-c li.completed a {
    background: brown;
    background: hsla(153, 57%, 51%, 1);
  }

  .breadcrumb-c li.completed a:after {
    border-left: 30px solid hsla(153, 57%, 51%, 1);
  }

  .breadcrumb-c li.active a {
    background: #ffc107;
  }

  .breadcrumb-c li.active a:after {
    border-left: 30px solid #ffc107;
  }

  .breadcrumb-c li:first-child a {
    padding-left: 1px;
  }

  .breadcrumb-c li:last-of-type a {
    width: calc( 100% - 38px );
  }

  .breadcrumb-c li a:before {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid white;
    position: absolute;
    top: 50%;
    margin-top: -50px;
    margin-left: 1px;
    left: 100%;
    z-index: 1;
  }

  .breadcrumb-c li a:after {
    content: " ";
    display: block;
    width: 0;
    height: 0;
    border-top: 50px solid transparent;
    border-bottom: 50px solid transparent;
    border-left: 30px solid hsla(0, 0%, 83%, 1);
    position: absolute;
    top: 50%;
    margin-top: -50px;
    left: 100%;
    z-index: 2;
  }

  .truncate {
    white-space: nowrap;
    text-overflow: ellipsis;
  }

  .scrolltbody tbody {
    display:block;
    height:300px;
    overflow:auto;
  }
  .scrolltbody thead, .scrolltbody tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;
  }
  .scrolltbody thead {
    width: calc(100%)
  }
  #powder-record-modal .form-control {
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: none;
  margin-bottom: 15px;
  }
</style>

<script>
    $(document).ready(function (){
        $('#toggle-jt-numpad').click(function(e){
            e.preventDefault();
            $('#search-jt-Modal').modal('show');
        });
    });
</script>