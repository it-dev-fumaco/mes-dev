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
										<td class="text-justify" colspan="4" style="width: 75%;">
											<b>{{ $item['item_code'] }}</b>
											@if(!$item['attributes'])
												- {!! $item['description'] !!}										
											@endif
										</td>
										<td class="text-left" style="width: 25%;">{{ number_format($item['qty']) }} {{ $item['uom'] }}</td>
									</tr>
									@if($item['attributes'])
										<tr>
											<td colspan=5 class="p-1" style="border: 1px solid rgba(108, 117, 125, .5); border-style: none solid solid solid !important">
												<table class="table table-bordered" style="font-size: 8.5pt;">
													<tr>
														<th class="p-1">Attribute</th>
														<th class="p-1 text-left">Value</th>
													</tr>
													@foreach ($child['attributes'] as $attribute => $value)
														@if(!in_array($value, ['N/A', 'n/a', 'N/a', 'n/A']))
															<tr>
																<td class="p-1">{{ $attribute }}</td>
																<td class="p-1 text-left">{{ $value }}</td>
															</tr>
														@endif
													@endforeach
												</table>
											</td>
										</tr>
									@endif
									@foreach($item['child_nodes'] as $child)
										<tr style="font-size: 10pt;">
											<td class="text-right" style="width: 3%;"><i class="now-ui-icons arrows-1_minimal-right"></i></td>
											<td class="text-justify" colspan="3" style="width: 72%;">
												<b>{{ $child['item_code'] }}</b>
												@if(!$child['attributes'])
												- {!! $child['description'] !!}
												@endif
											</td>
											<td class="text-left" style="width: 25%; vertical-align: top !important">{{ number_format($child['qty']) }} {{ $child['uom'] }}</td>
										</tr>
										@if($child['attributes'])
											<tr>
												<td colspan=5 class="p-1" style="border: 1px solid rgba(108, 117, 125, .5); border-style: none solid solid solid !important">
													<table class="table table-bordered" style="font-size: 8.5pt;">
														<tr>
															<th class="p-1">Attribute</th>
															<th class="p-1 text-left">Value</th>
														</tr>
														@foreach ($child['attributes'] as $attribute => $value)
															@if(!in_array($value, ['N/A', 'n/a', 'N/a', 'n/A']))
																<tr>
																	<td class="p-1">{{ $attribute }}</td>
																	<td class="p-1 text-left">{{ $value }}</td>
																</tr>
															@endif
														@endforeach
													</table>
												</td>
											</tr>
										@endif
										@foreach($child['child_nodes'] as $child1)
											<tr>
												<td style="width: 3%;"></td>
												<td class="text-right" style="width: 3%;"><i class="now-ui-icons arrows-1_minimal-right"></i></td>
												<td class="text-justify" style="width: 69%;" colspan="2">
													<b>{{ $child1['item_code'] }}</b>
													@if(!$child1['attributes'])
														- {!! $child1['description'] !!}										
													@endif
												</td>
												<td class="text-left" style="width: 25%;">{{ number_format($child1['qty'], 4) }} {{ $child1['uom'] }}</td>
											</tr>
											@if($child1['attributes'])
												<tr>
													<td style="width: 3%;">&nbsp;</td>
													<td colspan=3 class="p-1" style="border: 1px solid rgba(108, 117, 125, .5); border-style: none solid solid solid !important">
														<table class="table table-bordered" style="font-size: 8.5pt;">
															<tr>
																<th class="p-1">Attribute</th>
																<th class="p-1 text-left">Value</th>
															</tr>
															@foreach ($child1['attributes'] as $attribute => $value)
																@if(!in_array($value, ['N/A', 'n/a', 'N/a', 'n/A']))
																	<tr>
																		<td class="p-1">{{ $attribute }}</td>
																		<td class="p-1 text-left">{{ $value }}</td>
																	</tr>
																@endif
															@endforeach
														</table>
													</td>
												</tr>
											@endif
											@foreach($child1['child_nodes'] as $child2)
												<tr>
													<td style="width: 3%;"></td>
													<td style="width: 3%;"></td>
													<td class="text-right" style="width: 3%;"><i class="now-ui-icons arrows-1_minimal-right"></i></td>
													<td class="text-justify" style="width: 69%;">
														<b>{{ $child2['item_code'] }}</b>
														@if(!$child2['attributes'])
															- {!! $child2['description'] !!}										
														@endif
													</td>
													<td class="text-left" style="width: 25%;">{{ number_format($child2['qty'], 4) }} {{ $child2['uom'] }}</td>
												</tr>
												@if($child2['attributes'])
													<tr>
														<td style="width: 3%;">&nbsp;</td>
														<td style="width: 3%;">&nbsp;</td>
														<td colspan=1 class="p-1" style="border: 1px solid rgba(108, 117, 125, .5); border-style: none solid solid solid !important">
															<table class="table table-bordered" style="font-size: 8.5pt;">
																<tr>
																	<th class="p-1">Attribute</th>
																	<th class="p-1 text-left">Value</th>
																</tr>
																@foreach ($child2['attributes'] as $attribute => $value)
																	@if(!in_array($value, ['N/A', 'n/a', 'N/a', 'n/A']))
																		<tr>
																			<td class="p-1">{{ $attribute }}</td>
																			<td class="p-1 text-left">{{ $value }}</td>
																		</tr>
																	@endif
																@endforeach
															</table>
														</td>
													</tr>
												@endif
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