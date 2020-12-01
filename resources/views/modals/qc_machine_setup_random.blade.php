<!-- Modal -->
<div class="modal fade" id="qc-ms-ri-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="qc-type" style="font-weight: bold; color: green;"></span> - Quality Inspection <b>[@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif]</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <div id="tbl-qc-ms-ri"></div>
          </div>
            </div>
        <!-- Nav tabs -->
        {{-- <ul class="nav nav-tabs" id="#1" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="machine-setup-tab" data-toggle="tab" href="#machine-setup" role="tab" aria-controls="machine-setup" aria-selected="true">Machine Setup</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="rejects-tab" data-toggle="tab" href="#rejects" role="tab" aria-controls="rejects" aria-selected="false">Reject Confirmation</a>
          </li>
        </ul> --}}

        <!-- Tab panes -->
        {{-- <div class="tab-content">
          <div class="tab-pane active" id="machine-setup" role="tabpanel" aria-labelledby="machine-setup-tab">
            <div class="table-responsive">
              <div></div>
            </div>
          </div>
          <div class="tab-pane" id="rejects" role="tabpanel" aria-labelledby="rejects-tab">
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-12">
                <div class="table-responsive">
                  <table class="table scrolltbody" id="rejects-for-confirmation-tbl">
                    <col style="width: 10%;">
                    <col style="width: 30%;">
                    <col style="width: 20%;">
                    <col style="width: 20%;">
                    <col style="width: 20%;">
                    <thead class="text-primary" style="font-size: 8pt;">
                        <th class="text-center"><b>Production Order</b></th>
                        <th class="text-center"><b>Item Code</b></th>
                        <th class="text-center"><b>Reject Qty</b></th>
                        <th class="text-center"><b>Rejection Type</b></th>
                        <th class="text-center"><b>Actions</b></th>
                    </thead>
                    <tbody style="font-size: 9pt;"></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div> --}}
      </div>
    </div>
  </div>
</div>