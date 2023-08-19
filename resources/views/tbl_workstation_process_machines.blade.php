@foreach ($machines as $machine)
    @php
        switch ($machine->status) {
            case 'Available':
                $color = 'bg-success';
                break;
            case 'On-going Maintenance':
                $color = 'bg-warning';
                break;
            default:
                $color = 'bg-danger';
                break;
        }

        $disabled = null;
    @endphp
    @if ($disabled)
    <div class="col-md-4 assigned-to-different-machine">
        <div class="card" style="background-color: #1b4f7288;">
            <div class="card-body" style="padding-top: 0; padding-bottom: 0;">
                <div class="row" style="border: 0px solid; ">
                    <div class="col-md-4" style="padding: 0;">
                        <img src="{{ $machine->image }}" style="width: 100px; height: 100px;">
                    </div>
                    <div class="col-md-8">
                        <h5 class="card-category text-white" style="padding: 0; margin: 0">{{ $machine->machine_name.' ['.$machine->machine_code.']' }}</h5>
                        <p class="text-white">
                            <span class="{{ $color }} dot"></span> {{ $machine->status }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    @else
    <div class="col-md-4 selected-machine"
        data-machine-code="{{ $machine->machine_code }}"
        data-process-id="{{ $machine->process_id }}"
        data-status="{{ $machine->status }}">
        <div class="card" style="background-color: #1B4F72;">
            <div class="card-body" style="padding-top: 0; padding-bottom: 0;">
                <div class="row" style="border: 0px solid; ">
                    <div class="col-md-4" style="padding: 0;">
                        <img src="{{ $machine->image }}" style="width: 100px; height: 100px;">
                    </div>
                    <div class="col-md-8">
                        <h5 class="card-category text-white" style="padding: 0; margin: 0">{{ $machine->machine_name.' ['.$machine->machine_code.']' }}</h5>
                        <p class="text-white">
                            <span class="{{ $color }} dot"></span> {{ $machine->status }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach