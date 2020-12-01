<div class="table-responsive">
	<table class="table table-striped">
		<col style="width: 25%;">
		<col style="width: 25%;">
		<col style="width: 25%;">
		<col style="width: 25%;">
		<thead class="text-primary" style="font-size: 8pt;">
			<th class="text-center"><b>STE No.</b></th>
			<th class="text-center"><b>Feedbacked Qty</b></th>
			<th class="text-center"><b>Transaction Date</b></th>
			<th class="text-center"><b>Feedbacked By</b></th>
		</thead>
		<tbody style="font-size: 9pt;">
			@forelse ($feedbacked_log as $row)
			<tr>
				<td class="text-center">{{ $row->ste_no }}</td>
				<td class="text-center">
					<span class="d-block font-weight-bold">{{ $row->feedbacked_qty }}</span>
				</td>
				<td class="text-center">

					<span class="d-block" style="font-size: 8pt;">{{ $row->created_at }}</span>
				</td>
				<td class="text-center">{{ $row->created_by }}</td>
			</tr>
			@empty
			<tr>
					<td colspan="7" class="text-center" style="font-size: 10pt;">No Record Found</td>
			</tr>
			@endforelse
		</tbody>
	</table>
</div>
