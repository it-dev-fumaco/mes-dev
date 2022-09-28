<table class="table table-striped table-bordered text-center" style="font-size: 7pt;">
  <thead class="text-primary text-uppercase font-weight-bold">
    <th class="text-center p-2"><b>No.</b></th>
    <th class="text-center p-2"><b>Transaction Type</b></th>
    <th class="text-center p-2"><b>Email</b></th>
    <th class="text-center p-2"><b>Action/s</b></th>
  </thead>
  <tbody style="font-size: 12px;">
    @forelse($data as $rows)
    <tr>
      <td class="text-center p-2">{{ $rows->email_trans_recipient_id }}</td>
      <td class="text-center p-2">{{ $rows->email_trans }}</td>
      <td class="text-center p-2">{{ $rows->email }}</td>
      <td class="text-center p-2">
        <button type='button' class='btn pb-2 pt-2 pr-3 pl-3 btn-default hover-icon delete-email-trans-list'  data-toggle="modal" data-etrans='{{$rows->email_trans}}' data-eemail="{{$rows->email}}" data-emailtransid="{{ $rows->email_trans_recipient_id }}" ><i class='now-ui-icons ui-1_simple-remove'></i></button>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center text-uppercase text-muted">No Record(s) Found.</td>
    </tr>
    @endforelse
  </tbody>
</table>
<div id="tbl_email_trans_list_pagination" class="col-md-12 text-center">{{ $data->links() }}</div>