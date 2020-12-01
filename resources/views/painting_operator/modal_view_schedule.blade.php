
<div class="modal fade" id="view-scheduled-task-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="min-width: 95%;">
      <div class="modal-content">
        <div class="modal-header text-white " style="background-color: #0277BD;">
            <h5 class="modal-title font-weight-bold">Painting Schedule [{{ date('l, d-M-Y') }}]</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="row" style="margin-top: -1%;">
                  <div class="col-md-12">
                    <div id="view-scheduled-task-tbl"></div>
                  </div>               
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding-top: -100px;">Close</button>
          </div>
      </div>
    </div>
  </div>