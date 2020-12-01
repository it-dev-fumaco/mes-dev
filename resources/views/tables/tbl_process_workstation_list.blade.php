<div class="table-responsive" style="font-size: 9pt;">
                    <table class="table table-striped text-center">
                      <thead class="text-primary">
                        <th class="text-center"><b>No.</b></th>
                        <th class="text-center"><b>Process</b></th>
                        {{--<th class="text-center"><b>Remarks</b></th>--}}
                        {{--<th class="text-center"><b>Action/s</b></th>--}}
                      </thead>
                       <tbody>
                        @foreach($data as $rows)
                          <tr>
                            <td>{{ $rows['id'] }}</td>
                            <td>{{ $rows['process'] }}</td>
                            {{--<td class="text-center"><a href="#" class="hover-icon"  data-toggle="modal" data-target="#delete-process-workstation-{{ $rows->id }}-modal">
                                 <button type='button' class='btn btn-danger btn-sm'><i class='now-ui-icons ui-1_simple-remove' style="font-size: 8pt; font-weight: bold;"></i></button>
                              </a>
                              @include('modals.delete_process_workstation_modal')
                            </td>--}}
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <center>
  <div id="processWorkstation_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>
                   

@section('script')
<script type="text/javascript">
      $('#delete-process-frm').submit(function(e){
      var url = $(this).attr("action");
      $.ajax({
        url: url,
        type:"POST",
        data: $(this).serialize(),
        success:function(data){
          if (data.success < 1) {
            showNotification("danger", data.message, "now-ui-icons travel_info");
          }else{
            showNotification("success", data.message, "now-ui-icons ui-1_check");
                // $('#add-workstation-modal').modal('hide');
                // $('#add-worktation-frm').trigger("reset");
                location.reload(true);

          }
        }
      });
    });
</script>
@endsection
