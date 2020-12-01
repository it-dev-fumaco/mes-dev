<!-- Modal -->
<div class="modal fade" id="select-process-for-scrap-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document" style="min-width: 90%;">
      <div class="modal-content">
         <div class="modal-header text-white" style="background-color: #0277BD;">
            <h5 class="modal-title">Select Process [<b>{{$workstation}}</b>]</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <input type="hidden" name="scrap_id">
            <div class="row" id="select-process-for-scrap-table">
            </div>
         </div>
      </div>
   </div>
</div>