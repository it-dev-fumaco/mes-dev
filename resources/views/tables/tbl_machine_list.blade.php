<hr>
<b><label>Machine Details</label></b>
@if ($machine_list)
	<div>
		<label>Machine Name: {{ $machine_list->machine_name }} </label><br>
		<label>Model: {{ $machine_list->model }}</label><br>
		<label>Type: {{ $machine_list->type }} </label><br>
	</div>	
@else
	<div>
		<label>Machine not found.</label>
	</div>
@endif
