<!-- Modal -->
<div class="modal fade" id="end-scrap-task-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document" style="min-width: 70%;">
      <form action="/end_scrap_task" method="post" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #0277BD;">
               <h5 class="modal-title">Complete Task [<b>{{ $workstation }}</b>]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
			</div>
			<input type="hidden" name="id" class="timelog-id">
            <input type="hidden" name="balance_qty" class="balance-qty">
            <input type="hidden" name="production_order" class="production-order-input">
            <input type="hidden" name="workstation" class="workstation-input">
            <input type="hidden" id="material-density">
            <input type="hidden" name="completed_qty_kg">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6 pl-4 pr-0 pt-2">
							<div style="font-size: 13pt;" class="text-center pb-5">
								<span class="d-block font-weight-bold production-order">PROM-00000</span>
                  				<span class="d-block font-weight-bold process-name">Process</span>
                  				<span class="d-block mt-3">Total Scrap Remaining</span>
                  				<span class="d-block font-weight-bold max-qty" id="total-scrap-remaining">0</span> KG
							</div>
							<table style="width: 100%;">
								<col style="width: 30%;">
								<col style="width: 70%;">
								<tr>
									<td>
										<label for="scrap-material" style="font-size: 14pt;">Material</label>
									</td>
									<td>
										<div class="form-group">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="0" name="material" value="0" readonly style="background-color:#E5E8E8;" id="end-scrap-task-material">
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<label for="scrap-length" style="font-size: 14pt;">Length</label>
									</td>
									<td>
										<div class="input-group text-center">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="1" name="length" value="0" readonly  style="background-color:#E5E8E8;" id="end-scrap-task-length">
											<div class="input-group-append text-center">
											<span class="input-group-text" style="border-radius: 0px; background-color:#E5E8E8;">mm</span>
											</div>
										</div>
										
									</td>
								</tr>
								<tr>
									<td>
										<label for="scrap-width" style="font-size: 14pt;">Width</label>
									</td>
									<td>
										<div class="input-group text-center">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="1" name="width" value="0" readonly  style="background-color:#E5E8E8;" id="end-scrap-task-width">
											<div class="input-group-append text-center">
											<span class="input-group-text" style="border-radius: 0px; background-color:#E5E8E8;">mm</span>
											</div>
										</div>
									
									</td>
								</tr>
								<tr>
									<td>
										<label for="scrap-thickness" style="font-size: 14pt;">Thickness</label>
									</td>
									<td>
										<div class="input-group text-center">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="0" name="thickness" value="0" readonly  style="background-color:#E5E8E8;" id="end-scrap-task-thickness">
											<div class="input-group-append text-center">
											<span class="input-group-text" style="border-radius: 0px; background-color:#E5E8E8;">mm</span>
											</div>
										</div>
										
									</td>
								</tr>
							</table>
                  </div>
                  <div class="col-md-6">
							<div class="row">
								<div class="col-md-10 offset-md-1">
									<div class="form-group text-center">
										<label for="scrap-qty" style="font-size: 14pt;">Enter Completed Qty</label>
										<input type="text" class="form-control form-control-lg qty-input" data-edit="1" name="completed_qty" value="0" readonly style="background-color:#E5E8E8;" id="end-scrap-task-qty">
										<!-- <small class="form-text text-muted" style="font-size: 14pt;"><b>Maximum: <span class="max-qty">0</span></b></small> -->
								  </div>
								</div>
							</div>
                    
                    <style>
                        .qty-input{
                           font-size: 20pt; text-align: center; font-weight: bolder;
                        }
                    </style>
                    <div class="text-center numpad-div">
                       <div class="row1">
                          <span class="numpad num">1</span>
                          <span class="numpad num">2</span>
                          <span class="numpad num">3</span>
                       </div>
                       <div class="row1">
                          <span class="numpad num">4</span>
                          <span class="numpad num">5</span>
                          <span class="numpad num">6</span>
                       </div>
                       <div class="row1">
                          <span class="numpad num">7</span>
                          <span class="numpad num">8</span>
                          <span class="numpad num">9</span>
                       </div>
                       <div class="row1">
                          <span class="numpad del"><</span>
                          <span class="numpad num">0</span>
                          <span class="numpad num">.</span>
                       </div>
                    </div>
                 </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
                  </div>
                  <div class="col-md-6">
                     <button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>