@if (count($data) > 0)
<span class="d-block border-bottom m-1 p-2 font-weight-bold text-uppercase">In Process Project(s)</span>
<ul style="list-style-type: none; margin: 0; padding: 0;">
  @foreach ($data as $r)
  <li class="ml-1 p-1">
    <div class="d-flex flex-row align-items-center">
      <div class="col-2 p-0">
        <div class="progress m-0">
          <div class="bar" style="width:{{ $r['percentage'] }}%">
            <p class="percent">{{ $r['percentage'] }}%</p>
          </div>
        </div>
      </div>
      <div class="col-10 p-0"><b>{{ $r['reference'] }}</b> {{ $r['project'] }}</div>
    </div>
  </li>
  @endforeach 
</ul>
@endif