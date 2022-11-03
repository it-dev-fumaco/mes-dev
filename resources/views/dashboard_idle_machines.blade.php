<div class="card shadow-none">
    <div class="card-header pt-2 pl-3 pr-3 pb-2 bg-dark">
      <h6 class="text-white font-weight-bold text-center m-0 rounded-top" style="font-size: 10.5pt;">On Idle / Unavailable Machine(s) <span class="badge badge-primary pull-right">{{ count($data) }}</span></h6>
    </div>
    <div class="card-body p-0" style="min-height: 100px;" id="idle-machines-div">
        <div class="table-responsive" style="max-height: 400px;">
            <table class="table table-striped table-bordered m-0">
                <col style="width: 18%;">
                <col style="width: 82%;">
                <tbody style="font-size: 8pt;">
                    @forelse ($data as $row)
                    <tr>
                        <td class="p-1 text-center">
                            @if ($row['image'])
                            <img src="{{ asset($row['image']) }}" alt="{{ $row['machine_name'] }}" class="img-thumbnail" style="width: 45px; height: 45px;">
                            @else
                            <img src="{{ asset('/storage/no_img.png') }}" alt="{{ $row['machine_name'] }}" class="img-thumbnail" style="width: 45px; height: 45px;">
                            @endif
                        </td>
                        <td class="p-2">
                            <span class="d-block"><b>{{ $row['machine_code'] }}</b> {{ $row['machine_name'] }}</span> 
                            <span class="badge badge-danger" style="font-size: 7pt;">{{ $row['idle_time'] }}</span>
                        </td>
                    </tr>
                    @empty 
                    <tr>
                        <td colspan="2" class="text-center text-uppercase text-muted">No machine(s) found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>