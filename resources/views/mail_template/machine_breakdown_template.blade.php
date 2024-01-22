<p>Repoted by: <b> {{ $data['employee_name'] }}</b></p>
<p>Machine Code: <b>{{ $data['slip_id'] }}</b></p>
<p>{{ $data['type'] }}: <b>{{ $data['reason'] }}</b> </p>
@if($data['type'] =='Breakdown') <p>WARNING: <b>PRODUCTION HAS BEEN INTERRUPTED</b> </p> @endif
<br>

<p>Please Log in to http://10.0.0.83 to Check Request</p><br><b>Fumaco Inc / Machine Maintenance Request {{ $data['year'] }} </b><br></br><small>Auto Generated E-mail from MES - NO REPLY </small>
