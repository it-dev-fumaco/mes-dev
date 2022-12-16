<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="machine_breakdownhistory-{{ $rows['id'] }}">
  <div class="modal-dialog modal-lg" style="min-width: 60%;">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #0277BD;">
        <h5 class="modal-title" id="workstation_name_title">Machine Breakdown Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table style="width: 100%;" class="table-borderless">
              <tbody>
                <tr>
                  <td style="width: 15%;" class="text-left p-2"><b>Machine Code:</b></td>
                  <td style="width: 35%;" class="text-left p-2">{{ $rows['machine_code'] }}</td>
                  <td style="width: 20%;" class="text-left p-2"><b>Status:</b></td>
                  <td style="width: 30%;" class="text-left p-2">{{ $rows['status']}}</td>
                </tr>
                <tr>
                  <td class="text-left p-2"><b>Category:</b></td>
                  <td class="text-left p-2">{{ $rows['category'] }}</td>
                  <td class="text-left p-2"><b>Date Reported:</b></td>
                  <td class="text-left p-2">{{ $rows['date_reported'] ? \Carbon\Carbon::parse($rows['date_reported'])->format('M. d, Y h:i A') : '-' }}</td>
                </tr>
                <tr>
                  <td class="text-left p-2"><b>Reported by:</b></td>
                  <td class="text-left p-2">{{ $rows['reported_by'] }}</td>
                  <td class="text-left p-2"><b>Assigned Maintenance Staff:</b></td>
                  <td class="text-left p-2">{{ $rows['assigned_maintenance_staff'] }}</td>
                </tr>
                <tr>
                  <td class="text-left p-2"><b>{{ ($rows['type'] == "Corrective") ? "Corrective Reason:": "Breakdown Reason:"  }}</b></td>
                  <td class="text-left p-2">{{ ($rows['type'] == "Corrective") ? $rows['corrective_reason']: $rows['breakdown_reason']  }}</td>
                  <td class="text-left p-2"><b>Remarks:</b></td>
                  <td class="text-left p-2">{{ $rows['remarks'] }}</td>
                </tr>
                <tr>
                  <td class="text-left p-2"><b>Work Done:</b></td>
                  <td class="text-left p-2">{{ $rows['work_done']  }}</td>
                </tr>
              </tbody>
            </table>   
          </div>
        </div>
      </div>
    </div>
  </div>
</div>