<div class="table-responsive">
  <table border="1" style="font-size: 8pt; width: 2680px;">
    <tr class="font-weight-bold text-white">
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 150px; background-color:#0277BD;">Prod. Order</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 120px; background-color:#0277BD;">Ref. No.</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 380px; background-color:#0277BD;">Item Details</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 150px; background-color:#0277BD;">Workstation</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 200px; background-color:#0277BD;">Process</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 100px; background-color:#0277BD;">Machine</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 100px; background-color:#0277BD;">Date/Time</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 100px; background-color:#0277BD;">Batch Qty</td>
      @foreach ($reject_category as $s)
      <td colspan="3" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 180px; background-color:#0277BD;">{{ $s->reject_category_name }}</td>
      @endforeach
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 130px; background-color:#0277BD;">Status</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 150px; background-color:#0277BD;">Remarks</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 150px; background-color:#0277BD;">Operator</td>
      <td rowspan="2" class="text-center p-1" style="border: 1px solid #ABB2B9; width: 150px; background-color:#0277BD;">QC Staff</td>
    </tr>
    <tr class="font-weight-bold text-white">
      @foreach ($reject_category as $s)
      <td class="text-center p-1" style="border: 1px solid #ABB2B9; width: 60px; background-color:#0277BD;">Sample</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9; width: 60px; background-color:#0277BD;">Actual</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9; width: 60px; background-color:#0277BD;">Reject</td>
      @endforeach
    </tr>
    @forelse ($data as $r)
    <tr>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">
        <span class="d-block font-weight-bold">{{ $r['production_order'] }}</span>
        <small class="d-block">{{ $r['customer'] }}</small>
      </td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['reference'] }}</td>
      <td class="text-justify p-1" style="border: 1px solid #ABB2B9;">
        <b>{{ $r['item_code'] }}</b> - {{ $r['description'] }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['workstation'] }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['process_name'] }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['machine_code'] }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['qa_inspection_date'] }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['batch_qty'] }}</td>
      @foreach ($reject_category as $s)
      @php
        $qa_result = isset($qa_results[$s->reject_category_id][$r['reference_id']][$r['qa_id']]) ? $qa_results[$s->reject_category_id][$r['reference_id']][$r['qa_id']] : [];
      @endphp
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ isset($qa_result['sample_size']) ? $qa_result['sample_size'] : 'n/a' }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ isset($qa_result['actual_qty_checked']) ? $qa_result['actual_qty_checked'] : 'n/a' }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ isset($qa_result['rejected_qty']) ? $qa_result['rejected_qty'] : 'n/a' }}</td>
      @endforeach
      @php
        if ($r['status'] == "QC Passed") {
          $div_color = "background-color: #52be80;";
        }elseif ($r['status'] == "QC Failed") {
          $div_color = "background-color: #ec7063;";
        }else{
          $div_color = "background-color: #fae5d3;";
        }
      @endphp
      <td class="text-center p-0" style="border: 1px solid #ABB2B9; {{ $div_color }}">{{ $r['status'] }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['remarks'] }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['operator'] }}</td>
      <td class="text-center p-1" style="border: 1px solid #ABB2B9;">{{ $r['qc_staff'] }}</td>
    </tr>
    @empty
      <tr>
        <td colspan="24" class="text-center text-muted text-uppercase p-2" style="font-size: 15px; border: 1px solid #ABB2B9;">No inspection logs found</td>
      </tr>
    @endforelse
  </table>
</div>