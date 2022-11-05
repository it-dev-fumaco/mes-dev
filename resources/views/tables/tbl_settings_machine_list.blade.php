<table class="table table-striped table-bordered text-center text-hover" style="font-size: 7pt;">
  <col style="width: 5%;"><!-- No. -->
  <col style="width: 20%;"><!-- Machine Code -->
  <col style="width: 35%;"><!-- Action(s) -->
  <col style="width: 15%;"><!-- Status -->
  <col style="width: 25%;"><!-- Machine Name -->
  <thead class="text-white text-uppercase bg-secondary font-weight-bold">
    <th class="p-2 text-center"><b>No.</b></th>
    <th class="p-2 text-center"><b>Machine Code</b></th>
    <th class="p-2 text-center"><b>Machine Name</b></th>
    <th class="p-2 text-center"><b>Status</b></th>
    <th class="p-2 text-center"><b>Action(s)</b></th>
  </thead>
  <tbody style="font-size:13px;">
    @forelse($machine_list as $index => $row)
    <tr>
      <td class="p-2 text-center">{{ $row->machine_id }}</td>
      <td class="p-1 text-left">
        <div class="row">
          <div class="col-4"><img src="{{ asset(($row->image ? $row->image : '/storage/no_img.png')) }}" class="w-100 img-thumbnail" alt=""></div>
          <div class="col-5" style="display: flex; justify-content: center; align-items: center;">{{ $row->machine_code }}</div>
        </div>
      </td>
      <td class="p-2 text-left">{{ $row->machine_name }}</td>
      <td class="p-2 text-center">
        @php
          switch($row->status){
            case 'Available':
              $badge = 'success';
              break;
            case 'Unavailable':
              $badge = 'secondary';
              break;
            default:
              $badge = 'primary';
              break;
          }
        @endphp
        <span class="badge badge-{{ $badge }}" style="font-size: 8pt;">{{ $row->status }}</span>
      </td>
      <td class="p-2 text-center">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default btn-edit-machine hover-icon' data-toggle="modal" data-machineid="{{ $row->machine_id }}" data-machinecode="{{ $row->machine_code }}" data-machinename="{{ $row->machine_name }}" data-status="{{ $row->status }}" data-referencekey="{{ $row->reference_key }}" data-type="{{ $row->type}}" data-model="{{$row->model}}" data-image="{{ $row->image }}"><i class='now-ui-icons design-2_ruler-pencil'></i></button>
        <a href="/goto_machine_profile/{{ $row->machine_id }}" class="hover-icon btn pb-2 pt-2 pr-3 pl-3 btn-default" data-toggle="modal">
          <i class='now-ui-icons ui-1_zoom-bold'></i>
        </a>
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default btn-delete-machine hover-icon' data-toggle="modal" data-machineid="{{ $row->machine_id }}" data-machinecode="{{ $row->machine_code }}" data-machinename="{{ $row->machine_name }}" data-status="{{ $row->status }}" data-referencekey="{{ $row->reference_key }}" data-type="{{ $row->type}}" data-model="{{$row->model}}" data-image="{{ $row->image }}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="setting_machine_list_pagination" class="col-md-12 text-center">{{ $machine_list->links() }}</div>