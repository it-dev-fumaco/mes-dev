      <div class="table-responsive" style="font-size:10px;">

                        <table class="table table-striped">
                                  <col style="width: 5%;">
                                  <col style="width: 20%;">
                                  <col style="width: 20%;">
                                  <col style="width: 20%;">
                                  <col style="width: 20%;">
                                  <col style="width: 15%;">
                                <thead class="text-primary" style="font-size: 9px;font-weight: bold;">
                                  <th class="text-center"><b>No.</b></th>
                                  <th class="text-left"><b>Workstation</b></th>
                                  <th class="text-left"><b>Category</b></th>
                                  <th class="text-left"><b>Checklist</b></th>
                                  <th class="text-left"><b>Reason</b></th>
                                  
                                  <th class="text-center"><b>Action(s)</b></th>
                                </thead>
                                <tbody>
                                  @forelse($check_list as $index => $row)
                                    <tr>
                                    <td>{{ $row->qa_checklist_id }}</td>
                                    <td class="text-left">{{ $row->workstation_name }}</td>
                                    <td class="text-left"> {{ $row->reject_category_name }}</td>
                                    <td class="text-left"> {{ $row->reject_checklist }}</td>
                                    <td class="text-left"> {{ $row->reject_reason }}</td>
                                    
                                    <td>
                                        <a href="#" class="hover-icon"  data-toggle="modal" style="padding-left: 5px;">
                                          <button type='button' class='btn btn-default btn-delete-checklist' data-id="{{$row->qa_checklist_id}}" data-workstation="{{$row->workstation_name}}" data-rejectchecklist="{{$row->reject_checklist}}" data-operation="{{$row->operation_name}}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
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
  <div id="checklist_list_pagination_fabrication" class="col-md-12 text-center" style="text-align: center;">
   {{ $check_list->links() }}
  </div>
</center>
</div>
