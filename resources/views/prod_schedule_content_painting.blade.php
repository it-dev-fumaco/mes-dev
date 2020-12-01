<ul class="nav nav-tabs" id="workstation-tab-list" role="tablist">
  {{-- <li class="nav-item">
    <a class="nav-link " id="production-output-tab" data-toggle="tab" href="#production-output" role="tab" aria-controls="fabrication" aria-selected="true">Production Output Summary</a>
  </li> --}}
  @foreach($workstations as $i => $row)
  <li class="nav-item">
    <a class="nav-link active" id="wtab{{$i}}" data-toggle="tab" href="#w{{$i}}" role="tab" aria-controls="fabrication" aria-selected="false">{{ $row->name }}</a>
  </li>
  @endforeach
</ul>
<!-- Tab panes -->
<div class="tab-content" style="min-height: 620px;">
  {{-- <div class="tab-pane" id="production-output" role="tabpanel" aria-labelledby="production-output-tab">
    <br>
    <div class="row">
      @forelse($output as $row)
      @if(count($row['output']) > 0)
      <div class="col-md-3">
         <table class="table table-bordered scrolltbody1">
          <thead class="bg-primary text-white" style="font-size: 8pt;">
            <th class="text-center" colspan="3"><b>{{ $row['workstation'] }}</b></th>
          </thead>
          <tbody>
            <tr>
              <td class="text-center">Machine</td>
              <td class="text-center">Operator</td>
              <td class="text-center">Produced Qty</td>
            </tr>
            @forelse($row['output'] as $o)
            <tr>
              <td class="text-center">{{ $o['machine_name'] }}</td>
              <td class="text-center">{{ $o['operator_name'] }}</td>
              <td class="text-center">{{ number_format($o['qty']) }}</td>
            </tr>
            @empty
            <tr>
              <td class="text-center" colspan="3">No record(s) found.</td>
            </tr>
            @endforelse
            @if(count($row['output']) > 0)
            <tr>
              <td class="text-center" colspan="2"><b>Total Part(s) Produced</b></td>
              <td class="text-center"><b>{{ collect($row['output'])->sum('qty') }}</b></td>
            </tr>
            <tr>
              <td class="text-center" colspan="2"><b>Production Runtime</b></td>
              <td class="text-center"><b>{{ $row['total_runtime'] }}</b></td>
            </tr>
            @endif
          </tbody>
        </table>
      </div>
      @endif
      @empty
      @endforelse
    </div>
  </div> --}}
  @foreach($workstations as $i => $row)
  @php
    $jt = collect($prodorders)->where('workstation', $row->name);
  @endphp
  <div class="tab-pane active" id="w{{$i}}" role="tabpanel" aria-labelledby="wtab{{$i}}">
    <table class="table table-bordered scrolltbody">
      <thead class="text-secondary" style="font-size: 8pt;">
        <th class="text-center" style="width: 5%;"><b>Prod. No.</b></th>
        <th class="text-center" style="width: 10%;"><b>SO No.</b></th>
        <th class="text-center" style="width: 25%;"><b>Item Code</b></th>
        <th class="text-center" style="width: 5%;"><b>Qty</b></th>
        <th class="text-center" style="width: 5%;"><b>Good</b></th>
        <th class="text-center" style="width: 5%;"><b>Reject</b></th>
        <th class="text-center" style="width: 5%;"><b>Rework</b></th>
        <th class="text-center" style="width: 5%;"><b>Balance</b></th>
        <th class="text-center" style="width: 10%;"><b>Operator</b></th>
        <th class="text-center" style="width: 10%;"><b>Machine</b></th>
        <th class="text-center" style="width: 5%;"><b>Start</b></th>
        <th class="text-center" style="width: 5%;"><b>End</b></th>
        <th class="text-center" style="width: 5%;"><b>Duration</b></th>
      </thead>
      <tbody>
        @forelse($jt as $r)
        @php
        $is_hidden = (in_array($r['status'], ['Unassigned', 'Accepted'])) ? 'hidden' : '';
        @endphp
        <tr>
          <td class="text-center" style="width: 5%;">{{ $r['production_order'] }}</td>
          <td class="text-center" style="width: 10%;">@if($r['sales_order_no']){{ $r['sales_order_no'] }}<br>Customer: {{ $r['customer'] }}<br>Delivery Date: {{ $r['delivery_date'] }}@endif</td>
          <td class="text-left" style="width: 25%;"><b>{{ $r['production_item'] }}</b><br>{{$r['description'] }}</td>
          <td class="text-center" style="width: 5%; font-size: 12pt;"><b>{{ number_format($r['qty']) }}</b></td>
          <td class="text-center" style="width: 5%; font-size: 12pt; background-color: {{ ($r['good'] > 0) ? '#27AE60' : '#7F8C8D' }};">
            <b>{{ number_format($r['good']) }}</b>
          </td>
          <td class="text-center" style="width: 5%; font-size: 12pt; background-color: {{ ($r['reject'] > 0) ? '#E74C3C' : '#7F8C8D' }};">
            <b>{{ number_format($r['reject']) }}</b>
          </td>
          <td class="text-center" style="width: 5%; font-size: 12pt; background-color: {{ ($r['rework'] > 0) ? '#E74C3C' : '#7F8C8D' }};">
            <b>{{ number_format($r['rework']) }}</b>
          </td>
          <td class="text-center" style="width: 5%; font-size: 12pt; background-color: {{ ($r['balance'] > 0) ? '#F5B041' : '#7F8C8D' }};">
            <b>{{ $r['balance'] }}</b>
          </td>
          <td class="text-center" style="width: 10%;">{{ $r['operator_name'] }}</td>
          <td class="text-center" style="width: 10%;">{{ $r['machine_name'] }}</td>
          <td class="text-center" style="width: 5%;"><span {{$is_hidden}}>{{ $r['from_time'] }}</span></td>
          <td class="text-center" style="width: 5%;"><span {{$is_hidden}}>{{ $r['to_time'] }}</span></td>
          <td class="text-center" style="width: 5%;"><span {{$is_hidden}}>{{ $r['duration'] }}</span></td>
        </tr>
        @empty
        <tr>
          <td class="text-center">No scheduled task(s) found for <b>{{$row->name}}</b></td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @endforeach
</div>

<style type="text/css">
  .scrolltbody1 tbody {
      display:block;
      height:400px;
      overflow:auto;
  }
  .scrolltbody1 thead, .scrolltbody1 tbody tr {
      display:table;
      width:100%;
      table-layout:fixed;
  }
  .scrolltbody1 thead {
      width: calc(100%)
  }
</style>