
@foreach($scheduled as $r)
         <td class="td">
         <div class="card border-danger">
            <div class="card-header">
              <h5 class="card-title text-center" style="font-size: 16pt;">
                <img src="{{ asset('img/calendar4.png') }}" width="20">
                <span class="goto_machine_kanban" style="cursor: pointer;" data-date="{{ date('Y-m-d', strtotime($r['schedule'])) }}">
                  {{ date('D, M-d-Y', strtotime($r['schedule'])) }} <span style="font-size: 12pt;"><b>{{ (date('Y-m-d') == date('Y-m-d', strtotime($r['schedule']))) ? '[Today]' : '' }}</b></span>
                </span> 
                
                  @if(date('Y-m-d', strtotime($r['schedule'])) >= date('Y-m-d'))
                  <img src="{{ asset('img/scheduling.png') }}" width="40" class="goto_machine_kanban" style="cursor: pointer;" data-date="{{ date('Y-m-d', strtotime($r['schedule'])) }}">
                  <a href="/print_job_tickets/{{ date('Y-m-d', strtotime($r['schedule'])) }}" target="_blank">
                    <img src="{{ asset('img/print.png') }}" width="40">
                  </a>
                  @else
                  <img src="{{ asset('img/down.png') }}" width="40">
                  @endif
              </h5>
            </div>
            <div class="card-body sortable_list connectedSortable sorrtt" id="{{ $r['schedule'] }}" style="height: 750px; position: relative; overflow-y: auto;">
              @foreach($r['orders'] as $i => $order)
              <div class="card {{ $order['status'] }} border-danger" data-index="{{ $order['id'] }}" data-position="{{ $order['order_no'] }}" data-card="{{ $r['schedule'] }}" data-name="{{ $order['production_order'] }}" style="background-color: {{ $order['status'] == 'In Process' ? '#EB984E' : ''}};">
                <div class="card-body" style="font-size: 8pt;">
                  <table style="width: 100%;">
                    
                    <tr>
                      <td colspan="2"><b>{{ $order['name'] }}</b> [{{ $order['status'] }}] {!! $order['batch'] !!}</td>
                      <td><span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $order['order_no'] }}</span></td>
                    </tr>
                    @if($order['customer'])
                    <tr>
                      <td colspan="3">Customer: <b>{{ $order['customer'] }}</b><br>Delivery Date: <b>{{ date('M-d-Y', strtotime($order['delivery_date'])) }}</b></td>
                    </tr>
                    @endif
                    <tr>
                      <td colspan="2"><b>{{ $order['production_item'] }}</b> - {{ $order['description'] }}</td>
                      <td><input type="hidden" class="countqty" name="countqty" value="{{ $order['qty'] }}">
                      </td>
                      
                    </tr>
                    <tr>
                      <td>Qty: <span style="font-size: 9pt"><b>{{ number_format($order['qty']) }} {{ $order['stock_uom'] }}</b></span></td>
                      <td>CTD Qty: <span style="font-size: 9pt"><b>{{ number_format($order['produced_qty']) }} {{ $order['stock_uom'] }}</b></span></td>
                      <td><span class="pull-right"><b>{{ $order['classification'] }}</b></span></td>
                    </tr>
                    <tr>
                      <td colspan="2"></td>
                      <td style="float: right;"><a href="/single_print_job_ticket/{{ $order['name'] }}" target="_blank"><img src="{{ asset('img/print.png') }}" width="25"></a></td>
                      
                    </tr>
                  </table>
                </div>
              </div>
              
              
              @endforeach

            </div>
            {{-- <div class="card-footer text-muted card-footer_events" style="background-color: #254d78;color: white;">
            <div class="progress">
              <div id="dynamic{{ $r['schedule'] }}" class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <span id="current-progress{{ $r['schedule'] }}" style="color:black;"></span>
              </div>
            </div>
            <input type="hidden" value="{{ $r['percentage'] }}" id="processbarstattus{{ $r['schedule'] }}">
            <input type="hidden" value="{{ $r['max_qty'] }}" id="maxqty{{ $r['schedule'] }}">
                  <h6 style="color: white;height: 2px; font-size: 8pt; display:inline;margin-top:2px;" class="estimated">Estimated Capacity- {{ $r['max_qty'] }} PC(S)</h6>
                  <h6 style="color: white;height: 2px; font-size: 8pt; display:inline-block; float:right;margin-top:5px;" id="totalqty{{ $r['schedule'] }}">Total Qty - {{ $r['total_qty'] }} PC(S) </h6>
            </div> --}}
    
          </div>
        </td>
      
      @endforeach


      <style type="text/css">
  .scrolling table {
    table-layout: fixed;
    width: 100%;
}
.scrolling .td, .th {
    vertical-align: top;
  padding: 10px;
  width: 450px;
}

.scrolling .th {
  position: absolute;
  left: 0;
  width: 450px;
}
.outer {
  position: relative
}
.inner {
  overflow-x: auto;
  overflow-y: visible;
  margin-left: 450px;
}
.perc {position:absolute; display:none; top: 0; line-height:20px; right:10px;color:black; font-weight:bold;}
.container1 {
    position: relative;
    width: 100%;
    height: 20px;
    background-color: white;
    border-radius: 4px;
    margin: 10px auto;
}
.container1:after { position: absolute; top:0; right: 10px;line-height: 20px;}
.fillmult {
    height: 100%;
    width: 0;
    background-color: #3498db;
    border-radius: 4px;
    line-height: 20px;
    text-align: left;
}
.fillmult span {
    padding-left: 10px;
    color: black;
}
.bordersample {
  border-style: solid;
  border-color: red;
}

