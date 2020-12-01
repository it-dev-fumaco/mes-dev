<!-- Modal -->
<div class="modal fade" id="quality-check-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header text-white" style="background-color: #f57f17;">
            <h5 class="modal-title">
               <span class="qc-type" style="font-weight: bold;"></span> - Qual1ity Inspection <b>[@if(isset($workstation_name)){{ $workstation_name }}@else{{$workstation}}@endif]</b>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div id="quality-check-div"></div>
         </div>
      </div>
   </div>
</div>