<div class="card mt-2 mb-2">
    <div class="card-body pb-0 pt-0">
      <div class="row bg-white">
        <div class="col-md-12 p-0">
          <h6 class="text-white font-weight-bold text-center m-0 p-2" style="font-size: 10.5pt; border: 1px solid; background-color: #0772DD;">Quality Assurance</h6>
          <table class="table table-striped table-bordered text-center m-0">
            <col style="width: 45%">
            <col style="width: 30%">
            <col style="width: 25%">
            <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
              <th class="text-center p-2"><b>QA Inspector</b></th>
              <th class="text-center p-2"><b>Total Inspected</b></th>
              <th class="text-center p-2"><b>Total Qty</b></th>
            </thead>
            <tbody style="font-size: 9pt;">
              @forelse($per_inspector as $row)
              <tr>
                <td class="text-center">{{ $row['inspector'] }}</td>
                <td class="text-center">{{ $row['production_order'] }}</td>
                <td class="text-center">{{ $row['qty'] }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center font-weight-bold">No Quality Inspection</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          <h6 class="text-white font-weight-bold text-center m-0 p-2" style="font-size: 10.5pt; border: 1px solid; background-color: #0772DD;">Rejection</h6>
          <table class="table table-striped table-bordered text-center m-0 custom-table-fixed-1">
            <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
              <tr>
                <th class="text-center p-2" style="width: 100px;"><b>Prod. Order</b></th>
                <th class="text-center p-2" style="width: 135px;"><b>Operator</b></th>
                <th class="text-center p-2" style="width: 85px;"><b>Qty</b></th>
                <th class="text-center p-2" style="width: 100px;"><b>Status</b></th>
              </tr>
            </thead>
            <tbody style="font-size: 8pt; max-height: 300px;">
              @forelse($rejection as $row)
              @php
                if($row['status'] == 'For Confirmation'){
                  $badge = 'badge-warning';
                }else if($row['status'] == 'QC Failed'){
                  $badge = 'badge-danger';
                }else{
                  $badge = 'badge-success';
                }
              @endphp
              <tr>
                <td class="text-center" style="width: 100px;"><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
                <td class="text-center" style="width: 135px;">{{ $row['operator_name'] }}</td>
                <td class="text-center" style="width: 85px;">{{ $row['rejected_qty'] }}</td>
                <td class="text-center" style="width: 85px;">
                  <span class="badge {{ $badge }}" style="font-size: 7.5pt;">{{ $row['status'] }}</span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center font-weight-bold" style="width: 420px;">No Rejection</td>
              </tr>
              @endforelse
            </tbody>
          </table>
          <h6 class="text-white font-weight-bold text-center m-0 p-2" style="font-size: 10.5pt; border: 1px solid; background-color: #0772DD;">Quality Inspection Logs</h6>
          <table class="table table-striped table-bordered text-center m-0 custom-table-fixed-1">
            <thead class="text-white bg-secondary" style="font-size: 6.5pt;">
              <tr>
                <th class="text-center p-2" style="width: 100px;"><b>Prod. Order</b></th>
                <th class="text-center p-2" style="width: 135px;"><b>QA Inspector</b></th>
                <th class="text-center p-2" style="width: 85px;"><b>Qty</b></th>
                <th class="text-center p-2" style="width: 100px;"><b>Status</b></th>
              </tr>
            </thead>
            <tbody style="font-size: 8pt; max-height: 400px;">
              @forelse($quality_inspection as $row)
              @php
                if($row['status'] == 'For Confirmation'){
                  $badge = 'badge-warning';
                }else if($row['status'] == 'QC Failed'){
                  $badge = 'badge-danger';
                }else{
                  $badge = 'badge-success';
                }
              @endphp
              <tr>
                <td class="text-center" style="width: 100px;"><a href="#" data-jtno="{{ $row['production_order'] }}" class="prod-details-btn text-dark">{{ $row['production_order'] }}</a></td>
                <td class="text-center" style="width: 135px;">{{ $row['inspected_by'] }}</td>
                <td class="text-center" style="width: 85px;">{{ $row['quantity'] }}</td>
                <td class="text-center" style="width: 85px;">
                  <span class="badge {{ $badge }}" style="font-size: 7.5pt;">{{ $row['status'] }}</span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center font-weight-bold" style="width: 420px;">No Quality Inspection</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>