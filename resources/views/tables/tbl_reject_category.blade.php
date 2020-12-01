      <div class="table-responsive" style="font-size:13px;">
                        <table class="table table-striped">
                                  <col style="width: 5%;">
                                  <col style="width: 25%;">
                                  <col style="width: 25%;">
                                  <col style="width: 25%;">
                                  <col style="width: 20%;">
                                <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
                                  <th class="text-center"><b>No.</b></th>
                                  <th class="text-left"><b>Type</b></th>
                                  <th class="text-left"><b>Reject Category</b></th>
                                  <th class="text-left"><b>Description</b></th>                                  
                                  <th class="text-center"><b>Action(s)</b></th>
                                </thead>
                                <tbody>
                                  @forelse($reject_category as $index => $row)
                                    <tr>
                                    <td>{{ $row->reject_category_id }}</td>
                                    <td class="text-left">{{ $row->type }}</td>
                                    <td class="text-left">{{ $row->reject_category_name }}</td>
                                    <td class="text-left">{{ $row->category_description }}</td>
                                    <td>
                                      <button type='button' class='btn btn-default hover-icon edit-reject-category-btn' data-id="{{ $row->reject_category_id }}" data-type="{{ $row->type }}" data-category="{{ $row->reject_category_name }}" data-categorydesc="{{ $row->category_description }}">
                                        <i class='now-ui-icons design-2_ruler-pencil'></i>
                                      </button>
                                        
                                          <button type='button' class='btn btn-default hover-icon btn-delete-reject-category' data-id="{{ $row->reject_category_id }}" data-type="{{ $row->type }}" data-category="{{ $row->reject_category_name }}" data-categorydesc="{{ $row->category_description }}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
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
  <div id="reject_category_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $reject_category->links() }}
  </div>
</center>
</div>
