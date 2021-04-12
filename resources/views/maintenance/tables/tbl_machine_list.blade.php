
        <table class="table table-striped text-center" style="margin-top:-30px;">
          <col style="width:10%">
          <col style="width:20%">
          <col style="width:20%">
          <col style="width:20%">
          <col style="width:20%">

          <col style="width:10%">
              <thead class="text-primary" style="font-size: 8pt;">
                <th class="text-center"><b>ID</b></th>
                <th class="text-center"><b>Machine Code</b></th>
                <th class="text-center"><b>Machine Name</b></th>
                <th class="text-center"><b>Model</b></th>
                <th class="text-center"><b>Status</b></th>
                <th class="text-center"><b>Action/s</b></th>
              </thead>
              <tbody style="font-size: 10pt;">
                @forelse($m_list as $row)
                    @php 
                        if($row->status == "Unavailable"){
                            $b_badge="secondary";
                        }else if($row->status == "On-going Maintenance"){
                            $b_badge="warning";
                        }else{
                            $b_badge="success";
                        }
                   @endphp
                <tr>
                  <td class="text-center" style="font-size:18px;">
                    <span class="font-weight-bold d-block">{{ $row->machine_id }}</span>
                  </td>
                  <td class="text-center">
                    <span class=" d-block" style="font-size:15px;">{{ $row->machine_code }}</span>
                  </td>
                  <td class="text-center">
                    <span class="d-block" style="font-size:15px;">{{ $row->machine_name }}</span>
                  </td>
                  <td class="text-center">{{$row->model  }}</td>
                  
                  <td class="text-center">
                  <span class="badge badge-{{$b_badge}}" style="font-size: 12pt;">{{ $row->status }}</span>

                  </td>
            
                  <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Action
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item update-status-machine" data-id="{{ $row->machine_id }}" data-stat="{{$row->status}}" href="#">Update Status</a>
                        </div>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="9" class="text-center">No completed request found</td>
                </tr>
                @endforelse
              </tbody>
        </table>
        <center>
            <div id="paginate-machine-list" class="col-md-12 text-center" style="text-align: center;">
             {{ $m_list->links() }}
            </div>
          </center>

<script type="text/javascript">
</script>