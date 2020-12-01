<div class="table-responsive">
                    <table class="table table-striped text-center" style="font-size: 8pt;">
                    <col style="width: 5%;">
                    <col style="width: 10%;">
                    <col style="width: 15%;">
                    <col style="width: 20%;">
                    <col style="width: 10%;">
                    <col style="width: 20%;">
                    <col style="width: 20%;">
                      <thead class="text-primary">
                        <th class="text-center"><b>No.</b></th>
                         <th class="text-center"><b>P.O</b> 
                         <th class="text-center"><b>Reference</b>
                        <th class="text-center"><b>Decription</b>
                        <th class="text-center"><b>Qty</b>
                        <th class="text-center" style="font-size:12px;"><b>Planned Start Date</b>
                        <th class="text-center"><b>Action</b>
                      </thead>
                      <tbody>
                        @forelse($data as $index => $row)
                        @php
                            if($row['sales_order'] == null){
                                $ref=$row['material_request'];
                            }else{
                                $ref=$row['sales_order'];
                            }
                            $strip= strtok($row['description'], ",");
                            $date = \Carbon\Carbon::parse($row['planned_start_date'])->format('M d, Y');

                            @endphp
                          <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><a href="#" class="prod_order_link_to_search" data-prod="{{ $row['production_order'] }}" style="color:black;font-weight:bold;">{{ $row['production_order'] }}</a></td>
                            <td  data-prod="{{ $row['production_order'] }}">{{ $ref }} <br><a href="#" class="prod_order_link_to_tracking" data-itemcode="{{ $row['parent_item_code'] }}" data-guideid="{{ $ref }}"><i class="now-ui-icons location_pin"></i> Item Tracking</a></td>
                            <td class="text-left"><b>{{ $row['item_code'] }}</b>- {{ $strip }}</td>
                            <td><span style="font-size:14px;"><b>{{ $row['qty_to_manufacture'] }}</b></span><br>{{ $row['stock_uom'] }}</td>
                            <td>{{$date }}</td>
                            <td>
                            <div class="btn-group">
                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                              </button>
                              <div class="dropdown-menu">
                                <a href="#" class="dropdown-item cancel-prod-btn " style="padding-left: 15px;" data-prod="{{ $row['production_order'] }}" data-tabselected="inprogress">Cancel</a>
                                <a href="#" class="dropdown-item complete-prod-btn " style="padding-left: 15px;" data-prod="{{ $row['production_order'] }}" data-tabselected="inprogress">Complete</a>

                              </div>
                            </div>
                            </td>

                          </tr>
                        @empty
                        <tr>
                           <td colspan="8" class="text-center">No Record(s) Found.</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>



