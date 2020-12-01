<!-- Modal -->
<div class="modal fade" id="restart-task-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="restart-task-frm" action="/restart_task" method="post">
      @csrf
      <div class="modal-content">
        <div class="modal-header text-white" style="background-color: #00838f;">
          <h5 class="modal-title" id="modal-title"><i class="now-ui-icons travel_info"></i> Restart Task <span class="workstation"></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" class="timelog-id">
          <div class="text-center"><span style="font-size: 12pt;">Timer will reset.</span></div>
          <br>
          <div class="row">
            <div class="col-md-6">
               <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
            </div>
            <div class="col-md-6">
               <button type="submit" class="btn btn-primary btn-block btn-lg">Confirm</button>
            </div>
         </div>
        </div>
      </div>
    </form>
  </div>
</div>