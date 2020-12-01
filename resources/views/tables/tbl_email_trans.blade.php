<div class="table-responsive">
                    <table class="table table-striped text-center">
                      <thead class="text-primary" style="font-size: 8pt;">
                        <th class="text-center"><b>#</b></th>
                        <th class="text-center"><b>Transaction Type</b></th>
                        <th class="text-center"><b>Email</b></th>
                        <th class="text-center"><b>Action/s</b></th>
                      </thead>
                      <tbody style="font-size: 9pt;">
                        @forelse($data as $rows)
                        <tr>
                          <td>{{ $rows->email_trans_recipient_id }}</td>
                          <td>{{ $rows->email_trans }}</td>
                          <td>{{ $rows->email }}</td>
                          <td>
                              <a href="#" class="hover-icon delete-email-trans-list"  data-toggle="modal" data-etrans='{{$rows->email_trans}}' data-eemail="{{$rows->email}}" data-emailtransid="{{ $rows->email_trans_recipient_id }}" style="padding-left: 5px;">
                                <button type='button' class='btn btn-default'><i class='now-ui-icons ui-1_simple-remove'></i></button>
                              </a>
                           </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="6" class="text-center">No Record(s) Found.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                  <center>
  <div id="tbl_email_trans_list_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $data->links() }}
  </div>
</center>


