<div class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="scrolling outer">
        <div class="inner">
          <table>
            <tr>
              <td class="th">
              <div class="card">
            <div class="card-header">
               <h4 class="card-title text-center"><b>Unassigned Prod. Order(s)</b></h4>
            </div>
            <div class="card-body sortable_list connectedSortable" id="" style="height: 750px; position: relative;">
            @foreach($orders as $row)
              <div class="card {{ $row['status'] }}" data-index="{{ $row['jtname'] }}" data-position="{{ $row['order_no'] }}" data-card="null">
                <div class="card-body" style="font-size: 8pt;">
                  <table style="width: 100%;">
                    <tr>
                      <td colspan="2"><b>{{ $row['name'] }}</b> [{{ $row['status'] }}]</td>
                      <td><span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $row['order_no'] }}</span></td>
                    </tr>
                    @if($row['customer'])
                    <tr>
                      <td colspan="3">Customer: <b>{{ $row['customer'] }}</b></td>
                    </tr>
                    @endif
                    <tr>
                      <td>Qty: <span style="font-size: 9pt"><b>{{ number_format($row['qty']) }} {{ $row['stock_uom'] }}</b></span></td>
                      <td>CTD Qty: <span style="font-size: 9pt"><b>{{ number_format($row['produced_qty']) }} {{ $row['stock_uom'] }}</b></span></td>
                      <td><span class="pull-right"><b>{{ $row['classification'] }}</b></span></td>
                      <td><span class="pull-right"><b>{{ $row['workstation_plot'] }}</b></span></td>
                    </tr>
                  </table>
                 
                </div>
              </div>
              @endforeach
            </div>
         </div></th>
         
         @foreach($work as $row)
         @foreach($row['machine'] as $rows)
                            <td class="td">
                              <div class="card" style="float: left;">
                                  <div class="card-header" style="background-color: #f96332; color: white;font-weight: bold;">
                                    <h6 class="card-title text-center" style="padding-bottom: 20px;"><b>{{ $rows['machine_code'] }} - {{ $rows['machine_name'] }} </b></h6>
                                  </div>
                                  <div class="card-body connectedSortable sortable_list" style="height: 750px; position: relative; overflow-x: auto;" id="{{ $rows['machine_code'] }}" >
                                    @foreach($rows['machine_load'] as $rowss)
                                      <div class="card {{ $rowss->status }}" data-index="{{ $rowss->jtname }}" data-position="{{ $rowss->order_no}}" data-card="{{ $rowss->machine }}">
                                        <div class="card-body" style="font-size: 8pt;">
                                          <table style="width: 100%;">
                                            <tr>
                                              <td colspan="2"><b>{{ $rowss->name }}</b> [{{ $rowss->status }}]</td>
                                              <td><span class="pull-right badge badge-primary" style="font-size: 9pt;">{{ $rowss->order_no}}</span></td>
                                            </tr>
                                            @if($rowss->customer)
                                            <tr>
                                              <td colspan="3">Customer: <b>{{ $rowss->customer }}</b></td>
                                            </tr>
                                            @endif
                                            <tr>
                                              <td>Qty: <span style="font-size: 9pt"><b>{{ number_format($rowss->qty) }} {{ $rowss->stock_uom }}</b></span></td>
                                              <td>CTD Qty: <span style="font-size: 9pt"><b>{{ number_format($rowss->produced_qty) }} {{ $rowss->stock_uom}}</b></span></td>
                                              <td><span class="pull-right"><b>{{ $rowss->classification}}</b></span></td>
                                              <td><span class="pull-right"><b>{{ $rowss->workstation_plot}}</b></span></td>
                                              <td><span class="pull-right"><b>{{ $rowss->machine }}</b></span></td>
                                            </tr>
                                          </table>
                                         
                                        </div>
                                      </div>
                                      @endforeach
                                  </div>
                              </div>
                            </td>
                           @endforeach
                @endforeach
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>



<script>
  $(document).ready(function(){
    $( ".sortable_list" ).sortable({
    connectWith: ".connectedSortable",
    appendTo: 'body',
    helper: 'clone',
    update:function(event, ui) {
      var card_id = this.id;
            

      $(this).children().each(function(index){
        if ($(this).attr('data-position') != (index + 1) || $(this).attr('data-card') != card_id) {
          $(this).attr('data-position', (index + 1)).attr('data-card', card_id).addClass('updated');
          $(this).find('.badge').text( (index + 1));
        }
      });

      var pos = [];
      $('.updated').each(function(){
        var name = $(this).attr('data-index');
        var position = $(this).attr('data-position');
        pos.push([name, position, card_id]);
        $(this).removeClass('updated');
      });

      $.ajax({
        url:"/reorder_productions",
        type:"POST",
        dataType: "text",
        data: {
          positions: pos
        },
        success:function(data){
          // console.log(data);
        },
        error : function(data) {
          console.log(data.responseText);
        }
      });
    },
    receive: function(ev, ui) {
      // if(ui.item.hasClass("In Process"))
      //   ui.sender.sortable("cancel");
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
