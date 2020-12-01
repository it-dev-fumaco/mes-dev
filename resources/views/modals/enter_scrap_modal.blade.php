<!-- Modal -->
<div class="modal fade" id="enter-scrap-modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-lg" role="document" style="min-width: 70%;">
      <form id="enter-scrap-frm" action="/submit_scrap" method="post" autocomplete="off">
         @csrf
         <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #C62828;">
               <h5 class="modal-title"><i class="now-ui-icons ui-1_simple-remove" style="font-size: 15pt;"></i> Usable Scrap [<b>{{ $workstation }}</b>]</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
				</div>
				<input type="hidden" name="job_ticket_id" id="scrap-job-ticket-id">
				<input type="hidden" name="scrap_type" value="Usable">
            <div class="modal-body">
               <div class="row">
                  <div class="col-md-6 pl-4 pr-0 pt-4">
							<div style="font-size: 14pt; margin-bottom: 8px;" class="text-center pb-5">
								<span id="scrap-production-order" style="font-weight: bold;">PROM-00000</span>
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
											<input type="text" class="form-control form-control-lg qty-input" data-edit="0" name="material" id="scrap-material" value="0" readonly
											style="background-color:#E5E8E8;">
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<label for="scrap-length" style="font-size: 14pt;">Length</label>
									</td>
									<td>
										<div class="input-group text-center">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="1" name="length" id="scrap-length" value="0" readonly  style="background-color:#E5E8E8;">
											<div class="input-group-append text-center">
											<span class="input-group-text" style="border-radius: 0px; background-color:#E5E8E8;">mm</span>
											</div>
										</div>
										{{--  <div class="form-group">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="1" name="length" id="scrap-length"  value="0" readonly>
										</div>  --}}
									</td>
								</tr>
								<tr>
									<td>
										<label for="scrap-width" style="font-size: 14pt;">Width</label>
									</td>
									<td>
										<div class="input-group text-center">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="1" name="width" id="scrap-width" value="0" readonly  style="background-color:#E5E8E8;">
											<div class="input-group-append text-center">
											<span class="input-group-text" style="border-radius: 0px; background-color:#E5E8E8;">mm</span>
											</div>
										</div>
										{{--  <div class="form-group">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="1" name="width" id="scrap-width"  value="0" readonly>
										</div>  --}}
									</td>
								</tr>
								<tr>
									<td>
										<label for="scrap-thickness" style="font-size: 14pt;">Thickness</label>
									</td>
									<td>
										<div class="input-group text-center">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="0" name="thickness" id="scrap-thickness" value="0" readonly  style="background-color:#E5E8E8;">
											<div class="input-group-append text-center">
											<span class="input-group-text" style="border-radius: 0px; background-color:#E5E8E8;">mm</span>
											</div>
										</div>
										{{--  <div class="form-group">
											<input type="text" class="form-control form-control-lg qty-input" data-edit="0" name="thickness" id="scrap-thickness"  value="0" readonly>
										</div>  --}}
									</td>
								</tr>
							</table>
                  </div>
                  <div class="col-md-6">
							<div class="row">
								<div class="col-md-10 offset-md-1">
									<div class="form-group text-center">
										<label for="scrap-qty" style="font-size: 14pt;">Quantity</label>
										<input type="text" class="form-control form-control-lg qty-input" data-edit="1" name="qty" id="scrap-qty"  value="0" readonly
										style="background-color:#E5E8E8;">
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