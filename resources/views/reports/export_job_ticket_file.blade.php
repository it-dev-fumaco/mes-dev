<input type="text" class="d-none" id="file_name" value="Job-Ticket--{{ Carbon\Carbon::parse($min_export_date)->format('m-d-Y').'--'.Carbon\Carbon::parse($max_export_date)->format('m-d-Y') }}">
<table id="export-table" class="table table-bordered">
   <tr>
      <th>created_at</th>
      <th>production_order</th>
      <th>item_code</th>
      <th>description</th>
      <th>operation</th>
      <th>reference_no</th>
      <th>customer</th>
      <th>status</th>
      <th>workstation</th>
      <th>process</th>
      <th>good</th>
      <th>reject</th>
   </tr>
   @forelse($export_arr as $export)
      @php
         $reference_number = $export['sales_order'] ? $export['sales_order'] : $export['material_request'];
      @endphp
      <tr>
         <td>{{ $export['created_at'] }}</td>
         <td>{{ $export['production_order'] }}</td>
         <td>{{ $export['item_code'] }}</td>
         <td>{{ $export['item_description'] }}</td>
         <td>{{ $export['operation'] }}</td>
         <td>{{ $reference_number }}</td>
         <td>{{ $export['customer'] }}</td>
         <td>{{ $export['status'] }}</td>
         <td>{{ $export['workstation'] }}</td>
         <td>{{ $export['process_name'] }}</td>
         <td>{{ $export['good'] }}</td>
         <td>{{ $export['reject'] }}</td>
      </tr>
   @empty
      <tr>
         <td colspan="10">No result(s) found.</td>
      </tr>
   @endforelse
</table>
<script>
   $(document).ready(function(){
      var min = "{{ $min_export_date }}";
      var max = "{{ $max_export_date }}";
      alert('Data export is limited to 1000 records per export. Data exported from ' + min + ' to ' + max);
   });
</script>