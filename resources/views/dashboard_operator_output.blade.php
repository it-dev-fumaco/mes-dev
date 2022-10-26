@if (count($q) > 0)
<div class="card shadow-none border">
    <div class="card-header pt-2 pl-3 pr-3 pb-2" style="background-color: #012f6a;">
      <h6 class="text-white font-weight-bold text-left m-0 rounded-top" style="font-size: 10.5pt;">Operator Output for Today</h6>
    </div>
    <div class="card-body p-0" style="min-height: 200px;">
        <div class="row p-2 m-0" style="font-size: 8pt;">
            @if (count($data['fabrication']) > 0)
            <div class="col-4 p-0 m-0">
                <h5 class="text-center m-1">Fabrication</h5>
                @forelse ($data['fabrication'] as $r)
                <div class="d-flex flex-row align-items-center p-0 m-0">
                    <div class="col-4 p-1">{{ $r['operator_name'] }}</div>
                    <div class="col-8 p-0 m-0">
                        <div class="progress border m-1" style="height: 15px !important;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $r['percentage'] }}%" aria-valuenow="{{ $r['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                <small>{{ $r['output'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <small class="d-block p-3 text-center text-muted text-uppercase font-weight-bold">No records found</small>
                @endforelse
            </div>
            @endif
            @if (count($data['painting']) > 0)
            <div class="col-4 p-0 m-0">
                <h5 class="text-center m-1">Painting</h5>
                @forelse ($data['painting'] as $r)
                <div class="d-flex flex-row align-items-center p-0 m-0">
                    <div class="col-4 p-1">{{ $r['operator_name'] }}</div>
                    <div class="col-8 p-0 m-0">
                        <div class="progress border m-1" style="height: 15px !important;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $r['percentage'] }}%" aria-valuenow="{{ $r['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                <small>{{ $r['output'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <small class="d-block p-3 text-center text-muted text-uppercase font-weight-bold">No records found</small>
                @endforelse
            </div>
            @endif
            @if (count($data['assembly']) > 0)
            <div class="col-4 p-0 m-0">
                <h5 class="text-center m-1">Wiring & Assembly</h5>
                @forelse ($data['assembly'] as $r)
                <div class="d-flex flex-row align-items-center p-0 m-0">
                    <div class="col-4 p-1">{{ $r['operator_name'] }}</div>
                    <div class="col-8 p-0 m-0">
                        <div class="progress border m-1" style="height: 15px !important;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $r['percentage'] }}%" aria-valuenow="{{ $r['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                <small>{{ $r['output'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <small class="d-block p-3 text-center text-muted text-uppercase font-weight-bold">No records found</small>
                @endforelse
            </div>
            @endif
        </div>
    </div>
</div>
@endif