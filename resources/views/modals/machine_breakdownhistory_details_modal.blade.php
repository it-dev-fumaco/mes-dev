<!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="machine_breakdownhistory-{{ $rows['id'] }}">
  <div class="modal-dialog modal-lg">
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
                <tr style="line-height: 5px;">
                  <td style="width: 20%;text-align: left; line-height: 5px;"><b>Machine Code:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 5px;">{{ $rows['machine_code'] }}</td>
                  <td style="width: 20%;text-align: left; line-height: 5px;"><b>Status:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 5px;">{{ $rows['status']}}</td>
                </tr>
                <tr style="line-height: 5px;">
                  <td style="width: 20%;text-align: left; line-height: 5px;"><b>Category:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 5px;">{{ $rows['category'] }}</td>
                  <td style="width: 20%;text-align: left; line-height: 5px;"><b>Date Reported:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 5px;">{{ $rows['date_reported'] }}</td>
                </tr>
                <tr style="line-height: 5px;">
                  <td style="width: 20%;text-align: left; line-height: 5px;"><b>Reported by:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 5px;">{{ $rows['reported_by'] }}</td>
                  <td style="width: 30%;text-align: left; line-height: 5px;"><b>Assigned Maintenance Staff:</b></td>
                  <td style="width: 20%;text-align: left; line-height: 5px;">{{ $rows['assigned_maintenance_staff'] }}</td>
                </tr>
                <tr style="line-height: 5px;">
                  <td style="width: 20%;text-align: left; line-height: 5px;"><b>{{ ($rows['type'] == "Corrective") ? "Corrective Reason:": "Breakdown Reason:"  }}</b></td>
                  <td style="width: 25%;text-align: left; line-height: 5px;">{{ ($rows['type'] == "Corrective") ? $rows['corrective_reason']: $rows['breakdown_reason']  }}</td>
                  <td style="width: 20%;text-align: left; line-height: 5px;"><b>Remarks:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 5px;">{{ $rows['remarks'] }}</td>
                </tr>
                <tr style="line-height: 5px;">
                  <td style="width: 20%;text-align: left; line-height: 5px;"><b>Work Done:</b></td>
                  <td style="width: 25%;text-align: left; line-height: 5px;">{{ $rows['work_done']  }}</td>
                </tr>
              </tbody>
              
            </table>   
          </div>
        </div>
      </div>
    </div>
  </div>
</div>