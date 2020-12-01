<!-- Modal Production Order Enter Operator ID -->
<div class="modal fade" id="select-late-delivery-reason-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header text-white" style="background-color: #0277BD;">
				<h5 class="modal-title">Select Late Delivery Reason</h5>
				 {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>  --}}
			</div>
			<div class="modal-body">
				<form>
					@csrf
					<input type="hidden" id="custom-production-order" name="production_order">
					<input type="hidden" name="delivery_date">
					<input type="hidden" name="reschedule_date">
					<div class="row">
						<div class="col-md-12">
							<table style="width: 100%;">
								<tr>
									<td style="width:50%;">
										<div class="production-order font-weight-bold m-2">PROM-00000</div>
									</td>
									<td style="width:50%;">
										<div class="m-2">Reference No. <span class="reference font-weight-bold">SO-00001</span></div>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<div class="m-2">
											<span class="item-code font-weight-bold">FG00001</span> - <span class="item-description">DESCRIPTION</span>
										</div>
									</td>
								</tr>
								<tr>
									<td><div class="m-2">Qty to Manufacture: <span class="qty-uom font-weight-bold m-2">0</span></div></td>
								</tr>
								<tr>
									<td><div class="m-2">Delivery Date: <span class="delivery-date font-weight-bold m-2"></span></div></td>
									<td><div class="m-2 text-center font-weight-bold">Select Late Delivery Reason</div></td>
								</tr>
								<tr>
									<td>
										<div class="m-2">Rescheduled Delivery Date: <span class="rescheduled-date font-weight-bold mt-5 mb-2 ml-2"></span></div>
									</td>
									<td>
										<div class="form-group m-0">
											<select class="form-control" name="late_delivery_reason" required>
												<option value="test">Late Delivery Reason</option>
											</select>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<div class="col-md-6 offset-md-3 mt-3">
							<button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>