</style>


<script>
  $(document).ready(function(){
    // $('[class*="sorrtt"]').each(function(){
    //   load_percentage_bar($(this).attr('id'));
    // });
    $('#inner').scrollLeft(450);
    $( ".sortable_list" ).sortable({
    connectWith: ".connectedSortable",
    appendTo: 'body',
    helper: 'clone',
    update:function(event, ui) {
      var card_id = this.id;
      var total = 0;
                $('#'+ card_id + ' input.countqty').each(function() {
                    var num = parseInt(this.value, 10);
                    if (!isNaN(num)) {
                        total += num;
                    }
                });
                // alert(card_id);
                $('#totalqty'+card_id).text("Total Qty - " + total + " PC(S)");
                var max_qty = $('#maxqty'+ card_id).val();
                var perc = (total/max_qty) *100;
                var percentage = perc.toFixed(2);
                if(percentage >= 100){
                  var current_progress = $('#processbarstattus'+card_id).val();
                  $("#dynamic"+card_id)
                    .css("width", current_progress + "%")
                    .css("color", "black")
                    .css("background-color", "red")
                    .css("font-weight", "bold")
                    .text(current_progress + "%");
                  
                }else{
                  var current_progress = $('#processbarstattus'+card_id).val();
                  $("#dynamic"+card_id)
                    .css("width", current_progress + "%")
                    .css("color", "Black")
                    .css("background-color", "#007bff")
                    .css("font-weight", "bold")
                    .text(current_progress + "%");
                }
                
                // $('#processbarstattus'+ card_id).val(percentage);
                // load_percentage_bar(card_id);
                // $(this).children().each(function(index){
                //   if ($(this).attr('data-position') != (index + 1) || $(this).attr('data-card') != card_id) {
                //     $(this).attr('data-position', (index + 1)).attr('data-card', card_id).addClass('updated');
                //     $(this).find('.badge').text( (index + 1));
                //   }
                // });

      var pos = [];
      $('.updated').each(function(){
        var name = $(this).attr('data-index');
        var position = $(this).attr('data-position');
        var prod = $(this).attr('data-name');
        pos.push([name, position, card_id, prod]);
        $(this).removeClass('updated');
        console.log(pos);


      });

      if (pos) {
        $.ajax({
          url:"/reorder_production",
          type:"POST",
          dataType: "text",
          data: {
            positions: pos
          },
          success:function(data){
            // console.log(data);
            // reload_me();    

          },
          error : function(data) {
            console.log(data.responseText);
          }
        });
      }
    },
    receive: function(ev, ui) {
      var total = 0;
               
                
      if(ui.item.hasClass("In Process") || ui.item.hasClass("Completed"))
        ui.sender.sortable("cancel");
    }
    /*stop: function(event, ui) {
        var item_sortable_list_id = $(this).attr('id');
        console.log(ui);
        //alert($(ui.sender).attr('id'))
    },*/
    // receive: function(event, ui) {
    //   $(this).children().each(function(index){
    //     if ($(this).attr('data-position') != (index + 1)) {
    //       $(this).attr('data-position', (index + 1)).addClass('updated');
    //     }
    //   });

    //   var pos = [];
    //   $('.updated').each(function(){
    //     var name = $(this).attr('data-index');
    //     var position = $(this).attr('data-position');
    //     pos.push([name, position, card_id]);
    //     $(this).removeClass('updated');
    //   });
    //   console.log('receive' + this.id, pos);
        // console.log("dropped on = "+this.id); // Where the item is dropped
        //   console.log("sender = "+ui.sender[0].id); // Where it came from
        //   console.log("item = "+ui.item[0].innerHTML); //Which item (or ui.item[0].id)
    // }         
    }).disableSelection();

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    
    // $('.container1 > div').each(function(){
    // var width=$(this).data('width');
    // var length=$(this).data('length');
    // $(this).animate({ width: width }, 2500);
    // $(this).after( '<span class="perc">' + length + '</span>');
    // $('.perc').delay(3000).fadeIn(1000);
    // }); 


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
  });
</script>
<script type="text/javascript">
        $(document).on('click', '.goto_machine_kanban', function(){
        var date = $(this).data('date');
         window.location.href = "/machine_kanban/15/" + date;        
      });
</script>
<script type="text/javascript">
  function reload_me(){
    $.ajax({
        url:"/production_scheduling_tbl",
        type:"GET",
        success:function(data){
          $('#reload_me_tbl').html(data);

        }
      }); 
  }
</script>
<script>
// function load_percentage_bar(jt){
// var current_progress = $('#processbarstattus'+jt).val();
// if(current_progress > 100){
//                   var current_progress = $('#processbarstattus'+jt).val();
//                   $("#dynamic"+jt)
//                     .css("width", current_progress + "%")
//                     .css("color", "black")
//                     .css("background-color", "red")
//                     .css("font-weight", "bold")
//                     .text(current_progress + "%");
                  
//                 }else{
//                   var current_progress = $('#processbarstattus'+jt).val();
//                   $("#dynamic"+jt)
//                     .css("width", current_progress + "%")
//                     .css("color", "Black")
//                     .css("background-color", "#007bff")
//                     .css("font-weight", "bold")
//                     .text(current_progress + "%");
//                 }
// }
</script>
