<div class="container-fluid">
    @forelse ($painting_processes as $painting)
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-6" style="font-size: 10pt;">
                        <b>{{ $painting->production_order }}</b> <br>
                        <span><b>{{ $painting->item_code }}</b> - {{ $painting->description }}</span>
                        <div class="row">
                            <div class="col-6">
                                <span>Qty to manufacture: {{ $painting->qty_to_manufacture }}</span>
                            </div>
                            <div class="col-6">
                                <span>Currently in progress: <b>{{ $painting->good }}</b></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 d-flex flex-row justify-content-start align-items-center">
                        @php
                            $disable_restart = ($painting->status != 'In Progress') ? 'disabled' : '';
                        @endphp
                        <div class="col-4 p-0">
                            <button type="button" class="btn btn-block btn-danger sub-btn restart-task-btn" style="background-color: #00838f;" data-timelog-id="{{ $painting->time_log_id }}" {{ $disable_restart }}>
                                <i class="now-ui-icons loader_refresh" style="padding: 3px;"></i>
                                <br><span style="font-size: 8pt;">Restart</span>
                            </button>
                        </div>

                        <div class="col-4 p-0">
                            <button type="button" class="btn btn-block sub-btn quality-inspection-btn" data-timelog-id="{{ $painting->time_log_id }}" data-production-order="{{ $painting->production_order }}" data-processid="{{ $painting->process_id }}" data-inspection-type="Random Inspection" style="background-color: #f57f17;">
                              <i class="now-ui-icons ui-1_check" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Quality Check</span>
                            </button>
                        </div>

                        <div class="col-4 p-0">
                            <button type="button" class="btn btn-block btn-danger sub-btn" id="enter-reject-btn" data-process-name="Loading" data-production-order="{{ $painting->production_order }}" data-timelog-id="{{ $painting->time_log_id }}" data-good="{{ $painting->good }}">
                                <i class="now-ui-icons ui-1_simple-remove" style="font-size: 13pt;"></i><br><span style="font-size: 8pt;">Enter Reject</span>
                              </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="row">
            <div class="card" style="min-height: 500px;">
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
    background-size: 28%;
    background-repeat: no-repeat;
    background-position: center; 
  }

  .qc_failed{
    background-image: url("{{ asset('img/x.png') }}");
    background-size: 20%;
    background-repeat: no-repeat;
    background-position: center; 
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