      <div class="table-responsive" style="font-size:13px;">
                        <table class="table table-striped">
                                  <col style="width: 5%;">
                                  <col style="width: 11%;">
                                  <col style="width: 11%;">
                                  <col style="width: 12%;">
                                  <col style="width: 12%;">
                                  <col style="width: 13%;">
                                  <col style="width: 13%;">
                                  <col style="width: 12%;">
                                  <col style="width: 11%;">
                                <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
                                  <th class="text-center"><b>No</b></th>
                                  <th class="text-center"><b>STEM</b></th>
                                  <th class="text-center"><b>Transaction Date</b></th>
                                  <th class="text-center"><b>Order Type</b></th>
                                  <th class="text-center"><b>Sales Order</b></th>
                                  <th class="text-center"><b>Customer</b></th>
                                  <th class="text-center"><b>Project</b></th>   
                                  <th class="text-center"><b>Item Status</b></th>                                 
                                  <th class="text-center"><b>Action(s)</b></th>
                                </thead>
                                <tbody>
                                  @forelse($trans_list as $index => $row)
                                  @php
                                   if($row->item_status == "For Checking"){
                                    $badge="info";
                                    $tohide="none";
                                    $delete="";
                                    $submit="none";
                                    $stat_name="Pending";
                                   }elseif($row->item_status == "Issued"){
                                      $badge="warning";
                                      $tohide="none";
                                      $submit="";
                                      $delete="none";
                                      $stat_name='Issued';
                                   }else{
                                      $badge="";
                                      $delete="none";
                                      $tohide="none";
                                      $submit="none";
                                      $stat_name=$row->item_status;
                                   }
                                   if($row->docstatus == "2"){
                                      $badge="danger";
                                      $tohide="";
                                      $submit="none";
                                      $delete="none";
                                      $stat_name="Cancelled";
                                 }elseif($row->docstatus == "1"){
                                    $badge="success";
                                    $tohide="";
                                    $delete="none";
                                    $submit="none";
                                    $stat_name="Confirmed";
                                }
                                   $r_date = \Carbon\Carbon::parse($row->creation)->format('M d, Y');                

                                  @endphp
                                    <tr>
                                    <td class="text-center">{{ $index +1 }}</td>
                                    <td class="text-center"><b><a class="view_transfer_detail_btn" href="#" data-id="{{ $row->name }}" style="color:black;">{{ $row->name }}</a></b></td>
                                     <td class="text-center">{{ $r_date }}</td>
                                    <td class="text-center">{{ $row->order_type }}</td>
                                    <td class="text-center">{{ $row->sales_order_no }}</td>
                                    <td class="text-center">{{ $row->so_customer_name }}</td>
                                    <td class="text-center">{{ $row->project }}</td>
                                    <td class="text-center" style="font-size: 17px;"><span class="badge badge-{{$badge}}">{{ $stat_name }}</span></td>
                                    <td class="text-center">
                                      
                                          <button type="button" class="btn btn-primary confirmed-transfer-btn" data-id="{{ $row->name }}" style="display: {{$submit}}">Confirm</button>
                                          <button type="button" class="btn btn-default view_transfer_detail_btn" data-id="{{ $row->name }}" style="display: {{$tohide}}">Details</button>
                                          <button type="button" class="btn btn-default delete_transfer_btn" data-id="{{ $row->name }}" style="display: {{$delete}}">Delete</button>

                                          {{--<div class="btn-group">
                                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                          </button>
                                          <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#">Duplicate</a>
                                            <a class="dropdown-item cancel-transfer-btn" href="#"  data-id="{{ $row->name }}" style="display: {{$tohide}}">Cancel</a>
                                            
                                          </div>
                                        </div>--}}
                                        
                                    </td>
                                    @empty
                                    <tr>
                                      <td colspan="9" class="text-center">No Record(s) Found.</td>
                                    </tr>
                                    @endforelse

                                  </tr> 
                                </tbody>
                              </table>
      </div>
      <div class="row">
                                      <div id="transfer_list_pagination" class="col-md-8 text-center" style="text-align: center;">
                                  {{ $trans_list->links() }}
                                </div>
                                <div class="col-md-4 text-right"><h6 style="display:inline"><b>Total Material Transfer: </b></h6><span style="display:inline-block;padding-right:70px;padding-left:20px; font-size:16px;font-weight:bolder;">{{ $count }}</span></div>
                                </div>
                              </div>

