      <div class="table-responsive" style="font-size:11px;">

                        <table class="table table-striped">
                                  <col style="width: 5%;">
                                  <col style="width: 20%;">
                                  <col style="width: 20%;">
                                  <col style="width: 20%;">
                                  <col style="width: 20%;">
                                  <col style="width: 15%;">
                                <thead class="text-primary">
                                  <th class="text-center"><b>No.</b></th>
                                  <th class="text-left"><b>Operation</b></th>
                                  <th class="text-left"><b>Workstation</b></th>
                                  <th class="text-left"><b>Classification</b></th>
                                  <th class="text-left"><b>Checklist Category</b></th>
                                  <th class="text-center"><b>Action(s)</b></th>
                                </thead>
                                <tbody style="font-size:13px;">
                                  @forelse($check_list as $index => $row)
                                    <tr>
                                    <td>{{ $row->qa_checklist_id }}</td>
                                    <td class="text-left">{{ $row->operation_name }}</td>
                                    <td class="text-left"> {{ $row->workstation_name }}</td>
                                    <td class="text-left"> {{ $row->qa_checklist_classification }}</td>
                                    <td class="text-left"> {{ $row->check_list }}</td>
                                    
                                    <td>
                                        <a href="#" class="hover-icon"  data-toggle="modal">
                                          <button type='button' class='btn btn-default btn-edit-checklist' data-operationid="{{$row->operation_name}}" data-workstationid="{{$row->workstation_name}}" data-classifi="{{$row->qa_checklist_classification}}" data-checklist="{{$row->check_list}}" data-id="{{$row->qa_checklist_id}}" ><i class='now-ui-icons design-2_ruler-pencil'></i></button>
                                        </a>
                                        <a href="#" class="hover-icon"  data-toggle="modal" style="padding-left: 5px;">
                                          <button type='button' class='btn btn-default btn-delete-checklist' data-id="{{$row->qa_checklist_id}}" data-checklist="{{$row->check_list}}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
                                        </a>
                                    </td>
                                    @empty
                                    <tr>
                                      <td colspan="6" class="text-center">No Record(s) Found.</td>
                                    </tr>
                                    @endforelse

                                  </tr>
                                </tbody>
                              </table>
                              <center>
  <div id="checklist_list_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $check_list->links() }}
  </div>
</center>
</div>
