<!-- Modal -->
<div class="modal fade" id="qc-rejects-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span style="font-weight: bold; color: red;">Item Reject</span> - Quality Inspection <b>[@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif]</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div id="tbl-qc-rejects"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>