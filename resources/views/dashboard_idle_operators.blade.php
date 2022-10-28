<div class="card shadow-none">
    <div class="card-header pt-2 pl-3 pr-3 pb-2 bg-dark">
      <h6 class="text-white font-weight-bold text-center m-0 rounded-top" style="font-size: 10.5pt;">On Idle Operator(s) <span class="badge badge-primary pull-right">{{ count($list) }}</span></h6>
    </div>
    <div class="card-body p-0" style="min-height: 100px;">
        <div class="table-responsive" style="max-height: 300px;">
            <table class="table table-striped table-bordered m-0">
                <col style="width: 18%;">
                <col style="width: 82%;">
                <tbody style="font-size: 8pt;">
                    @forelse ($list as $row)
                    <tr>
                        <td class="p-1 text-center">
                            @if ($row['image'])
                            <img src="{{ ($row['image']) }}" alt="{{ $row['name'] }}" class="img-thumbnail" style="width: 45px; height: 45px;">
                            @else
                            <img src="{{ asset('/storage/no_img.png') }}" alt="{{ $row['name'] }}" class="img-thumbnail" style="width: 45px; height: 45px;">
                            @endif
                        </td>
                        <td class="p-2 align-middle">
                            <span class="d-block font-weight-bold">{{ $row['name'] }}</span>
                            <span class="badge badge-danger" style="font-size: 7pt;">{{ $row['idle_time'] }}</span>
                        </td>
                    </tr>
                    @empty 
                    <tr>
                        <td colspan="2" class="text-center text-uppercase text-muted">No operator(s) found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>