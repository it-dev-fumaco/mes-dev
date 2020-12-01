<div class="row">
	<div class="col-md-12">
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="subassembly-tab" data-toggle="tab" href="#subassembly" role="tab" aria-controls="subassembly" aria-selected="true">Sub Assembly</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="components-tab" data-toggle="tab" href="#components" role="tab" aria-controls="components" aria-selected="false">Assembly Component(s)</a>
			</li>
		</ul>
		<!-- Tab panes -->
		<div class="tab-content" style="min-height: 620px;">
			<div class="tab-pane active" id="subassembly" role="tabpanel" aria-labelledby="subassembly-tab">
				<div class="row" style="margin-top: 10px;">
					<div class="col-md-10 offset-md-1">
						<h5 class="text-center">Sub Assembly</h5>
						<table class="table table-hover" style="font-size: 9pt;">
							<tbody>
			   				@foreach($bom as $idx => $item)
			   				@if(count($item['child_nodes']) > 0)
			   				<tr style="font-size: 11pt;">
			      				<td class="text-justify" colspan="4" style="width: 75%;"><b>{{ $item['item_code'] }}</b> - {!! $item['description'] !!}</td>
			      				<td class="text-left" style="width: 25%;">{{ number_format($item['qty']) }} {{ $item['uom'] }}</td>
			      			</tr>
			      			@foreach($item['child_nodes'] as $child)
			      			<tr style="font-size: 10pt;">
			      				<td class="text-right" style="width: 3%;"><i class="now-ui-icons arrows-1_minimal-right"></i></td>
			      				<td class="text-justify" colspan="3" style="width: 72%;"><b>{{ $child['item_code'] }}</b> - {!! $child['description'] !!}</td>
			      				<td class="text-left" style="width: 25%;">{{ number_format($child['qty']) }} {{ $child['uom'] }}</td>
			      			</tr>
			      			@foreach($child['child_nodes'] as $child1)
			      			<tr>
			      				<td style="width: 3%;"></td>
			      				<td class="text-right" style="width: 3%;"><i class="now-ui-icons arrows-1_minimal-right"></i></td>
			      				<td class="text-justify" style="width: 69%;" colspan="2"><b>{{ $child1['item_code'] }}</b> - {!! $child1['description'] !!}</td>
			      				<td class="text-left" style="width: 25%;">{{ number_format($child1['qty'], 4) }} {{ $child1['uom'] }}</td>
			      			</tr>
			      			@foreach($child1['child_nodes'] as $child2)
			      			<tr>
			      				<td style="width: 3%;"></td>
			      				<td style="width: 3%;"></td>
			      				<td class="text-right" style="width: 3%;"><i class="now-ui-icons arrows-1_minimal-right"></i></td>
			      				<td class="text-justify" style="width: 69%;"><b>{{ $child2['item_code'] }}</b> - {!! $child2['description'] !!}</td>
			      				<td class="text-left" style="width: 25%;">{{ number_format($child2['qty'], 4) }} {{ $child2['uom'] }}</td>
			      			</tr>
			      			@endforeach
			      			@endforeach
			      			@endforeach
			      			@endif
				      		@endforeach
				      	</tbody>
				      </table>
			   	</div>
			   </div>
			</div>
			<div class="tab-pane" id="components" role="tabpanel" aria-labelledby="components-tab">
				<div class="row" style="margin-top: 10px;">
					<div class="col-md-10 offset-md-1">
	            <h5 class="text-center">Assembly Component(s)</h5>
						<table class="table table-hover" style="font-size: 9pt;">
								<tbody>
								@foreach($bom as $idx => $item)
								@if(count($item['child_nodes']) <= 0)
								<tr>
			      				<td class="text-justify" colspan="3" style="width: 75%;"><b>{{ $item['item_code'] }}</b> - {!! $item['description'] !!}</td>
			      				<td class="text-left" style="width: 25%;">{{ number_format($item['qty'], 4) }} {{ $item['uom'] }}</td>
			      			</tr>
	   						@endif
	      					@endforeach
	      				</tbody>
	      			</table>
	          	</div>
	          </div>
	       </div>
	   </div>
	</div>
</div>