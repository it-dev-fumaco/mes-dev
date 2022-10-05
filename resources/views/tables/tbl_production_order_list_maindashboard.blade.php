<table class="table table-striped table-bordered text-center m-0">
  <col style="width: {{ $operation == 2 ? '15%' : '20%' }}"><!-- Workstation -->
  <col style="width: 14%"><!-- Prod. Order -->
  @if ($operation == 2)
    <col style="width: 10%"><!-- Qty -->
    <col style="width: 17%"><!-- Loading -->
    <col style="width: 17%"><!-- Unloading -->
  @else
    <col style="width: 20%"><!-- Start Time -->
    <col style="width: 20%"><!-- Operator -->
  @endif
  <col style="width: 13%"><!-- Machine -->
  <col style="width: 13%"><!-- Action -->
  <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
    <th class="text-center p-1"><b>Workstation</b></th>
    <th class="text-center p-1"><b>Prod. Order</b></th>
    @if ($operation == 2) {{-- for painting --}}
    <th class="text-center p-1"><b>In-process Qty</b></th>
    <th class="text-center p-1"><b>Loading Time</b></th>
    <th class="text-center p-1"><b>Unloading Time</b></th>
    @else
    <th class="text-center p-1"><b>Start Time</b></th>
    <th class="text-center p-1"><b>Operator</b></th>
    @endif
    <th class="text-center p-1"><b>Machine</b></th>
    <th class="text-center p-1"><b>Action</b></th>
  </thead>
  <tbody style="font-size: 8pt;">
    @forelse($result as $row)
    <tr style="background-color: {{ ($current_date != (date('Y-m-d', strtotime( $row['from_time']))) ) ? '#f5b7b1':'' }}">
      <td class="text-center p-1">
        <span class="font-weight-bold d-block">{{ $row['workstation_plot'] }}</span>
        <span class="font-italic d-block">({{ $row['process_name'] }})</span>
      </td>
      <td class="text-center p-0"><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
      @if ($operation == 2)
        <td class="text-center p-0">
          <span class="d-block font-weight-bold" style="font-size: 11pt !important">{{ $row['qty_to_manufacture'] }}</span>
        </td>
        <td class="text-center">
          <span>{{ $row['loading_time'] }}</span><br>
          <span class="font-italic">({{ $row['loading_operator'] }})</span>
        </td>
        <td class="text-center p-0">
          @if ($row['unloading_time'])
              <span>{{ $row['unloading_time'] }}</span><br>
              <span class="font-italic">({{ $row['unloading_operator'] }})</span>
          @endif
        </td>
      @else
        <td class="text-center p-0">{{ $row['from_time']  }}</td>
        <td class="text-center p-0">
          <span class="d-block">{{ $row['operator_name'] }}</span>
          @foreach($row['helpers'] as $helper_name)
            <span class="d-block">{{ $helper_name }}</span>
          @endforeach
        </td>
      @endif
      <td class="text-center p-0">{{ $row['machine'] }}</td>
      <td>
        <button class="btn btn-primary mark-done-btn" data-workstationid="{{ $row['workstation_id'] }}" data-jtid="{{ $row['jtname'] }}" data-workstation="{{ $row['workstation_plot'] }}" data-qtyaccepted="{{ $row['qty_accepted'] }}" style="padding: 10px;">Mark as Done</button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="8" class="text-center font-weight-bold text-muted text-uppercase">No In Progress Task(s)</td>
    </tr>
    @endforelse
  </tbody>
</table>

<script type="text/javascript">
  $(document).on('click', '.mark-done-btn', function(){
    if ($('#machine_kanban_details #card-status').val() == 'Unassigned') {
      showNotification("danger", 'Please assigned task to machine first', "now-ui-icons travel_info");
      return false;
    }

    if ($('#machine_kanban_details #task-status').val() == 'Completed') {
      showNotification("danger", 'Unable to Mark as Done.', "now-ui-icons travel_info");
      return false;
    }

    var workstation_id= $(this).attr('data-workstationid');
    var jtid= $(this).attr('data-jtid');
    var workstation = $(this).attr('data-workstation');
    var qty = $(this).attr('data-qtyaccepted');
      $.ajax({
      url:"/get_AssignMachineinProcess_jquery/"+ jtid + "/" + workstation_id,
      type:"GET",
      success:function(data){
        $('#machine_selection').html(data);
        $('#mark-done-modal #jt-index').val(jtid);
        $('#mark-done-modal #qty-accepted-override').val(qty);
        $('#mark-done-modal #workstation-override').val(workstation);
        
        $('#mark-done-modal .workstation-text').text('[' + workstation + ']');
        $('#mark-done-modal').modal('show');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    }); 
  });
</script>
<script type="text/javascript">
  function load_dashboard(){
      $( "#on-going-production-orders-content .tab-pane" ).each(function( index ) {
        const operation = $(this).data('operation');
        const el = $(this);
        
        get_ongoing_production_orders(operation, el);
        get_qa(operation, el);
      });
    }

    function get_ongoing_production_orders(operation, el){
      $.ajax({
        url:"/get_production_order_list/" + date_today,
        type:"GET",
        data: {operation: operation},
        success:function(data){
          $(el).find('.table-div').eq(0).html(data);
        }
      }); 
    }

    function get_qa(operation, el){
      $.ajax({
        url:"/qa_monitoring_summary/" + date_today,
        type:"GET",
        data: {operation: operation},
        success:function(data){
          $(el).find('.table-div').eq(3).html(data);
        }
      }); 
    }

      $('#mark-done-frm').submit(function(e){
      e.preventDefault();
      var url = $(this).attr('action');
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success) {
            showNotification("success", data.message, "now-ui-icons ui-1_check");
            $('#mark-done-modal').modal('hide');
            $('#jtname-modal').modal("hide");
            $('#view-machine-task-modal').modal("hide");
            load_dashboard();
            table_po_orders();
            count_current_production();
          }else{
            showNotification("danger", data.message, "now-ui-icons travel_info");
            return false;
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
      }); 
    });
</script>
<script type="text/javascript">
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
</script>
<script type="text/javascript">
    $(document).on('click', '.spotclass', function(event){
      event.preventDefault();
      var jtid = $(this).attr('data-jobticket');
      var prod = $(this).attr('data-prodno');
      $.ajax({
        url: "/spotwelding_production_order_search/" + jtid,
        type:"GET",
        success:function(data){
            $('#spotwelding-div').html(data);
            $('#spotwelding-modal .prod-title').text(prod+" - Spotwelding");
            $('#spotwelding-modal').modal('show');
        }
      });
    });
  

</script> 