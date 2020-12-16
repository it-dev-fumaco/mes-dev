<div class="table-responsive" style="font-size:13px;">

    <table class="table table-striped">
              <col style="width: 10%;">
              <col style="width: 80%;">
              <col style="width: 10%;">
            <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
              <th class="text-center"><b>No.</b></th>
              <th class="text-center"><b>Material Type</b></th>                                
              <th class="text-center"><b>Action(s)</b></th>
            </thead>
            <tbody>
              @forelse($list as $index => $row)
                <tr>
                <td>{{ $row->reject_material_type_id }}</td>
                <td class="text-left">{{ $row->material_type }}</td>
                <td>
                  <button type='button' class='btn btn-default hover-icon btn_edit_material_type' data-id="{{ $row->reject_material_type_id }}" data-reason="{{ $row->material_type }}" >
                    <i class='now-ui-icons design-2_ruler-pencil'></i>
                  </button>                
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
    <div id="material_type_pagination" class="col-md-12 text-center" style="text-align: center;">
    {{ $list->links() }}
    </div>
    </center>
    </div>
    