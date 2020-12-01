<div class="modal fade" id="select-scrap-modal" tabindex="-1" role="dialog">
   	<div class="modal-dialog modal-lg" role="document" style="min-width: 80%;">
		<div class="modal-content">
			<div class="modal-header text-white" style="background-color: #2E86C1;">
				<h5 class="modal-title">Available Usable Scrap</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="#" id="ste-frm">
					<input type="text" name="production_order">
					<input type="text" name="item_code">
					<input type="text" name="s_warehouses[]">
				</form>
				<form action="/update_scrap" method="post" id="scrap-frm">
					@csrf
					<input type="hidden" name="projected_scrap">
					<input type="hidden" id="req-url-input">
					<div id="select-available-scrap-div"></div>
					<div class="row">
						<div class="col-md-6">
							<button type="button" class="btn btn-secondary btn-block btn-lg" data-dismiss="modal">Cancel</button>
						</div>
						<div class="col-md-6">
							<button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
   	</div>
</div>