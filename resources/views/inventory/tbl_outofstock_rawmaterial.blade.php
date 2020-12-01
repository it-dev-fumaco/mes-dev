<div class="table-responsive">
	<table class="table table-striped">
			<col style="width: 15%;">
		    <col style="width: 25%;"> 
		    <col style="width: 25%;"> 
		    <col style="width: 10%;"> 
		    <col style="width: 25%;"> 
			<thead style="font-size:10px;">
				<tr>
					<th >Item Code</th>
					<th >Decription</th>
					<th >Item Classification</th>
					<th >Min Stock Level</th>
					<th >Default Warehouse</th>
				</tr>
			</thead>
			<tbody>
				@forelse($data as $row)
				<tr style="font-size:11px;">
					<td class="text-center">{{$row['item_code']}}</td>
					<td class="text-left">{{$row['description']}}</td>
					<td class="text-center">{{$row['item_class']}}</td>
					<td class="text-center">{{$row['minimum']}}</td>
					<td class="text-center">{{$row['default_warehouse']}}</td>
				</tr>
				@empty
					<tr>
		          <td colspan="6" class="text-center">No Record(s) Found.</td>
		        </tr>
				@endforelse
			</tbody>
	</table>
</div>

