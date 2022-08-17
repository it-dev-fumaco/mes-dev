@extends('painting_operator.app', [
    'namePage' => 'MES',
    'activePage' => 'painting_dashboard',
])

@section('content')
<div class="panel-header" style="border: 1px solid; min-height: 350px;">
  <div class="header">
    <div class="row" style="margin-top: -70px; margin-left: -160px;">
      <div class="col-md-8 text-white">  
          <table style="width: 85%; margin-left: 15px;">  
            <tr>  
              <td style="width: 50%; border-right: 5px solid;">
              <h2 class="title text-center" style="font-size:10px;">
                <div class="pull-right" style="margin-right: 30px;">
                  <span style="display: block; font-size: 17pt;">{{ date('M-d-Y') }}</span>
                  <span style="display: block; font-size: 13pt;">{{ date('l') }}</span>
                </div>
              </h2>
            </td>
            <td style="width: 50%;" style="pull-left">
              <h4 id="qwe" class="title text-center" style="margin: 1px auto; font-size: 25pt;margin-left:-100px;">-:--:-- --</h4>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
@include('painting_operator.modal_enter_operator_id')
@include('modals.search_productionorder')
<div class="content" style="margin-top: -100px; min-height: 10px;">

<div class="row" style="margin-top: -265px;">
  <div class="col-md-6 mx-auto">
    <div class="card" style="min-height: 500px;">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <h5 class="text-black mt-1 text-center text-uppercase font-weight-bold">
              Enter Biometric ID
            </h5>
            <div id="bio-scan-img">
              <center>
                <img src="{{ asset('img/tap.gif') }}" width="330" height="250" id="toggle-bio-numpad" style="margin: -15px 10px 50px 10px;">
              </center>
            </div>
          </div>
          <div class="col-md-10 offset-md-1" style="display: none;" id="bio-numpad">
            <form action="" method="post" id="login-form">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" name="user_id" class="form-control" id="bio-id" style="font-size: 15pt;" required>
                    </div>
                    </div>
                    <div id="bio-numpad">
                    <div class="text-center">
                        <div class="row1">
                            <span class="numpad num">1</span>
                            <span class="numpad num">2</span>
                            <span class="numpad num">3</span>
                        </div>
                        <div class="row1">
                            <span class="numpad num">4</span>
                            <span class="numpad num">5</span>
                            <span class="numpad num">6</span>
                        </div>
                        <div class="row1">
                            <span class="numpad num">7</span>
                            <span class="numpad num">8</span>
                            <span class="numpad num">9</span>
                        </div>
                        <div class="row1">
                            <span class="numpad" onclick="document.getElementById('bio-id').value=document.getElementById('bio-id').value.slice(0, -1);"><</span>
                            <span class="numpad num">0</span>
                            <span class="numpad" onclick="document.getElementById('bio-id').value='';">Clear</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10 offset-md-1">
                        <button type="submit" class="btn btn-block btn-primary btn-lg" id="submit-bio-btn">SUBMIT</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        </div>
      </div>
    </div>
  </div>

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
@endsection

@section('script')
<script src="{{ asset('/js/jquery.rfid.js') }}"></script>
<script>
    var now = new Date(<?php echo time() * 1000 ?>);
    function startInterval(){  
        setInterval('showTime();', 1000);
    }

    function showTime() {
        manilaTime = new Date();
        var clock = document.getElementById('qwe');
        if(clock){
            clock.innerHTML = manilaTime.toLocaleTimeString();//adjust to suit
        }

        var timeformat = manilaTime.toTimeString();
        $('.breaktime_input').each(function() {
            var div_id= "#" + $(this).data('divid');
            if($(this).data('timein') <= timeformat && $(this).data('timeout') >= timeformat ){
                $(div_id).show();
                $(div_id).addClass("blink_break");
            }else{
                $(div_id).hide();
                $("#div_id").removeClass("blink_break");
            }
        });
    }

    $('#login-form').submit(function(e){
        e.preventDefault();
        var operator_id = $('#bio-id').val();

        var data = {  
            operator_id: operator_id,
            process_name: 'Loading',
            _token: '{{ csrf_token() }}'
        }

        $.ajax({
            url:"/painting/login",
            type:"post",
            data: data,
            success:function(data){
                if (data.success < 1) {
                    showNotification("danger", data.message, "now-ui-icons travel_info");
                }else{
                    showNotification("success", 'Logging in. Please wait..', "now-ui-icons ui-1_check");
                    window.location.href = data.url;
                }
            }, 
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    });

    $('#toggle-bio-numpad').click(function(e){
        e.preventDefault();
        $('#bio-numpad').slideToggle();
        $('#bio-scan-img').slideToggle();
    });

    $(document).on('click', '#bio-numpad .num', function(e){
        e.preventDefault();
        var num = $(this).text();
        var current = $('#bio-id').val();
        var new_input = current + num;
        new_input = format(new_input.replace(/-/g, ""), [5], "-");
            
        $('#bio-id').val(new_input);
    });

    function format(input, format, sep) {
        var output = "";
        var idx = 0;
        for (var i = 0; i < format.length && idx < input.length; i++) {
            output += input.substr(idx, format[i]);
            if (idx + format[i] < input.length) output += sep;
            idx += format[i];
        }
    
        output += input.substr(idx);
    
        return output;
    }

    startInterval();
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
</script>
@endsection