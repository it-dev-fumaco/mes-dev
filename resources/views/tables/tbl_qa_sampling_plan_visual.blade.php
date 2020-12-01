      <div class="table-responsive" style="font-size:13px;">

                        <table class="table table-striped">
                                  <col style="width: 35%;">
                                  <col style="width: 20%;">
                                  <col style="width: 10%;">
                                  <col style="width: 10%;">
                                  <col style="width: 25%;">
                                <thead class="text-primary" style="font-size: 10px;font-weight: bold;">
                                  <th class="text-center"><b>Lot Size(PCS).</b></th>
                                  <th class="text-left"><b>Sample Size</b></th>
                                  <th class="text-left"><b>AC</b></th>
                                  <th class="text-left"><b>RE</b></th>                                  
                                  <th class="text-center"><b>Action(s)</b></th>
                                </thead>
                                <tbody>
                                  @forelse($sampling_plan as $index => $row)
                                    <tr>
                                    <td><b>{{ $row->lot_size_min }}</b> TO <b>{{ $row->lot_size_max }}</b></td>
                                    <td class="text-left">{{ $row->sample_size }}</td>
                                    <td class="text-left">{{ $row->acceptance_level }}</td>
                                    <td class="text-left">{{ $row->reject_level }}</td>
                                    
                                    <td class="text-center">
                                        <button type='button' class='btn btn-default hover-icon btn-delete-sampling_plan' data-id="{{$row->sampling_plan_id}}" data-category="{{$row->reject_category_id}}"><i class='now-ui-icons ui-1_simple-remove'></i></button>
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
  <div id="sampling_visual_pagination" class="col-md-12 text-center" style="text-align: center;">
   {{ $sampling_plan->links() }}
  </div>
</center>
</div>
