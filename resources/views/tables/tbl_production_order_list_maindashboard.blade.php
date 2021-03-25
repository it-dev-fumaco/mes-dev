<div class="card mt-2 mb-2">
  <div class="card-body pb-0 pt-0">
    <div class="row bg-white" style="min-height: 600px;">
      <div class="col-md-12 p-0">
        <table class="table table-striped table-bordered text-center m-0">
          <col style="width: 20%">
          <col style="width: 14%">
          <col style="width: 20%">
          <col style="width: 20%">
          <col style="width: 13%">
          <col style="width: 13%">
          <thead style="background-color: #f39c12;">
            <th class="text-center p-2" colspan="6">
              <h6 class="text-white font-weight-bold text-center m-0" style="font-size: 10.5pt;">On-Going Production Order(s)</h6>
            </th>
          </thead>
          <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
            <th class="text-center p-2"><b>Workstation</b></th>
            <th class="text-center p-2"><b>Prod. Order</b></th>
            <th class="text-center p-2"><b>Start Time</b></th>
            <th class="text-center p-2"><b>Operator</b></th>
            <th class="text-center p-2"><b>Machine</b></th>
            <th class="text-center p-2"><b>Action</b></th>
          </thead>
          <tbody style="font-size: 9pt;">
            @forelse($result as $row)
            <tr style="background-color: {{ ($current_date != (date('Y-m-d', strtotime( $row['from_time']))) ) ? '#f5b7b1':'' }}">
              <td class="text-center p-1">
                <span class="font-weight-bold d-block">{{ $row['workstation_plot'] }}</span>
                <span class="font-italic d-block">({{ $row['process_name'] }})</span>
              </td>
              <td class="text-center"><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
              <td class="text-center">{{ $row['from_time']  }}</td>
              <td class="text-center">
                <span class="d-block">{{ $row['operator_name'] }}</span>
                @foreach($row['helpers'] as $helper_name)
                  <span class="d-block">{{ $helper_name }}</span>
                @endforeach
              </td>
              <td class="text-center">{{ $row['machine'] }}</td>
              <td>
                <button class="btn btn-primary mark-done-btn" data-workstationid="{{ $row['workstation_id'] }}" data-jtid="{{ $row['jtname'] }}" data-workstation="{{ $row['workstation_plot'] }}" data-qtyaccepted="{{ $row['qty_accepted'] }}" style="padding: 10px;">Mark as Done</button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center font-weight-bold">No In Progress Task(s)</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>



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