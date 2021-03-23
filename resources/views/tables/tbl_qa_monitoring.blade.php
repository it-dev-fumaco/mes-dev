<div class="card mt-2 mb-2">
    <div class="card-body pb-0 pt-0">
      <div class="row bg-white">
        <div class="col-md-12 p-0">
          <table class="table table-striped table-bordered text-center m-0">
            <col style="width: 27%">
            <col style="width: 30%">
            <col style="width: 18%">
            <col style="width: 25%">
            <thead style="background-color: #0772DD;">
              <th class="text-center p-2" colspan="4">
                <h6 class="text-white font-weight-bold text-center m-0" style="font-size: 10.5pt;">Quality Assurance</h6>
              </th>
            </thead>
            <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
              <th class="text-center p-2"><b>Prod. Order</b></th>
              <th class="text-center p-2"><b>Inspected by</b></th>
              <th class="text-center p-2"><b>Qty</b></th>
              <th class="text-center p-2"><b>Status</b></th>
            </thead>
            <tbody style="font-size: 8pt;">
              @forelse($quality_inspection as $row)
              <tr>
                <td class="text-center"><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
                <td class="text-center">
                  <span class="d-block">{{ $row['inspected_by'] }}</span>
                </td>
                <td class="text-center">
                  <span class="d-block">{{ $row['quantity'] }}</span>
                </td>
                <td class="text-center">
                  @php
                    if($row['status'] == 'For Confirmation'){
                      $badge = 'badge-warning';
                    }else if($row['status'] == 'QC Failed'){
                      $badge = 'badge-danger';
                    }else{
                      $badge = 'badge-success';
                    }
                  @endphp
                  <span class="badge {{ $badge }}" style="font-size: 7.5pt;">{{ $row['status'] }}</span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center font-weight-bold">No Quality Inspection</td>
              </tr>
              @endforelse
            </tbody>
            <thead style="background-color: #0772DD;">
              <th class="text-center p-2" colspan="4">
                <h6 class="text-white font-weight-bold text-center m-0" style="font-size: 10.5pt;">Rejection</h6>
              </th>
            </thead>
            <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
              <th class="text-center p-2"><b>Prod. Order</b></th>
              <th class="text-center p-2"><b>Operator</b></th>
              <th class="text-center p-2"><b>Qty</b></th>
              <th class="text-center p-2"><b>Status</b></th>
            </thead>
            <tbody style="font-size: 8pt;">
              @forelse($rejection as $row)
              <tr>
                <td class="text-center"><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
                <td class="text-center">
                  <span class="d-block">{{ $row['operator_name'] }}</span>
                </td>
                <td class="text-center">
                  <span class="d-block">{{ $row['rejected_qty'] }}</span>
                </td>
                <td class="text-center">
                  @php
                    if($row['status'] == 'For Confirmation'){
                      $badge = 'badge-warning';
                    }else if($row['status'] == 'QC Failed'){
                      $badge = 'badge-danger';
                    }else{
                      $badge = 'badge-success';
                    }
                  @endphp
                  <span class="badge {{ $badge }}" style="font-size: 7.5pt;">{{ $row['status'] }}</span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center font-weight-bold">No Rejection</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>